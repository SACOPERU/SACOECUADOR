<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use SoapClient;
use App\Models\Product;
use App\Models\Image;
use Faker\Factory as Faker;

class ProductflexController extends Controller
{
    public function consultaProductos(Request $request)
    {
        try {
            // URL del WSDL del servicio web SOAP
            $wsdlUrl = "https://ws-erp.manager.cl/Flexline/Saco/Ws%20ConsultaStock/ConsultaStock.asmx?WSDL";

            // Crear un cliente SOAP
            $soapClient = new \SoapClient($wsdlUrl, [
                'trace' => true,
                'exceptions' => true,
            ]);

            // Parámetros para la llamada SOAP
            $empresaParameter = $request->input('empresa');
            $bodegaParameter = $request->input('bodega');

            // Realizar la llamada SOAP al método "ConsultaStock_BodegaLPrecios"
            $response = $soapClient->ConsultaStock_BodegaLPrecios([
                '__Empresa' => $empresaParameter,
                '__Bodega' => $bodegaParameter,
            ]);

            // Obtener y procesar la respuesta en formato XML
            $xmlResponse = $response->ConsultaStock_BodegaLPreciosResult;
            $responseData = simplexml_load_string($xmlResponse);

            $faker = Faker::create();

            foreach ($responseData->Producto as $productData) {
                $sku = (string) $productData->CodProducto;
                if (intval($productData->Bodega->Cantidad) > 0 && (str_starts_with($sku, 'XIA') || str_starts_with($sku, 'SM') || str_starts_with($sku, 'INF') || str_starts_with($sku, 'TMG'))) {
                    $existingProduct = Product::where('sku', $sku)->first();

                    if ($existingProduct) {

                        // Verificar si el campo 'description' está vacío antes de actualizarlo
                        if (empty($existingProduct->description)) {
                            $existingProduct->update([
                                'description' => (string) $productData->Descripcion,
                            ]);
                        }

                        $existingProduct->update([
                            'name' => (string) $productData->Descripcion,
                            //'description' => (string) $productData->Descripcion,
                            'quantity' => intval($productData->Bodega->Cantidad),
                            'bodega' => (string) $productData->Bodega->Descripcion,
                        ]);

                        switch ($existingProduct->bodega) {
                            case '03-LIM-ATOCONG-MISTR':
                                $existingProduct->{'atocong'} = intval($productData->Bodega->Cantidad);
                                break;
                            case '03-LIM-JOCKEYPZ-MIST':
                                $existingProduct->{'jockeypz'} = intval($productData->Bodega->Cantidad);
                                break;
                            case '03-LIM-MEGAPLZ-MISTR':
                                $existingProduct->{'megaplz'} = intval($productData->Bodega->Cantidad);
                                break;
                            case '03-LIM-HUAYLAS-MISTR':
                                $existingProduct->{'huaylas'} = intval($productData->Bodega->Cantidad);
                                break;
                            case '03-LIM-PURUCHU-MISTR':
                                $existingProduct->{'puruchu'} = intval($productData->Bodega->Cantidad);
                                break;
                            case '03-LIM-BSF-WEBSACO':
                                $existingProduct->{'principal'} = intval($productData->Bodega->Cantidad);
                                break;
                        }

                        // Calcular y actualizar el campo 'stock_flex'
                        $existingProduct->quantity =
                            $existingProduct->atocong +
                            $existingProduct->jockeypz +
                            $existingProduct->megaplz +
                            $existingProduct->huaylas +
                            $existingProduct->principal +
                            $existingProduct->puruchu;

                        $existingProduct->save();
                    } else {
                        // El producto no existe, crea uno nuevo
                        $newProduct = Product::create([
                            'sku' => $sku,
                            'name' => (string) $productData->Descripcion,
                            'description' => (string) $productData->Descripcion,
                            'quantity' => 0,
                            'bodega' => (string) $productData->Bodega->Descripcion,
                            // Asigna un valor predeterminado a las columnas correspondientes
                            'atocong' => 0,
                            'jockeypz' => 0,
                            'megaplz' => 0,
                            'huaylas' => 0,
                            'principal' => 0,
                            'puruchu' => 0,

                            'subcategory_id' => 3,
                            'brand_id' => 24,
                            'slug' => (string) $productData->Descripcion,
                            'stock_flex' => 0, // Inicializa 'stock_flex' en 0
                            'price' => 0,
                            'price_tachado' => 0,
                            'price_partner' => 0,
                            'quantity_partner' => 0,
                            // Agrega otros campos según sea necesario
                        ]);

                        // Verificar el valor de 'bodega' y asignar 'quantity' según corresponda
                        switch ($newProduct->bodega) {
                            case '03-LIM-ATOCONG-MISTR':
                                $newProduct->{'atocong'} = intval($productData->Bodega->Cantidad);
                                break;
                            case '03-LIM-JOCKEYPZ-MIST':
                                $newProduct->{'megaplz'} = intval($productData->Bodega->Cantidad);
                                break;
                            case '03-LIM-MEGAPLZ-MISTR':
                                $newProduct->{'megaplz'} = intval($productData->Bodega->Cantidad);
                                break;
                            case '03-LIM-HUAYLAS-MISTR':
                                $newProduct->{'huaylas'} = intval($productData->Bodega->Cantidad);
                                break;
                            case '03-LIM-PURUCHU-MISTR':
                                $newProduct->{'puruchu'} = intval($productData->Bodega->Cantidad);
                                break;
                            case '03-LIM-BSF-WEBSACO':
                                $newProduct->{'principal'} = intval($productData->Bodega->Cantidad);
                                break;
                        }

                        // Calcular y asignar el campo 'stock_flex'
                        $newProduct->quantity =
                            $newProduct->atocong +
                            $newProduct->jockeypz +
                            $newProduct->megaplz +
                            $newProduct->huaylas +
                            $newProduct->principal +
                            $newProduct->puruchu;

                        $newProduct->save();

                        // Agregar lógica para asociar una imagen con datos falsos a los productos aquí
                        $this->associateImageWithFakerData($newProduct, $faker);
                    }
                }
            }

            // Devolver la vista con los datos obtenidos del servicio web
            return view('livewire.admin.consulta-productos', ['responseData' => $responseData]);
        } catch (\Exception $e) {
            dd($e->getMessage());
            return view('livewire.error', ['error' => $e->getMessage()]);
        }
    }

    private function associateImageWithFakerData($product, $faker)
    {
        $defaultImageUrl = 'default_image_url.jpg';

        $image = new Image([
            'url' => $defaultImageUrl,
            'imageable_id' => $product->id,
            'imageable_type' => Product::class,
        ]);

        $image->save();
    }
}

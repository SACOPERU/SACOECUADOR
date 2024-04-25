<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use SoapClient;
use App\Models\Product;

class ConsultaPrecioTachadoController extends Controller
{
    public function consultaPrecioTachado(Request $request)
    {
        try {

            $responseData = $this->realizarConsultaSOAP($request->input('empresa'), $request->input('listaprecio'));


            $this->guardarDatosEnDB($responseData);


            Log::info('Consulta SOAP exitosa y datos guardados en la base de datos.');


            return view('livewire.admin.consulta-precio-tachado', ['responseData' => $responseData]);
        } catch (\SoapFault $soapException) {

            dd($soapException->getMessage());
        } catch (\Exception $e) {

            dd($e->getMessage());
        }
    }

    private function realizarConsultaSOAP($empresa, $listaprecio)
    {

        $wsdlUrl = "https://ws-erp.manager.cl/Flexline/Saco/Ws%20ConsultaProducto/WSConsultaProducto.asmx?WSDL";


        $soapClient = new \SoapClient($wsdlUrl, [
            'trace' => true,
            'exceptions' => true,
        ]);


        $response = $soapClient->ConsultaListaPrecios([
            '__Empresa' => $empresa,
            '__IdListaPrecios' => $listaprecio,
        ]);


        $xmlResponse = $response->ConsultaListaPreciosResult;
        $responseData = simplexml_load_string($xmlResponse);

        return $responseData;
    }

    private function guardarDatosEnDB($responseData)
    {
        foreach ($responseData->PRODUCTO as $producto) {

            $sku = (string)$producto->PRODUCTO;
            $existingProduct = Product::where('sku', $sku)->first();

            if ($existingProduct) {

                $existingProduct->name = (string)$producto->GLOSA;
                $existingProduct->price_tachado = (float)$producto->PRECIOLISTA;
                $existingProduct->price_partner = 0;

                $existingProduct->save();
            }
        }
    }
}

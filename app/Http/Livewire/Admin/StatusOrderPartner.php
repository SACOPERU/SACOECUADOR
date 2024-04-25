<?php

namespace App\Http\Livewire\Admin;

use Illuminate\Http\Request;
use Livewire\Component;
use App\Models\OrderPartner;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Log;

class StatusOrderPartner extends Component
{
    use WithFileUploads;

    public $order, $status, $courrier,
        $tracking_number, $guia_remision,
        $alto_paquete, $ancho_paquete, $largo_paquete,
        $peso_paquete, $observacion, $emision, $selectedCourier, $data_sent_to_olva,
        $remito;

    public $editImage;

    public $trackingNumberUpdated;

    public $createForm = [
        'status' => null,
        'image' => null,
    ];

    public $editForm = [
        'status' => null,
        'image' => null,
    ];

    public $rules = [
        'courrier' => 'required',
        'tracking_number' => 'required',
        'guia_remision' => 'required',
    ];

    public $dataSentToOlva = false;

    public function files(OrderPartner $order, Request $request)
    {
        $request->validate([
            'file' => 'required|image|max:4048'
        ]);

        $url = Storage::put('orders', $request->file('file'));

        $order->images()->create([
            'url' => $url
        ]);
    }

    public function mount()
    {
        if ($this->order) {
            $this->status = $this->order->status;
            $this->courrier = $this->order->courrier;
            $this->tracking_number = $this->order->tracking_number;
            $this->guia_remision = $this->order->guia_remision;
            $this->emision = $this->order->emision;
            $this->remito = $this->order->remito;
            $this->alto_paquete = $this->order->alto_paquete;
            $this->ancho_paquete = $this->order->ancho_paquete;
            $this->largo_paquete = $this->order->largo_paquete;
            $this->peso_paquete = $this->order->peso_paquete;
            $this->observacion = $this->order->observacion;
        }
    }

    public function updateTrackingNumber()
    {
        $this->order->update([
            'tracking_number' => $this->tracking_number,
        ]);

        $this->trackingNumberUpdated = $this->tracking_number;
    }

    private function data_sent_to_olva()
    {

        if ($this->order->courrier !== 'OLVA') {
            Log::info('El courier no es OLVA. No se enviarán datos a la API de Olva.');
            return null;
        }

        // Verificar el estado de la orden
        if ($this->order->status >= 3) {
            Log::info('El estado de la orden es 3 o mayor. No se enviarán datos a la API de Olva.');
            return null;
        }

        $url = "http://wap.olvacourier.com:8080/RegistroRemito-1.0-SNAPSHOT/webresources/remito/generar";

        $dataToSend = [
            'consignado' => $this->order->name_order,
            'nroDocConsignado' => $this->order->dni_order,
            'direccion' => $this->order->address,
            'ubigeo' => $this->order->district_id,
            'codigoRastreo' => $this->order->id,
            'observacion' => $this->order->observacion,
            'montoArticulo' => $this->order->total,
            'receptor' => $this->order->name_order,
            'rucSeller' => '20603393491',
            'ubigeoSeller' => '150101',
            'seller' => 'Smartphones & Comunicaciones del Perú S.R.L.',
            'direccionSeller' => 'Av. Los Forestales 1296, Int. C-09, Villa El Salvador, Lima',
            'contacto' => 'Maria Quispe',
            'telefono' => '902977730',
            'codClienteRucDni' => '20603393491',
            'total' => '0',
            'formaPago' => 'PPD',
            'tipoEnvio' => '10',
            'altoEnvio' => $this->order->alto_paquete,
            'anchoEnvio' => $this->order->ancho_paquete,
            'largoEnvio' => $this->order->largo_paquete,
            'pesoUnitario' => $this->order->peso_paquete,
            'codContenedor' => '2',
        ];
        $token = "9vv8jCBaMXr30ZXjNNwxpEP5WPfnVPDSPB6VAzqlWfLKYeqgK1xT7Fk8EzMuatoUNmQxoJNGSPefcbalqmiJAw==";

        $client = new Client();

        try {
            $response = $client->request('PUT', $url, [
                'json' => $dataToSend,
                'headers' => [
                    'Authorization' => $token,
                    'Content-Type' => 'application/json',
                ],
            ]);

            Log::info("Solicitud enviada a la API de Olva: " . json_encode($dataToSend));
            Log::info("Respuesta de la API de Olva: " . $response->getBody()->getContents());
            // Muestra el contenido de la respuesta en un dd()
            $jsonResponse = json_decode($response->getBody(), true);
            // Obtener los valores de 'remito' y 'emision' del array
            $this->remito = $jsonResponse['remito'] ?? null;
            $this->emision = $jsonResponse['emision'] ?? null;

            // Crear una cadena con formato JSON solo para 'remito' y 'emision'
            $this->tracking_number = $this->emision . '-' . $this->remito;

            $statusCode = $response->getStatusCode();
            $responseBody = json_decode($response->getBody()->getContents(), true);

            if ($statusCode == 200) {
                if (!$this->dataSentToOlva) {  // Verificar si no se ha establecido previamente
                    $this->dataSentToOlva = true;
                    $this->data_sent_to_olva = '1';
                    return $responseBody;
                } else {
                    Log::info('Los datos ya se enviaron a OLVA para esta orden. Se omite la solicitud.');
                }
            } else {
                Log::error("Error durante la solicitud a la API de Olva - Código de estado: $statusCode, Respuesta: $responseBody");
                $this->emit('apiRequestError', $responseBody);
            }
        } catch (RequestException $e) {
            Log::error("Error durante la solicitud a la API de Olva: " . $e->getMessage());
            $this->emit('apiRequestError', $e->getMessage());
        }

        return null;
    }

    public function save()
    {
        if (!$this->dataSentToOlva && $this->order->status == 2) {
            // Verificar si el status cambia de 2 a 3
            $selectedCourier = $this->courrier;

            $this->order->status = $this->status; // Actualizar el status
            $this->order->courrier = $selectedCourier;
            $this->order->guia_remision = $this->guia_remision;
            $this->order->alto_paquete = $this->alto_paquete;
            $this->order->ancho_paquete = $this->ancho_paquete;
            $this->order->largo_paquete = $this->largo_paquete;
            $this->order->peso_paquete = $this->peso_paquete;
            $this->order->observacion = $this->observacion;
            $this->order->tracking_number = $this->tracking_number;

            if (!$this->order->save()) {
                Log::error('Error al intentar guardar los cambios en la base de datos.');
                $this->emit('apiRequestError', 'Error al intentar guardar los cambios en la base de datos.');
                return;
            }

            $responseBody = $this->data_sent_to_olva();

            if ($responseBody) {
                $decodedResponse = json_decode($responseBody, true);

                Log::info('Respuesta decodificada de OLVA:', $decodedResponse);

                // Verificar si la respuesta contiene los valores esperados
                if (isset($decodedResponse['remito'])) {
                    $remitoValue = $decodedResponse['remito'];
                    // Asignar el valor real del campo tracking_number de la respuesta de la API
                    $trackingNumber = '24-' . $remitoValue;
                    //$this->order->remito = $remitoValue;
                    $this->order->tracking_number = $trackingNumber;

                    $this->order->save(); // Guardar el modelo nuevamente con los nuevos valores

                } else {
                    Log::error('La respuesta de la API de OLVA no contiene los valores esperados.');
                    $this->emit('apiRequestError', 'La respuesta de la API de OLVA no contiene los valores esperados.');
                }
            }
            $this->dataSentToOlva = true;
            $this->emit('saved');
        } else {
            Log::info('Los datos ya se enviaron a OLVA para esta orden, el status no es 2 o el status es diferente de 3. Se omite la solicitud.');
        }
    }


    public function update()
    {
        if (!$this->dataSentToOlva) {
            $selectedCourier = $this->courrier;

            $this->order->update([
                'status' => $this->status,
                'courrier' => $selectedCourier,
                'guia_remision' => $this->guia_remision,
                'alto_paquete' => $this->alto_paquete,
                'ancho_paquete' => $this->ancho_paquete,
                'largo_paquete' => $this->largo_paquete,
                'peso_paquete' => $this->peso_paquete,
                'observacion' => $this->observacion,
                'tracking_number' => $this->tracking_number,
            ]);

            $responseBody = $this->data_sent_to_olva();

            if ($responseBody) {
                $decodedResponse = json_decode($responseBody, true);

                Log::info('Respuesta decodificada de OLVA:', $decodedResponse);

                // Verificar si la respuesta contiene los valores esperados
                if (isset($decodedResponse['remito'])) {
                    $remitoValue = $decodedResponse['remito'];
                    // Asignar el valor real del campo tracking_number de la respuesta de la API
                    $trackingNumber = '24-' . $remitoValue;

                    $this->order->remito = $remitoValue;
                    $this->order->tracking_number = $trackingNumber;

                    $this->order->save(); // Guardar el modelo nuevamente con los nuevos valores

                } else {
                    Log::error('La respuesta de la API de OLVA no contiene los valores esperados.');
                    $this->emit('apiRequestError', 'La respuesta de la API de OLVA no contiene los valores esperados.');
                }
            }
        } else {
            Log::info('Los datos ya se enviaron a OLVA para esta orden. Se omite la solicitud.');
        }
    }
    public function render()
    {

        $items = json_decode($this->order->content);

        return view('livewire.admin.status-order-partner', compact('items'));
    }
}

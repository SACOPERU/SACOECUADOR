<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use SoapClient;

class PedidoController extends Controller
{
    public function sendXmlToSoap(Request $request)
    {
        try {
            // Datos para completar el XML
            $usuario = 'flexline';
            $password = 'flexline';
            $empresa = '003';
            $tipoDocto = 'PEDIDO BITEL';
            $correlativo = '15030';
            $ctaCte = '20543254798';
            $numero = 'B-9999999';
            // ... (otros campos)

            // Construir el XML
            $xml = '<?xml version="1.0" encoding="UTF-8"?>';
            $xml .= '<DOCUMENTO_LIST>';
            $xml .= '<LOGIN>';
            $xml .= '<usuario>' . $usuario . '</usuario>';
            $xml .= '<password>' . $password . '</password>';
            $xml .= '</LOGIN>';
            $xml .= '<DOCUMENTO>';
            $xml .= '<ENCABEZADO>';
            $xml .= '<Empresa>' . $empresa . '</Empresa>';
            $xml .= '<TipoDocto>' . $tipoDocto . '</TipoDocto>';
            $xml .= '<Correlativo>' . $correlativo . '</Correlativo>';
            $xml .= '<CtaCte>' . $ctaCte . '</CtaCte>';
            $xml .= '<Numero>' . $numero . '</Numero>';
            // ... (otros campos)
            $xml .= '</DOCUMENTO>';
            $xml .= '</DOCUMENTO_LIST>';

            // Configuración del cliente SOAP
            $soapOptions = [
                'location' => 'https://ws-erp.manager.cl/Flexline/Saco/Ws%20Documento/Documento.asmx',
                'uri' => 'http://ws.flexline.cl/', // Puedes consultar la documentación del servicio para obtener el URI correcto
            ];

            $client = new SoapClient(null, $soapOptions);

            // Llamar al método del servicio SOAP y enviar el XML como parámetro
            $response = $client->__soapCall('InyectarDocumentoXML', [$xml]);

            // Procesar la respuesta del servicio SOAP
            // Puedes hacer lo que necesites con la respuesta aquí

            // Registrar la respuesta en el registro de Laravel
            Log::info('__sTextoXML: ' . json_encode($response));

            return response()->json(['message' => 'XML enviado exitosamente al servicio SOAP']);
        } catch (\Exception $e) {
            // Manejar errores
            Log::error('Error al enviar el XML al servicio SOAP: ' . $e->getMessage());

            return response()->json(['error' => 'Ocurrió un error al enviar el XML al servicio SOAP'], 500);
        }
    }
}

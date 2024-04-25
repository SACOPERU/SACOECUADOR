<?php

namespace App\Http\Controllers\OrderMistore;

use App\Http\Controllers\Controller;
use App\Models\Clients;
use Illuminate\Http\Request;
use SimpleXMLElement;
use SoapClient;
use SoapFault;

class OrderMistoreController extends Controller
{
    public function create()
    {
        return view('mistore.create-client');
    }

    public function store(Request $request)
    {
        // Validar los datos del formulario
        $request->validate([
            'doc_identidad' => 'required|numeric',
            'name' => 'required|string|max:255',
            'direccion' => 'string|max:255',
            'tipo_persona' => 'required|in:01,02',
            'tipo_identidad' => 'required|in:1,4,6,7',
            'canal' => 'required|string|max:255',
            'subcanal' => 'required|string|max:255',
            'vendedor' => 'required|string|max:255',
            'number' => 'required|numeric',
        ]);

        // Crear un nuevo cliente en la base de datos
        $client = new Clients([
            'doc_identidad' => $request->input('doc_identidad'),
            'name' => $request->input('name'),
            'direccion' => $request->input('direccion'),
            'tipo_persona' => $request->input('tipo_persona'),
            'tipo_identidad' => $request->input('tipo_identidad'),
            'canal' => $request->input('canal'),
            'subcanal' => $request->input('subcanal'),
            'vendedor' => $request->input('vendedor'),
            'number' => $request->input('number'),
        ]);

        // Guardar el cliente en la base de datos
        $client->save();

        // Llamar al servicio web SOAP
        $result = $this->callSoapService($client);

        // Redireccionar a la vista de éxito o error según la respuesta del WS SOAP
        if ($result['success']) {
            return redirect()->route('mistore.success')->with('success', $result['message']);
        } else {
            return redirect()->route('mistore.success')->with('error', $result['message']);
        }
    }

    public function success()
    {
        return view('mistore.success');
    }

    private function callSoapService(Clients $client)
    {
        // Configuración del servicio web SOAP
        $soapOptions = [
            'trace' => 1,
            'exceptions' => true,
        ];

        // URL del servicio web SOAP
        $soapUrl = 'https://ws-erp.manager.cl/Flexline/Saco/Ws%20InyectaCtaCte/InyectaCtaCte.asmx?WSDL';

        // Datos a enviar al servicio web SOAP
        $xmlData = $this->generateXmlData($client);

        // Crear el cliente SOAP
        $soapClient = new SoapClient($soapUrl, $soapOptions);

        // Llamar al método del servicio web SOAP
        try {
            $result = $soapClient->InyectarCtaCteXML(['__sTextoXML' => $xmlData]);

            // Manejar la respuesta del servicio web SOAP según tus necesidades
            if (stripos($result->InyectarCtaCteXMLResult, 'éxito') !== false) {
                return ['success' => true, 'message' => 'Cliente registrado y enviado exitosamente'];
            } else {
                return ['success' => false, 'message' => 'Hubo un problema al enviar la información al WS SOAP'];
            }
        } catch (SoapFault $e) {
            // Manejar las excepciones SOAP
            return ['success' => false, 'message' => 'Error SOAP: ' . $e->getMessage()];
        }
    }

    // Función para convertir un array a formato XML
    private function generateXmlData(Clients $client)
    {
        $xmlData = new SimpleXMLElement('<CTACTELIST></CTACTELIST>');

        // Agregar elementos LOGIN y CLIENTE-PROVEEDOR al XML
        $login = $xmlData->addChild('LOGIN');
        $login->addChild('usuario', 'fl20603393491');
        $login->addChild('password', 'flexline');

        $clienteProveedor = $xmlData->addChild('CLIENTE-PROVEEDOR');
        $clienteProveedor->addChild('Empresa', '003');
        $clienteProveedor->addChild('TipoCtaCte', 'CLIENTE');
        $clienteProveedor->addChild('CtaCte', $client->doc_identidad);
        $clienteProveedor->addChild('CodLegal', $client->doc_identidad);
        $clienteProveedor->addChild('RazonSocial', $client->name);
        $clienteProveedor->addChild('Sigla');
        $clienteProveedor->addChild('Giro');
        $clienteProveedor->addChild('Tipo', $client->canal);
        $clienteProveedor->addChild('Grupo', $client->subcanal);
        $clienteProveedor->addChild('Ejecutivo');
        $clienteProveedor->addChild('CondPago', 'CONTADO');
        $clienteProveedor->addChild('Vigencia', 'S');
        $clienteProveedor->addChild('ListaPrecio');
        $clienteProveedor->addChild('Zona', 'Central');
        $clienteProveedor->addChild('Direccion', $client->direccion);
        $clienteProveedor->addChild('Ciudad');
        $clienteProveedor->addChild('Comuna');
        $clienteProveedor->addChild('Estado');
        $clienteProveedor->addChild('Pais', 'Peru');
        $clienteProveedor->addChild('Telefono');
        $clienteProveedor->addChild('Fax');
        $clienteProveedor->addChild('eMail');
        $clienteProveedor->addChild('CodPostal');
        $clienteProveedor->addChild('Contacto');
        $clienteProveedor->addChild('ModoEnvio');
        $clienteProveedor->addChild('DireccionEnvio');
        $clienteProveedor->addChild('LimiteCredito', '0');
        $clienteProveedor->addChild('VigenciaCredito');
        $clienteProveedor->addChild('RetrasoCredito', '0');
        $clienteProveedor->addChild('Comentario1', 'INYECTADO DESDE PLATAFORMA MISTORE');
        $clienteProveedor->addChild('Comentario2');
        $clienteProveedor->addChild('Comentario3');
        $clienteProveedor->addChild('Comentario4');
        $clienteProveedor->addChild('Texto1');
        $clienteProveedor->addChild('Texto2');
        $clienteProveedor->addChild('Texto3');
        $clienteProveedor->addChild('FechaModif');
        $clienteProveedor->addChild('UsuarioModif', 'consultoria');
        $clienteProveedor->addChild('TIPOCONTRIBUYENTE');
        $clienteProveedor->addChild('PorcDr1', '0');
        $clienteProveedor->addChild('PorcDr2', '0');
        $clienteProveedor->addChild('PorcDr3', '0');
        $clienteProveedor->addChild('PorcDr4', '0');
        $clienteProveedor->addChild('Analisisctacte1', $client->tipo_identidad);
        $clienteProveedor->addChild('Analisisctacte2', $client->tipo_persona);
        $clienteProveedor->addChild('Analisisctacte3');
        $clienteProveedor->addChild('Analisisctacte4');
        $clienteProveedor->addChild('Analisisctacte5');
        $clienteProveedor->addChild('Analisisctacte6');
        $clienteProveedor->addChild('Analisisctacte7');
        $clienteProveedor->addChild('Analisisctacte8');
        $clienteProveedor->addChild('Analisisctacte9');
        $clienteProveedor->addChild('Analisisctacte10');
        $clienteProveedor->addChild('ZonaCob');
        $clienteProveedor->addChild('FlujoCob');
        $clienteProveedor->addChild('CobradorCob');
        $clienteProveedor->addChild('FechaBloqueo');
        $clienteProveedor->addChild('UsuarioBloqueo');
        $clienteProveedor->addChild('ComentarioBloqueo');
        $clienteProveedor->addChild('Moneda', 'S/');
        $clienteProveedor->addChild('EstaCertificado', 'N');
        $clienteProveedor->addChild('AnalisisCtacte11');
        $clienteProveedor->addChild('AnalisisCtacte12');
        $clienteProveedor->addChild('AnalisisCtacte13');
        $clienteProveedor->addChild('AnalisisCtacte14');
        $clienteProveedor->addChild('AnalisisCtacte15');
        $clienteProveedor->addChild('AnalisisCtacte16');
        $clienteProveedor->addChild('AnalisisCtacte17');
        $clienteProveedor->addChild('AnalisisCtacte18');
        $clienteProveedor->addChild('AnalisisCtacte19');
        $clienteProveedor->addChild('AnalisisCtacte20');
        $clienteProveedor->addChild('AnalisisCtacte21');
        $clienteProveedor->addChild('AnalisisCtacte22');
        $clienteProveedor->addChild('AnalisisCtacte23');
        $clienteProveedor->addChild('AnalisisCtacte24');
        $clienteProveedor->addChild('AnalisisCtacte25');
        $clienteProveedor->addChild('AnalisisCtacte26');
        $clienteProveedor->addChild('AnalisisCtacte27');
        $clienteProveedor->addChild('AnalisisCtacte28');
        $clienteProveedor->addChild('AnalisisCtacte29');
        $clienteProveedor->addChild('AnalisisCtacte30');

        return $xmlData->asXML();
    }
}

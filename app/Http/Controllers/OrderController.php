<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use GuzzleHttp\Client;
use SoapClient;
use Illuminate\Support\Facades\Log;
use SimpleXMLElement;
use Illuminate\Support\Facades\Session;

//use Openpay\Data\Openpay;
use Openpay;
use Openpay\Data\Openpay as DataOpenpay;

require_once '../vendor/autoload.php';

class OrderController extends Controller
{
    public function index()
    {
        $userId = auth()->user()->id;

        $orders = Order::where('user_id', $userId);

        if (request()->has('status')) {
            $orders->where('status', request('status'));
        }

        $orders = $orders->get();

        $reservado = Order::where('status', 1)->where('user_id', $userId)->count();
        $pagado = Order::where('status', 2)->where('user_id', $userId)->count();
        $despachado = Order::where('status', 3)->where('user_id', $userId)->count();
        $entregado = Order::where('status', 4)->where('user_id', $userId)->count();
        $anulado = Order::where('status', 5)->where('user_id', $userId)->count();

        return view('orders.index', compact('orders', 'reservado', 'pagado', 'despachado', 'entregado', 'anulado'));
    }

    public function show(Order $order)
    {
        $this->authorize('author', $order);
        $items = json_decode($order->content);
        return view('orders.show', compact('order', 'items'));
    }

    public function payment(Order $order)
    {
        $this->authorize('author', $order);

        try {
            $authorization = base64_encode(config('services.izipay.client_id') . ':' . config('services.izipay.client_secret'));
            //dd($authorization);
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $authorization,
                'Accept' => 'application/json',
            ])->post(config('services.izipay.url'), [
                'amount' => $order->total * 100,
                'currency' => 'PEN',
                'orderId' => $order->id,
                'customer' => [
                    'reference' => auth()->id(),
                    'email' => auth()->user()->email,
                    'billingDetails' => [
                        'firstName' => auth()->user()->name,
                    ],
                ],
            ])->json();

            if (isset($response['answer']['formToken'])) {
                $formToken = $response['answer']['formToken'];
            } else {
                return redirect()->back()->with('error', 'Error al obtener el formToken');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error en la solicitud HTTP');
        }

        $items = json_decode($order->content);

        return view('orders.payment', compact('order', 'items', 'formToken'));
    }
    public function openPayPayment(Request $request, Order $order)
    {
        // Verificar la autorización
        $this->authorize('author', $order);

        try {
            // Crear instancia de cliente para hacer peticiones HTTP
            $client = new Client();

            // Obtener la clave secreta de la configuración
            $claveSecreta = config('services.openpay.client_secret') . ':';

            // Log de la cabecera 'Authorization'
            Log::info('Authorization Header: ' . base64_encode($claveSecreta));

            // Log del payload 'json'
            $jsonPayload = [
                'method' => 'card',
                'amount' => $order->total,
                'currency' => 'PEN',
                'description' => 'PEDIDO 003-0000' . $order->id,
                'order_id' => $order->id,
                'source_id' => $request->token_id,
                'device_session_id' => $request->deviceIdHiddenFieldName,
                'customer' => [
                    'name' => auth()->user()->name,
                    'email' => auth()->user()->email,
                ],
            ];
            Log::info('JSON Payload: ' . json_encode($jsonPayload));

            // Realizar la petición al servicio OpenPay
            $response = $client->post(config('services.openpay.url') . config('services.openpay.client_id') . '/charges', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Authorization' => 'Basic ' . base64_encode($claveSecreta),
                ],
                'json' => $jsonPayload,
            ]);

            // Decodificar la respuesta de OpenPay
            $datos = json_decode($response->getBody(), true);


            // Log de la respuesta de OpenPay
            Log::info('Respuesta OpenPay: ' . json_encode($datos));

            // Verificar si la respuesta es exitosa (código de estado 200 y estado "completed")
            if ($response->getStatusCode() === 200 && $datos['status'] === 'completed') {
                // Decodificar la información de la tarjeta
                $cardType = $datos['card']['type'];

                // Log para verificar el tipo de tarjeta
                Log::info('Tipo de tarjeta: ' . $cardType);

                // Actualizar el campo condicion_pago según el tipo de tarjeta
                if ($cardType === 'credit') {
                    $order->condicion_pago = 1;
                } elseif ($cardType === 'debit') {
                    $order->condicion_pago = 2;
                }
                $order->save();

                // Llamar al servicio web SOAP si el estado de la orden es '2'
                $this->enviarAServicioSoap($order);

                // Actualizar el estado de la orden a "2" (PAGADO)
                $order->update(['status' => 2]);

                $xmlContentDocumento = $this->generarDocumentoXml($order);

                try {
                    Log::info('Antes de la llamada SOAP Documento');
                    Log::info('XML a enviar: ' . $xmlContentDocumento);

                    $soapClientDocumento = new SoapClient("https://ws-erp.manager.cl/Flexline/Saco/Ws%20Documento/Documento.asmx?WSDL", [
                        'stream_context' => stream_context_create([
                            'ssl' => [
                                'verify_peer' => false,
                                'verify_peer_name' => false
                            ]
                        ])
                    ]);

                    $responseDocumento = $soapClientDocumento->__soapCall('InyectarDocumentoXML', [
                        'InyectarDocumentoXML' => [
                            '__sTextoXML' => $xmlContentDocumento,
                        ],
                    ]);

                    Log::info('Después de la llamada SOAP Documento');
                    Log::info('Respuesta SOAP Documento: ' . var_export($responseDocumento, true));
                } catch (\SoapFault $soapFault) {
                    Log::error('Error SOAP Documento: ' . $soapFault->getMessage());
                    Log::error('Código de error SOAP Documento: ' . $soapFault->getCode());
                    Log::error('Detalles del error SOAP Documento: ' . var_export($soapFault, true));
                } catch (\Exception $e) {
                    Log::error('Error general Documento: ' . $e->getMessage());
                    Log::error('Detalles del error general Documento: ' . var_export($e, true));
                }

                // Redirigir a la vista orders.show con el ID de la orden
                return redirect()->route('orders.show', ['order' => $order]);
            } else {
                // Log de errores de OpenPay
                Log::error('Respuesta no exitosa de OpenPay: ' . $response->getBody());

                // Guardar el mensaje de error en la variable de sesión
                Session::flash('openpay_error', 'Ocurrió un problema al procesar su pago. Intentarlo nuevamente con los datos correctos');

                // Redirigir a la vista orders.show con el ID de la orden
                return redirect()->back();
            }
        } catch (\Exception $e) {
            // Log de errores
            Log::error('Error en OpenPay: ' . $e->getMessage());

            // Guardar el mensaje de error en la variable de sesión
            Session::flash('openpay_error', 'Ocurrió un problema al procesar su pago. Intentarlo nuevamente con los datos correctos.');

            // Redirigir a la vista orders.show con el ID de la orden
            return redirect()->back();
        }
    }

    private function enviarAServicioSoap(Order $order)
    {
        try {
            // Crear instancia del cliente SOAP con configuración de contexto de transmisión
            $soapClient = new SoapClient("https://ws-erp.manager.cl/Flexline/Saco/Ws%20InyectaCtaCte/InyectaCtaCte.asmx?WSDL", [
                'stream_context' => stream_context_create([
                    'ssl' => [
                        'verify_peer' => true, // Habilitar verificación de SSL
                        'verify_peer_name' => true
                    ]
                ])
            ]);

            // Preparar datos para la solicitud SOAP
            $xmlData = $this->generarDatosXml($order);

            // Log del contenido XML antes de la llamada SOAP
            Log::info('Contenido XML enviado a SOAP: ' . $xmlData);

            // Verificar si el XML es vacío o nulo
            if (empty($xmlData)) {
                Log::error('Error: XML generado está vacío o nulo.');
                return;
            }

            // Llamada SOAP usando __soapCall
            $responseInyectaCtaCte = $soapClient->__soapCall('InyectarCtaCteXML', [
                'InyectarCtaCteXML' => [
                    '__sTextoXML' => $xmlData,
                ],
            ]);

            // Log de la respuesta SOAP usando var_export
            Log::info('Respuesta SOAP: ' . var_export($responseInyectaCtaCte, true));
        } catch (\SoapFault $e) {
            // Log de errores SOAP
            Log::error('Error en la solicitud SOAP: ' . $e->getMessage());
            // Manejar errores SOAP según sea necesario
        } catch (\Exception $e) {
            // Log de errores generales
            Log::error('Error en la solicitud SOAP: ' . $e->getMessage());
            // Manejar otros errores según sea necesario
        }
    }

    private function generarDocumentoXml(Order $order)
    {
        $xmlData = new SimpleXMLElement('<DOCUMENTO_LIST></DOCUMENTO_LIST>');
        $login = $xmlData->addChild('LOGIN');
        $login->addChild('usuario', 'flexline');
        $login->addChild('password', 'flexline');
        $documento = $xmlData->addChild('DOCUMENTO');
        $encabezado = $documento->addChild('ENCABEZADO');
        $encabezado->addChild('Empresa', '003');
        $encabezado->addChild('TipoDocto', 'PEDIDO WEBSACO');
        $encabezado->addChild('Correlativo', $order->id);
        $encabezado->addChild('CtaCte', $order->dni ? $order->dni : $order->ruc);
        $encabezado->addChild('Numero', '003-0000' . $order->id);
        $fecha = $order->created_at->format('d-m-Y');
        $encabezado->addChild('Fecha', $fecha);
        $encabezado->addChild('Proveedor');
        $encabezado->addChild('Cliente', $order->dni ? $order->dni : $order->ruc);
        $encabezado->addChild('Bodega', $order->selected_store);
        $encabezado->addChild('Bodega2');
        $encabezado->addChild('Local');
        $encabezado->addChild('Comprador');
        $encabezado->addChild('Vendedor', 'OFICINA');
        $encabezado->addChild('CentroCosto');
        $fechaVcto = $order->created_at->format('d-m-Y');
        $encabezado->addChild('FechaVcto', $fechaVcto);
        $encabezado->addChild('ListaPrecio', 'LP-LIM-WEBSACO');
        $encabezado->addChild('Analisis');
        $encabezado->addChild('Zona');
        $encabezado->addChild('TipoCta');
        $encabezado->addChild('Moneda', 'S/');
        $encabezado->addChild('Paridad', '1.00000000');
        $encabezado->addChild('RefTipoDocto');
        $encabezado->addChild('RefCorrelativo', '0');
        $encabezado->addChild('ReferenciaExterna', '0');
        $encabezado->addChild('Neto', '258.07000000');
        $encabezado->addChild('SubTotal', $order->total);
        $encabezado->addChild('Total', $order->total);
        $encabezado->addChild('NetoIngreso', '258.07000000');
        $encabezado->addChild('SubTotalIngreso', $order->total);
        $encabezado->addChild('TotalIngreso', $order->total);
        $encabezado->addChild('Centraliza');
        $encabezado->addChild('Valoriza');
        $encabezado->addChild('Costeo');
        $encabezado->addChild('Aprobacion', 'S');
        $encabezado->addChild('TipoComprobante');
        $encabezado->addChild('NroComprobante', '0');
        $encabezado->addChild('FechaComprobante', '01-01-1900');
        $encabezado->addChild('PeriodoLibro', '202401');
        $encabezado->addChild('FactorMonto', '0');
        $encabezado->addChild('FactorMontoProyectado', '0');
        $encabezado->addChild('TipoCtaCte', 'CLIENTE');
        $encabezado->addChild('IdCtaCte', $order->dni ? $order->dni : $order->ruc);
        $encabezado->addChild('Glosa');
        $encabezado->addChild('Comentario1');
        $encabezado->addChild('Comentario2');
        $encabezado->addChild('Comentario3');
        $encabezado->addChild('Comentario4');
        $encabezado->addChild('Estado');
        $encabezado->addChild('FechaEstado', '01-01-1900');
        $encabezado->addChild('NroMensaje', '0');
        $encabezado->addChild('Vigencia', 'S');
        $encabezado->addChild('Emitido', 'N');
        $encabezado->addChild('PorcentajeAsignado', '0.0');
        $encabezado->addChild('Adjuntos', 'N');
        $encabezado->addChild('Direccion');
        $encabezado->addChild('Ciudad');
        $encabezado->addChild('Comuna');
        $encabezado->addChild('EstadoDir');
        $encabezado->addChild('Pais');
        $encabezado->addChild('Contacto');
        $fechaModif = $order->created_at->format('d-m-Y');
        $encabezado->addChild('FechaModif', $fechaModif);
        //$encabezado->addChild('FechaModif', $order->created_at);
        $fechaUModif = $order->created_at->format('d-m-Y');
        $encabezado->addChild('FechaUModif', $fechaUModif);
        //$encabezado->addChild('FechaUModif', $order->created_at);
        $encabezado->addChild('UsuarioModif', 'ROOT');
        $encabezado->addChild('ComisionCantidad', '0');
        $encabezado->addChild('ComisionLPrecio', '0');
        $encabezado->addChild('ComisionMonto', '0');
        $encabezado->addChild('Hora', '13:12:06');
        $encabezado->addChild('Caja');
        $encabezado->addChild('Pago', '0.00000000');
        $encabezado->addChild('Donacion', '0.00000000');
        $encabezado->addChild('IdApertura', '0');
        $encabezado->addChild('DrCondPago', '0.00000000');
        $encabezado->addChild('PorcDr1', '0.00000000');
        $encabezado->addChild('PorcDr2', '0.00000000');
        $encabezado->addChild('PorcDr3', '0.00000000');
        $encabezado->addChild('PorcDr4', '0.00000000');
        $encabezado->addChild('Multipagina');
        $encabezado->addChild('NetoBimoneda', '0.00000000');
        $encabezado->addChild('SubtotalBimoneda', '0.00000000');
        $encabezado->addChild('TotalBimoneda', '0.00000000');
        $encabezado->addChild('ParidadBimoneda', '0.00000000');
        $encabezado->addChild('ParidadAdic', '0.00000000');
        $encabezado->addChild('AnalisisE1');
        $encabezado->addChild('AnalisisE2');
        $encabezado->addChild('AnalisisE3');
        $encabezado->addChild('AnalisisE4');
        $encabezado->addChild('UsuarioAprueba');
        $encabezado->addChild('ANALISISE5');
        $encabezado->addChild('ANALISISE6');
        $encabezado->addChild('ANALISISE7');
        $encabezado->addChild('ANALISISE8', 'admin');
        $encabezado->addChild('ANALISISE9');
        $encabezado->addChild('ANALISISE10');
        $encabezado->addChild('ANALISISE11');
        $encabezado->addChild('ANALISISE12');
        $encabezado->addChild('ANALISISE13', 'INMEDIATA');
        $encabezado->addChild('ANALISISE14', 'HASTA AGOTAR STOCK');
        $encabezado->addChild('ANALISISE15');
        $encabezado->addChild('ANALISISE16');
        $encabezado->addChild('ANALISISE17');
        $encabezado->addChild('ANALISISE18');
        $encabezado->addChild('ANALISISE19');
        $encabezado->addChild('ANALISISE20');
        $encabezado->addChild('IdFolioSucursal');
        $encabezado->addChild('SUPER_DR');
        $encabezado->addChild('usuariocierre');
        $encabezado->addChild('FechaCierre');
        $encabezado->addChild('AnalisisE21');
        $encabezado->addChild('AnalisisE22', '003-0000' . $order->id);
        $encabezado->addChild('AnalisisE23');
        $encabezado->addChild('AnalisisE24');
        $encabezado->addChild('AnalisisE25');
        $encabezado->addChild('AnalisisE26', '12 MESES');
        $encabezado->addChild('AnalisisE27');
        $encabezado->addChild('AnalisisE28');
        $encabezado->addChild('AnalisisE29');
        $encabezado->addChild('AnalisisE30', '0101');
        $fechaAprueba = $order->created_at->format('d-m-Y');
        $encabezado->addChild('FechaAprueba', $fechaAprueba);
        //$encabezado->addChild('FechaAprueba',  $order->created_at);

        $producto_1 = $documento->addChild('DETALLE');
        $producto_1->addChild('Empresa', '003');
        $producto_1->addChild('TipoDocto', 'PEDIDO WEBSACO');
        $producto_1->addChild('Correlativo', $order->id);
        $producto_1->addChild('Secuencia', '1');
        $producto_1->addChild('Linea', '1');

        $content = json_decode($order->content, true);
        $firstItem = reset($content);
        $sku = $firstItem['options']['sku'];
        $producto_1->addChild('Producto', $sku);

        $qty_1 = json_decode($order->content, true);
        $firstItem = reset($qty_1);
        $qty = $firstItem['qty'];
        $producto_1->addChild('Cantidad', $qty . '.00000000');

        $price_1 = json_decode($order->content, true);
        $firstItem = reset($price_1);
        $price = $firstItem['price'];
        $producto_1->addChild('Precio', $price . '.00000000');

        $producto_1->addChild('PorcentajeDR', '0.0000');
        $producto_1->addChild('SubTotal', '0.00000000');
        $producto_1->addChild('Impuesto', '0.00000000');
        $producto_1->addChild('Neto', '0.00000000');
        $producto_1->addChild('DRGlobal', '0.00000000');
        $producto_1->addChild('Costo', '0.00000000');
        $producto_1->addChild('Total', '0.00000000');
        $producto_1->addChild('PrecioAjustado', '0.00000000');
        $producto_1->addChild('UnidadIngreso', 'NIU');
        $producto_1->addChild('CantidadIngreso', $qty . '.00000000');
        $producto_1->addChild('PrecioIngreso', $price . '.00000000');
        $producto_1->addChild('SubTotalIngreso', '0.00000000');
        $producto_1->addChild('ImpuestoIngreso', '0.00000000');
        $producto_1->addChild('NetoIngreso', '0.00000000');
        $producto_1->addChild('DRGlobalIngreso', '0.00000000');
        $producto_1->addChild('TotalIngreso', '0.00000000');
        $producto_1->addChild('Serie');
        $producto_1->addChild('Lote');
        $fechaVcto = $order->created_at->format('d-m-Y');
        $producto_1->addChild('FechaVcto', $fechaVcto);
        $producto_1->addChild('TipoDoctoOrigen');
        $producto_1->addChild('CorrelativoOrigen', '0');
        $producto_1->addChild('SecuenciaOrigen', '0');
        $producto_1->addChild('Bodega', $order->selected_store);
        $producto_1->addChild('CentroCosto');
        $producto_1->addChild('Proceso');
        $producto_1->addChild('FactorInventario', '0');
        $producto_1->addChild('FactorInvProyectado',  -$qty . '.00000000');
        $fechaEntrega = $order->created_at->format('d-m-Y');
        $producto_1->addChild('FechaEntrega', $fechaEntrega);
        $producto_1->addChild('CantidadAsignada', '0.00000000');
        $fecha = $order->created_at->format('d-m-Y');
        $producto_1->addChild('Fecha', $fecha);
        $producto_1->addChild('Nivel', '0');
        $producto_1->addChild('SecciaProceso', '0');
        $producto_1->addChild('Comentario');
        $producto_1->addChild('Vigente', 'S');
        $producto_1->addChild('FechaModif');
        $producto_1->addChild('AUX_VALOR1');
        $producto_1->addChild('AUX_VALOR2');
        $producto_1->addChild('AUX_VALOR3');
        $producto_1->addChild('AUX_VALOR4');
        $producto_1->addChild('AUX_VALOR5');
        $producto_1->addChild('AUX_VALOR6');
        $producto_1->addChild('AUX_VALOR7');
        $producto_1->addChild('AUX_VALOR8');
        $producto_1->addChild('AUX_VALOR9');
        $producto_1->addChild('AUX_VALOR10');
        $producto_1->addChild('AUX_VALOR11');
        $producto_1->addChild('AUX_VALOR12');
        $producto_1->addChild('AUX_VALOR13');
        $producto_1->addChild('AUX_VALOR14');
        $producto_1->addChild('AUX_VALOR15');
        $producto_1->addChild('AUX_VALOR16');
        $producto_1->addChild('AUX_VALOR17');
        $producto_1->addChild('AUX_VALOR18');
        $producto_1->addChild('AUX_VALOR19');
        $producto_1->addChild('AUX_VALOR20');
        $producto_1->addChild('VALOR1');
        $producto_1->addChild('VALOR2');
        $producto_1->addChild('VALOR3');
        $producto_1->addChild('VALOR4');
        $producto_1->addChild('VALOR5');
        $producto_1->addChild('VALOR6');
        $producto_1->addChild('VALOR7');
        $producto_1->addChild('VALOR8');
        $producto_1->addChild('VALOR9');
        $producto_1->addChild('VALOR10');
        $producto_1->addChild('VALOR11');
        $producto_1->addChild('VALOR12');
        $producto_1->addChild('VALOR13');
        $producto_1->addChild('VALOR14');
        $producto_1->addChild('VALOR15');
        $producto_1->addChild('VALOR16');
        $producto_1->addChild('VALOR17');
        $producto_1->addChild('VALOR18');
        $producto_1->addChild('VALOR19');
        $producto_1->addChild('VALOR20');
        $producto_1->addChild('CUP', '0.00000000');
        $producto_1->addChild('Ubicacion', 'PRINCIPAL');
        $producto_1->addChild('Ubicacion2', 'PRINCIPAL');
        $producto_1->addChild('Cuenta');
        $producto_1->addChild('RFGrupo1');
        $producto_1->addChild('RFGrupo2');
        $producto_1->addChild('RFGrupo3');
        $producto_1->addChild('Estado_Prod');
        $producto_1->addChild('Placa');
        $producto_1->addChild('Transportista');
        $producto_1->addChild('TipoPallet');
        $producto_1->addChild('TipoCaja');
        $producto_1->addChild('FactorImpto', '0.00000000');
        $producto_1->addChild('SeriePrint');
        $producto_1->addChild('PrecioBimoneda', '0.00000000');
        $producto_1->addChild('SubtotalBimoneda', '0.00000000');
        $producto_1->addChild('ImpuestoBimoneda', '0.00000000');
        $producto_1->addChild('NetoBimoneda', '0.00000000');
        $producto_1->addChild('DrGlobalBimoneda', '0.00000000');
        $producto_1->addChild('TotalBimoneda', '0.00000000');
        $producto_1->addChild('PrecioListaP', '0.00000000');
        $producto_1->addChild('Analisis1');
        $producto_1->addChild('Analisis2');
        $producto_1->addChild('Analisis3');
        $producto_1->addChild('Analisis4');
        $producto_1->addChild('Analisis5');
        $producto_1->addChild('Analisis6');
        $producto_1->addChild('Analisis7');
        $producto_1->addChild('Analisis8');
        $producto_1->addChild('Analisis9');
        $producto_1->addChild('Analisis10');
        $producto_1->addChild('Analisis11');
        $producto_1->addChild('Analisis12');
        $producto_1->addChild('Analisis13');
        $producto_1->addChild('Analisis14', '003-0000' . $order->id);
        $producto_1->addChild('Analisis15');
        $producto_1->addChild('Analisis16');
        $producto_1->addChild('Analisis17');
        $producto_1->addChild('Analisis18');
        $producto_1->addChild('Analisis19');
        $producto_1->addChild('Analisis20');
        $producto_1->addChild('UniMedDynamic', '1.00000000');
        $producto_1->addChild('ProdAlias');
        $producto_1->addChild('FechaVigenciaLp');
        $producto_1->addChild('LoteDestino');
        $producto_1->addChild('SerieDestino');
        $producto_1->addChild('DoctoOrigenVal', 'N');
        $producto_1->addChild('DRGlobal1', '0.00000000');
        $producto_1->addChild('DRGlobal2', '0.00000000');
        $producto_1->addChild('DRGlobal3', '0.00000000');
        $producto_1->addChild('DRGlobal4', '0.00000000');
        $producto_1->addChild('DRGlobal5', '0.00000000');
        $producto_1->addChild('DRGlobal1Ingreso', '0.00000000');
        $producto_1->addChild('DRGlobal2Ingreso', '0.00000000');
        $producto_1->addChild('DRGlobal3Ingreso', '0.00000000');
        $producto_1->addChild('DRGlobal4Ingreso', '0.00000000');
        $producto_1->addChild('DRGlobal5Ingreso', '0.00000000');
        $producto_1->addChild('DRGlobal1Bimoneda', '0.00000000');
        $producto_1->addChild('DRGlobal2Bimoneda', '0.00000000');
        $producto_1->addChild('DRGlobal3Bimoneda', '0.00000000');
        $producto_1->addChild('DRGlobal4Bimoneda', '0.00000000');
        $producto_1->addChild('DRGlobal5Bimoneda', '0.00000000');
        $producto_1->addChild('PorcentajeDr2', '0.0000');
        $producto_1->addChild('PorcentajeDr3', '0.0000');
        $producto_1->addChild('PorcentajeDr4', '0.0000');
        $producto_1->addChild('PorcentajeDr5', '0.0000');
        $producto_1->addChild('ValPorcentajeDr1', '0.00000000');
        $producto_1->addChild('ValPorcentajeDr2', '0.00000000');
        $producto_1->addChild('ValPorcentajeDr3', '0.00000000');
        $producto_1->addChild('ValPorcentajeDr4', '0.00000000');
        $producto_1->addChild('ValPorcentajeDr5', '0.00000000');
        $producto_1->addChild('ValPorcentajeDr1Ingreso', '0.00000000');
        $producto_1->addChild('ValPorcentajeDr2Ingreso', '0.00000000');
        $producto_1->addChild('ValPorcentajeDr3Ingreso', '0.00000000');
        $producto_1->addChild('ValPorcentajeDr4Ingreso', '0.00000000');
        $producto_1->addChild('ValPorcentajeDr5Ingreso', '0.00000000');
        $producto_1->addChild('ValPorcentajeDr1Bimoneda', '0.00000000');
        $producto_1->addChild('ValPorcentajeDr2Bimoneda', '0.00000000');
        $producto_1->addChild('ValPorcentajeDr3Bimoneda', '0.00000000');
        $producto_1->addChild('ValPorcentajeDr4Bimoneda', '0.00000000');
        $producto_1->addChild('ValPorcentajeDr5Bimoneda', '0.00000000');
        $producto_1->addChild('CostoBimoneda', '0');
        $producto_1->addChild('CupBimoneda', '0');
        $producto_1->addChild('MontoAsignado', '0.00000000');
        $producto_1->addChild('Analisis21');
        $producto_1->addChild('Analisis22');
        $producto_1->addChild('Analisis23');
        $producto_1->addChild('Analisis24');
        $producto_1->addChild('Analisis25');
        $producto_1->addChild('Analisis26');
        $producto_1->addChild('Analisis27');
        $producto_1->addChild('Analisis28');
        $producto_1->addChild('Analisis29', '1000');
        $producto_1->addChild('Analisis30', '10');
        $producto_1->addChild('Receta');

        $content = json_decode($order->content, true);
        if (is_array($content) && count($content) >= 2) {
            next($content);
            $secondItem = current($content);
            if ($secondItem) {

                $producto_2 = $documento->addChild('DETALLE');
                $producto_2->addChild('Empresa', '003');
                $producto_2->addChild('TipoDocto', 'PEDIDO WEBSACO');
                $producto_2->addChild('Correlativo', $order->id);
                $producto_2->addChild('Secuencia', '2');
                $producto_2->addChild('Linea', '2');

                $sku_value2 = $secondItem['options']['sku'];
                $producto_2->addChild('Producto', $sku_value2);

                $qty_2 = $secondItem['qty'];
                $producto_2->addChild('Cantidad', $qty_2 . '.00000000');

                $price_2 = $secondItem['price'];
                $producto_2->addChild('Precio',  $price_2 . '.00000000');


                $producto_2->addChild('PorcentajeDR', '0.0000');
                $producto_2->addChild('SubTotal', '0.00000000');
                $producto_2->addChild('Impuesto', '0.00000000');
                $producto_2->addChild('Neto', '0.00000000');
                $producto_2->addChild('DRGlobal', '0.00000000');
                $producto_2->addChild('Costo', '0.00000000');
                $producto_2->addChild('Total', '0.00000000');
                $producto_2->addChild('PrecioAjustado', '0.00000000');
                $producto_2->addChild('UnidadIngreso', 'NIU');
                $producto_2->addChild('CantidadIngreso', $qty_2 . '.00000000');
                $producto_2->addChild('PrecioIngreso', $price_2 . '.00000000');
                $producto_2->addChild('SubTotalIngreso', $price_2 . '.00000000');
                $producto_2->addChild('ImpuestoIngreso', '0.00000000');
                $producto_2->addChild('NetoIngreso',  number_format(($price . '.00000000') / 1.18, 8));
                $producto_2->addChild('DRGlobalIngreso', '0.00000000');
                $producto_2->addChild('TotalIngreso', '0.00000000');
                $producto_2->addChild('Serie');
                $producto_2->addChild('Lote');
                $fechaVcto = $order->created_at->format('d-m-Y');
                $producto_2->addChild('FechaVcto', $fechaVcto);
                $producto_2->addChild('TipoDoctoOrigen');
                $producto_2->addChild('CorrelativoOrigen', '0');
                $producto_2->addChild('SecuenciaOrigen', '0');
                $producto_2->addChild('Bodega', $order->selected_store);
                $producto_2->addChild('CentroCosto');
                $producto_2->addChild('Proceso');
                $producto_2->addChild('FactorInventario', '0');
                $producto_2->addChild('FactorInvProyectado', -$qty_2 . '.00000000');
                $fechaEntrega = $order->created_at->format('d-m-Y');
                $producto_2->addChild('FechaEntrega', $fechaEntrega);
                $producto_2->addChild('CantidadAsignada', '0.00000000');
                $fecha = $order->created_at->format('d-m-Y');
                $producto_2->addChild('Fecha', $fecha);
                $producto_2->addChild('Nivel', '0');
                $producto_2->addChild('SecciaProceso', '0');
                $producto_2->addChild('Comentario');
                $producto_2->addChild('Vigente', 'S');
                $producto_2->addChild('FechaModif');
                $producto_2->addChild('AUX_VALOR1');
                $producto_2->addChild('AUX_VALOR2');
                $producto_2->addChild('AUX_VALOR3');
                $producto_2->addChild('AUX_VALOR4');
                $producto_2->addChild('AUX_VALOR5');
                $producto_2->addChild('AUX_VALOR6');
                $producto_2->addChild('AUX_VALOR7');
                $producto_2->addChild('AUX_VALOR8');
                $producto_2->addChild('AUX_VALOR9');
                $producto_2->addChild('AUX_VALOR10');
                $producto_2->addChild('AUX_VALOR11');
                $producto_2->addChild('AUX_VALOR12');
                $producto_2->addChild('AUX_VALOR13');
                $producto_2->addChild('AUX_VALOR14');
                $producto_2->addChild('AUX_VALOR15');
                $producto_2->addChild('AUX_VALOR16');
                $producto_2->addChild('AUX_VALOR17');
                $producto_2->addChild('AUX_VALOR18');
                $producto_2->addChild('AUX_VALOR19');
                $producto_2->addChild('AUX_VALOR20');
                $producto_2->addChild('VALOR1');
                $producto_2->addChild('VALOR2');
                $producto_2->addChild('VALOR3');
                $producto_2->addChild('VALOR4');
                $producto_2->addChild('VALOR5');
                $producto_2->addChild('VALOR6');
                $producto_2->addChild('VALOR7');
                $producto_2->addChild('VALOR8');
                $producto_2->addChild('VALOR9');
                $producto_2->addChild('VALOR10');
                $producto_2->addChild('VALOR11');
                $producto_2->addChild('VALOR12');
                $producto_2->addChild('VALOR13');
                $producto_2->addChild('VALOR14');
                $producto_2->addChild('VALOR15');
                $producto_2->addChild('VALOR16');
                $producto_2->addChild('VALOR17');
                $producto_2->addChild('VALOR18');
                $producto_2->addChild('VALOR19');
                $producto_2->addChild('VALOR20');
                $producto_2->addChild('CUP', '0.00000000');
                $producto_2->addChild('Ubicacion', 'PRINCIPAL');
                $producto_2->addChild('Ubicacion2', 'PRINCIPAL');
                $producto_2->addChild('Cuenta');
                $producto_2->addChild('RFGrupo1');
                $producto_2->addChild('RFGrupo2');
                $producto_2->addChild('RFGrupo3');
                $producto_2->addChild('Estado_Prod');
                $producto_2->addChild('Placa');
                $producto_2->addChild('Transportista');
                $producto_2->addChild('TipoPallet');
                $producto_2->addChild('TipoCaja');
                $producto_2->addChild('FactorImpto', '0.00000000');
                $producto_2->addChild('SeriePrint');
                $producto_2->addChild('PrecioBimoneda', '0.00000000');
                $producto_2->addChild('SubtotalBimoneda', '0.00000000');
                $producto_2->addChild('ImpuestoBimoneda', '0.00000000');
                $producto_2->addChild('NetoBimoneda', '0.00000000');
                $producto_2->addChild('DrGlobalBimoneda', '0.00000000');
                $producto_2->addChild('TotalBimoneda', '0.00000000');
                $producto_2->addChild('PrecioListaP', '0.00000000');
                $producto_2->addChild('Analisis1');
                $producto_2->addChild('Analisis2');
                $producto_2->addChild('Analisis3');
                $producto_2->addChild('Analisis4');
                $producto_2->addChild('Analisis5');
                $producto_2->addChild('Analisis6');
                $producto_2->addChild('Analisis7');
                $producto_2->addChild('Analisis8');
                $producto_2->addChild('Analisis9');
                $producto_2->addChild('Analisis10');
                $producto_2->addChild('Analisis11');
                $producto_2->addChild('Analisis12');
                $producto_2->addChild('Analisis13');
                $producto_2->addChild('Analisis14', '003-0000' . $order->id);
                $producto_2->addChild('Analisis15');
                $producto_2->addChild('Analisis16');
                $producto_2->addChild('Analisis17');
                $producto_2->addChild('Analisis18');
                $producto_2->addChild('Analisis19');
                $producto_2->addChild('Analisis20');
                $producto_2->addChild('UniMedDynamic', '1.00000000');
                $producto_2->addChild('ProdAlias');
                $producto_2->addChild('FechaVigenciaLp');
                $producto_2->addChild('LoteDestino');
                $producto_2->addChild('SerieDestino');
                $producto_2->addChild('DoctoOrigenVal', 'N');
                $producto_2->addChild('DRGlobal1', '0.00000000');
                $producto_2->addChild('DRGlobal2', '0.00000000');
                $producto_2->addChild('DRGlobal3', '0.00000000');
                $producto_2->addChild('DRGlobal4', '0.00000000');
                $producto_2->addChild('DRGlobal5', '0.00000000');
                $producto_2->addChild('DRGlobal1Ingreso', '0.00000000');
                $producto_2->addChild('DRGlobal2Ingreso', '0.00000000');
                $producto_2->addChild('DRGlobal3Ingreso', '0.00000000');
                $producto_2->addChild('DRGlobal4Ingreso', '0.00000000');
                $producto_2->addChild('DRGlobal5Ingreso', '0.00000000');
                $producto_2->addChild('DRGlobal1Bimoneda', '0.00000000');
                $producto_2->addChild('DRGlobal2Bimoneda', '0.00000000');
                $producto_2->addChild('DRGlobal3Bimoneda', '0.00000000');
                $producto_2->addChild('DRGlobal4Bimoneda', '0.00000000');
                $producto_2->addChild('DRGlobal5Bimoneda', '0.00000000');
                $producto_2->addChild('PorcentajeDr2', '0.0000');
                $producto_2->addChild('PorcentajeDr3', '0.0000');
                $producto_2->addChild('PorcentajeDr4', '0.0000');
                $producto_2->addChild('PorcentajeDr5', '0.0000');
                $producto_2->addChild('ValPorcentajeDr1', '0.00000000');
                $producto_2->addChild('ValPorcentajeDr2', '0.00000000');
                $producto_2->addChild('ValPorcentajeDr3', '0.00000000');
                $producto_2->addChild('ValPorcentajeDr4', '0.00000000');
                $producto_2->addChild('ValPorcentajeDr5', '0.00000000');
                $producto_2->addChild('ValPorcentajeDr1Ingreso', '0.00000000');
                $producto_2->addChild('ValPorcentajeDr2Ingreso', '0.00000000');
                $producto_2->addChild('ValPorcentajeDr3Ingreso', '0.00000000');
                $producto_2->addChild('ValPorcentajeDr4Ingreso', '0.00000000');
                $producto_2->addChild('ValPorcentajeDr5Ingreso', '0.00000000');
                $producto_2->addChild('ValPorcentajeDr1Bimoneda', '0.00000000');
                $producto_2->addChild('ValPorcentajeDr2Bimoneda', '0.00000000');
                $producto_2->addChild('ValPorcentajeDr3Bimoneda', '0.00000000');
                $producto_2->addChild('ValPorcentajeDr4Bimoneda', '0.00000000');
                $producto_2->addChild('ValPorcentajeDr5Bimoneda', '0.00000000');
                $producto_2->addChild('CostoBimoneda', '0');
                $producto_2->addChild('CupBimoneda', '0');
                $producto_2->addChild('MontoAsignado', '0.00000000');
                $producto_2->addChild('Analisis21');
                $producto_2->addChild('Analisis22');
                $producto_2->addChild('Analisis23');
                $producto_2->addChild('Analisis24');
                $producto_2->addChild('Analisis25');
                $producto_2->addChild('Analisis26');
                $producto_2->addChild('Analisis27');
                $producto_2->addChild('Analisis28');
                $producto_2->addChild('Analisis29', '1000');
                $producto_2->addChild('Analisis30', '10');
                $producto_2->addChild('Receta');
            }
        }

        $sku_3 = json_decode($order->content, true);
        if (is_array($sku_3) && count($sku_3) >= 3) {

            for ($i = 0; $i < 2; $i++) {
                next($sku_3);
            }
            $thirdItem = current($sku_3);
            if ($thirdItem) {

                $producto_3 = $documento->addChild('DETALLE');
                $producto_3->addChild('Empresa', '003');
                $producto_3->addChild('TipoDocto', 'PEDIDO WEBSACO');
                $producto_3->addChild('Correlativo', $order->id);
                $producto_3->addChild('Secuencia', '3');
                $producto_3->addChild('Linea', '3');
                $sku_value3 = $thirdItem['options']['sku'];
                $producto_3->addChild('Producto', $sku_value3);

                $qty_3 = $thirdItem['qty'];
                $producto_3->addChild('Cantidad', $qty_3 . '.00000000');

                $price_3 = $thirdItem['price'];
                $producto_3->addChild('Precio',  $price_3 . '.00000000');

                $producto_3->addChild('PorcentajeDR', '0.0000');
                $producto_3->addChild('SubTotal', '0.00000000');
                $producto_3->addChild('Impuesto', '0.00000000');
                $producto_3->addChild('Neto', '0.00000000');
                $producto_3->addChild('DRGlobal', '0.00000000');
                $producto_3->addChild('Costo', '0.00000000');
                $producto_3->addChild('Total', '0.00000000');
                $producto_3->addChild('PrecioAjustado', '0.00000000');
                $producto_3->addChild('UnidadIngreso', 'NIU');
                $producto_3->addChild('CantidadIngreso',  $qty_3 . '.00000000');
                $producto_3->addChild('PrecioIngreso',  $price_3 . '.00000000');
                $producto_3->addChild('SubTotalIngreso', '0.00000000');
                $producto_3->addChild('ImpuestoIngreso', '0.00000000');
                $producto_3->addChild('NetoIngreso', '0.00000000');
                $producto_3->addChild('DRGlobalIngreso', '0.00000000');
                $producto_3->addChild('TotalIngreso', '0.00000000');
                $producto_3->addChild('Serie');
                $producto_3->addChild('Lote');
                $fechaVcto = $order->created_at->format('d-m-Y');
                $producto_3->addChild('FechaVcto', $fechaVcto);
                $producto_3->addChild('TipoDoctoOrigen');
                $producto_3->addChild('CorrelativoOrigen', '0');
                $producto_3->addChild('SecuenciaOrigen', '0');
                $producto_3->addChild('Bodega', $order->selected_store);
                $producto_3->addChild('CentroCosto');
                $producto_3->addChild('Proceso');
                $producto_3->addChild('FactorInventario', '0');
                $producto_3->addChild('FactorInvProyectado', '-1');
                $fechaEntrega = $order->created_at->format('d-m-Y');
                $producto_3->addChild('FechaEntrega', $fechaEntrega);
                $producto_3->addChild('CantidadAsignada', '0.00000000');
                $fecha = $order->created_at->format('d-m-Y');
                $producto_3->addChild('Fecha', $fecha);
                $producto_3->addChild('Nivel', '0');
                $producto_3->addChild('SecciaProceso', '0');
                $producto_3->addChild('Comentario');
                $producto_3->addChild('Vigente', 'S');
                $producto_3->addChild('FechaModif');
                $producto_3->addChild('AUX_VALOR1');
                $producto_3->addChild('AUX_VALOR2');
                $producto_3->addChild('AUX_VALOR3');
                $producto_3->addChild('AUX_VALOR4');
                $producto_3->addChild('AUX_VALOR5');
                $producto_3->addChild('AUX_VALOR6');
                $producto_3->addChild('AUX_VALOR7');
                $producto_3->addChild('AUX_VALOR8');
                $producto_3->addChild('AUX_VALOR9');
                $producto_3->addChild('AUX_VALOR10');
                $producto_3->addChild('AUX_VALOR11');
                $producto_3->addChild('AUX_VALOR12');
                $producto_3->addChild('AUX_VALOR13');
                $producto_3->addChild('AUX_VALOR14');
                $producto_3->addChild('AUX_VALOR15');
                $producto_3->addChild('AUX_VALOR16');
                $producto_3->addChild('AUX_VALOR17');
                $producto_3->addChild('AUX_VALOR18');
                $producto_3->addChild('AUX_VALOR19');
                $producto_3->addChild('AUX_VALOR20');
                $producto_3->addChild('VALOR1');
                $producto_3->addChild('VALOR2');
                $producto_3->addChild('VALOR3');
                $producto_3->addChild('VALOR4');
                $producto_3->addChild('VALOR5');
                $producto_3->addChild('VALOR6');
                $producto_3->addChild('VALOR7');
                $producto_3->addChild('VALOR8');
                $producto_3->addChild('VALOR9');
                $producto_3->addChild('VALOR10');
                $producto_3->addChild('VALOR11');
                $producto_3->addChild('VALOR12');
                $producto_3->addChild('VALOR13');
                $producto_3->addChild('VALOR14');
                $producto_3->addChild('VALOR15');
                $producto_3->addChild('VALOR16');
                $producto_3->addChild('VALOR17');
                $producto_3->addChild('VALOR18');
                $producto_3->addChild('VALOR19');
                $producto_3->addChild('VALOR20');
                $producto_3->addChild('CUP', '0.00000000');
                $producto_3->addChild('Ubicacion', 'PRINCIPAL');
                $producto_3->addChild('Ubicacion2', 'PRINCIPAL');
                $producto_3->addChild('Cuenta');
                $producto_3->addChild('RFGrupo1');
                $producto_3->addChild('RFGrupo2');
                $producto_3->addChild('RFGrupo3');
                $producto_3->addChild('Estado_Prod');
                $producto_3->addChild('Placa');
                $producto_3->addChild('Transportista');
                $producto_3->addChild('TipoPallet');
                $producto_3->addChild('TipoCaja');
                $producto_3->addChild('FactorImpto', '0.00000000');
                $producto_3->addChild('SeriePrint');
                $producto_3->addChild('PrecioBimoneda', '0.00000000');
                $producto_3->addChild('SubtotalBimoneda', '0.00000000');
                $producto_3->addChild('ImpuestoBimoneda', '0.00000000');
                $producto_3->addChild('NetoBimoneda', '0.00000000');
                $producto_3->addChild('DrGlobalBimoneda', '0.00000000');
                $producto_3->addChild('TotalBimoneda', '0.00000000');
                $producto_3->addChild('PrecioListaP', '0.00000000');
                $producto_3->addChild('Analisis1');
                $producto_3->addChild('Analisis2');
                $producto_3->addChild('Analisis3');
                $producto_3->addChild('Analisis4');
                $producto_3->addChild('Analisis5');
                $producto_3->addChild('Analisis6');
                $producto_3->addChild('Analisis7');
                $producto_3->addChild('Analisis8');
                $producto_3->addChild('Analisis9');
                $producto_3->addChild('Analisis10');
                $producto_3->addChild('Analisis11');
                $producto_3->addChild('Analisis12');
                $producto_3->addChild('Analisis13');
                $producto_3->addChild('Analisis14', '003-0000' . $order->id);
                $producto_3->addChild('Analisis15');
                $producto_3->addChild('Analisis16');
                $producto_3->addChild('Analisis17');
                $producto_3->addChild('Analisis18');
                $producto_3->addChild('Analisis19');
                $producto_3->addChild('Analisis20');
                $producto_3->addChild('UniMedDynamic', '1.00000000');
                $producto_3->addChild('ProdAlias');
                $producto_3->addChild('FechaVigenciaLp');
                $producto_3->addChild('LoteDestino');
                $producto_3->addChild('SerieDestino');
                $producto_3->addChild('DoctoOrigenVal', 'N');
                $producto_3->addChild('DRGlobal1', '0.00000000');
                $producto_3->addChild('DRGlobal2', '0.00000000');
                $producto_3->addChild('DRGlobal3', '0.00000000');
                $producto_3->addChild('DRGlobal4', '0.00000000');
                $producto_3->addChild('DRGlobal5', '0.00000000');
                $producto_3->addChild('DRGlobal1Ingreso', '0.00000000');
                $producto_3->addChild('DRGlobal2Ingreso', '0.00000000');
                $producto_3->addChild('DRGlobal3Ingreso', '0.00000000');
                $producto_3->addChild('DRGlobal4Ingreso', '0.00000000');
                $producto_3->addChild('DRGlobal5Ingreso', '0.00000000');
                $producto_3->addChild('DRGlobal1Bimoneda', '0.00000000');
                $producto_3->addChild('DRGlobal2Bimoneda', '0.00000000');
                $producto_3->addChild('DRGlobal3Bimoneda', '0.00000000');
                $producto_3->addChild('DRGlobal4Bimoneda', '0.00000000');
                $producto_3->addChild('DRGlobal5Bimoneda', '0.00000000');
                $producto_3->addChild('PorcentajeDr2', '0.0000');
                $producto_3->addChild('PorcentajeDr3', '0.0000');
                $producto_3->addChild('PorcentajeDr4', '0.0000');
                $producto_3->addChild('PorcentajeDr5', '0.0000');
                $producto_3->addChild('ValPorcentajeDr1', '0.00000000');
                $producto_3->addChild('ValPorcentajeDr2', '0.00000000');
                $producto_3->addChild('ValPorcentajeDr3', '0.00000000');
                $producto_3->addChild('ValPorcentajeDr4', '0.00000000');
                $producto_3->addChild('ValPorcentajeDr5', '0.00000000');
                $producto_3->addChild('ValPorcentajeDr1Ingreso', '0.00000000');
                $producto_3->addChild('ValPorcentajeDr2Ingreso', '0.00000000');
                $producto_3->addChild('ValPorcentajeDr3Ingreso', '0.00000000');
                $producto_3->addChild('ValPorcentajeDr4Ingreso', '0.00000000');
                $producto_3->addChild('ValPorcentajeDr5Ingreso', '0.00000000');
                $producto_3->addChild('ValPorcentajeDr1Bimoneda', '0.00000000');
                $producto_3->addChild('ValPorcentajeDr2Bimoneda', '0.00000000');
                $producto_3->addChild('ValPorcentajeDr3Bimoneda', '0.00000000');
                $producto_3->addChild('ValPorcentajeDr4Bimoneda', '0.00000000');
                $producto_3->addChild('ValPorcentajeDr5Bimoneda', '0.00000000');
                $producto_3->addChild('CostoBimoneda', '0');
                $producto_3->addChild('CupBimoneda', '0');
                $producto_3->addChild('MontoAsignado', '0.00000000');
                $producto_3->addChild('Analisis21');
                $producto_3->addChild('Analisis22');
                $producto_3->addChild('Analisis23');
                $producto_3->addChild('Analisis24');
                $producto_3->addChild('Analisis25');
                $producto_3->addChild('Analisis26');
                $producto_3->addChild('Analisis27');
                $producto_3->addChild('Analisis28');
                $producto_3->addChild('Analisis29', '1000');
                $producto_3->addChild('Analisis30', '10');
                $producto_3->addChild('Receta');
            }
        }

        $sku_4 = json_decode($order->content, true);
        if (is_array($sku_4) && count($sku_4) >= 4) {
            for ($i = 0; $i < 3; $i++) {
                next($sku_4);
            }
            $fourthItem = current($sku_4);
            if ($fourthItem) {

                $producto_4 = $documento->addChild('DETALLE');
                $producto_4->addChild('Empresa', '003');
                $producto_4->addChild('TipoDocto', 'PEDIDO WEBSACO');
                $producto_4->addChild('Correlativo', $order->id);
                $producto_4->addChild('Secuencia', '4');
                $producto_4->addChild('Linea', '4');
                $sku_value4 = $fourthItem['options']['sku'];
                $producto_4->addChild('Producto', $sku_value4);
                $qty_4 = $fourthItem['qty'];
                $producto_4->addChild('Cantidad', $qty_4 . '.00000000');
                $price_4 = $fourthItem['price'];
                $producto_4->addChild('Precio', $price_4 . '.00000000');
                $producto_4->addChild('PorcentajeDR', '0.0000');
                $producto_4->addChild('SubTotal', '0.00000000');
                $producto_4->addChild('Impuesto', '0.00000000');
                $producto_4->addChild('Neto', '0.00000000');
                $producto_4->addChild('DRGlobal', '0.00000000');
                $producto_4->addChild('Costo', '0.00000000');
                $producto_4->addChild('Total', '0.00000000');
                $producto_4->addChild('PrecioAjustado', '0.00000000');
                $producto_4->addChild('UnidadIngreso', 'NIU');
                $producto_4->addChild('CantidadIngreso',  $qty_4 . '.00000000');
                $producto_4->addChild('PrecioIngreso', $price_4 . '.00000000');
                $producto_4->addChild('SubTotalIngreso', '0.00000000');
                $producto_4->addChild('ImpuestoIngreso', '0.00000000');
                $producto_4->addChild('NetoIngreso', '0.00000000');
                $producto_4->addChild('DRGlobalIngreso', '0.00000000');
                $producto_4->addChild('TotalIngreso', '0.00000000');
                $producto_4->addChild('Serie');
                $producto_4->addChild('Lote');
                $fechaVcto = $order->created_at->format('d-m-Y');
                $producto_4->addChild('FechaVcto', $fechaVcto);
                //$producto_4->addChild('FechaVcto',  $order->created_at);
                $producto_4->addChild('TipoDoctoOrigen');
                $producto_4->addChild('CorrelativoOrigen', '0');
                $producto_4->addChild('SecuenciaOrigen', '0');
                $producto_4->addChild('Bodega', $order->selected_store);
                $producto_4->addChild('CentroCosto');
                $producto_4->addChild('Proceso');
                $producto_4->addChild('FactorInventario', '0');
                $producto_4->addChild('FactorInvProyectado', '-1');
                $fechaEntrega = $order->created_at->format('d-m-Y');
                $producto_4->addChild('FechaEntrega', $fechaEntrega);
                //$producto_4->addChild('FechaEntrega',  $order->created_at);
                $producto_4->addChild('CantidadAsignada', '0.00000000');
                $fecha = $order->created_at->format('d-m-Y');
                $producto_4->addChild('Fecha', $fecha);
                //$producto_4->addChild('Fecha',  $order->created_at);
                $producto_4->addChild('Nivel', '0');
                $producto_4->addChild('SecciaProceso', '0');
                $producto_4->addChild('Comentario');
                $producto_4->addChild('Vigente', 'S');
                $producto_4->addChild('FechaModif');
                $producto_4->addChild('AUX_VALOR1');
                $producto_4->addChild('AUX_VALOR2');
                $producto_4->addChild('AUX_VALOR3');
                $producto_4->addChild('AUX_VALOR4');
                $producto_4->addChild('AUX_VALOR5');
                $producto_4->addChild('AUX_VALOR6');
                $producto_4->addChild('AUX_VALOR7');
                $producto_4->addChild('AUX_VALOR8');
                $producto_4->addChild('AUX_VALOR9');
                $producto_4->addChild('AUX_VALOR10');
                $producto_4->addChild('AUX_VALOR11');
                $producto_4->addChild('AUX_VALOR12');
                $producto_4->addChild('AUX_VALOR13');
                $producto_4->addChild('AUX_VALOR14');
                $producto_4->addChild('AUX_VALOR15');
                $producto_4->addChild('AUX_VALOR16');
                $producto_4->addChild('AUX_VALOR17');
                $producto_4->addChild('AUX_VALOR18');
                $producto_4->addChild('AUX_VALOR19');
                $producto_4->addChild('AUX_VALOR20');
                $producto_4->addChild('VALOR1');
                $producto_4->addChild('VALOR2');
                $producto_4->addChild('VALOR3');
                $producto_4->addChild('VALOR4');
                $producto_4->addChild('VALOR5');
                $producto_4->addChild('VALOR6');
                $producto_4->addChild('VALOR7');
                $producto_4->addChild('VALOR8');
                $producto_4->addChild('VALOR9');
                $producto_4->addChild('VALOR10');
                $producto_4->addChild('VALOR11');
                $producto_4->addChild('VALOR12');
                $producto_4->addChild('VALOR13');
                $producto_4->addChild('VALOR14');
                $producto_4->addChild('VALOR15');
                $producto_4->addChild('VALOR16');
                $producto_4->addChild('VALOR17');
                $producto_4->addChild('VALOR18');
                $producto_4->addChild('VALOR19');
                $producto_4->addChild('VALOR20');
                $producto_4->addChild('CUP', '0.00000000');
                $producto_4->addChild('Ubicacion', 'PRINCIPAL');
                $producto_4->addChild('Ubicacion2', 'PRINCIPAL');
                $producto_4->addChild('Cuenta');
                $producto_4->addChild('RFGrupo1');
                $producto_4->addChild('RFGrupo2');
                $producto_4->addChild('RFGrupo3');
                $producto_4->addChild('Estado_Prod');
                $producto_4->addChild('Placa');
                $producto_4->addChild('Transportista');
                $producto_4->addChild('TipoPallet');
                $producto_4->addChild('TipoCaja');
                $producto_4->addChild('FactorImpto', '0.00000000');
                $producto_4->addChild('SeriePrint');
                $producto_4->addChild('PrecioBimoneda', '0.00000000');
                $producto_4->addChild('SubtotalBimoneda', '0.00000000');
                $producto_4->addChild('ImpuestoBimoneda', '0.00000000');
                $producto_4->addChild('NetoBimoneda', '0.00000000');
                $producto_4->addChild('DrGlobalBimoneda', '0.00000000');
                $producto_4->addChild('TotalBimoneda', '0.00000000');
                $producto_4->addChild('PrecioListaP', '0.00000000');
                $producto_4->addChild('Analisis1');
                $producto_4->addChild('Analisis2');
                $producto_4->addChild('Analisis3');
                $producto_4->addChild('Analisis4');
                $producto_4->addChild('Analisis5');
                $producto_4->addChild('Analisis6');
                $producto_4->addChild('Analisis7');
                $producto_4->addChild('Analisis8');
                $producto_4->addChild('Analisis9');
                $producto_4->addChild('Analisis10');
                $producto_4->addChild('Analisis11');
                $producto_4->addChild('Analisis12');
                $producto_4->addChild('Analisis13');
                $producto_4->addChild('Analisis14', '003-0000' . $order->id);
                $producto_4->addChild('Analisis15');
                $producto_4->addChild('Analisis16');
                $producto_4->addChild('Analisis17');
                $producto_4->addChild('Analisis18');
                $producto_4->addChild('Analisis19');
                $producto_4->addChild('Analisis20');
                $producto_4->addChild('UniMedDynamic', '1.00000000');
                $producto_4->addChild('ProdAlias');
                $producto_4->addChild('FechaVigenciaLp');
                $producto_4->addChild('LoteDestino');
                $producto_4->addChild('SerieDestino');
                $producto_4->addChild('DoctoOrigenVal', 'N');
                $producto_4->addChild('DRGlobal1', '0.00000000');
                $producto_4->addChild('DRGlobal2', '0.00000000');
                $producto_4->addChild('DRGlobal3', '0.00000000');
                $producto_4->addChild('DRGlobal4', '0.00000000');
                $producto_4->addChild('DRGlobal5', '0.00000000');
                $producto_4->addChild('DRGlobal1Ingreso', '0.00000000');
                $producto_4->addChild('DRGlobal2Ingreso', '0.00000000');
                $producto_4->addChild('DRGlobal3Ingreso', '0.00000000');
                $producto_4->addChild('DRGlobal4Ingreso', '0.00000000');
                $producto_4->addChild('DRGlobal5Ingreso', '0.00000000');
                $producto_4->addChild('DRGlobal1Bimoneda', '0.00000000');
                $producto_4->addChild('DRGlobal2Bimoneda', '0.00000000');
                $producto_4->addChild('DRGlobal3Bimoneda', '0.00000000');
                $producto_4->addChild('DRGlobal4Bimoneda', '0.00000000');
                $producto_4->addChild('DRGlobal5Bimoneda', '0.00000000');
                $producto_4->addChild('PorcentajeDr2', '0.0000');
                $producto_4->addChild('PorcentajeDr3', '0.0000');
                $producto_4->addChild('PorcentajeDr4', '0.0000');
                $producto_4->addChild('PorcentajeDr5', '0.0000');
                $producto_4->addChild('ValPorcentajeDr1', '0.00000000');
                $producto_4->addChild('ValPorcentajeDr2', '0.00000000');
                $producto_4->addChild('ValPorcentajeDr3', '0.00000000');
                $producto_4->addChild('ValPorcentajeDr4', '0.00000000');
                $producto_4->addChild('ValPorcentajeDr5', '0.00000000');
                $producto_4->addChild('ValPorcentajeDr1Ingreso', '0.00000000');
                $producto_4->addChild('ValPorcentajeDr2Ingreso', '0.00000000');
                $producto_4->addChild('ValPorcentajeDr3Ingreso', '0.00000000');
                $producto_4->addChild('ValPorcentajeDr4Ingreso', '0.00000000');
                $producto_4->addChild('ValPorcentajeDr5Ingreso', '0.00000000');
                $producto_4->addChild('ValPorcentajeDr1Bimoneda', '0.00000000');
                $producto_4->addChild('ValPorcentajeDr2Bimoneda', '0.00000000');
                $producto_4->addChild('ValPorcentajeDr3Bimoneda', '0.00000000');
                $producto_4->addChild('ValPorcentajeDr4Bimoneda', '0.00000000');
                $producto_4->addChild('ValPorcentajeDr5Bimoneda', '0.00000000');
                $producto_4->addChild('CostoBimoneda', '0');
                $producto_4->addChild('CupBimoneda', '0');
                $producto_4->addChild('MontoAsignado', '0.00000000');
                $producto_4->addChild('Analisis21');
                $producto_4->addChild('Analisis22');
                $producto_4->addChild('Analisis23');
                $producto_4->addChild('Analisis24');
                $producto_4->addChild('Analisis25');
                $producto_4->addChild('Analisis26');
                $producto_4->addChild('Analisis27');
                $producto_4->addChild('Analisis28');
                $producto_4->addChild('Analisis29', '1000');
                $producto_4->addChild('Analisis30', '10');
                $producto_4->addChild('Receta');
            }
        }

        $sku_5 = json_decode($order->content, true);
        if (is_array($sku_5) && count($sku_5) >= 5) {
            for ($i = 0; $i < 3; $i++) {
                next($sku_5);
            }
            $fivethItem = current($sku_5);
            if ($fivethItem) {

                $producto_5 = $documento->addChild('DETALLE');
                $producto_5->addChild('Empresa', '003');
                $producto_5->addChild('TipoDocto', 'PEDIDO WEBSACO');
                $producto_5->addChild('Correlativo', $order->id);
                $producto_5->addChild('Secuencia', '5');
                $producto_5->addChild('Linea', '5');
                $sku_value5 = $fivethItem['options']['sku'];
                $producto_5->addChild('Producto', $sku_value5);
                $qty_5 = $fivethItem['qty'];
                $producto_5->addChild('Cantidad', $qty_5 . '0.00000000');
                $price_5 = $fivethItem['price'];
                $producto_5->addChild('Precio', $price_5 . '0.00000000');
                $producto_5->addChild('PorcentajeDR', '0.0000');
                $producto_5->addChild('SubTotal', '0.00000000');
                $producto_5->addChild('Impuesto', '0.00000000');
                $producto_5->addChild('Neto', '0.00000000');
                $producto_5->addChild('DRGlobal', '0.00000000');
                $producto_5->addChild('Costo', '0.00000000');
                $producto_5->addChild('Total', '0.00000000');
                $producto_5->addChild('PrecioAjustado', '0.00000000');
                $producto_5->addChild('UnidadIngreso', 'NIU');
                $producto_5->addChild('CantidadIngreso', $qty_5 . '0.00000000');
                $producto_5->addChild('PrecioIngreso', $price_5 . '0.00000000');
                $producto_5->addChild('SubTotalIngreso', '0.00000000');
                $producto_5->addChild('ImpuestoIngreso', '0.00000000');
                $producto_5->addChild('NetoIngreso', '0.00000000');
                $producto_5->addChild('DRGlobalIngreso', '0.00000000');
                $producto_5->addChild('TotalIngreso', '0.00000000');
                $producto_5->addChild('Serie');
                $producto_5->addChild('Lote');
                $fechaVcto = $order->created_at->format('d-m-Y');
                $producto_5->addChild('FechaVcto', $fechaVcto);
                //$producto_5->addChild('FechaVcto',  $order->created_at);
                $producto_5->addChild('TipoDoctoOrigen');
                $producto_5->addChild('CorrelativoOrigen', '0');
                $producto_5->addChild('SecuenciaOrigen', '0');
                $producto_5->addChild('Bodega', $order->selected_store);
                $producto_5->addChild('CentroCosto');
                $producto_5->addChild('Proceso');
                $producto_5->addChild('FactorInventario', '0');
                $producto_5->addChild('FactorInvProyectado', '-1');
                $fechaEntrega = $order->created_at->format('d-m-Y');
                $producto_5->addChild('FechaEntrega', $fechaEntrega);
                //$producto_5->addChild('FechaEntrega',  $order->created_at);
                $producto_5->addChild('CantidadAsignada', '0.00000000');
                $fecha = $order->created_at->format('d-m-Y');
                $producto_5->addChild('Fecha', $fecha);
                //$producto_5->addChild('Fecha',  $order->created_at);
                $producto_5->addChild('Nivel', '0');
                $producto_5->addChild('SecciaProceso', '0');
                $producto_5->addChild('Comentario');
                $producto_5->addChild('Vigente', 'S');
                $producto_5->addChild('FechaModif');
                $producto_5->addChild('AUX_VALOR1');
                $producto_5->addChild('AUX_VALOR2');
                $producto_5->addChild('AUX_VALOR3');
                $producto_5->addChild('AUX_VALOR4');
                $producto_5->addChild('AUX_VALOR5');
                $producto_5->addChild('AUX_VALOR6');
                $producto_5->addChild('AUX_VALOR7');
                $producto_5->addChild('AUX_VALOR8');
                $producto_5->addChild('AUX_VALOR9');
                $producto_5->addChild('AUX_VALOR10');
                $producto_5->addChild('AUX_VALOR11');
                $producto_5->addChild('AUX_VALOR12');
                $producto_5->addChild('AUX_VALOR13');
                $producto_5->addChild('AUX_VALOR14');
                $producto_5->addChild('AUX_VALOR15');
                $producto_5->addChild('AUX_VALOR16');
                $producto_5->addChild('AUX_VALOR17');
                $producto_5->addChild('AUX_VALOR18');
                $producto_5->addChild('AUX_VALOR19');
                $producto_5->addChild('AUX_VALOR20');
                $producto_5->addChild('VALOR1');
                $producto_5->addChild('VALOR2');
                $producto_5->addChild('VALOR3');
                $producto_5->addChild('VALOR4');
                $producto_5->addChild('VALOR5');
                $producto_5->addChild('VALOR6');
                $producto_5->addChild('VALOR7');
                $producto_5->addChild('VALOR8');
                $producto_5->addChild('VALOR9');
                $producto_5->addChild('VALOR10');
                $producto_5->addChild('VALOR11');
                $producto_5->addChild('VALOR12');
                $producto_5->addChild('VALOR13');
                $producto_5->addChild('VALOR14');
                $producto_5->addChild('VALOR15');
                $producto_5->addChild('VALOR16');
                $producto_5->addChild('VALOR17');
                $producto_5->addChild('VALOR18');
                $producto_5->addChild('VALOR19');
                $producto_5->addChild('VALOR20');
                $producto_5->addChild('CUP', '0.00000000');
                $producto_5->addChild('Ubicacion', 'PRINCIPAL');
                $producto_5->addChild('Ubicacion2', 'PRINCIPAL');
                $producto_5->addChild('Cuenta');
                $producto_5->addChild('RFGrupo1');
                $producto_5->addChild('RFGrupo2');
                $producto_5->addChild('RFGrupo3');
                $producto_5->addChild('Estado_Prod');
                $producto_5->addChild('Placa');
                $producto_5->addChild('Transportista');
                $producto_5->addChild('TipoPallet');
                $producto_5->addChild('TipoCaja');
                $producto_5->addChild('FactorImpto', '0.00000000');
                $producto_5->addChild('SeriePrint');
                $producto_5->addChild('PrecioBimoneda', '0.00000000');
                $producto_5->addChild('SubtotalBimoneda', '0.00000000');
                $producto_5->addChild('ImpuestoBimoneda', '0.00000000');
                $producto_5->addChild('NetoBimoneda', '0.00000000');
                $producto_5->addChild('DrGlobalBimoneda', '0.00000000');
                $producto_5->addChild('TotalBimoneda', '0.00000000');
                $producto_5->addChild('PrecioListaP', '0.00000000');
                $producto_5->addChild('Analisis1');
                $producto_5->addChild('Analisis2');
                $producto_5->addChild('Analisis3');
                $producto_5->addChild('Analisis4');
                $producto_5->addChild('Analisis5');
                $producto_5->addChild('Analisis6');
                $producto_5->addChild('Analisis7');
                $producto_5->addChild('Analisis8');
                $producto_5->addChild('Analisis9');
                $producto_5->addChild('Analisis10');
                $producto_5->addChild('Analisis11');
                $producto_5->addChild('Analisis12');
                $producto_5->addChild('Analisis13');
                $producto_5->addChild('Analisis14', '003-0000' . $order->id);
                $producto_5->addChild('Analisis15');
                $producto_5->addChild('Analisis16');
                $producto_5->addChild('Analisis17');
                $producto_5->addChild('Analisis18');
                $producto_5->addChild('Analisis19');
                $producto_5->addChild('Analisis20');
                $producto_5->addChild('UniMedDynamic', '1.00000000');
                $producto_5->addChild('ProdAlias');
                $producto_5->addChild('FechaVigenciaLp');
                $producto_5->addChild('LoteDestino');
                $producto_5->addChild('SerieDestino');
                $producto_5->addChild('DoctoOrigenVal', 'N');
                $producto_5->addChild('DRGlobal1', '0.00000000');
                $producto_5->addChild('DRGlobal2', '0.00000000');
                $producto_5->addChild('DRGlobal3', '0.00000000');
                $producto_5->addChild('DRGlobal4', '0.00000000');
                $producto_5->addChild('DRGlobal5', '0.00000000');
                $producto_5->addChild('DRGlobal1Ingreso', '0.00000000');
                $producto_5->addChild('DRGlobal2Ingreso', '0.00000000');
                $producto_5->addChild('DRGlobal3Ingreso', '0.00000000');
                $producto_5->addChild('DRGlobal4Ingreso', '0.00000000');
                $producto_5->addChild('DRGlobal5Ingreso', '0.00000000');
                $producto_5->addChild('DRGlobal1Bimoneda', '0.00000000');
                $producto_5->addChild('DRGlobal2Bimoneda', '0.00000000');
                $producto_5->addChild('DRGlobal3Bimoneda', '0.00000000');
                $producto_5->addChild('DRGlobal4Bimoneda', '0.00000000');
                $producto_5->addChild('DRGlobal5Bimoneda', '0.00000000');
                $producto_5->addChild('PorcentajeDr2', '0.0000');
                $producto_5->addChild('PorcentajeDr3', '0.0000');
                $producto_5->addChild('PorcentajeDr4', '0.0000');
                $producto_5->addChild('PorcentajeDr5', '0.0000');
                $producto_5->addChild('ValPorcentajeDr1', '0.00000000');
                $producto_5->addChild('ValPorcentajeDr2', '0.00000000');
                $producto_5->addChild('ValPorcentajeDr3', '0.00000000');
                $producto_5->addChild('ValPorcentajeDr4', '0.00000000');
                $producto_5->addChild('ValPorcentajeDr5', '0.00000000');
                $producto_5->addChild('ValPorcentajeDr1Ingreso', '0.00000000');
                $producto_5->addChild('ValPorcentajeDr2Ingreso', '0.00000000');
                $producto_5->addChild('ValPorcentajeDr3Ingreso', '0.00000000');
                $producto_5->addChild('ValPorcentajeDr4Ingreso', '0.00000000');
                $producto_5->addChild('ValPorcentajeDr5Ingreso', '0.00000000');
                $producto_5->addChild('ValPorcentajeDr1Bimoneda', '0.00000000');
                $producto_5->addChild('ValPorcentajeDr2Bimoneda', '0.00000000');
                $producto_5->addChild('ValPorcentajeDr3Bimoneda', '0.00000000');
                $producto_5->addChild('ValPorcentajeDr4Bimoneda', '0.00000000');
                $producto_5->addChild('ValPorcentajeDr5Bimoneda', '0.00000000');
                $producto_5->addChild('CostoBimoneda', '0');
                $producto_5->addChild('CupBimoneda', '0');
                $producto_5->addChild('MontoAsignado', '0.00000000');
                $producto_5->addChild('Analisis21');
                $producto_5->addChild('Analisis22');
                $producto_5->addChild('Analisis23');
                $producto_5->addChild('Analisis24');
                $producto_5->addChild('Analisis25');
                $producto_5->addChild('Analisis26');
                $producto_5->addChild('Analisis27');
                $producto_5->addChild('Analisis28');
                $producto_5->addChild('Analisis29', '1000');
                $producto_5->addChild('Analisis30', '10');
                $producto_5->addChild('Receta');
            }
        }

        $sku_6 = json_decode($order->content, true);
        if (is_array($sku_6) && count($sku_6) >= 6) {
            for ($i = 0; $i < 3; $i++) {
                next($sku_6);
            }
            $sixthItem = current($sku_6);
            if ($sixthItem) {

                $producto_6 = $documento->addChild('DETALLE');
                $producto_6->addChild('Empresa', '003');
                $producto_6->addChild('TipoDocto', 'PEDIDO WEBSACO');
                $producto_6->addChild('Correlativo', $order->id);
                $producto_6->addChild('Secuencia', '6');
                $producto_6->addChild('Linea', '6');
                $sku_value6 = $sixthItem['options']['sku'];
                $producto_6->addChild('Producto', $sku_value6);
                $qty_6 = $sixthItem['qty'];
                $producto_6->addChild('Cantidad', $qty_6 . '0.00000000');
                $price_6 = $sixthItem['price'];
                $producto_6->addChild('Precio', $price_6 . '0.00000000');
                $producto_6->addChild('PorcentajeDR', '0.00000000');
                $producto_6->addChild('SubTotal', '0.00000000');
                $producto_6->addChild('Impuesto', '0.00000000');
                $producto_6->addChild('Neto', '0.00000000');
                $producto_6->addChild('DRGlobal', '0.00000000');
                $producto_6->addChild('Costo', '0.00000000');
                $producto_6->addChild('Total', '0.00000000');
                $producto_6->addChild('PrecioAjustado', '0.00000000');
                $producto_6->addChild('UnidadIngreso', 'NIU');
                $producto_6->addChild('CantidadIngreso', $qty_6 . '0.00000000');
                $producto_6->addChild('PrecioIngreso', $price_6 . '0.00000000');
                $producto_6->addChild('SubTotalIngreso', '0.00000000');
                $producto_6->addChild('ImpuestoIngreso', '0.00000000');
                $producto_6->addChild('NetoIngreso', '0.00000000');
                $producto_6->addChild('DRGlobalIngreso', '0.00000000');
                $producto_6->addChild('TotalIngreso', '0.00000000');
                $producto_6->addChild('Serie');
                $producto_6->addChild('Lote');
                $fechaVcto = $order->created_at->format('d-m-Y');
                $producto_6->addChild('FechaVcto', $fechaVcto);
                //$producto_6->addChild('FechaVcto',  $order->created_at);
                $producto_6->addChild('TipoDoctoOrigen');
                $producto_6->addChild('CorrelativoOrigen', '0');
                $producto_6->addChild('SecuenciaOrigen', '0');
                $producto_6->addChild('Bodega', $order->selected_store);
                $producto_6->addChild('CentroCosto');
                $producto_6->addChild('Proceso');
                $producto_6->addChild('FactorInventario', '0');
                $producto_6->addChild('FactorInvProyectado', '-1');
                $fechaEntrega = $order->created_at->format('d-m-Y');
                $producto_6->addChild('FechaEntrega', $fechaEntrega);
                //$producto_6->addChild('FechaEntrega',  $order->created_at);
                $producto_6->addChild('CantidadAsignada', '0.00000000');
                $fecha = $order->created_at->format('d-m-Y');
                $producto_6->addChild('Fecha', $fecha);
                //$producto_6->addChild('Fecha',  $order->created_at);
                $producto_6->addChild('Nivel', '0');
                $producto_6->addChild('SecciaProceso', '0');
                $producto_6->addChild('Comentario');
                $producto_6->addChild('Vigente', 'S');
                $producto_6->addChild('FechaModif');
                $producto_6->addChild('AUX_VALOR1');
                $producto_6->addChild('AUX_VALOR2');
                $producto_6->addChild('AUX_VALOR3');
                $producto_6->addChild('AUX_VALOR4');
                $producto_6->addChild('AUX_VALOR5');
                $producto_6->addChild('AUX_VALOR6');
                $producto_6->addChild('AUX_VALOR7');
                $producto_6->addChild('AUX_VALOR8');
                $producto_6->addChild('AUX_VALOR9');
                $producto_6->addChild('AUX_VALOR10');
                $producto_6->addChild('AUX_VALOR11');
                $producto_6->addChild('AUX_VALOR12');
                $producto_6->addChild('AUX_VALOR13');
                $producto_6->addChild('AUX_VALOR14');
                $producto_6->addChild('AUX_VALOR15');
                $producto_6->addChild('AUX_VALOR16');
                $producto_6->addChild('AUX_VALOR17');
                $producto_6->addChild('AUX_VALOR18');
                $producto_6->addChild('AUX_VALOR19');
                $producto_6->addChild('AUX_VALOR20');
                $producto_6->addChild('VALOR1');
                $producto_6->addChild('VALOR2');
                $producto_6->addChild('VALOR3');
                $producto_6->addChild('VALOR4');
                $producto_6->addChild('VALOR5');
                $producto_6->addChild('VALOR6');
                $producto_6->addChild('VALOR7');
                $producto_6->addChild('VALOR8');
                $producto_6->addChild('VALOR9');
                $producto_6->addChild('VALOR10');
                $producto_6->addChild('VALOR11');
                $producto_6->addChild('VALOR12');
                $producto_6->addChild('VALOR13');
                $producto_6->addChild('VALOR14');
                $producto_6->addChild('VALOR15');
                $producto_6->addChild('VALOR16');
                $producto_6->addChild('VALOR17');
                $producto_6->addChild('VALOR18');
                $producto_6->addChild('VALOR19');
                $producto_6->addChild('VALOR20');
                $producto_6->addChild('CUP', '0.00000000');
                $producto_6->addChild('Ubicacion', 'PRINCIPAL');
                $producto_6->addChild('Ubicacion2', 'PRINCIPAL');
                $producto_6->addChild('Cuenta');
                $producto_6->addChild('RFGrupo1');
                $producto_6->addChild('RFGrupo2');
                $producto_6->addChild('RFGrupo3');
                $producto_6->addChild('Estado_Prod');
                $producto_6->addChild('Placa');
                $producto_6->addChild('Transportista');
                $producto_6->addChild('TipoPallet');
                $producto_6->addChild('TipoCaja');
                $producto_6->addChild('FactorImpto', '0.00000000');
                $producto_6->addChild('SeriePrint');
                $producto_6->addChild('PrecioBimoneda', '0.00000000');
                $producto_6->addChild('SubtotalBimoneda', '0.00000000');
                $producto_6->addChild('ImpuestoBimoneda', '0.00000000');
                $producto_6->addChild('NetoBimoneda', '0.00000000');
                $producto_6->addChild('DrGlobalBimoneda', '0.00000000');
                $producto_6->addChild('TotalBimoneda', '0.00000000');
                $producto_6->addChild('PrecioListaP', '0.00000000');
                $producto_6->addChild('Analisis1');
                $producto_6->addChild('Analisis2');
                $producto_6->addChild('Analisis3');
                $producto_6->addChild('Analisis4');
                $producto_6->addChild('Analisis5');
                $producto_6->addChild('Analisis6');
                $producto_6->addChild('Analisis7');
                $producto_6->addChild('Analisis8');
                $producto_6->addChild('Analisis9');
                $producto_6->addChild('Analisis10');
                $producto_6->addChild('Analisis11');
                $producto_6->addChild('Analisis12');
                $producto_6->addChild('Analisis13');
                $producto_6->addChild('Analisis14', '003-0000' . $order->id);
                $producto_6->addChild('Analisis15');
                $producto_6->addChild('Analisis16');
                $producto_6->addChild('Analisis17');
                $producto_6->addChild('Analisis18');
                $producto_6->addChild('Analisis19');
                $producto_6->addChild('Analisis20');
                $producto_6->addChild('UniMedDynamic', '1.00000000');
                $producto_6->addChild('ProdAlias');
                $producto_6->addChild('FechaVigenciaLp');
                $producto_6->addChild('LoteDestino');
                $producto_6->addChild('SerieDestino');
                $producto_6->addChild('DoctoOrigenVal', 'N');
                $producto_6->addChild('DRGlobal1', '0.00000000');
                $producto_6->addChild('DRGlobal2', '0.00000000');
                $producto_6->addChild('DRGlobal3', '0.00000000');
                $producto_6->addChild('DRGlobal4', '0.00000000');
                $producto_6->addChild('DRGlobal5', '0.00000000');
                $producto_6->addChild('DRGlobal1Ingreso', '0.00000000');
                $producto_6->addChild('DRGlobal2Ingreso', '0.00000000');
                $producto_6->addChild('DRGlobal3Ingreso', '0.00000000');
                $producto_6->addChild('DRGlobal4Ingreso', '0.00000000');
                $producto_6->addChild('DRGlobal5Ingreso', '0.00000000');
                $producto_6->addChild('DRGlobal1Bimoneda', '0.00000000');
                $producto_6->addChild('DRGlobal2Bimoneda', '0.00000000');
                $producto_6->addChild('DRGlobal3Bimoneda', '0.00000000');
                $producto_6->addChild('DRGlobal4Bimoneda', '0.00000000');
                $producto_6->addChild('DRGlobal5Bimoneda', '0.00000000');
                $producto_6->addChild('PorcentajeDr2', '0.0000');
                $producto_6->addChild('PorcentajeDr3', '0.0000');
                $producto_6->addChild('PorcentajeDr4', '0.0000');
                $producto_6->addChild('PorcentajeDr5', '0.0000');
                $producto_6->addChild('ValPorcentajeDr1', '0.00000000');
                $producto_6->addChild('ValPorcentajeDr2', '0.00000000');
                $producto_6->addChild('ValPorcentajeDr3', '0.00000000');
                $producto_6->addChild('ValPorcentajeDr4', '0.00000000');
                $producto_6->addChild('ValPorcentajeDr5', '0.00000000');
                $producto_6->addChild('ValPorcentajeDr1Ingreso', '0.00000000');
                $producto_6->addChild('ValPorcentajeDr2Ingreso', '0.00000000');
                $producto_6->addChild('ValPorcentajeDr3Ingreso', '0.00000000');
                $producto_6->addChild('ValPorcentajeDr4Ingreso', '0.00000000');
                $producto_6->addChild('ValPorcentajeDr5Ingreso', '0.00000000');
                $producto_6->addChild('ValPorcentajeDr1Bimoneda', '0.00000000');
                $producto_6->addChild('ValPorcentajeDr2Bimoneda', '0.00000000');
                $producto_6->addChild('ValPorcentajeDr3Bimoneda', '0.00000000');
                $producto_6->addChild('ValPorcentajeDr4Bimoneda', '0.00000000');
                $producto_6->addChild('ValPorcentajeDr5Bimoneda', '0.00000000');
                $producto_6->addChild('CostoBimoneda', '0');
                $producto_6->addChild('CupBimoneda', '0');
                $producto_6->addChild('MontoAsignado', '0.00000000');
                $producto_6->addChild('Analisis21');
                $producto_6->addChild('Analisis22');
                $producto_6->addChild('Analisis23');
                $producto_6->addChild('Analisis24');
                $producto_6->addChild('Analisis25');
                $producto_6->addChild('Analisis26');
                $producto_6->addChild('Analisis27');
                $producto_6->addChild('Analisis28');
                $producto_6->addChild('Analisis29', '1000');
                $producto_6->addChild('Analisis30', '10');
                $producto_6->addChild('Receta');
            }
        }

        $sku_7 = json_decode($order->content, true);
        if (is_array($sku_7) && count($sku_7) >= 7) {
            // Avanzar tres veces para llegar al cuarto elemento
            for ($i = 0; $i < 3; $i++) {
                next($sku_7);
            }

            // Obtener el cuarto elemento
            $sevenItem = current($sku_7);

            // Verificar si existe un cuarto elemento
            if ($sevenItem) {


                $producto_7 = $documento->addChild('DETALLE');
                $producto_7->addChild('Empresa', '003');
                $producto_7->addChild('TipoDocto', 'PEDIDO WEBSACO');
                $producto_7->addChild('Correlativo', $order->id);
                $producto_7->addChild('Secuencia', '7');
                $producto_7->addChild('Linea', '7');

                $sku_value7 = $sevenItem['options']['sku'];
                $producto_7->addChild('Producto', $sku_value7);;

                $producto_7->addChild('Cantidad', '1.00000000');
                $producto_7->addChild('Precio', '0.00000000');
                $producto_7->addChild('PorcentajeDR', '0.0000');
                $producto_7->addChild('SubTotal', '0.00000000');
                $producto_7->addChild('Impuesto', '0.00000000');
                $producto_7->addChild('Neto', '0.00000000');
                $producto_7->addChild('DRGlobal', '0.00000000');
                $producto_7->addChild('Costo', '0.00000000');
                $producto_7->addChild('Total', '0.00000000');
                $producto_7->addChild('PrecioAjustado', '0.00000000');
                $producto_7->addChild('UnidadIngreso', 'NIU');
                $producto_7->addChild('CantidadIngreso', '1.00000000');
                $producto_7->addChild('PrecioIngreso', '0.00000000');
                $producto_7->addChild('SubTotalIngreso', '0.00000000');
                $producto_7->addChild('ImpuestoIngreso', '0.00000000');
                $producto_7->addChild('NetoIngreso', '0.00000000');
                $producto_7->addChild('DRGlobalIngreso', '0.00000000');
                $producto_7->addChild('TotalIngreso', '0.00000000');
                $producto_7->addChild('Serie');
                $producto_7->addChild('Lote');
                $fechaVcto = $order->created_at->format('d-m-Y');
                $producto_7->addChild('FechaVcto', $fechaVcto);
                //$producto_7->addChild('FechaVcto',  $order->created_at);
                $producto_7->addChild('TipoDoctoOrigen');
                $producto_7->addChild('CorrelativoOrigen', '0');
                $producto_7->addChild('SecuenciaOrigen', '0');
                $producto_7->addChild('Bodega', $order->selected_store);
                $producto_7->addChild('CentroCosto');
                $producto_7->addChild('Proceso');
                $producto_7->addChild('FactorInventario', '0');
                $producto_7->addChild('FactorInvProyectado', '-1');
                $fechaEntrega = $order->created_at->format('d-m-Y');
                $producto_7->addChild('FechaEntrega', $fechaEntrega);
                //$producto_7->addChild('FechaEntrega',  $order->created_at);
                $producto_7->addChild('CantidadAsignada', '0.00000000');
                $fecha = $order->created_at->format('d-m-Y');
                $producto_7->addChild('Fecha', $fecha);
                //$producto_7->addChild('Fecha',  $order->created_at);
                $producto_7->addChild('Nivel', '0');
                $producto_7->addChild('SecciaProceso', '0');
                $producto_7->addChild('Comentario');
                $producto_7->addChild('Vigente', 'S');
                $producto_7->addChild('FechaModif');
                $producto_7->addChild('AUX_VALOR1');
                $producto_7->addChild('AUX_VALOR2');
                $producto_7->addChild('AUX_VALOR3');
                $producto_7->addChild('AUX_VALOR4');
                $producto_7->addChild('AUX_VALOR5');
                $producto_7->addChild('AUX_VALOR6');
                $producto_7->addChild('AUX_VALOR7');
                $producto_7->addChild('AUX_VALOR8');
                $producto_7->addChild('AUX_VALOR9');
                $producto_7->addChild('AUX_VALOR10');
                $producto_7->addChild('AUX_VALOR11');
                $producto_7->addChild('AUX_VALOR12');
                $producto_7->addChild('AUX_VALOR13');
                $producto_7->addChild('AUX_VALOR14');
                $producto_7->addChild('AUX_VALOR15');
                $producto_7->addChild('AUX_VALOR16');
                $producto_7->addChild('AUX_VALOR17');
                $producto_7->addChild('AUX_VALOR18');
                $producto_7->addChild('AUX_VALOR19');
                $producto_7->addChild('AUX_VALOR20');
                $producto_7->addChild('VALOR1');
                $producto_7->addChild('VALOR2');
                $producto_7->addChild('VALOR3');
                $producto_7->addChild('VALOR4');
                $producto_7->addChild('VALOR5');
                $producto_7->addChild('VALOR6');
                $producto_7->addChild('VALOR7');
                $producto_7->addChild('VALOR8');
                $producto_7->addChild('VALOR9');
                $producto_7->addChild('VALOR10');
                $producto_7->addChild('VALOR11');
                $producto_7->addChild('VALOR12');
                $producto_7->addChild('VALOR13');
                $producto_7->addChild('VALOR14');
                $producto_7->addChild('VALOR15');
                $producto_7->addChild('VALOR16');
                $producto_7->addChild('VALOR17');
                $producto_7->addChild('VALOR18');
                $producto_7->addChild('VALOR19');
                $producto_7->addChild('VALOR20');
                $producto_7->addChild('CUP', '0.00000000');
                $producto_7->addChild('Ubicacion', 'PRINCIPAL');
                $producto_7->addChild('Ubicacion2', 'PRINCIPAL');
                $producto_7->addChild('Cuenta');
                $producto_7->addChild('RFGrupo1');
                $producto_7->addChild('RFGrupo2');
                $producto_7->addChild('RFGrupo3');
                $producto_7->addChild('Estado_Prod');
                $producto_7->addChild('Placa');
                $producto_7->addChild('Transportista');
                $producto_7->addChild('TipoPallet');
                $producto_7->addChild('TipoCaja');
                $producto_7->addChild('FactorImpto', '0.00000000');
                $producto_7->addChild('SeriePrint');
                $producto_7->addChild('PrecioBimoneda', '0.00000000');
                $producto_7->addChild('SubtotalBimoneda', '0.00000000');
                $producto_7->addChild('ImpuestoBimoneda', '0.00000000');
                $producto_7->addChild('NetoBimoneda', '0.00000000');
                $producto_7->addChild('DrGlobalBimoneda', '0.00000000');
                $producto_7->addChild('TotalBimoneda', '0.00000000');
                $producto_7->addChild('PrecioListaP', '0.00000000');
                $producto_7->addChild('Analisis1');
                $producto_7->addChild('Analisis2');
                $producto_7->addChild('Analisis3');
                $producto_7->addChild('Analisis4');
                $producto_7->addChild('Analisis5');
                $producto_7->addChild('Analisis6');
                $producto_7->addChild('Analisis7');
                $producto_7->addChild('Analisis8');
                $producto_7->addChild('Analisis9');
                $producto_7->addChild('Analisis10');
                $producto_7->addChild('Analisis11');
                $producto_7->addChild('Analisis12');
                $producto_7->addChild('Analisis13');
                $producto_7->addChild('Analisis14', '003-0000' . $order->id);
                $producto_7->addChild('Analisis15');
                $producto_7->addChild('Analisis16');
                $producto_7->addChild('Analisis17');
                $producto_7->addChild('Analisis18');
                $producto_7->addChild('Analisis19');
                $producto_7->addChild('Analisis20');
                $producto_7->addChild('UniMedDynamic', '1.00000000');
                $producto_7->addChild('ProdAlias');
                $producto_7->addChild('FechaVigenciaLp');
                $producto_7->addChild('LoteDestino');
                $producto_7->addChild('SerieDestino');
                $producto_7->addChild('DoctoOrigenVal', 'N');
                $producto_7->addChild('DRGlobal1', '0.00000000');
                $producto_7->addChild('DRGlobal2', '0.00000000');
                $producto_7->addChild('DRGlobal3', '0.00000000');
                $producto_7->addChild('DRGlobal4', '0.00000000');
                $producto_7->addChild('DRGlobal5', '0.00000000');
                $producto_7->addChild('DRGlobal1Ingreso', '0.00000000');
                $producto_7->addChild('DRGlobal2Ingreso', '0.00000000');
                $producto_7->addChild('DRGlobal3Ingreso', '0.00000000');
                $producto_7->addChild('DRGlobal4Ingreso', '0.00000000');
                $producto_7->addChild('DRGlobal5Ingreso', '0.00000000');
                $producto_7->addChild('DRGlobal1Bimoneda', '0.00000000');
                $producto_7->addChild('DRGlobal2Bimoneda', '0.00000000');
                $producto_7->addChild('DRGlobal3Bimoneda', '0.00000000');
                $producto_7->addChild('DRGlobal4Bimoneda', '0.00000000');
                $producto_7->addChild('DRGlobal5Bimoneda', '0.00000000');
                $producto_7->addChild('PorcentajeDr2', '0.0000');
                $producto_7->addChild('PorcentajeDr3', '0.0000');
                $producto_7->addChild('PorcentajeDr4', '0.0000');
                $producto_7->addChild('PorcentajeDr5', '0.0000');
                $producto_7->addChild('ValPorcentajeDr1', '0.00000000');
                $producto_7->addChild('ValPorcentajeDr2', '0.00000000');
                $producto_7->addChild('ValPorcentajeDr3', '0.00000000');
                $producto_7->addChild('ValPorcentajeDr4', '0.00000000');
                $producto_7->addChild('ValPorcentajeDr5', '0.00000000');
                $producto_7->addChild('ValPorcentajeDr1Ingreso', '0.00000000');
                $producto_7->addChild('ValPorcentajeDr2Ingreso', '0.00000000');
                $producto_7->addChild('ValPorcentajeDr3Ingreso', '0.00000000');
                $producto_7->addChild('ValPorcentajeDr4Ingreso', '0.00000000');
                $producto_7->addChild('ValPorcentajeDr5Ingreso', '0.00000000');
                $producto_7->addChild('ValPorcentajeDr1Bimoneda', '0.00000000');
                $producto_7->addChild('ValPorcentajeDr2Bimoneda', '0.00000000');
                $producto_7->addChild('ValPorcentajeDr3Bimoneda', '0.00000000');
                $producto_7->addChild('ValPorcentajeDr4Bimoneda', '0.00000000');
                $producto_7->addChild('ValPorcentajeDr5Bimoneda', '0.00000000');
                $producto_7->addChild('CostoBimoneda', '0');
                $producto_7->addChild('CupBimoneda', '0');
                $producto_7->addChild('MontoAsignado', '0.00000000');
                $producto_7->addChild('Analisis21');
                $producto_7->addChild('Analisis22');
                $producto_7->addChild('Analisis23');
                $producto_7->addChild('Analisis24');
                $producto_7->addChild('Analisis25');
                $producto_7->addChild('Analisis26');
                $producto_7->addChild('Analisis27');
                $producto_7->addChild('Analisis28');
                $producto_7->addChild('Analisis29', '1000');
                $producto_7->addChild('Analisis30', '10');
                $producto_7->addChild('Receta');
            }
        }

        $sku_8 = json_decode($order->content, true);
        if (is_array($sku_8) && count($sku_8) >= 8) {
            for ($i = 0; $i < 3; $i++) {
                next($sku_8);
            }
            $niethItem = current($sku_8);
            if ($niethItem) {

                $producto_8 = $documento->addChild('DETALLE');
                $producto_8->addChild('Empresa', '003');
                $producto_8->addChild('TipoDocto', 'PEDIDO WEBSACO');
                $producto_8->addChild('Correlativo', $order->id);
                $producto_8->addChild('Secuencia', '8');
                $producto_8->addChild('Linea', '8');
                $sku_value8 = $niethItem['options']['sku'];
                $producto_8->addChild('Producto', $sku_value8);
                $qty_8 = $niethItem['qty'];
                $producto_8->addChild('Cantidad', $qty_8 . '0.00000000');
                $price_8 = $niethItem['price'];
                $producto_8->addChild('Precio', $price_8 . '0.00000000');
                $producto_8->addChild('PorcentajeDR', '0.00000000');
                $producto_8->addChild('SubTotal', '0.00000000');
                $producto_8->addChild('Impuesto', '0.00000000');
                $producto_8->addChild('Neto', '0.00000000');
                $producto_8->addChild('DRGlobal', '0.00000000');
                $producto_8->addChild('Costo', '0.00000000');
                $producto_8->addChild('Total', '0.00000000');
                $producto_8->addChild('PrecioAjustado', '0.00000000');
                $producto_8->addChild('UnidadIngreso', 'NIU');
                $producto_8->addChild('CantidadIngreso',  $qty_8 . '0.00000000');
                $producto_8->addChild('PrecioIngreso',  $price_8 . '0.00000000');
                $producto_8->addChild('SubTotalIngreso', '0.00000000');
                $producto_8->addChild('ImpuestoIngreso', '0.00000000');
                $producto_8->addChild('NetoIngreso', '0.00000000');
                $producto_8->addChild('DRGlobalIngreso', '0.00000000');
                $producto_8->addChild('TotalIngreso', '0.00000000');
                $producto_8->addChild('Serie');
                $producto_8->addChild('Lote');
                $fechaVcto = $order->created_at->format('d-m-Y');
                $producto_8->addChild('FechaVcto', $fechaVcto);
                //$producto_8->addChild('FechaVcto',  $order->created_at);
                $producto_8->addChild('TipoDoctoOrigen');
                $producto_8->addChild('CorrelativoOrigen', '0');
                $producto_8->addChild('SecuenciaOrigen', '0');
                $producto_8->addChild('Bodega', $order->selected_store);
                $producto_8->addChild('CentroCosto');
                $producto_8->addChild('Proceso');
                $producto_8->addChild('FactorInventario', '0');
                $producto_8->addChild('FactorInvProyectado', '-1');
                $fechaEntrega = $order->created_at->format('d-m-Y');
                $producto_8->addChild('FechaEntrega', $fechaEntrega);
                //$producto_8->addChild('FechaEntrega',  $order->created_at);
                $producto_8->addChild('CantidadAsignada', '0.00000000');
                $fecha = $order->created_at->format('d-m-Y');
                $producto_8->addChild('Fecha', $fecha);
                //$producto_8->addChild('Fecha',  $order->created_at);
                $producto_8->addChild('Nivel', '0');
                $producto_8->addChild('SecciaProceso', '0');
                $producto_8->addChild('Comentario');
                $producto_8->addChild('Vigente', 'S');
                $producto_8->addChild('FechaModif');
                $producto_8->addChild('AUX_VALOR1');
                $producto_8->addChild('AUX_VALOR2');
                $producto_8->addChild('AUX_VALOR3');
                $producto_8->addChild('AUX_VALOR4');
                $producto_8->addChild('AUX_VALOR5');
                $producto_8->addChild('AUX_VALOR6');
                $producto_8->addChild('AUX_VALOR7');
                $producto_8->addChild('AUX_VALOR8');
                $producto_8->addChild('AUX_VALOR9');
                $producto_8->addChild('AUX_VALOR10');
                $producto_8->addChild('AUX_VALOR11');
                $producto_8->addChild('AUX_VALOR12');
                $producto_8->addChild('AUX_VALOR13');
                $producto_8->addChild('AUX_VALOR14');
                $producto_8->addChild('AUX_VALOR15');
                $producto_8->addChild('AUX_VALOR16');
                $producto_8->addChild('AUX_VALOR17');
                $producto_8->addChild('AUX_VALOR18');
                $producto_8->addChild('AUX_VALOR19');
                $producto_8->addChild('AUX_VALOR20');
                $producto_8->addChild('VALOR1');
                $producto_8->addChild('VALOR2');
                $producto_8->addChild('VALOR3');
                $producto_8->addChild('VALOR4');
                $producto_8->addChild('VALOR5');
                $producto_8->addChild('VALOR6');
                $producto_8->addChild('VALOR7');
                $producto_8->addChild('VALOR8');
                $producto_8->addChild('VALOR9');
                $producto_8->addChild('VALOR10');
                $producto_8->addChild('VALOR11');
                $producto_8->addChild('VALOR12');
                $producto_8->addChild('VALOR13');
                $producto_8->addChild('VALOR14');
                $producto_8->addChild('VALOR15');
                $producto_8->addChild('VALOR16');
                $producto_8->addChild('VALOR17');
                $producto_8->addChild('VALOR18');
                $producto_8->addChild('VALOR19');
                $producto_8->addChild('VALOR20');
                $producto_8->addChild('CUP', '0.00000000');
                $producto_8->addChild('Ubicacion', 'PRINCIPAL');
                $producto_8->addChild('Ubicacion2', 'PRINCIPAL');
                $producto_8->addChild('Cuenta');
                $producto_8->addChild('RFGrupo1');
                $producto_8->addChild('RFGrupo2');
                $producto_8->addChild('RFGrupo3');
                $producto_8->addChild('Estado_Prod');
                $producto_8->addChild('Placa');
                $producto_8->addChild('Transportista');
                $producto_8->addChild('TipoPallet');
                $producto_8->addChild('TipoCaja');
                $producto_8->addChild('FactorImpto', '0.00000000');
                $producto_8->addChild('SeriePrint');
                $producto_8->addChild('PrecioBimoneda', '0.00000000');
                $producto_8->addChild('SubtotalBimoneda', '0.00000000');
                $producto_8->addChild('ImpuestoBimoneda', '0.00000000');
                $producto_8->addChild('NetoBimoneda', '0.00000000');
                $producto_8->addChild('DrGlobalBimoneda', '0.00000000');
                $producto_8->addChild('TotalBimoneda', '0.00000000');
                $producto_8->addChild('PrecioListaP', '0.00000000');
                $producto_8->addChild('Analisis1');
                $producto_8->addChild('Analisis2');
                $producto_8->addChild('Analisis3');
                $producto_8->addChild('Analisis4');
                $producto_8->addChild('Analisis5');
                $producto_8->addChild('Analisis6');
                $producto_8->addChild('Analisis7');
                $producto_8->addChild('Analisis8');
                $producto_8->addChild('Analisis9');
                $producto_8->addChild('Analisis10');
                $producto_8->addChild('Analisis11');
                $producto_8->addChild('Analisis12');
                $producto_8->addChild('Analisis13');
                $producto_8->addChild('Analisis14', '003-0000' . $order->id);
                $producto_8->addChild('Analisis15');
                $producto_8->addChild('Analisis16');
                $producto_8->addChild('Analisis17');
                $producto_8->addChild('Analisis18');
                $producto_8->addChild('Analisis19');
                $producto_8->addChild('Analisis20');
                $producto_8->addChild('UniMedDynamic', '1.00000000');
                $producto_8->addChild('ProdAlias');
                $producto_8->addChild('FechaVigenciaLp');
                $producto_8->addChild('LoteDestino');
                $producto_8->addChild('SerieDestino');
                $producto_8->addChild('DoctoOrigenVal', 'N');
                $producto_8->addChild('DRGlobal1', '0.00000000');
                $producto_8->addChild('DRGlobal2', '0.00000000');
                $producto_8->addChild('DRGlobal3', '0.00000000');
                $producto_8->addChild('DRGlobal4', '0.00000000');
                $producto_8->addChild('DRGlobal5', '0.00000000');
                $producto_8->addChild('DRGlobal1Ingreso', '0.00000000');
                $producto_8->addChild('DRGlobal2Ingreso', '0.00000000');
                $producto_8->addChild('DRGlobal3Ingreso', '0.00000000');
                $producto_8->addChild('DRGlobal4Ingreso', '0.00000000');
                $producto_8->addChild('DRGlobal5Ingreso', '0.00000000');
                $producto_8->addChild('DRGlobal1Bimoneda', '0.00000000');
                $producto_8->addChild('DRGlobal2Bimoneda', '0.00000000');
                $producto_8->addChild('DRGlobal3Bimoneda', '0.00000000');
                $producto_8->addChild('DRGlobal4Bimoneda', '0.00000000');
                $producto_8->addChild('DRGlobal5Bimoneda', '0.00000000');
                $producto_8->addChild('PorcentajeDr2', '0.0000');
                $producto_8->addChild('PorcentajeDr3', '0.0000');
                $producto_8->addChild('PorcentajeDr4', '0.0000');
                $producto_8->addChild('PorcentajeDr5', '0.0000');
                $producto_8->addChild('ValPorcentajeDr1', '0.00000000');
                $producto_8->addChild('ValPorcentajeDr2', '0.00000000');
                $producto_8->addChild('ValPorcentajeDr3', '0.00000000');
                $producto_8->addChild('ValPorcentajeDr4', '0.00000000');
                $producto_8->addChild('ValPorcentajeDr5', '0.00000000');
                $producto_8->addChild('ValPorcentajeDr1Ingreso', '0.00000000');
                $producto_8->addChild('ValPorcentajeDr2Ingreso', '0.00000000');
                $producto_8->addChild('ValPorcentajeDr3Ingreso', '0.00000000');
                $producto_8->addChild('ValPorcentajeDr4Ingreso', '0.00000000');
                $producto_8->addChild('ValPorcentajeDr5Ingreso', '0.00000000');
                $producto_8->addChild('ValPorcentajeDr1Bimoneda', '0.00000000');
                $producto_8->addChild('ValPorcentajeDr2Bimoneda', '0.00000000');
                $producto_8->addChild('ValPorcentajeDr3Bimoneda', '0.00000000');
                $producto_8->addChild('ValPorcentajeDr4Bimoneda', '0.00000000');
                $producto_8->addChild('ValPorcentajeDr5Bimoneda', '0.00000000');
                $producto_8->addChild('CostoBimoneda', '0');
                $producto_8->addChild('CupBimoneda', '0');
                $producto_8->addChild('MontoAsignado', '0.00000000');
                $producto_8->addChild('Analisis21');
                $producto_8->addChild('Analisis22');
                $producto_8->addChild('Analisis23');
                $producto_8->addChild('Analisis24');
                $producto_8->addChild('Analisis25');
                $producto_8->addChild('Analisis26');
                $producto_8->addChild('Analisis27');
                $producto_8->addChild('Analisis28');
                $producto_8->addChild('Analisis29', '1000');
                $producto_8->addChild('Analisis30', '10');
                $producto_8->addChild('Receta');
            }
        }

        $sku_9 = json_decode($order->content, true);
        if (is_array($sku_9) && count($sku_9) >= 9) {
            for ($i = 0; $i < 3; $i++) {
                next($sku_9);
            }
            $nineItem = current($sku_9);
            if ($nineItem) {

                $producto_9 = $documento->addChild('DETALLE');
                $producto_9->addChild('Empresa', '003');
                $producto_9->addChild('TipoDocto', 'PEDIDO WEBSACO');
                $producto_9->addChild('Correlativo', $order->id);
                $producto_9->addChild('Secuencia', '9');
                $producto_9->addChild('Linea', '9');
                $sku_value9 = $nineItem['options']['sku'];
                $producto_9->addChild('Producto', $sku_value9);
                $qty_9 = $nineItem['qty'];
                $producto_9->addChild('Cantidad', $qty_9 . '0.00000000');
                $price_9 = $nineItem['price'];
                $producto_9->addChild('Producto', $price_9 . '0.00000000');
                $producto_9->addChild('PorcentajeDR', '0.00000000');
                $producto_9->addChild('SubTotal', '0.00000000');
                $producto_9->addChild('Impuesto', '0.00000000');
                $producto_9->addChild('Neto', '0.00000000');
                $producto_9->addChild('DRGlobal', '0.00000000');
                $producto_9->addChild('Costo', '0.00000000');
                $producto_9->addChild('Total', '0.00000000');
                $producto_9->addChild('PrecioAjustado', '0.00000000');
                $producto_9->addChild('UnidadIngreso', 'NIU');
                $producto_9->addChild('CantidadIngreso', $qty_9 . '0.00000000');
                $producto_9->addChild('PrecioIngreso', $price_9 . '0.00000000');
                $producto_9->addChild('SubTotalIngreso', '0.00000000');
                $producto_9->addChild('ImpuestoIngreso', '0.00000000');
                $producto_9->addChild('NetoIngreso', '0.00000000');
                $producto_9->addChild('DRGlobalIngreso', '0.00000000');
                $producto_9->addChild('TotalIngreso', '0.00000000');
                $producto_9->addChild('Serie');
                $producto_9->addChild('Lote');
                $fechaVcto = $order->created_at->format('d-m-Y');
                $producto_9->addChild('FechaVcto', $fechaVcto);
                //$producto_9->addChild('FechaVcto',  $order->created_at);
                $producto_9->addChild('TipoDoctoOrigen');
                $producto_9->addChild('CorrelativoOrigen', '0');
                $producto_9->addChild('SecuenciaOrigen', '0');
                $producto_9->addChild('Bodega', $order->selected_store);
                $producto_9->addChild('CentroCosto');
                $producto_9->addChild('Proceso');
                $producto_9->addChild('FactorInventario', '0');
                $producto_9->addChild('FactorInvProyectado', '-1');
                $fechaEntrega = $order->created_at->format('d-m-Y');
                $producto_9->addChild('FechaEntrega', $fechaEntrega);
                //$producto_9->addChild('FechaEntrega',  $order->created_at);
                $producto_9->addChild('CantidadAsignada', '0.00000000');
                $fecha = $order->created_at->format('d-m-Y');
                $producto_9->addChild('Fecha', $fecha);
                //$producto_9->addChild('Fecha',  $order->created_at);
                $producto_9->addChild('Nivel', '0');
                $producto_9->addChild('SecciaProceso', '0');
                $producto_9->addChild('Comentario');
                $producto_9->addChild('Vigente', 'S');
                $producto_9->addChild('FechaModif');
                $producto_9->addChild('AUX_VALOR1');
                $producto_9->addChild('AUX_VALOR2');
                $producto_9->addChild('AUX_VALOR3');
                $producto_9->addChild('AUX_VALOR4');
                $producto_9->addChild('AUX_VALOR5');
                $producto_9->addChild('AUX_VALOR6');
                $producto_9->addChild('AUX_VALOR7');
                $producto_9->addChild('AUX_VALOR8');
                $producto_9->addChild('AUX_VALOR9');
                $producto_9->addChild('AUX_VALOR10');
                $producto_9->addChild('AUX_VALOR11');
                $producto_9->addChild('AUX_VALOR12');
                $producto_9->addChild('AUX_VALOR13');
                $producto_9->addChild('AUX_VALOR14');
                $producto_9->addChild('AUX_VALOR15');
                $producto_9->addChild('AUX_VALOR16');
                $producto_9->addChild('AUX_VALOR17');
                $producto_9->addChild('AUX_VALOR18');
                $producto_9->addChild('AUX_VALOR19');
                $producto_9->addChild('AUX_VALOR20');
                $producto_9->addChild('VALOR1');
                $producto_9->addChild('VALOR2');
                $producto_9->addChild('VALOR3');
                $producto_9->addChild('VALOR4');
                $producto_9->addChild('VALOR5');
                $producto_9->addChild('VALOR6');
                $producto_9->addChild('VALOR7');
                $producto_9->addChild('VALOR8');
                $producto_9->addChild('VALOR9');
                $producto_9->addChild('VALOR10');
                $producto_9->addChild('VALOR11');
                $producto_9->addChild('VALOR12');
                $producto_9->addChild('VALOR13');
                $producto_9->addChild('VALOR14');
                $producto_9->addChild('VALOR15');
                $producto_9->addChild('VALOR16');
                $producto_9->addChild('VALOR17');
                $producto_9->addChild('VALOR18');
                $producto_9->addChild('VALOR19');
                $producto_9->addChild('VALOR20');
                $producto_9->addChild('CUP', '0.00000000');
                $producto_9->addChild('Ubicacion', 'PRINCIPAL');
                $producto_9->addChild('Ubicacion2', 'PRINCIPAL');
                $producto_9->addChild('Cuenta');
                $producto_9->addChild('RFGrupo1');
                $producto_9->addChild('RFGrupo2');
                $producto_9->addChild('RFGrupo3');
                $producto_9->addChild('Estado_Prod');
                $producto_9->addChild('Placa');
                $producto_9->addChild('Transportista');
                $producto_9->addChild('TipoPallet');
                $producto_9->addChild('TipoCaja');
                $producto_9->addChild('FactorImpto', '0.00000000');
                $producto_9->addChild('SeriePrint');
                $producto_9->addChild('PrecioBimoneda', '0.00000000');
                $producto_9->addChild('SubtotalBimoneda', '0.00000000');
                $producto_9->addChild('ImpuestoBimoneda', '0.00000000');
                $producto_9->addChild('NetoBimoneda', '0.00000000');
                $producto_9->addChild('DrGlobalBimoneda', '0.00000000');
                $producto_9->addChild('TotalBimoneda', '0.00000000');
                $producto_9->addChild('PrecioListaP', '0.00000000');
                $producto_9->addChild('Analisis1');
                $producto_9->addChild('Analisis2');
                $producto_9->addChild('Analisis3');
                $producto_9->addChild('Analisis4');
                $producto_9->addChild('Analisis5');
                $producto_9->addChild('Analisis6');
                $producto_9->addChild('Analisis7');
                $producto_9->addChild('Analisis8');
                $producto_9->addChild('Analisis9');
                $producto_9->addChild('Analisis10');
                $producto_9->addChild('Analisis11');
                $producto_9->addChild('Analisis12');
                $producto_9->addChild('Analisis13');
                $producto_9->addChild('Analisis14', '003-0000' . $order->id);
                $producto_9->addChild('Analisis15');
                $producto_9->addChild('Analisis16');
                $producto_9->addChild('Analisis17');
                $producto_9->addChild('Analisis18');
                $producto_9->addChild('Analisis19');
                $producto_9->addChild('Analisis20');
                $producto_9->addChild('UniMedDynamic', '1.00000000');
                $producto_9->addChild('ProdAlias');
                $producto_9->addChild('FechaVigenciaLp');
                $producto_9->addChild('LoteDestino');
                $producto_9->addChild('SerieDestino');
                $producto_9->addChild('DoctoOrigenVal', 'N');
                $producto_9->addChild('DRGlobal1', '0.00000000');
                $producto_9->addChild('DRGlobal2', '0.00000000');
                $producto_9->addChild('DRGlobal3', '0.00000000');
                $producto_9->addChild('DRGlobal4', '0.00000000');
                $producto_9->addChild('DRGlobal5', '0.00000000');
                $producto_9->addChild('DRGlobal1Ingreso', '0.00000000');
                $producto_9->addChild('DRGlobal2Ingreso', '0.00000000');
                $producto_9->addChild('DRGlobal3Ingreso', '0.00000000');
                $producto_9->addChild('DRGlobal4Ingreso', '0.00000000');
                $producto_9->addChild('DRGlobal5Ingreso', '0.00000000');
                $producto_9->addChild('DRGlobal1Bimoneda', '0.00000000');
                $producto_9->addChild('DRGlobal2Bimoneda', '0.00000000');
                $producto_9->addChild('DRGlobal3Bimoneda', '0.00000000');
                $producto_9->addChild('DRGlobal4Bimoneda', '0.00000000');
                $producto_9->addChild('DRGlobal5Bimoneda', '0.00000000');
                $producto_9->addChild('PorcentajeDr2', '0.0000');
                $producto_9->addChild('PorcentajeDr3', '0.0000');
                $producto_9->addChild('PorcentajeDr4', '0.0000');
                $producto_9->addChild('PorcentajeDr5', '0.0000');
                $producto_9->addChild('ValPorcentajeDr1', '0.00000000');
                $producto_9->addChild('ValPorcentajeDr2', '0.00000000');
                $producto_9->addChild('ValPorcentajeDr3', '0.00000000');
                $producto_9->addChild('ValPorcentajeDr4', '0.00000000');
                $producto_9->addChild('ValPorcentajeDr5', '0.00000000');
                $producto_9->addChild('ValPorcentajeDr1Ingreso', '0.00000000');
                $producto_9->addChild('ValPorcentajeDr2Ingreso', '0.00000000');
                $producto_9->addChild('ValPorcentajeDr3Ingreso', '0.00000000');
                $producto_9->addChild('ValPorcentajeDr4Ingreso', '0.00000000');
                $producto_9->addChild('ValPorcentajeDr5Ingreso', '0.00000000');
                $producto_9->addChild('ValPorcentajeDr1Bimoneda', '0.00000000');
                $producto_9->addChild('ValPorcentajeDr2Bimoneda', '0.00000000');
                $producto_9->addChild('ValPorcentajeDr3Bimoneda', '0.00000000');
                $producto_9->addChild('ValPorcentajeDr4Bimoneda', '0.00000000');
                $producto_9->addChild('ValPorcentajeDr5Bimoneda', '0.00000000');
                $producto_9->addChild('CostoBimoneda', '0');
                $producto_9->addChild('CupBimoneda', '0');
                $producto_9->addChild('MontoAsignado', '0.00000000');
                $producto_9->addChild('Analisis21');
                $producto_9->addChild('Analisis22');
                $producto_9->addChild('Analisis23');
                $producto_9->addChild('Analisis24');
                $producto_9->addChild('Analisis25');
                $producto_9->addChild('Analisis26');
                $producto_9->addChild('Analisis27');
                $producto_9->addChild('Analisis28');
                $producto_9->addChild('Analisis29', '1000');
                $producto_9->addChild('Analisis30', '10');
                $producto_9->addChild('Receta');
            }
        }

        $sku_10 = json_decode($order->content, true);
        if (is_array($sku_10) && count($sku_10) >= 10) {
            for ($i = 0; $i < 3; $i++) {
                next($sku_10);
            }
            $temItem = current($sku_10);
            if ($temItem) {

                $producto_10 = $documento->addChild('DETALLE');
                $producto_10->addChild('Empresa', '003');
                $producto_10->addChild('TipoDocto', 'PEDIDO WEBSACO');
                $producto_10->addChild('Correlativo', $order->id);
                $producto_10->addChild('Secuencia', '10');
                $producto_10->addChild('Linea', '10');
                $sku_value10 = $temItem['options']['sku'];
                $producto_10->addChild('Producto', $sku_value10);
                $qty_10 = $temItem['qty'];
                $producto_10->addChild('Cantidad', $qty_10 . '0.00000000');
                $price_10 = $temItem['price'];
                $producto_10->addChild('Precio', $price_10 . '0.00000000');
                $producto_10->addChild('PorcentajeDR', '0.00000000');
                $producto_10->addChild('SubTotal', '0.00000000');
                $producto_10->addChild('Impuesto', '0.00000000');
                $producto_10->addChild('Neto', '0.00000000');
                $producto_10->addChild('DRGlobal', '0.00000000');
                $producto_10->addChild('Costo', '0.00000000');
                $producto_10->addChild('Total', '0.00000000');
                $producto_10->addChild('PrecioAjustado', '0.00000000');
                $producto_10->addChild('UnidadIngreso', 'NIU');
                $producto_10->addChild('CantidadIngreso', $qty_10 . '0.00000000');
                $producto_10->addChild('PrecioIngreso', $price_10 . '0.00000000');
                $producto_10->addChild('SubTotalIngreso', '0.00000000');
                $producto_10->addChild('ImpuestoIngreso', '0.00000000');
                $producto_10->addChild('NetoIngreso', '0.00000000');
                $producto_10->addChild('DRGlobalIngreso', '0.00000000');
                $producto_10->addChild('TotalIngreso', '0.00000000');
                $producto_10->addChild('Serie');
                $producto_10->addChild('Lote');
                $fechaVcto = $order->created_at->format('d-m-Y');
                $producto_10->addChild('FechaVcto', $fechaVcto);
                //$producto_10->addChild('FechaVcto',  $order->created_at);
                $producto_10->addChild('TipoDoctoOrigen');
                $producto_10->addChild('CorrelativoOrigen', '0');
                $producto_10->addChild('SecuenciaOrigen', '0');
                $producto_10->addChild('Bodega', $order->selected_store);
                $producto_10->addChild('CentroCosto');
                $producto_10->addChild('Proceso');
                $producto_10->addChild('FactorInventario', '0');
                $producto_10->addChild('FactorInvProyectado', '-1');
                $fechaEntrega = $order->created_at->format('d-m-Y');
                $producto_10->addChild('FechaEntrega', $fechaEntrega);
                //$producto_10->addChild('FechaEntrega',  $order->created_at);
                $producto_10->addChild('CantidadAsignada', '0.00000000');
                $fecha = $order->created_at->format('d-m-Y');
                $producto_10->addChild('Fecha', $fecha);
                //$producto_10->addChild('Fecha',  $order->created_at);
                $producto_10->addChild('Nivel', '0');
                $producto_10->addChild('SecciaProceso', '0');
                $producto_10->addChild('Comentario');
                $producto_10->addChild('Vigente', 'S');
                $producto_10->addChild('FechaModif');
                $producto_10->addChild('AUX_VALOR1');
                $producto_10->addChild('AUX_VALOR2');
                $producto_10->addChild('AUX_VALOR3');
                $producto_10->addChild('AUX_VALOR4');
                $producto_10->addChild('AUX_VALOR5');
                $producto_10->addChild('AUX_VALOR6');
                $producto_10->addChild('AUX_VALOR7');
                $producto_10->addChild('AUX_VALOR8');
                $producto_10->addChild('AUX_VALOR9');
                $producto_10->addChild('AUX_VALOR10');
                $producto_10->addChild('AUX_VALOR11');
                $producto_10->addChild('AUX_VALOR12');
                $producto_10->addChild('AUX_VALOR13');
                $producto_10->addChild('AUX_VALOR14');
                $producto_10->addChild('AUX_VALOR15');
                $producto_10->addChild('AUX_VALOR16');
                $producto_10->addChild('AUX_VALOR17');
                $producto_10->addChild('AUX_VALOR18');
                $producto_10->addChild('AUX_VALOR19');
                $producto_10->addChild('AUX_VALOR20');
                $producto_10->addChild('VALOR1');
                $producto_10->addChild('VALOR2');
                $producto_10->addChild('VALOR3');
                $producto_10->addChild('VALOR4');
                $producto_10->addChild('VALOR5');
                $producto_10->addChild('VALOR6');
                $producto_10->addChild('VALOR7');
                $producto_10->addChild('VALOR8');
                $producto_10->addChild('VALOR9');
                $producto_10->addChild('VALOR10');
                $producto_10->addChild('VALOR11');
                $producto_10->addChild('VALOR12');
                $producto_10->addChild('VALOR13');
                $producto_10->addChild('VALOR14');
                $producto_10->addChild('VALOR15');
                $producto_10->addChild('VALOR16');
                $producto_10->addChild('VALOR17');
                $producto_10->addChild('VALOR18');
                $producto_10->addChild('VALOR19');
                $producto_10->addChild('VALOR20');
                $producto_10->addChild('CUP', '0.00000000');
                $producto_10->addChild('Ubicacion', 'PRINCIPAL');
                $producto_10->addChild('Ubicacion2', 'PRINCIPAL');
                $producto_10->addChild('Cuenta');
                $producto_10->addChild('RFGrupo1');
                $producto_10->addChild('RFGrupo2');
                $producto_10->addChild('RFGrupo3');
                $producto_10->addChild('Estado_Prod');
                $producto_10->addChild('Placa');
                $producto_10->addChild('Transportista');
                $producto_10->addChild('TipoPallet');
                $producto_10->addChild('TipoCaja');
                $producto_10->addChild('FactorImpto', '0.00000000');
                $producto_10->addChild('SeriePrint');
                $producto_10->addChild('PrecioBimoneda', '0.00000000');
                $producto_10->addChild('SubtotalBimoneda', '0.00000000');
                $producto_10->addChild('ImpuestoBimoneda', '0.00000000');
                $producto_10->addChild('NetoBimoneda', '0.00000000');
                $producto_10->addChild('DrGlobalBimoneda', '0.00000000');
                $producto_10->addChild('TotalBimoneda', '0.00000000');
                $producto_10->addChild('PrecioListaP', '0.00000000');
                $producto_10->addChild('Analisis1');
                $producto_10->addChild('Analisis2');
                $producto_10->addChild('Analisis3');
                $producto_10->addChild('Analisis4');
                $producto_10->addChild('Analisis5');
                $producto_10->addChild('Analisis6');
                $producto_10->addChild('Analisis7');
                $producto_10->addChild('Analisis8');
                $producto_10->addChild('Analisis9');
                $producto_10->addChild('Analisis10');
                $producto_10->addChild('Analisis11');
                $producto_10->addChild('Analisis12');
                $producto_10->addChild('Analisis13');
                $producto_10->addChild('Analisis14', '003-0000' . $order->id);
                $producto_10->addChild('Analisis15');
                $producto_10->addChild('Analisis16');
                $producto_10->addChild('Analisis17');
                $producto_10->addChild('Analisis18');
                $producto_10->addChild('Analisis19');
                $producto_10->addChild('Analisis20');
                $producto_10->addChild('UniMedDynamic', '1.00000000');
                $producto_10->addChild('ProdAlias');
                $producto_10->addChild('FechaVigenciaLp');
                $producto_10->addChild('LoteDestino');
                $producto_10->addChild('SerieDestino');
                $producto_10->addChild('DoctoOrigenVal', 'N');
                $producto_10->addChild('DRGlobal1', '0.00000000');
                $producto_10->addChild('DRGlobal2', '0.00000000');
                $producto_10->addChild('DRGlobal3', '0.00000000');
                $producto_10->addChild('DRGlobal4', '0.00000000');
                $producto_10->addChild('DRGlobal5', '0.00000000');
                $producto_10->addChild('DRGlobal1Ingreso', '0.00000000');
                $producto_10->addChild('DRGlobal2Ingreso', '0.00000000');
                $producto_10->addChild('DRGlobal3Ingreso', '0.00000000');
                $producto_10->addChild('DRGlobal4Ingreso', '0.00000000');
                $producto_10->addChild('DRGlobal5Ingreso', '0.00000000');
                $producto_10->addChild('DRGlobal1Bimoneda', '0.00000000');
                $producto_10->addChild('DRGlobal2Bimoneda', '0.00000000');
                $producto_10->addChild('DRGlobal3Bimoneda', '0.00000000');
                $producto_10->addChild('DRGlobal4Bimoneda', '0.00000000');
                $producto_10->addChild('DRGlobal5Bimoneda', '0.00000000');
                $producto_10->addChild('PorcentajeDr2', '0.0000');
                $producto_10->addChild('PorcentajeDr3', '0.0000');
                $producto_10->addChild('PorcentajeDr4', '0.0000');
                $producto_10->addChild('PorcentajeDr5', '0.0000');
                $producto_10->addChild('ValPorcentajeDr1', '0.00000000');
                $producto_10->addChild('ValPorcentajeDr2', '0.00000000');
                $producto_10->addChild('ValPorcentajeDr3', '0.00000000');
                $producto_10->addChild('ValPorcentajeDr4', '0.00000000');
                $producto_10->addChild('ValPorcentajeDr5', '0.00000000');
                $producto_10->addChild('ValPorcentajeDr1Ingreso', '0.00000000');
                $producto_10->addChild('ValPorcentajeDr2Ingreso', '0.00000000');
                $producto_10->addChild('ValPorcentajeDr3Ingreso', '0.00000000');
                $producto_10->addChild('ValPorcentajeDr4Ingreso', '0.00000000');
                $producto_10->addChild('ValPorcentajeDr5Ingreso', '0.00000000');
                $producto_10->addChild('ValPorcentajeDr1Bimoneda', '0.00000000');
                $producto_10->addChild('ValPorcentajeDr2Bimoneda', '0.00000000');
                $producto_10->addChild('ValPorcentajeDr3Bimoneda', '0.00000000');
                $producto_10->addChild('ValPorcentajeDr4Bimoneda', '0.00000000');
                $producto_10->addChild('ValPorcentajeDr5Bimoneda', '0.00000000');
                $producto_10->addChild('CostoBimoneda', '0');
                $producto_10->addChild('CupBimoneda', '0');
                $producto_10->addChild('MontoAsignado', '0.00000000');
                $producto_10->addChild('Analisis21');
                $producto_10->addChild('Analisis22');
                $producto_10->addChild('Analisis23');
                $producto_10->addChild('Analisis24');
                $producto_10->addChild('Analisis25');
                $producto_10->addChild('Analisis26');
                $producto_10->addChild('Analisis27');
                $producto_10->addChild('Analisis28');
                $producto_10->addChild('Analisis29', '1000');
                $producto_10->addChild('Analisis30', '10');
                $producto_10->addChild('Receta');
            }
        }

        $pagos = $documento->addChild('PAGOS');
        $pagos->addChild('Empresa', '003');
        $pagos->addChild('TipoDocto', 'PEDIDO WEBSACO');
        $pagos->addChild('Correlativo', $order->id);
        $pagos->addChild('Linea', '1');
        // Agregar el elemento 'CodigoPago' solo si $order->condicion_pago es 1 o 2
        if ($order->condicion_pago == 1 || $order->condicion_pago == 2) {
            $codigoPagoValue = ($order->condicion_pago == 1) ? 'OPENPAY-T.CRE' : 'OPENPAY-T.DEB';
            $pagos->addChild('CodigoPago', $codigoPagoValue);
        }

        $pagos->addChild('TipoPago', 'S');
        $fechaVcto = $order->created_at->format('d-m-Y');
        $pagos->addChild('FechaVcto', $fechaVcto);
        $pagos->addChild('Monto', '0.00000000');
        $pagos->addChild('MontoIngreso', '0.00000000');
        $pagos->addChild('TipoDoctoPago', 'PEDIDO WEBSACO');
        $pagos->addChild('NroDoctoPago', $order->id);
        $pagos->addChild('Cuenta', '012010201001');
        $pagos->addChild('MontoBimoneda', '0.00000000');
        $pagos->addChild('AjusteBimoneda', '0');
        $pagos->addChild('Entidad');
        $pagos->addChild('NumAutoriza');
        $pagos->addChild('CuentaPago');
        $pagos->addChild('FechaVctoTarjeta');
        $pagos->addChild('PropietarioTarjeta');
        $fechaVctoDocto = $order->created_at->format('d-m-Y');
        $pagos->addChild('FechaVctoDocto', $fechaVctoDocto);
        $pagos->addChild('RutComprador');
        $pagos->addChild('RutGirador');
        $pagos->addChild('MonedaPago', 'S/');
        $pagos->addChild('MontoPago', '0.00000000');
        $pagos->addChild('ParidadPago', '1.00000000');
        $pagos->addChild('ValorGenerico');
        $pagos->addChild('LineaTipo', '0');

        $valores = $documento->addChild('VALORES');
        $valores->addChild('Empresa', '003');
        $valores->addChild('TipoDocto', 'PEDIDO WEBSACO');
        $valores->addChild('Correlativo', $order->id);
        $valores->addChild('Nombre', 'Afecto');
        $valores->addChild('Orden', '2');
        $valores->addChild('Factor', '0');
        $valores->addChild('Monto', '0.00000000');
        $valores->addChild('MontoIngreso', '0.00000000');
        $valores->addChild('Ajuste', '0.00000000');
        $valores->addChild('AjusteIngreso', '0.00000000');
        $valores->addChild('Texto');
        $valores->addChild('Porcentaje', '0.0000');
        $valores->addChild('MontoBimoneda', '0.00000000');
        $valores->addChild('AjusteBimoneda', '0');

        $valores = $documento->addChild('VALORES');
        $valores->addChild('Empresa', '003');
        $valores->addChild('TipoDocto', 'PEDIDO WEBSACO');
        $valores->addChild('Correlativo', $order->id);
        $valores->addChild('Nombre', 'IGV');
        $valores->addChild('Orden', '3');
        $valores->addChild('Factor', '0');
        $valores->addChild('Monto', '46.45000000');
        $valores->addChild('MontoIngreso', '46.45000000');
        $valores->addChild('Ajuste', '0.00000000');
        $valores->addChild('AjusteIngreso', '0.00000000');
        $valores->addChild('Texto');
        $valores->addChild('Porcentaje', '0.0000');
        $valores->addChild('MontoBimoneda', '12.90000000');
        $valores->addChild('AjusteBimoneda', '0');

        $valores = $documento->addChild('VALORES');
        $valores->addChild('Empresa', '003');
        $valores->addChild('TipoDocto', 'PEDIDO WEBSACO');
        $valores->addChild('Correlativo', $order->id);
        $valores->addChild('Nombre', 'Neto');
        $valores->addChild('Orden', '1');
        $valores->addChild('Factor', '0');
        $valores->addChild('Monto', $order->total);
        $valores->addChild('MontoIngreso', $order->total);
        $valores->addChild('Ajuste', '0.00000000');
        $valores->addChild('AjusteIngreso', '0.00000000');
        $valores->addChild('Texto');
        $valores->addChild('Porcentaje', '0.0000');
        $valores->addChild('MontoBimoneda', '84.59000000');
        $valores->addChild('AjusteBimoneda', '0');
        return $xmlData->asXML();
    }

    private function generarDatosXml(Order $order)
    {
        // Crear instancia de SimpleXMLElement para generar el XML
        $xmlData = new SimpleXMLElement('<CTACTELIST></CTACTELIST>');
        // Agregar elementos LOGIN y CLIENTE-PROVEEDOR al XML
        $login = $xmlData->addChild('LOGIN');
        $login->addChild('usuario', 'flexline');
        $login->addChild('password', 'flexline');

        $clienteProveedor = $xmlData->addChild('CLIENTE-PROVEEDOR');
        $clienteProveedor->addChild('Empresa', '003');
        $clienteProveedor->addChild('TipoCtaCte', 'CLIENTE');
        $clienteProveedor->addChild('CtaCte', $order->dni ? $order->dni : $order->ruc);
        $clienteProveedor->addChild('CodLegal', $order->dni ? $order->dni : $order->ruc);
        $clienteProveedor->addChild('RazonSocial', $order->name ? $order->name : $order->razon_social);
        $clienteProveedor->addChild('Sigla');
        $clienteProveedor->addChild('Giro');
        $clienteProveedor->addChild('Tipo', 'RETAIL');
        $clienteProveedor->addChild('Grupo', 'RET-WEBSACO');
        $clienteProveedor->addChild('Ejecutivo', 'oficina');
        if ($order->condicion_pago == 1 || $order->condicion_pago == 2) {
            $clienteProveedor->addChild('CondPago', ($order->condicion_pago == 1) ? 'OPENPAY-T.CRE' : 'OPENPAY-T.DEB');
        };
        $clienteProveedor->addChild('Vigencia', 'S');
        $clienteProveedor->addChild('ListaPrecio', 'LP-LIM-WEBSACO');
        $clienteProveedor->addChild('Zona');
        $clienteProveedor->addChild('Direccion');
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
        $clienteProveedor->addChild('Comentario1', 'VENTA WEB OPENPAY');
        $clienteProveedor->addChild('Comentario2');
        $clienteProveedor->addChild('Comentario3');
        $clienteProveedor->addChild('Comentario4');
        $clienteProveedor->addChild('Texto1');
        $clienteProveedor->addChild('Texto2');
        $clienteProveedor->addChild('Texto3');
        $clienteProveedor->addChild('FechaModif');
        $clienteProveedor->addChild('UsuarioModif', 'ROOT');
        $clienteProveedor->addChild('TIPOCONTRIBUYENTE');
        $clienteProveedor->addChild('PorcDr1', '0');
        $clienteProveedor->addChild('PorcDr2', '0');
        $clienteProveedor->addChild('PorcDr3', '0');
        $clienteProveedor->addChild('PorcDr4', '0');
        $clienteProveedor->addChild('Analisisctacte1', $order->tipo_identidad);
        $clienteProveedor->addChild('Analisisctacte2', '0' . $order->tipo_doc);
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

<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use SoapClient;

class InyectaDocumentoController extends Controller
{
    // Método para mostrar el formulario de edición
    public function editarXmlForm()
    {
        return view('livewire.admin.editar-xml');
    }

    // Método para enviar el XML al servicio SOAP
public function enviarXmlASoap(Request $request)
{

    $request->validate([
        'Numero' => 'required',
        'Cliente' => 'required',
        'Fecha' => 'required',
        // Agrega más reglas de validación según sea necesario
    ]);

    // URL del servicio WSDL
    $wsdlUrl = 'https://ws-erp.manager.cl/Flexline/Saco/Ws%20Documento/Documento.asmx?WSDL';

    // Obtener datos del formulario
    $Numero = $request->input('Numero');
    $Cliente = $request->input('Cliente');
    $Fecha = $request->input('Fecha');

    // Agregar más campos según sea necesario

    // Construir el XML con los datos del formulario
    $xmlContent = '

    <DOCUMENTO_LIST>
	    <LOGIN>
		<usuario>flexline</usuario>
		<password>flexline</password>
	    </LOGIN>
	    <DOCUMENTO>
	    	<ENCABEZADO>
			<Empresa>003</Empresa>
			<TipoDocto>PEDIDO BITEL</TipoDocto>
			<Correlativo>'.$Numero.'</Correlativo>
			<CtaCte>20543254798</CtaCte>
			<Numero>T-'.$Numero.'</Numero>
			<Fecha>'. $Fecha .'</Fecha>
			<Proveedor></Proveedor>
			<Cliente>'. $Cliente .'</Cliente>
			<Bodega>01-CH-BITEL</Bodega>
			<Bodega2></Bodega2>
			<Local></Local>
			<Comprador></Comprador>
			<Vendedor>OFICINA</Vendedor>
			<CentroCosto></CentroCosto>
			<FechaVcto>'. $Fecha .'</FechaVcto>
			<ListaPrecio>LP_BITEL_2021/08/31</ListaPrecio>
			<Analisis></Analisis>
			<Zona></Zona>
			<TipoCta></TipoCta>
			<Moneda>S/</Moneda>
			<Paridad>1.00000000</Paridad>
			<RefTipoDocto></RefTipoDocto>
			<RefCorrelativo>0</RefCorrelativo>
			<ReferenciaExterna>0</ReferenciaExterna>
			<Neto>258.07000000</Neto>
			<SubTotal>304.52000000</SubTotal>
			<Total>304.52000000</Total>
			<NetoIngreso>258.07000000</NetoIngreso>
			<SubTotalIngreso>304.52000000</SubTotalIngreso>
			<TotalIngreso>304.52000000</TotalIngreso>
			<Centraliza></Centraliza>
			<Valoriza></Valoriza>
			<Costeo></Costeo>
			<Aprobacion>S</Aprobacion>
			<TipoComprobante></TipoComprobante>
			<NroComprobante>0</NroComprobante>
			<FechaComprobante>01-01-1900</FechaComprobante>
			<PeriodoLibro>202310</PeriodoLibro>
			<FactorMonto>0</FactorMonto>
			<FactorMontoProyectado>0</FactorMontoProyectado>
			<TipoCtaCte>CLIENTE</TipoCtaCte>
			<IdCtaCte>20543254798</IdCtaCte>
			<Glosa></Glosa>
			<Comentario1></Comentario1>
			<Comentario2></Comentario2>
			<Comentario3></Comentario3>
			<Comentario4></Comentario4>
			<Estado></Estado>
			<FechaEstado>01-01-1900</FechaEstado>
			<NroMensaje>0</NroMensaje>
			<Vigencia>S</Vigencia>
			<Emitido>N</Emitido>
			<PorcentajeAsignado>0.0</PorcentajeAsignado>
			<Adjuntos>N</Adjuntos>
			<Direccion></Direccion>
			<Ciudad></Ciudad>
			<Comuna></Comuna>
			<EstadoDir></EstadoDir>
			<Pais></Pais>
			<Contacto></Contacto>
			<FechaModif>'.$Fecha.' 12:00:00</FechaModif>
			<FechaUModif>'.$Fecha.' 12:00:00</FechaUModif>
			<UsuarioModif>ROOT</UsuarioModif>
			<ComisionCantidad>0</ComisionCantidad>
			<ComisionLPrecio>0</ComisionLPrecio>
			<ComisionMonto>0</ComisionMonto>
			<Hora>13:12:06</Hora>
			<Caja></Caja>
			<Pago>0.00000000</Pago>
			<Donacion>0.00000000</Donacion>
			<IdApertura>0</IdApertura>
			<DrCondPago>0.00000000</DrCondPago>
			<PorcDr1>0.00000000</PorcDr1>
			<PorcDr2>0.00000000</PorcDr2>
			<PorcDr3>0.00000000</PorcDr3>
			<PorcDr4>0.00000000</PorcDr4>
			<Multipagina></Multipagina>
			<NetoBimoneda>71.69000000</NetoBimoneda>
			<SubtotalBimoneda>84.59000000</SubtotalBimoneda>
			<TotalBimoneda>84.59000000</TotalBimoneda>
			<ParidadBimoneda>3.60000000</ParidadBimoneda>
			<ParidadAdic>0.00000000</ParidadAdic>
			<AnalisisE1></AnalisisE1>
			<AnalisisE2></AnalisisE2>
			<AnalisisE3></AnalisisE3>
			<AnalisisE4></AnalisisE4>
			<UsuarioAprueba></UsuarioAprueba>
			<ANALISISE5></ANALISISE5>
			<ANALISISE6></ANALISISE6>
			<ANALISISE7></ANALISISE7>
			<ANALISISE8>admin</ANALISISE8>
			<ANALISISE9>987456321</ANALISISE9>
			<ANALISISE10>admin@saco.com</ANALISISE10>
			<ANALISISE11></ANALISISE11>
			<ANALISISE12></ANALISISE12>
			<ANALISISE13></ANALISISE13>
			<ANALISISE14></ANALISISE14>
			<ANALISISE15></ANALISISE15>
			<ANALISISE16></ANALISISE16>
			<ANALISISE17></ANALISISE17>
			<ANALISISE18></ANALISISE18>
			<ANALISISE19></ANALISISE19>
			<ANALISISE20></ANALISISE20>
			<IdFolioSucursal></IdFolioSucursal>
			<SUPER_DR></SUPER_DR>
			<usuariocierre></usuariocierre>
			<FechaCierre></FechaCierre>
			<AnalisisE21></AnalisisE21>
			<AnalisisE22>15031</AnalisisE22>
			<AnalisisE23></AnalisisE23>
			<AnalisisE24></AnalisisE24>
			<AnalisisE25></AnalisisE25>
			<AnalisisE26></AnalisisE26>
			<AnalisisE27></AnalisisE27>
			<AnalisisE28></AnalisisE28>
			<AnalisisE29></AnalisisE29>
			<AnalisisE30>0101</AnalisisE30>
			<FechaAprueba>01-01-1900</FechaAprueba>
	    	</ENCABEZADO>
	    	<DETALLE>
			<Empresa>003</Empresa>
			<TipoDocto>PEDIDO BITEL</TipoDocto>
			<Correlativo>'.$Numero.'</Correlativo>
			<Secuencia>1</Secuencia>
			<Linea>1</Linea>
			<Producto>XIA-45780</Producto>
			<Cantidad>1.00000000</Cantidad>
			<Precio>290.02000000</Precio>
			<PorcentajeDR>0.0000</PorcentajeDR>
			<SubTotal>290.02000000</SubTotal>
			<Impuesto>44.24000000</Impuesto>
			<Neto>245.78000000</Neto>
			<DRGlobal>0.00000000</DRGlobal>
			<Costo>234.20000000</Costo>
			<Total>245.78000000</Total>
			<PrecioAjustado>245.78000000</PrecioAjustado>
			<UnidadIngreso>NIU</UnidadIngreso>
			<CantidadIngreso>1.00000000</CantidadIngreso>
			<PrecioIngreso>290.02000000</PrecioIngreso>
			<SubTotalIngreso>290.02000000</SubTotalIngreso>
			<ImpuestoIngreso>44.24000000</ImpuestoIngreso>
			<NetoIngreso>245.78000000</NetoIngreso>
			<DRGlobalIngreso>0.00000000</DRGlobalIngreso>
			<TotalIngreso>245.78000000</TotalIngreso>
			<Serie></Serie>
			<Lote></Lote>
			<FechaVcto>01-01-1900</FechaVcto>
			<TipoDoctoOrigen></TipoDoctoOrigen>
			<CorrelativoOrigen>0</CorrelativoOrigen>
			<SecuenciaOrigen>0</SecuenciaOrigen>
			<Bodega>01-CH-BITEL</Bodega>
			<CentroCosto></CentroCosto>
			<Proceso></Proceso>
			<FactorInventario>0</FactorInventario>
			<FactorInvProyectado>-1</FactorInvProyectado>
			<FechaEntrega>'.$Fecha.'</FechaEntrega>
			<CantidadAsignada>0.00000000</CantidadAsignada>
			<Fecha>'.$Fecha.'</Fecha>
			<Nivel>0</Nivel>
			<SecciaProceso>0</SecciaProceso>
			<Comentario></Comentario>
			<Vigente>S</Vigente>
			<FechaModif></FechaModif>
			<AUX_VALOR1></AUX_VALOR1>
			<AUX_VALOR2></AUX_VALOR2>
			<AUX_VALOR3></AUX_VALOR3>
			<AUX_VALOR4></AUX_VALOR4>
			<AUX_VALOR5></AUX_VALOR5>
			<AUX_VALOR6></AUX_VALOR6>
			<AUX_VALOR7></AUX_VALOR7>
			<AUX_VALOR8></AUX_VALOR8>
			<AUX_VALOR9></AUX_VALOR9>
			<AUX_VALOR10></AUX_VALOR10>
			<AUX_VALOR11></AUX_VALOR11>
			<AUX_VALOR12></AUX_VALOR12>
			<AUX_VALOR13></AUX_VALOR13>
			<AUX_VALOR14></AUX_VALOR14>
			<AUX_VALOR15></AUX_VALOR15>
			<AUX_VALOR16></AUX_VALOR16>
			<AUX_VALOR17></AUX_VALOR17>
			<AUX_VALOR18></AUX_VALOR18>
			<AUX_VALOR19></AUX_VALOR19>
			<AUX_VALOR20></AUX_VALOR20>
			<VALOR1></VALOR1>
			<VALOR2></VALOR2>
			<VALOR3></VALOR3>
			<VALOR4></VALOR4>
			<VALOR5></VALOR5>
			<VALOR6></VALOR6>
			<VALOR7></VALOR7>
			<VALOR8></VALOR8>
			<VALOR9></VALOR9>
			<VALOR10></VALOR10>
			<VALOR11></VALOR11>
			<VALOR12></VALOR12>
			<VALOR13></VALOR13>
			<VALOR14></VALOR14>
			<VALOR15></VALOR15>
			<VALOR16></VALOR16>
			<VALOR17></VALOR17>
			<VALOR18></VALOR18>
			<VALOR19></VALOR19>
			<VALOR20></VALOR20>
			<CUP>234.20000000</CUP>
			<Ubicacion>PRINCIPAL</Ubicacion>
			<Ubicacion2>PRINCIPAL</Ubicacion2>
			<Cuenta></Cuenta>
			<RFGrupo1></RFGrupo1>
			<RFGrupo2></RFGrupo2>
			<RFGrupo3></RFGrupo3>
			<Estado_Prod></Estado_Prod>
			<Placa></Placa>
			<Transportista></Transportista>
			<TipoPallet></TipoPallet>
			<TipoCaja></TipoCaja>
			<FactorImpto>0.84745763</FactorImpto>
			<SeriePrint></SeriePrint>
			<PrecioBimoneda>80.56111100</PrecioBimoneda>
			<SubtotalBimoneda>80.56000000</SubtotalBimoneda>
			<ImpuestoBimoneda>12.29000000</ImpuestoBimoneda>
			<NetoBimoneda>68.27000000</NetoBimoneda>
			<DrGlobalBimoneda>0.00000000</DrGlobalBimoneda>
			<TotalBimoneda>68.27000000</TotalBimoneda>
			<PrecioListaP>290.02000000</PrecioListaP>
			<Analisis1></Analisis1>
			<Analisis2></Analisis2>
			<Analisis3></Analisis3>
			<Analisis4></Analisis4>
			<Analisis5></Analisis5>
			<Analisis6></Analisis6>
			<Analisis7></Analisis7>
			<Analisis8></Analisis8>
			<Analisis9></Analisis9>
			<Analisis10></Analisis10>
			<Analisis11></Analisis11>
			<Analisis12></Analisis12>
			<Analisis13></Analisis13>
			<Analisis14>15031</Analisis14>
			<Analisis15></Analisis15>
			<Analisis16></Analisis16>
			<Analisis17></Analisis17>
			<Analisis18></Analisis18>
			<Analisis19></Analisis19>
			<Analisis20></Analisis20>
			<UniMedDynamic>1.00000000</UniMedDynamic>
			<ProdAlias>056351C</ProdAlias>
			<FechaVigenciaLp>'.$Fecha.'</FechaVigenciaLp>
			<LoteDestino></LoteDestino>
			<SerieDestino></SerieDestino>
			<DoctoOrigenVal>N</DoctoOrigenVal>
			<DRGlobal1>0.00000000</DRGlobal1>
			<DRGlobal2>0.00000000</DRGlobal2>
			<DRGlobal3>0.00000000</DRGlobal3>
			<DRGlobal4>0.00000000</DRGlobal4>
			<DRGlobal5>0.00000000</DRGlobal5>
			<DRGlobal1Ingreso>0.00000000</DRGlobal1Ingreso>
			<DRGlobal2Ingreso>0.00000000</DRGlobal2Ingreso>
			<DRGlobal3Ingreso>0.00000000</DRGlobal3Ingreso>
			<DRGlobal4Ingreso>0.00000000</DRGlobal4Ingreso>
			<DRGlobal5Ingreso>0.00000000</DRGlobal5Ingreso>
			<DRGlobal1Bimoneda>0.00000000</DRGlobal1Bimoneda>
			<DRGlobal2Bimoneda>0.00000000</DRGlobal2Bimoneda>
			<DRGlobal3Bimoneda>0.00000000</DRGlobal3Bimoneda>
			<DRGlobal4Bimoneda>0.00000000</DRGlobal4Bimoneda>
			<DRGlobal5Bimoneda>0.00000000</DRGlobal5Bimoneda>
			<PorcentajeDr2>0.0000</PorcentajeDr2>
			<PorcentajeDr3>0.0000</PorcentajeDr3>
			<PorcentajeDr4>0.0000</PorcentajeDr4>
			<PorcentajeDr5>0.0000</PorcentajeDr5>
			<ValPorcentajeDr1>0.00000000</ValPorcentajeDr1>
			<ValPorcentajeDr2>0.00000000</ValPorcentajeDr2>
			<ValPorcentajeDr3>0.00000000</ValPorcentajeDr3>
			<ValPorcentajeDr4>0.00000000</ValPorcentajeDr4>
			<ValPorcentajeDr5>0.00000000</ValPorcentajeDr5>
			<ValPorcentajeDr1Ingreso>0.00000000</ValPorcentajeDr1Ingreso>
			<ValPorcentajeDr2Ingreso>0.00000000</ValPorcentajeDr2Ingreso>
			<ValPorcentajeDr3Ingreso>0.00000000</ValPorcentajeDr3Ingreso>
			<ValPorcentajeDr4Ingreso>0.00000000</ValPorcentajeDr4Ingreso>
			<ValPorcentajeDr5Ingreso>0.00000000</ValPorcentajeDr5Ingreso>
			<ValPorcentajeDr1Bimoneda>0.00000000</ValPorcentajeDr1Bimoneda>
			<ValPorcentajeDr2Bimoneda>0.00000000</ValPorcentajeDr2Bimoneda>
			<ValPorcentajeDr3Bimoneda>0.00000000</ValPorcentajeDr3Bimoneda>
			<ValPorcentajeDr4Bimoneda>0.00000000</ValPorcentajeDr4Bimoneda>
			<ValPorcentajeDr5Bimoneda>0.00000000</ValPorcentajeDr5Bimoneda>
			<CostoBimoneda>0</CostoBimoneda>
			<CupBimoneda>0</CupBimoneda>
			<MontoAsignado>0.00000000</MontoAsignado>
			<Analisis21></Analisis21>
			<Analisis22></Analisis22>
			<Analisis23></Analisis23>
			<Analisis24></Analisis24>
			<Analisis25></Analisis25>
			<Analisis26></Analisis26>
			<Analisis27></Analisis27>
			<Analisis28></Analisis28>
			<Analisis29>1000</Analisis29>
			<Analisis30>10</Analisis30>
			<Receta></Receta>
	    	</DETALLE>
	    	<DETALLE>
			<Empresa>003</Empresa>
			<TipoDocto>PEDIDO BITEL</TipoDocto>
			<Correlativo>15030</Correlativo>
			<Secuencia>2</Secuencia>
			<Linea>2</Linea>
			<Producto>ZONA 1</Producto>
			<Cantidad>1.00000000</Cantidad>
			<Precio>14.50000000</Precio>
			<PorcentajeDR>0.0000</PorcentajeDR>
			<SubTotal>14.50000000</SubTotal>
			<Impuesto>2.21000000</Impuesto>
			<Neto>12.29000000</Neto>
			<DRGlobal>0.00000000</DRGlobal>
			<Costo>0.00000000</Costo>
			<Total>12.29000000</Total>
			<PrecioAjustado>12.29000000</PrecioAjustado>
			<UnidadIngreso>NIU</UnidadIngreso>
			<CantidadIngreso>1.00000000</CantidadIngreso>
			<PrecioIngreso>14.50000000</PrecioIngreso>
			<SubTotalIngreso>14.50000000</SubTotalIngreso>
			<ImpuestoIngreso>2.21000000</ImpuestoIngreso>
			<NetoIngreso>12.29000000</NetoIngreso>
			<DRGlobalIngreso>0.00000000</DRGlobalIngreso>
			<TotalIngreso>12.29000000</TotalIngreso>
			<Serie></Serie>
			<Lote></Lote>
			<FechaVcto>01-01-1900</FechaVcto>
			<TipoDoctoOrigen></TipoDoctoOrigen>
			<CorrelativoOrigen>0</CorrelativoOrigen>
			<SecuenciaOrigen>0</SecuenciaOrigen>
			<Bodega>01-CH-BITEL</Bodega>
			<CentroCosto></CentroCosto>
			<Proceso></Proceso>
			<FactorInventario>0</FactorInventario>
			<FactorInvProyectado>-1</FactorInvProyectado>
			<FechaEntrega>'.$Fecha.'</FechaEntrega>
			<CantidadAsignada>0.00000000</CantidadAsignada>
			<Fecha>'.$Fecha.'</Fecha>
			<Nivel>0</Nivel>
			<SecciaProceso>0</SecciaProceso>
			<Comentario></Comentario>
			<Vigente>S</Vigente>
			<FechaModif></FechaModif>
			<AUX_VALOR1></AUX_VALOR1>
			<AUX_VALOR2></AUX_VALOR2>
			<AUX_VALOR3></AUX_VALOR3>
			<AUX_VALOR4></AUX_VALOR4>
			<AUX_VALOR5></AUX_VALOR5>
			<AUX_VALOR6></AUX_VALOR6>
			<AUX_VALOR7></AUX_VALOR7>
			<AUX_VALOR8></AUX_VALOR8>
			<AUX_VALOR9></AUX_VALOR9>
			<AUX_VALOR10></AUX_VALOR10>
			<AUX_VALOR11></AUX_VALOR11>
			<AUX_VALOR12></AUX_VALOR12>
			<AUX_VALOR13></AUX_VALOR13>
			<AUX_VALOR14></AUX_VALOR14>
			<AUX_VALOR15></AUX_VALOR15>
			<AUX_VALOR16></AUX_VALOR16>
			<AUX_VALOR17></AUX_VALOR17>
			<AUX_VALOR18></AUX_VALOR18>
			<AUX_VALOR19></AUX_VALOR19>
			<AUX_VALOR20></AUX_VALOR20>
			<VALOR1></VALOR1>
			<VALOR2></VALOR2>
			<VALOR3></VALOR3>
			<VALOR4></VALOR4>
			<VALOR5></VALOR5>
			<VALOR6></VALOR6>
			<VALOR7></VALOR7>
			<VALOR8></VALOR8>
			<VALOR9></VALOR9>
			<VALOR10></VALOR10>
			<VALOR11></VALOR11>
			<VALOR12></VALOR12>
			<VALOR13></VALOR13>
			<VALOR14></VALOR14>
			<VALOR15></VALOR15>
			<VALOR16></VALOR16>
			<VALOR17></VALOR17>
			<VALOR18></VALOR18>
			<VALOR19></VALOR19>
			<VALOR20></VALOR20>
			<CUP>0.00000000</CUP>
			<Ubicacion>PRINCIPAL</Ubicacion>
			<Ubicacion2>PRINCIPAL</Ubicacion2>
			<Cuenta></Cuenta>
			<RFGrupo1></RFGrupo1>
			<RFGrupo2></RFGrupo2>
			<RFGrupo3></RFGrupo3>
			<Estado_Prod></Estado_Prod>
			<Placa></Placa>
			<Transportista></Transportista>
			<TipoPallet></TipoPallet>
			<TipoCaja></TipoCaja>
			<FactorImpto>0.84745763</FactorImpto>
			<SeriePrint></SeriePrint>
			<PrecioBimoneda>4.02777800</PrecioBimoneda>
			<SubtotalBimoneda>4.03000000</SubtotalBimoneda>
			<ImpuestoBimoneda>0.61000000</ImpuestoBimoneda>
			<NetoBimoneda>3.41000000</NetoBimoneda>
			<DrGlobalBimoneda>0.00000000</DrGlobalBimoneda>
			<TotalBimoneda>3.41000000</TotalBimoneda>
			<PrecioListaP>14.50000000</PrecioListaP>
			<Analisis1></Analisis1>
			<Analisis2></Analisis2>
			<Analisis3></Analisis3>
			<Analisis4></Analisis4>
			<Analisis5></Analisis5>
			<Analisis6></Analisis6>
			<Analisis7></Analisis7>
			<Analisis8></Analisis8>
			<Analisis9></Analisis9>
			<Analisis10></Analisis10>
			<Analisis11></Analisis11>
			<Analisis12></Analisis12>
			<Analisis13></Analisis13>
			<Analisis14>15031</Analisis14>
			<Analisis15></Analisis15>
			<Analisis16></Analisis16>
			<Analisis17></Analisis17>
			<Analisis18></Analisis18>
			<Analisis19></Analisis19>
			<Analisis20></Analisis20>
			<UniMedDynamic>1.00000000</UniMedDynamic>
			<ProdAlias></ProdAlias>
			<FechaVigenciaLp>30-12-2023</FechaVigenciaLp>
			<LoteDestino></LoteDestino>
			<SerieDestino></SerieDestino>
			<DoctoOrigenVal>N</DoctoOrigenVal>
			<DRGlobal1>0.00000000</DRGlobal1>
			<DRGlobal2>0.00000000</DRGlobal2>
			<DRGlobal3>0.00000000</DRGlobal3>
			<DRGlobal4>0.00000000</DRGlobal4>
			<DRGlobal5>0.00000000</DRGlobal5>
			<DRGlobal1Ingreso>0.00000000</DRGlobal1Ingreso>
			<DRGlobal2Ingreso>0.00000000</DRGlobal2Ingreso>
			<DRGlobal3Ingreso>0.00000000</DRGlobal3Ingreso>
			<DRGlobal4Ingreso>0.00000000</DRGlobal4Ingreso>
			<DRGlobal5Ingreso>0.00000000</DRGlobal5Ingreso>
			<DRGlobal1Bimoneda>0.00000000</DRGlobal1Bimoneda>
			<DRGlobal2Bimoneda>0.00000000</DRGlobal2Bimoneda>
			<DRGlobal3Bimoneda>0.00000000</DRGlobal3Bimoneda>
			<DRGlobal4Bimoneda>0.00000000</DRGlobal4Bimoneda>
			<DRGlobal5Bimoneda>0.00000000</DRGlobal5Bimoneda>
			<PorcentajeDr2>0.0000</PorcentajeDr2>
			<PorcentajeDr3>0.0000</PorcentajeDr3>
			<PorcentajeDr4>0.0000</PorcentajeDr4>
			<PorcentajeDr5>0.0000</PorcentajeDr5>
			<ValPorcentajeDr1>0.00000000</ValPorcentajeDr1>
			<ValPorcentajeDr2>0.00000000</ValPorcentajeDr2>
			<ValPorcentajeDr3>0.00000000</ValPorcentajeDr3>
			<ValPorcentajeDr4>0.00000000</ValPorcentajeDr4>
			<ValPorcentajeDr5>0.00000000</ValPorcentajeDr5>
			<ValPorcentajeDr1Ingreso>0.00000000</ValPorcentajeDr1Ingreso>
			<ValPorcentajeDr2Ingreso>0.00000000</ValPorcentajeDr2Ingreso>
			<ValPorcentajeDr3Ingreso>0.00000000</ValPorcentajeDr3Ingreso>
			<ValPorcentajeDr4Ingreso>0.00000000</ValPorcentajeDr4Ingreso>
			<ValPorcentajeDr5Ingreso>0.00000000</ValPorcentajeDr5Ingreso>
			<ValPorcentajeDr1Bimoneda>0.00000000</ValPorcentajeDr1Bimoneda>
			<ValPorcentajeDr2Bimoneda>0.00000000</ValPorcentajeDr2Bimoneda>
			<ValPorcentajeDr3Bimoneda>0.00000000</ValPorcentajeDr3Bimoneda>
			<ValPorcentajeDr4Bimoneda>0.00000000</ValPorcentajeDr4Bimoneda>
			<ValPorcentajeDr5Bimoneda>0.00000000</ValPorcentajeDr5Bimoneda>
			<CostoBimoneda>0</CostoBimoneda>
			<CupBimoneda>0</CupBimoneda>
			<MontoAsignado>0.00000000</MontoAsignado>
			<Analisis21></Analisis21>
			<Analisis22></Analisis22>
			<Analisis23></Analisis23>
			<Analisis24></Analisis24>
			<Analisis25></Analisis25>
			<Analisis26></Analisis26>
			<Analisis27></Analisis27>
			<Analisis28></Analisis28>
			<Analisis29>1000</Analisis29>
			<Analisis30>10</Analisis30>
			<Receta></Receta>
	    	</DETALLE>
	    	<PAGOS>
			<Empresa>003</Empresa>
			<TipoDocto>PEDIDO BITEL</TipoDocto>
			<Correlativo>15030</Correlativo>
			<Linea>1</Linea>
			<CodigoPago>CONTADO PEN</CodigoPago>
			<TipoPago>S</TipoPago>
			<FechaVcto>'.$Fecha.'</FechaVcto>
			<Monto>304.52000000</Monto>
			<MontoIngreso>304.52000000</MontoIngreso>
			<TipoDoctoPago>PEDIDO BITEL</TipoDoctoPago>
			<NroDoctoPago>15031</NroDoctoPago>
			<Cuenta>012010201001</Cuenta>
			<MontoBimoneda>84.59000000</MontoBimoneda>
			<AjusteBimoneda>0</AjusteBimoneda>
			<Entidad></Entidad>
			<NumAutoriza></NumAutoriza>
			<CuentaPago></CuentaPago>
			<FechaVctoTarjeta></FechaVctoTarjeta>
			<PropietarioTarjeta></PropietarioTarjeta>
			<FechaVctoDocto>'.$Fecha.'</FechaVctoDocto>
			<RutComprador></RutComprador>
			<RutGirador></RutGirador>
			<MonedaPago>S/</MonedaPago>
			<MontoPago>304.52000000</MontoPago>
			<ParidadPago>1.00000000</ParidadPago>
			<ValorGenerico></ValorGenerico>
			<LineaTipo>0</LineaTipo>
	    	</PAGOS>
	    	<VALORES>
			<Empresa>001</Empresa>
			<TipoDocto>PEDIDO BITEL</TipoDocto>
			<Correlativo>15030</Correlativo>
			<Nombre>Afecto</Nombre>
			<Orden>2</Orden>
			<Factor>0</Factor>
			<Monto>258.07000000</Monto>
			<MontoIngreso>258.07000000</MontoIngreso>
			<Ajuste>0.00000000</Ajuste>
			<AjusteIngreso>0.00000000</AjusteIngreso>
			<Texto></Texto>
			<Porcentaje>0.0000</Porcentaje>
			<MontoBimoneda>71.69000000</MontoBimoneda>
			<AjusteBimoneda>0</AjusteBimoneda>
	    	</VALORES>
	    	<VALORES>
			<Empresa>001</Empresa>
			<TipoDocto>PEDIDO BITEL</TipoDocto>
			<Correlativo>15030</Correlativo>
			<Nombre>IGV</Nombre>
			<Orden>3</Orden>
			<Factor>0</Factor>
			<Monto>46.45000000</Monto>
			<MontoIngreso>46.45000000</MontoIngreso>
			<Ajuste>0.00000000</Ajuste>
			<AjusteIngreso>0.00000000</AjusteIngreso>
			<Texto></Texto>
			<Porcentaje>0.0000</Porcentaje>
			<MontoBimoneda>12.90000000</MontoBimoneda>
			<AjusteBimoneda>0</AjusteBimoneda>
	    	</VALORES>
	    	<VALORES>
			<Empresa>001</Empresa>
			<TipoDocto>PEDIDO BITEL</TipoDocto>
			<Correlativo>15030</Correlativo>
			<Nombre>Neto</Nombre>
			<Orden>1</Orden>
			<Factor>0</Factor>
			<Monto>304.52000000</Monto>
			<MontoIngreso>304.52000000</MontoIngreso>
			<Ajuste>0.00000000</Ajuste>
			<AjusteIngreso>0.00000000</AjusteIngreso>
			<Texto></Texto>
			<Porcentaje>0.0000</Porcentaje>
			<MontoBimoneda>84.59000000</MontoBimoneda>
			<AjusteBimoneda>0</AjusteBimoneda>
	    	</VALORES>
	    </DOCUMENTO>
    </DOCUMENTO_LIST>

    ';

    // Crea un cliente SOAP
    $client = new SoapClient($wsdlUrl, [
        'trace' => 1, // Habilita el registro de solicitudes y respuestas SOAP
        'cache' => WSDL_CACHE_NONE, // Deshabilita la caché del WSDL
    ]);

    // Crea una solicitud SOAP
    $soapRequest = new \stdClass();
    $soapRequest->__sTextoXML = $xmlContent;

    try {
        // Llama al método del servicio SOAP
        $response = $client->InyectarDocumentoXML($soapRequest);

        // Accede a la respuesta
        $result = $response->InyectarDocumentoXMLResult;

        // Puedes procesar $result según tus necesidades

        return response()->json(['result' => $result]);
    } catch (\SoapFault $e) {
        // Manejo de excepciones SOAP
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

}

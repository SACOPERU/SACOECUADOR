<?php

use App\Http\Controllers\Admin\AdministrationController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Livewire\Admin\ShowProducts;
use Illuminate\Support\Facades\Route;
use App\Http\Livewire\Admin\CreateProduct;
use App\Http\Livewire\Admin\EditProduct;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Livewire\Admin\ShowCategory;
use App\Http\Livewire\Admin\BrandComponent;
use App\Http\Livewire\Admin\DepartmentComponent;
use App\Http\Livewire\Admin\StatusOrder;
use App\Http\Livewire\Admin\ShowDepartment;
use App\Http\Livewire\Admin\UserComponent;
use App\Http\Controllers\Admin\PromocionController;
use App\Http\Controllers\Admin\LogoTiendaController;
use App\Http\Controllers\Admin\ConsultaPrecioTachadoController;

use App\Http\Controllers\Admin\XcoverController;
use App\Http\Controllers\Admin\ScoverController;
use App\Http\Controllers\Admin\VcoverController;
use App\Http\Controllers\Admin\IcoverController;


use App\Http\Livewire\Admin\ShowBanner;
use App\Http\Livewire\Admin\ShowPromocion;
use App\Http\Controllers\Admin\ProductflexController;
use App\Http\Controllers\Admin\ConsultaPrecioController;
use App\Http\Controllers\Admin\InyectaDocumentoController;
use App\Http\Controllers\facturacion\DatosController;
use App\Models\Company;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Facturacion\RegisterController;
use App\Http\Controllers\Admin\OrderPartnerController;
use App\Http\Livewire\Admin\CanalSubcanal;
use App\Http\Livewire\Admin\PaisMoneda;
use App\Http\Livewire\Admin\EmpresaCanal;
use App\Http\Livewire\Admin\Parametrizacion;

//Vista de Index
Route::get('/', function(){
    return view('admin.dashboard');
})->name('dashboard');

//Adminstrador
Route::get('administraciones', [AdministrationController::class, 'index'])->name('admin.administraciones.index');
Route::get('administrar/paismoneda', PaisMoneda::class)->name('livewire.admin.pais-moneda');
Route::get('administrar/empresacanal', EmpresaCanal::class)->name('livewire.admin.empresa-canal');
Route::get('administrar/canalsubcanal', CanalSubcanal::class)->name('livewire.admin.canal-subcanal');
Route::get('administrar/parametrizacion', Parametrizacion::class)->name('livewire.admin.parametrizacion');

//ruta para PDF

Route::get('orders/{order}/pdf', [OrderController::class, 'pdf'])->name('admin.orders.pdf');
Route::get('orders/{order}/ticket', [OrderController::class, 'ticket'])->name('admin.orders.ticket');


Route::get('users', UserComponent::class)->name('admin.users.index');

//ruta vista de productos
Route::get('product/vista', ShowProducts::class)->name('admin.index');
Route::get('product/flexproduct', [ShowProducts::class, 'flexproduct'])->name('livewire.admin.show-products');

Route::get('products/create', CreateProduct::class)->name('admin.products.create');
Route::get('products/{product}/edit', EditProduct::class)->name('admin.products.edit');
Route::post('products/{product}/files', [ProductController::class, 'files'])->name('admin.products.files');

Route::get('categories', [CategoryController::class, 'index'])->name('admin.categories.index');
Route::get('categories/{category}', ShowCategory::class)->name('admin.categories.show');

Route::get('brands', BrandComponent::class)->name('admin.brands.index');

Route::get('orders', [OrderController::class, 'index'])->name('admin.orders.index');
Route::get('orders/{order}', [OrderController::class, 'show'])->name('admin.orders.show');

//Partner
Route::match(['get', 'put'],'/orderpartners', [OrderPartnerController::class, 'index_partner'])->name('admin.orderpartners.index');
Route::match(['get', 'put'],'/orderpartners/{order}', [OrderPartnerController::class, 'show'])->name('admin.orderpartners.show');

Route::post('orders/{order}/files', [StatusOrder::class, 'files'])->name('admin.orders.files');


Route::get('departments', DepartmentComponent::class)->name('admin.departments.index');
Route::get('departments/{department}', ShowDepartment::class)->name('admin.departments.show');


Route::get('banners', [BannerController::class, 'index'])->name('admin.banners.index');
Route::get('banners/{banner}', ShowBanner::class)->name('admin.banners.show');

Route::get('promocions', [PromocionController::class, 'index'])->name('admin.promocions.index');
Route::get('promocions/{promocion}', ShowPromocion::class)->name('admin.promocions.show');


Route::get('logotiendas', [LogoTiendaController::class, 'index'])->name('admin.logotienda.index');

//Banners de las Marcas
Route::match(['get', 'post'],'xcovers', [XcoverController::class, 'index'])->name('admin.xcovers.index');
Route::match(['get', 'post'],'scovers', [ScoverController::class, 'index'])->name('admin.scovers.index');
Route::match(['get', 'post'],'vcovers', [VcoverController::class, 'index'])->name('admin.vcovers.index');
Route::match(['get', 'post'],'icovers', [IcoverController::class, 'index'])->name('admin.icovers.index');


//Invoice
Route::get('Invoice', function () {
    $company = Company::first();

    return Storage::get($company->logo_path);
});


Route::middleware(['auth'])->group(function () {

   //CONSULTA FLEXLINE
    Route::match(['get', 'post'], '/consulta-productos', [ProductflexController::class, 'consultaProductos'])->name('livewire.admin.consulta-productos');
    Route::match(['get', 'post'], '/consulta-precio', [ConsultaPrecioController::class, 'consultaPrecio'])->name('livewire.admin.consulta-precio');
	Route::match(['get', 'post'], '/consulta-precio-tachado', [ConsultaPrecioTachadoController::class, 'consultaPrecioTachado'])->name('livewire.admin.consulta-precio-tachado');

});


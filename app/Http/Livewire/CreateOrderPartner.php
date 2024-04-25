<?php

namespace App\Http\Livewire;

use App\Models\City;
use Livewire\Component;
use App\Models\Department;
use App\Models\District;
use Gloudemans\Shoppingcart\Facades\Cart;
use App\Models\OrderPartner;
use Illuminate\Support\Facades\Redirect;

class CreateOrderPartner extends Component
{

    public $envio_type = 1;
    //enum
    public $tipo_doc = '';
    public $tipo_identidad = '';

    public $departments, $cities = [], $districts = [];
    public $department_id = "", $city_id = "", $district_id = "";
    public  $atocong, $jockeypz, $megaplz, $huaylas, $puruchu;
    public $selectedStore = '';
  	public  $zona = '';
  	public $cost;

    public
        $name_order,
        $phone_order,
        $dni_order,

        $ruc,
        $razon_social,
        $direccion_fiscal,
        $contacto_ruc,
        $email_ruc,
        $phone_ruc,

        $name,
        $dni,
        $phone,
        $email,

        $total,
        $content,
        $address,
        $references,
        $envio,
        $shipping_cost = 0;

    public $rules = [
        'name_order' => 'required',
        'phone_order' => 'required',
        'dni_order' => 'required',
        'envio_type' => 'required',
    ];

    public function mount()
    {
        $this->departments = Department::all();
    }

    public function updatedEnvioType($value)
    {
        if ($value == 1) {
            $this->resetValidation([
                'department_id',
                'city_id',
                'district_id',
                'address',
                'references',
            ]);
        }
    }
    public function updatedDepartmentId($value)
    {
        $this->cities = City::where('department_id', $value)->get();
    }

    public function updatedCityId($value)
    {
        $district = District::where('city_id', $value)->first();

        if ($district) {
            // Carga la relación 'city'
            $district->load('city');

            $this->shipping_cost = $district->cost;
            $this->districts = District::where('city_id', $value)->get();

            // Almacena la zona en la propiedad $zona
            $this->zona = $district->zona;
        } else {
            $this->shipping_cost = 0;
            $this->districts = collect();
            $this->zona = null; // Asegúrate de inicializar $zona si no hay distrito
        }
    }

    public function create_order()
    {
        $rules = $this->rules;

        if ($this->envio_type == 2) {
            $rules['department_id'] = 'required';
            $rules['city_id'] = 'required';
            $rules['district_id'] = 'required';
            $rules['address'] = 'required';
            $rules['references'] = 'required';
        }

        $this->validate($rules);

        $order_partner = new OrderPartner();

        $order_partner->user_id = auth()->user()->id;
        $order_partner->name_order = $this->name_order;
        $order_partner->phone_order = $this->phone_order;
        $order_partner->dni_order = $this->dni_order;

        $order_partner->tipo_doc =  $this->tipo_doc;
        $order_partner->tipo_identidad = $this->tipo_identidad;

        $order_partner->name = strtoupper($this->name);
        $order_partner->dni = $this->dni;
        $order_partner->phone = $this->phone;
        $order_partner->email = $this->email;

        $order_partner->ruc = $this->ruc;
        $order_partner->razon_social = strtoupper($this->razon_social);
        $order_partner->direccion_fiscal = $this->direccion_fiscal;
        $order_partner->contacto_ruc =  $this->contacto_ruc;
        $order_partner->email_ruc   =   $this->email_ruc;
        $order_partner->phone_ruc   =   $this->phone_ruc;

        $order_partner->envio_type = $this->envio_type;
        $order_partner->shipping_cost = 0;
        $order_partner->total = $this->shipping_cost + Cart::subtotal(2, '.', '');

        $order_partner->content = Cart::content();

        if ($this->envio_type == 2) {
            $newProduct = [
                'id' => 999,
                'name' => $this->zona,
                'qty' => 1,
                'price' => $this->shipping_cost,
                'weight' => 550,
                'options' => [
                    'sku' => $this->zona,
                    'image' => null,
                    'size_id' => null,
                    'color_id' => null,
                ],
                'subtotal' => $this->shipping_cost, // Usa la propiedad $shipping_cost directamente
            ];


            Cart::add($newProduct);

            $order_partner->shipping_cost = $this->shipping_cost;
            $order_partner->department_id = $this->department_id;
            $order_partner->city_id = $this->city_id;
            $order_partner->district_id = $this->district_id;
            $order_partner->address = $this->address;
            $order_partner->references = $this->references;
            $order_partner->selected_store = '01-CH-PRINCIPAL';
        } elseif ($this->envio_type == 1) {
            $order_partner->atocong = $this->atocong;
            $order_partner->jockeypz = $this->jockeypz;
            $order_partner->megaplz = $this->megaplz;
            $order_partner->huaylas = $this->huaylas;
            $order_partner->puruchu = $this->puruchu;
            $order_partner->selected_store = $this->selectedStore;
        }

        $order_partner->save();

        foreach (Cart::content() as $item) {
            discount($item);
        }

        Cart::destroy();

        return redirect()->route('orderpartners.payment', $order_partner);

    }

    public function render()
    {
        return view('livewire.create-order-partner')->layout('layouts.partner');
    }
}

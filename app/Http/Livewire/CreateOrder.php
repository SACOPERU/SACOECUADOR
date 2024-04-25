<?php

namespace App\Http\Livewire;

use App\Models\City;
use Livewire\Component;
use App\Models\Department;
use App\Models\District;
use Gloudemans\Shoppingcart\Facades\Cart;
use App\Models\Order;
use Illuminate\Support\Facades\Redirect;

class CreateOrder extends Component
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
        $name,
        $dni,
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

        $order = new Order();

        $order->user_id = auth()->user()->id;
        $order->name_order = $this->name_order;
        $order->phone_order = $this->phone_order;
        $order->dni_order = $this->dni_order;
        $order->name = strtoupper($this->name);
        $order->dni = $this->dni;
        $order->tipo_doc =  $this->tipo_doc;
        $order->tipo_identidad = $this->tipo_identidad;
        $order->ruc = $this->ruc;
        $order->razon_social = strtoupper($this->razon_social);
        $order->envio_type = $this->envio_type;
        $order->direccion_fiscal = $this->direccion_fiscal;
        $order->shipping_cost = 0;
        $order->total = $this->shipping_cost + Cart::subtotal(2, '.', '');

        $order->content = Cart::content();

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

            $order->shipping_cost = $this->shipping_cost;
            $order->department_id = $this->department_id;
            $order->city_id = $this->city_id;
            $order->district_id = $this->district_id;
            $order->address = $this->address;
            $order->references = $this->references;
            $order->selected_store = '03-LIM-BSF-WEBSACO';
        } elseif ($this->envio_type == 1) {
            $order->atocong = $this->atocong;
            $order->jockeypz = $this->jockeypz;
            $order->megaplz = $this->megaplz;
            $order->huaylas = $this->huaylas;
            $order->puruchu = $this->puruchu;
            $order->selected_store = $this->selectedStore;
        }

        $order->save();

        foreach (Cart::content() as $item) {
            discount($item);
        }

        Cart::destroy();

        return redirect()->route('orders.payment', $order);
    }

    public function render()
    {
        return view('livewire.create-order');
    }
}
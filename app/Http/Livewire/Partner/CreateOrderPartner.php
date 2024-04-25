<?php

namespace App\Http\Livewire\Partner;

use App\Models\City;
use Livewire\Component;
use App\Models\Department;
use App\Models\District;
use Gloudemans\Shoppingcart\Facades\Cart;
use App\Models\OrderPartner;
use Illuminate\Support\Facades\Redirect;

class CreateOrderPartner extends Component
{
    public $envio_type =1;

    public $contact,$phone ,$address, $references, $shipping_cost = 0, $dni ;

    public $departments, $cities = [], $districts = [];
    public $department_id ="", $city_id = "", $district_id = "";

    public $rules = [
        'contact' => 'required',
        'phone' => 'required',
        'envio_type' => 'required',
        'dni' => 'required'
    ];

    public function mount(){
        $this->departments = Department::all();

    }

    public function updatedEnvioType($value){
        if ($value == 1){
            $this->resetValidation([
                'department_id',
                'city_id',
                'district_id',
                'address',
                'references',
            ]);
        }

    }
    public function updatedDepartmentId($value){
            $this->cities = City::where('department_id', $value)->get();
    }

    public function updatedCityId($value){

            $city = City::find($value);

            $this->shipping_cost = $city->cost;
            $this->districts = District::where('city_id', $value)->get();
            $this->reset('district_id');

    }

    public function create_orderpartner(){
        $rules= $this->rules;

        if($this->envio_type == 2){
            $rules['department_id'] = 'required';
            $rules['city_id'] = 'required';
            $rules['district_id'] = 'required';
            $rules['address'] = 'required';
            $rules['references'] = 'required';

        }

        $this->validate($rules);

        $order_partner = new OrderPartner();
        //dd($order);

        $order_partner->user_id = auth()->user()->id;
        $order_partner->contact = $this->contact;
         $order_partner->phone = $this->phone;
         $order_partner->dni = $this->dni;
         $order_partner->envio_type = $this->envio_type;
         $order_partner->shipping_cost = 0;
         $order_partner->total = $this->shipping_cost + Cart::subtotal(2,'.','');
        //dd($order->total);
        $order_partner->content = Cart::content();


        if ($this->envio_type == 2) {

             $order_partner->shipping_cost = $this->shipping_cost;
             $order_partner->department_id = $this->department_id;
             $order_partner->city_id = $this->city_id;
             $order_partner->district_id = $this->district_id;
             $order_partner->address = $this->address;
             $order_partner->references = $this->references;
        }

        $order_partner->save();

        foreach (Cart::content() as $item) {
            discount($item);
        }

        Cart::destroy();

        return redirect()->route('orderpartner.payment',  $order_partner);

    }

    public function render()
    {
        return view('livewire.partner.create-order-partner')->layout('layouts.partner');
    }
}

<div class="container py-8 grid lg:grid-cols-2 xl:grid-cols-5 gap-6">


    <div class="order-2 lg:order-1 lg:col-span-1 xl:col-span-3">

        <div class="bg-white rounded-lg shadow p-6">
            <div class="col-span-2">
                <x-jet-label value="Tipo de Identidad" />
                <select wire:model="tipo_identidad" class="form-control w-full">
                    <option value="" disabled selected>Seleccione Tipo de Identidad *Obligatorio</option>
                    <option value="04">RUC</option>
                    <option value="05">CEDULA</option>
                    <option value="06">PASAPORTE</option>
                </select>
                <x-jet-input-error for="tipo_identidad" />
            </div>
        </div>

        <p class="mt-6 mb-3 text-lg  text-gray-700 text-semibold">Datos de Cliente</p>
        <div class="px-6 p-6 grid grid-cols-2 gap-6 rounded-lg shadow  bg-white">
            <div class="col-span-1"> <!-- Primer div a la izquierda -->
                <div>
                    <div class="col-span-2 mb-4">
                        <x-jet-label value="RUC"/>
                        <x-jet-input class="w-full" wire:model="ruc" type="text"/>
                        <x-jet-input-error for="ruc"/>
                    </div>

                    <div class="col-span-2 mb-4">
                        <x-jet-label value="Razon Social"/>
                        <x-jet-input class="w-full" wire:model="razon_social" type="text"/>
                        <x-jet-input-error for="razon_social"/>
                    </div>

                    <div class="col-span-2 mb-4">
                        <x-jet-label value="Direccion Fiscal"/>
                        <x-jet-input class="w-full" wire:model="direccion_fiscal" type="text"/>
                        <x-jet-input-error for="direccion_fiscal"/>
                    </div>
                </div>
            </div>

            <div class="col-span-1"> <!-- Segundo div a la izquierda -->
                <div>
                    <div class="col-span-2 mb-4">
                        <x-jet-label value="Contacto"/>
                        <x-jet-input class="w-full" wire:model="contacto_ruc" type="text"/>
                        <x-jet-input-error for="contacto_ruc"/>
                    </div>

                    <div class="col-span-2 mb-4">
                        <x-jet-label value="Correo"/>
                        <x-jet-input class="w-full" wire:model="email_ruc" type="text"/>
                        <x-jet-input-error for="email_ruc"/>
                    </div>

                    <div class="col-span-2 mb-4">
                        <x-jet-label value="Celular"/>
                        <x-jet-input class="w-full" wire:model="phone_ruc" type="text"/>
                        <x-jet-input-error for="phone_ruc"/>
                    </div>
                </div>
            </div>
        </div>

        <div x-data="{envio_type: @entangle('envio_type')}">
            <p class="mt-6 mb-3 text-lg text-gray-700 text-semibold">Envios</p>

            <div class="bg-white rounded-lg shadow">
                <label class="px-6 py-4 flex items-center">
                    <input x-model="envio_type" type="radio" value="2" name="envio_type" class="text-gray-700">
                    <span class="ml-2 text-gray-700">Envio a Domicilio</span>
                </label>

                <div class="px-6 pb-6 grid grid-cols-2 gap-6" :class="{'hidden': envio_type != 2}">
                    {{--Departamento--}}
                    <div>
                        <x-jet-label value="Departamento"/>
                        <select name="" id="" class="form-control w-full" wire:model="department_id">
                            <option value="" disabled selected>Selecione un Departamento</option>
                            @foreach ($departments as $department)
                                <option value="{{$department->id}}">{{$department->name}}</option>
                            @endforeach
                        </select>
                        <x-jet-input-error for="department_id"/>
                    </div>

                    {{--Ciudad--}}
                    <div>
                        <x-jet-label value="Ciudad"/>
                        <select name="" id="" class="form-control w-full" wire:model="city_id">
                            <option value="" disabled selected>Selecione una Ciudad</option>
                            @foreach ($cities as $city)
                                <option value="{{$city->id}}">{{$city->name}}</option>
                            @endforeach
                        </select>
                        <x-jet-input-error for="city_id"/>
                    </div>

                    {{--Distritos--}}
                    <div>
                        <x-jet-label value="Distrito"/>
                        <select name="" id="" class="form-control w-full" wire:model="district_id">
                            <option value="" disabled selected>Selecione un Distrito</option>
                            @foreach ($districts as $district)
                                <option value="{{$district->id}}">{{$district->name}}</option>
                            @endforeach
                        </select>
                        <x-jet-input-error for="district_id"/>
                    </div>

                    <div>
                        <x-jet-label value="Direccion"/>
                        <x-jet-input  class="w-full" wire:model="address" type="text"/>
                        <x-jet-input-error for="address"/>
                    </div>

                    <div class="col-span-2">
                        <x-jet-label value="Referencia"/>
                        <x-jet-input class="w-full" wire:model="references" type="text"/>
                        <x-jet-input-error for="references"/>
                    </div>
                    <span class="text-sm">Ubigeo {{$district_id}}</span>
                </div>
            </div>
        </div>

        <div>
            <x-button-partner
                wire:loading.attr="disabled"
                wire:target="create_order"
                class="mt-6 mb-4 text-xs"
                wire:click="create_order">
                Crear Pedido
            </x-button-partner >
        </div>

    </div>

    <div class="order-1 lg:order-2 lg:col-span-1 xl:col-span-2">
        <div class="bg-white rounded-lg shadow p-6">
            <ul>
                @forelse (Cart::content() as $item)
                    <li class="flex p-2 border-b border-blue-500">
                        <img class="h-15 w-20 object-cover mr-4" src="{{$item->options->image}}" alt="">
                        <article class="flex-1">
                            <h1 class="font-bold text-gray-700">{{$item->name}}</h1>
                            <h1 class=" text-xs text-green-400">{{$item->options->sku}}</h1>
                            <div class="flex">
                                <p>Cant: {{$item->qty}}</p>
                            </div>
                            <p class="font-bold text-blue-500">S/ {{$item->price}}</p>
                        </article>
                    </li>
                @empty
                    <li class="py-6 px-4">
                        <p class="text-center text-gray-700">
                            No tiene agregado ningún item en el carrito
                        </p>
                    </li>
                @endforelse
            </ul>
            <hr class="mt-4 mb-3">
            <div class="text-gray-700">
                <p class="flex justify-between items-center font-bold">
                    Subtotal
                    <span class="font-bold"> S/ {{Cart::subtotal(2,'.')}}</span>
                </p>
                <p class="flex justify-between items-center">
                    Envio
                    <span class="font-semibold">
                        @if ($envio_type == 1)
                            Gratis
                        @else
                            S/ {{$shipping_cost}}
                        @endif
                    </span>
                </p>
                <hr class="mt-4 mb-3">
                <p class="flex justify-between items-center text-blue-500 font-bold">
                    <span class="font-semibold text-lg">Total</span>
                    @php
                        $subtotal = floatval(str_replace(',', '', Cart::subtotal(2, '.')));
                        $total = $envio_type == 1 ? $subtotal : $subtotal + $shipping_cost;
                    @endphp
                    S/ {{ number_format($total, 2) }}
                </p>
            </div>
        </div>

    </div>

</div>

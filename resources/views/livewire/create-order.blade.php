<div class="container py-8 grid lg:grid-cols-2 xl:grid-cols-5 gap-6">

    <div class="order-2 lg:order-1 lg:col-span-1 xl:col-span-3">
        <p class="mt-6 mb-3 text-lg text-gray-700 text-semibold">Recepcion de Pedido</p>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="mb-4">
                <x-jet-label value="Nombre de Contacto" />
                <x-jet-input type="text"
                            wire:model.defer="name_order"
                            placeholder="Ingrese el nombre de la persona que lo va a recoger"
                            class="w-full" />
                <x-jet-input-error for="name_order"/>
            </div>

            <div class="mb-4">
                <x-jet-label value="Telefono de contacto" />
                <x-jet-input type="text"
                            wire:model.defer="phone_order"
                            placeholder="Ingrese numero de telefono de contacto"
                            class="w-full" />
                <x-jet-input-error for="phone_order"/>
            </div>

            <div>
                <x-jet-label value="Dni" />
                <x-jet-input type="text"
                            wire:model.defer="dni_order"
                            placeholder="Ingrese su documento de Identidad"
                            class="w-full" />
                <x-jet-input-error for="dni_order"/>
            </div>
        </div>
        <br>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="col-span-2">
                <x-jet-label value="Tipo de Identidad" />
                <select wire:model="tipo_identidad" class="form-control w-full">
                    <option value="" disabled selected>Seleccione Tipo de Identidad *Obligatorio</option>
                    <option value="6">RUC</option>
                    <option value="1">DNI</option>
                    <option value="4">CARNET DE EXTRANJERIA</option>
                    <option value="7">PASAPORTE</option>
                </select>
                <x-jet-input-error for="tipo_identidad" />
            </div>
        </div>

        <div x-data="{tipo_doc: @entangle('tipo_doc')}">
            <p class="mt-6 mb-3 text-lg text-gray-700 text-semibold">Datos de Factura-Boleta</p>

                <div class="bg-white rounded-lg shadow">
                    <label class=" px-6 py-4 flex items-center">
                        <input x-model="tipo_doc" type="radio" value="2" name="tipo_doc" class="text-gray-700">
                            <span class="font-semibold text-gray-700 ml-2">
                                Factura
                            </span>
                    </label>

                    <div class="px-6 pb-6 grid grid-cols-2 gap-6" :class="{'hidden': tipo_doc != 2}">

                        <div>
                            <div class="col-span-2">
                                <x-jet-label value="RUC"/>
                                <x-jet-input class="w-full" wire:model="ruc" type="text"/>
                                <x-jet-input-error for="ruc"/>
                            </div>

                            <div class="col-span-2">
                                <x-jet-label value="Razon Social"/>
                                <x-jet-input class="w-full" wire:model="razon_social" type="text"/>
                                <x-jet-input-error for="razon_social"/>
                            </div>

                            <div class="col-span-2">
                                <x-jet-label value="Direccion Fiscal"/>
                                <x-jet-input class="w-full" wire:model="direccion_fiscal" type="text"/>
                                <x-jet-input-error for="direccion_fiscal"/>
                            </div>
                        </div>
                    </div>

                </div>

                <br>

                <div class="bg-white rounded-lg shadow">
                    <label class="px-6 py-4 flex items-center">
                        <input x-model="tipo_doc" type="radio" value="1" name="tipo_doc" class="text-gray-700">
                        <span class="font-semibold text-gray-700 ml-2">Boleta</span>
                    </label>

                    <div class="px-6 pb-6 grid grid-cols-2 gap-6" :class="{'hidden': tipo_doc != 1}">

                        <div class="col-span-2">
                            <x-jet-label value="Nombre y Apellido" />
                            <x-jet-input class="w-full" wire:model="name" type="text" />
                            <x-jet-input-error for="name" />
                        </div>

                        <div class="col-span-2">
                            <x-jet-label value="DNI" />
                            <x-jet-input class="w-full" wire:model="dni" type="text" />
                            <x-jet-input-error for="dni" />
                        </div>
                    </div>
                </div>

        </div>

        <div x-data="{envio_type: @entangle('envio_type')}">
            <p class="mt-6 mb-3 text-lg text-gray-700 text-semibold">Envios</p>

            <label class="bg-white rounded-lg shadow px-6 py-4 flex items-center mb-4">
                <input x-model="envio_type" type="radio" value="1" name="envio_type" class="text-gray-700">
                <span class="ml-2 text-gray-700">Recojo en Tienda</span>
                <x-jet-label value="" />

                <select wire:model="selectedStore" name="store" class="form-control w-full">
                    <option value="" disabled selected>Seleccione una tienda</option>
                    <option value="03-LIM-ATOCONG-MISTR">Tienda Open Plaza Atocongo</option>
                    <option value="03-LIM-JOCKEYPZ-MIST">Tienda Tottus Jockey Plaza</option>
                    <option value="03-LIM-MEGAPLZ-MISTR">Tienda Tottus Mega plaza</option>
                    <option value="03-LIM-HUAYLAS-MISTR">Tienda Tottus Huaylas</option>
                    <option value="03-LIM-PURUCHU-MISTR">Tienda Real Plaza Tottus Puruchuco</option>
                </select>
            </label>


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
            <x-jet-button
                wire:loading.attr="disabled"
                wire:target="create_order"
                class="mt-6 mb-4 text-xs"
                wire:click="create_order">
                Continuar con la compra
            </x-jet-button>
        </div>
        <div>
            <form>
                <button type="submit"></button>
                <input type="checkbox" name="envio" id="envio" class="form-check-input text-gray-700" required aria-invalid="true">
                <label class="text-sm text-gray-700 mt-2">
                    Acepto
                <a href="{{ asset('docs/politicas.pdf') }}" class="font-semibold text-red-700 inline-block cursor-pointer hover:underline" target="_blank">términos y condiciones</a>
                </label>
                <div role="alert" style="color: red; display: none;">Por favor, acepta los términos y condiciones</div>
            </form>
        </div>
    </div>

    <div class="order-1 lg:order-2 lg:col-span-1 xl:col-span-2">
        <div class="bg-white rounded-lg shadow p-6">
            <ul>
                @forelse (Cart::content() as $item)
                    <li class="flex p-2 border-b border-gray-700">
                        <img class="h-15 w-20 object-cover mr-4" src="{{$item->options->image}}" alt="">
                        <article class="flex-1">
                            <h1 class="font-bold text-red-600">{{$item->name}}</h1>
                            <h1 class="font-bold text-blue-400">{{$item->options->sku}}</h1>
                            <div class="flex">
                                <p>Cant: {{$item->qty}}</p>
                            </div>
                            <p>S/ {{$item->price}}</p>
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
                <p class="flex justify-between items-center text-red-700 font-bold">
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

<div class="container py-8 grid grid-cols-5 gap-6">
    <div class="col-span-3">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="mb-4 ">
                <x-jet-label value="Nombre de Contacto" />
                <x-jet-input type="text"
                            wire:model.defer="contact"
                            placeholder="Ingrese el nombre de la persona que lo va a recoger"
                            class="w-full" />
                <x-jet-input-error for="contact"/>
            </div>

            <div class="mb-4 ">
                <x-jet-label value="Telefono de contacto" />
                <x-jet-input type="text"
                            wire:model.defer="phone"
                            placeholder="Ingrese numero de telefono de contacto"
                            class="w-full" />
                <x-jet-input-error for="phone"/>

            </div>

            <div>
                <x-jet-label value="Dni" />
                <x-jet-input type="text"
                            wire:model.defer="dni"
                            placeholder="Ingrese su documento de Identidad"
                            class="w-full" />
                <x-jet-input-error for="dni"/>

            </div>
        </div>

        <div x-data="{envio_type: @entangle('envio_type')}">
            <p class="mt-6 mb-3 text-lg text-gray-700 text-semibold">Envios</p>
            <label class="bg-white rounded-lg shadow px-6 py-4 flex items-center mb-4">
                <input x-model="envio_type" type="radio" value="1" name="envio_type" class="text-gray-700">
                <span class="ml-2 text-gray-700">Recojo en tienda (Nombre de la tienda Ojo)</span>
                <span class="font-semibold text-gray-700 ml-auto">Gratis</span>
            </label>
            <div class=" bg-white rounded-lg shadow">
            <label class=" px-6 py-4 flex items-center">
                <input x-model="envio_type" type="radio" value="2" name="envio_type" class="text-gray-700">
                <span class="ml-2 text-gray-700">Envio a Domicilio</span>

            </label>

            <div class="px-6 pb-6 grid grid-cols-2 gap-6" :class="{'hidden':envio_type!=2}">
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
                wire:target="create_orderpartner"
                class=" mt-6 mb-4 text-xs"
                wire:click="create_orderpartner">
                    Continuar con la compra
                </x-jet-button>
                <hr>
                <input type="checkbox" name="envio" class="form-check-input text-gray-700">
                <label class="text-sm text-gray-700 mt-2">Acepto <a href="" class="font-semibold text-red-700 inline-block cursor-pointer hover:underline">términos y condiciones</a> y la <a href="" class="font-semibold text-red-700 inline-block cursor-pointer hover:underline">Política de Privacidad y Tratamiento de Datos Personales</a></label>


            </div>
    </div>

    <div class="col-span-2">
       <div class="bg-white rounded-lg shadow p-6">
        <ul>
            @forelse (Cart::content() as $item)
                <li class="flex p-2 border-b border-yellow-300">
                    <img class="h-15 w-20 object-cover mr-4" src="{{$item->options->image}}" alt="">

                    <article class="flex-1">
                        <h1 class="font-bold text-red-600">{{$item->name}}</h1>
                        <h1 class="font-bold text-red-600">{{$item->sku}}</h1>

                        <div class="flex">
                            <p>Cant: {{$item->qty}}</p>
                            @isset($item->options['color'])
                                <p class="mx-2">- Color: {{__($item->options['color'])}}</p>
                            @endisset

                            @isset($item->options['size'])
                                <p>{{$item->options['size']}}</p>
                            @endisset

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
                <span class="font-bold"> S/ {{Cart::subtotal(2,'.','')}}</span>

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

                @if ($envio_type == 1)

                    S/ {{Cart::subtotal(2,'.','')}}

                @else
                S/ {{Cart::subtotal(2,'.','') + $shipping_cost}}
                @endif
            </p>
        </div>
        </div>
    </div>
</div>

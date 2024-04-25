<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 text-gray-700">
    <div class="bg-white shadow-xl rounded-lg p-6">
        <form wire:submit.prevent="guardar" class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <div class="mb-4">
                <x-jet-label value="Empresa id" />
                <x-jet-input type="text" class="w-full" wire:model="empresa" placeholder="Ingrese Empresa id" />
                <x-jet-input-error for="empresa" />
            </div>

            <div class="mb-4">
                <x-jet-label value="Desc. Empresa" />
                <x-jet-input type="text" class="w-full" wire:model="desc_empresa"
                    placeholder="Ingrese Desc. Empresa" />
                <x-jet-input-error for="desc_empresa" />
            </div>

            <div class="mb-4">
                <x-jet-label value="Ruc" />
                <x-jet-input type="text" class="w-full" wire:model="ruc" placeholder="Ingrese Ruc" />
                <x-jet-input-error for="ruc" />
            </div>

            <div class="mb-4">
                <x-jet-label value="Nombre Comercial" />
                <x-jet-input type="text" class="w-full" wire:model="nombre_comercial"
                    placeholder="Ingrese Nombre Comercial" />
                <x-jet-input-error for="nombre_comercial" />
            </div>

            <div class="mb-4">
                <x-jet-label value="Direccion" />
                <x-jet-input type="text" class="w-full" wire:model="direccion" placeholder="Ingrese Direccion" />
                <x-jet-input-error for="direccion" />
            </div>

            <div class="mb-4">
                <x-jet-label value="Telefono 1" />
                <x-jet-input type="text" class="w-full" wire:model="telefono01" placeholder="Ingrese Telefono" />
                <x-jet-input-error for="telefono01" />
            </div>

            <div class="mb-4">
                <x-jet-label value="Telefono 2" />
                <x-jet-input type="text" class="w-full" wire:model="telefono02" placeholder="Ingrese Telefono" />
                <x-jet-input-error for="telefono02" />
            </div>

            <div class="mb-4">
                <x-jet-label value="Correo" />
                <x-jet-input type="text" class="w-full" wire:model="correo" placeholder="Ingrese Correo" />
                <x-jet-input-error for="correo" />
            </div>

            <div class="mb-4">
                <x-jet-label value="logo_path" />
                <x-jet-input type="text" class="w-full" wire:model="logo_path" placeholder="Ingrese logo_path" />
                <x-jet-input-error for="logo_path" />
            </div>

            <div class="mb-4">
                <x-jet-label value="PAIS" />
                <select class="w-full form-control" wire:model="pais_id">
                    <option value="" selected disabled>Seleccione un País</option>
                    @foreach ($paises as $pais)
                        <option value="{{ $pais->id }}">{{ $pais->desc_pais }}</option>
                    @endforeach
                </select>
                <x-jet-input-error for="pais_id" />
            </div>

            <div class="mb-4">
                <x-jet-label value="moneda_id" />
                <select class="w-full form-control" wire:model="moneda_id">
                    <option value="0" selected disabled>Seleccione una Moneda</option>
                    @foreach ($paises as $moneda)
                        <option value="{{ $moneda->id }}">{{ $moneda->desc_moneda }}</option>
                    @endforeach
                </select>
                <x-jet-input-error for="moneda_id" />
            </div>

            <div class="flex mt-4 col-span-2">
                <x-jet-button wire:loading.attr="disabled" wire:target="guardar" wire:click="guardar" class="ml-auto">
                    Guardar
                </x-jet-button>
            </div>
        </form>
    </div>
</div>

<div class="flex items-center container py-10">
    <table style="width:100%; border-collapse: collapse; margin-top: 10px;">
        <thead>
            <tr style="background-color: #f2f2f2;">
                <th style="padding: 8px; border: 1px solid #ddd;">Empresa</th>
                <th style="padding: 8px; border: 1px solid #ddd;">Descripción de la Empresa</th>
                <th style="padding: 8px; border: 1px solid #ddd;">RUC</th>
                <th style="padding: 8px; border: 1px solid #ddd;">Nombre Comercial</th>
                <th style="padding: 8px; border: 1px solid #ddd;">Dirección</th>
                <th style="padding: 8px; border: 1px solid #ddd;">Teléfono 01</th>
                <th style="padding: 8px; border: 1px solid #ddd;">Teléfono 02</th>
                <th style="padding: 8px; border: 1px solid #ddd;">Correo</th>
                <th style="padding: 8px; border: 1px solid #ddd;">País</th>
                <th style="padding: 8px; border: 1px solid #ddd;">Moneda</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($empresasCanales as $empresaCanal)
                <tr>
                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $empresaCanal->empresa }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $empresaCanal->desc_empresa }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $empresaCanal->ruc }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $empresaCanal->nombre_comercial }}</td>

                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $empresaCanal->direccion }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $empresaCanal->telefono01 }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $empresaCanal->telefono02 }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $empresaCanal->correo }}</td>

                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $empresaCanal->pais->desc_pais }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $empresaCanal->moneda->desc_moneda }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

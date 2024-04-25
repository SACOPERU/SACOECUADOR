<div class="width:100%; border-collapse: collapse; margin-top: 10px; py-10">
    <div class="bg-white shadow-xl rounded-lg p-6">
        <form wire:submit.prevent="guardar" class="grid grid-cols-1 md:grid-cols-4 gap-4">

            <div class="mb-4">
                <x-jet-label value="Seleccione un ID Empresa" />
                <select class="w-full form-control" wire:model="empresa_id">
                    <option value="" selected disabled></option>
                    @foreach ($empresas as $empresa)
                        <option value="{{ $empresa->id }}">{{ $empresa->empresa }}</option>
                    @endforeach
                </select>
                <x-jet-input-error for="empresa_id" />
            </div>

            <div class="mb-4">
                <x-jet-label value="Selececione Desc. Empresa" />
                <select class="w-full form-control" wire:model="desc_empresa_id">
                    <option value="" selected disabled></option>
                    @foreach ($empresas as $empresa)
                        <option value="{{ $empresa->id }}">{{ $empresa->desc_empresa }}</option>
                    @endforeach
                </select>
                <x-jet-input-error for="desc_empresa_id" />
            </div>

            <div class="mb-4">
                <x-jet-label value="Seleccione el ID Canal" />
                <select class="w-full form-control" wire:model="canal_id">
                    <option value="" selected disabled></option>
                    @foreach ($canales as $canal)
                        <option value="{{ $canal->id }}">{{ $canal->canal }}</option>
                    @endforeach
                </select>
                <x-jet-input-error for="canal_id" />
            </div>

            <div class="mb-4">
                <x-jet-label value="Seleccione Desc. Canal" />
                <select class="w-full form-control" wire:model="desc_canal_id">
                    <option value="" selected disabled></option>
                    @foreach ($canales as $canal)
                        <option value="{{ $canal->id }}">{{ $canal->desc_canal }}</option>
                    @endforeach
                </select>
                <x-jet-input-error for="desc_canal_id" />
            </div>

            <div class="mb-4">
                <x-jet-label value="Seleccione un Subcanal" />
                <select class="w-full form-control" wire:model="subcanal_id">
                    <option value="" selected disabled></option>
                    @foreach ($canales as $canal)
                        <option value="{{ $canal->id }}">{{ $canal->subcanal }}</option>
                    @endforeach
                </select>
                <x-jet-input-error for="subcanal_id" />
            </div>

            <div class="mb-4">
                <x-jet-label value="Seleccione un Desc. SubCanal" />
                <select class="w-full form-control" wire:model="desc_subcanal_id">
                    <option value="" selected disabled></option>
                    @foreach ($canales as $canal)
                        <option value="{{ $canal->id }}">{{ $canal->desc_subcanal }}</option>
                    @endforeach
                </select>
                <x-jet-input-error for="desc_subcanal_id" />
            </div>

            <div class="mb-4">
                <x-jet-label value="Seleccione un Modelo de Negocio" />
                <select class="w-full form-control" wire:model="modelo_negocio_id">
                    <option value="" selected disabled></option>
                    @foreach ($canales as $canal)
                        <option value="{{ $canal->id }}">{{ $canal->modelo_negocio }}</option>
                    @endforeach
                </select>
                <x-jet-input-error for="modelo_negocio_id" />
            </div>

            <div class="mb-4">
                <x-jet-label value="Seleccione una Bodega" />
                <select class="w-full form-control" wire:model="bodega_id">
                    <option value="" selected disabled></option>
                    @foreach ($canales as $canal)
                        <option value="{{ $canal->id }}">{{ $canal->bodega }}</option>
                    @endforeach
                </select>
                <x-jet-input-error for="bodega_id" />
            </div>

            <div class="mb-4">
                <x-jet-label value="Seleccione tipo de Distribucion" />
                <select class="w-full form-control" wire:model="tipo_distribucion_id">
                    <option value="" selected disabled></option>
                    @foreach ($canales as $canal)
                        <option value="{{ $canal->id }}">{{ $canal->tipo_distribucion }}</option>
                    @endforeach
                </select>
                <x-jet-input-error for="tipo_distribucion_id" />
            </div>


            <div class="mb-4">
                <x-jet-label value="Seleccione un ID LP FLexline VISUAL" />
                <select class="w-full form-control" wire:model="idflexline_visual_id">
                    <option value="" selected disabled></option>
                    @foreach ($canales as $canal)
                        <option value="{{ $canal->id }}">{{ $canal->idflexline_visual }}</option>
                    @endforeach
                </select>
                <x-jet-input-error for="idflexline_visual_id" />
            </div>

            <div class="mb-4">
                <x-jet-label value="Seleccione un LP Visual" />
                <select class="w-full form-control" wire:model="lp_visual_id">
                    <option value="" selected disabled></option>
                    @foreach ($canales as $canal)
                        <option value="{{ $canal->id }}">{{ $canal->lp_visual }}</option>
                    @endforeach
                </select>
                <x-jet-input-error for="lp_visual_id" />
            </div>

            <div class="mb-4">
                <x-jet-label value="Seleccione Desc. LP Visual" />
                <select class="w-full form-control" wire:model="desc_lp_visual_id">
                    <option value="" selected disabled></option>
                    @foreach ($canales as $canal)
                        <option value="{{ $canal->id }}">{{ $canal->desc_lp_visual }}</option>
                    @endforeach
                </select>
                <x-jet-input-error for="desc_lp_visual_id" />
            </div>

            <div class="mb-4">
                <x-jet-label value="Seleccione un ID LP FLexline Neto" />
                <select class="w-full form-control" wire:model="idflexline_neto_id">
                    <option value="" selected disabled></option>
                    @foreach ($canales as $canal)
                        <option value="{{ $canal->id }}">{{ $canal->idflexline_visual }}</option>
                    @endforeach
                </select>
                <x-jet-input-error for="idflexline_neto_id" />
            </div>


            <div class="mb-4">
                <x-jet-label value="Seleccione  un LP Neto" />
                <select class="w-full form-control" wire:model="lp_neto_id">
                    <option value="" selected disabled></option>
                    @foreach ($canales as $canal)
                        <option value="{{ $canal->id }}">{{ $canal->lp_neto }}</option>
                    @endforeach
                </select>
                <x-jet-input-error for="lp_neto_id" />
            </div>

            <div class="mb-4">
                <x-jet-label value="Seleccione un Desc. LP Neto" />
                <select class="w-full form-control" wire:model="desc_lp_neto_id">
                    <option value="" selected disabled></option>
                    @foreach ($canales as $canal)
                        <option value="{{ $canal->id }}">{{ $canal->desc_lp_neto }}</option>
                    @endforeach
                </select>
                <x-jet-input-error for="desc_lp_neto_id" />
            </div>


            <!-- Repite la estructura anterior para los demás campos -->

            <div class="flex mt-4 col-span-2">
                <x-jet-button wire:loading.attr="disabled" wire:target="guardar" wire:click="guardar"
                    class="ml-auto">
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
                <th style="padding: 8px; border: 1px solid #ddd;">Canal</th>
                <th style="padding: 8px; border: 1px solid #ddd;">Descripción del Canal</th>
                <th style="padding: 8px; border: 1px solid #ddd;">Subcanal</th>
                <th style="padding: 8px; border: 1px solid #ddd;">Descripción del Subcanal</th>
                <th style="padding: 8px; border: 1px solid #ddd;">Modelo de Negocio</th>
                <th style="padding: 8px; border: 1px solid #ddd;">Bodega</th>
                <th style="padding: 8px; border: 1px solid #ddd;">Tipo de Distribución</th>

                <th style="padding: 8px; border: 1px solid #ddd;">ID LP Visual</th>
                <th style="padding: 8px; border: 1px solid #ddd;">LP Visual</th>
                <th style="padding: 8px; border: 1px solid #ddd;">Descripción LP Visual</th>

                <th style="padding: 8px; border: 1px solid #ddd;">ID LP Neto</th>
                <th style="padding: 8px; border: 1px solid #ddd;">LP Neto</th>
                <th style="padding: 8px; border: 1px solid #ddd;">Descripción LP Neto</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($parametrizados as $parametrizado)
                <tr>
                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $parametrizado->empresa->empresa }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $parametrizado->empresa->desc_empresa }}
                    </td>
                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $parametrizado->canal->canal }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $parametrizado->canal->desc_canal }}
                    </td>
                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $parametrizado->subcanal->subcanal }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $parametrizado->subcanal->desc_subcanal }}
                    </td>
                    <td style="padding: 8px; border: 1px solid #ddd;">
                        {{ $parametrizado->modelo_negocio->modelo_negocio }}
                    </td>
                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $parametrizado->bodega->bodega }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">
                        {{ $parametrizado->tipo_distribucion->tipo_distribucion }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">
                        {{ $parametrizado->idflexline_visual->idflexline_visual }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $parametrizado->lp_visual->lp_visual }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">
                        {{ $parametrizado->desc_lp_visual->desc_lp_visual }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">
                        {{ $parametrizado->idflexline_neto->idflexline_neto }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $parametrizado->lp_neto->lp_neto }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $parametrizado->desc_lp_neto->desc_lp_neto }}
                    </td>

                </tr>
            @endforeach
        </tbody>
    </table>
</div>

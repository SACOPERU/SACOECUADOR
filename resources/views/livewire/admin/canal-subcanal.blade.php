<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 text-gray-700">
    <div class="bg-white shadow-xl rounded-lg p-6">
        <form wire:submit.prevent="guardar" class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <!-- First Column -->
            <div class="mb-4">
                <x-jet-label value="Canal:" />
                <x-jet-input type="text" class="w-full" wire:model="canal" placeholder="Ingrese el Canal" />
                <x-jet-input-error for="canal" />
            </div>

            <div class="mb-4">
                <x-jet-label value="Descripción del Canal:" />
                <x-jet-input type="text" class="w-full" wire:model="desc_canal"
                    placeholder="Ingrese el Desc. Canal" />
                <x-jet-input-error for="desc_canal" />
            </div>

            <!-- Add more fields for the first column as needed -->

            <!-- Second Column -->
            <div class="mb-4">
                <x-jet-label value="Subcanal:" />
                <x-jet-input type="text" class="w-full" wire:model="subcanal" placeholder="Ingrese el SubCanal" />
                <x-jet-input-error for="subcanal" />
            </div>

            <div class="mb-4">
                <x-jet-label value="Desc. Subcanal:" />
                <x-jet-input type="text" class="w-full" wire:model="desc_subcanal"
                    placeholder="Ingrese el Desc. SubCanal" />
                <x-jet-input-error for="desc_subcanal" />
            </div>

            <!-- Add more fields for the second column as needed -->

            <!-- Third Column -->
            <div class="mb-4">
                <x-jet-label value="Modelo de Negocio:" />
                <x-jet-input type="text" class="w-full" wire:model="modelo_negocio"
                    placeholder="Ingrese Modelo de Negocio" />
                <x-jet-input-error for="modelo_negocio" />
            </div>

            <!-- Add more fields for the third column as needed -->

            <!-- Fourth Column -->
            <div class="mb-4">
                <x-jet-label value="Bodega:" />
                <x-jet-input type="text" class="w-full" wire:model="bodega" placeholder="Ingrese el Bodega" />
                <x-jet-input-error for="bodega" />
            </div>

            <!-- Add more fields for the fourth column as needed -->

            <!-- Fifth Column -->
            <div class="mb-4">
                <x-jet-label value="Tipo de Distribucion:" />
                <x-jet-input type="text" class="w-full" wire:model="tipo_distribucion"
                    placeholder="Tipo de Distribucion" />
                <x-jet-input-error for="tipo_distribucion" />
            </div>
            <br>
            <div class="  rounded-lg p-6">

                <div class="mb-4">
                    <x-jet-label value="ID flexline LP VISUAL:" />
                    <x-jet-input type="text" class="w-full" wire:model="idflexline_visual" placeholder="" />
                    <x-jet-input-error for="idflexline_visual" />
                </div>

                <div class="mb-4">
                    <x-jet-label value="Lista de Precios Visual:" />
                    <x-jet-input type="text" class="w-full" wire:model="lp_visual"
                        placeholder="Lista de Precios Visual" />
                    <x-jet-input-error for="lp_visual" />
                </div>


                <div class="mb-4">
                    <x-jet-label value="Descripción LP Visual:" />
                    <x-jet-input type="text" class="w-full" wire:model="desc_lp_visual"
                        placeholder="Desc. LP Visual" />
                    <x-jet-input-error for="desc_lp_visual" />
                </div>

            </div>

            <div class="  rounded-lg p-6">

                <div class="mb-4">
                    <x-jet-label value="ID flexline LP NETO:" />
                    <x-jet-input type="text" class="w-full" wire:model="idflexline_neto" placeholder="" />
                    <x-jet-input-error for="idflexline_neto" />
                </div>

                <div class="mb-4">
                    <x-jet-label value="Lista de Precios Neto:" />
                    <x-jet-input type="text" class="w-full" wire:model="lp_neto"
                        placeholder="Lista de Precios Neto" />
                    <x-jet-input-error for="lp_neto" />
                </div>


                <div class="mb-4">
                    <x-jet-label value="Descripción LP Neto:" />
                    <x-jet-input type="text" class="w-full" wire:model="desc_lp_neto"
                        placeholder="Desc. LP Visual" />
                    <x-jet-input-error for="desc_lp_neto" />
                </div>

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
            <tr>
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
            @foreach ($canalSubcanales as $canalSubcanal)
                <tr>
                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $canalSubcanal->canal }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $canalSubcanal->desc_canal }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $canalSubcanal->subcanal }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $canalSubcanal->desc_subcanal }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $canalSubcanal->modelo_negocio }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $canalSubcanal->bodega }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $canalSubcanal->tipo_distribucion }}</td>

                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $canalSubcanal->idflexline_visual }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $canalSubcanal->lp_visual }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $canalSubcanal->desc_lp_visual }}</td>

                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $canalSubcanal->idflexline_neto }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $canalSubcanal->lp_neto }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $canalSubcanal->desc_lp_neto }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

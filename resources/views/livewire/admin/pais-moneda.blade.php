<div>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 text-gray-700">
        <div class="bg-white shadow-xl rounded-lg p-6">
            <form wire:submit.prevent="guardar">

                <div class="bg-gray-200 shadow-xl rounded-lg p-6">
                    <div class="mb-4">
                        <x-jet-label value="Pais" />
                        <x-jet-input type="text" class="w-full" wire:model="pais" placeholder="Ingrese el Pais" />
                        <x-jet-input-error for="pais" />
                    </div>

                    <div class="mb-4">
                        <x-jet-label value="Desc. Pais" />
                        <x-jet-input type="text" class="w-full" wire:model="desc_pais"
                            placeholder="Ingrese Desc. Pais" />
                        <x-jet-input-error for="desc_pais" />
                    </div>

                </div>

                <br>

                <div class="bg-gray-200 shadow-xl rounded-lg p-6">


                    <div class="mb-4">
                        <x-jet-label value="Moneda" />
                        <select class="w-full form-control" wire:model="moneda">
                            <option value="PEN">PEN</option>
                            <option value="USD">USD</option>
                        </select>
                        <x-jet-input-error for="moneda" />
                    </div>

                    <div class="mb-4">
                        <x-jet-label value="Desc. Moneda" />
                        <x-jet-input type="text" class="w-full" wire:model="desc_moneda"
                            placeholder="Ingrese Desc. Moneda" />
                        <x-jet-input-error for="desc_moneda" />
                    </div>
                </div>

                <div class="flex mt-4">
                    <x-jet-button wire:loading.attr="disabled" wire:target="guardar" wire:click="guardar"
                        class="ml-auto">
                        Guardar
                    </x-jet-button>
                </div>

            </form>

            <br>

            <div>
                <h2>Lista de Países y Monedas:</h2>
                <table style="width:100%; border-collapse: collapse; margin-top: 10px;">
                    <thead>
                        <tr style="background-color: #f2f2f2;">
                            <th style="padding: 8px; border: 1px solid #ddd;">País</th>
                            <th style="padding: 8px; border: 1px solid #ddd;">Descripción del País</th>
                            <th style="padding: 8px; border: 1px solid #ddd;">Moneda</th>
                            <th style="padding: 8px; border: 1px solid #ddd;">Descripción de la Moneda</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($paisesMonedas as $paisMoneda)
                            <tr>
                                <td style="padding: 8px; border: 1px solid #ddd;">{{ $paisMoneda->pais }}</td>
                                <td style="padding: 8px; border: 1px solid #ddd;">{{ $paisMoneda->desc_pais }}</td>
                                <td style="padding: 8px; border: 1px solid #ddd;">{{ $paisMoneda->moneda }}</td>
                                <td style="padding: 8px; border: 1px solid #ddd;">{{ $paisMoneda->desc_moneda }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

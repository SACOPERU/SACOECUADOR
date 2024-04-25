<div>
    <div class="bg-white shadow-xl rounded-lg p-6 mb-4">
        <p class="text-2xl text-center font-semibold mb-2">Estado del Producto</p>

        <div class="flex">
            <label class="mr-6">
                <input wire:model="status" type="radio" value="1">
                BORRADOR
            </label>

            <label>
                <input wire:model="status" type="radio" value="2">
                PUBLICADO
            </label>
        </div>

        <div class="flex my-4">
            <label class="mr-6">
                <input wire:model="destacado" type="radio" value="1">
                NO DESTACADO
            </label>

            <label>
                <input wire:model="destacado" type="radio" value="2">
                DESTACADO
            </label>
        </div>

        <div class="flex my-4">
            <label class="mr-6">
                <input wire:model="product_principal" type="radio" value="1">
                NO PRODUCTO PRINCIPAL
            </label>

            <label>
                <input wire:model="product_principal" type="radio" value="2">
                PRODUCTO PRINCIPAL
            </label>
        </div>

        <div class="flex justify-end items-center">

            <x-jet-action-message class="mr-3" on="saved">
                Actulizado
            </x-jet-action-message>

            <x-jet-button wire:click="save" wire:loading.attr="disabled" wire:target="save">
                Actualizar
            </x-jet-button>

        </div>
    </div>
</div>

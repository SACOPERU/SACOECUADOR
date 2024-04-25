<div x-data>

        <div class="flex-1">
            <x-button-partner
                x-bind:disabled="$wire.qty > $wire.quantity"
                class="w-full text-xs"
                wire:click="addItem"
                wire:loading.attr="disabled"
                wire:target="addItem">
                Agregar
            </x-button-partner>
        </div>

</div>

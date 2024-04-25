<div x-data>

    <p class="text-blue-700 mb-4">
        <span class="font-semibold text-lg">Stock disponible:</span> {{$product->quantity_partner}}
        <span class="font-semibold text-lg">Stock disponible:</span> {{$quantity_partner}}
    </p>

    <div class="flex">
        <div class="mr-4">
            <x-jet-secondary-button
                disabled
                x-bind:disabled="$wire.qty <= 1"
                wire:loading.attr="disabled"
                wire:target="decrement"
                wire:click="decrement">
                -
            </x-jet-secondary-button>

            <span class="mx-2 text-gray-700">{{$qty}}</span>

            <x-jet-secondary-button
                x-bind:disabled="$wire.qty>= $wire.quantity_partner"
                wire:loading.attr="disabled"
                wire:target="increment"
                wire:click="increment">
                +
        </x-jet-secondary-button>
        </div>

        <div class="flex-1">
            <x-button-partner
                x-bind:disabled="$wire.qty>= $wire.quantity_partner"
                class="w-full"
                wire:click="addItem"
                wire:loading.attr="disable"
                wire:target="addItem">
                Agregar
            </x-button-partner>
        </div>
    </div>
</div>

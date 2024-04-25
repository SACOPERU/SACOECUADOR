<div x-data>

    <p class="text-gray-700 mb-4">
        <span class="font-semibold text-sm">Stock disponible: {{$product->quantity_partner}}</span>

    </p>

    <div class="flex items-center text-xs"> <!-- Aplicando la clase text-xs para reducir el tamaño del texto -->
        <div class="mr-2">
            <x-jet-secondary-button disabled x-bind:disabled="$wire.qty <= 1" wire:loading.attr="disabled"
                wire:target="decrement" wire:click="decrement" class="p-1">
                -
            </x-jet-secondary-button>
    
            <span class="text-sm text-gray-700">{{ $qty }}</span> <!-- Manteniendo el tamaño del texto de este span como text-sm -->
    
            <x-jet-secondary-button x-bind:disabled="$wire.qty >= $wire.quantity_partner" wire:loading.attr="disabled"
                wire:target="increment" wire:click="increment" class="p-1">
                +
            </x-jet-secondary-button>
        </div>
    
        <div class="flex-1">
            <x-button-partner x-bind:disabled="$wire.qty > $wire.quantity_partner" class="w-full text-xs p-1"
                wire:click="addItem"
                wire:loading.attr="disable"
                wire:target="addItem">
                Agregar
            </x-button-partner>
        </div>
    </div>
    
    
    
</div>

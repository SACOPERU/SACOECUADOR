<div class="container py-12">
    {{--Departamento --}}

    <x-jet-form-section submit="save" class="mb-6">

        <x-slot name="title">
            Nuevo departamento
        </x-slot>
        <x-slot name="description">
            Complete para un nuevo Departamento
        </x-slot>
        <x-slot name="form">
            <div class="col-span-6 sm:col-span-4 ">
                <x-jet-label>
                    Nombre
                </x-jet-label>

                <x-jet-input wire:model="createForm.name" type="text" class="w-full mt-1" />
                <x-jet-input-error for="createForm.name" />

            </div>
        </x-slot>
        <x-slot name="actions">

            <x-jet-action-message class="mr-3" on="saved">
                Departamento agregado
            </x-jet-action-message>

            <x-jet-button>
                Agregar
            </x-jet-button>

        </x-slot>

    </x-jet-form-section>

    {{--Mostrar Departamentos  --}}

    <x-jet-action-section>
        <x-slot name="title">
            Lista de Departamentos
        </x-slot>

        <x-slot name="description">
            Aqui encontrara todas los Departamentos agregados
        </x-slot>

        <x-slot name="content">

            <table class="text-gray-600">
                <thead class="border-b border-gray-300">
                    <tr class="text-left">
                        <th class="py-2 w-full">Nombre</th>
                        <th class="py-2">Accion</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-300">
                    @foreach ($departments as $department )
                        <tr>
                            <td class="py-2">
                                <a href="{{route('admin.departments.show', $department)}}" class="uppercase underline hover:text-yellow-500">
                                    {{$department->name}}
                                </a>
                            </td>
                            <td class="py-2">
                                <div class="flex divide-x divide-gray-300 font-semibold">
                                    <a  class="pr-2 hover:text-blue-600 cursor-pointer" wire:click="edit({{$department}})">Editar</a>
                                    <a  class="pl-2 hover:text-red-600 cursor-pointer" wire:click="">Eliminar</a>
                                </div>
                            </td>
                        </tr>

                    @endforeach
                </tbody>
            </table>

        </x-slot>
    </x-jet-action-section>

     {{--Modal Departamentos  --}}

    <x-jet-dialog-modal wire:model="editForm.open">

        <x-slot name="title">
            Editar Categoria
        </x-slot>

        <x-slot name="content">
            <div class="space-y-3">

                <div  >
                    <x-jet-label>
                        Nombre
                    </x-jet-label>

                    <x-jet-input wire:model="editForm.name" type="text" class="w-full mt-1" />
                    <x-jet-input-error for="editForm.name" />
                </div>

            </div>
        </x-slot>

        <x-slot name="footer">
            <x-jet-danger-button wire:click="update" wire:loading.attr="disabled" wire:target="editImage, update">
                Actualizar
            </x-jet-danger-button>
        </x-slot>


    </x-jet-dialog-modal>

</div>

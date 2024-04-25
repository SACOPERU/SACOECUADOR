<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight container">
           Usuarios
        </h2>
    </x-slot>

    <div class="container py-12">
        <x-table-responsive>

            <div class="px-6 py-4">
                <x-jet-input type="text" wire:model="search" class="w-full" placeholder="Escriba para filtrar usuarios">

                </x-jet-input>
            </div>

            @if (count($users))

            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                  <tr>
                    <th class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase bg-gray-50">
                      ID
                    </th>
                    <th class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase bg-gray-50">
                        Nombre
                      </th>
                    <th class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase bg-gray-50">
                      Email
                    </th>
                    <th class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase bg-gray-50">
                      Rol
                    </th>

                    <th scope="col" class="relative px-6 py-3">
                        <span  class="sr-only">Editar</span>
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">

                 @foreach ($users as $user )

                    <tr wire:key="{{$user->email}}">
                    <td class="px-6 py-4 whitespace-no-wrap">
                        <div class=" text-gray-900 ">
                                {{$user->id}}
                        </div>
                    </td>

                    <td class="px-6 py-4 whitespace-no-wrap">
                        <div class="text-sm leading-5 text-gray-900">
                            {{$user->name}}
                        </div>
                      </td>
                    <td class="px-6 py-4 whitespace-no-wrap">
                      <div class="text-sm leading-5 text-gray-900">
                        {{$user->email}}
                      </div>
                    </td>

                    <td class="px-6 py-4 text-sm leading-5 text-gray-500 whitespace-no-wrap">
                        <div class="text-sm leading-5 text-gray-900">
                            @if (count($user->roles))
                                Admin
                            @else
                                Sin Permiso
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm font-medium leading-5 text-right whitespace-no-wrap">
                        <label >
                            <input {{count($user->roles) ? 'checked' : ''}} value="1" type="radio" name="{{$user->email}}" wire:change="assingRole({{$user->id}}, $event.target.value)">
                            Si
                        </label>

                        <label class="ml-2">
                            <input {{count($user->roles) ? '' : 'checked'}} value="0" type="radio" name="{{$user->email}}" wire:change="assingRole({{$user->id}}, $event.target.value)">
                            No
                        </label>
                    </td>
                    </tr>

                 @endforeach
                  <!-- More rows... -->

                </tbody>
            </table>

            @else

            <div class="px-6 py-4">
                    Sin coincidencias
            </div>

            @endif

         @if ($users->hasPages())

            <div class="px-6 py-4">
                {{$users->links()}}
            </div>

         @endif
        </x-table-responsive>
    </div>
</div>

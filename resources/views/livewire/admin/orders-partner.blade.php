<div>

    <x-slot name="header">
        <div class="flex items-center container">
            <h2 class="font-semibold text-xl text-gray-700 leading-tigth">
                Pedidos de Partner
            </h2>

            <x-button-enlace class="ml-auto" href="{{route('admin.orderspartner.create')}}">
                Crear Pedido
            </x-button-enlace>
           </div>
    </x-slot>

    <div class="container py-12">
        <x-table-responsive>

            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                  <tr>
                    <th class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase bg-gray-50">
                        Codigo de Pedido
                    </th>
                    <th class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase bg-gray-50">
                        Nombre de Cliente
                      </th>
                    <th class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase bg-gray-50">
                        Precio
                    </th>
                    <th class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase bg-gray-50">
                        Estado
                    </th>
                    <th class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase bg-gray-50">
                        Documentos
                      </th>
                    <th class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase bg-gray-50">
                        Acciones
                    </th>
                    <th class="px-6 py-3 bg-gray-50"></th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">

                </tbody>
            </table>

        </x-table-responsive>
    </div>


</div>

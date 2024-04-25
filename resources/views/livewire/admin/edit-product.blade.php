    <div>
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center">
                    <h1 class="font-semibolt text-xl text-gray-800 leading-tight">
                        Productos
                    </h1>
                    <x-jet-danger-button wire:click="$emit('deleteProduct')">
                        Eliminar
                    </x-jet-danger-button>

                </div>
            </div>
        </header>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-3 text-gray-700">
            <h1 class="text-3xl text-center font-semibold mb-8">Complete esta informacion para crear un producto</h1>

            <div class="mb-4" wire:ignore>
                <form action="{{ route('admin.products.files', $product) }}" method="POST" class="dropzone"
                    id="my-awesome-dropzone">
                </form>
            </div>

            @if ($product->images->count())
                <section class="bg-white shadow-xl rounded-lg p-6 mb-4">
                    <h1 class="text-2xl text-center font-semibold mb-2">Imagenes del producto</h1>

                    <ul class="flex flex-wrap">
                        @foreach ($product->images as $image)
                            <li class="relative" wire:key="image-{{ $image->id }}">
                                <img class="w-32 h-20 object-cover" src="{{ Storage::url($image->url) }}"
                                    alt="">
                                <x-jet-danger-button class="absolute right-2 top-2"
                                    wire:click="deleteImage({{ $image->id }})" wire:loading.attr="disabled"
                                    wire:target="deleteImage({{ $image->id }})">
                                    x
                                </x-jet-danger-button>
                            </li>
                        @endforeach
                    </ul>
                </section>

            @endif

            @livewire('admin.status-product', ['product' => $product], key('status-product-' . $product->id))

            <div class="bg-white shadow-xl rounded-lg p-6">
                <div class="grid grid-cols-2 gap-6 mb-4">
                    {{-- Categoria --}}

                    <div>
                        <x-jet-label value="Categorias" />
                        <select class="w-full form-control" wire:model="category_id">
                            <option value="" selected disabled>seleccione una categoria</option>

                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach

                        </select>
                        <x-jet-input-error for="category_id" />


                    </div>
                    {{-- Subcategoria --}}
                    <div>
                        <x-jet-label value="Subcategorías" />
                        <select class="w-full form-control" wire:model="product.subcategory_id">
                            <option value="" selected disabled>seleccione una subcategorías</option>

                            @foreach ($subcategories as $subcategory)
                                <option value="{{ $subcategory->id }}">{{ $subcategory->name }}</option>
                            @endforeach

                        </select>
                        <x-jet-input-error for="product.subcategory_id" />
                    </div>

                    <div>
                        <x-jet-label value="Subfamilia" />
                        <select class="w-full form-control" wire:model="product.subfamilia">
                            <option value="" selected disabled>Seleccione una Subfamilia</option>
                            <option value="AFEITADORA">AFEITADORA</option>
                            <option value="ALIMENTACION PARA MASCOTAS">ALIMENTACION PARA MASCOTAS</option>
                            <option value="ASPIRADORA">ASPIRADORA</option>
                            <option value="AUDIFONOS">AUDIFONOS</option>
                            <option value="BALANZA DIGITAL">BALANZA DIGITAL</option>
                            <option value="BANDA">BANDA</option>
                            <option value="BATERIA EXTERNA">BATERIA EXTERNA</option>
                            <option value="CABLE">CABLE</option>
                            <option value="CAMARA">CAMARA</option>
                            <option value="CANGURO">CANGURO</option>
                            <option value="CARGADOR">CARGADOR</option>
                            <option value="CARGADOR DE CARRO">CARGADOR DE CARRO</option>
                            <option value="CELULARES">CELULARES</option>
                            <option value="CORREA">CORREA</option>
                            <option value="DESARMADOR">DESARMADOR</option>
                            <option value="FOCO">FOCO</option>
                            <option value="FREIDORA DE AIRE">FREIDORA DE AIRE</option>
                            <option value="HERVIDOR DE AGUA">HERVIDOR DE AGUA</option>
                            <option value="LAMPARA">LAMPARA</option>
                            <option value="LAPIZ INTELIGENTE">LAPIZ INTELIGENTE</option>
                            <option value="MOCHILA">MOCHILA</option>
                            <option value="PARLANTE BLUETOOTH">PARLANTE BLUETOOTH</option>
                            <option value="PILA">PILA</option>
                            <option value="REPRODUCTOR STREAMIN">REPRODUCTOR STREAMIN</option>
                            <option value="ROUTER">ROUTER</option>
                            <option value="SCOOTER">SCOOTER</option>
                            <option value="SECADORA DE CABELLO">SECADORA DE CABELLO</option>
                            <option value="SMARTWATCH">SMARTWATCH</option>
                            <option value="TABLET">TABLET</option>
                            <option value="TECLADO">TECLADO</option>
                        </select>
                        <x-jet-input-error for="product.subfamilia" />

                    </div>

                </div>

                <div class="mb-4">
                    <x-jet-label value="Nombre" />
                    <x-jet-input type="text" class="w-full" wire:model="product.name"
                        placeholder="Ingrese nombre del producto" />
                    <x-jet-input-error for="product.name" />
                </div>

                <div class="mb-4">
                    <x-jet-label value="Codigo SACO" />
                    <x-jet-input type="text" class="w-full bg-gray-200" disabled wire:model="product.sku"
                        placeholder="Ingrese SKU" />
                    <x-jet-input-error for="product.sku" />
                </div>


                <div class="mb-4">
                    <x-jet-label value="Slug" />
                    <x-jet-input type="text" class="w-full bg-gray-200" disabled wire:model="slug"
                        placeholder="Ingrese el slug del producto" />
                    <x-jet-input-error for="slug" />
                </div>

                <div class="mb-4">

                    <div wire:ignore>
                        <x-jet-label value="Descripcion" />

                        <textarea class="w-full form-control" name="" id="" cols="30" rows="4"
                            wire:model="product.description" x-data x-init="ClassicEditor
                                .create($refs.miEditor)
                                .then(function(editor) {
                                    editor.model.document.on('change:data', () => {
                                        @this.set('product.description', editor.getData())
                                    })
                                })
                                .catch(error => {
                                    console.error(error);
                                });" x-ref="miEditor">
                            </textarea>
                    </div>
                    <x-jet-input-error for="product.description" />
                </div>


                <div class="grid grid-cols-2 gap-6 mb-4">
                    <div>
                        <x-jet-label value="Marca" />
                        <select class="form-control w-full" wire:model="product.brand_id">
                            <option value="" selected disabled>Selecione una Marca</option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                            @endforeach
                        </select>
                        <x-jet-input-error for="product.brand_id" />
                    </div>


                    <div class="mb-4">
                        <x-jet-label value="Precio" />
                        <x-jet-input type="text" class="w-full bg-gray-200" disabled wire:model="product.price" />
                        <x-jet-input-error for="product.price" />
                    </div>

                    <div>
                        <x-jet-label value="Precio Tachado" />
                        <x-jet-input wire:model="product.price_tachado" type="number" class="w-full"
                            step=".001" />
                        <x-jet-input-error for="product.price_tachado" />
                    </div>
                    {{-- <div>
                    <x-jet-label value="Precio Partner"/>
                    <x-jet-input
                    wire:model="product.price_partner"
                    type="number"
                    class="w-full"
                    step=".001" />
                    <x-jet-input-error for="product.price_partner" />
                    </div>
                --}}

                    @if ($this->subcategory)

                        @if (!$this->subcategory->color && !$this->subcategory->size)
                            <div class="mb-4">
                                <x-jet-label value="Stock Total" />
                                <x-jet-input type="text" class="w-full bg-gray-200" disabled
                                    wire:model="product.quantity" />
                                <x-jet-input-error for="product.quantity" />
                            </div>

                            {{-- <div>
                                        <x-jet-label value="Cantidad Partner"/>
                                        <x-jet-input
                                        wire:model="product.quantity_partner"
                                        type="number"
                                        class="w-full" />
                                        <x-jet-input-error for="product.quantity_partner" />
                                        </div>
                                     --}}


                            {{-- Bodegas de Tienda Xiaomi --}}

                            <div class="mb-4">
                                <x-jet-label value="03-LIM-ATOCONG-MISTR" />
                                <x-jet-input type="text" class="w-full bg-gray-200" disabled
                                    wire:model="product.atocong" />
                                <x-jet-input-error for="product.atocong" />
                            </div>

                            <div class="mb-4">
                                <x-jet-label value="03-LIM-JOCKEYPZ-MIST" />
                                <x-jet-input type="text" class="w-full bg-gray-200" disabled
                                    wire:model="product.jockeypz" />
                                <x-jet-input-error for="product.jockeypz" />
                            </div>

                            <div class="mb-4">
                                <x-jet-label value="03-LIM-MEGAPLZ-MISTR" />
                                <x-jet-input type="text" class="w-full bg-gray-200" disabled
                                    wire:model="product.megaplz" />
                                <x-jet-input-error for="product.megaplz" />
                            </div>

                            <div class="mb-4">
                                <x-jet-label value="03-LIM-HUAYLAS-MISTR" />
                                <x-jet-input type="text" class="w-full bg-gray-200" disabled
                                    wire:model="product.huaylas" />
                                <x-jet-input-error for="product.huaylas" />
                            </div>

                            <div class="mb-4">
                                <x-jet-label value="03-LIM-PURUCHU-MISTR" />
                                <x-jet-input type="text" class="w-full bg-gray-200" disabled
                                    wire:model="product.puruchu" />
                                <x-jet-input-error for="product.puruchu" />
                            </div>

                            <div class="mb-4">
                                <x-jet-label value="03-LIM-BSF-WEBSACO" />
                                <x-jet-input type="text" class="w-full bg-gray-200" disabled
                                    wire:model="product.principal" />
                                <x-jet-input-error for="product.principal" />
                            </div>
                        @endif

                    @endif

                    <br>
                    <div class="flex justify-end items-center mt-4">

                        <x-jet-action-message class="mr-3" on="saved">
                            Actualizado
                        </x-jet-action-message>

                        <x-jet-button wire:loading.attr="disabled" wire:target="save" wire:click="save">
                            Actualizar Producto
                        </x-jet-button>
                    </div>
                </div>
            </div>

            @if ($this->subcategory)

                @if ($this->subcategory->size)
                    @livewire('admin.size-product', ['product' => $product], key('size-product' . $product->id))
                @elseif ($this->subcategory->color)
                    @livewire('admin.color-product', ['product' => $product], key('color-product' . $product->id))
                @endif

            @endif

        </div>

        @push('script')
            <script>
                Dropzone.options.myAwesomeDropzone = {
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    dictDefaultMessage: "Arrastre una imagen al recuadro",
                    acceptedFiles: 'image/*',
                    paramName: "file", // The name that will be used to transfer the file
                    maxFilesize: 2, // MB
                    complete: function(file) {
                        this.removeFile(file);

                    },
                    queuecomplete: function() {
                        Livewire.emit('refreshProduct')
                    }

                };

                livewire.on('deleteProduct', () => {
                    Swal.fire({
                        title: '¿Estas seguro?',
                        text: "¡No podrás revertir esto!",
                        icon: 'Advertencia',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: '¡Sí, bórralo!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            livewire.emitTo('admin.edit-product', 'delete');
                            Swal.fire(
                                '¡Eliminado!',
                                'Ha sido eliminado de manera correcta.',
                                'éxito'
                            )
                        }
                    })
                })

                livewire.on('deleteSize', sizeId => {
                    Swal.fire({
                        title: '¿Estas seguro?',
                        text: "¡No podrás revertir esto!",
                        icon: 'Advertencia',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: '¡Sí, bórralo!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            livewire.emitTo('admin.size-product', 'delete', sizeId);
                            Swal.fire(
                                '¡Eliminado!',
                                'Ha sido eliminado de manera correcta.',
                                'éxito'
                            )
                        }
                    })
                })

                Livewire.on('deletePivot', pivot => {
                    Swal.fire({
                        title: '¿Estas seguro?',
                        text: "¡No podrás revertir esto!",
                        icon: 'Advertencia',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: '¡Sí, bórralo!'
                    }).then((result) => {
                        if (result.isConfirmed) {

                            Livewire.emitTo('admin.color-product', 'delete', pivot)

                            Swal.fire(
                                '¡Eliminado!',
                                'Ha sido eliminado de manera correcta.',
                                'éxito'
                            )
                        }
                    })
                })

                Livewire.on('deleteColorSize', pivot => {
                    Swal.fire({
                        title: '¿Estas seguro?',
                        text: "¡No podrás revertir esto!",
                        icon: 'Advertencia',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: '¡Sí, bórralo!'
                    }).then((result) => {
                        if (result.isConfirmed) {

                            Livewire.emitTo('admin.color-size', 'delete', pivot)

                            Swal.fire(
                                '¡Eliminado!',
                                'Ha sido eliminado de manera correcta.',
                                'éxito'
                            )
                        }
                    })
                })
            </script>
        @endpush

    </div>

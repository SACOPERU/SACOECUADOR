<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos por Marca</title>
    <style>
        .custom-image {
            width: 100%;
            /* Ancho del 100% en dispositivos móviles */
            height: auto;
            /* Altura automática para mantener la proporción */
            object-fit: cover;
            /* Para cubrir el área especificada sin distorsionar */
        }

        @media (min-width: 640px) {
            .custom-image {
                width: 300px;
                /* Ancho deseado para tamaños de pantalla más grandes */
                height: 200px;
                /* Alto deseado */
            }
        }

        /* Estilo para cambiar el color de las marcas a azul al pasar el mouse */
        .brand-item:hover {
            color: rgb(67, 105, 254);
            cursor: pointer;
        }
    </style>
</head>

<body>

    <div class="py-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
        <aside>
            <h2 class="font-semibold text-center mt-4 mb-2">Marcas</h2>
            <ul class="divide-y divide-green-500" id="brands-list">
                @php
                    $previousBrandName = null;
                @endphp

                @foreach ($products as $product)
                    @if ($product->brand->name !== $previousBrandName)
                        <p data-brand="{{ $product->brand->name }}" class="font-bold text-xs text-gray-800 brand-item">
                            {{ $product->brand->name }}</p>
                        @php
                            $previousBrandName = $product->brand->name;
                        @endphp
                    @endif
                @endforeach
            </ul>
            <!-- Botón para mostrar todos los productos -->
            <x-button-partner  class="mt-4" onclick="resetearPagina()">
                Eliminar Filtros
            </x-button-partner >

            <script>
                function resetearPagina() {
                    location.reload();
                }
            </script>

        </aside>

        <div class="md:grid-cols-2 lg:col-span-4">
            <ul class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 rounded-lg p-4" id="products-container">
                @forelse ($products as $product)
                    <li class="bg-white rounded-lg shadow border border-blue-500"
                        data-brand="{{ $product->brand->name }}">
                        <section>
                            <article>
                                <figure class="relative">
                                    <img class="custom-image" src="{{ Storage::url($product->images->first()->url) }}"
                                        alt="Cargar Imagen para Producto.">
                                </figure>
                            </article>
                        </section>

                        <section>
                            <div class="py-2 px-6">
                                <h1 class="text-xs font-semibold">
                                    <a>
                                        {{ Str::limit($product->name, 50) }}
                                    </a>
                                    <p class="text-xs text-green-500">{{ $product->sku }}</p>
                                </h1>
                            </div>
                        </section>

                        <section>
                            <p class="font-bold text-blue-500" style="text-align: center;">S/
                                {{ $product->price_partner }}.00</p>
                        </section>

                        <!-- Botón de agregar al carrito -->
                        <section style="text-align: center;">
                            <div class="text-sm mt-4" style="font-size: 10px; display: inline-block;"
                                id="add-to-cart-{{ $product->id }}">
                                @livewire('add-cart-item-partner', ['product' => $product])
                            </div>
                        </section>

                    </li>
                @empty
                    <li class="md:col-span-2 lg:col-span-4">
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative"
                            role="alert">
                            <strong class="font-bold">Upss.!!</strong>
                            <span class="block sm:inline">No hay registro con ese filtro</span>
                        </div>
                    </li>
                @endforelse
            </ul>

            <div class="mt-4">
                {{ $products->links() }}
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const brandItems = document.querySelectorAll(".brand-item");

            brandItems.forEach(function(brandItem) {
                brandItem.addEventListener("click", function() {
                    const brandName = this.dataset.brand;
                    showProductsByBrand(brandName);
                });
            });

            function showProductsByBrand(brandName) {
                const productsContainer = document.getElementById("products-container");
                const productItems = productsContainer.querySelectorAll("li");

                productItems.forEach(function(productItem) {
                    if (productItem.dataset.brand === brandName) {
                        productItem.style.display = "block";
                    } else {
                        productItem.style.display = "none";
                    }
                });
            }

            // Función para mostrar todos los productos y reiniciar la página
            function showAllProducts() {
                window.location.reload();
            }
        });
    </script>

</body>

</html>

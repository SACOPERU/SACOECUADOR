<x-app-layout>

    @push('script')
        <script>
            $(document).ready(function() {
                $('.flexslider').flexslider({
                    animation: "slide",
                    controlsContainer: $(".custom-controls-container"),
                    customDirectionNav: $(".custom-navigation a")
                });

                Livewire.on('glider', function(id) {
                    new Glider(document.querySelector('.glider-' + id), {
                        slidesToShow: 1,
                        slidesToScroll: 1,
                        draggable: true,
                        dots: '.glider-' + id + '~ .dots',
                        arrows: {
                            prev: '.glider-' + id + '~ .glider-prev',
                            next: '.glider-' + id + '~ .glider-next'
                        },
                        responsive: [{
                                breakpoint: 640,
                                settings: {
                                    slidesToShow: 2.5,
                                    slidesToScroll: 2,
                                }
                            },
                            {
                                breakpoint: 768,
                                settings: {
                                    slidesToShow: 3.5,
                                    slidesToScroll: 3,
                                }
                            },
                            {
                                breakpoint: 1024,
                                settings: {
                                    slidesToShow: 4.5,
                                    slidesToScroll: 4,
                                }
                            },
                            {
                                breakpoint: 1280,
                                settings: {
                                    slidesToShow: 5.5,
                                    slidesToScroll: 5,
                                }
                            },
                        ]
                    });
                });

            });
        </script>
    @endpush

    <section class="my-2 container">
        <div>

            @php
                $detect = new Mobile_Detect();
                $isMobile = $detect->isMobile();
            @endphp

            <div id="default-carousel" class="relative w-full" data-carousel="slide">
                <!-- Carousel wrapper -->
                <div class="relative h-56 overflow-hidden rounded-lg md:h-96">
                    @foreach ($banners as $index => $banner)
                        @if ($isMobile && Str::contains(strtolower($banner->name), 'movil'))
                            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                <img src="{{ asset('storage/' . $banner->image) }}"
                                    class="absolute block w-full h-full object-cover" alt="...">
                            </div>
                        @elseif (!$isMobile && Str::contains(strtolower($banner->name), 'banners'))
                            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                <img src="{{ asset('storage/' . $banner->image) }}"
                                    class="absolute block w-full h-full object-cover" alt="...">
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <script>
                // Espera a que el DOM esté completamente cargado
                document.addEventListener("DOMContentLoaded", function() {
                    // Selecciona el carrusel
                    var carousel = document.querySelector('[data-carousel="slide"]');
                    // Selecciona todas las imágenes dentro del carrusel
                    var images = carousel.querySelectorAll('img');
                    // Inicializa el índice de la imagen actual
                    var currentIndex = 0;

                    // Función para cambiar la imagen
                    function changeImage() {
                        // Oculta todas las imágenes
                        images.forEach(function(image) {
                            image.style.display = 'none';
                        });
                        // Muestra la siguiente imagen
                        images[currentIndex].style.display = 'block';
                        // Incrementa el índice de la imagen actual
                        currentIndex = (currentIndex + 1) % images.length;
                    }

                    // Cambia la imagen cada 10 segundos
                    setInterval(changeImage, 10000);
                });
            </script>

        </div>
    </section>

    <section class="container bg-gray-100 my-8">
        <h2
            class="text-center mb-4 text-4xl font-extrabold leading-none tracking-tight text-gray-900 md:text-5xl lg:text-6xl">
            PRODUCTOS DESTACADOS
        </h2>
        <div>
            @foreach ($categories as $category)
                <section class="mb-7">
                    @livewire('category-principal', ['category' => $category])
                </section>
            @endforeach
        </div>
    </section>
</x-app-layout>

@livewire('footer')

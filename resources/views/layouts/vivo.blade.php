<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

        <!-- Styles -->
          {{-- Dropzone --}}
          <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css" integrity="sha512-jU/7UFiaW5UBGODEopEqnbIAHOI8fO6T99m7Tsmqs2gkdujByJfkCbbfPSN4Wlqlb9TGnsuC0YgUgWkRBK7B9A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

        @livewireStyles
        <!-- Scripts -->
        {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}

        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css')}}">




         {{-- Glider --}}
         <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/glider-js/1.7.7/glider.min.css" integrity="sha512-YM6sLXVMZqkCspZoZeIPGXrhD9wxlxEF7MzniuvegURqrTGV2xTfqq1v9FJnczH+5OGFl5V78RgHZGaK34ylVg==" crossorigin="anonymous" />

        {{-- Dropzone --}}
        <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.js" integrity="sha512-U2WE1ktpMTuRBPoCFDzomoIorbOyUv0sP8B+INA3EzNAhehbzED1rOJg6bCqPf/Tuposxb5ja/MAUnC8THSbLQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>


         {{-- FlexSlider --}}
        <link rel="stylesheet" href="{{ asset('vendor/Flex Slider/flexslider.css')}}">

        @livewireStyles
        {{-- Scripts --}}
        <script src="{{ asset('js/app.js') }}" defer></script>


        @stack('head')

        {{-- Glider --}}
        <script src="https://cdn.jsdelivr.net/npm/glider-js@1/glider.min.js"></script>
      	<script src="https://cdn.jsdelivr.net/jsbarcode/3.12.0/JsBarcode.all.min.js"></script>  
      <script src="https://cdnjs.cloudflare.com/ajax/libs/glider-js/1.7.7/glider.min.js" integrity="sha512-tHimK/KZS+o34ZpPNOvb/bTHZb6ocWFXCtdGqAlWYUcz+BGHbNbHMKvEHUyFxgJhQcEO87yg5YqaJvyQgAEEtA==" crossorigin="anonymous"></script>


        <script src="https://code.jquery.com/jquery-3.6.3.min.js"></script>



          {{-- FlexSlider --}}
        <script src="{{ asset('vendor/Flex Slider/jquery.flexslider-min.js')}}"></script>


    </head>
    <body class="font-sans antialiased">
        <x-jet-banner />

        <div class="min-h-screen bg-gray-100">
            @livewire('vivo')

            <!-- Page Heading -->

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        @stack('modals')

        @livewireScripts

        <script>

            function dropdown(){
                return{
                    open: false,
                    show(){
                        if(this.open){
                            //se cierra el menu
                            this.open= false;
                            document.getElementsByTagName('html')[0].style.overflow = 'auto'
                        }else{
                            //se abre menu
                            this.open= true;
                            document.getElementsByTagName('html')[0].style.overflow = 'hidden'
                        }
                    },
                    close(){
                        this.open= false;
                            document.getElementsByTagName('html')[0].style.overflow = 'auto'
                    }
                }
            }

        </script>

        @stack('script')
        @stack('script2')

    </body>
</html>

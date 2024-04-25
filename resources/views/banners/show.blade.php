<x-app-layout>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <figure class="mb-4">
            <img class="w-full h-80 object-cover object-center" src="{{Storage::url($banner->image)}}" alt="">
        </figure>

        @livewire('banners', ['banner'=> $banner])
    </div>

</x-app-layout>

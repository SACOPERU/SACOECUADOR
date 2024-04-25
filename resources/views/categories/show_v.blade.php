<x-vivo-layout>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <figure class="mb-4">
            <img class="w-full sm:h-80 md:h-60 lg:h-80 xl:h-80 object-cover object-center" src="{{ Storage::url($category->image) }}" alt="">
        </figure>

        @livewire('category-filter', ['category' => $category])
    </div>

</x-vivo-layout>
@livewire('footer')

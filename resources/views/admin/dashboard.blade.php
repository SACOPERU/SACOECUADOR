<x-admin-layout>
    <div class="grid grid-cols-2 gap-6">

        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center">
                <img class="h-8 w-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />

                <div class="ml-4 flex-1">
                    <h2>Bienvenido {{auth()->user()->name}}</h2>
                </div>
            </div>

        </div>

        <div class="bg-white rounded-lg shadow-lg p-6">

        </div>

    </div>
</x-admin-layout>

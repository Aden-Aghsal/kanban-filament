<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Profil Saya</h2>
    </x-slot>

    <div class="max-w-xl mx-auto mt-6 bg-white p-6 rounded shadow space-y-4">
        <div>
            <p class="text-gray-500">Nama</p>
            <p class="font-semibold">{{ $user->name }}</p>
        </div>

        <div>
            <p class="text-gray-500">Email</p>
            <p class="font-semibold">{{ $user->email }}</p>
        </div>

        <a href="{{ route('profile.edit') }}"
           class="inline-block mt-4 px-4 py-2 bg-blue-600 text-white rounded">
            Edit Profil
        </a>
    </div>
</x-app-layout>

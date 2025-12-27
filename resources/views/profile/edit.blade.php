<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Edit Profil</h2>
    </x-slot>

    <div class="max-w-xl mx-auto mt-6 bg-white p-6 rounded shadow">
        <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <label class="block text-sm">Nama</label>
                <input type="text"
                       name="name"
                       value="{{ old('name', $user->name) }}"
                       class="w-full border rounded px-3 py-2">
            </div>

            <div>
                <label class="block text-sm">Email</label>
                <input type="email"
                       value="{{ $user->email }}"
                       disabled
                       class="w-full border rounded px-3 py-2 bg-gray-100">
            </div>

            <div class="flex justify-end gap-2">
                <a href="{{ route('profile.show') }}"
                   class="px-4 py-2 border rounded">
                    Batal
                </a>

                <button class="px-4 py-2 bg-blue-600 text-white rounded">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</x-app-layout>

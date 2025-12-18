<x-filament-panels::page.simple>
    <form wire:submit.prevent="authenticate">
        {{ $this->form }}

        <x-filament::button type="submit" class="w-full mt-4">
            Login
        </x-filament::button>
    </form>

    <div class="mt-4 text-center">
        <a
            href="{{ route('google.login') }}"
            class="inline-flex items-center justify-center w-full px-4 py-2 mt-2 text-white bg-red-500 rounded-lg"
        >
            Login dengan Google
        </a>
    </div>
</x-filament-panels::page.simple>

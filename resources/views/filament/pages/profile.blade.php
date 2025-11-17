<x-filament::page>
    <form wire:submit.prevent="submit" class="space-y-6">
        {{ $this->form }}
        <x-filament::button type="submit" color="primary">
            Simpan Perubahan
        </x-filament::button>
    </form>
</x-filament::page>
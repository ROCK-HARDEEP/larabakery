<x-filament-panels::page>
    {{-- Add navigation breadcrumb --}}
    <x-slot name="breadcrumbs">
        {{ \Filament\Facades\Filament::renderHook('panels::resource.pages.list-records.breadcrumbs') }}
    </x-slot>
    
    {{ $this->table }}
</x-filament-panels::page>
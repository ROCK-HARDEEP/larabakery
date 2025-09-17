@php
    $record = $getRecord();
    $variants = $record ? $record->variants : collect();
    $activeCount = $variants->where('is_active', true)->count();
    $totalCount = $variants->count();
@endphp

<div class="space-y-4">
    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Product Variants</h3>
                        <p class="text-sm text-gray-500 mt-1">Manage different variations of this product</p>
                    </div>
                </div>
                
                <div class="mt-4 grid grid-cols-2 gap-4">
                    <div class="bg-gray-50 rounded-lg px-4 py-3">
                        <p class="text-sm text-gray-600">Total Variants</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalCount }}</p>
                    </div>
                    <div class="bg-green-50 rounded-lg px-4 py-3">
                        <p class="text-sm text-green-600">Active Variants</p>
                        <p class="text-2xl font-bold text-green-900">{{ $activeCount }}</p>
                    </div>
                </div>

                @if($totalCount > 0)
                <div class="mt-4">
                    <p class="text-sm font-medium text-gray-700 mb-2">Current Variants:</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($variants->take(5) as $variant)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $variant->is_active ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-600' }}">
                            {{ $variant->variant_value }} ({{ $variant->variant_type }})
                        </span>
                        @endforeach
                        @if($totalCount > 5)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                            +{{ $totalCount - 5 }} more
                        </span>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>

        @if($record && $record->exists)
        <div class="mt-6 pt-6 border-t border-gray-200">
            <x-filament::button
                wire:click="mountRelationManager('variants')"
                size="lg"
                icon="heroicon-o-cog-6-tooth"
                class="w-full"
            >
                Manage Variants
            </x-filament::button>
        </div>
        @else
        <div class="mt-6 pt-6 border-t border-gray-200">
            <p class="text-sm text-gray-500 text-center">Save the product first to manage variants</p>
        </div>
        @endif
    </div>
</div>
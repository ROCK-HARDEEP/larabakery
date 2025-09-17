<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-heroicon-o-squares-2x2 class="w-5 h-5 text-primary-500" />
                <span>Manage Product Variants</span>
            </div>
        </x-slot>

        <x-slot name="description">
            Add and manage different variations of this product (sizes, colors, etc.)
        </x-slot>

        @php
            $variants = $record ? $record->variants : collect();
            $activeCount = $variants->where('is_active', true)->count();
            $totalCount = $variants->count();
        @endphp

        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <x-filament::stats.card
                    label="Total Variants"
                    value="{{ $totalCount }}"
                    icon="heroicon-o-squares-2x2"
                    color="gray"
                />
                <x-filament::stats.card
                    label="Active Variants"
                    value="{{ $activeCount }}"
                    icon="heroicon-o-check-circle"
                    color="success"
                />
            </div>

            @if($totalCount > 0)
                <div class="space-y-2">
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Current Variants:</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($variants->take(8) as $variant)
                            <x-filament::badge
                                :color="$variant->is_active ? 'success' : 'gray'"
                            >
                                {{ $variant->name ?? $variant->variant_value }}
                                @if($variant->price)
                                    (₹{{ number_format($variant->price, 0) }})
                                @endif
                            </x-filament::badge>
                        @endforeach
                        @if($totalCount > 8)
                            <x-filament::badge color="gray">
                                +{{ $totalCount - 8 }} more
                            </x-filament::badge>
                        @endif
                    </div>
                </div>
            @else
                <div class="text-center py-6">
                    <x-heroicon-o-inbox class="mx-auto h-12 w-12 text-gray-400" />
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">No variants added yet</p>
                </div>
            @endif

            <div class="pt-4 border-t">
                @if($record && $record->exists)
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        <x-heroicon-o-information-circle class="inline w-4 h-4 mr-1" />
                        Use the Variants tab above to manage product variants
                    </p>
                @else
                    <x-filament::badge color="warning" icon="heroicon-o-exclamation-triangle">
                        Save the product first to manage variants
                    </x-filament::badge>
                @endif
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
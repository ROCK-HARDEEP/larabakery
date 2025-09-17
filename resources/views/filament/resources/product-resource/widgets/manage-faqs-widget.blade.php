<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-heroicon-o-question-mark-circle class="w-5 h-5 text-primary-500" />
                <span>Manage Product FAQs</span>
            </div>
        </x-slot>

        <x-slot name="description">
            Add frequently asked questions and answers for this product
        </x-slot>

        @php
            $faqs = $record ? $record->faqs : collect();
            $activeCount = $faqs->where('is_active', true)->count();
            $totalCount = $faqs->count();
        @endphp

        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <x-filament::stats.card
                    label="Total FAQs"
                    value="{{ $totalCount }}"
                    icon="heroicon-o-question-mark-circle"
                    color="gray"
                />
                <x-filament::stats.card
                    label="Active FAQs"
                    value="{{ $activeCount }}"
                    icon="heroicon-o-check-circle"
                    color="success"
                />
            </div>

            @if($totalCount > 0)
                <div class="space-y-3">
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Recent FAQs:</p>
                    <div class="space-y-2">
                        @foreach($faqs->take(3) as $faq)
                            <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                <div class="flex items-start gap-2">
                                    <x-heroicon-o-question-mark-circle class="w-4 h-4 text-primary-500 mt-0.5 flex-shrink-0" />
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                            {{ $faq->question }}
                                        </p>
                                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1 line-clamp-2">
                                            {{ Str::limit($faq->answer, 100) }}
                                        </p>
                                    </div>
                                    <x-filament::badge
                                        :color="$faq->is_active ? 'success' : 'gray'"
                                        size="xs"
                                    >
                                        {{ $faq->is_active ? 'Active' : 'Inactive' }}
                                    </x-filament::badge>
                                </div>
                            </div>
                        @endforeach
                        @if($totalCount > 3)
                            <p class="text-xs text-gray-500 dark:text-gray-400 text-center">
                                And {{ $totalCount - 3 }} more FAQ{{ $totalCount - 3 > 1 ? 's' : '' }}...
                            </p>
                        @endif
                    </div>
                </div>
            @else
                <div class="text-center py-6">
                    <x-heroicon-o-question-mark-circle class="mx-auto h-12 w-12 text-gray-400" />
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">No FAQs added yet</p>
                    <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                        Add FAQs to help customers understand your product better
                    </p>
                </div>
            @endif

            <div class="pt-4 border-t">
                @if($record && $record->exists)
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        <x-heroicon-o-information-circle class="inline w-4 h-4 mr-1" />
                        Use the FAQs tab above to manage product FAQs
                    </p>
                @else
                    <x-filament::badge color="warning" icon="heroicon-o-exclamation-triangle">
                        Save the product first to manage FAQs
                    </x-filament::badge>
                @endif
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
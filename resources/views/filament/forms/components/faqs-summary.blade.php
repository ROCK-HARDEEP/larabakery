@php
    $record = $getRecord();
    $faqs = $record ? $record->faqs : collect();
    $activeCount = $faqs->where('is_active', true)->count();
    $totalCount = $faqs->count();
@endphp

<div class="space-y-4">
    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Frequently Asked Questions</h3>
                        <p class="text-sm text-gray-500 mt-1">Manage FAQs specific to this product</p>
                    </div>
                </div>
                
                <div class="mt-4 grid grid-cols-2 gap-4">
                    <div class="bg-gray-50 rounded-lg px-4 py-3">
                        <p class="text-sm text-gray-600">Total FAQs</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalCount }}</p>
                    </div>
                    <div class="bg-green-50 rounded-lg px-4 py-3">
                        <p class="text-sm text-green-600">Active FAQs</p>
                        <p class="text-2xl font-bold text-green-900">{{ $activeCount }}</p>
                    </div>
                </div>

                @if($totalCount > 0)
                <div class="mt-4">
                    <p class="text-sm font-medium text-gray-700 mb-2">Recent Questions:</p>
                    <ul class="space-y-2">
                        @foreach($faqs->take(3) as $faq)
                        <li class="flex items-start">
                            <svg class="w-4 h-4 text-{{ $faq->is_active ? 'green' : 'gray' }}-500 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm text-gray-600">{{ Str::limit($faq->question, 60) }}</span>
                        </li>
                        @endforeach
                        @if($totalCount > 3)
                        <li class="text-sm text-gray-500 italic ml-6">
                            ... and {{ $totalCount - 3 }} more FAQ{{ $totalCount - 3 > 1 ? 's' : '' }}
                        </li>
                        @endif
                    </ul>
                </div>
                @endif
            </div>
        </div>

        @if($record && $record->exists)
        <div class="mt-6 pt-6 border-t border-gray-200">
            <x-filament::button
                wire:click="mountRelationManager('faqs')"
                size="lg"
                icon="heroicon-o-question-mark-circle"
                color="warning"
                class="w-full"
            >
                Manage FAQs
            </x-filament::button>
        </div>
        @else
        <div class="mt-6 pt-6 border-t border-gray-200">
            <p class="text-sm text-gray-500 text-center">Save the product first to manage FAQs</p>
        </div>
        @endif
    </div>
</div>
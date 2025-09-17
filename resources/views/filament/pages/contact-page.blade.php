<x-filament-panels::page>
    {{-- Add navigation breadcrumb --}}
    <x-slot name="breadcrumbs">
        {{ \Filament\Facades\Filament::renderHook('panels::resource.pages.list-records.breadcrumbs') }}
    </x-slot>
    
    <div class="space-y-6">
        <!-- Contact Page Sections Management -->
        <x-filament::section>
            <x-slot name="heading">
                Contact Page Management
            </x-slot>
            <x-slot name="description">
                Manage all sections and content that appear on your Contact page
            </x-slot>
            
            <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-800">
                            <th class="px-4 py-3 text-left font-medium text-gray-900 dark:text-gray-100">Section</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-900 dark:text-gray-100">Description</th>
                            <th class="px-4 py-3 text-center font-medium text-gray-900 dark:text-gray-100">Items</th>
                            <th class="px-4 py-3 text-center font-medium text-gray-900 dark:text-gray-100">Active</th>
                            <th class="px-4 py-3 text-center font-medium text-gray-900 dark:text-gray-100">Status</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-900 dark:text-gray-100">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-900">
                        @foreach($this->getSections() as $section)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                <td class="px-4 py-3">
                                    <span class="font-medium text-gray-900 dark:text-gray-100">
                                        {{ $section['title'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                    {{ Str::limit($section['description'], 60) }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                        {{ $section['items_count'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-success-100 text-success-700 dark:bg-success-900 dark:text-success-300">
                                        {{ $section['active_count'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($section['status'] === 'configured')
                                        <x-filament::badge color="success">
                                            Configured
                                        </x-filament::badge>
                                    @else
                                        <x-filament::badge color="gray">
                                            Empty
                                        </x-filament::badge>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <x-filament::link
                                        :href="$section['manage_url']"
                                        icon="heroicon-o-arrow-right"
                                        icon-position="after"
                                    >
                                        Manage
                                    </x-filament::link>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
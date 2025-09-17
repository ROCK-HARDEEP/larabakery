<x-filament-panels::page>
    {{-- Add navigation breadcrumb --}}
    <x-slot name="breadcrumbs">
        {{ \Filament\Facades\Filament::renderHook('panels::resource.pages.list-records.breadcrumbs') }}
    </x-slot>
    
    <x-filament::section heading="Header & Footer Management">
        <div class="overflow-hidden rounded-xl border">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-3 text-left">Section</th>
                        <th class="px-4 py-3 text-left">Description</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-t">
                        <td class="px-4 py-3">Header Settings</td>
                        <td class="px-4 py-3">Manage your website header including brand logo and name</td>
                        <td class="px-4 py-3">
                            <x-filament::badge color="success">Active</x-filament::badge>
                        </td>
                        <td class="px-4 py-3">
                            <x-filament::link :href="route('filament.admin.pages.manage-header-settings')" icon="heroicon-o-pencil-square">Manage</x-filament::link>
                        </td>
                    </tr>
                    <tr class="border-t">
                        <td class="px-4 py-3">Footer Settings</td>
                        <td class="px-4 py-3">Configure footer content including brand info, links, social media, and colors</td>
                        <td class="px-4 py-3">
                            <x-filament::badge color="success">Active</x-filament::badge>
                        </td>
                        <td class="px-4 py-3">
                            <x-filament::link :href="route('filament.admin.pages.manage-footer-settings')" icon="heroicon-o-pencil-square">Manage</x-filament::link>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-filament::section>
</x-filament-panels::page>



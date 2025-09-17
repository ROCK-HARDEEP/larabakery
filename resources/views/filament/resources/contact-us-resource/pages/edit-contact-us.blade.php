<x-filament-panels::page>
    @php
        $activeTab = request()->query('activeTab');
    @endphp

    @if($activeTab)
        {{-- Custom styles to hide other tabs and show only the active one --}}
        <style>
            /* Hide the tab list when a specific tab is selected */
            .fi-tabs-tab-list {
                display: none !important;
            }

            /* Make sure only the content is visible */
            .fi-tabs {
                border: none !important;
                background: transparent !important;
            }

            /* Remove tab panel borders and padding for cleaner look */
            .fi-tabs-panel {
                padding: 0 !important;
                border: none !important;
            }
        </style>

        {{-- Display which section is being edited --}}
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                Editing: {{ str_replace('%20', ' ', $activeTab) }}
            </h2>
        </div>
    @endif

    <x-filament-panels::form wire:submit="save">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>

    @if($activeTab)
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Find and activate the specific tab
                setTimeout(function() {
                    const activeTabName = '{{ str_replace('%20', ' ', $activeTab) }}';
                    const tabs = document.querySelectorAll('[role="tab"]');

                    tabs.forEach(tab => {
                        if (tab.textContent.trim() === activeTabName) {
                            // Click the tab to make it active
                            tab.click();
                        }
                    });
                }, 100);
            });
        </script>
    @endif
</x-filament-panels::page>
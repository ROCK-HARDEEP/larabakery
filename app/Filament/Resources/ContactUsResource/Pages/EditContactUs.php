<?php

namespace App\Filament\Resources\ContactUsResource\Pages;

use App\Filament\Resources\ContactUsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Js;

class EditContactUs extends EditRecord
{
    protected static string $resource = ContactUsResource::class;

    protected static string $view = 'filament.resources.contact-us-resource.pages.edit-contact-us';

    public ?string $activeTab = null;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Get the active tab from the query string
        $this->activeTab = request()->query('activeTab');

        return $data;
    }

    protected function getViewData(): array
    {
        return [
            'activeTab' => $this->activeTab,
        ];
    }

    public function mount($record): void
    {
        parent::mount($record);

        // Set the active tab from query parameter
        $this->activeTab = request()->query('activeTab');

        // If activeTab is provided, we'll use JavaScript to activate only that tab
        if ($this->activeTab) {
            $this->dispatch('setActiveTab', ['tab' => $this->activeTab]);
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Back to Contact Page')
                ->url(route('filament.admin.pages.contact-page'))
                ->icon('heroicon-o-arrow-left'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        // If we have an active tab, redirect back with the same tab
        if ($this->activeTab) {
            return $this->getResource()::getUrl('edit', [
                'record' => $this->getRecord(),
                'activeTab' => $this->activeTab
            ]);
        }

        return $this->getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Contact Page Updated')
            ->body('The contact page content has been successfully updated.');
    }
}
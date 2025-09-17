<?php

namespace App\Filament\Pages;

use App\Exports\OrdersExport;
use App\Exports\ProductsExport;
use App\Exports\UsersExport;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Actions\Action;
use Maatwebsite\Excel\Facades\Excel;

class ReportsAndExports extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Reports & Exports';
    protected static ?string $navigationGroup = 'Reports';
    protected static ?int $navigationSort = 1;
    protected static bool $shouldRegisterNavigation = true;
    protected static string $view = 'filament.pages.reports-and-exports';

    public ?string $orders_start_date = null;
    public ?string $orders_end_date = null;
    public ?string $users_start_date = null;
    public ?string $users_end_date = null;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_orders')
                ->label('Export Orders')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function () {
                    return Excel::download(new OrdersExport($this->orders_start_date, $this->orders_end_date), 'orders.xlsx');
                }),
            Action::make('export_products')
                ->label('Export Products')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function () {
                    return Excel::download(new ProductsExport(), 'products.xlsx');
                }),
            Action::make('export_users')
                ->label('Export Users')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function () {
                    return Excel::download(new UsersExport($this->users_start_date, $this->users_end_date), 'users.xlsx');
                }),
        ];
    }
}



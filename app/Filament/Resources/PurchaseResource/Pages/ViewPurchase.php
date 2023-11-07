<?php

declare(strict_types=1);

namespace App\Filament\Resources\PurchaseResource\Pages;

use App\Filament\Resources\PurchaseResource;
use App\Settings\InvoiceSettings;
use Filament\Actions\Action;
use Filament\Actions\StaticAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\View\View;

class ViewPurchase extends ViewRecord
{
    protected static string $resource = PurchaseResource::class;

    protected function getActions(): array
    {
        return [
            Action::make('print_invoice')
                ->icon('heroicon-o-printer')
                ->label('Print Invoice')
                ->modalContent(fn ($record, InvoiceSettings $invoiceSettings): View => view(
                    'filament.pages.actions.print_invoice',
                    ['record' => $record, 'settings' => $invoiceSettings],
                ))
                ->modalHeading('')
                ->modalWidth('sm')
                ->action(function (): void {
                    $this->js('window.print();');
                })
                ->modalSubmitActionLabel('Print')
                ->stickyModalFooter()
                ->modalCancelAction(fn (StaticAction $action) => $action->label('Close'))
                ->closeModalByClickingAway(false),
        ];
    }
}

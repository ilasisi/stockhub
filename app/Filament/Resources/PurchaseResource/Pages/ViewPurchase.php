<?php

declare(strict_types=1);

namespace App\Filament\Resources\PurchaseResource\Pages;

use App\Filament\Resources\PurchaseResource;
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
                ->label('Print Invoice')
                ->modalContent(fn ($record): View => view(
                    'filament.pages.actions.print_invoice',
                    ['record' => $record],
                ))
                ->modalHeading('')
                ->modalWidth('sm')
                ->action(function (): void {
                    dd($this->record);
                })
                ->mountUsing(function (): void {
                    // dd($this->record);
                })
                ->modalSubmitActionLabel('Print')
                ->stickyModalFooter()
                ->modalCancelAction(fn (StaticAction $action) => $action->label('Close'))
                ->closeModalByClickingAway(false),
        ];
    }
}

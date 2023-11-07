<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Settings\InvoiceSettings;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManageInvoice extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = InvoiceSettings::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Textarea::make('thank_you_message')
                    ->label('Thank You Message'),
                Textarea::make('sub_thank_you_message')
                    ->label('Sub Thank You Message'),
            ]);
    }
}

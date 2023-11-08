<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Settings\InvoiceSettings;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManageInvoice extends SettingsPage
{
    protected static string $settings = InvoiceSettings::class;

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $title = 'Invoice Settings';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->isSuperAdmin();
    }

    public function boot(): void
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);
    }

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

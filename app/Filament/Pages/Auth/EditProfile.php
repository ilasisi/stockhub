<?php

declare(strict_types=1);

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;

class EditProfile extends BaseEditProfile
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label(__('filament-panels::pages/auth/edit-profile.form.name.label'))
                    ->required()
                    ->disabled(! auth()->user()->isSuperAdmin())
                    ->maxLength(255)
                    ->autofocus(),
                TextInput::make('email')
                    ->label(__('filament-panels::pages/auth/edit-profile.form.email.label'))
                    ->email()
                    ->required()
                    ->disabled(! auth()->user()->isSuperAdmin())
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
            ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Filament\Pages\Tenancy;

use App\Models\Branch;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Pages\Tenancy\RegisterTenant;

class RegisterBranch extends RegisterTenant
{
    public static function getLabel(): string
    {
        return 'Register Branch';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->live(onBlur: true)
                    ->required()
                    ->afterStateUpdated(fn ($state, Set $set) => $set('slug', str($state)->slug())),
                TextInput::make('slug')
                    ->disabled()
                    ->unique(Branch::class, 'slug')
                    ->dehydrated(),
                TextInput::make('contact_phone')
                    ->required(),
                Textarea::make('address'),
            ]);
    }

    protected function handleRegistration(array $data): Branch
    {
        $branch = Branch::create($data);

        $branch->users()->attach(auth()->user());

        return $branch;
    }
}

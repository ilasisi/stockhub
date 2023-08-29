<?php

declare(strict_types=1);

namespace App\Filament\Pages\Tenancy;

use App\Models\Branch;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Pages\Tenancy\EditTenantProfile;

class EditBranchProfile extends EditTenantProfile
{
    public static function getLabel(): string
    {
        return 'Branch Profile';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->live(onBlur: true)
                            ->required()
                            ->afterStateUpdated(fn ($state, Set $set) => $set('slug', str($state)->slug())),
                        TextInput::make('slug')
                            ->disabled()
                            ->unique(Branch::class, 'slug', ignoreRecord: true)
                            ->dehydrated(),
                    ]),
            ]);
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Branch profile updated successfully';
    }
}

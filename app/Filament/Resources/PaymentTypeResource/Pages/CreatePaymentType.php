<?php

declare(strict_types=1);

namespace App\Filament\Resources\PaymentTypeResource\Pages;

use App\Filament\Resources\PaymentTypeResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePaymentType extends CreateRecord
{
    protected static string $resource = PaymentTypeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Payment type created successfully';
    }
}

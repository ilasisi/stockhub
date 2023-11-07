<?php

declare(strict_types=1);

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class InvoiceSettings extends Settings
{
    public ?string $thank_you_message;

    public ?string $sub_thank_you_message;

    public static function group(): string
    {
        return 'invoice';
    }
}

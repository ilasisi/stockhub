<?php

declare(strict_types=1);

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class() extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('invoice.thank_you_message', 'Thanks for you patronage!');
        $this->migrator->add('invoice.sub_thank_you_message', 'Order bought in good condition can not be returned!');
    }
};

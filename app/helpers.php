<?php

declare(strict_types=1);

if (! function_exists('getYears')) {
    function getYears(): ?array
    {
        $years = collect(range(now()->year, 2023))
            ->mapWithKeys(fn ($value) => [$value => $value])
            ->toArray();

        return $years;
    }
}

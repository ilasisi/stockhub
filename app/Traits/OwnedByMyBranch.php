<?php

declare(strict_types=1);

namespace App\Traits;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;

trait OwnedByMyBranch
{
    public function scopeOwnedByMyBranch(Builder $query): Builder
    {
        return $query->where('branch_id', Filament::getTenant()->id);
    }
}

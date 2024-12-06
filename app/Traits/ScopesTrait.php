<?php
// app/Traits/ScopesTrait.php

namespace App\Traits;

trait ScopesTrait
{
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', false);
    }

    
}

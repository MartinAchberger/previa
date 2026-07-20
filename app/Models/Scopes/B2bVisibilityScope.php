<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class B2bVisibilityScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (app()->runningInConsole()) {
            return;
        }
        if (Auth::guard('b2b')->check()) {
            return;
        }
        // Orchid admin (web guard) sees the full catalogue, but ONLY inside the
        // admin panel — an admin browsing the public storefront sees the same
        // public catalogue as a guest, not the b2b-only products.
        $adminPrefix = ltrim((string) config('platform.prefix', 'admin'), '/');
        if (Auth::guard('web')->check() && request()->is($adminPrefix, $adminPrefix . '/*')) {
            return;
        }
        $builder->where($model->getTable() . '.b2b_only', false);
    }
}

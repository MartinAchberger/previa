<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The eshop screens gained permission() gates (platform.eshop.*). Existing
     * Orchid admins do not have those permissions yet, so on production they'd
     * hit 403 on /admin/products etc. Grant the permissions to every existing
     * admin user (and role) so the panel keeps working after deploy.
     */
    private array $perms = [
        'platform.eshop.orders'  => true,
        'platform.eshop.catalog' => true,
        'platform.eshop.b2b'     => true,
    ];

    public function up(): void
    {
        foreach (['users', 'roles'] as $table) {
            if (! DB::getSchemaBuilder()->hasTable($table)) {
                continue;
            }

            foreach (DB::table($table)->get() as $row) {
                $perms = json_decode($row->permissions ?? '{}', true) ?: [];
                $perms = array_merge($perms, $this->perms);

                DB::table($table)->where('id', $row->id)->update([
                    'permissions' => json_encode($perms),
                ]);
            }
        }
    }

    public function down(): void
    {
        foreach (['users', 'roles'] as $table) {
            if (! DB::getSchemaBuilder()->hasTable($table)) {
                continue;
            }

            foreach (DB::table($table)->get() as $row) {
                $perms = json_decode($row->permissions ?? '{}', true) ?: [];
                foreach (array_keys($this->perms) as $key) {
                    unset($perms[$key]);
                }

                DB::table($table)->where('id', $row->id)->update([
                    'permissions' => json_encode($perms),
                ]);
            }
        }
    }
};

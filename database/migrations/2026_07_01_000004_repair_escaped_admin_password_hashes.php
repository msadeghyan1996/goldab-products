<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('admins')
            ->select(['id', 'password'])
            ->orderBy('id')
            ->each(function (object $admin): void {
                if (! str_starts_with($admin->password, '\\$2y\\$')) {
                    return;
                }

                $password = str_replace('\\$', '$', $admin->password);

                if (password_get_info($password)['algoName'] !== 'bcrypt') {
                    return;
                }

                DB::table('admins')
                    ->where('id', $admin->id)
                    ->update(['password' => $password]);
            });
    }

    public function down(): void
    {
        //
    }
};

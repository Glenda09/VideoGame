<?php

use App\Enums\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('role', 32)
                ->default(Role::Customer->value)
                ->after('password');
        });

        DB::table('users')
            ->where('is_admin', true)
            ->update(['role' => Role::SuperAdmin->value]);

        DB::table('users')
            ->whereNull('role')
            ->update(['role' => Role::Customer->value]);

        Schema::table('users', function (Blueprint $table): void {
            if (Schema::hasColumn('users', 'is_admin')) {
                $table->dropColumn('is_admin');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->boolean('is_admin')
                ->default(false)
                ->after('password');
        });

        DB::table('users')
            ->where('role', Role::SuperAdmin->value)
            ->update(['is_admin' => true]);

        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn('role');
        });
    }
};


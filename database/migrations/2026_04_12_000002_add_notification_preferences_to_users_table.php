<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('order_notifications_enabled')->default(true)->after('is_active');
            $table->boolean('interest_notifications_enabled')->default(true)->after('order_notifications_enabled');
            $table->boolean('system_notifications_enabled')->default(true)->after('interest_notifications_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'order_notifications_enabled',
                'interest_notifications_enabled',
                'system_notifications_enabled',
            ]);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->string('notification_category', 30)->nullable()->after('body_en');
            $table->string('notification_type', 100)->nullable()->after('notification_category');
            $table->string('model_type')->nullable()->after('notification_type');
            $table->unsignedBigInteger('model_id')->nullable()->after('model_type');
            $table->json('payload')->nullable()->after('order_id');
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn([
                'notification_category',
                'notification_type',
                'model_type',
                'model_id',
                'payload',
            ]);
        });
    }
};

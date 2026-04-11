<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auth_updates', function (Blueprint $table) {
            $table->id();
            $table->morphs('auth_updateable');
            $table->string('type', 20);
            $table->string('sub_type', 40);
            $table->string('attribute')->nullable();
            $table->string('country_code', 5)->nullable();
            $table->string('code', 6)->nullable();
            $table->timestamp('code_expires_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index(['type', 'sub_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auth_updates');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('investment_seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('opportunity_id')->constrained('opportunities')->cascadeOnDelete();
            $table->decimal('price_paid', 12, 2)->nullable();
            $table->timestamp('purchased_at')->nullable();
            $table->string('payment_id')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'opportunity_id']);
        });

        Schema::create('interest_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('opportunity_id')->constrained('opportunities')->cascadeOnDelete();
            $table->foreignId('investment_seat_id')->constrained('investment_seats')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'opportunity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interest_requests');
        Schema::dropIfExists('investment_seats');
    }
};

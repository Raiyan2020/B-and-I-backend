<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{

    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->enum('role', ['investor', 'advertiser'])->default('investor');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('image')->nullable();
            $table->string('country_code', 5)->nullable();
            $table->string('phone', 8)->nullable();
            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('code', 10)->nullable();
            $table->boolean('is_blocked')->default(false);
            $table->boolean('is_active')->default(true);
            
            // Shared info
            $table->enum('subscription_plan', ['basic', 'premium', 'vip'])->nullable();
            $table->text('bio')->nullable();
            $table->string('tagline')->nullable();
            
            // Investor
            $table->enum('investor_type', ['angel', 'company', 'crowdfunding'])->nullable();
            $table->string('investor_sector')->nullable();
            $table->decimal('investor_capital', 12, 3)->nullable();
            $table->unsignedInteger('investment_count')->nullable();
            $table->enum('investor_experience', ['beginner', 'intermediate', 'expert'])->nullable();
            
            // Advertiser
            $table->string('company_name')->nullable();
            $table->string('company_license_url')->nullable();
            $table->string('license_number')->nullable();

            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });
    }


    public function down()
    {
        Schema::dropIfExists('users');
    }
}

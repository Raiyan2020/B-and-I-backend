<?php

use App\Enums\InvestorExperience;
use App\Enums\InvestorType;
use App\Enums\UserRole;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{

    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->enum('role', UserRole::values())->default(UserRole::Investor->value);
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone', 8)->nullable();
            $table->string('country_code', 5)->nullable();
            $table->string('email')->nullable();
            $table->string('password');
            $table->string('company_license')->nullable();

            $table->string('investor_type')->nullable();
            $table->decimal('capital', 12, 3)->nullable(); // راس المال
            $table->decimal('available_capital', 12, 3)->nullable(); // راس المال المتاح
            $table->foreignId('preferred_sector_id')->nullable()->constrained('preferred_sectors');
            $table->foreignId('category_id')->nullable()->constrained('categories');
            $table->decimal('experience_level', 12, 3)->nullable();
            $table->integer('previous_investments_count')->nullable();
            $table->string('investor_experience')->nullable();

            $table->string('image')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('lang', 2)->default('ar');
            $table->boolean('is_blocked')->default(false);
            $table->boolean('is_active')->default(true);

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

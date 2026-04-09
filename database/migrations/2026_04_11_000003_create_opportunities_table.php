<?php

use App\Enums\OpportunityGoal;
use App\Enums\OpportunityStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('opportunities', function (Blueprint $table) {
            $table->id();
            $table->string('image')->nullable();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories');
            $table->foreignId('reviewed_by_admin_id')->nullable()->constrained('admins')->nullOnDelete();

            $table->enum('goal', OpportunityGoal::values());
            $table->enum('status', OpportunityStatus::values())->default(OpportunityStatus::PendingReview->value);

            $table->string('contact_name');
            $table->string('contact_phone', 20);
            $table->string('contact_email');
            $table->string('owner_name');
            $table->string('admin_company_name');
            $table->string('license_number', 100);

            $table->string('company_name');
            $table->unsignedInteger('business_age_years');
            $table->decimal('investment_required', 14, 3);
            $table->string('business_stage');
            $table->decimal('sale_percentage', 5, 2)->nullable();

            $table->string('legal_entity');
            $table->string('financial_status');
            $table->text('investment_reason');
            $table->longText('full_description');

            $table->text('review_note')->nullable();
            $table->timestamp('reviewed_at')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('opportunities');
    }
};

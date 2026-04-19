<?php

use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications_tmp', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->string('notification_category', 30)->nullable()->index();
            $table->json('data');
            $table->timestamp('read_at')->nullable()->index();
            $table->timestamps();
        });

        DB::table('notifications')
            ->orderBy('id')
            ->chunk(500, function ($rows): void {
                $payloads = [];

                foreach ($rows as $row) {
                    $notifiableType = null;
                    $notifiableId = null;

                    if (! empty($row->admin_id)) {
                        $notifiableType = Admin::class;
                        $notifiableId = (int) $row->admin_id;
                    } elseif (! empty($row->user_id)) {
                        $notifiableType = User::class;
                        $notifiableId = (int) $row->user_id;
                    }

                    if (! $notifiableType || ! $notifiableId) {
                        continue;
                    }

                    $legacyPayload = json_decode((string) ($row->payload ?? '[]'), true);
                    if (! is_array($legacyPayload)) {
                        $legacyPayload = [];
                    }

                    $data = array_filter([
                        'notification_type' => $row->notification_type ?: 'legacy.notification',
                        'notification_category' => $row->notification_category,
                        'title' => array_filter([
                            'ar' => $row->title_ar,
                            'en' => $row->title_en,
                        ]),
                        'body' => array_filter([
                            'ar' => $row->body_ar,
                            'en' => $row->body_en,
                        ]),
                        'model_type' => $row->model_type,
                        'model_id' => $row->model_id,
                        'order_id' => $row->order_id,
                        'legacy_id' => $row->id,
                    ], static fn ($value) => $value !== null && $value !== [] && $value !== '');

                    $payloads[] = [
                        'id' => (string) Str::uuid(),
                        'type' => \App\Notifications\GeneralNotification::class,
                        'notifiable_type' => $notifiableType,
                        'notifiable_id' => $notifiableId,
                        'notification_category' => $row->notification_category,
                        'data' => json_encode($data + $legacyPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                        'read_at' => ! empty($row->seen) ? ($row->updated_at ?? $row->created_at ?? now()) : null,
                        'created_at' => $row->created_at ?? now(),
                        'updated_at' => $row->updated_at ?? now(),
                    ];
                }

                if ($payloads !== []) {
                    DB::table('notifications_tmp')->insert($payloads);
                }
            });

        Schema::drop('notifications');
        Schema::rename('notifications_tmp', 'notifications');
    }

    public function down(): void
    {
        Schema::create('notifications_legacy', function (Blueprint $table) {
            $table->id();
            $table->string('title_ar')->nullable();
            $table->string('title_en')->nullable();
            $table->text('body_ar')->nullable();
            $table->text('body_en')->nullable();
            $table->string('notification_category', 30)->nullable();
            $table->string('notification_type', 100)->nullable();
            $table->string('model_type')->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->json('payload')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->boolean('seen')->default(false);
            $table->timestamps();
        });

        Schema::drop('notifications');
        Schema::rename('notifications_legacy', 'notifications');
    }
};

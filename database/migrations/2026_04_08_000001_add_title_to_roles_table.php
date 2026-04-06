<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->json('title')->nullable()->after('guard_name');
        });

        foreach (DB::table('roles')->orderBy('id')->get() as $row) {
            $display = $row->name;
            $title = json_encode(['ar' => $display, 'en' => $display], JSON_UNESCAPED_UNICODE);
            DB::table('roles')->where('id', $row->id)->update(['title' => $title]);
        }
    }

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('title');
        });
    }
};

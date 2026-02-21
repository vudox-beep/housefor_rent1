<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->string('public_id')->nullable()->unique()->after('id');
        });

        // Backfill existing rows with UUIDs
        DB::table('listings')->whereNull('public_id')->orderBy('id')->chunkById(500, function ($rows) {
            foreach ($rows as $row) {
                DB::table('listings')
                    ->where('id', $row->id)
                    ->update(['public_id' => (string) Str::uuid()]);
            }
        });

        Schema::table('listings', function (Blueprint $table) {
            $table->string('public_id')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropUnique(['public_id']);
            $table->dropColumn('public_id');
        });
    }
};


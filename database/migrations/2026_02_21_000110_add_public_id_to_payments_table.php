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
        Schema::table('payments', function (Blueprint $table) {
            $table->string('public_id')->nullable()->unique()->after('id');
        });

        DB::table('payments')->whereNull('public_id')->orderBy('id')->chunkById(500, function ($rows) {
            foreach ($rows as $row) {
                DB::table('payments')
                    ->where('id', $row->id)
                    ->update(['public_id' => (string) Str::uuid()]);
            }
        });

        if (Schema::getConnection()->getDriverName() !== 'sqlite') {
            Schema::table('payments', function (Blueprint $table) {
                $table->string('public_id')->nullable(false)->change();
            });
        }
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropUnique(['public_id']);
            $table->dropColumn('public_id');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        // Make transaction_id nullable, removing any constraints.
        // Using CHANGE to replace the column definition completely.
        DB::statement("ALTER TABLE `payments` CHANGE `transaction_id` `transaction_id` VARCHAR(255) NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        // Restore the original NOT NULL constraint.
        DB::statement("ALTER TABLE `payments` CHANGE `transaction_id` `transaction_id` VARCHAR(255) NOT NULL UNIQUE");
    }
};

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

        // Modify the 'type' enum to include 'subscription'
        DB::statement("ALTER TABLE `payments` CHANGE `type` `type` ENUM('dealer_registration', 'promotion', 'subscription') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        // Restore the original enum (remove 'subscription')
        DB::statement("ALTER TABLE `payments` CHANGE `type` `type` ENUM('dealer_registration', 'promotion') NOT NULL");
    }
};

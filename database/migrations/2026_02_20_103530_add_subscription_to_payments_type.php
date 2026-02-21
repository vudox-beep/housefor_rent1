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
        // Modify the 'type' enum to include 'subscription'
        DB::statement("ALTER TABLE `payments` CHANGE `type` `type` ENUM('dealer_registration', 'promotion', 'subscription') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore the original enum (remove 'subscription')
        DB::statement("ALTER TABLE `payments` CHANGE `type` `type` ENUM('dealer_registration', 'promotion') NOT NULL");
    }
};

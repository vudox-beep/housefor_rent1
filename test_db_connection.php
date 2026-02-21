<?php

use Illuminate\Support\Facades\DB;

try {
    DB::connection()->getPdo();
    echo "SUCCESS: Connected to database '" . DB::connection()->getDatabaseName() . "'.";
} catch (\Exception $e) {
    echo "ERROR: Could not connect to the database. " . $e->getMessage();
}

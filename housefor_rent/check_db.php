<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306', 'root', '');
    echo "MySQL connection successful.\n";
    $pdo->exec("USE rent");
    echo "Selected database 'rent'.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

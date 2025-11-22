<?php
try {
    $pdo = new PDO('pgsql:host=127.0.0.1;port=5432;dbname=postgres', 'postgres', '');
    echo "Connected";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

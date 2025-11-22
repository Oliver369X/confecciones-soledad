<?php
try {
    $pdo = new PDO('pgsql:host=127.0.0.1;port=5432;dbname=postgres', 'postgres', 'root');
    $pdo->exec("CREATE DATABASE confecciones_soledad");
    echo "Database created";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

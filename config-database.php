<?php
// config-database.php

function getDBConnection(): PDO {
    // Conexión directa usando el ID de tu proyecto y su región real (us-east-2)
    $host = 'db.qrfaqadirfmzvxbaijqp.supabase.co'; 
    $port = '5432'; // Puerto directo estándar de PostgreSQL
    $name = 'postgres';
    $user = 'postgres'; 
    $pass = '**Ucv123456**/'; 

    // DSN limpio y directo para entornos de producción Cloud
    $dsn = "pgsql:host=$host;port=$port;dbname=$name;sslmode=require";

    try {
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        return $pdo;
    } catch (PDOException $e) {
        die("Error crítico de conexión a Supabase: " . $e->getMessage());
    }
}
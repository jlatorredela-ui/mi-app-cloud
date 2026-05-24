<?php
// config-database.php

function getDBConnection(): PDO {
    // HOST DIRECTO (Puerto 5432) - Es más estable y acepta tu contraseña actual
    $host = 'db.qrfaqadirfmzvxbaijqp.supabase.co'; 
    $port = '5432'; 
    $name = 'postgres';
    
    // USUARIO LIMPIO (No necesitamos el ID del proyecto aquí)
    $user = 'postgres'; 
    $pass = '**Ucv123456**/'; 

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
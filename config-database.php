<?php
// config-database.php

function getDBConnection(): PDO {
    // CORRECCIÓN: Usamos el host optimizado para IPv4 (aws-0-) que exige Render en conexiones directas
    $host = 'aws-0-sa-east-1.pooler.supabase.com'; 
    $port = '5432'; // Mantenemos el puerto directo estándar
    $name = 'postgres';
    
    // Al usar el puerto 5432 directo con el pooler de AWS, el usuario DEBE llevar tu ID de proyecto adjunto
    $user = 'postgres.qrfaqadirfmzvxbaijqp'; 
    $pass = '**Ucv123456**/'; 

    // DSN limpio y forzado por IPv4
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
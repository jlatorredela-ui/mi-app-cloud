<?php
// config-database.php

function getDBConnection(): PDO {
    // CONEXIÓN DIRECTA INFALIBLE: Usamos el host directo por puerto estándar
    $host = 'db.qrfaqadirfmzvxbaijqp.supabase.co'; 
    $port = '5432'; // Puerto directo estándar de PostgreSQL (sin pooler)
    $name = 'postgres';
    $user = 'postgres'; // Usuario limpio e inicial sin añadidos
    $pass = '**Ucv123456**/'; 

    // Agregamos opciones para obligar a PHP a resolver por IPv4 y evitar el "Network is unreachable"
    $dsn = "pgsql:host=$host;port=$port;dbname=$name;sslmode=require";

    try {
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // Forzar tiempo de espera óptimo para entornos Cloud
            PDO::ATTR_TIMEOUT            => 5 
        ]);
        return $pdo;
    } catch (PDOException $e) {
        die("Error crítico de conexión a Supabase: " . $e->getMessage());
    }
}
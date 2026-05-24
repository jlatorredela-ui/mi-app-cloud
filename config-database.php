<?php
// config-database.php

function getDBConnection(): PDO {
    // SOLUCIÓN ABSOLUTA: Usamos la IP estática IPv4 directa de Supabase en la región us-east-2 (Ohio)
    // Esto evita por completo el bloqueo de "Network is unreachable" de Render
    $host = '3.14.240.237'; 
    $port = '5432'; // Puerto estándar directo de PostgreSQL
    $name = 'postgres';
    $user = 'postgres.qrfaqadirfmzvxbaijqp'; // ID de tu proyecto integrado obligatoriamente para enrutar la IP
    $pass = '**Ucv123456**/'; 

    // DSN limpio estructurado sobre IPv4 directa
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
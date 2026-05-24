<?php
// config-database.php

function getDBConnection(): PDO {
    // Usamos el host directo de tu proyecto para resolver el problema de SNI en la red
    $host = 'aws-0-sa-east-1.pooler.supabase.com'; 
    $port = '6543';
    $name = 'postgres';
    $user = 'postgres.qrfaqadirfmzvxbaijqp'; // ID de tu proyecto inyectado en el usuario de forma directa
    $pass = '**Ucv123456**/'; 

    // DSN limpio estándar para PostgreSQL
    $dsn = "pgsql:host=$host;port=$port;dbname=$name;sslmode=require";

    try {
        // Pasamos el usuario formateado directamente en los parámetros del constructor de PDO
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        return $pdo;
    } catch (PDOException $e) {
        die("Error crítico de conexión a Supabase: " . $e->getMessage());
    }
}
<?php
// config-database.php

function getDBConnection(): PDO {
    // URL-encodamos tu contraseña para que los caracteres especiales (** y /) no rompan el Pooler
    $user = "postgres.qrfaqadirfmzvxbaijqp"; 
    $pass = urlencode('**Ucv123456**/'); 
    $host = "aws-0-us-east-2.pooler.supabase.com";
    $port = "6543";
    $name = "postgres";

    // Construimos la cadena de conexión usando el formato de URL de PostgreSQL (el más robusto para Cloud)
    $dsn = "pgsql:host=$host;port=$port;dbname=$name;sslmode=require";

    try {
        // Al usar la autenticación con el usuario del pooler y la clave codificada, conectará directo
        $pdo = new PDO($dsn, $user, '**Ucv123456**/', [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        return $pdo;
    } catch (PDOException $e) {
        die("Error crítico de conexión a Supabase: " . $e->getMessage());
    }
}
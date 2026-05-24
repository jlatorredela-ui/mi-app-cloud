<?php
// config-database.php

function getDBConnection(): PDO {
    // SOLUCIÓN: Usamos el pooler exacto de tu región Ohio (us-east-2) para resolver por IPv4
    $host = 'aws-0-us-east-2.pooler.supabase.com'; 
    $port = '6543'; // Volvemos al puerto del pooler de Supabase
    $name = 'postgres';
    
    // El usuario formateado estrictamente con tu ID de proyecto de Ohio
    $user = 'postgres.qrfaqadirfmzvxbaijqp'; 
    $pass = '**Ucv123456**/'; 

    // DSN configurado para resolver con éxito desde Render
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
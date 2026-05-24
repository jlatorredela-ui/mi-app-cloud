<?php
// config-database.php

function getDBConnection(): PDO {
    // 1. Usamos el host del pooler regional de Ohio que ya validamos que responde
    $host = 'aws-0-us-east-2.pooler.supabase.com'; 
    $port = '6543'; 
    $name = 'postgres';
    
    // 2. Pasamos el usuario con el ID del proyecto directamente aquí
    $user = 'postgres.qrfaqadirfmzvxbaijqp'; 
    $pass = '**Ucv123456**/'; 

    // 3. El DSN se mantiene limpio, sin configuraciones extrañas de texto
    $dsn = "pgsql:host=$host;port=$port;dbname=$name;sslmode=require";

    try {
        // 4. Pasamos las variables de forma nativa al constructor de PDO
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_PERSISTENT         => false // Evita que se queden conexiones muertas en Render
        ]);
        return $pdo;
    } catch (PDOException $e) {
        die("Error crítico de conexión a Supabase: " . $e->getMessage());
    }
}
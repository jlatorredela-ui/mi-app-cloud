<?php
// config-database.php

function getDBConnection(): PDO {
    // Forzamos los datos reales directamente para evitar que Render los pise con variables antiguas
    $host = 'aws-0-sa-east-1.pooler.supabase.com';
    $port = '6543';
    $name = 'postgres';
    $user = 'postgres';
    $pass = '**Ucv123456**/'; 

    // Tu ID de proyecto real obtenido de Supabase
    $proyecto_id = "qrfaqadirfmzvxbaijqp"; 
    
    // Formato estricto que exige Supabase Cloud para identificar tu cuenta: usuario.id_proyecto
    $user_dsn = "{$user}.{$proyecto_id}";

    // Construcción limpia y directa del DSN
    $dsn = "pgsql:host=$host;port=$port;dbname=$name;user=$user_dsn;sslmode=require";

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
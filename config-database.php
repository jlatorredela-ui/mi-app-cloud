<?php
// config-database.php

function getDBConnection(): PDO {
    // Estos datos provienen de la cadena que pusiste arriba
    $host = 'aws-1-us-east-2.pooler.supabase.com'; // O usa db.qrfaqadirfmzvxbaijqp.supabase.co
    $port = '5432'; 
    $db   = 'postgres';
    $user = 'postgres.qrfaqadirfmzvxbaijqp';
    $pass = 'TU_CONTRASEÑA_REAL_AQUÍ'; // Pon tu contraseña aquí

    // Construcción del DSN (Data Source Name)
    $dsn = "pgsql:host=$host;port=$port;dbname=$db;sslmode=require";

    try {
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // IMPORTANTE: Evitar persistencia ayuda a que Render no guarde errores de red antiguos
            PDO::ATTR_PERSISTENT         => false
        ]);
        return $pdo;
    } catch (PDOException $e) {
        // Esto te dirá exactamente qué falla si la conexión no se logra
        die("Error crítico de conexión: " . $e->getMessage());
    }
}
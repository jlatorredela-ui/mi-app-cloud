<?php
// index.php
require_once 'config-database.php';
$pdo = getDBConnection();

$error_message = "";
$estudiante_editar = null;

// Captura de parámetros de búsqueda de manera global para usarse en redirecciones
$buscar = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';

// ==========================================================
// EJERCICIO 2: VALIDACIÓN EN BACKEND (RESTRICCIONES OWASP)
// ==========================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $nombre = trim($_POST['nombre']);
        $email = trim($_POST['email']);
        $carrera = trim($_POST['carrera']);

        // Reglas de validación solicitadas
        if (strlen($nombre) < 3 || strlen($nombre) > 100) {
            $error_message = "Error: El nombre debe tener entre 3 y 100 caracteres.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_message = "Error: El formato del correo electrónico no es válido.";
        } elseif (empty($carrera)) {
            $error_message = "Error: El campo de la carrera profesional no puede estar vacío.";
        } else {
            // PROCESO DE INSERCIÓN (CREATE)
            if ($_POST['action'] === 'create') {
                $stmt = $pdo->prepare("INSERT INTO estudiantes (nombre, email, carrera) VALUES (:n, :e, :c)");
                $stmt->execute([
                    ':n' => htmlspecialchars($nombre),
                    ':e' => filter_var($email, FILTER_SANITIZE_EMAIL),
                    ':c' => htmlspecialchars($carrera)
                ]);
                // CORRECCIÓN: Redirección segura al archivo local de entrada
                header('Location: index.php'); exit;
            } 
            // ==========================================================
            // EJERCICIO 1: FUNCIÓN DE ACTUALIZACIÓN (UPDATE)
            // ==========================================================
            elseif ($_POST['action'] === 'update' && isset($_POST['id'])) {
                $id = (int)$_POST['id'];
                $stmt = $pdo->prepare("UPDATE estudiantes SET nombre = :n, email = :e, carrera = :c WHERE id = :id");
                $stmt->execute([
                    ':n'  => htmlspecialchars($nombre),
                    ':e'  => filter_var($email, FILTER_SANITIZE_EMAIL),
                    ':c'  => htmlspecialchars($carrera),
                    ':id' => $id
                ]);
                // OPTIMIZACIÓN: Mantiene el filtro de búsqueda tras editar si existía alguno
                $query_param = !empty($buscar) ? '?buscar=' . urlencode($buscar) : '';
                header('Location: index.php' . $query_param); exit;
            }
        }
    }
}

// CAPTURA DE DATOS PARA LA EDICIÓN
if (isset($_GET['edit'])) {
    $id_edit = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM estudiantes WHERE id = :id");
    $stmt->execute([':id' => $id_edit]);
    $estudiante_editar = $stmt->fetch();
}

// PROCESO DE ELIMINACIÓN (DELETE)
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM estudiantes WHERE id = :id");
    $stmt->execute([':id' => (int)$_GET['delete']]);
    // OPTIMIZACIÓN: Mantiene el filtro de búsqueda tras eliminar si existía alguno
    $query_param = !empty($buscar) ? '?buscar=' . urlencode($buscar) : '';
    header('Location: index.php' . $query_param); exit;
}

// ==========================================================
// EJERCICIOS 3 Y 4: BUSCADOR CON ILIKE + PAGINACIÓN CON LIMIT Y OFFSET
// ==========================================================

// Configuración de paginación
$limit = 5; // Mostrar 5 estudiantes por página
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($pagina < 1) $pagina = 1;
$offset = ($pagina - 1) * $limit;

if (!empty($buscar)) {
    // Contar registros filtrados por la búsqueda (ILIKE para PostgreSQL)
    $count_sql = "SELECT COUNT(*) FROM estudiantes WHERE nombre ILIKE :b OR carrera ILIKE :b";
    $stmt_count = $pdo->prepare($count_sql);
    $stmt_count->execute([':b' => "%$buscar%"]);
    $total_registros = $stmt_count->fetchColumn();

    // Obtener registros filtrados con límites de paginación
    $data_sql = "SELECT * FROM estudiantes WHERE nombre ILIKE :b OR carrera ILIKE :b ORDER BY creado_en DESC LIMIT :limit OFFSET :offset";
    $stmt_data = $pdo->prepare($data_sql);
    $stmt_data->bindValue(':b', "%$buscar%", PDO::PARAM_STR);
    $stmt_data->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt_data->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt_data->execute();
    $estudiantes = $stmt_data->fetchAll();
} else {
    // Contar total general de la tabla
    $total_registros = $pdo->query("SELECT COUNT(*) FROM estudiantes")->fetchColumn();

    // Obtener registros globales paginados
    $data_sql = "SELECT * FROM estudiantes ORDER BY creado_en DESC LIMIT :limit OFFSET :offset";
    $stmt_data = $pdo->prepare($data_sql);
    $stmt_data->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt_data->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt_data->execute();
    $estudiantes = $stmt_data->fetchAll();
}

// Calcular páginas totales
$total_paginas = ceil($total_registros / $limit);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Gestión de Estudiantes</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 40px; background-color: #f8f9fa; color: #333; }
        h2 { color: #2c3e50; margin-top: 0; }
        .alert-error { color: #721c24; background-color: #f8d7da; padding: 12px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #f5c6cb; font-weight: bold; }
        .card { background: #ffffff; padding: 25px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-bottom: 30px; border: 1px solid #e9ecef; }
        input[type="text"], input[type="email"] { padding: 10px 14px; margin-right: 12px; border: 1px solid #ced4da; border-radius: 6px; width: 220px; font-size: 14px; }
        input[type="text"]:focus, input[type="email"]:focus { border-color: #80bdff; outline: 0; }
        button { padding: 10px 18px; background-color: #28a745; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 6px; font-size: 14px; }
        button:hover { background-color: #218838; }
        .btn-secondary { background-color: #6c757d; }
        .btn-secondary:hover { background-color: #5a6268; }
        .btn-search { background-color: #007bff; }
        .btn-search:hover { background-color: #0069d9; }
        table { width: 100%; border-collapse: collapse; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        th, td { padding: 14px 18px; text-align: left; border-bottom: 1px solid #dee2e6; }
        th { background-color: #343a40; color: white; font-weight: 600; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px; }
        tr:hover { background-color: #f8f9fa; }
        .actions a { text-decoration: none; font-weight: bold; margin-right: 15px; font-size: 14px; }
        .link-edit { color: #ffc107; }
        .link-edit:hover { color: #d39e00; }
        .link-delete { color: #dc3545; }
        .link-delete:hover { color: #bd2130; }
        .pagination-container { margin-top: 25px; display: flex; align-items: center; gap: 8px; }
        .pagination-container a { padding: 8px 16px; border: 1px solid #007bff; color: #007bff; text-decoration: none; border-radius: 6px; font-weight: 500; font-size: 14px; }
        .pagination-container a:hover { background-color: #007bff; color: white; }
        .pagination-container a.disabled { border-color: #6c757d; color: #6c757d; pointer-events: none; opacity: 0.5; }
        .pagination-info { font-size: 14px; color: #6c757d; margin: 0 15px; font-weight: 500; }
    </style>
</head>
<body>

    <?php if (!empty($error_message)): ?>
        <div class="alert-error"><?= $error_message ?></div>
    <?php endif; ?>

    <div class="card">
        <h2><?= $estudiante_editar ? "📝 Formulario de Edición" : "➕ Registrar Nuevo Estudiante" ?></h2>
        <form method="POST">
            <input type="hidden" name="action" value="<?= $estudiante_editar ? 'update' : 'create' ?>">
            <?php if ($estudiante_editar): ?>
                <input type="hidden" name="id" value="<?= $estudiante_editar['id'] ?>">
            <?php endif; ?>

            <input name="nombre" placeholder="Nombre (3 a 100 caracteres)" required 
                   value="<?= $estudiante_editar ? htmlspecialchars($estudiante_editar['nombre']) : '' ?>">
            
            <input name="email" type="email" placeholder="Correo institucional" required 
                   value="<?= $estudiante_editar ? htmlspecialchars($estudiante_editar['email']) : '' ?>">
            
            <input name="carrera" placeholder="Carrera universitaria" required 
                   value="<?= $estudiante_editar ? htmlspecialchars($estudiante_editar['carrera']) : '' ?>">
            
            <button type="submit"><?= $estudiante_editar ? "Actualizar Datos" : "Registrar" ?></button>
            <?php if ($estudiante_editar): ?>
                <button type="button" class="btn-secondary" onclick="window.location.href='index.php'">Cancelar</button>
            <?php endif; ?>
        </form>
    </div>

    <div class="card">
        <h2>🔍 Sistema de Búsqueda Avanzada</h2>
        <form method="GET">
            <input name="buscar" style="width: 350px;" placeholder="Filtrar por coincidencia de nombre o carrera..." value="<?= htmlspecialchars($buscar) ?>">
            <button type="submit" class="btn-search">Ejecutar Filtro</button>
            <?php if (!empty($buscar)): ?>
                <button type="button" class="btn-secondary" onclick="window.location.href='index.php'">Limpiar Vista</button>
            <?php endif; ?>
        </form>
    </div>

    <h2>Estudiantes en el Sistema Cloud (Métricas encontradas: <?= $total_registros ?>)</h2>
    <table>
        <thead>
            <tr>
                <th style="width: 80px;">ID</th>
                <th>Nombre Completo</th>
                <th>Email</th>
                <th>Carrera Profesional</th>
                <th style="width: 180px;">Acciones Operativas</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($estudiantes) > 0): ?>
                <?php foreach ($estudiantes as $e): ?>
                    <tr>
                        <td><?= $e['id'] ?></td>
                        <td><?= htmlspecialchars($e['nombre']) ?></td>
                        <td><?= htmlspecialchars($e['email']) ?></td>
                        <td><?= htmlspecialchars($e['carrera']) ?></td>
                        <td class="actions">
                            <a class="link-edit" href="?edit=<?= $e['id'] ?><?= !empty($buscar) ? '&buscar='.urlencode($buscar) : '' ?>">Editar</a>
                            <a class="link-delete" href="?delete=<?= $e['id'] ?><?= !empty($buscar) ? '&buscar='.urlencode($buscar) : '' ?>" onclick="return confirm('¿Confirmar la eliminación permanente de este estudiante del servidor cloud?')">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align: center; color: #868e96; padding: 30px;">Ningún registro coincide con los parámetros solicitados.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if ($total_paginas > 1): ?>
        <div class="pagination-container">
            <a class="<?= ($pagina <= 1) ? 'disabled' : '' ?>" 
               href="?pagina=<?= $pagina - 1 ?><?= !empty($buscar) ? '&buscar='.urlencode($buscar) : '' ?>">Anterior</a>
            
            <span class="pagination-info">Página <?= $pagina ?> de <?= $total_paginas ?></span>
            
            <a class="<?= ($pagina >= $total_paginas) ? 'disabled' : '' ?>" 
               href="?pagina=<?= $pagina + 1 ?><?= !empty($buscar) ? '&buscar='.urlencode($buscar) : '' ?>">Siguiente</a>
        </div>
    <?php endif; ?>

</body>
</html>
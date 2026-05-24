<?php
// index.php
require_once 'config-database.php';
$pdo = getDBConnection();

$error_message = "";
$estudiante_editar = null;

$buscar = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $nombre = trim($_POST['nombre']);
        $email = trim($_POST['email']);

        if (strlen($nombre) < 3 || strlen($nombre) > 100) {
            $error_message = "Error: El nombre debe tener entre 3 y 100 caracteres.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_message = "Error: El formato del correo electrónico no es válido.";
        } else {
            if ($_POST['action'] === 'create') {
                $stmt = $pdo->prepare("INSERT INTO estudiantes (nombre, email) VALUES (?, ?)");
                $stmt->execute([$nombre, $email]);
                header("Location: index.php" . (!empty($buscar) ? "?buscar=" . urlencode($buscar) : ""));
                exit;
            } elseif ($_POST['action'] === 'update') {
                $id = intval($_POST['id']);
                $stmt = $pdo->prepare("UPDATE estudiantes SET nombre = ?, email = ? WHERE id = ?");
                $stmt->execute([$nombre, $email, $id]);
                header("Location: index.php" . (!empty($buscar) ? "?buscar=" . urlencode($buscar) : ""));
                exit;
            }
        }
    }
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $pdo->prepare("DELETE FROM estudiantes WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: index.php" . (!empty($buscar) ? "?buscar=" . urlencode($buscar) : ""));
    exit;
}

if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $stmt = $pdo->prepare("SELECT * FROM estudiantes WHERE id = ?");
    $stmt->execute([$id]);
    $estudiante_editar = $stmt->fetch();
}

$pagina = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
if ($pagina < 1) $pagina = 1;
$registros_por_pagina = 5;
$offset = ($pagina - 1) * $registros_por_pagina;

if (!empty($buscar)) {
    $stmt_count = $pdo->prepare("SELECT COUNT(*) FROM estudiantes WHERE nombre ILIKE ? OR email ILIKE ?");
    $stmt_count->execute(["%$buscar%", "%$buscar%"]);
    $total_registros = $stmt_count->fetchColumn();

    $stmt = $pdo->prepare("SELECT * FROM estudiantes WHERE nombre ILIKE ? OR email ILIKE ? ORDER BY id DESC LIMIT ? OFFSET ?");
    $stmt->bindValue(1, "%$buscar%", PDO::PARAM_STR);
    $stmt->bindValue(2, "%$buscar%", PDO::PARAM_STR);
    $stmt->bindValue(3, $registros_por_pagina, PDO::PARAM_INT);
    $stmt->bindValue(4, $offset, PDO::PARAM_INT);
    $stmt->execute();
    $estudiantes = $stmt->fetchAll();
} else {
    $total_registros = $pdo->query("SELECT COUNT(*) FROM estudiantes")->fetchColumn();
    $stmt = $pdo->prepare("SELECT * FROM estudiantes ORDER BY id DESC LIMIT ? OFFSET ?");
    $stmt->bindValue(1, $registros_por_pagina, PDO::PARAM_INT);
    $stmt->bindValue(2, $offset, PDO::PARAM_INT);
    $stmt->execute();
    $estudiantes = $stmt->fetchAll();
}

$total_paginas = ceil($total_registros / $registros_por_pagina);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Gestión de Estudiantes - UCV</title>
    <style>
        :root { --primary: #0d6efd; --primary-hover: #0b5ed7; --text: #212529; --bg: #f8f9fa; --border: #dee2e6; }
        body { font-family: 'Segoe UI', system-ui, sans-serif; margin: 40px auto; max-width: 1000px; padding: 0 20px; background-color: var(--bg); color: var(--text); line-height: 1.5; }
        h1, h2 { color: #0f2c59; border-bottom: 2px solid var(--border); padding-bottom: 10px; }
        .form-container { background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-bottom: 30px; border: 1px solid var(--border); }
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 20px; }
        .form-group { display: flex; flex-direction: column; }
        label { font-weight: 600; margin-bottom: 8px; color: #495057; }
        input[type="text"], input[type="email"] { padding: 10px 14px; border: 1px solid var(--border); border-radius: 4px; font-size: 15px; background-color: #fff; transition: border-color 0.2s; }
        input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(13,110,253,0.15); }
        .btn { padding: 10px 20px; border: none; border-radius: 4px; font-size: 15px; font-weight: 600; cursor: pointer; transition: background-color 0.2s; display: inline-block; text-align: center; text-decoration: none; }
        .btn-primary { background-color: var(--primary); color: white; }
        .btn-primary:hover { background-color: var(--primary-hover); }
        .btn-secondary { background-color: #6c757d; color: white; margin-left: 10px; }
        .btn-secondary:hover { background-color: #5c636a; }
        .error-banner { background-color: #f8d7da; color: #842029; padding: 12px 20px; border-radius: 4px; border: 1px solid #f5c2c7; margin-bottom: 20px; font-weight: 500; }
        .search-container { display: flex; gap: 10px; margin-bottom: 20px; background: #fff; padding: 15px; border-radius: 8px; border: 1px solid var(--border); }
        .search-container input { flex: 1; }
        table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid var(--border); margin-bottom: 20px; }
        th, td { padding: 14px 18px; text-align: left; border-bottom: 1px solid var(--border); }
        th { background-color: #0f2c59; color: white; font-weight: 600; }
        tr:hover { background-color: #f1f3f5; }
        .actions a { margin-right: 15px; font-weight: 600; text-decoration: none; font-size: 14px; }
        .link-edit { color: #ffc107; }
        .link-edit:hover { color: #d39e00; }
        .link-delete { color: #dc3545; }
        .link-delete:hover { color: #bd2130; }
        .pagination-container { display: flex; justify-content: space-between; align-items: center; margin-top: 20px; padding: 10px 0; }
        .pagination-container a { padding: 8px 16px; background: #fff; border: 1px solid var(--border); border-radius: 4px; color: var(--primary); text-decoration: none; font-weight: 600; }
        .pagination-container a.disabled { color: #ced4da; pointer-events: none; background: #e9ecef; }
    </style>
</head>
<body>

    <h1>🎓 Sistema de Gestión de Estudiantes - UCV Cloud</h1>

    <?php if (!empty($error_message)): ?>
        <div class="error-banner"><?= htmlspecialchars($error_message) ?></div>
    <?php endif; ?>

    <div class="form-container">
        <h2><?= $estudiante_editar ? "📝 Editar Estudiante" : "➕ Registrar Nuevo Estudiante" ?></h2>
        <form action="index.php<?= !empty($buscar) ? '?buscar='.urlencode($buscar) : '' ?>" method="POST">
            <input type="hidden" name="action" value="<?= $estudiante_editar ? 'update' : 'create' ?>">
            <?php if ($estudiante_editar): ?>
                <input type="hidden" name="id" value="<?= $estudiante_editar['id'] ?>">
            <?php endif; ?>

            <div class="form-grid">
                <div class="form-group">
                    <label for="nombre">Nombre Completo del Estudiante:</label>
                    <input type="text" id="nombre" name="nombre" required 
                           value="<?= $estudiante_editar ? htmlspecialchars($estudiante_editar['nombre']) : '' ?>" 
                           placeholder="Ej. Juan Pérez">
                </div>
                <div class="form-group">
                    <label for="email">Correo Institucional:</label>
                    <input type="email" id="email" name="email" required 
                           value="<?= $estudiante_editar ? htmlspecialchars($estudiante_editar['email']) : '' ?>" 
                           placeholder="Ej. juan@ucvvirtual.edu.pe">
                </div>
            </div>

            <button type="submit" class="btn btn-primary"><?= $estudiante_editar ? "Actualizar Registro" : "Guardar en Servidor Cloud" ?></button>
            <?php if ($estudiante_editar): ?>
                <a href="index.php<?= !empty($buscar) ? '?buscar='.urlencode($buscar) : '' ?>" class="btn btn-secondary">Cancelar Edición</a>
            <?php endif; ?>
        </form>
    </div>

    <h2>📋 Listado de Estudiantes Registrados</h2>

    <form method="GET" action="index.php" class="search-container">
        <input type="text" name="buscar" value="<?= htmlspecialchars($buscar) ?>" placeholder="Buscar por nombre o correo electrónico institucional...">
        <button type="submit" class="btn btn-primary">Buscar</button>
        <?php if (!empty($buscar)): ?>
            <a href="index.php" class="btn btn-secondary">Limpiar</a>
        <?php endif; ?>
    </form>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre Completo</th>
                <th>Correo Electrónico</th>
                <th>Fecha Registro</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($estudiantes) > 0): ?>
                <?php foreach ($estudiantes as $e): ?>
                    <tr>
                        <td><?= htmlspecialchars($e['id']) ?></td>
                        <td><?= htmlspecialchars($e['nombre']) ?></td>
                        <td><?= htmlspecialchars($e['email']) ?></td>
                        <td><?= htmlspecialchars($e['creado_en'] ?? 'No registrada') ?></td>
                        <td class="actions">
                            <a class="link-edit" href="?edit=<?= $e['id'] ?><?= !empty($buscar) ? '&buscar='.urlencode($buscar) : '' ?>">Editar</a>
                            <a class="link-delete" href="?delete=<?= $e['id'] ?><?= !empty($buscar) ? '&buscar='.urlencode($buscar) : '' ?>" onclick=\"return confirm('¿Confirmar la eliminación permanente?')\">Eliminar</a>
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
            <a class=\"<?= ($pagina <= 1) ? 'disabled' : '' ?>\" href=\"?pagina=<?= $pagina - 1 ?><?= !empty($buscar) ? '&buscar='.urlencode($buscar) : '' ?>\">Anterior</a>
            <span class="pagination-info">Página <?= $pagina ?> de <?= $total_paginas ?></span>
            <a class=\"<?= ($pagina >= $total_paginas) ? 'disabled' : '' ?>\" href=\"?pagina=<?= $pagina + 1 ?><?= !empty($buscar) ? '&buscar='.urlencode($buscar) : '' ?>\">Siguiente</a>
        </div>
    <?php endif; ?>

</body>
</html>
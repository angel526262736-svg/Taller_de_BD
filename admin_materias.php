<?php
session_start();
require 'config.php';

// Check session authorization first
if (!isset($_SESSION['carrera_id'])) {
    $_SESSION['error'] = "Acceso no autorizado. Por favor, inicia sesión.";
    header("Location: admin.php");
    exit;
}

// Set carrera_id from session
$carrera_id = $_SESSION['carrera_id'];

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle password verification for login
    if (isset($_POST['carrera_id'], $_POST['password'])) {
        $carrera_id_input = filter_var($_POST['carrera_id'], FILTER_VALIDATE_INT);
        $password = $_POST['password'];

        if ($carrera_id_input === false || $carrera_id_input <= 0) {
            $_SESSION['error'] = "ID de carrera inválido.";
            header("Location: admin.php");
            exit;
        }

        // Fetch password hash from database
        $stmt = $pdo->prepare("SELECT password_hash FROM carreras WHERE id = :id");
        $stmt->execute(['id' => $carrera_id_input]);
        $hash = $stmt->fetchColumn();

        if ($hash === false) {
            $_SESSION['error'] = "Carrera no encontrada.";
            header("Location: admin.php");
            exit;
        }

        // Verify password
        if (password_verify($password, $hash)) {
            $_SESSION['carrera_id'] = $carrera_id_input;
            $_SESSION['success'] = "Inicio de sesión exitoso.";
            header("Location: admin_materias.php"); // Redirect to refresh
            exit;
        } else {
            $_SESSION['error'] = "Contraseña incorrecta.";
            header("Location: admin.php");
            exit;
        }
    }

    // Handle materia update
    if (isset($_POST['update_materia'], $_POST['materia_id'], $_POST['nombre'], $_POST['descripcion'])) {
        $materia_id = filter_var($_POST['materia_id'], FILTER_VALIDATE_INT);
        $nombre = trim($_POST['nombre']);
        $descripcion = trim($_POST['descripcion']);

        if ($materia_id === false || $materia_id <= 0 || empty($nombre)) {
            $_SESSION['error'] = "Datos de materia inválidos.";
            header("Location: admin_materias.php");
            exit;
        }

        $stmt = $pdo->prepare("UPDATE materias SET nombre = :nombre, descripcion = :descripcion WHERE id = :id AND carrera_id = :carrera_id");
        $stmt->execute([
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'id' => $materia_id,
            'carrera_id' => $carrera_id
        ]);
        $_SESSION['success'] = "Materia actualizada.";
        header("Location: admin_materias.php");
        exit;
    }

    // Handle new materia addition
    if (isset($_POST['add_materia'], $_POST['nombre_nuevo'], $_POST['descripcion_nuevo'])) {
        $nombre = trim($_POST['nombre_nuevo']);
        $descripcion = trim($_POST['descripcion_nuevo']);

        if (empty($nombre)) {
            $_SESSION['error'] = "El nombre de la materia es obligatorio.";
            header("Location: admin_materias.php");
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO materias (carrera_id, nombre, descripcion) VALUES (:carrera_id, :nombre, :descripcion)");
        $stmt->execute([
            'carrera_id' => $carrera_id,
            'nombre' => $nombre,
            'descripcion' => $descripcion
        ]);
        $_SESSION['success'] = "Nueva materia agregada.";
        header("Location: admin_materias.php");
        exit;
    }
}

// List materias for the carrera
$stmt = $pdo->prepare("SELECT * FROM materias WHERE carrera_id = :carrera_id");
$stmt->execute(['carrera_id' => $carrera_id]);
$materias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Materias</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .message { padding: 10px; margin-bottom: 10px; }
        .success { background-color: #d4edda; color: #155724; }
        .error { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <h1>Editar Materias de la Carrera</h1>
    <?php
    // Display flash messages
    if (isset($_SESSION['success'])) {
        echo "<div class='message success'>" . htmlspecialchars($_SESSION['success']) . "</div>";
        unset($_SESSION['success']);
    }
    if (isset($_SESSION['error'])) {
        echo "<div class='message error'>" . htmlspecialchars($_SESSION['error']) . "</div>";
        unset($_SESSION['error']);
    }
    ?>

    <?php if (empty($materias)): ?>
        <p>No hay materias registradas para esta carrera.</p>
    <?php else: ?>
        <?php foreach ($materias as $materia): ?>
            <form method="POST">
                <input type="hidden" name="materia_id" value="<?php echo htmlspecialchars($materia['id']); ?>">
                <label>Nombre:</label>
                <input type="text" name="nombre" value="<?php echo htmlspecialchars($materia['nombre']); ?>" required><br>
                <label>Descripción:</label>
                <textarea name="descripcion"><?php echo htmlspecialchars($materia['descripcion']); ?></textarea><br>
                <button type="submit" name="update_materia">Actualizar</button>
            </form>
            <hr>
        <?php endforeach; ?>
    <?php endif; ?>

    <h2>Agregar Nueva Materia</h2>
    <form method="POST">
        <label>Nombre:</label>
        <input type="text" name="nombre_nuevo" required><br>
        <label>Descripción:</label>
        <textarea name="descripcion_nuevo"></textarea><br>
        <button type="submit" name="add_materia">Agregar</button>
    </form>

    <p><a href="admin.php">Cambiar Carrera</a> | <a href="index.php">Volver</a></p>
</body>
</html>
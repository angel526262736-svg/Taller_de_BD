
<?php
session_start();
require 'config.php';

// Handle password reset
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_password'])) {
    $carrera_id = $_POST['carrera_id'];
    $new_password = $_POST['new_password'];

    // Validate input
    if (empty($carrera_id) || empty($new_password)) {
        $error = "Por favor, completa todos los campos.";
    } else {
        // Check if carrera exists
        $stmt = $pdo->prepare("SELECT id FROM carreras WHERE id = :id");
        $stmt->execute(['id' => $carrera_id]);
        if ($stmt->fetch()) {
            // Update password
            $stmt = $pdo->prepare("UPDATE carreras SET password_hash = :password_hash WHERE id = :id");
            $stmt->execute([
                'password_hash' => hash('sha256', $new_password),
                'id' => $carrera_id
            ]);
            $success = "Contraseña actualizada correctamente.";
        } else {
            $error = "No se encontró una carrera con ese ID.";
        }
    }
}

// Handle new career addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_carrera'])) {
    $nombre = $_POST['nombre'];
    $password = $_POST['password'];

    // Validate input
    if (empty($nombre) || empty($password)) {
        $error = "Por favor, completa todos los campos.";
    } else {
        // Insert new career
        $stmt = $pdo->prepare("INSERT INTO carreras (nombre, password_hash) VALUES (:nombre, :password_hash)");
        $stmt->execute([
            'nombre' => $nombre,
            'password_hash' => hash('sha256', $password)
        ]);
        $success = "Carrera agregada correctamente.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Carreras</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Gestión de Carreras</h1>

        <!-- Password Reset Form -->
        <h2>Recuperar Contraseña de Carrera</h2>
        <form method="POST">
            <label for="carrera_id">ID de la Carrera:</label>
            <input type="number" id="carrera_id" name="carrera_id" required>
            <label for="new_password">Nueva Contraseña:</label>
            <input type="password" id="new_password" name="new_password" required>
            <button type="submit" name="reset_password">Restablecer Contraseña</button>
        </form>

        <!-- Add New Career Form -->
        <h2>Agregar Nueva Carrera</h2>
        <form method="POST">
            <label for="nombre">Nombre de la Carrera:</label>
            <input type="text" id="nombre" name="nombre" required>
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit" name="add_carrera">Agregar Carrera</button>
        </form>

        <!-- Display Messages -->
        <?php if (isset($error)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <p style="color: green;"><?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>

        <!-- Links -->
        <a href="admin.php">Volver a Administración de Materias</a> | 
      
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administración de Materias</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Administración de Materias por Carrera</h1>

        <!-- Formulario para seleccionar carrera y ingresar contraseña -->
        <form method="POST" action="admin_materias.php">
            <label for="carrera_id">Selecciona Carrera:</label>
            <select id="carrera_id" name="carrera_id" required>
                <?php
                require 'config.php';
                $stmt = $pdo->query("SELECT id, nombre FROM carreras");
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='{$row['id']}'>{$row['nombre']}</option>";
                }
                ?>
            </select>
            <br>
            <label for="password">Contraseña de la Carrera:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Acceder</button>
        </form>

        <a href="gestion_carreras.php">Gestionar Carreras</a> |
        

        
    </div>
    <a href="index.php">Salir</a>
</body>
</html>
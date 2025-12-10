<!-- admin.php: Parte para modificar información de materias, con contraseña por carrera -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administración de Materias</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
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

    <a href="index.php">Volver a Revisar Calificaciones</a>
    <a href="gestion_carreras.php">Abministrar contraseña</a>

</body>

</html>
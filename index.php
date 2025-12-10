<!-- index.php: Parte para que el usuario revise calificaciones con número de control -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Revisar Calificaciones</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Revisar Calificaciones</h1>
    <link rel="stylesheet" href="styles.css">
    <form method="POST">
        <label for="numero_control">ID Control:</label>
        <input type="text" id="numero_control" name="numero_control" required>
        <button type="submit">Revisar</button>
    </form>

    <?php
    require 'config.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $numero_control = $_POST['numero_control'];

        // Consulta para obtener calificaciones
        $stmt = $pdo->prepare("
            SELECT m.nombre AS materia, c.calificacion
            FROM calificaciones c
            JOIN materias m ON c.materia_id = m.id
            WHERE c.numero_control = :numero_control
        ");
        $stmt->execute(['numero_control' => $numero_control]);
        $calificaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($calificaciones) {
            echo "<h2>Calificaciones para $numero_control:</h2>";
            echo "<ul>";
            foreach ($calificaciones as $cal) {
                echo "<li>{$cal['materia']}: {$cal['calificacion']}</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>No se encontraron calificaciones para este número de control.</p>";
        }
    }
    ?>

    <a href="admin.php">Ir a Administración de Materias</a>
    <link rel="stylesheet" href="styles.css">
</body>
</html>
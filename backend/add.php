<?php
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $descripcion = $conn->real_escape_string($_POST['descripcion']);

    $sql = "INSERT INTO Items (nombre, descripcion) VALUES ('$nombre', '$descripcion')";
    if ($conn->query($sql)) {
        echo json_encode([
            'success' => true,
            'id' => $conn->insert_id,
            'nombre' => $nombre,
            'descripcion' => $descripcion
        ]);
    } else {
        echo json_encode(['success' => false]);
    }
}
?>

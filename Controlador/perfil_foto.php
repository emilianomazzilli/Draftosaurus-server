<?php
require_once __DIR__ . '/../Modelo/conexion.php';

if (!isset($_SESSION['user_id'])) {
    echo 'No logueado';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['foto'])) {
    $userId = $_SESSION['user_id'];
    $file = $_FILES['foto'];
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (!in_array(strtolower($ext), $allowed)) {
        echo 'Formato no permitido';
        exit;
    }

    $dest = '../img/perfil_' . $userId . '.' . $ext;
    if (move_uploaded_file($file['tmp_name'], $dest)) {
        // Guardar ruta en la base de datos
        $stmt = $con->prepare("UPDATE usuario SET foto = ? WHERE id = ?");
        $stmt->bind_param('si', $dest, $userId);
        $stmt->execute();
        $stmt->close();
        echo $dest;
    } else {
        echo 'Error al subir';
    }
    exit;
}
echo 'Petición inválida';

<?php
require_once __DIR__ . '/../Modelo/conexion.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Comprueba sesión
if (!isset($_SESSION['user_id'])) {
    echo 'No logueado';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['foto'])) {
    echo 'Petición inválida';
    exit;
}

$file = $_FILES['foto'];

// Errores comunes de upload
if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
    echo 'Error al subir (upload error).';
    exit;
}

// Límite tamaño 3MB
$maxBytes = 3 * 1024 * 1024;
if ($file['size'] > $maxBytes) {
    echo 'La imagen no debe superar 3MB.';
    exit;
}

// Comprueba MIME real con finfo
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime  = $finfo->file($file['tmp_name']);
$allowedMimes = [
    'image/jpeg' => 'jpg',
    'image/pjpeg' => 'jpg',
    'image/png'  => 'png',
    'image/gif'  => 'gif',
    'image/webp' => 'webp',
    'image/avif' => 'avif',
];
if (!array_key_exists($mime, $allowedMimes)) {
    echo 'Formato no permitido';
    exit;
}

$ext = $allowedMimes[$mime];
$userId = (int) $_SESSION['user_id'];

// Obtener foto actual del usuario (para eliminarla después si procede)
$oldFoto = null;
$stmtSel = $con->prepare("SELECT foto FROM usuario WHERE id = ? LIMIT 1");
if ($stmtSel) {
    $stmtSel->bind_param('i', $userId);
    $stmtSel->execute();
    $res = $stmtSel->get_result();
    if ($res) {
        $row = $res->fetch_assoc();
        $oldFoto = $row['foto'] ?? null;
    }
    $stmtSel->close();
}

// Asegura existencia del directorio img/perfiles de forma robusta
$imgBase = realpath(__DIR__ . '/../img') ?: (__DIR__ . '/../img');
if (!is_dir($imgBase)) {
    if (!mkdir($imgBase, 0755, true)) {
        echo 'Error interno: no se pudo crear directorio.';
        exit;
    }
}
$dir = rtrim($imgBase, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'perfiles';
if (!is_dir($dir)) {
    if (!mkdir($dir, 0755, true)) {
        echo 'Error interno: no se pudo crear directorio.';
        exit;
    }
}
$dir = rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

// Nombre seguro y único
try {
    $random = bin2hex(random_bytes(6));
} catch (Exception $e) {
    $random = substr(md5(uniqid((string)time(), true)), 0, 12);
}
$filename = sprintf('perfil_%d_%s.%s', $userId, $random, $ext);
$targetPath = $dir . $filename;

// Mover archivo
if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
    echo 'Error al mover el archivo.';
    exit;
}

// Ajustar permisos (opcional)
@chmod($targetPath, 0644);

/* ------------------ CAMBIO: usar nombre de usuario en el filename ------------------ */
// Obtener nombre base (preferir sesión, fallback a BD o userid)
$rawName = $_SESSION['usuario'] ?? $_SESSION['username'] ?? null;
if (!$rawName) {
    // Si no está en sesión, intentar leer de la BD (silencioso)
    $stmtN = $con->prepare("SELECT usuario, email FROM usuario WHERE id = ? LIMIT 1");
    if ($stmtN) {
        $stmtN->bind_param('i', $userId);
        $stmtN->execute();
        $resN = $stmtN->get_result();
        if ($resN && ($rN = $resN->fetch_assoc())) {
            $rawName = $rN['usuario'] ?? $rN['email'] ?? null;
        }
        $stmtN->close();
    }
}

// slugify simple y seguro
$slug = 'user' . $userId;
if (!empty($rawName)) {
    $s = (string)$rawName;
    if (function_exists('iconv')) {
        $trans = @iconv('UTF-8', 'ASCII//TRANSLIT', $s);
        if ($trans !== false) $s = $trans;
    }
    $s = preg_replace('/[^A-Za-z0-9 _-]/u', '', $s);
    $s = strtolower($s);
    $s = preg_replace('/[ _-]+/', '-', $s);
    $s = trim($s, '-');
    $s = substr($s, 0, 30);
    if ($s !== '') $slug = $s . '_' . $userId;
}

// Nuevo nombre deseado
$newFilename = $slug . '.' . $ext;
$newTargetPath = $dir . $newFilename;

// Si ya existe, añadir sufijo numérico para evitar colisiones
$counter = 1;
while (is_file($newTargetPath)) {
    $newFilename = $slug . '_' . $counter . '.' . $ext;
    $newTargetPath = $dir . $newFilename;
    $counter++;
}

// Renombrar el archivo físicamente (si el rename falla, mantener el original)
if ($newTargetPath !== $targetPath) {
    if (!@rename($targetPath, $newTargetPath)) {
        // no se pudo renombrar; conservar el nombre original
        $newTargetPath = $targetPath;
        $newFilename = basename($targetPath);
    } else {
        // ajustar permisos al nuevo fichero
        @chmod($newTargetPath, 0644);
    }
}

// Actualizar variables que usarás para BD y respuesta
$targetPath = $newTargetPath;
$filename = $newFilename;
$dbPath = 'img/perfiles/' . $filename;

$echoPath = '../' . $dbPath;
/* ------------------------------------------------------------------------------- */

// Actualizar BD
$stmt = $con->prepare("UPDATE usuario SET foto = ? WHERE id = ?");
if (!$stmt) {
    @unlink($targetPath);
    echo 'Error interno.';
    exit;
}
$stmt->bind_param('si', $dbPath, $userId);
if (!$stmt->execute()) {
    @unlink($targetPath);
    $stmt->close();
    echo 'Error al guardar en la base de datos.';
    exit;
}
$stmt->close();

// Si había foto anterior y es distinta, eliminar fichero antiguo (con comprobaciones de seguridad)
if ($oldFoto && $oldFoto !== $dbPath) {
    $oldBasename = basename($oldFoto);
    // aceptar patrones como: perfil_12_abcd.jpg  OR  gonza_12.jpg  OR  gonza-12_1.jpg
    if ($oldBasename !== '' && preg_match('/^[A-Za-z0-9\-]+_[0-9]+(?:_[0-9]+)?\.[A-Za-z0-9]+$/', $oldBasename)) {
        $oldFull = $dir . $oldBasename;
        $realDir = realpath($dir);
        $realOld = realpath($oldFull);
        if ($realDir && $realOld && strpos($realOld, $realDir) === 0 && is_file($realOld)) {
            // No borrar si por alguna razón coincide con el archivo que acabamos de subir/renombrar
            if (realpath($targetPath) !== $realOld) {
                @unlink($realOld);
            }
        }
    }
}

// Actualizar sesión de forma segura
$_SESSION['foto'] = $dbPath;

// Responder con la ruta relativa que espera el cliente
echo $echoPath;
exit;

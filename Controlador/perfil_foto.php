<?php
// Subir/renombrar foto de perfil usando el nombre de usuario (slug_userid.ext)
// Requisitos: conexion.php debe definir $con y manejar session_start()

require_once __DIR__ . '/../Modelo/conexion.php';

// Seguridad / comprobaciones básicas
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo 'No autenticado';
    exit;
}
$userId = (int) $_SESSION['user_id'];

if (!isset($_FILES['foto']) || !is_uploaded_file($_FILES['foto']['tmp_name'])) {
    echo 'No hay archivo subido';
    exit;
}

$file = $_FILES['foto'];
$tmp  = $file['tmp_name'];

// Validar tipo de imagen (exif_imagetype es fiable)
$imgType = @exif_imagetype($tmp);
if ($imgType === false) {
    echo 'Formato no permitido';
    exit;
}
$ext = image_type_to_extension($imgType, false); // p.ej. "jpeg","png"
if ($ext === 'jpeg') $ext = 'jpg';
$ext = strtolower(preg_replace('/[^a-z0-9]+/','',$ext));

// Limitar tamaño (opcional)
$maxBytes = 3 * 1024 * 1024;
if ($file['size'] > $maxBytes) {
    echo 'La imagen no debe superar 3MB';
    exit;
}

// Directorio destino
$dir = __DIR__ . '/../img/perfiles/';
if (!is_dir($dir)) {
    if (!@mkdir($dir, 0755, true)) {
        echo 'No se pudo crear el directorio';
        exit;
    }
}

// Obtener nombre base: preferir POST usuario -> session -> DB
$rawName = trim((string)($_POST['usuario'] ?? ($_SESSION['usuario'] ?? '')));

if ($rawName === '') {
    // intentar leer de BD
    $stmtN = $con->prepare("SELECT usuario, email FROM usuario WHERE id = ? LIMIT 1");
    if ($stmtN) {
        $stmtN->bind_param('i', $userId);
        $stmtN->execute();
        $resN = $stmtN->get_result();
        if ($resN && ($r = $resN->fetch_assoc())) {
            $rawName = $r['usuario'] ?? $r['email'] ?? '';
        }
        $stmtN->close();
    }
}

// Slugify simple y seguro
$slug = 'user' . $userId;
if ($rawName !== '') {
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

// Generar nombre final evitando colisiones
$filename = $slug . '.' . $ext;
$targetPath = $dir . $filename;
$counter = 1;
while (is_file($targetPath)) {
    $filename = $slug . '_' . $counter . '.' . $ext;
    $targetPath = $dir . $filename;
    $counter++;
}

// Mover archivo subido al destino final
if (!move_uploaded_file($tmp, $targetPath)) {
    echo 'Error al mover el archivo';
    exit;
}
@chmod($targetPath, 0644);

// Obtener foto antigua para eliminarla (si existe y distinta)
$oldFoto = null;
if (isset($_SESSION['foto']) && trim($_SESSION['foto']) !== '') {
    $oldFoto = trim($_SESSION['foto']);
} else {
    $stmtF = $con->prepare("SELECT foto FROM usuario WHERE id = ? LIMIT 1");
    if ($stmtF) {
        $stmtF->bind_param('i', $userId);
        $stmtF->execute();
        $resF = $stmtF->get_result();
        if ($resF && ($rF = $resF->fetch_assoc())) {
            $oldFoto = $rF['foto'] ?? null;
        }
        $stmtF->close();
    }
}

// Ruta a guardar en BD y a devolver al cliente
$dbPath   = 'img/perfiles/' . $filename;    // guardado en BD
$echoPath = '../' . $dbPath;                // ruta que devuelve al cliente

// Actualizar BD: foto y opcionalmente usuario/email recibidos en POST
$inputName  = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';
$inputEmail = isset($_POST['email'])   ? trim($_POST['email'])   : '';

try {
    $stmt = $con->prepare("
        UPDATE usuario
        SET foto = ?,
            usuario = COALESCE(NULLIF(?,''), usuario),
            email  = COALESCE(NULLIF(?,''), email)
        WHERE id = ?
    ");
    if ($stmt === false) throw new Exception('prepare falla');
    $stmt->bind_param('sssi', $dbPath, $inputName, $inputEmail, $userId);
    $stmt->execute();
    $stmt->close();

    // Eliminar foto antigua si existía y es distinta del nuevo path
    if (!empty($oldFoto) && basename($oldFoto) !== basename($dbPath)) {
        $oldBasename = basename($oldFoto);
        // seguridad: aceptar solo nombres simples y evitar traversal
        if (preg_match('/^[A-Za-z0-9\-\._]+$/', $oldBasename)) {
            $oldFull = $dir . $oldBasename;
            $realDir = realpath($dir);
            $realOld = realpath($oldFull);
            if ($realDir && $realOld && strpos($realOld, $realDir) === 0 && is_file($realOld)) {
                @unlink($realOld);
            }
        }
    }

    // Actualizar sesión
    $_SESSION['foto'] = $dbPath;
    if ($inputName !== '')  $_SESSION['usuario'] = $inputName;
    if ($inputEmail !== '') $_SESSION['email']   = $inputEmail;

    // Responder con la ruta que espera el frontend
    echo $echoPath;
    exit;
} catch (mysqli_sql_exception $e) {
    error_log('DB error (perfil_foto.php): ' . $e->getMessage());
    echo 'Error al guardar en la base de datos';
    exit;
} catch (Exception $e) {
    error_log('Error (perfil_foto.php): ' . $e->getMessage());
    echo 'Error interno';
    exit;
}
?>
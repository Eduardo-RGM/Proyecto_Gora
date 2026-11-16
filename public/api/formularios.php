<?php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Solo se permite POST.']);
    exit;
}

require_once __DIR__ . '/../../admin/models/sistema.class.php';

$tipo = $_POST['tipo'] ?? '';
$nombre = trim($_POST['nombre'] ?? '');
$correo = trim($_POST['correo'] ?? '');
$mensaje = trim($_POST['mensaje'] ?? '');

if ($nombre === '' || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode(['error' => 'Escribe un nombre y un correo válido.']);
    exit;
}

$app = new sistema();
$asunto = '';
$contenido = '';

if ($tipo === 'contacto') {
    $telefono = trim($_POST['telefono'] ?? '');
    if ($mensaje === '') {
        http_response_code(422);
        echo json_encode(['error' => 'Escribe un mensaje.']);
        exit;
    }

    $asunto = 'Nuevo mensaje de contacto de GORA';
    $contenido = '<h2>Mensaje de contacto</h2>'
        . '<p><strong>Nombre:</strong> ' . htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') . '</p>'
        . '<p><strong>Correo:</strong> ' . htmlspecialchars($correo, ENT_QUOTES, 'UTF-8') . '</p>'
        . '<p><strong>Teléfono:</strong> ' . htmlspecialchars($telefono, ENT_QUOTES, 'UTF-8') . '</p>'
        . '<p><strong>Mensaje:</strong><br>' . nl2br(htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8')) . '</p>';
} elseif ($tipo === 'cotizacion') {
    $material = trim($_POST['material'] ?? '');
    $cantidad = trim($_POST['cantidad'] ?? '');
    if ($material === '' || !is_numeric($cantidad) || (float)$cantidad <= 0) {
        http_response_code(422);
        echo json_encode(['error' => 'Indica el material y una cantidad válida.']);
        exit;
    }

    $asunto = 'Nueva solicitud de cotización de GORA';
    $contenido = '<h2>Solicitud de cotización</h2>'
        . '<p><strong>Nombre:</strong> ' . htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') . '</p>'
        . '<p><strong>Correo:</strong> ' . htmlspecialchars($correo, ENT_QUOTES, 'UTF-8') . '</p>'
        . '<p><strong>Material:</strong> ' . htmlspecialchars($material, ENT_QUOTES, 'UTF-8') . '</p>'
        . '<p><strong>Cantidad:</strong> ' . htmlspecialchars($cantidad, ENT_QUOTES, 'UTF-8') . ' kg</p>'
        . '<p><strong>Mensaje:</strong><br>' . nl2br(htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8')) . '</p>';
} else {
    http_response_code(422);
    echo json_encode(['error' => 'Tipo de formulario no válido.']);
    exit;
}

if (SMTP_USER === '' || SMTP_PASS === '') {
    http_response_code(503);
    echo json_encode(['error' => 'El correo del servidor todavía no está configurado.']);
    exit;
}

if (!$app->enviarCorreo([SMTP_USER], $asunto, $contenido)) {
    http_response_code(502);
    echo json_encode(['error' => 'No fue posible enviar el formulario. Intenta más tarde.']);
    exit;
}

echo json_encode(['mensaje' => 'Tu mensaje fue enviado correctamente.']);

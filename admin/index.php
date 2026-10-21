<?php
    include_once __DIR__ . '/models/sistema.class.php';
    $app = new sistema();
    $app->checarRol('Administrador');
    if (isset($_SESSION['correo_login_enviado'])) {
        $alerta = $_SESSION['correo_login_enviado']
            ? ['tipo' => 'success', 'mensaje' => 'Inicio de sesión correcto. Se envió una notificación a tu correo.']
            : ['tipo' => 'warning', 'mensaje' => 'Inicio de sesión correcto, pero no se pudo enviar la notificación por correo.'];
        unset($_SESSION['correo_login_enviado']);
        include_once __DIR__ . '/../templates/rbac/alert.html';
    }
    include_once __DIR__ . '/../templates/admin/index.html';
?>
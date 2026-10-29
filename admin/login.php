<?php
    include_once __DIR__ . '/models/sistema.class.php';
    include_once __DIR__ . '/models/usuario/usuario.class.php';
    $app = new sistema();
    $appUsuario = new usuario();
    $accion = $_GET['accion'] ?? 'login';
    switch($accion){
        case 'validar':
            if(isset($_POST['enviar'])){
                $correo = trim($_POST['correo'] ?? '');
                $contrasena = $_POST['contrasena'] ?? '';
                if ($app->login($correo, $contrasena)) {
                    header('Location: index.php');
                    exit;
                }
            }
            $alerta['tipo'] = 'danger';
            $alerta['mensaje'] = 'Correo o contraseña incorrectos';
            require_once __DIR__ . '/../templates/rbac/alert.html';
            require_once __DIR__ . '/../templates/login/login.html';
            
            break;
        
        case 'logout':
            $app->logout();
            header('Location: login.php?accion=login&cerrada=1');
            exit;

        case 'login':
            default:
            if (isset($_GET['cerrada'])) {
                $alerta = [
                    'tipo' => 'success',
                    'mensaje' => 'Sesión cerrada correctamente.'
                ];
                require_once __DIR__ . '/../templates/rbac/alert.html';
            }
            require_once __DIR__ . '/../templates/login/login.html';
            break;

        case 'recuperar':
            require_once __DIR__ . '/../templates/login/_form_correo.html';
            break;

        case 'restablecer':
            $token = $_GET['token'] ?? '';
            if ($appUsuario->validarToken($token)) {
                require_once __DIR__ . '/../templates/login/_form_contraseña.html';
            } else {
                $alerta = ['tipo' => 'danger', 'mensaje' => 'El enlace no es válido o ya fue utilizado.'];
                require_once __DIR__ . '/../templates/rbac/alert.html';
                require_once __DIR__ . '/../templates/login/login.html';
            }
            break;
        
        case 'enviar_correo':
            if (isset($_POST['enviar'])) {
                $correoIngresado = trim($_POST['correo'] ?? '');
                if (filter_var($correoIngresado, FILTER_VALIDATE_EMAIL)) {
                    $token = bin2hex(random_bytes(32));
                    $enlace = 'http://' . $_SERVER['HTTP_HOST']
                        . '/appGora/admin/login.php?accion=restablecer&token='
                        . urlencode($token);
                    $contenido = '<p>Para restablecer tu contraseña de GORA, haz clic en el siguiente enlace:</p>'
                        . '<p><a href="' . htmlspecialchars($enlace, ENT_QUOTES, 'UTF-8') . '">'
                        . 'Restablecer contraseña</a></p>';

                    if (!$appUsuario->guardarToken($correoIngresado, $token)) {
                        $alerta = [
                            'tipo' => 'danger',
                            'mensaje' => 'No se encontró un usuario con ese correo en GORA.'
                        ];
                    } elseif (!$app->enviarCorreo([$correoIngresado], 'Restablecer contraseña | GORA', $contenido)) {
                        $alerta = [
                            'tipo' => 'danger',
                            'mensaje' => 'El usuario existe, pero no se pudo enviar el correo. Revisa la configuración SMTP.'
                        ];
                    } else {
                        $alerta = [
                            'tipo' => 'success',
                            'mensaje' => 'Se envió un enlace para restablecer la contraseña.'
                        ];
                    }
                } else {
                    $alerta = ['tipo' => 'danger', 'mensaje' => 'Correo inválido.'];
                }
            }
            require_once __DIR__ . '/../templates/rbac/alert.html';
            require_once __DIR__ . '/../templates/login/_form_correo.html';
            break;

        case 'restaurar':
            $token = $_POST['token'] ?? '';
            $contrasena = $_POST['contrasena'] ?? '';
            $confirmacion = $_POST['contrasena2'] ?? '';
            if ($contrasena === '' || $contrasena !== $confirmacion) {
                $alerta = ['tipo' => 'danger', 'mensaje' => 'Las contraseñas no coinciden.'];
                require_once __DIR__ . '/../templates/rbac/alert.html';
                require_once __DIR__ . '/../templates/login/_form_contraseña.html';
                break;
            }
            if ($appUsuario->actualizarContrasena($token, $contrasena)) {
                $alerta = ['tipo' => 'success', 'mensaje' => 'Contraseña actualizada correctamente.'];
                require_once __DIR__ . '/../templates/rbac/alert.html';
                require_once __DIR__ . '/../templates/login/login.html';
                break;
            }
            $alerta = ['tipo' => 'danger', 'mensaje' => 'El enlace no es válido o ya fue utilizado.'];
            require_once __DIR__ . '/../templates/rbac/alert.html';
            require_once __DIR__ . '/../templates/login/login.html';
            break;
    }
?>
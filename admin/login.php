<?php
    include_once __DIR__ . '/models/sistema.class.php';
    $app = new sistema();
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
            $alerta['tipo'] = 'success';
            $alerta['mensaje'] = 'Sesión cerrada correctamente';
            require_once __DIR__ . '/../templates/rbac/alert.html';
            require_once __DIR__ . '/../templates/login/login.html';
            exit;
            break;

        case 'login':
            default:
            require_once __DIR__ . '/../templates/login/login.html';
            break;

        case 'recuperar':
           require_once __DIR__ . '/../templates/login/_form_correo.html';
            break;

        case 'restablecer':
            require_once __DIR__ . '/../templates/login/_form_contraseña.html';
            break;
        
        case 'enviar_correo':
            if (isset($_POST['enviar'])) {
                $correoIngresado = trim($_POST['correo'] ?? '');
                if (!filter_var($correoIngresado, FILTER_VALIDATE_EMAIL)) {
                    $alerta['tipo'] = 'danger';
                    $alerta['mensaje'] = 'Correo inválido';
                    require_once __DIR__ . '/../templates/rbac/alert.html';
                }
            }
            require_once __DIR__ . '/../templates/login/_form_correo.html';
            break;
    }
?>
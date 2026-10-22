<?php

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;

    require_once __DIR__.'/config.php';
    class sistema{
        var $db;
        function __construct(){
                $this->db = new PDO(
                    'mysql:host='. DB_HOST .';dbname='. DB_NAME . ';charset=utf8mb4',
                    DB_USER,
                    DB_PASS,
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
                );
            }

            function cargarArchivo($nombre, $ruta){
                if (!isset($_FILES['data']['error'][$nombre]) ||
                    $_FILES['data']['error'][$nombre] === UPLOAD_ERR_NO_FILE) {
                    return null;
                }

                if ($_FILES['data']['error'][$nombre] !== UPLOAD_ERR_OK) {
                    return null;
                }

                $archivo = $_FILES['data'];
                $tmp = $archivo['tmp_name'][$nombre];
                $tamanoMaximo = 2 * 1024 * 1024;
                $extensiones = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];
                $tipos = [
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    'image/gif' => 'gif',
                    'image/webp' => 'webp',
                    'image/bmp' => 'bmp'
                ];
                $mime = (new finfo(FILEINFO_MIME_TYPE))->file($tmp);
                $extension = strtolower(pathinfo($archivo['name'][$nombre], PATHINFO_EXTENSION));

                if (!isset($tipos[$mime]) || !in_array($extension, $extensiones, true) ||
                    $archivo['size'][$nombre] > $tamanoMaximo || !is_uploaded_file($tmp)) {
                    return null;
                }

                $nombreNuevo = bin2hex(random_bytes(16)) . '.' . $tipos[$mime];
                $directorio = __DIR__ . $ruta;
                if (!is_dir($directorio) && !mkdir($directorio, 0755, true)) {
                    return null;
                }

                return move_uploaded_file($tmp, $directorio . $nombreNuevo) ? $nombreNuevo : null;
            }

            function validarTexto($data, $campo, $maximo, $obligatorio = true){
                $valor = trim((string)($data[$campo] ?? ''));
                if ($obligatorio && $valor === '') return "El campo {$campo} es obligatorio.";
                if ($valor !== '' && mb_strlen($valor) > $maximo) return "El campo {$campo} no puede superar {$maximo} caracteres.";
                return null;
            }

            function validarId($data, $campo){
                return filter_var($data[$campo] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) === false
                    ? "Selecciona un {$campo} válido." : null;
            }

            function validarFecha($data, $campo){
                $fecha = $data[$campo] ?? '';
                $valida = DateTime::createFromFormat('Y-m-d', $fecha);
                return !$valida || $valida->format('Y-m-d') !== $fecha || $fecha > date('Y-m-d')
                    ? 'La fecha no es válida o es posterior al día actual.' : null;
            }

            function validarCantidad($data, $campo){
                $valor = $data[$campo] ?? null;
                return filter_var($valor, FILTER_VALIDATE_FLOAT) === false ||
                    (float)$valor <= 0 ? 'La cantidad debe ser mayor que cero.' : null;
            }

            function validarEmail($data, $campo){
                $valor = trim((string)($data[$campo] ?? ''));
                return $valor !== '' && filter_var($valor, FILTER_VALIDATE_EMAIL) === false
                    ? 'El correo electrónico no es válido.' : null;
            }

            function login($correo, $contrasena){
                $correo = strtolower(trim($correo));
                if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                    $this->logout();
                    return false;
                }

                $stmt = $this->db->prepare(
                    "SELECT * FROM usuario
                     WHERE LOWER(TRIM(correo)) = :correo AND contrasena = :contrasena"
                );
                $stmt->execute([
                    ':correo' => $correo,
                    ':contrasena' => md5($contrasena)
                ]);
                $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$usuario) {
                    $this->logout();
                    return false;
                }

                session_regenerate_id(true);
                $_SESSION['correo'] = $correo;
                $_SESSION['id_usuario'] = $usuario['id_usuario'];
                $_SESSION['valido'] = true;
                $_SESSION['roles'] = $this->getRoles($correo);
                $_SESSION['permisos'] = $this->getPermisos($correo);
                $_SESSION['correo_login_enviado'] = $this->enviarCorreo(
                    [$correo],
                    'Inicio de sesión en GORA',
                    '<p>Se inició sesión en tu cuenta de GORA.</p><p>Si no reconoces este acceso, cambia tu contraseña y avisa al administrador.</p>'
                );
                return true;
            }

            function logout(){
                $_SESSION = [];
                if (ini_get('session.use_cookies')) {
                    $params = session_get_cookie_params();
                    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
                }
                session_destroy();
            }

            function checarRol($rol){
                if (isset($_SESSION['roles']) && in_array($rol, $_SESSION['roles'], true)) {
                    return true;
                }

                $this->logout();
                $alerta = [
                    'tipo' => 'danger',
                    'mensaje' => 'No tienes el rol para acceder a esta página. Inicia sesión con un usuario que tenga el rol adecuado.'
                ];
                require_once __DIR__ . '/../../templates/rbac/alert.html';
                require_once __DIR__ . '/../../templates/login/login.html';
                exit;
            }

            function getRoles($correo){
                $stmt = $this->db->prepare(
                    "SELECT r.rol FROM rol r
                     INNER JOIN usuario_rol ur ON r.id_rol = ur.id_rol
                     INNER JOIN usuario u ON ur.id_usuario = u.id_usuario
                     WHERE LOWER(TRIM(u.correo)) = :correo"
                );
                $stmt->execute([':correo' => $correo]);
                return $stmt->fetchAll(PDO::FETCH_COLUMN);
            }

            function getPermisos($correo){
                $stmt = $this->db->prepare(
                    "SELECT p.permiso FROM permiso p
                     INNER JOIN rol_permiso rp ON p.id_permiso = rp.id_permiso
                     INNER JOIN rol r ON rp.id_rol = r.id_rol
                     INNER JOIN usuario_rol ur ON r.id_rol = ur.id_rol
                     INNER JOIN usuario u ON ur.id_usuario = u.id_usuario
                     WHERE LOWER(TRIM(u.correo)) = :correo"
                );
                $stmt->execute([':correo' => $correo]);
                return $stmt->fetchAll(PDO::FETCH_COLUMN);
            }

            function enviarCorreo($destinatarios, $asunto, $contenido, $adjunto = null){
                if (SMTP_USER === '' || SMTP_PASS === '') {
                    return false;
                }

                require_once __DIR__ . '/../../../vendor/autoload.php';
                $mail = new PHPMailer();
                $mail->isSMTP();
                $mail->SMTPDebug = SMTP::DEBUG_OFF;
                $mail->Host = SMTP_HOST;
                $mail->Port = SMTP_PORT;
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->SMTPAuth = true;
                $mail->Username = SMTP_USER;
                $mail->Password = SMTP_PASS;
                $mail->setFrom(SMTP_USER, SMTP_FROM_NAME);

                foreach ($destinatarios as $destinatario) {
                    $mail->addAddress($destinatario);
                }

                $mail->Subject = $asunto;
                $mail->msgHTML($contenido);
                if ($adjunto !== null) {
                    $mail->addAttachment($adjunto);
                }

                return $mail->send();
            }
        }
?>
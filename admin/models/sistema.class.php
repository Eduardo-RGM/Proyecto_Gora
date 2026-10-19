<?php

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
        }
?>
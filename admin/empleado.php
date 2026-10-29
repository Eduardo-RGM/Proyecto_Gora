<?php
    include_once __DIR__ . '/models/empleado/empleado.class.php';
    $app = new empleado();
    $app->checarPermiso('Administrar empleados');
    $accion = $_GET['accion'] ?? 'read';
    $id = $_GET['id'] ?? null;
    $tituloPagina = 'Empleados';
    $descripcionPagina = 'Consulta y administra el personal de GORA.';
    include_once __DIR__ . '/../templates/admin/header.html';
    switch ($accion) {
        case 'create':
            if (isset($_POST['enviar'])) {
                $datos = $app->create($_POST['data']);
                if (isset($datos['errores'])) {
                    $errores = $datos['errores'];
                    $datos = $_POST['data'];
                    include_once __DIR__ . '/../templates/empleado/_form.html';
                } else {
                    $empleados = $app->getall();
                    include_once __DIR__ . '/../templates/empleado/index.html';
                }
            } else {
                include_once __DIR__ . '/../templates/empleado/_form.html';
            }
            break;
        case 'update':
            if (isset($_POST['enviar'])) {
                $resultado = $app->update($id, $_POST['data']);
                if (is_array($resultado) && isset($resultado['errores'])) {
                    $errores = $resultado['errores'];
                    $datos = array_merge($_POST['data'], ['id_empleado' => $id]);
                    include_once __DIR__ . '/../templates/empleado/_form.html';
                } else {
                    $empleados = $app->getall();
                    include_once __DIR__ . '/../templates/empleado/index.html';
                }
            } else {
                $datos = $app->getone($id);
                include_once __DIR__ . '/../templates/empleado/_form.html';
            }
            break;
        case 'delete':
            $app->delete($id);
            $empleados = $app->getall();
            include_once __DIR__ . '/../templates/empleado/index.html';
            break;
        default:
            $empleados = $app->getall();
            include_once __DIR__ . '/../templates/empleado/index.html';
    }
    include_once __DIR__ . '/../templates/admin/footer.html';
?>
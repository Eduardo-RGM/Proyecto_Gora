<?php
    include_once __DIR__ . '/models/cliente/cliente.class.php';
    $app = new cliente();
    $accion = $_GET['accion'] ?? 'read';
    $id = $_GET['id'] ?? null;
    $tituloPagina = 'Clientes';
    $descripcionPagina = 'Consulta y administra el registro de clientes.';
    include_once __DIR__ . '/../templates/admin/header.html';
    switch ($accion) {
        case 'create':
            if (isset($_POST['enviar'])) {
                $datos = $app->create($_POST['data']);
                if (isset($datos['errores'])) {
                    $errores = $datos['errores'];
                    $datos = $_POST['data'];
                    include_once __DIR__ . '/../templates/cliente/_form.html';
                } else {
                    $clientes = $app->getall();
                    include_once __DIR__ . '/../templates/cliente/index.html';
                }
            } else {
                include_once __DIR__ . '/../templates/cliente/_form.html';
            }
            break;
        case 'update':
            if (isset($_POST['enviar'])) {
                $resultado = $app->update($id, $_POST['data']);
                if (is_array($resultado) && isset($resultado['errores'])) {
                    $errores = $resultado['errores'];
                    $datos = array_merge($_POST['data'], ['id_cliente' => $id]);
                    include_once __DIR__ . '/../templates/cliente/_form.html';
                } else {
                    $clientes = $app->getall();
                    include_once __DIR__ . '/../templates/cliente/index.html';
                }
            } else {
                $datos = $app->getone($id);
                include_once __DIR__ . '/../templates/cliente/_form.html';
            }
            break;
        case 'delete':
            $app->delete($id);
            $clientes = $app->getall();
            include_once __DIR__ . '/../templates/cliente/index.html';
            break;
        default:
            $clientes = $app->getall();
            include_once __DIR__ . '/../templates/cliente/index.html';
    }
    include_once __DIR__ . '/../templates/admin/footer.html';
?>
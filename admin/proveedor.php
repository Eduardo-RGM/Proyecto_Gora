<?php
    include_once __DIR__ . '/models/proveedor/proveedor.class.php';
    $app = new proveedor();
    $accion = $_GET['accion'] ?? 'read';
    $id = $_GET['id'] ?? null;
    $tituloPagina = 'Proveedores';
    $descripcionPagina = 'Consulta y administra el registro de proveedores.';
    include_once __DIR__ . '/../templates/admin/header.html';
    switch ($accion) {
        case 'create':
            if(isset($_POST['enviar'])){
                $data=$_POST['data'];
                $datos = $app->create($data);
                if (isset($datos['errores'])) {
                    $errores = $datos['errores'];
                    $datos = $data;
                    include_once __DIR__ . '/../templates/proveedor/_form.html';
                } else {
                    $proveedores = $app->getall();
                    include_once __DIR__ . '/../templates/proveedor/index.html';
                }
            }else{
                include_once __DIR__ . '/../templates/proveedor/_form.html';
            }
            break;
        case 'update':
            if(isset($_POST['enviar'])){
                $data =$_POST['data'];
                $datos = $app->update($id,$data);
                if (is_array($datos) && isset($datos['errores'])) {
                    $errores = $datos['errores'];
                    $datos = array_merge($data, ['id_proveedor' => $id]);
                    include_once __DIR__ . '/../templates/proveedor/_form.html';
                } else {
                    $proveedores = $app->getall();
                    include_once __DIR__ . '/../templates/proveedor/index.html';
                }
            }else{
                $datos = $app->getone($id);
                include_once __DIR__ . '/../templates/proveedor/_form.html';
            }
            break;
        case 'delete':
            $afectadors = $app->delete($id);
            $proveedores = $app->getall();
            include_once __DIR__ . '/../templates/proveedor/index.html';
            break;
        
        default:
            case 'read':
                $proveedores = $app->getall();
                include_once __DIR__ . '/../templates/proveedor/index.html';
            //break;
    }
    include_once __DIR__ . '/../templates/admin/footer.html';
?>
<?php
    include_once __DIR__ . '/models/rol/rol.class.php';
    $app = new rol();
    $app ->checarPermiso('Administrar roles');
    $accion = $_GET['accion'] ?? 'read';
    $id = $_GET['id'] ?? null;
    include_once __DIR__ . '/../templates/admin/header.html';
    switch ($accion) {
        case 'create':
            if(isset($_POST['enviar'])){
                $data=$_POST['data'];
                $datos = $app->create($data);
                if (isset($datos['id_rol'])) {
                    $app->updatePermisos($datos['id_rol'], isset($data['id_permiso']) ? $data['id_permiso'] : []);
                }
                $roles = $app->getall();
                include_once __DIR__ . '/../templates/rol/index.html';
            }else{
                $permisos = $app->getAllPermisos();
                $datos['permisos_rol'] = [];
                include_once __DIR__ . '/../templates/rol/_form.html';
            }
            break;
        case 'update':
            if(isset($_POST['enviar'])){
                $data =$_POST['data'];
                $datos = $app->update($id,$data);
                $app->updatePermisos($id, isset($data['id_permiso']) ? $data['id_permiso'] : []);
                $roles = $app->getall();
                include_once __DIR__ . '/../templates/rol/index.html';
            }else{
                $datos = $app->getone($id);
                $datos['permisos_rol'] = $app->getPermisosRol($id);
                $permisos = $app->getAllPermisos();
                include_once __DIR__ . '/../templates/rol/_form.html';
            }
            break;
        case 'delete':
            $afectadors = $app->delete($id);
            $roles = $app->getall();
            include_once __DIR__ . '/../templates/rol/index.html';
            break;
        
        default:
            case 'read':
                $roles = $app->getall();
                include_once __DIR__ . '/../templates/rol/index.html';
            //break;
    }
    include_once __DIR__ . '/../templates/admin/footer.html';
?>

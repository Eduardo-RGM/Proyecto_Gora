<?php
    include_once __DIR__ . '/models/permiso/permiso.class.php';
    $app = new permiso();
    $app ->checarPermiso('Administrar permisos');
    $accion = $_GET['accion'] ?? 'read';
    $id = $_GET['id'] ?? null;
    include_once __DIR__ . '/../templates/admin/header.html';
    switch ($accion) {
        case 'create':
            if(isset($_POST['enviar'])){
                $data=$_POST['data'];
                $datos = $app->create($data);
                $permisos = $app->getall();
                include_once __DIR__ . '/../templates/permiso/index.html';
            }else{
                include_once __DIR__ . '/../templates/permiso/_form.html';
            }
            break;
        case 'update':
            if(isset($_POST['enviar'])){
                $data =$_POST['data'];
                $datos = $app->update($id,$data);
                $permisos = $app->getall();
                include_once __DIR__ . '/../templates/permiso/index.html';
            }else{
                $datos = $app->getone($id);
                include_once __DIR__ . '/../templates/permiso/_form.html';
            }
            break;
        case 'delete':
            $afectadors = $app->delete($id);
            $permisos = $app->getall();
            include_once __DIR__ . '/../templates/permiso/index.html';
            break;
        
        default:
            case 'read':
                $permisos = $app->getall();
                include_once __DIR__ . '/../templates/permiso/index.html';
            //break;
    }
    include_once __DIR__ . '/../templates/admin/footer.html';
?>

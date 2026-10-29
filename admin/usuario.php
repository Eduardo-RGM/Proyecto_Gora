<?php
    include_once __DIR__ . '/models/usuario/usuario.class.php';
    $app = new usuario();
    $app ->checarPermiso('Administrar usuarios');
    $accion = $_GET['accion'] ?? 'read';
    $id = $_GET['id'] ?? null;
    include_once __DIR__ . '/../templates/admin/header.html';
    switch ($accion) {
        case 'create':
            if(isset($_POST['enviar'])){
                $data=$_POST['data'];
                $datos = $app->create($data);
                if (isset($datos['error'])) {
                    $roles = $app->getAllRoles();
                    $datos['roles_usuario'] = [];
                    include_once __DIR__ . '/../templates/usuario/_form.html';
                    break;
                }
                if (isset($datos['id_usuario'])) {
                    $app->updateRoles($datos['id_usuario'], isset($data['id_rol']) ? $data['id_rol'] : []);
                }
                $alerta = $datos['correo_enviado']
                    ? ['tipo' => 'success', 'mensaje' => 'Usuario creado y correo de bienvenida enviado.']
                    : ['tipo' => 'warning', 'mensaje' => 'Usuario creado, pero no se pudo enviar el correo de bienvenida.'];
                include_once __DIR__ . '/../templates/rbac/alert.html';
                $usuarios = $app->getall();
                include_once __DIR__ . '/../templates/usuario/index.html';
            }else{
                $roles = $app->getAllRoles();
                $datos['roles_usuario'] = [];
                include_once __DIR__ . '/../templates/usuario/_form.html';
            }
            break;
        case 'update':
            if(isset($_POST['enviar'])){
                $data =$_POST['data'];
                $datos = $app->update($id,$data);
                $app->updateRoles($id, isset($data['id_rol']) ? $data['id_rol'] : []);
                $usuarios = $app->getall();
                include_once __DIR__ . '/../templates/usuario/index.html';
            }else{
                $datos = $app->getone($id);
                $datos['roles_usuario'] = $app->getRolesUsuario($id);
                $roles = $app->getAllRoles();
                include_once __DIR__ . '/../templates/usuario/_form.html';
            }
            break;
        case 'delete':
            $afectadors = $app->delete($id);
            $usuarios = $app->getall();
            include_once __DIR__ . '/../templates/usuario/index.html';
            break;
        
        default:
            case 'read':
                $usuarios = $app->getall();
                include_once __DIR__ . '/../templates/usuario/index.html';
            //break;
    }
    include_once __DIR__ . '/../templates/admin/footer.html';
?>

<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../models/proveedor/proveedor.class.php';
$app = new proveedor();
$accion = $_SERVER['REQUEST_METHOD'];
$id = $_GET['id'] ?? null;
$datos = [];
switch($accion){
    case 'POST':
        $data = $_POST['data'];
        if(isset($id) && is_numeric($id)){
            $datos['afectados'] = $app->update($id, $data);
            $datos['mensaje'] = 'Se ha modificado el registro: ' . $id;
            $datos['registro'] = $app->getone($id);
        }else{
            $datos['afectados'] = $app->create($data);
            $datos['mensaje'] = 'Se ha creado un nuevo registro';
        }
        break;
    case 'DELETE':
        if(isset($id) && is_numeric($id)){
            $datos['afectados'] = $app->delete($id);
            $datos['mensaje'] = 'Proveedor eliminado correctamente ' . $id;
        }
        break;
    case 'GET':
    default:
        if(isset($id) && is_numeric($id)){
            $datos = $app->getone($id);
        }else{
            $datos = $app->getall();
        }
}
echo json_encode($datos, JSON_PRETTY_PRINT);
?>
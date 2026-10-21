<?php
require_once __DIR__ . '/../sistema.class.php';

class rol extends sistema
{
    function create($data){
        $datos = [];
        if (!isset($data['rol'])) {
            $datos['error'] = 'Faltan datos requeridos';
            return $datos;
        }
        $sql = "INSERT INTO rol(rol) VALUES(:rol)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':rol', $data['rol'], PDO::PARAM_STR);
        $stmt->execute();
        $datos = $data;
        $datos['afectados'] = $stmt->rowCount();
        $datos['id_rol'] = $this->db->lastInsertId();
        return $datos;
    }

    function getAllPermisos()
    {
        $datos = [];
        $sql = "SELECT * FROM permiso";
        $stmt = $this->db->query($sql);
        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $datos;
    }

    function getPermisosRol($id_rol)
    {
        $datos = [];
        if (is_numeric($id_rol)) {
            $sql = "SELECT id_permiso FROM rol_permiso WHERE id_rol = :id_rol";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_rol', $id_rol, PDO::PARAM_INT);
            $stmt->execute();
            $datos = $stmt->fetchAll(PDO::FETCH_COLUMN);
        }
        return $datos;
    }

    function updatePermisos($id_rol, $permisos)
    {
        $afectados = 0;
        if (is_numeric($id_rol)) {
            $sql = "DELETE FROM rol_permiso WHERE id_rol = :id_rol";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_rol', $id_rol, PDO::PARAM_INT);
            $stmt->execute();
            $afectados = $stmt->rowCount();

            if (is_array($permisos)) {
                $sql = "INSERT INTO rol_permiso(id_rol, id_permiso) VALUES(:id_rol, :id_permiso)";
                foreach ($permisos as $id_permiso) {
                    if (is_numeric($id_permiso)) {
                        $stmt = $this->db->prepare($sql);
                        $stmt->bindParam(':id_rol', $id_rol, PDO::PARAM_INT);
                        $stmt->bindParam(':id_permiso', $id_permiso, PDO::PARAM_INT);
                        $stmt->execute();
                        $afectados += $stmt->rowCount();
                    }
                }
            }
        }
        return $afectados;
    }

    function getone($id){
        $datos = [];
        if (is_numeric($id)) {
            $sql = "SELECT * FROM rol WHERE id_rol = :id_rol";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_rol', $id, PDO::PARAM_INT);
            $stmt->execute();
            $datos = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return $datos;
    }

    function getall(){
        $datos = [];
        $sql = "SELECT * FROM rol";
        $stmt = $this->db->query($sql);
        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $datos;
    }

    function update($id, $data){
        $afectados = 0;
        if (is_numeric($id) && isset($data['rol'])) {
            $sql = "UPDATE rol SET rol = :rol WHERE id_rol = :id_rol";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':rol', $data['rol'], PDO::PARAM_STR);
            $stmt->bindParam(':id_rol', $id, PDO::PARAM_INT);
            $stmt->execute();
            $afectados = $stmt->rowCount();
        }
        return $afectados;
    }

    function delete($id){
        $afectados = 0;
        if (is_numeric($id)) {
            $sql = "DELETE FROM rol WHERE id_rol = :id_rol";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_rol', $id, PDO::PARAM_INT);
            $stmt->execute();
            $afectados = $stmt->rowCount();
        }
        return $afectados;
    }
}

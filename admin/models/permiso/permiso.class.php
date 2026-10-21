<?php
require_once __DIR__ . '/../sistema.class.php';

class permiso extends sistema
{
    function create($data)
    {
        $datos = [];
        if (!isset($data['permiso'])) {
            $datos['error'] = 'Faltan datos requeridos';
            return $datos;
        }

        $sql = "INSERT INTO permiso(permiso) VALUES(:permiso)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':permiso', $data['permiso'], PDO::PARAM_STR);
        $stmt->execute();

        $datos = $data;
        $datos['afectados'] = $stmt->rowCount();
        $datos['id_permiso'] = $this->db->lastInsertId();
        return $datos;
    }

    function getone($id)
    {
        $datos = [];
        if (is_numeric($id)) {
            $sql = "SELECT * FROM permiso WHERE id_permiso = :id_permiso";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_permiso', $id, PDO::PARAM_INT);
            $stmt->execute();
            $datos = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return $datos;
    }

    function getall()
    {
        $datos = [];
        $sql = "SELECT * FROM permiso";
        $stmt = $this->db->query($sql);
        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $datos;
    }

    function update($id, $data)
    {
        $afectados = 0;
        if (is_numeric($id) && isset($data['permiso'])) {
            $sql = "UPDATE permiso SET permiso = :permiso WHERE id_permiso = :id_permiso";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':permiso', $data['permiso'], PDO::PARAM_STR);
            $stmt->bindParam(':id_permiso', $id, PDO::PARAM_INT);
            $stmt->execute();
            $afectados = $stmt->rowCount();
        }
        return $afectados;
    }

    function delete($id)
    {
        $afectados = 0;
        if (is_numeric($id)) {
            $sql = "DELETE FROM permiso WHERE id_permiso = :id_permiso";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_permiso', $id, PDO::PARAM_INT);
            $stmt->execute();
            $afectados = $stmt->rowCount();
        }
        return $afectados;
    }
}

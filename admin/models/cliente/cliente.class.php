<?php
    require_once __DIR__ . '/../sistema.class.php';
    class cliente extends sistema {
        function create($data) {
            $errores = $this->validar($data);
            if ($errores) return ['errores' => $errores];
            $sql = "INSERT INTO cliente(nombre,telefono,direccion,correo) VALUES(:nombre,:telefono,:direccion,:correo)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':nombre' => $data['nombre'], ':telefono' => $data['telefono'],
                ':direccion' => $data['direccion'], ':correo' => $data['correo']
            ]);
            $data['id_cliente'] = $this->db->lastInsertId();
            return $data;
        }
        function getone($id) {
            if (!is_numeric($id)) return [];
            $stmt = $this->db->prepare("SELECT * FROM cliente WHERE id_cliente = :id");
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
        }
        function getall() {
            return $this->db->query("SELECT * FROM cliente")->fetchAll(PDO::FETCH_ASSOC);
        }
        function update($id, $data) {
            if (!is_numeric($id)) return 0;
            $errores = $this->validar($data);
            if ($errores) return ['errores' => $errores];
            $stmt = $this->db->prepare("UPDATE cliente SET nombre=:nombre, telefono=:telefono, direccion=:direccion, correo=:correo WHERE id_cliente=:id");
            $stmt->execute([
                ':nombre' => $data['nombre'], ':telefono' => $data['telefono'],
                ':direccion' => $data['direccion'], ':correo' => $data['correo'], ':id' => $id
            ]);
            return $stmt->rowCount();
        }
        private function validar($data) {
            $errores = [];
            foreach (['nombre' => 100, 'telefono' => 20, 'direccion' => 150] as $campo => $maximo) {
                $error = $this->validarTexto($data, $campo, $maximo);
                if ($error) $errores[] = $error;
            }
            $error = $this->validarEmail($data, 'correo');
            if ($error) $errores[] = $error;
            return $errores;
        }
        function delete($id) {
            if (!is_numeric($id)) return 0;
            $stmt = $this->db->prepare("DELETE FROM cliente WHERE id_cliente = :id");
            $stmt->execute([':id' => $id]);
            return $stmt->rowCount();
        }
    }
?>
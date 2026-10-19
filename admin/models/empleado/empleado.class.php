<?php
    require_once __DIR__ . '/../sistema.class.php';
    class empleado extends sistema {
        function create($data) {
            $errores = $this->validar($data);
            $foto = $this->cargarArchivo('foto', '/../../images/uploads/');
            if ($foto === null && isset($_FILES['data']['error']['foto']) &&
                $_FILES['data']['error']['foto'] !== UPLOAD_ERR_NO_FILE) {
                $errores[] = 'La foto no es válida (imagen de máximo 2 MB).';
            }
            if ($errores) return ['errores' => $errores];
            $sql = "INSERT INTO empleado(nombre,rfc,pri_apellido,seg_apellido,puesto,foto) VALUES(:nombre,:rfc,:pri_apellido,:seg_apellido,:puesto,:foto)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':nombre' => $data['nombre'], ':rfc' => $data['rfc'],
                ':pri_apellido' => $data['pri_apellido'], ':seg_apellido' => $data['seg_apellido'],
                ':puesto' => $data['puesto'], ':foto' => $foto
            ]);
            $data['id_empleado'] = $this->db->lastInsertId();
            $data['foto'] = $foto;
            return $data;
        }
        function getone($id) {
            if (!is_numeric($id)) return [];
            $stmt = $this->db->prepare("SELECT * FROM empleado WHERE id_empleado = :id");
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
        }
        function getall() {
            return $this->db->query("SELECT * FROM empleado")->fetchAll(PDO::FETCH_ASSOC);
        }
        function update($id, $data) {
            if (!is_numeric($id)) return 0;
            $errores = $this->validar($data);
            if ($errores) return ['errores' => $errores];
            $foto = $this->cargarArchivo('foto', '/../../images/uploads/');
            if ($foto === null && isset($_FILES['data']['error']['foto']) &&
                $_FILES['data']['error']['foto'] !== UPLOAD_ERR_NO_FILE) {
                return ['errores' => ['La foto no es válida (imagen de máximo 2 MB).']];
            }
            $sql = $foto !== null
                ? "UPDATE empleado SET nombre=:nombre, rfc=:rfc, pri_apellido=:pri_apellido, seg_apellido=:seg_apellido, puesto=:puesto, foto=:foto WHERE id_empleado=:id"
                : "UPDATE empleado SET nombre=:nombre, rfc=:rfc, pri_apellido=:pri_apellido, seg_apellido=:seg_apellido, puesto=:puesto WHERE id_empleado=:id";
            $parametros = [
                ':nombre' => $data['nombre'], ':rfc' => $data['rfc'],
                ':pri_apellido' => $data['pri_apellido'], ':seg_apellido' => $data['seg_apellido'],
                ':puesto' => $data['puesto'], ':id' => $id
            ];
            if ($foto !== null) $parametros[':foto'] = $foto;
            $stmt = $this->db->prepare($sql);
            $stmt->execute($parametros);
            return $stmt->rowCount();
        }
        private function validar($data) {
            $errores = [];
            foreach (['nombre' => 100, 'rfc' => 13, 'pri_apellido' => 60, 'puesto' => 80] as $campo => $maximo) {
                $error = $this->validarTexto($data, $campo, $maximo);
                if ($error) $errores[] = $error;
            }
            $error = $this->validarTexto($data, 'seg_apellido', 60, false);
            if ($error) $errores[] = $error;
            return $errores;
        }
        function delete($id) {
            if (!is_numeric($id)) return 0;
            $stmt = $this->db->prepare("DELETE FROM empleado WHERE id_empleado = :id");
            $stmt->execute([':id' => $id]);
            return $stmt->rowCount();
        }
    }
?>
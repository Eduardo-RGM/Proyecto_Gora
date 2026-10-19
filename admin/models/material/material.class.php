<?php
    require_once __DIR__ . '/../sistema.class.php';
    class material extends sistema {
        function create($data) {
            $errores = $this->validar($data);
            $foto = $this->cargarArchivo('foto', '/../../images/uploads/');
            if ($foto === null) $errores[] = 'Debes seleccionar una foto válida (imagen de máximo 2 MB).';
            if ($errores) return ['errores' => $errores];
            $stmt = $this->db->prepare("INSERT INTO material(material,descripcion,foto) VALUES(:material,:descripcion,:foto)");
            $stmt->execute([
                ':material' => $data['material'],
                ':descripcion' => $data['descripcion'],
                ':foto' => $foto
            ]);
            $data['id_material'] = $this->db->lastInsertId();
            $data['foto'] = $foto;
            return $data;
        }
        function getone($id) {
            if (!is_numeric($id)) return [];
            $stmt = $this->db->prepare("SELECT * FROM material WHERE id_material = :id");
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
        }
        function getall() {
            return $this->db->query("SELECT * FROM material")->fetchAll(PDO::FETCH_ASSOC);
        }
        function update($id, $data) {
            if (!is_numeric($id)) return 0;
            $errores = $this->validar($data);
            $foto = $this->cargarArchivo('foto', '/../../images/uploads/');
            if ($foto === null && isset($_FILES['data']['error']['foto']) &&
                $_FILES['data']['error']['foto'] !== UPLOAD_ERR_NO_FILE) {
                $errores[] = 'La foto no es válida (imagen de máximo 2 MB).';
            }
            if ($errores) return ['errores' => $errores];
            $sql = $foto !== null
                ? "UPDATE material SET material=:material, descripcion=:descripcion, foto=:foto WHERE id_material=:id"
                : "UPDATE material SET material=:material, descripcion=:descripcion WHERE id_material=:id";
            $stmt = $this->db->prepare($sql);
            $parametros = [
                ':material' => $data['material'],
                ':descripcion' => $data['descripcion'],
                ':id' => $id
            ];
            if ($foto !== null) $parametros[':foto'] = $foto;
            $stmt->execute($parametros);
            return $stmt->rowCount();
        }
        function delete($id) {
            if (!is_numeric($id)) return 0;
            $stmt = $this->db->prepare("DELETE FROM material WHERE id_material = :id");
            $stmt->execute([':id' => $id]);
            return $stmt->rowCount();
        }
        private function validar($data){
            $errores = [];
            $error = $this->validarTexto($data, 'material', 100);
            if ($error) $errores[] = $error;
            $error = $this->validarTexto($data, 'descripcion', 150);
            if ($error) $errores[] = $error;
            if (!in_array(strtolower(trim($data['material'] ?? '')), ['papel', 'cartón', 'pet'], true)) {
                $errores[] = 'Selecciona un material válido: papel, cartón o pet.';
            }
            return $errores;
        }
    }
?>

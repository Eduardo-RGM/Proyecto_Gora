<?php
    require_once __DIR__.'/../sistema.class.php';
    class proveedor extends sistema{
        function create($data){
            $errores = $this->validar($data);
            $logo = $this->cargarArchivo('logo', '/../../images/uploads/');
            if ($logo === null) $errores[] = 'Debes seleccionar un logo válido (imagen de máximo 2 MB).';
            if ($errores) return ['errores' => $errores];
            $sql = "INSERT INTO proveedor(razon_social,rfc,telefono,correo,logo) VALUES(:razon_social,:rfc,:telefono,:correo,:logo)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':razon_social' => $data['razon_social'], ':rfc' => $data['rfc'],
                ':telefono' => $data['telefono'], ':correo' => $data['correo'], ':logo' => $logo
            ]);
            return array_merge($data, ['logo' => $logo, 'id_proveedor' => $this->db->lastInsertId()]);
        }
        function getone($id){
            if(is_numeric($id)){
                $sql = "SELECT * FROM proveedor WHERE id_proveedor = :id";
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
            }
            return [];
        }
        function getall(){
            return $this->db->query("SELECT * FROM proveedor")->fetchAll(PDO::FETCH_ASSOC);
        }
        function update($id, $data){
            $afectados = 0;
            if(is_numeric($id)){
                $errores = $this->validar($data);
                $logo = $this->cargarArchivo('logo', '/../../images/uploads/');
                if ($logo === null && isset($_FILES['data']['error']['logo']) &&
                    $_FILES['data']['error']['logo'] !== UPLOAD_ERR_NO_FILE) {
                    $errores[] = 'El logo no es válido (imagen de máximo 2 MB).';
                }
                if ($errores) return ['errores' => $errores];
                $sql = $logo !== null
                    ? "UPDATE proveedor SET razon_social=:razon_social, rfc=:rfc, telefono=:telefono, correo=:correo, logo=:logo WHERE id_proveedor=:id"
                    : "UPDATE proveedor SET razon_social=:razon_social, rfc=:rfc, telefono=:telefono, correo=:correo WHERE id_proveedor=:id";
                $stmt = $this->db->prepare($sql);
                $parametros = [
                    ':razon_social' => $data['razon_social'], ':rfc' => $data['rfc'],
                    ':telefono' => $data['telefono'], ':correo' => $data['correo'], ':id' => $id
                ];
                if ($logo !== null) $parametros[':logo'] = $logo;
                $stmt->execute($parametros);
                $afectados = $stmt->rowCount();
            }
            return $afectados;
        }
        private function validar($data){
            $errores = [];
            foreach (['razon_social' => 100, 'rfc' => 13, 'telefono' => 20] as $campo => $maximo) {
                $error = $this->validarTexto($data, $campo, $maximo);
                if ($error) $errores[] = $error;
            }
            $error = $this->validarEmail($data, 'correo');
            if ($error) $errores[] = $error;
            if (!empty($data['rfc']) && strlen(trim($data['rfc'])) < 12) {
                $errores[] = 'El RFC debe tener al menos 12 caracteres.';
            }
            return $errores;
        }
        function delete($id){
            $afectados = 0;
            if(is_numeric($id)){
                $sql = "DELETE FROM proveedor WHERE id_proveedor = :id";
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                $afectados = $stmt->rowCount();
            }
            return $afectados;

        }

    }
?>
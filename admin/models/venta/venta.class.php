<?php
    require_once __DIR__ . '/../sistema.class.php';
    class venta extends sistema {
        function create($data) {
            $errores = $this->validar($data);
            if ($errores) return ['errores' => $errores];
            $stmt = $this->db->prepare("INSERT INTO venta(id_cliente,id_empleado,id_material,fecha,cantidad,unidad) VALUES(:id_cliente,:id_empleado,:id_material,:fecha,:cantidad,:unidad)");
            $stmt->execute([
                ':id_cliente' => $data['id_cliente'],
                ':id_empleado' => $data['id_empleado'],
                ':id_material' => $data['id_material'],
                ':fecha' => $data['fecha'],
                ':cantidad' => $data['cantidad'],
                ':unidad' => $data['unidad']
            ]);
            $data['id_venta'] = $this->db->lastInsertId();
            return $data;
        }
        function getone($id) {
            if (!is_numeric($id)) return [];
            $stmt = $this->db->prepare("SELECT * FROM venta WHERE id_venta = :id");
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
        }
        function getall() {
            $sql = "SELECT v.*, c.nombre AS cliente, e.nombre AS empleado, m.material
                    FROM venta v
                    INNER JOIN cliente c ON c.id_cliente = v.id_cliente
                    INNER JOIN empleado e ON e.id_empleado = v.id_empleado
                    INNER JOIN material m ON m.id_material = v.id_material
                    ORDER BY v.id_venta DESC";
            return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        }
        function update($id, $data) {
            if (!is_numeric($id)) return 0;
            $errores = $this->validar($data);
            if ($errores) return ['errores' => $errores];
            $stmt = $this->db->prepare("UPDATE venta SET id_cliente=:id_cliente, id_empleado=:id_empleado, id_material=:id_material, fecha=:fecha, cantidad=:cantidad, unidad=:unidad WHERE id_venta=:id");
            $stmt->execute([
                ':id_cliente' => $data['id_cliente'],
                ':id_empleado' => $data['id_empleado'],
                ':id_material' => $data['id_material'],
                ':fecha' => $data['fecha'],
                ':cantidad' => $data['cantidad'],
                ':unidad' => $data['unidad'],
                ':id' => $id
            ]);
            return $stmt->rowCount();
        }
        private function validar($data) {
            $errores = [];
            foreach (['id_cliente', 'id_empleado', 'id_material'] as $campo) {
                $error = $this->validarId($data, $campo);
                if ($error) $errores[] = $error;
            }
            $error = $this->validarFecha($data, 'fecha');
            if ($error) $errores[] = $error;
            $error = $this->validarCantidad($data, 'cantidad');
            if ($error) $errores[] = $error;
            if (!in_array($data['unidad'] ?? '', ['kilogramos', 'toneladas'], true)) {
                $errores[] = 'Selecciona una unidad válida.';
            }
            return $errores;
        }
        function delete($id) {
            if (!is_numeric($id)) return 0;
            $stmt = $this->db->prepare("DELETE FROM venta WHERE id_venta = :id");
            $stmt->execute([':id' => $id]);
            return $stmt->rowCount();
        }
    }
?>

<?php
    require_once __DIR__ . '/../sistema.class.php';

    class compra extends sistema {
        function create($data) {
            $errores = $this->validar($data);
            if ($errores) return ['errores' => $errores];

            $stmt = $this->db->prepare(
                "INSERT INTO compra(id_proveedor, id_material, id_empleado, fecha, cantidad)
                 VALUES(:id_proveedor, :id_material, :id_empleado, :fecha, :cantidad)"
            );
            $stmt->execute([
                ':id_proveedor' => $data['id_proveedor'],
                ':id_material' => $data['id_material'],
                ':id_empleado' => $data['id_empleado'],
                ':fecha' => $data['fecha'],
                ':cantidad' => $data['cantidad']
            ]);
            $data['id_compra'] = $this->db->lastInsertId();
            return $data;
        }

        function getone($id) {
            if (!is_numeric($id)) return [];
            $stmt = $this->db->prepare("SELECT * FROM compra WHERE id_compra = :id");
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
        }

        function getall() {
            $sql = "SELECT c.*, p.razon_social AS proveedor, m.material, e.nombre AS empleado
                    FROM compra c
                    INNER JOIN proveedor p ON p.id_proveedor = c.id_proveedor
                    INNER JOIN material m ON m.id_material = c.id_material
                    INNER JOIN empleado e ON e.id_empleado = c.id_empleado
                    ORDER BY c.id_compra DESC";
            return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        }

        function getResumenPorMes() {
            $sql = "SELECT DATE_FORMAT(fecha, '%Y-%m') AS mes, SUM(cantidad) AS cantidad
                    FROM compra
                    GROUP BY DATE_FORMAT(fecha, '%Y-%m')
                    ORDER BY mes";
            return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        }

        function update($id, $data) {
            if (!is_numeric($id)) return 0;
            $errores = $this->validar($data);
            if ($errores) return ['errores' => $errores];

            $stmt = $this->db->prepare(
                "UPDATE compra SET id_proveedor=:id_proveedor, id_material=:id_material,
                 id_empleado=:id_empleado, fecha=:fecha, cantidad=:cantidad
                 WHERE id_compra=:id"
            );
            $stmt->execute([
                ':id_proveedor' => $data['id_proveedor'],
                ':id_material' => $data['id_material'],
                ':id_empleado' => $data['id_empleado'],
                ':fecha' => $data['fecha'],
                ':cantidad' => $data['cantidad'],
                ':id' => $id
            ]);
            return $stmt->rowCount();
        }

        private function validar($data) {
            $errores = [];
            foreach (['id_proveedor', 'id_material', 'id_empleado'] as $campo) {
                $error = $this->validarId($data, $campo);
                if ($error) $errores[] = $error;
            }
            $error = $this->validarFecha($data, 'fecha');
            if ($error) $errores[] = $error;
            $error = $this->validarCantidad($data, 'cantidad');
            if ($error) $errores[] = $error;
            return $errores;
        }

        function delete($id) {
            if (!is_numeric($id)) return 0;
            $stmt = $this->db->prepare("DELETE FROM compra WHERE id_compra = :id");
            $stmt->execute([':id' => $id]);
            return $stmt->rowCount();
        }
    }
?>

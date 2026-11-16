<?php
    require_once __DIR__ . '/../sistema.class.php';

    class precio extends sistema {
        function getall() {
            return $this->db->query(
                "SELECT id_material, material, precio_compra, fecha_actualizacion
                 FROM material
                 ORDER BY material"
            )->fetchAll(PDO::FETCH_ASSOC);
        }

        function actualizar($precios) {
            $errores = [];
            foreach ($precios as $id => $valor) {
                if (!is_numeric($id) || filter_var($valor, FILTER_VALIDATE_FLOAT) === false || (float)$valor < 0) {
                    $errores[] = 'Todos los precios deben ser números mayores o iguales a cero.';
                    break;
                }
            }
            if ($errores) return $errores;

            $this->db->beginTransaction();
            try {
                $stmt = $this->db->prepare(
                    "UPDATE material
                     SET precio_compra = :precio, fecha_actualizacion = CURDATE()
                     WHERE id_material = :id"
                );
                foreach ($precios as $id => $valor) {
                    $stmt->execute([
                        ':precio' => number_format((float)$valor, 2, '.', ''),
                        ':id' => $id
                    ]);
                }
                $this->db->commit();
                return [];
            } catch (Throwable $error) {
                $this->db->rollBack();
                error_log('No se pudieron actualizar los precios de GORA: ' . $error->getMessage());
                return ['No fue posible guardar los precios. Intenta nuevamente.'];
            }
        }
    }
?>

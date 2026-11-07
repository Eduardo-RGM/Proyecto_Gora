<?php
    require_once __DIR__ . '/../sistema.class.php';

    class bitacora extends sistema {
        function obtenerRegistros($limite = 200) {
            $ruta = __DIR__ . '/../../../storage/logs/gora.log';
            if (!is_file($ruta)) {
                return [];
            }

            $registros = [];
            $lineas = file($ruta, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach (array_reverse($lineas) as $linea) {
                $registro = json_decode($linea, true);
                if (is_array($registro)) {
                    $registros[] = $registro;
                }
                if (count($registros) >= $limite) {
                    break;
                }
            }
            return $registros;
        }
    }
?>

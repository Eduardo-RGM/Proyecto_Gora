<?php
    include_once __DIR__ . '/models/empleado/empleado.class.php';
    $app = new empleado();
    $app->checarPermiso('Administrar empleados');
    $accion = $_GET['accion'] ?? 'read';
    $id = $_GET['id'] ?? null;
    $tituloPagina = 'Empleados';
    $descripcionPagina = 'Consulta y administra el personal de GORA.';

    if ($accion === 'credencial' || $accion === 'reporte') {
        $autoload = __DIR__ . '/../../vendor/autoload.php';
        if (!is_file($autoload)) {
            http_response_code(500);
            exit('No se encontró Composer. Instala las dependencias del proyecto.');
        }
        require_once $autoload;
        if (!class_exists('\Mpdf\Mpdf')) {
            http_response_code(500);
            exit('mPDF no está instalado. Ejecuta Composer en la carpeta src.');
        }

        $empleadoModel = new empleado();
        $rutaImagenes = __DIR__ . '/../images/uploads/';
        $imagenDataUri = static function ($nombre) use ($rutaImagenes) {
            $ruta = $rutaImagenes . ($nombre ?? '');
            if (empty($nombre) || !is_file($ruta)) {
                return '';
            }
            return 'data:' . mime_content_type($ruta) . ';base64,' . base64_encode(file_get_contents($ruta));
        };

        if ($accion === 'credencial') {
            $datos = $empleadoModel->getone($id);
            if (empty($datos)) {
                http_response_code(404);
                exit('Empleado no encontrado.');
            }
            $mpdf = new \Mpdf\Mpdf([
                'format' => [86, 54],
                'margin_left' => 0,
                'margin_right' => 0,
                'margin_top' => 0,
                'margin_bottom' => 0,
                'margin_header' => 0,
                'margin_footer' => 0,
                'autoPageBreak' => false
            ]);
            $mpdf->SetAutoPageBreak(false, 0);
            $nombre = htmlspecialchars(trim($datos['nombre'] . ' ' . $datos['pri_apellido'] . ' ' . ($datos['seg_apellido'] ?? '')), ENT_QUOTES, 'UTF-8');
            $rfc = htmlspecialchars($datos['rfc'], ENT_QUOTES, 'UTF-8');
            $puesto = htmlspecialchars($datos['puesto'], ENT_QUOTES, 'UTF-8');
            $correo = htmlspecialchars($datos['correo'] ?? 'Sin correo registrado', ENT_QUOTES, 'UTF-8');
            $foto = $imagenDataUri($datos['foto'] ?? '');
            $html = '<style>
                @page { margin: 0; }
                body { margin: 0; font-family: sans-serif; color: #12344d; }
                .credencial { width: 82mm; border: 0.35mm solid #b8d7d0; background: #f7fbfa; page-break-inside: avoid; }
                .cabecera { background: #0b3854; color: #fff; padding: 2mm 3mm 1.5mm; font-size: 12pt; font-weight: bold; letter-spacing: .7pt; white-space: nowrap; }
                .cabecera span { color: #8bc9a1; }
                .franja { background: #4b9f68; color: #fff; padding: 1mm 3mm; font-size: 5.5pt; letter-spacing: .25pt; white-space: nowrap; }
                .contenido { padding: 4mm 3mm; }
                .foto, .sin-foto { width: 25mm; height: 27mm; }
                .foto { border-radius: 1mm; }
                .sin-foto { background: #dfe9e8; color: #53706d; text-align: center; padding-top: 10mm; font-size: 7pt; }
                .datos { font-size: 6.5pt; line-height: 1.25; padding-left: 4mm; }
                .nombre { color: #0b3854; font-size: 9pt; font-weight: bold; line-height: 1.1; }
                .etiqueta { color: #4b9f68; font-weight: bold; }
                .correo { font-size: 5.5pt; }
            </style>
            <table class="credencial" cellpadding="0" cellspacing="0">
              <tr><td class="cabecera">GORA <span>| PERSONAL</span></td></tr>
              <tr><td class="franja">COMERCIALIZADORA DE FIBRAS DEL BAJÍO</td></tr>
              <tr><td class="contenido">
                <table width="100%" cellpadding="0" cellspacing="0"><tr>
                    <td width="36%">' . ($foto ? '<img class="foto" src="' . $foto . '">' : '<div class="sin-foto">SIN FOTO</div>') . '</td>
                    <td class="datos">
                        <div class="nombre">' . $nombre . '</div>
                        <span class="etiqueta">ID</span> ' . (int) $datos['id_empleado'] . '<br>
                        <span class="etiqueta">RFC</span> ' . $rfc . '<br>
                        <span class="etiqueta">PUESTO</span> ' . $puesto . '<br>
                        <span class="etiqueta">CORREO</span><br><span class="correo">' . $correo . '</span>
                    </td>
                </tr></table>
              </td></tr>
            </table>';
            $mpdf->WriteHTML($html);
            $mpdf->Output('credencial-' . (int) $datos['id_empleado'] . '.pdf', 'I');
            exit;
        }

        $empleados = $empleadoModel->getall();
        $filas = '';
        foreach ($empleados as $empleado) {
            $nombre = htmlspecialchars(trim($empleado['nombre'] . ' ' . $empleado['pri_apellido'] . ' ' . ($empleado['seg_apellido'] ?? '')), ENT_QUOTES, 'UTF-8');
            $foto = $imagenDataUri($empleado['foto'] ?? '');
            $filas .= '<tr><td class="id">' . (int) $empleado['id_empleado'] . '</td><td>' .
                ($foto ? '<img class="foto" src="' . $foto . '">' : '<span class="sin-foto">Sin foto</span>') .
                '</td><td>' . $nombre . '</td><td>' . htmlspecialchars($empleado['rfc'], ENT_QUOTES, 'UTF-8') .
                '</td><td>' . htmlspecialchars($empleado['puesto'], ENT_QUOTES, 'UTF-8') . '</td></tr>';
        }
        $mpdf = new \Mpdf\Mpdf(['format' => 'A4', 'orientation' => 'L', 'margin_top' => 12, 'margin_bottom' => 12]);
        $html = '<style>
            body { font-family: sans-serif; color: #12344d; }
            .barra { background: #0b3854; color: #fff; padding: 11px 14px; border-bottom: 4px solid #4b9f68; }
            h1 { margin: 0; font-size: 20px; letter-spacing: .5px; } .marca { color: #8bc9a1; font-size: 9px; }
            table { border-collapse: collapse; width: 100%; font-size: 9px; }
            th { background: #4b9f68; color: #fff; } th, td { border-bottom: 1px solid #cfe0dc; padding: 7px 6px; vertical-align: middle; }
            tr:nth-child(even) { background: #f3f8f6; } .foto { width: 16mm; height: 20mm; border-radius: 3px; } .sin-foto { color: #53706d; font-style: italic; }
            .id { color: #0b3854; font-weight: bold; }
        </style>
        <div class="barra"><div class="marca">GORA · ADMINISTRACIÓN</div><h1>Reporte de empleados</h1></div>
        <table><thead><tr><th>ID</th><th>Foto</th><th>Nombre completo</th><th>RFC</th><th>Puesto</th></tr></thead><tbody>' . $filas . '</tbody></table>';
        $mpdf->WriteHTML($html);
        $mpdf->Output('reporte-empleados.pdf', 'I');
        exit;
    }

    include_once __DIR__ . '/../templates/admin/header.html';
    switch ($accion) {
        case 'create':
            if (isset($_POST['enviar'])) {
                $datos = $app->create($_POST['data']);
                if (isset($datos['errores'])) {
                    $errores = $datos['errores'];
                    $datos = $_POST['data'];
                    include_once __DIR__ . '/../templates/empleado/_form.html';
                } else {
                    $app->registrarBitacora('EMPLEADO_CREADO', ['id_empleado' => $datos['id_empleado']]);
                    $empleados = $app->getall();
                    include_once __DIR__ . '/../templates/empleado/index.html';
                }
            } else {
                include_once __DIR__ . '/../templates/empleado/_form.html';
            }
            break;
        case 'update':
            if (isset($_POST['enviar'])) {
                $resultado = $app->update($id, $_POST['data']);
                if (is_array($resultado) && isset($resultado['errores'])) {
                    $errores = $resultado['errores'];
                    $datos = array_merge($_POST['data'], ['id_empleado' => $id]);
                    include_once __DIR__ . '/../templates/empleado/_form.html';
                } else {
                    $app->registrarBitacora('EMPLEADO_ACTUALIZADO', ['id_empleado' => $id]);
                    $empleados = $app->getall();
                    include_once __DIR__ . '/../templates/empleado/index.html';
                }
            } else {
                $datos = $app->getone($id);
                include_once __DIR__ . '/../templates/empleado/_form.html';
            }
            break;
        case 'delete':
            if ($app->delete($id)) {
                $app->registrarBitacora('EMPLEADO_ELIMINADO', ['id_empleado' => $id]);
            }
            $empleados = $app->getall();
            include_once __DIR__ . '/../templates/empleado/index.html';
            break;
        default:
            $empleados = $app->getall();
            include_once __DIR__ . '/../templates/empleado/index.html';
    }
    include_once __DIR__ . '/../templates/admin/footer.html';
?>
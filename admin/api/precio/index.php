<?php
    header('Content-Type: application/json; charset=utf-8');
    require_once __DIR__ . '/../../models/precio/precio.class.php';

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        echo json_encode(['error' => 'Solo se permite GET.']);
        exit;
    }

    $app = new precio();
    echo json_encode($app->getall(), JSON_PRETTY_PRINT);
?>

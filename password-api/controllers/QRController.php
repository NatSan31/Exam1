<?php

require_once __DIR__ . '/../services/QRService.php';

class QRController
{
    private $qrService;

    public function __construct()
    {
        $this->qrService = new QRService();
    }

    public function generate()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['type']) || !isset($data['content'])) {
            http_response_code(400);
            echo json_encode(["error" => "type y content son requeridos"]);
            return;
        }

        $size = $data['size'] ?? 300;
        $errorCorrection = $data['error_correction'] ?? 'M';

        try {
            $filename = $this->qrService->generate(
                $data['type'],
                $data['content'],
                $size,
                $errorCorrection
            );

            http_response_code(201);
            echo json_encode([
                "message" => "QR generado correctamente",
                "download_url" => "http://localhost:8000/storage/$filename"
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(["error" => $e->getMessage()]);
        }
    }
}
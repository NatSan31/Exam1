<?php

class QRService
{
    public function generate($type, $content, $size = 300, $errorCorrection = 'M')
    {
        if ($size < 100 || $size > 1000) {
            throw new Exception("Tamaño inválido (100-1000)");
        }

        $formatted = $this->formatContent($type, $content);

        // Usaremos Google Charts API (simple y sin composer)
        $url = "https://api.qrserver.com/v1/create-qr-code/?size={$size}x{$size}&data=" . urlencode($formatted);

        $image = file_get_contents($url);

        if (!$image) {
            throw new Exception("Error generando QR");
        }

        if (!is_dir(__DIR__ . '/../storage')) {
            mkdir(__DIR__ . '/../storage');
        }

        $filename = uniqid() . ".png";
        file_put_contents(__DIR__ . "/../storage/$filename", $image);

        return $filename;
    }

    private function formatContent($type, $content)
    {
        switch ($type) {
            case 'text':
                return $content;

            case 'url':
                if (!filter_var($content, FILTER_VALIDATE_URL)) {
                    throw new Exception("URL inválida");
                }
                return $content;

            case 'wifi':
                return "WIFI:T:{$content['encryption']};S:{$content['ssid']};P:{$content['password']};;";

            case 'geo':
                if ($content['lat'] < -90 || $content['lat'] > 90) {
                    throw new Exception("Latitud inválida");
                }
                if ($content['lng'] < -180 || $content['lng'] > 180) {
                    throw new Exception("Longitud inválida");
                }
                return "geo:{$content['lat']},{$content['lng']}";

            default:
                throw new Exception("Tipo no soportado");
        }
    }
}
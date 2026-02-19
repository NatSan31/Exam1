<?php

require_once __DIR__ . "/../services/PasswordService.php";

class PasswordController
{
    private $service;

    public function __construct()
    {
    $this->service = PasswordService::getInstance();
    }   


    public function generatePassword()
    {
        try {
            $password = $this->service->generate($_GET);

            echo json_encode([
                "success" => true,
                "password" => $password
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "error" => $e->getMessage()
            ]);
        }
    }

    public function generateMultiplePasswords()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        try {
            $count = $data['count'] ?? 1;
            if ($count < 1 || $count > 50) {
                throw new Exception("El count debe estar entre 1 y 50");
            }

            $passwords = [];

            for ($i = 0; $i < $count; $i++) {
                $passwords[] = $this->service->generate($data);
            }

            echo json_encode([
                "success" => true,
                "passwords" => $passwords
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "error" => $e->getMessage()
            ]);
        }
    }

    public function validatePassword()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        $password = $data['password'] ?? "";
        $requirements = $data['requirements'] ?? [];

        $result = $this->service->validate($password, $requirements);

        echo json_encode([
            "valid" => !in_array(false, $result),
            "details" => $result
        ]);
    }
}

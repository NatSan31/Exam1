<?php

class PasswordService
{
    private static $instance = null;

    private $uppercase = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    private $lowercase = "abcdefghijklmnopqrstuvwxyz";
    private $numbers = "0123456789";
    private $symbols = "!@#$%^&*()_+-=[]{}|;:,.<>?";

    private function __construct() {}

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new PasswordService();
        }
        return self::$instance;
    }

    public function generate($options)
    {
        $length = isset($options['length']) ? (int)$options['length'] : 12;

        if ($length < 4 || $length > 128) {
            throw new Exception("La longitud debe estar entre 4 y 128");
        }

        $includeUpper = !empty($options['includeUppercase']);
        $includeLower = !empty($options['includeLowercase']);
        $includeNumbers = !empty($options['includeNumbers']);
        $includeSymbols = !empty($options['includeSymbols']);

        if (!$includeUpper && !$includeLower && !$includeNumbers && !$includeSymbols) {
            throw new Exception("Debes seleccionar al menos un tipo de carácter");
        }

        $characters = "";
        $requiredChars = [];

        if ($includeUpper) {
            $characters .= $this->uppercase;
            $requiredChars[] = $this->uppercase[random_int(0, strlen($this->uppercase)-1)];
        }
        if ($includeLower) {
            $characters .= $this->lowercase;
            $requiredChars[] = $this->lowercase[random_int(0, strlen($this->lowercase)-1)];
        }
        if ($includeNumbers) {
            $characters .= $this->numbers;
            $requiredChars[] = $this->numbers[random_int(0, strlen($this->numbers)-1)];
        }
        if ($includeSymbols) {
            $characters .= $this->symbols;
            $requiredChars[] = $this->symbols[random_int(0, strlen($this->symbols)-1)];
        }

        if (!empty($options['excludeAmbiguous'])) {
            $characters = str_replace(['0','O','l','1'], '', $characters);
        }

        $password = $requiredChars;

        while (count($password) < $length) {
            $password[] = $characters[random_int(0, strlen($characters)-1)];
        }

        shuffle($password);

        $finalPassword = implode("", $password);

        // Soporte de patrón regex opcional
        if (!empty($options['pattern'])) {
            if (!preg_match($options['pattern'], $finalPassword)) {
                throw new Exception("La contraseña no cumple el patrón especificado");
            }
        }

        return $finalPassword;
    }

    public function validate($password, $requirements)
    {
        return [
            "length" => strlen($password) >= ($requirements['minLength'] ?? 8),
            "uppercase" => !empty($requirements['requireUppercase']) ? preg_match('/[A-Z]/', $password) : true,
            "numbers" => !empty($requirements['requireNumbers']) ? preg_match('/[0-9]/', $password) : true,
            "symbols" => !empty($requirements['requireSymbols']) ? preg_match('/[\W]/', $password) : true,
        ];
    }
}

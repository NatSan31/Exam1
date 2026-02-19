<?php

class PasswordService
{
    private $uppercase = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    private $lowercase = "abcdefghijklmnopqrstuvwxyz";
    private $numbers = "0123456789";
    private $symbols = "!@#$%^&*()_+-=[]{}|;:,.<>?";

    public function generate($options)
    {
        $length = isset($options['length']) ? (int)$options['length'] : 12;

        if ($length < 4 || $length > 128) {
            throw new Exception("La longitud debe estar entre 4 y 128");
        }

        $characters = "";

        if (!empty($options['includeUppercase'])) {
            $characters .= $this->uppercase;
        }
        if (!empty($options['includeLowercase'])) {
            $characters .= $this->lowercase;
        }
        if (!empty($options['includeNumbers'])) {
            $characters .= $this->numbers;
        }
        if (!empty($options['includeSymbols'])) {
            $characters .= $this->symbols;
        }

        if ($characters === "") {
            throw new Exception("Debes seleccionar al menos un tipo de carácter");
        }

        if (!empty($options['excludeAmbiguous'])) {
            $characters = str_replace(['0','O','l','1'], '', $characters);
        }

        $password = "";
        $max = strlen($characters) - 1;

        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[random_int(0, $max)];
        }

        return $password;
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

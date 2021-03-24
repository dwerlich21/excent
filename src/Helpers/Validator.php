<?php

namespace App\helpers;


class Validator
{

    public static function validatePassword($data)
    {
        if ($data['password2'] != $data['password']) {
            throw new \Exception('How passwords are different');
        }
        if (strlen($data['password']) < 8) {
            throw new \Exception('Password must be at least 8 characters');
        }

    }

    public static function requireValidator($fields, $data)
    {
        foreach ($fields as $key => $value) {
            if (!array_key_exists($key, $data) ||(is_string($data[$key]) && trim($data[$key]) === '')|| $data[$key] === null) {
                throw new \Exception('The field ' . $value. ' is required');
            }
        }
    }

    public static function validateCPF(string $cpf)
    {

        if (empty($cpf)) {
            throw new \Exception('CPF inválido');
        }

        $cpf = preg_replace("/[^0-9]/", "", $cpf);
        $cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);

        if (strlen($cpf) != 11) {
            throw new \Exception('CPF inválido');
        } else if ($cpf == '00000000000' ||
            $cpf == '11111111111' ||
            $cpf == '22222222222' ||
            $cpf == '33333333333' ||
            $cpf == '44444444444' ||
            $cpf == '55555555555' ||
            $cpf == '66666666666' ||
            $cpf == '77777777777' ||
            $cpf == '88888888888' ||
            $cpf == '99999999999') {
            throw new \Exception('CPF inválido');
        } else {
            for ($t = 9; $t < 11; $t++) {
                for ($d = 0, $c = 0; $c < $t; $c++) {
                    $d += $cpf[$c] * (($t + 1) - $c);
                }
                $d = ((10 * $d) % 11) % 10;
                if ($cpf[$c] != $d) {
                    throw new \Exception('CPF inválido');
                }
            }
        }
    }
}

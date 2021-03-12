<?php
/**
 * Created by PhpStorm.
 * User: rwerl
 * Date: 11/09/2019
 * Time: 22:02
 */

namespace App\Helpers;


class Date
{

    public static function validDate(string $date){
        $date = explode('-', $date);
        if(!checkdate($date[1], $date[2], $date[0])){
            throw new \Exception("Data inválida: {$date[2]}/{$date[1]}/{$date[0]}");
        }
        return true;
    }


}
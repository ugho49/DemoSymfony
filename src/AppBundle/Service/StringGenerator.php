<?php

namespace AppBundle\Service;

/**
 * Created by PhpStorm.
 * User: ughostephan
 * Date: 21/07/2017
 * Time: 21:03
 */
class StringGenerator
{

    /**
     * Generate a new random string
     *
     * @param int $length
     * @return string
     */
    function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
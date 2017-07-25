<?php

namespace ApiBundle\Traits;

use Symfony\Component\HttpFoundation\Response;

/**
 * Created by PhpStorm.
 * User: ughostephan
 * Date: 21/07/2017
 * Time: 20:38
 */
trait RestControler
{
    public function jsonResponse($data = null, $status = 200, $headers = array()) {
        $dataJson = $this->container->get('jms_serializer')->serialize($data, 'json');

        $response = new Response($dataJson, $status, $headers);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
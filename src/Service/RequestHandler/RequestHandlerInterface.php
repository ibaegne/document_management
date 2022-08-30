<?php

namespace App\Service\RequestHandler;

use Symfony\Component\HttpFoundation\Request;

interface RequestHandlerInterface
{
    public function handleRequest(Request $request);
}

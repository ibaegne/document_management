<?php

namespace App\Service\RequestHandler;

use Symfony\Component\HttpFoundation\Request;

interface JsonBodyDecodeInterface
{
    public function decodeJsonBody(Request $request);
}

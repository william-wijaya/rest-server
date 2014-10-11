<?php
namespace Plum\Rest\Server;

use Plum\Http\Exception\MethodNotAllowedException;
use Plum\Http\Exception\NotFoundException;
use Plum\Http\Request;
use Plum\Http\Response;

interface Router
{
    /**
     * Resolves given request into a callable action which can be invoked to
     * satisfy the request
     *
     * @param Request $request
     *
     * @return callable which accepts {@link Request} and {@link Response} as
     *      it's parameters
     *
     * @throws NotFoundException when resource is not found
     * @throws MethodNotAllowedException when resource is found but the method
     *      is not allowed
     */
    public function resolve(Request $request);
} 

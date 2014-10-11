<?php
namespace Plum\Rest\Server;

use Plum\Http\Request;
use Plum\Http\Response;

interface RestServer
{
    /**
     * Serves request and returns the corresponding response
     *
     * @param Request $request
     *
     * @return Response
     */
    public function serve(Request $request);
}

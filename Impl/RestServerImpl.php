<?php
namespace Plum\Rest\Server\Impl;

use Plum\Http\HttpException;
use Plum\Http\Request;
use Plum\Http\Response;
use Plum\Mapper\ObjectMapper;
use Plum\Rest\Server\RestServer;
use Plum\Rest\Server\Router;

class RestServerImpl implements RestServer
{
    private $router;
    private $mapper;
    private $encoder;

    public function __construct(
        Router $router, ObjectMapper $mapper, ResponseEncoder $encoder
    )
    {
        $this->router = $router;
        $this->mapper = $mapper;
        $this->encoder = $encoder;
    }

    /**
     * {@inheritdoc}
     */
    public function serve(Request $req)
    {
        $rep = new RestResponseImpl();
        try {
            $action = $this->router->resolve($req);

            $result = $action($req, $rep);
            if (!$result)
                return $rep;

            if ($result instanceof Response)
                return $result;

            if (is_object($result))
                $result = $this->mapper->reverse($result);

            $this->encoder->encode($result, $req, $rep);
        } catch (HttpException $httpEx) {
            $httpEx->applyTo($rep);
        } catch (\RuntimeException $e) {
            $rep->setStatusCode(Response::STATUS_INTERNAL_SERVER_ERROR);
            $rep->write($e->getMessage());
        }

        return $rep;
    }
}

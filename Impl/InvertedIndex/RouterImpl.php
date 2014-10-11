<?php
namespace Plum\Rest\Server\Impl\InvertedIndex;

use Plum\Http\Request;
use Plum\Http\Response;
use Plum\Rest\Server\Router;

class RouterImpl implements Router
{
    public function __construct()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function resolve(Request $request)
    {
        return function(Request $req, Response $rep) {
            return [1, 2, 3];
        };
    }
}

<?php
namespace Plum\Rest\Server\Impl;

use Plum\Gen\ClassFileWriter;
use Plum\Gen\CodeSpace;
use Plum\Gen\CodeWriter;
use Plum\Http\Request;
use Plum\Inject\Injector;
use Plum\Rest\Server\Impl\InvertedIndex\RouterImpl;
use Plum\Rest\Server\Router;

class RouterFactory
{
    private $space;

    public function __construct(CodeSpace $space)
    {
        $this->space = $space;
    }

    /**
     * @return Router
     */
    public function get()
    {
        $class = "InvertedIndexRouter";

        $w = new CodeWriter();
        $cls = new ClassFileWriter($w);
        $cls->beginClass(null, $class)->implement(Router::class)
            ->method(null, "resolve")
                ->parameter("req", Request::class)
                ->body(function(CodeWriter $w) {
                    $w->write('return function($req, $rep) {
return [1, 2, 3];
                    };');
                })
            ->endClass();

        $this->space->save($class, $w);
        $this->space->load($class);

        return new $class();
    }
} 

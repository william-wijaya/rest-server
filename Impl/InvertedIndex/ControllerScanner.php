<?php
namespace Plum\Rest\Server\Impl\InvertedIndex;

use Plum\Reflect\Matcher\MatchAnnotatedWith;
use Plum\Reflect\Reflection;
use Plum\Reflect\Type;
use Plum\Rest\RestMethod;

class ControllerScanner
{
    private $reflection;

    public function __construct(Reflection $reflection)
    {
        $this->reflection = $reflection;
    }

    /**
     * Returns all controllers which are classes that has method(s) annotated
     * with {@link RestMethod} annotation
     *
     * @param array $namespaces
     *
     * @return Type[]
     */
    public function scan(array $namespaces)
    {
        $m = new MatchAnnotatedWith(RestMethod::class);
        $controllers = [];
        foreach ($namespaces as $ns) {
            $controllers = array_merge(
                $controllers,
                $this->reflection->find($ns, $m)
            );
        }

        return $controllers;
    }
}

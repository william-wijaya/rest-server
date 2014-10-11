<?php
namespace Plum\Rest\Server;

use Plum\Http\Codec\FormUrlEncodedCodec;
use Plum\Http\Codec\JsonCodec;
use Plum\Http\Exception\UnsupportedMediaTypeException;
use Plum\Http\HttpException;
use Plum\Rest\Server\Impl\RestRequestImpl;
use Plum\Rest\Server\Impl\RestServerImpl;
use Plum\Inject\Named;
use Plum\Inject\Provides;
use Plum\Inject\Singleton;
use Plum\Http\Request;
use Plum\Rest\Server\Impl\InvertedIndex\RouterImpl;
use Plum\Rest\Server\Impl\RouterFactory;

class RestServerModule
{
    /** @Provides(RestServer::class) @Singleton */
    public static function provideServer(RestServerImpl $impl)
    {
        return $impl;
    }

    /** @Provides(Request::class) */
    public static function provideRequest(
        /** @Named("http.codecs") */$codecs
    )
    {
        $body = file_get_contents("php://input");
        $headers = getallheaders();
        if (isset($headers["Content-Length"])) {
            $mt = $headers["Content-Type"];
            if (!isset($codecs[$mt]))
                throw new UnsupportedMediaTypeException();

            $payload = $codecs[$mt]->decode($body);
        } else {
            $payload = null;
        }

        return new RestRequestImpl(
            $_SERVER["REQUEST_METHOD"],
            $_GET
                ? strtok($_SERVER["REQUEST_URI"], "?")
                : $_SERVER["REQUEST_URI"],
            $headers, $body, $_GET, $payload
        );
    }

    /** @Provides(Router::class) @Singleton */
    public static function provideRouter(RouterFactory $f)
    {
        return $f->get();
    }

    /** @Provides(Provides::ELEMENT) @Named("http.codecs") */
    public static function provideJsonCodec(JsonCodec $json)
    {
        return $json;
    }

    /** @Provides(Provides::ELEMENT) @Named("http.codecs") */
    public static function provideFormCodec(FormUrlEncodedCodec $form)
    {
        return $form;
    }

    /** @Provides @Named("http.codecs") @Singleton */
    public static function provideHttpCodecs(
        /** @Named("http.codecs") */array $codecs
    )
    {
        $map = [];
        foreach ($codecs as $c) {
            $map[$c->mediaType()] = $c;
        }

        return $map;
    }
} 

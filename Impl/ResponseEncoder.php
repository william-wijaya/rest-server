<?php
namespace Plum\Rest\Server\Impl;

use Plum\Http\HttpException;
use Plum\Http\MediaTypeCodec;
use Plum\Http\Request;
use Plum\Http\Response;
use Plum\Inject\Named;
use Plum\Rest\Server\Impl\ContentNegotiation\AcceptHeaderParser;

class ResponseEncoder
{
    /**
     * @var MediaTypeCodec[]
     */
    private $codecs;

    private $parser;

    /**
     * @param MediaTypeCodec[] $codecs
     * @param AcceptHeaderParser $parser
     */
    public function __construct(
        /** @Named("http.codecs") */$codecs, AcceptHeaderParser $parser
    )
    {
        $this->codecs = $codecs;
        $this->parser = $parser;
    }

    /**
     * @param mixed $value
     * @param Request $request
     * @param Response $response
     *
     * @return string
     */
    public function encode($value, Request $request, Response $response)
    {
        $mediaTypes = $this->parser->parse($request->accept());
        foreach ($mediaTypes as $mediaType) {
            if (isset($this->codecs[$mediaType])) {
                $body = $this->codecs[$mediaType]
                    ->encode($value);

                break;
            }
        }

        if (!isset($body)) {
            $response->setStatusCode(Response::STATUS_NOT_ACCEPTABLE);
            $response->setContentType(key($this->codecs));

            $body = reset($this->codecs)->encode($value);
        }

        $response->write($body);
    }
} 

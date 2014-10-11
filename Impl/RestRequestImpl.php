<?php
namespace Plum\Rest\Server\Impl;

use Plum\Http\Request;

class RestRequestImpl implements Request
{
    private $uri;
    private $body;
    private $method;
    private $headers;

    private $QUERY;
    private $PAYLOAD;

    public function __construct(
        $method, $uri, array $headers = [], $body = null,
        array $QUERY = [], $PAYLOAD = null
    )
    {
        $this->uri = $uri;
        $this->body = $body;
        $this->method = $method;
        $this->headers = $headers;

        $this->QUERY = $QUERY;
        $this->PAYLOAD = $PAYLOAD;
    }

    /**
     * {@inheritdoc}
     */
    public function uri()
    {
        return $this->uri;
    }

    /**
     * {@inheritdoc}
     */
    public function method()
    {
        return $this->method;
    }

    /**
     * {@inheritdoc}
     */
    public function headers()
    {
        return $this->headers;
    }

    /**
     * {@inheritdoc}
     */
    public function header($name)
    {
        if (isset($this->headers[$name]))
            return $this->headers[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function host()
    {
        return $this->header("Host");
    }

    /**
     * {@inheritdoc}
     */
    public function contentType()
    {
        return $this->header("Content-Type");
    }

    /**
     * {@inheritdoc}
     */
    public function accept()
    {
        return $this->header("Accept");
    }

    /**
     * {@inheritdoc}
     */
    public function length()
    {
        return (int)$this->header("Content-Length");
    }

    /**
     * {@inheritdoc}
     */
    public function userAgent()
    {
        return $this->header("User-Agent");
    }

    /**
     * {@inheritdoc}
     */
    public function locale()
    {
        return $this->header("Accept-Language");
    }

    /**
     * {@inheritdoc}
     */
    public function date()
    {
        $d = $this->header("Date");
        if ($d)
            return new \DateTime($d);

        return new \DateTime("now");
    }

    /**
     * {@inheritdoc}
     */
    public function body()
    {
        return $this->body;
    }

    /**
     * {@inheritdoc}
     */
    public function queryParams()
    {
        return $this->QUERY;
    }

    /**
     * {@inheritdoc}
     */
    public function queryParam($name)
    {
        if (isset($this->QUERY[$name]))
            return $this->QUERY[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function payload()
    {
        return $this->PAYLOAD;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        $s = null;
        $s .= $this->method." ".$this->uri."\r\n";
        foreach ($this->headers as $name => $value)
            $s .= "{$name}: {$value}\r\n";

        $s .= "\r\n".$this->body;

        return $s;
    }
}

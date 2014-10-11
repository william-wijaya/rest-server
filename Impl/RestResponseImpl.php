<?php
namespace Plum\Rest\Server\Impl;

use Plum\Http\HttpException;
use Plum\Http\Response;

class RestResponseImpl implements Response
{

    private $body;
    private $headers = [];
    private $statusCode;
    private $httpVersion = 1.1;

    /**
     * @param int $code
     * @param array $headers
     * @param string|null $body
     */
    public function __construct(
        $code = Response::STATUS_OK, array $headers = [], $body = null
    )
    {
        $this->body = $body;
        $this->headers = $headers;
        $this->statusCode = $code;
    }

    /**
     * {@inheritdoc}
     */
    public function statusCode()
    {
        return $this->statusCode;
    }

    /**
     * {@inheritdoc}
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
    }

    /**
     * {@inheritdoc}
     */
    public function httpVersion()
    {
        return $this->httpVersion;
    }

    /**
     * {@inheritdoc}
     */
    public function setHttpVersion($version)
    {
        $version = (float)$version;
        if ($version !== 1 || $version !== 1.1)
            throw new \InvalidArgumentException(
                "Invalid HTTP Version {$version} given"
            );

        $this->httpVersion = $version;
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
    public function setHeader($name, $value)
    {
        $this->headers[$name] = $value;
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
    public function contentType()
    {
        return $this->header("Content-Type");
    }

    /**
     * {@inheritdoc}
     */
    public function setContentType($mediaType)
    {
        $this->setHeader("Content-Type", $mediaType);
    }

    /**
     * {@inheritdoc}
     */
    public function location()
    {
        return $this->header("Location");
    }

    /**
     * {@inheritdoc}
     */
    public function setLocation($location)
    {
        $this->setHeader("Location", $location);
    }

    /**
     * {@inheritdoc}
     */
    public function write($message, ...$messages)
    {
        $this->body .= $message.implode($messages);
    }

    /**
     * {@inheritdoc}
     */
    public function flushTo($stream)
    {
        if (!fwrite($stream, (string)$this))
            throw HttpException::internalServerError();
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        header(
            "HTTP/{$this->httpVersion} {$this->statusCode} ".
            self::statusNameOf($this->statusCode)
        );
        foreach ($this->headers as $name => $value)
            header("{$name}: {$value}");

        echo $this->body;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        $s = null;
        $s .= "HTTP/{$this->httpVersion} ";
        $s .= "{$this->statusCode} ".self::statusNameOf($this->statusCode);
        $s .= implode("\r\n", $this->headers);
        $s .= "\r\n";
        $s .= $this->body;

        return $s;
    }

    public static function statusNameOf($statusCode)
    {
        static $names = [
            Response::STATUS_OK => "OK",
            Response::STATUS_CREATED => "Created",
            Response::STATUS_ACCEPTED => "Accepted",

            Response::STATUS_NO_CONTENT => "No Content",
            Response::STATUS_RESET_CONTENT => "Reset Content",

            Response::STATUS_MOVED_PERMANENTLY => "Moved Permanently",
            Response::STATUS_FOUND => "Found",
            Response::STATUS_SEE_OTHER => "See Other",
            Response::STATUS_NOT_MODIFIED => "Not Modified",
            Response::STATUS_TEMPORARY_REDIRECT => "Temporary Redirect",

            Response::STATUS_BAD_REQUEST => "Bad Request",
            Response::STATUS_UNAUTHORIZED => "Unauthorized",
            Response::STATUS_FORBIDDEN => "Forbidden",
            Response::STATUS_NOT_FOUND => "Not Found",
            Response::STATUS_METHOD_NOT_ALLOWED => "Method Not Allowed",
            Response::STATUS_NOT_ACCEPTABLE => "Not Acceptable",
            Response::STATUS_CONFLICT => "Conflict",
            Response::STATUS_GONE => "Gone",
            Response::STATUS_LENGTH_REQUIRED => "Length Required",
            Response::STATUS_PRECONDITION_FAILED => "Precondition Failed",
            Response::STATUS_REQUEST_ENTITY_TOO_LARGE => "Request Entity Too Large",
            Response::STATUS_REQUEST_URI_TOO_LONG => "Request-URI Too Long",
            Response::STATUS_UNSUPPORTED_MEDIA_TYPE => "Unsupported Media Type",
            Response::STATUS_UNPROCESSABLE_ENTITY => "Unprocessable Entity",

            Response::STATUS_INTERNAL_SERVER_ERROR => "Internal Server Error",
            Response::STATUS_NOT_IMPLEMENTED => "Not Implemented",
            Response::STATUS_BAD_GATEWAY => "Bad Gateway",
            Response::STATUS_SERVICE_UNAVAILABLE => "Service Unavailable",
            Response::STATUS_GATEWAY_TIMEOUT => "Gateway Timeout",
        ];

        if (isset($names[$statusCode]))
            return $names[$statusCode];

        throw new \InvalidArgumentException(
            "Invalid HTTP Status Code of {$statusCode} given"
        );
    }
}

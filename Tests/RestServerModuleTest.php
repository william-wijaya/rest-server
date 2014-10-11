<?php
namespace Plum\Rest\Server
{
    use Plum\Rest\Server\Tests\RestServerModuleTest;

    function getallheaders()
    {
        return RestServerModuleTest::$HEADERS;
    }

    function file_get_contents($name)
    {
        return RestServerModuleTest::$BODY;
    }
}

namespace Plum\Rest\Server\Tests
{
    use Plum\Http\MediaType;
    use Plum\Http\Request;
    use Plum\Inject\Injector;
    use Plum\Mapper\MapperModule;
    use Plum\Rest\Server\RestServerModule;

    class RestServerModuleTest extends \PHPUnit_Framework_TestCase
    {
        public static $HEADERS = [];

        public static $BODY;

        private $SERVER_CACHE;

        /** @before */
        function setUp()
        {
            $this->SERVER_CACHE = $_SERVER;
            $_SERVER["REQUEST_URI"] = "/default-uri";
            $_SERVER["REQUEST_METHOD"] = "POST";
        }

        /** @after */
        function tearDown()
        {
            $_SERVER = $this->SERVER_CACHE;
            self::$HEADERS = [];
            self::$BODY = "";
        }

        /** @test */
        function it_should_returns_request_instance()
        {
            $req = Injector::create(null, null, RestServerModule::class, MapperModule::class)
                ->getInstance(Request::class);

            $this->assertInstanceOf(Request::class, $req);
        }

        /** @test */
        function it_should_returns_request_method()
        {
            $method = "POST";

            $_SERVER["REQUEST_METHOD"] = $method;

            $req = Injector::create(null, null, RestServerModule::class, MapperModule::class)
                ->getInstance(Request::class);

            $this->assertEquals($method, $req->method());
        }

        /** @test */
        function it_should_returns_request_uri()
        {
            $uri = "/home";

            $_SERVER["REQUEST_URI"] = $uri;

            $req = Injector::create(null, null, RestServerModule::class, MapperModule::class)
                ->getInstance(Request::class);

            $this->assertEquals($uri, $req->uri());
        }

        /** @test */
        function it_should_returns_request_header()
        {
            self::$HEADERS = ["Content-Type" => "text/plain"];

            $req = Injector::create(null, null, RestServerModule::class, MapperModule::class)
                ->getInstance(Request::class);

            $this->assertEquals(
                self::$HEADERS["Content-Type"],
                $req->headers()["Content-Type"]
            );
        }

        /** @test */
        function it_should_returns_request_body()
        {
            self::$BODY = "{}";

            $req = Injector::create(null, null, RestServerModule::class, MapperModule::class)
                ->getInstance(Request::class);

            $this->assertEquals(self::$BODY, $req->body());
        }

        /** @test */
        function it_should_returns_request_query()
        {
            $_GET = ["my-value" => 123];

            $req = Injector::create(null, null, RestServerModule::class, MapperModule::class)
                ->getInstance(Request::class);

            $this->assertEquals($_GET, $req->queryParams());
        }

        /** @test */
        function it_should_returns_request_payload()
        {
            self::$BODY = '{"key": "value"}';
            self::$HEADERS = [
                "Content-Length" => strlen(self::$BODY),
                "Content-Type" => MediaType::APPLICATION_JSON
            ];

            $req = Injector::create(null, null, RestServerModule::class, MapperModule::class)
                ->getInstance(Request::class);

            $this->assertEquals(json_decode(self::$BODY, true), $req->payload());
        }
    }
}

<?php

use Ilex\SwoolePsr7\SwooleResponseConverter;
use Ilex\SwoolePsr7\SwooleServerRequestConverter;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Container\ContainerInterface;
use Spiral\Boot\DispatcherInterface;
use Spiral\Http\Http;

class Dispatcher implements DispatcherInterface
{

    public function __construct(
        private readonly ContainerInterface $container,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function canServe(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function serve()
    {
        $server = new Swoole\Http\Server('localhost', 8080);

        $psr17Factory = new Psr17Factory();


        $serverRequestFactory = new SwooleServerRequestConverter(
            $psr17Factory,
            $psr17Factory,
            $psr17Factory,
            $psr17Factory
        );

        $server->on('start', function (Swoole\Http\Server $server) {
            echo "Swoole http server started at http://{$server->host}:{$server->port}\n";
        });

        $server->on(
            'request',
            function (Swoole\Http\Request $request, Swoole\Http\Response $response) use ($serverRequestFactory) {
                /** @var Http $http */
                $http = $this->container->get(Http::class);

                $psr7Request = $serverRequestFactory->createFromSwoole($request);

                $psr7Response = $http->handle($psr7Request);

                $converter = new SwooleResponseConverter($response);
                $converter->send($psr7Response);
            }
        );

        $server->start();
    }
}

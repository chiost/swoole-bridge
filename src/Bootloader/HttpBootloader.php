<?php

namespace Chiost\SwooleBridge\Bootloader;

use Chiost\SwooleBridge\Http\Dispatcher;
use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Boot\KernelInterface;
use Spiral\Core\FactoryInterface;

class HttpBootloader extends Bootloader
{
    public function start(KernelInterface $kernel, FactoryInterface $factory): void
    {
        $kernel->addDispatcher($factory->make(Dispatcher::class));
    }
}

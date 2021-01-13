<?php

declare(strict_types=1);

namespace Imi\Test\TCPServer\Middleware;

use Imi\Log\Log;
use Imi\RequestContext;
use Imi\Swoole\Server\TcpServer\IReceiveHandler;
use Imi\Swoole\Server\TcpServer\Message\IReceiveData;
use Imi\Swoole\Server\TcpServer\Middleware\IMiddleware;

class RequestLogMiddleware implements IMiddleware
{
    public function process(IReceiveData $data, IReceiveHandler $handler)
    {
        Log::info('Server: ' . RequestContext::getServer()->getName() . ', Url: ' . var_export($data->getFormatData(), true));

        return $handler->handle($data);
    }
}

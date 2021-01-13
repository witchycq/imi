<?php

declare(strict_types=1);

namespace Imi\Server\Http\Middleware;

use Imi\Bean\Annotation\Bean;
use Imi\RequestContext;
use Imi\Server\Http\Error\IHttpNotFoundHandler;
use Imi\Server\Http\Route\HttpRoute;
use Imi\Swoole\Server\Annotation\ServerInject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @Bean("RouteMiddleware")
 */
class RouteMiddleware implements MiddlewareInterface
{
    /**
     * @ServerInject("HttpRoute")
     *
     * @var \Imi\Server\Http\Route\HttpRoute
     */
    protected HttpRoute $route;

    /**
     * @ServerInject("HttpNotFoundHandler")
     *
     * @var \Imi\Server\Http\Error\IHttpNotFoundHandler
     */
    protected IHttpNotFoundHandler $notFoundHandler;

    /**
     * 处理方法.
     *
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $context = RequestContext::getContext();
        // 路由解析
        $result = $this->route->parse($request);
        if (null === $result || !\is_callable($result->callable))
        {
            // 未匹配到路由
            $response = $this->notFoundHandler->handle($handler, $request, $context['response']);
        }
        else
        {
            $context['routeResult'] = $result;
            $response = $handler->handle($request);
        }

        return $context['response'] = $response;
    }
}

<?php

namespace Swoft\Http\Server\Http;

use Swoft\App;
use Swoft\Bean\Collector\SwooleListenerCollector;
use Swoft\Bootstrap\SwooleEvent;
use Swoft\Exception\RuntimeException;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server;
use Swoft\Bootstrap\Server\AbstractServer;

/**
 * HTTP服务器
 *
 * @uses      HttpServer
 * @version   2017年10月14日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class HttpServer extends AbstractServer
{
    /**
     * @var \Swoole\Server::$port tcp监听器
     */
    protected $listen;

    /**
     * 启动Server
     */
    public function start()
    {
        // http server
        $this->server = new Server($this->httpSetting['host'], $this->httpSetting['port'], $this->httpSetting['model'], $this->httpSetting['type']);

        // 设置事件监听
        $this->server->set($this->setting);
        $this->server->on(SwooleEvent::ON_START, [$this, 'onStart']);
        $this->server->on(SwooleEvent::ON_WORKER_START, [$this, 'onWorkerStart']);
        $this->server->on(SwooleEvent::ON_MANAGER_START, [$this, 'onManagerStart']);
        $this->server->on(SwooleEvent::ON_REQUEST, [$this, 'onRequest']);

        // 启动RPC服务
        if ((int)$this->serverSetting['tcpable'] === 1) {
            $this->registerRpcEvent();
        }

        $this->registerSwooleServerEvents();
        $this->beforeStart();
        $this->server->start();
    }

    /**
     * register rpc event
     */
    private function registerRpcEvent()
    {
        $swooleListeners = SwooleListenerCollector::getCollector();
        if (!isset($swooleListeners[SwooleEvent::TYPE_PORT][0]) || empty($swooleListeners[SwooleEvent::TYPE_PORT][0])) {
            throw new RuntimeException("Please 'composer require swoft/rpc-server'! ");
        }

        $this->listen = $this->server->listen($this->tcpSetting['host'], $this->tcpSetting['port'], $this->tcpSetting['type']);
        $tcpSetting   = $this->getListenTcpSetting();
        $this->listen->set($tcpSetting);

        $swooleRpcPortEvents = $swooleListeners[SwooleEvent::TYPE_PORT][0];
        $this->registerSwooleEvents($this->listen, $swooleRpcPortEvents);
    }

    /**
     * http请求每次会启动一个协程
     *
     * @param Request  $request
     * @param Response $response
     */
    public function onRequest(Request $request, Response $response)
    {
        dispatcher_server()->doDispatcher($request, $response);
    }
}

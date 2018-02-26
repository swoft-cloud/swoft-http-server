<?php

namespace Swoft\Http\Server\Command;

use Swoft\Console\Bean\Annotation\Command;
use Swoft\Helper\EnvHelper;
use Swoft\Http\Server\Http\HttpServer;

/**
 * the group command list of http-server
 *
 * @Command(coroutine=false,server=true)
 * @uses      ServerCommand
 * @version   2017年10月06日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class ServerCommand
{
    /**
     * start http server
     *
     * @Usage
     * server:{command} [arguments] [options]
     *
     * @Options
     * -d,--d start by daemonized process
     *
     * @Example
     * php swoft.php server:start -d -r
     */
    public function start()
    {
        $httpServer = $this->getHttpServer();

        // sever配置参数
        $serverStatus = $httpServer->getServerSetting();

        // 是否正在运行
        if ($httpServer->isRunning()) {
            output()->writeln("<error>The server have been running!(PID: {$serverStatus['masterPid']})</error>", true, true);
        }

        // 启动参数
        $this->setStartArgs($httpServer);
        $httpStatus = $httpServer->getHttpSetting();
        $tcpStatus = $httpServer->getTcpSetting();

        // setting
        $workerNum = $httpServer->setting['worker_num'];

        // http启动参数
        $httpHost = $httpStatus['host'];
        $httpPort = $httpStatus['port'];
        $httpModel = $httpStatus['model'];
        $httpType = $httpStatus['type'];

        // tcp启动参数
        $tcpEnable = $serverStatus['tcpable'];
        $tcpHost = $tcpStatus['host'];
        $tcpPort = $tcpStatus['port'];
        $tcpType = $tcpStatus['type'];
        $tcpEnable = $tcpEnable ? 1 : 0;

        // 信息面板
        $lines = [
            '                         Information Panel                     ',
            '******************************************************************',
            "* http | Host: <note>$httpHost</note>, port: <note>$httpPort</note>, Model: <note>$httpModel</note>, type: <note>$httpType</note>, Worker: <note>$workerNum</note>",
            "* tcp  | Enable: <note>$tcpEnable</note>, host: <note>$tcpHost</note>, port: <note>$tcpPort</note>, type: <note>$tcpType</note>, Worker: <note>$workerNum</note>",
            '******************************************************************',
        ];

        // 启动服务器
        output()->writeln(implode("\n", $lines));
        $httpServer->start();
    }

    /**
     * reload worker process
     *
     * @Usage
     * server:{command} [arguments] [options]
     *
     * @Options
     * -t only to reload task processes, default to reload worker and task
     *
     * @Example
     * php swoft.php server:reload
     */
    public function reload()
    {
        $httpServer = $this->getHttpServer();

        // 是否已启动
        if (!$httpServer->isRunning()) {
            output()->writeln('<error>The server is not running! cannot reload</error>', true, true);
        }

        output()->writeln(sprintf('<info>Server %s is reloading</info>', input()->getFullScript()));

        // 重载
        $reloadTask = input()->hasOpt('t');
        $httpServer->reload($reloadTask);
        output()->writeln(sprintf('<success>Server %s reload success</success>', input()->getFullScript()));
    }

    /**
     * stop http server
     *
     * @Usage
     * server:{command} [arguments] [options]
     *
     * @Example
     * php swoft.php server:stop
     */
    public function stop()
    {
        $httpServer = $this->getHttpServer();

        // 是否已启动
        if (!$httpServer->isRunning()) {
            output()->writeln('<error>The server is not running! cannot stop</error>', true, true);
        }

        // pid文件
        $serverStatus = $httpServer->getServerSetting();
        $pidFile = $serverStatus['pfile'];

        @unlink($pidFile);
        output()->writeln(sprintf('<info>Swoft %s is stopping ...</info>', input()->getFullScript()));

        $result = $httpServer->stop();

        // 停止失败
        if (!$result) {
            output()->writeln(sprintf('<error>Swoft %s stop fail</error>', input()->getFullScript()), true, true);
        }

        output()->writeln(sprintf('<success>Swoft %s stop success!</success>', input()->getFullScript()));
    }

    /**
     * restart http server
     *
     * @Usage
     * server:{command} [arguments] [options]
     *
     * @Example
     * php swoft.php server:restart
     */
    public function restart()
    {
        $httpServer = $this->getHttpServer();

        // 是否已启动
        if ($httpServer->isRunning()) {
            $this->stop();
        }

        // 重启默认是守护进程
        $httpServer->setDaemonize();
        $this->start();
    }

    /**
     * @return HttpServer
     */
    private function getHttpServer()
    {
        // check env
        EnvHelper::check();

        // http server初始化
        $script = input()->getScript();

        $httpServer = new HttpServer();
        $httpServer->setScriptFile($script);

        return $httpServer;
    }

    /**
     * 设置启动选项，覆盖 config/server.php 配置选项
     *
     * @param HttpServer $httpServer
     */
    private function setStartArgs(HttpServer $httpServer)
    {
        $daemonize = input()->hasOpt('d');

        if ($daemonize) {
            $httpServer->setDaemonize();
        }
    }
}

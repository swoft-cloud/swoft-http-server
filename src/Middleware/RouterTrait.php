<?php
/**
 * @uses      RouterTrait
 * @version   2018年01月28日
 * @author    huangzhhui <huangzhwork@gmail.com>
 * @copyright Copyright 2010-2018 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */

namespace Swoft\Http\Server\Middleware;


use Psr\Http\Message\RequestInterface;
use Swoft\App;

trait RouterTrait
{

    /**
     * The attribute of Router
     *
     * @var string
     */
    protected $routerAttribute = 'requestHandler';

    /**
     * @param \Psr\Http\Message\RequestInterface $request
     * @return \Psr\Http\Message\RequestInterface
     */
    protected function handleRouter(RequestInterface $request): RequestInterface
    {
        $path = $request->getUri()->getPath();
        $method = $request->getMethod();

        /* @var \Swoft\Http\Server\Router\HandlerMapping $httpRouter */
        $httpRouter = App::getBean('httpRouter');
        $httpHandler = $httpRouter->getHandler($path, $method);
        $request = $request->withAttribute($this->routerAttribute, $httpHandler);
        return $request;
    }

}
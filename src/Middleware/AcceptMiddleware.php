<?php

namespace Swoft\Http\Server\Middleware;

use Interop\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Swoft\Bean\Annotation\Bean;
use Swoft\Http\Message\Server\Response;
use Swoft\Middleware\MiddlewareInterface;

/**
 * the middleware of accept type
 *
 * @Bean()
 * @uses      AcceptMiddleware
 * @version   2018年01月15日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class AcceptMiddleware implements MiddlewareInterface
{
    /**
     * the attribut of response data
     */
    const RESPONSE_ATTRIBUTE = 'responseAttribute';

    /**
     * the accept of json
     */
    const ACCEPT_JSON = 'application/json';

    /**
     * @param \Psr\Http\Message\ServerRequestInterface     $request
     * @param \Interop\Http\Server\RequestHandlerInterface $handler
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        $response = $this->acceptType($request, $response);
        return $response;
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    private function acceptType(ServerRequestInterface $request, ResponseInterface $response)
    {
        if (!($response instanceof Response)) {
            return $response;
        }

        // veiw
        $content = $response->getAttribute(AcceptMiddleware::RESPONSE_ATTRIBUTE);
        if($content === null){
            return $response;
        }

        $accepts       = $request->getHeader('accept');
        $currentAccept = current($accepts);
        $data          = $response->getAttribute(self::RESPONSE_ATTRIBUTE);

        if(empty($currentAccept)){
            if($response->isArrayable($data)){
                $response =  $response->json($data);
                return $response;
            }else{
                return $response->raw((string)$data);
            }
        }

        $isJson = $response->isMatchAccept($currentAccept, self::ACCEPT_JSON);
        $isArrayable = $response->isArrayable($data);

        if ($isJson || $isArrayable) {
            return $response->json($data);
        }

        if (!empty($data)) {
            return $response->raw((string)$data);
        }

        return $response;
    }
}
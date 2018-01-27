<?php

namespace Swoft\Http\Server\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Swoft\Http\Message\Server\Response;


trait AcceptTrait
{

    /**
     * The attribute of response data
     *
     * @var string
     */
    protected $responseAttribute = 'responseAttribute';

    /**
     * Json format accept
     *
     * @var string
     */
    protected $acceptJson = 'application/json';


    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function handleAccept(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        // Only handle HTTP-Server Response
        if (! $response instanceof Response) {
            return $response;
        }

        // View
        $content = $response->getAttribute($this->responseAttribute);
        if ($content === null) {
            return $response;
        }

        $accepts = $request->getHeader('accept');
        $currentAccept = current($accepts);
        $data = $response->getAttribute($this->responseAttribute);

        if (empty($currentAccept)) {
            if ($response->isArrayable($data)) {
                $response = $response->json($data);
                return $response;
            } else {
                return $response->raw((string)$data);
            }
        }

        $isJson = $response->isMatchAccept($currentAccept, $this->acceptJson);
        $isArrayable = $response->isArrayable($data);

        if ($isJson || $isArrayable) {
            return $response->json($data);
        }

        if (! empty($data)) {
            return $response->raw((string)$data);
        }

        return $response;
    }

}
<?php

namespace Swix\AmoCrm\Middleware;

use Swix\AmoCrm\Exception\AuthenticationException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class AuthMiddleware
 *
 * @package Swix\AmoCrm\HttpClient
 * @author Andrii Vasyliev
 */
class AuthenticationMiddleware
{
    /** @var array */
    const ERROR_CODES = [
        110 => 'Invalid login or api_key parameter',
        111 => 'Too many login attempts, please, login from browser and pass CAPTCHA',
        112 => 'Given user is disabled or does not belongs to provided account',
        113 => 'This IP address is not allowed. Please, check AmoCRM IP whitelist for provided account',
        101 => 'Given account does not exists',
        401 => 'Account does not exists on provided server. Please, use another one: {{domain}}'
    ];

    /** @var string */
    protected $login;

    /** @var string */
    protected $apiKey;

    /**
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    public function __construct(string $login, string $apiKey)
    {
        $this->login = $login;
        $this->apiKey = $apiKey;
    }

    public function pushMiddleware(HandlerStack $handlerStack)
    {
        $handlerStack->push(Middleware::mapRequest([$this, 'addParameters']), 'authAddParams');
        $handlerStack->push(Middleware::mapResponse([$this, 'handleErrors']), 'authHandleErrors');

        return $this;
    }

    public function addParameters(RequestInterface $request): RequestInterface
    {
        $login = $this->getLogin();
        $apiKey = $this->getApiKey();

        $currentUri = $request->getUri();
        $divider = strpos($currentUri, '?') === false ? '?' : '&';

        $uri = new Uri($currentUri . $divider . http_build_query(['login' => $login, 'api_key' => $apiKey]));

        return \GuzzleHttp\Psr7\modify_request($request, ['uri' => $uri]);
    }

    public function handleErrors(ResponseInterface $response): ResponseInterface
    {
        if ($response->getStatusCode() == 200) {
            return $response;
        }

        $data = json_decode($response->getBody()->getContents(), true);
        if (!isset($data['response']) || !isset($data['response']['error_code'])) {
            return $response; // unknown response
        }

        $errorCode = $data['response']['error_code'];
        if (!isset(self::ERROR_CODES[$errorCode])) {
            return $response; // another middleware should handle it
        }

        $message = self::ERROR_CODES[$errorCode];
        if (isset($data['response']['domain'])) {
            $message = str_replace('{{domain}}', $data['response']['domain'], $message);
        }

        throw new AuthenticationException($message, $errorCode);
    }
}

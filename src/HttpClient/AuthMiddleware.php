<?php

namespace Swix\AmoCrm\HttpClient;

use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Swix\AmoCrm\Exception\AuthException;

/**
 * Class AuthMiddleware
 *
 * @package Swix\AmoCrm\HttpClient
 * @author Andrii Vasyliev
 */
class AuthMiddleware
{
    /** @var string */
    protected $apiUrl;

    /** @var string */
    protected $login;

    /** @var string */
    protected $apiKey;

    /** @var array */
    const AUTH_ERROR_CODES = [
        110 => 'Invalid login or api_key parameter',
        111 => 'Too many login attempts, please, login from browser and pass CAPTCHA',
        112 => 'Given user is disabled or does not belongs to provided account',
        113 => 'This IP address is not allowed. Please, check AmoCRM IP whitelist for provided account',
        101 => 'Given account does not exists',
        401 => 'Account does not exists on provided server. Please, use another one: {{domain}}'
    ];

    /**
     * @return string
     */
    public function getApiUrl()
    {
        return $this->apiUrl;
    }

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

    public function __construct($login, $apiKey)
    {
        $this->login = $login;
        $this->apiKey = $apiKey;
    }

    public function pushMiddleware(HandlerStack $handlerStack)
    {
        $handlerStack->push(Middleware::mapRequest([$this, 'addParameters']));
        $handlerStack->push(Middleware::mapResponse([$this, 'handleErrors']));

        return $this;
    }

    public function addParameters(RequestInterface $request)
    {
        $login = $this->getLogin();
        $apiKey = $this->getApiKey();

        $currentUri = $request->getUri()->__toString();
        $divider = strpos($currentUri, '?') === false ? '?' : '&';

        $uri = new Uri($currentUri . $divider . http_build_query(['login' => $login, 'api_key' => $apiKey]));

        return \GuzzleHttp\Psr7\modify_request($request, ['uri' => $uri]);
    }

    public function handleErrors(ResponseInterface $response)
    {
        if (in_array($response->getStatusCode(), [200, 204])) {
            return $response;
        }

        $responseData = json_decode($response->getBody()->getContents(), true)['response'];
        $errorCode = $responseData['error_code'];

        if (!isset(self::AUTH_ERROR_CODES[$errorCode])) {
            if (isset($responseData['error'])) {
                $message = 'Unknown auth error: ' . $responseData['error'];
            } else {
                $message = 'Unknown auth error';
            }

            throw new AuthException($message, $errorCode);
        }

        if ($errorCode == 401) {
            $message = self::AUTH_ERROR_CODES[$errorCode];
            $message = str_replace('{{domain}}', $responseData['domain'], $message);

            throw new AuthException($message, $errorCode);
        }

        throw new AuthException(self::AUTH_ERROR_CODES[$errorCode], $errorCode);
    }
}

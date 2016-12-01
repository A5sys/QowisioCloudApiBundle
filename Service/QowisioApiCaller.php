<?php

namespace QowisioCloudApiBundle\Service;

use Eljam\GuzzleJwt\JwtMiddleware;
use Eljam\GuzzleJwt\Manager\JwtManager;
use Eljam\GuzzleJwt\Strategy\Auth\JsonAuthStrategy;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use QowisioCloudApiBundle\Exception\QowisioCredentialsException;
use QowisioCloudApiBundle\Exception\QowisioException;

/**
 * service to call Web Services against Qowisio Cloud API
 * ref: qowisio.cloud.api.caller
 */
class QowisioApiCaller
{
    const REST_GET = 'GET';
    const REST_POST = 'POST';
    const REST_PUT = 'PUT';
    const REST_DELETE = 'DELETE';

    /**
     * Prefix to url to get the JSON Web Token (prepended to the auth server URL)
     */
    const JWT_TOKEN_FUNCTION = 'login';

    /**
     * In the JSON response provided by Qowisio authentication API, the key under which is found the JWT token
     */
    const JWT_TOKEN_KEY = 'jwt';

    /**
     * IoC authentication server URL
     * @var string
     */
    protected $authEndpoint;

    /**
     * IoC authentication email
     * @var string
     */
    protected $authEmail;

    /**
     * IoC authentication password
     * @var string
     */
    protected $authPassword;

    /**
     * IoC data api URL
     * @var string
     */
    protected $dataEndpoint;

    /**
     * Singleton Client
     * @var GuzzleHttp\Client
     */
    protected $client;

    /**
     * Construct
     * @param string $authEndpoint URL of the authentication API server
     * @param string $authEmail    Email of the Qowisio account
     * @param string $authPassword Password of the Qowisio account
     * @param string $dataEndpoint URL of the data API server
     */
    public function __construct($authEndpoint, $authEmail, $authPassword, $dataEndpoint)
    {
        $this->authEndpoint = $authEndpoint;
        $this->authEmail = $authEmail;
        $this->authPassword = $authPassword;
        $this->dataEndpoint = $dataEndpoint;
    }

    /**
     *
     * @param string $wsFunction     name of the web service function
     * @param string $method         GET, POST, PUT, DELETE
     * @param array  $requestParams  array of parameters
     * @param array  $requestOptions array of options to pass to Guzzle Request
     * @throws QowisioCredentialsException
     * @throws QowisioException
     * @return mixed array<stdClass>|stdClass
     */
    public function callWs($wsFunction, $method = 'GET', $requestParams = array(), $requestOptions = array())
    {
        $client = $this->getApiClient();

        if (count($requestParams) > 0) {
            switch ($method) {
                case static::REST_GET:
                    // no body payload for get method, but params are in the query string
                    $wsFunction .= '?'.http_build_query($requestParams);
                    break;
                default:
                    // params are set in JSON in the body of the request
                    $requestOptions['body'] = json_encode($requestParams);
                    break;
            }
        }

        /* @var $response \GuzzleHttp\Psr7\Response */
        try {
            $response = $client->request($method, $wsFunction, $requestOptions);
        } catch (RequestException $e) {
            throw $this->getQowisioException($e);
        }

        return json_decode($response->getBody());
    }

    /**
     * Returns the singleton Guzzle Client to make API calls
     * @return Client
     */
    protected function getApiClient()
    {
        if ($this->client === null) {
            $this->client = new Client([
                'handler' => $this->getHandlerStack(),
                'base_uri' => $this->dataEndpoint,
            ]);
        }

        return $this->client;
    }

    /**
     * Returns the Guzzle Client HandlerStack.
     * Used to allow JWT authentication before sending the actual request
     * @return HandlerStack
     */
    protected function getHandlerStack()
    {
        // Create a HandlerStack
        $stack = HandlerStack::create();

        // Add middleware
        $stack->push($this->getJwtMiddleware());

        return $stack;
    }

    /**
     * Get the JWT middleware needed by guzzle to authenticate against Qowisio
     * @return JwtMiddleware
     */
    protected function getJwtMiddleware()
    {
        return new JwtMiddleware(
            new JwtManager(
                $this->getAuthClient(),
                $this->getAuthStrategy(),
                [
                    'token_url' => static::JWT_TOKEN_FUNCTION,
                    'token_key' => static::JWT_TOKEN_KEY,
                ]
            )
        );
    }

    /**
     * Returns the authentication strategy
     * @return JsonAuthStrategy
     */
    protected function getAuthStrategy()
    {
        return new JsonAuthStrategy([
            'username' => $this->authEmail,
            'password' => $this->authPassword,
            'json_fields' => ['email', 'password'], // actual field names sent to auth server
        ]);
    }

    /**
     * Get the Authentication Guzzle Client
     * @return Client
     */
    protected function getAuthClient()
    {
        return new Client(['base_uri' => $this->authEndpoint]);
    }

    /**
     * Returns an appropriate exception accordind to the error that occured
     * @param RequestException $exception
     * @throws QowisioCredentialsException
     * @throws QowisioException
     */
    protected function getQowisioException(RequestException $exception)
    {
        if ($exception->getResponse() && $exception->getResponse()->getStatusCode() === 401) {
            $responseObject = json_decode($exception->getResponse()->getBody());
            $exception = new QowisioCredentialsException($responseObject->error);
        } else {
            $exception = new QowisioException($exception->getMessage());
        }

        return $exception;
    }
}

<?php

namespace A5sys\QowisioCloudApiBundle\Service;

use A5sys\QowisioCloudApiBundle\Exception\QowisioException;

/**
 * Test if your user is authenticated
 * Note that the process of authentication itself is NOT done in this service since it is needed for every single api calls, with a JSON Web Token (JWT). @see QowisioApiCaller
 * ref: qowisio.cloud.api.authentication
 */
class QowisioApiAuthenticationService
{
    const FUNCTION_AMIAUTHENTICATED = 'amiauthenticated';

    /**
     * WS API caller
     * @var QuowisioApiCaller
     */
    protected $apiCaller;

    /**
     * Constructor
     * @param \A5sys\QowisioCloudApiBundle\Service\QowisioApiCaller $apiCaller
     */
    public function __construct(QowisioApiCaller $apiCaller)
    {
        $this->apiCaller = $apiCaller;
    }

    /**
     * Is the user connected to Qowisio Cloud API
     * @return boolean true if authentication on the Quowisio Cloud API
     */
    public function amIAuthenticated()
    {
        $isAuthenticated = true;
        try {
            $this->apiCaller->callWs(static::FUNCTION_AMIAUTHENTICATED);
        } catch (QowisioException $ex) {
            $isAuthenticated = false;
        }

        return $isAuthenticated;
    }
}

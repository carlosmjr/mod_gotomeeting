<?php

namespace GoToMeeting\DalPraS\OAuth2\Client\Resources;

use GoToMeeting\DalPraS\OAuth2\Client\Decorators\AccessTokenDecorator;

abstract class AuthenticatedResourceAbstract {

    /**
     * @var \GoToMeeting\DalPraS\OAuth2\Client\Provider\GotoWebinar
     */
    protected $provider;

    /**
     * Original League AccessToken
     *
     * @var \League\OAuth2\Client\Token\AccessToken
     */
    protected $accessToken;

    /**
     * @param \GoToMeeting\DalPraS\OAuth2\Client\Provider\GotoWebinar $provider
     * @param \League\OAuth2\Client\Token\AccessToken $accessToken
     */
    public function __construct(\GoToMeeting\DalPraS\OAuth2\Client\Provider\GotoMeeting $provider, \League\OAuth2\Client\Token\AccessToken $accessToken) {
        $this->provider    = $provider;
        $this->accessToken = $accessToken;
    }
}


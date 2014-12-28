# OpenID Connect (OAuth2) Client Library

[![Dependency Status](https://www.versioneye.com/user/projects/529a0143632bac39c900003d/badge.png)](https://www.versioneye.com/user/projects/529a0143632bac39c900003d)

The purpose of the library is to provide tools and building blocks for creating clients using delegated authentication/authorization based on the [OAuth2](http://oauth.net/2/) protocol with emphasis on the [OpenID Connect](https://openid.net/connect/) specification.

## Features

* flexible and extensible
* dependency injection approach
* covered with unit tests
* based on parts of the Zend Framework 2


## Compatibility

The library has been tested successfully with the following identity providers:

- [Github](http://developer.github.com/v3/oauth/)
- [Google](https://developers.google.com/accounts/docs/OAuth2Login)


## Requirements

* Zend Framework >= 2.2.1

## Documentation

* [API docs](http://debug.cz/apidoc/php-openid-connect-client/)

## Installation

### With composer

Add the following requirement to your `composer.json` file:

    "require":  {
		"ivan-novakov/php-openid-connect-client": "dev-master"
	}

### Without composer

Just clone the repository or download and unpack the latest [release](https://github.com/ivan-novakov/php-openid-connect-client/releases) and configure your autoloader accordingly.

## Basic usage

You need a `client_id` and `client_secret` registered at the identity provider. And you have to know the URLs of the provider endpoints.

The most common flow is:

1. generate authorize request URL
2. redirect the user to the authorize URL or make him click a "login" button
3. process the callback request and retrieve the authorization code
4. make a token request with the authorization code and retrieve the access token
5. (optional) make a user info request with the access token and retrieve information about the user

The library introduces a "flow" object, which integrates the above actions into just two calls:

- `getAuthorizationRequestUri` - generates the URL for user authorization, then it's up to the developer, how the user is redirected to the URL
- `process` - performs actions 3, 4 and 5 from the above list in one go

Simple example:

    use InoOicClient\Flow\Basic;

    $config = array(
        'client_info' => array(
            'client_id' => '<client ID>',
            'redirect_uri' => '<redirect URI>',
            
            'authorization_endpoint' => 'https://accounts.google.com/o/oauth2/auth',
            'token_endpoint' => 'https://accounts.google.com/o/oauth2/token',
            'user_info_endpoint' => 'https://www.googleapis.com/oauth2/v1/userinfo',
            
            'authentication_info' => array(
                'method' => 'client_secret_post',
                'params' => array(
                    'client_secret' => '<client secret>'
                )
            )
        )
    );
    
    $flow = new Basic($config);
    
    if (! isset($_GET['redirect'])) {
        try {
            $uri = $flow->getAuthorizationRequestUri('openid email profile');
            printf("<a href=\"%s\">Login</a>", $uri);
        } catch (\Exception $e) {
            printf("Exception during authorization URI creation: [%s] %s", get_class($e), $e->getMessage());
        }
    } else {
        try {
            $userInfo = $flow->process();
        } catch (\Exception $e) {
            printf("Exception during user authentication: [%s] %s", get_class($e), $e->getMessage());
        }
    }


## Dispatchers

The "flow" object is just a facade. The real "work" is done by the so called "dispatchers":

- `InoOicClient\Oic\Authorization\Dispatcher` - generates authorization request URI and processes the callbakc request
- `InoOicClient\Oic\Token\Dispatcher` - sends a token request
- `InoOicClient\Oic\UserInfo\Dispatcher` - sends a user info request

## HTTP client

The library uses the Zend Framework 2 HTTP client with the cURL connection adapter, which provides the best security regarding secure HTTPS connections. The HTTP client is created through a factory, which configures the client to validate the server certificate by default. The client also performs a CN matching validation. You can find more info about secure HTTPS connections with Zend Framework 2 in [this blogpost](http://blog.debug.cz/2012/11/https-connections-with-zend-framework-2.html).

However, it is possible to inject your own instance of the HTTP client, configured differently.


## Client authentication

According to the [OpenID Connect specification](http://openid.net/specs/openid-connect-messages-1_0-20.html#client_authentication) (see also the [OAuth2 specs](https://tools.ietf.org/html/rfc6749#section-2.3.1)), the library supports these client authentication methods:

- `client_secret_basic` - the client secret is sent in an `Authorization` HTTP header
- `client_secret_post` - the client secret is sent as a POST parameter

## State persistance

The [specifications](https://tools.ietf.org/html/rfc6749#section-4.1.1) recommend using the `state` parameter when requesting for authorization. The server is then obliged to return the same value in the callback. This may prevent cross-site request forgery attacks.

The library authomatically handles the state:

1. generates an opaque state value during authorization URI creation
2. saves the state in a user session
3. checks the state value sent from the server against the saved one

By default, the generated state value is saved in the user session (a [session container](http://framework.zend.com/manual/2.2/en/modules/zend.session.container.html) from the Zend Framework). It is possible to use another storage by implementing the `InoOicClient\Oic\Authorization\State\Storage\StorageInterface`

## Advanced usage

If you need to build custom flow or to extend/modify some of the functionality, you can implement your own flow object (see `InoOicClient\Flow\Basic` for details) or you can use dispatchers directly. Then you can build and configure the involved objects (dispatchers, requests, responses etc.) to suit your use case.

Creating the client info object:

    use InoOicClient\Client\ClientInfo;
    
    $clientOptions = array(
        'client_id' => '<client ID>',
        'redirect_uri' => '<redirect URI>',
        
        'authorization_endpoint' => 'https://accounts.google.com/o/oauth2/auth',
        'token_endpoint' => 'https://accounts.google.com/o/oauth2/token',
        'user_info_endpoint' => 'https://www.googleapis.com/oauth2/v1/userinfo',
        
        'authentication_info' => array(
            'method' => 'client_secret_post',
            'params' => array(
                'client_secret' => '<client secret>'
            )
        )
    );
    
    $clientInfo = new ClientInfo();
    $clientInfo->fromArray($clientOptions);
    

Preparing the authorization request URI:

    use InoOicClient\Oic\Authorization;

    $stateManager = new Manager();
    
    $dispatcher = new Authorization\Dispatcher();
    $dispatcher->setStateManager($stateManager);

    $request = new Authorization\Request($clientInfo, 'code', 'openid profile email');
    $uri = $dispatcher->createAuthorizationRequestUri($request);

Retrieve the authorization code from the callback:

    $stateManager = new Manager();
    
    $dispatcher = new Authorization\Dispatcher();
    $dispatcher->setStateManager($stateManager);
    
    $response = $dispatcher->getAuthorizationResponse();
    printf("OK<br>Code: %s<br>State: %s<br>", $response->getCode(), $response->getState());
    
Peform token request:

    $httpClientFactory = new Http\ClientFactory();
    $httpClient = $httpClientFactory->createHttpClient();
    
    $tokenDispatcher = new Token\Dispatcher($httpClient);
    
    $tokenRequest = new Token\Request();
    $tokenRequest->setClientInfo($clientInfo);
    $tokenRequest->setCode($authorizationCode);
    $tokenRequest->setGrantType('authorization_code');
    
    $tokenResponse = $tokenDispatcher->sendTokenRequest($tokenRequest);
    printf("Access token: %s<br>", $tokenResponse->getAccessToken());

## Running unit tests

Make sure phpunit has been installed through composer ("require-dev") and from the root directory run:
```
$ ./vendor/bin/phpunit -c tests/
```

## TODO

- provide user-friendly demos for different providers
- add support for JWT and ID token validation

## Specs

OpenID Connect:

- [Basic Client Profile](http://openid.net/specs/openid-connect-basic-1_0.html)
- [Standard Profile](http://openid.net/specs/openid-connect-standard-1_0.html)

OAuth2:

- [The OAuth 2.0 Authorization Framework](https://tools.ietf.org/html/rfc6749)

## Provider documentation

- [Github](http://developer.github.com/v3/oauth/)
- [Google](https://developers.google.com/accounts/docs/OAuth2Login)
- [Facebook](https://developers.facebook.com/docs/facebook-login/login-flow-for-web-no-jssdk/)

## License

- [BSD-3-Clause](http://debug.cz/license/bsd-3-clause)


## Author

- [Ivan Novakov](http://novakov.cz)

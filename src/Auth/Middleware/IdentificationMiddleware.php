<?php
/**
 * Created by PhpStorm.
 * User: victorynox
 * Date: 15.02.16
 * Time: 13:53
 */

namespace zaboy\Auth\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use zaboy\Auth\Adapter\AuthAdapter;
use Zend\Authentication\AuthenticationService;
use Zend\Filter\Digits;

class IdentificationMiddleware
{

    private $authConfig;

    public function __construct($authConfig)
    {
        $this->authConfig = $authConfig;
    }

    public function __invoke(Request $request, Response $response, callable $next)
    {

        $auth = new AuthenticationService();

        //access-control-request-headers
        $aCRH = $request->getHeaderLine("access-control-request-headers");
        if (isset($aCRH) && strlen($aCRH) > 0 && $aCRH === 'authorization') {
            $response = $response->withHeader("WWW-Authenticate", " Basic realm=\"TestR api\"");
            return $response->withStatus(401);
        }

        //"Basic YWRtaW46N2I0eEw2NnlxNEFGQmpHVg=="
        $authHeader = $request->getHeaderLine("Authorization");
        if (isset($authHeader)) {
            $basicAuthPattern = '/^Basic\ ([\w\W]+)/';
            $match = [];
            if (preg_match($basicAuthPattern, $authHeader, $match)) {
                if (isset($match[1])) {
                    $authData = base64_decode($match[1]);
                    $authPattern = '/([\w\W]+)\:([\w\W]+)/';
                    $match = [];
                    preg_match($authPattern, $authData, $match);
                    if (isset($match[1]) && isset($match[2])) {
                        $login = $match[1];
                        $pass = $match[2];
                        $adapter = new AuthAdapter($login, $pass, $this->authConfig);
                        $result = $auth->authenticate($adapter);
                        if (!$result->isValid()) {
                            throw new \Exception('');
                        }
                    }
                }
            }
        }

        if ($auth->hasIdentity()) {
            $identity = $auth->getIdentity();
            $role = $identity['role'];
        } else {
            $role = 'guest';
        }

        $request = $request->withAttribute('role', $role);
        return $next($request, $response);
    }
}

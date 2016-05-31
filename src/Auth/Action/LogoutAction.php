<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 30.05.16
 * Time: 15:50
 */

namespace zaboy\Auth\Action;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Zend\Authentication\AuthenticationService;

class LogoutAction
{
    public function __invoke(Request $request, Response $response, callable $next)
    {
        $auth = new AuthenticationService();
        if ($auth->hasIdentity()) {
            $auth->clearIdentity();
        }

        return $next($request, $response);
    }
}

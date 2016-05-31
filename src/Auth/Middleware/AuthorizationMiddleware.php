<?php
/**
 * Created by PhpStorm.
 * User: victorynox
 * Date: 15.02.16
 * Time: 13:06
 */

namespace zaboy\Auth\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 *
 *
 */
class AuthorizationMiddleware
{

    public function __invoke(Request $request, Response $response, callable $next)
    {
        $role = $request->getAttribute('role');
        $uri = $request->getUri();
        $path = $uri->getPath();
        switch ($path) {
            case preg_match("/\\/auth/", $path) > 0:
                break;
            case preg_match("/^\\/rest[\\w\\W]+/", $path) > 0:
                if ($role != 'admin' && $role != 'rest' && $role != 'guest') {
                    throw new \Exception("You are not authorized", 403);
                }
                break;
            default:
                if ('admin' != $role) {
                    throw new \Exception("You are not authorized", 403);
                }
                break;
        }

        /*if ($uri->getPath() != "/auth") {
            if ('admin' != $role) {
                throw new \Exception("You are not authorized", 403);
            }
        }*/

        return $next($request, $response);
    }
}

<?php

/**
 * Created by PhpStorm.
 * User: root
 * Date: 15.06.16
 * Time: 13:46
 */

namespace zaboy\Acl\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use zaboy\Acl\Exception\AccessForbiddenException;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Resource\GenericResource;
use Zend\Permissions\Acl\Role\GenericRole;

class AclMiddleware
{
    protected $config;
    protected $acl;


    public function __construct($config)
    {
        $this->config = $config;
        $this->acl = new Acl();
        foreach ($config as $role => $resources) {
            $this->acl->addRole(new GenericRole($role));
            foreach ($resources as $resourceId => $access) {
                if (!$this->acl->hasResource($resourceId)) {
                    $this->acl->addResource(new GenericResource($resourceId));
                }
                foreach ($access as $statusAccess => $privileges) {
                    if ($statusAccess === 'allow') {
                        $this->acl->allow($role, $resourceId, $privileges);
                    } else if ($statusAccess === 'deny') {
                        $this->acl->deny($role, $resourceId, $privileges);
                    } else {
                        throw new \Exception("Error in set access rules. You set: " . $statusAccess);
                    }
                }
            }
        }
    }

    public function __invoke(Request $request, Response $response, callable $next)
    {
        $role = $request->getAttribute('role');
        $path = $request->getUri()->getPath();
        $query = $request->getUri()->getQuery();
        $uri = $path .'?'. $query;
        $resource = '';
        
        foreach ($this->acl->getResources() as $resourceId){
            $match = [];
            preg_match($resourceId, $uri, $match);
            if(is_array($match) && count($match) > 0){
                $resource = $resourceId;
            }
        }

        if($resource === ''){
            throw new \Exception();
        }

        $method = strtoupper($request->getMethod());
        if (!$this->acl->isAllowed($role, $resource, $method)) {
            throw new \Exception();
        }

        return $next($request, $response);
    }
}

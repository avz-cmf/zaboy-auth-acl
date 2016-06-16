<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 15.06.16
 * Time: 13:47
 */

namespace zaboy\Acl\Middleware;

use Interop\Container\ContainerInterface;

class AclFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get('config');
        return new AclMiddleware($config['acl']);
    }
}

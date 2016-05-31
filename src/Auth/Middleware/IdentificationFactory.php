<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 31.05.16
 * Time: 10:34
 */

namespace zaboy\Auth\Middleware;

use Interop\Container\ContainerInterface;

class IdentificationFactory
{
    public function __invoke(ContainerInterface $container)
    {
        // TODO: Implement __invoke() method.
        $config = $container->get('config');
        $authConfig  = $config['authentications'];
        return new IdentificationMiddleware($authConfig);
    }
}

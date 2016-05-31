<?php
/**
 * Created by PhpStorm.
 * User: victorynox
 * Date: 15.02.16
 * Time: 14:26
 */

namespace zaboy\Auth\Middleware;

use Interop\Container\ContainerInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class AuthErrorHandlerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new AuthErrorHandlerMiddleware($container->get(TemplateRendererInterface::class));
    }
}

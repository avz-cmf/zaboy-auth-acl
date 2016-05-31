<?php
/**
 * Created by PhpStorm.
 * User: victorynox
 * Date: 15.02.16
 * Time: 14:16
 */

namespace zaboy\Auth\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template\TemplateRendererInterface;

class AuthErrorHandlerMiddleware
{
    private $template;

    public function __construct(TemplateRendererInterface $renderer)
    {
        $this->template = $renderer;
    }

    /**
     * @param $error \Error
     * @param Request $request
     * @param Response $response
     * @param callable $next
     * @return HtmlResponse
     */
    public function __invoke($error, Request $request, Response $response, callable $next)
    {
        if ($error->getCode() == 403) {
            $render = $this->template->render('app::login', ['error' => $error->getMessage()]);

            $query = $request->getParsedBody();
            $query['view']['render'] = $render;
            $query['view']['code'] = 403;

            $request = $request->withParsedBody($query);
        }
        return $next($request, $response);


    }
}

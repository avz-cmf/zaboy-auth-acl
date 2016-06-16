<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 15.06.16
 * Time: 15:20
 */

namespace zaboy\test\Acl\Middleware;

use PHPUnit_Framework_TestCase;
use zaboy\Acl\Middleware\AclMiddleware;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;
use Zend\Stratigility\Http\Request;

class AclMiddlewareTest extends PHPUnit_Framework_TestCase
{
    /** @var  AclMiddleware */
    private $aclMiddleware;

    private $_host = 'http://www.localhost';
    private $_aclConfig = [
        'admin' => [
            '/(\/rest\??[\w\W]*)/' => [
                'allow' => [
                    'GET',
                    'POST',
                    'UPDATE',
                    'DELETE'
                ],
            ],
            '/(\/[\w\W]*)/' => [
                'allow' => [
                    'GET',
                    'POST',
                    'UPDATE',
                    'DELETE'
                ]
            ],
        ],
        'guest' => [
            '/(\/rest\??[\w\W]*)/' => [
                'allow' => [
                    'GET',
                ],
                'deny' => [
                    'POST',
                    'UPDATE',
                    'DELETE'
                ]
            ],
            '/(\/[\w\W]*)/' => [
                'allow' => [
                    'GET',
                    'POST'
                ],
                'deny' => [
                    'UPDATE',
                    'DELETE'
                ]
            ],
        ]
    ];

    public function setUp()
    {
        $this->aclMiddleware = new AclMiddleware($this->_aclConfig);
    }

    public function allow_trueDataProvider()
    {
        return [
            ['guest', '/', '', 'get'],

            ['guest', '/rest', '', 'get'],
            ['guest', '/rest', 'and(eq(q,1),ne(w,2))&select(count(id))', 'pOsT'],

            ['admin', '/', '', 'get'],
            ['admin', '/', 'eq(q,1)', 'PoSt'],
            ['admin', '/', 'and(eq(q,1),eq(q,1))', 'UPDATE'],
            ['admin', '/', 'eq(q,1)&select(count(id))', 'delete'],

            ['admin', '/rest', '', 'get'],
            ['admin', '/rest', 'eq(q,1)', 'PoSt'],
            ['admin', '/rest', 'and(eq(q,1),eq(q,1))', 'UPDATE'],
            ['admin', '/rest', 'eq(q,1)&select(count(id))', 'delete'],
        ];

    }

    /** @dataProvider Allow_trueDataProvider
     * @param $role
     * @param $path
     * @param $query
     * @param $method
     * @throws \Exception
     */
    public function testAllow_true($role, $path, $query, $method)
    {
        $uri = $this->_host . $path . $query;
        $request = new Request(new ServerRequest([], [], $uri, $method));
        $request = $request->withAttribute('role', $role);
        $response = new Response();
        $this->aclMiddleware->__invoke($request, $response, function () {
        });

    }

    public function Allow_falseDataProvider()
    {
        return [
            ['guest', '/', 'and(eq(q,1),ne(w,2))&select(count(id))', 'UPDATE'],
            ['guest', '/', 'eq(q,1)&select(count(id))', 'delete'],

            ['guest', '/rest', 'and(eq(q,1),eq(q,1))', 'UPDATE'],
            ['guest', '/rest', 'eq(q,1)&select(count(id))', 'delete'],
        ];

    }

    /** @dataProvider Allow_falseDataProvider
     * @param $role
     * @param $path
     * @param $query
     * @param $method
     */
    public function testAllow_false($role, $path, $query, $method)
    {
        $uri = $this->_host . $path . $query;
        $request = new Request(new ServerRequest([], [], $uri, $method));
        $request =$request->withAttribute('role', $role);
        $response = new Response();

        $this->setExpectedException("Exception");
        $this->aclMiddleware->__invoke($request, $response, function () {
        });

    }

}

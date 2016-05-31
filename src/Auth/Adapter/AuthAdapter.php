<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 30.05.16
 * Time: 15:58
 */

namespace zaboy\Auth\Adapter;

use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;

class AuthAdapter implements AdapterInterface
{
    private $login;
    private $config;
    private $password;


    /**
     * AuthAdapter constructor.
     * @param $login
     * @param $password
     * @param $config ( `authentications` array)
     */
    public function __construct($login, $password, $config)
    {
        $this->login = $login;
        $this->password = $password;
        $this->config = $config;
    }

    /**
     * Performs an authentication attempt
     * @return \Zend\Authentication\Result
     * @throws \Zend\Authentication\Adapter\Exception\ExceptionInterface If authentication cannot be performed
     */
    public function authenticate()
    {
        if (isset($this->config[$this->login])) {
            $pass = $this->config[$this->login]['password'];
            $role = $this->config[$this->login]['role'];
            if (strcmp($pass, $this->password) == 0) {
                return new Result(Result::SUCCESS, ['login' => $this->login, 'role' => $role]);
            } else {
                return new Result(Result::FAILURE, $this->login);
            }
        } else {
            return new Result(Result::FAILURE_IDENTITY_NOT_FOUND, $this->login);
        }
    }
}

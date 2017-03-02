<?php
namespace Humphries\Contracts;

interface AuthenticationInterface
{

    /**
     * Checks if the user is logged in.
     *
     * @param string $identity
     * @param string $password
     * @param bool   $rememberMe
     *
     * @return bool
     */
    public function attempt($identity, $password, $rememberMe = false);

    /**
     * Checks if the user is logged in.
     *
     * @return bool
     */
    public function isLogged();

    /**
     * Logout user session.
     *
     * @return boolean
     */
    public function logout();

}
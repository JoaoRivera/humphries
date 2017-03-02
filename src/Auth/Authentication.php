<?php
namespace Humphries\Auth;

use Humphries\Contracts\Authentication as AuthenticationInterface;

abstract class Authentication implements AuthenticationInterface
{

    /**
     * @var string|null
     */
    protected $_token = null;

    /**
     * @var null|true
     */
    protected $_logout = null;

    public function __construct(Se)
    {
    }

    /**
     * Checks if the user is logged in
     *
     * @param string $identity
     * @param string $password
     * @param bool   $rememberMe
     *
     * @return bool
     */
    public function attempt($identity, $password, $rememberMe = false)
    {
        $user = $this->hasCredentials($identity, $password);
        $token = $this->getSession()->create(self::AUTH_ID, $user->id);
        if (!empty($user) && !empty($token)) {
            $this->_token = $token;
            $this->_user = $user;
            $this->_logout = null;

            return true;
        }

        return false;
    }

    /**
     * Checks if the user is logged in
     *
     * @return bool
     */
    public function isLogged()
    {
        if ($this->_logout === true) return false;
        if (!empty($this->_user)) return true;

        $user = $this->getUserService()->getOne( $this->getSession()->get(self::AUTH_ID, 0) );
        if (!empty($user)) {
            $this->_user = $user;

            return true;
        }

        return false;
    }

    /**
     * Logout user session
     *
     * @return boolean
     */
    public function logout()
    {
        $this->_user = null;
        $this->_logout = true;
        $this->getSession()->remove(self::AUTH_ID);

        return true;
    }

    /**
     * Check if the credentials exists.
     *
     * @param string $identity
     * @param string $password
     *
     * @return boolean
     */
    abstract protected function hasCredentials($identity, $password);

}
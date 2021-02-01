<?php

namespace Invoke;

class InvokeUserAuthorization
{
    /**
     * @var mixed $user
     */
    protected $user;

    /**
     * @param mixed $user
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }
}

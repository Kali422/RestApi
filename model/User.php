<?php


namespace Api\model;


class User
{
    /**
     * @var string
     */
    private $first_name;
    /**
     * @var string
     */
    private $last_name;
    /**
     * @var string
     */
    private $money;
    /**
     * @var int
     */
    private $id;

    private $password;

    function __construct($id = null, $first_name, $last_name, $money, $password)
    {

        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->money = $money;
        $this->id = $id;
        $this->setPassword($password);
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * @return string
     */
    public function getMoney()
    {
        return $this->money;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function setPassword($password)
    {
        if (preg_match("/^(?=.*[A-Z])(?=.*[@]).{8,}$/", $password))
        {
            $this->password=$password;
            return true;
        }
        else return false;

    }

    public function getPassword()
    {
        return $this->password;
    }




}
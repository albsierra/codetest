<?php


namespace CT\DAO;


class CT_User
{
    private $user_id;
    private $deleted;
    private $profile_id;
    private $displayname;
    private $email;

    public function __construct($user_id = null)
    {
        $context = array();
        if (isset($user_id)) {
            $query = CT_DAO::getQuery('user', 'getByUserId');
            $arr = array(':user_id' => $user_id);
            $context = $query['PDOX']->rowDie($query['sentence'], $arr);
        }
        CT_DAO::setObjectPropertiesFromArray($this, $context);
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param mixed $user_id
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * @return mixed
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param mixed $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

    /**
     * @return mixed
     */
    public function getProfileId()
    {
        return $this->profile_id;
    }

    /**
     * @param mixed $profile_id
     */
    public function setProfileId($profile_id)
    {
        $this->profile_id = $profile_id;
    }

    /**
     * @return mixed
     */
    public function getDisplayname()
    {
        return $this->displayname;
    }

    /**
     * @param mixed $displayname
     */
    public function setDisplayname($displayname)
    {
        $this->displayname = $displayname;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

}
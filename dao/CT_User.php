<?php


namespace CT\DAO;


class CT_User
{
    private $user_id;
    private $deleted;
    private $profile_id;
    private $displayname;
    private $email;

    const INSTRUCTOR_ROLE = 1000;

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

    function isInstructor($context_id) {
        $query = CT_DAO::getQuery('user', 'getUserRoles');
        $arr = array(':context_id' => $context_id, ':user_id' => $this->getUserId());
        $role = $query['PDOX']->rowDie($query['sentence'], $arr);
        return $role["role"] == self::INSTRUCTOR_ROLE;
    }

    static function findInstructors($context_id) {
        $query = CT_DAO::getQuery('user', 'findInstructors');
        $arr = array(':context_id' => $context_id, ':role' => self::INSTRUCTOR_ROLE);
        $instructorsArray = $query['PDOX']->allRowsDie($query['sentence'], $arr);
        return CT_DAO::createObjectFromArray(self::class, $instructorsArray);
    }

    /**
     * @param $ct_id int The context_id
     * @return CT_User[]
     */
    static function getUsersWithAnswers($ct_id) {
        $query = CT_DAO::getQuery('user', 'getUsersWithAnswers');
        $arr = array(':ctId' => $ct_id);
        return CT_DAO::createObjectFromArray(self::class, $query['PDOX']->allRowsDie($query['sentence'], $arr));
    }

    /**
     * @param $question_id int
     * @return CT_Answer
     */
    function getAnswerForQuestion($question_id) {
        $query = CT_DAO::getQuery('user','getAnswerForQuestion');
        $arr = array(':questionId' => $question_id, ':userId' => $this->getUserId());
        $context = $query['PDOX']->rowDie($query['sentence'], $arr);
        $answer = new CT_Answer();
        CT_DAO::setObjectPropertiesFromArray($answer, $context);
        return $answer;
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
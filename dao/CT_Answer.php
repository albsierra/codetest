<?php


namespace CT;


class CT_Answer
{
    private $answer_id;
    private $user_id;
    private $question_id;
    private $answer_txt;
    private $answer_success;
    private $modified;

    public function __construct($answer_id = null)
    {
        $context = array();
        if (isset($answer_id)) {
            $query = \CT\CT_DAO::getQuery('answer','getByAnswerId');
            $arr = array(':answer_id' => $answer_id);
            $context = $query['PDOX']->rowDie($query['sentence'], $arr);
        }
        \CT\CT_DAO::setObjectPropertiesFromArray($this, $context);
    }

    /**
     * @return mixed
     */
    public function getAnswerId()
    {
        return $this->answer_id;
    }

    /**
     * @param mixed $answer_id
     */
    public function setAnswerId($answer_id)
    {
        $this->answer_id = $answer_id;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @return CT_User
     */
    public function getUser()
    {
        return new CT_User($this->user_id);
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
    public function getQuestionId()
    {
        return $this->question_id;
    }

    /**
     * @param mixed $question_id
     */
    public function setQuestionId($question_id)
    {
        $this->question_id = $question_id;
    }

    /**
     * @return mixed
     */
    public function getAnswerTxt()
    {
        return $this->answer_txt;
    }

    /**
     * @param mixed $answer_txt
     */
    public function setAnswerTxt($answer_txt)
    {
        $this->answer_txt = $answer_txt;
    }

    /**
     * @return mixed
     */
    public function getAnswerSuccess()
    {
        return $this->answer_success ? $this->answer_success : 0;
    }

    /**
     * @param mixed $answer_success
     */
    public function setAnswerSuccess($answer_success)
    {
        $this->answer_success = $answer_success;
    }

    /**
     * @return mixed
     */
    public function getModified()
    {
        return $this->modified;
    }

    /**
     * @param mixed $modified
     */
    public function setModified($modified)
    {
        $this->modified = $modified;
    }

    public function isNew()
    {
        $answer_id = $this->getAnswerId();
        return !(isset($answer_id) && $answer_id > 0);
    }

    public function save() {
        global $CFG;
        $currentTime = new \DateTime('now', new \DateTimeZone($CFG->timezone));
        $currentTime = $currentTime->format("Y-m-d H:i:s");
        if($this->isNew()) {
            $query = \CT\CT_DAO::getQuery('answer','insert');
        } else {
            $query = \CT\CT_DAO::getQuery('answer','update');
        }
        $arr = array(
            ':modified' => $currentTime,
            ':userId' => $this->getUserId(),
            ':questionId' => $this->getQuestionId(),
            ':answerTxt' => $this->getAnswerTxt(),
            'answerSuccess' => $this->getAnswerSuccess(),
        );
        if(!$this->isNew()) $arr[':answer_id'] = $this->getAnswerId();
        $query['PDOX']->queryDie($query['sentence'], $arr);
        if($this->isNew()) $this->setAnswerId($query['PDOX']->lastInsertId());
    }

    function delete() {
        $query = \CT\CT_DAO::getQuery('answer','deleteOne');
        $arr = array(':answerId' => $this->getAnswerId());
        $query['PDOX']->queryDie($query['sentence'], $arr);
    }

    static function deleteAnswers($questions, $user_id) {
        $questionIds = array();
        foreach($questions as $question) {
            array_push($questionIds, $question->getQuestionId());
        }
        $query = \CT\CT_DAO::getQuery('answer','deleteFromQuestions');
        $query['sentence'] = str_replace("/questionsId/", implode(',', array_map('intval', $questionIds)), $query['sentence']);
        $arr = array(':userId' => $user_id);
        $query['PDOX']->queryDie($query['sentence'], $arr);
    }

    static function deleteInstructorAnswers($questions, $ct_id)
    {
        $instructors = \CT\CT_User::findInstructors($ct_id);
        foreach($instructors as $instructor) {
            self::deleteAnswers($questions, $instructor->getUserId());
        }
    }

}
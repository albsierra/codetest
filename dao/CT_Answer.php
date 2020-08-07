<?php


namespace CT\DAO;


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
            $connection = CT_DAO::getConnection();
            $query = "SELECT * FROM {$connection['p']}ct_answer WHERE answer_id = :answer_id";
            $arr = array(':answer_id' => $answer_id);
            $context = $connection['PDOX']->rowDie($query, $arr);
        }
        CT_DAO::setObjectPropertiesFromArray($this, $context);
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
        $connection = CT_DAO::getConnection();
        $currentTime = new \DateTime('now', new \DateTimeZone($CFG->timezone));
        $currentTime = $currentTime->format("Y-m-d H:i:s");
        if($this->isNew()) {
            $query = "INSERT INTO {$connection['p']}ct_answer "
                . "(`user_id`, `question_id`, `answer_txt`, `answer_success`, `modified`) "
                . "VALUES (:userId, :questionId, :answerTxt, :answerSuccess, :modified)";
        } else {
            $query = "UPDATE {$connection['p']}ct_answer set "
                . "`user_id` = :userId, "
                . "`question_id` = :questionId, "
                . "`answer_txt` = :answerTxt, "
                . "`answer_success` = :answerSuccess"
                . "`modified` = :modified "
                . "WHERE answer_id = :answer_id";
        }
        $arr = array(
            ':modified' => $currentTime,
            ':userId' => $this->getUserId(),
            ':questionId' => $this->getQuestionId(),
            ':answerTxt' => $this->getAnswerTxt(),
            'answerSuccess' => $this->getAnswerSuccess(),
        );
        if(!$this->isNew()) $arr[':answer_id'] = $this->getAnswerId();
        $connection['PDOX']->queryDie($query, $arr);
        if($this->isNew()) $this->setAnswerId($connection['PDOX']->lastInsertId());
    }

    function delete() {
        $connection = CT_DAO::getConnection();
        $query = "DELETE FROM {$connection['p']}ct_answer WHERE answer_id = :answerId;";
        $arr = array(':answerId' => $this->getAnswerId());
        $connection['PDOX']->queryDie($query, $arr);
    }

    static function deleteAnswers($questions, $user_id) {
        $connection = CT_DAO::getConnection();
        $questionIds = array();
        foreach($questions as $question) {
            array_push($questionIds, $question->getQuestionId());
        }
        $query = "DELETE FROM {$connection['p']}ct_answer WHERE user_id = :userId AND question_id in (".implode(',', array_map('intval', $questionIds)).");";
        $arr = array(':userId' => $user_id);
        $connection['PDOX']->queryDie($query, $arr);
    }

}
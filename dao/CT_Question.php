<?php


namespace CT;


class CT_Question
{
    private $question_id;
    private $ct_id;
    private $question_num;
    private $question_txt;
    private $modified;
    private $answers;

    public function __construct($question_id = null)
    {
        $context = array();
        if (isset($question_id)) {
            $query = \CT\CT_DAO::getQuery('question', 'getById');
            $arr = array(':question_id' => $question_id);
            $context = $query['PDOX']->rowDie($query['sentence'], $arr);
        }
        \CT\CT_DAO::setObjectPropertiesFromArray($this, $context);
    }

    //TODO Convertir en array de objetos
    static function findQuestionsForImport($user_id, $ct_id) {
        $query = \CT\CT_DAO::getQuery('question', 'findQuestionsForImport');
        $arr = array(':userId' => $user_id, ":ct_id" => $ct_id);
        return $query['PDOX']->allRowsDie($query['sentence'], $arr);
    }

    function createAnswer($user_id, $answer_txt) {
        $answer = new \CT\CT_Answer();
        $answer->setUserId($user_id);
        $answer->setQuestionId($this->getQuestionId());
        $answer->setAnswerTxt($answer_txt);
        $this->grade($answer);
        $answer->save();
        $this->answers = $this->getAnswers();
        array_push($this->answers, $answer);
        return $answer;
    }

    public function getMain()
    {
        return new CT_Main($this->getCtId());
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
    public function getCtId()
    {
        return $this->ct_id;
    }

    /**
     * @param mixed $ct_id
     */
    public function setCtId($ct_id)
    {
        $this->ct_id = $ct_id;
    }

    /**
     * @return mixed
     */
    public function getQuestionNum()
    {
        return $this->question_num;
    }

    /**
     * @param mixed $question_num
     */
    public function setQuestionNum($question_num)
    {
        $this->question_num = $question_num;
    }

    function getNextQuestionNumber() {
        $query = \CT\CT_DAO::getQuery('question', 'getNextQuestionNumber');
        $arr = array(':ctId' => $this->getCtId());
        $lastNum = $query['PDOX']->rowDie($query['sentence'], $arr)["lastNum"];
        return $lastNum + 1;
    }

    static function fixUpQuestionNumbers($ct_id) {
        $query = \CT\CT_DAO::getQuery('question', 'fixUpQuestionNumbers');
        $arr = array(':ctId' => $ct_id);
        $query['PDOX']->queryDie($query['sentence'], $arr);
    }

    /**
     * @return mixed
     */
    public function getQuestionTxt()
    {
        return $this->question_txt;
    }

    /**
     * @param mixed $question_txt
     */
    public function setQuestionTxt($question_txt)
    {
        $this->question_txt = $question_txt;
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

    /**
     * @return \CT\CT_Answer[] $answers
     */
    public function getAnswers()
    {
        if(!is_array($this->answers)) {
            $this->answers = array();
            $query = \CT\CT_DAO::getQuery('question', 'getAnswers');
            $arr = array(':questionId' => $this->getQuestionId());
            $answers = $query['PDOX']->allRowsDie($query['sentence'], $arr);
            $this->answers = \CT\CT_DAO::createObjectFromArray(\CT\CT_Answer::class, $answers);
        }
        return $this->answers;
    }

    public function getNumberAnswers()
    {
        return count($this->getAnswers());
    }

    public function getQuestionByType()
    {
        global $CFG;
        $class = $this->getMain()->getTypeProperty('class');
        return new $class($this->getQuestionId());
    }

    /**
     * @return CT_Question
     */
    public function getQuestionParent()
    {
        return new CT_Question($this->getQuestionId());
    }

    public function setQuestionParentProperties()
    {
        \CT\CT_DAO::setObjectPropertiesFromArray($this, \CT\CT_DAO::setObjectPropertiesToArray($this->getQuestionParent()));
    }

    public function isNew()
    {
        $question_id = $this->getQuestionId();
        return !(isset($question_id) && $question_id > 0);
    }

    public function save() {
        global $CFG;
        $currentTime = new \DateTime('now', new \DateTimeZone($CFG->timezone));
        $currentTime = $currentTime->format("Y-m-d H:i:s");
        if($this->isNew()) {
            $this->setQuestionNum($this->getNextQuestionNumber());
            $query = \CT\CT_DAO::getQuery('question', 'insert');
        } else {
            $query = \CT\CT_DAO::getQuery('question', 'update');
        }
        $arr = array(
            ':modified' => $currentTime,
            ':ctId' => $this->getCtId(),
            ':question_num' => $this->getQuestionNum(),
            ':question_txt' => $this->getQuestionTxt(),
        );
        if(!$this->isNew()) $arr[':question_id'] = $this->getQuestionId();
        $query['PDOX']->queryDie($query['sentence'], $arr);
        if($this->isNew()) $this->setQuestionId($query['PDOX']->lastInsertId());
    }

    function delete() {
        $query = \CT\CT_DAO::getQuery('question', 'delete');
        $arr = array(':questionId' => $this->getQuestionId());
        $query['PDOX']->queryDie($query['sentence'], $arr);
    }

}

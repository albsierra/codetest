<?php


namespace CT\DAO;


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
            $query = CT_DAO::getQuery('question', 'getById');
            $arr = array(':question_id' => $question_id);
            $context = $query['PDOX']->rowDie($query['sentence'], $arr);
        }
        CT_DAO::setObjectPropertiesFromArray($this, $context);
    }

    // TODO aplicar la misma solución que para getAnswers()
    public static function getByMain($ct_id)
    {
        $query = CT_DAO::getQuery('question', 'getByMain');
        $arr = array(':ctId' => $ct_id);
        return CT_DAO::createObjectFromArray(self::class, $query['PDOX']->allRowsDie($query['sentence'], $arr));
    }

    //TODO Convertir en array de objetos
    static function findQuestionsForImport($user_id, $ct_id) {
        $query = CT_DAO::getQuery('question', 'findQuestionsForImport');
        $arr = array(':userId' => $user_id, ":ct_id" => $ct_id);
        return $query['PDOX']->allRowsDie($query['sentence'], $arr);
    }

    function createAnswer($user_id, $answer_txt) {
        $answer = new CT_Answer();
        $answer->setUserId($user_id);
        $answer->setQuestionId($this->getQuestionId());
        $answer->setAnswerTxt($answer_txt);
        $answer->save();
        $this->answers = $this->getAnswers();
        array_push($this->answers, $answer);
        return $answer;
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
        $query = CT_DAO::getQuery('question', 'getNextQuestionNumber');
        $arr = array(':ctId' => $this->getCtId());
        $lastNum = $query['PDOX']->rowDie($query['sentence'], $arr)["lastNum"];
        return $lastNum + 1;
    }

    static function fixUpQuestionNumbers($ct_id) {
        $query = CT_DAO::getQuery('question', 'fixUpQuestionNumbers');
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
     * @return CT_Answer[] $answers
     */
    public function getAnswers()
    {
        if(!is_array($this->answers)) {
            $this->answers = array();
            $query = CT_DAO::getQuery('question', 'getAnswersId');
            $arr = array(':questionId' => $this->getQuestionId());
            $answers = $query['PDOX']->allRowsDie($query['sentence'], $arr);
            foreach ($answers as $answer) {
                array_push($this->answers, new CT_Answer($answer['answer_id']));
            }
        }
        return $this->answers;
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
            $query = CT_DAO::getQuery('question', 'insert');
        } else {
            $query = CT_DAO::getQuery('question', 'update');
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
        $query = CT_DAO::getQuery('question', 'delete');
        $arr = array(':questionId' => $this->getQuestionId());
        $query['PDOX']->queryDie($query['sentence'], $arr);
    }

}

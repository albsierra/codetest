<?php


namespace CT\DAO;


class CT_Question
{
    private $question_id;
    private $ct_id;
    private $question_num;
    private $question_txt;
    private $modified;

    public function __construct($question_id = null)
    {
        $context = array();
        if (isset($question_id)) {
            $connection = CT_DAO::getConnection();
            $query = "SELECT * FROM {$connection['p']}ct_question WHERE question_id = :question_id";
            $arr = array(':question_id' => $question_id);
            $context = $connection['PDOX']->rowDie($query, $arr);
        }
        CT_DAO::setObjectPropertiesFromArray($this, $context);
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

}
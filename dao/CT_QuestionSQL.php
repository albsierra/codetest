<?php


namespace CT;


class CT_QuestionSQL extends CT_Question
{
    private $question_type;
    private $question_database;
    private $question_solution;
    private $question_probe;

    public function __construct($question_id = null)
    {
        $context = array();
        if (isset($question_id)) {
            $query = \CT\CT_DAO::getQuery('questionSQL', 'getById');
            $arr = array(':question_id' => $question_id);
            $context = $query['PDOX']->rowDie($query['sentence'], $arr);
        }
        \CT\CT_DAO::setObjectPropertiesFromArray($this, $context);
        $this->setQuestionParentProperties();
    }

    /**
     * @return mixed
     */
    public function getQuestionType()
    {
        return $this->question_type;
    }

    /**
     * @param mixed $question_type
     */
    public function setQuestionType($question_type)
    {
        $this->question_type = $question_type;
    }

    /**
     * @return mixed
     */
    public function getQuestionDatabase()
    {
        return $this->question_database;
    }

    /**
     * @param mixed $question_database
     */
    public function setQuestionDatabase($question_database)
    {
        $this->question_database = $question_database;
    }

    /**
     * @return mixed
     */
    public function getQuestionSolution()
    {
        return $this->question_solution;
    }

    /**
     * @param mixed $question_solution
     */
    public function setQuestionSolution($question_solution)
    {
        $this->question_solution = $question_solution;
    }

    /**
     * @return mixed
     */
    public function getQuestionProbe()
    {
        return $this->question_probe;
    }

    /**
     * @param mixed $question_probe
     */
    public function setQuestionProbe($question_probe)
    {
        $this->question_probe = $question_probe;
    }

    function grade()
    {
        
    }

    public function save() {
        $isNew = $this->isNew();
        parent::save();
        $query = \CT\CT_DAO::getQuery('questionSQL', $isNew ? 'insert' : 'update');
        $arr = array(
            ':question_id' => $this->getQuestionId(),
            ':question_type' => $this->getQuestionType(),
            ':question_database' => $this->getQuestionDatabase(),
            ':question_solution' => $this->getQuestionSolution(),
            ':question_probe' => $this->getQuestionProbe(),
        );
        $query['PDOX']->queryDie($query['sentence'], $arr);
    }

}

<?php

namespace CT;

class CT_QuestionCode extends CT_Question
{
    private $question_language;
    private $question_input_test;
    private $question_input_grade;
    private $question_output_test;
    private $question_output_grade;
    private $question_solution;

    public function __construct($question_id = null)
    {
        $context = array();
        if (isset($question_id)) {
            $query = \CT\CT_DAO::getQuery('questionCode', 'getById');
            $arr = array(':question_id' => $question_id);
            $context = $query['PDOX']->rowDie($query['sentence'], $arr);
        }
        \CT\CT_DAO::setObjectPropertiesFromArray($this, $context);
        $this->setQuestionParentProperties();
    }

    /**
     * @return mixed
     */
    public function getQuestionLanguage()
    {
        return $this->question_language;
    }

    /**
     * @param mixed $question_language
     */
    public function setQuestionLanguage($question_language)
    {
        $this->question_language = $question_language;
    }

    /**
     * @return mixed
     */
    public function getQuestionInputTest()
    {
        return $this->question_input_test;
    }

    /**
     * @param mixed $question_input_test
     */
    public function setQuestionInputTest($question_input_test)
    {
        $this->question_input_test = $question_input_test;
    }

    /**
     * @return mixed
     */
    public function getQuestionInputGrade()
    {
        return $this->question_input_grade;
    }

    /**
     * @param mixed $question_input_grade
     */
    public function setQuestionInputGrade($question_input_grade)
    {
        $this->question_input_grade = $question_input_grade;
    }

    /**
     * @return mixed
     */
    public function getQuestionOutputTest()
    {
        return $this->question_output_test;
    }

    /**
     * @param mixed $question_output_test
     */
    public function setQuestionOutputTest($question_output_test)
    {
        $this->question_output_test = $question_output_test;
    }

    /**
     * @return mixed
     */
    public function getQuestionOutputGrade()
    {
        return $this->question_output_grade;
    }

    /**
     * @param mixed $question_output_grade
     */
    public function setQuestionOutputGrade($question_output_grade)
    {
        $this->question_output_grade = $question_output_grade;
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

    function grade()
    {

    }

    public function save() {
        $isNew = $this->isNew();
        parent::save();
        $query = \CT\CT_DAO::getQuery('questionCode', $isNew ? 'insert' : 'update');
        $arr = array(
            ':question_id' => $this->getQuestionId(),
            ':question_language' => $this->getQuestionLanguage(),
            ':question_input_test' => $this->getQuestionInputTest(),
            ':question_input_grade' => $this->getQuestionInputGrade(),
            ':question_output_test' => $this->getQuestionOutputTest(),
            ':question_output_grade' => $this->getQuestionOutputGrade(),
            ':question_solution' => $this->getQuestionSolution(),
        );
        $query['PDOX']->queryDie($query['sentence'], $arr);
    }

}

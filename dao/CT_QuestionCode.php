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
    private $recalculateOutputs = false;

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
        if($this->question_input_test != $question_input_test) $this->recalculateOutputs = true;
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
        if($this->question_input_grade != $question_input_grade) $this->recalculateOutputs = true;
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
        if($this->question_solution != $question_solution) $this->recalculateOutputs = true;
        $this->question_solution = $question_solution;
    }

    public function setOutputs()
    {
        $this->setQuestionOutputTest($this->getOutputFromCode(
            $this->getQuestionSolution(),
            $this->getQuestionLanguage(),
            $this->getQuestionInputTest()
        ));

        $this->setQuestionOutputGrade($this->getOutputFromCode(
            $this->getQuestionSolution(),
            $this->getQuestionLanguage(),
            $this->getQuestionInputGrade()
        ));
    }

    /**
     * @param CT_Answer $answer
     */
    function grade($answer) {
        $outputSolution = $this->getQuestionOutputGrade();
        $outputAnswer =  $this->getOutputFromCode(
            $answer->getAnswerTxt(), $this->getQuestionLanguage(), $this->getQuestionInputGrade()
        );
        CT_DAO::debug($outputSolution);
        CT_DAO::debug($outputAnswer);

        $grade = ($outputSolution == $outputAnswer);
        $answer->setAnswerSuccess($grade);
        $main = $this->getMain();
        $main->gradeUser($answer->getUserId());
    }

    function getOutputFromCode($answerCode, $language, $input) {
        $tmpfile = tmpfile();
        fwrite($tmpfile, $answerCode);
        $output = $this->launchCode($tmpfile, $language, $input);
        return($output);
    }

    function launchCode($file, $language, $input) {
        global $CFG;
        $main = $this->getMain();
        $languages = $main->getTypeProperty('codeLanguages');
        $languageName = $languages[$this->getQuestionLanguage()]['name'];
        $fileExtension = $languages[$this->getQuestionLanguage()]['ext'];

        $pathFile = stream_get_meta_data($file)['uri'];
        rename($pathFile, "$pathFile.$fileExtension");

        $descriptorspec = array(
            0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
            1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
            2 => array("file", "/tmp/error-output.txt", "a") // stderr is a file to write to
        );

        $cwd = sys_get_temp_dir(); // '/tmp';
        $env = array();

        $output = "";

        // Descomentando las siguientes líneas se pueden permitir diferentes casos de prueba
        // separándolos por un EOL
        // $inputs = explode(PHP_EOL, trim($input));

        //foreach ($inputs as $inputLine) {

        $command = $languages[$this->getQuestionLanguage()]['command'] . " $pathFile.$fileExtension";

        // Run shell command
        $process = proc_open($command, $descriptorspec, $pipes, $cwd, $env);

        if (is_resource($process)) {
            // $pipes now looks like this:
            // 0 => writeable handle connected to child stdin
            // 1 => readable handle connected to child stdout
            // Any error output will be appended to /tmp/error-output.txt

            fwrite($pipes[0], $input); //fwrite($pipes[0], $inputLine); //para varios casos de prueba
            fclose($pipes[0]);

            $output .= trim(stream_get_contents($pipes[1])) . "\n";
            fclose($pipes[1]);

            // It is important that you close any pipes before calling
            // proc_close in order to avoid a deadlock
            $return_value = proc_close($process);
        }
        //} // cierra el foreach que permite varios casos de prueba
        // remove code file
        unlink("$pathFile.$fileExtension");
        return $output;
    }

    public function save() {
        $isNew = $this->isNew();
        parent::save();
        if ($this->recalculateOutputs) $this->setOutputs();
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

<?php

namespace CT;


class CT_Test implements \JsonSerializable 
{
   private $test_id;
   private $description;
   private $name;
   private $questions;

    public function __construct($question_id = null) {
        $context = array();
        if (isset($question_id)) {
            $query = \CT\CT_DAO::getQuery('question', 'getById');
            $arr = array(':question_id' => $question_id);
            $context = $query['PDOX']->rowDie($query['sentence'], $arr);
        }
        \CT\CT_DAO::setObjectPropertiesFromArray($this, $context);
    }
    
     public function jsonSerialize() {
        return [
            'test_id' => $this->getTest_id(),
            'description' => $this->getDescription(),
            'name' => $this->getName(),
            'questions' => $this->getQuestions(),
            
        ];
    }

    static function getToken() {
        global $CFG;
        $url = $CFG->repositoryUrl . "/api/auth/signin";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);
        curl_close($curl);

        return "Authorization: Bearer " . $result;
    }

    static function MapJsonToTestsArray($json) {
        $response = json_decode($json);

        if ($response) {
            $tests = array();
            foreach ($response as $test) {
                $questions = array();
                $CTTest = new CT_Test();
                $CTTest->setTest_id($test->id);
                $CTTest->setName($test->name);
                $CTTest->setDescription($test->description);
                $questions1 = ($test->questions);
                foreach ($questions1 as $question) {
                    if ($question->type == 'MYSQL') {
                        $CTQuestion = self::mapObjectToSQLQuestion($question, $test->id);
                    } else {
                        $CTQuestion = self::mapObjectToCodeQuestion($question, $test->id);
                    }

                    array_push($questions, $CTQuestion);
                }
                $CTTest->setQuestions($questions);
                array_push($tests, $CTTest);
            }
            return $tests;
        }
    }

    static function mapObjectToQuestion($question, $testId = null) {
        $CTQuestion = new CT_Question();
        $CTQuestion->setQuestionId($question->id);
        $CTQuestion->setTitle($question->title);
        $CTQuestion->setDifficulty($question->difficulty);
        $CTQuestion->setType($question->type);
        $CTQuestion->setTestId($testId);
        isset($question->averageGrade) ? $CTQuestion->setAverageGrade($question->averageGrade) : false;
        isset($question->keywords) ? $CTQuestion->setKeywords($question->keywords) : false;

        return $CTQuestion;
    }

    static function mapObjectToSQLQuestion($question, $testId = null) {
        $CTQuestion = new CT_QuestionSQL();
        $CTQuestion->setQuestionId($question->id);
        $CTQuestion->setCtId($_SESSION['ct_id']);
        $CTQuestion->setTitle($question->title);
        $CTQuestion->setDifficulty($question->difficulty);
        $CTQuestion->setType($question->type);
        $CTQuestion->setTestId($testId);
        isset($question->averageGrade) ? $CTQuestion->setAverageGrade($question->averageGrade) : false;
        isset($question->keywords) ? $CTQuestion->setKeywords($question->keywords) : false;
        (isset($question->question_dbms) ? $CTQuestion->setQuestionDbms($question->question_dbms) : null );
        (isset($question->question_sql_type) ? $CTQuestion->setQuestionSQLType($question->question_sql_type) : null );
        (isset($question->question_database) ? $CTQuestion->setQuestionDatabase($question->question_database) : null );
        (isset($question->question_solution) ? $CTQuestion->setQuestionSolution($question->question_solution) : null );
        (isset($question->question_probe) ? $CTQuestion->setQuestionProbe($question->question_probe) : null );
        (isset($question->question_onfly) ? $CTQuestion->setQuestionOnfly($question->question_onfly) : null );
        (isset($question->question_must) ? $CTQuestion->setQuestionMust($question->question_must) : null );
        (isset($question->question_musnt) ? $CTQuestion->setQuestionMusnt($question->question_musnt) : null );

        return $CTQuestion;
    }

    static function mapObjectToCodeQuestion($question, $testId = null) {
        $CTQuestion = new CT_QuestionCode();
        $CTQuestion->setQuestionId($question->id);
        $CTQuestion->setCtId($_SESSION['ct_id']);
        $CTQuestion->setTitle($question->title);
        $CTQuestion->setDifficulty($question->difficulty);
        $CTQuestion->setType($question->type);
        $CTQuestion->setTestId($testId);
        isset($question->averageGrade) ? $CTQuestion->setAverageGrade($question->averageGrade) : false;
        isset($question->keywords) ? $CTQuestion->setKeywords($question->keywords) : false;
        $CTQuestion->setQuestionLanguage($question->question_language);
        (isset($question->question_input_test) ? $CTQuestion->setQuestionInputTest($question->question_input_test) : null );
        (isset($question->question_input_grade) ? $CTQuestion->setQuestionInputGrade($question->question_input_grade) : null );
        (isset($question->question_output_test) ? $CTQuestion->setQuestionOutputTest($question->question_output_test) : null );
        (isset($question->question_output_grade) ? $CTQuestion->setQuestionInputGrade($question->question_output_grade) : null );
        (isset($question->question_solution) ? $CTQuestion->setQuestionSolution($question->question_solution) : null );
        (isset($question->question_must) ? $CTQuestion->setQuestionMust($question->question_must) : null );
        (isset($question->question_musnt) ? $CTQuestion->setQuestionMusnt($question->question_musnt) : null );

        return $CTQuestion;
    }

    static function MapJsonToTest($json) {
        $response = json_decode($json);
        $CTTest = new CT_Test();
        $CTTest->setTest_id($response->id);
        $CTTest->setName($response->name);
        $CTTest->setDescription($response->description);
        $questions1 = ($response->questions);
        $questions = array();
        foreach ($questions1 as $question) {
            if ($question->type == 'MYSQL') {
                $CTQuestion = self::mapObjectToSQLQuestion($question, $response->id);
            } else {
                $CTQuestion = self::mapObjectToCodeQuestion($question, $response->id);
            }
            array_push($questions, $CTQuestion);
        }
        $CTTest->setQuestions($questions);
        return $CTTest;
    }

    static function checkerAdd($value) {
        global $CFG;
        
        //checks the category of the value and adds it if it is not
        if (in_array($value, $CFG->type)) {
            if (!in_array($value, $_SESSION['tags']['type'])) {
                array_push($_SESSION['tags']['type'], $value);
            }
        } else if (in_array($value, $CFG->difficulty)) {
            if (!in_array($value, $_SESSION['tags']['difficulty'])) {
                array_push($_SESSION['tags']['difficulty'], $value);
            }
        } else if (is_numeric($value) && $value <= 5 && $value > 0 ) {
           
            $_SESSION['tags']['averageGrade'] = Array();
            array_push($_SESSION['tags']['averageGrade'], $value);
        } else if ($value=="delete") {
            if (!empty($_SESSION['tags']['averageGrade'])) {
                unset($_SESSION['tags']['averageGrade'][0]);
            }
        } else {
            if (!in_array($value, $_SESSION['tags']['keywords'])) {
                array_push($_SESSION['tags']['keywords'], $value);
            }
        }
    }

    static function checkerDelete($value) {
        //Search for the value and delete it
        
        if (($key = array_search($value, $_SESSION['tags']['type'])) !== false) {
            unset($_SESSION['tags']['type'][$key]);
            $_SESSION['tags']['type'] = array_values($_SESSION['tags']['type']);
        } else if (($key = array_search($value, $_SESSION['tags']['difficulty'])) !== false) {
            unset($_SESSION['tags']['difficulty'][$key]);
            $_SESSION['tags']['difficulty'] = array_values($_SESSION['tags']['difficulty']);
        } else if (($key = array_search($value, $_SESSION['tags']['averageGrade'])) !== false) {
            unset($_SESSION['tags']['averageGrade'][$key]);
            $_SESSION['tags']['averageGrade'] = array_values($_SESSION['tags']['averageGrade']);
        }else if (($key = array_search($value, $_SESSION['tags']['keywords'])) !== false) {
            unset($_SESSION['tags']['keywords'][$key]);
            $_SESSION['tags']['keywords'] = array_values($_SESSION['tags']['keywords']);
        }
    }

    static function checker($object) {
        global $CFG;
        $arrayTags = Array();

        //Add the categories with values to an array
        foreach ($_SESSION['tags'] as $x => $x_value) {
            if ($_SESSION['tags'][$x]) {
                array_push($arrayTags, $x);
            }
        }
   
        //depending on the amount of values returns a url and the data to post in the call
        if (count($arrayTags) == 4) {

            $array["postData"] = [[$arrayTags[0]], $_SESSION['tags'][$arrayTags[0]], [$arrayTags[1]],
                $_SESSION['tags'][$arrayTags[1]], [$arrayTags[2]], $_SESSION['tags'][$arrayTags[2]],
                [$arrayTags[3]], $_SESSION['tags'][$arrayTags[3]]];
            $array["url"] = $CFG->repositoryUrl . "/api/".$object."/getTestQuestionBy4Values";
        } else if (count($arrayTags) == 3) {

            $array["postData"] = [[$arrayTags[0]], $_SESSION['tags'][$arrayTags[0]], [$arrayTags[1]],
                $_SESSION['tags'][$arrayTags[1]], [$arrayTags[2]], $_SESSION['tags'][$arrayTags[2]]];
            $array["url"] = $CFG->repositoryUrl . "/api/".$object."/getTestQuestionBy3Values";
        } else if (count($arrayTags) == 2) {

            $array["postData"] = [[$arrayTags[0]], $_SESSION['tags'][$arrayTags[0]], [$arrayTags[1]], $_SESSION['tags'][$arrayTags[1]]];
            $array["url"] = $CFG->repositoryUrl . "/api/".$object."/getTestQuestionByValues";
        } else if (count($arrayTags) == 1) {

            $array["postData"] = [$_SESSION['tags'][$arrayTags[0]], [$arrayTags[0]]];
            $array["url"] = $CFG->repositoryUrl . "/api/".$object."/getTestByValue";
        }


        if (isset($array)) {
            return $array;
        }
    }

    
    static function findTestForImportByPage($page) {
        global $CFG;
        $url = $CFG->repositoryUrl . "/api/tests/getAllTest/".$page;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json', self::getToken()));
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);
        $decode= json_decode($result);
        $totalPages=$decode[1];
        $tests = json_encode($decode[0]);
        curl_close($curl);

        $tests = self::MapJsonToTestsArray($tests);
        $array = ['tests' =>$tests, 'totalPages'=> $totalPages[0]];
       

        return $array;
    }

    //check the test questions to leave the ones that match the tags
    static function checkerTests($tests) {
        $tests1 = Array();
        if($tests){
        foreach ($tests as $test1 => $test) {
            $test1 = self::checkerTest($test);
            array_push($tests1, $test1);
        }
        }
        return $tests1;
    }
    
    static function in_array_any($needles, $haystack) {
        return !empty(array_intersect($needles, $haystack));
    }

    //check the test questions to leave the ones that match the tags
    static function checkerTest($test) {
        foreach ($test->getQuestions() as $question2 => $question) {
            if (!in_array($question->getType(), $_SESSION['tags']['type']) && !empty($_SESSION['tags']['type'])) {
                unset($test->questions[$question2]);
            }
            if (!in_array($question->getDifficulty(), $_SESSION['tags']['difficulty']) && !empty($_SESSION['tags']['difficulty'])) {
                unset($test->questions[$question2]);
            }
            if ( (empty($question->getAverageGrade()) && !empty($_SESSION['tags']['averageGrade'])) || (!empty($question->getAverageGrade()) && !empty($_SESSION['tags']['averageGrade']) && $question->getAverageGrade() < $_SESSION['tags']['averageGrade'][0] )) {
                unset($test->questions[$question2]);
            }
            if ((empty($question->getKeywords()) && !empty($_SESSION['tags']['keywords'])) ||
                    (!empty($question->getKeywords()) && !empty($_SESSION['tags']['keywords']) && !self::in_array_any($question->getKeywords(), $_SESSION['tags']['keywords']))) {
                unset($test->questions[$question2]);
            }
        }
        return $test;
    }

    
    //Find the Test questions on the repo by the tags
    static function findTestForImportByValue($value = null, $page = 0) {
        
  //if values is passed check if is already on the array
        if ($value) {
            self::checkerAdd($value);
        }
        $array = self::checker("tests");

        $postData = $array["postData"];
        $url = $array["url"] . "/" . $page;
        
        //if are tags
        if (isset($postData)) {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json', self::getToken()));
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($postData));
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

            $result = curl_exec($curl);
            curl_close($curl);
            $decode = json_decode($result);
            $totalPages = $decode[1];
            $tests = json_encode($decode[0]);

            //decode the questions from json and maps to Test objects deleting the question that do not meet the tags
            $tests = self::MapJsonToTestsArray($tests);
            $tests1 = self::checkerTests($tests);
            $array = ['tests' => $tests1, 'totalPages' => $totalPages[0]];
        } else {
               //if not tags
            
            $array = \CT\CT_Test::findTestForImportByPage($page);
        }
        return $array;
    }

    static function findTestForImportByDeleteValue($value) {
        global $CFG;
//Deletes the value passed
        self::checkerDelete($value);
        
         //Check if there is any value left
        $array = self::checker("tests");
        $postData = $array["postData"];
        $url = $array["url"] . "/0";

        //if there is any value left
        if (isset($postData)) {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json', self::getToken()));
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($postData));
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

            $result = curl_exec($curl);
            curl_close($curl);
            $decode = json_decode($result);
            $totalPages = $decode[1];
            $tests = json_encode($decode[0]);
            $tests = self::MapJsonToTestsArray($tests);
            $array = ['tests' => $tests, 'totalPages' => $totalPages[0]];
        } else {
            //if there is no value left
            
            $array = \CT\CT_Test::findTestForImportByPage(0);
        }
        return $array;
    }

    static function findTestForImportId($id) {
        global $CFG;
        $url = $CFG->repositoryUrl."/api/tests/getTestId/" . $id;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json', self::getToken()));
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);
        curl_close($curl);
        $CTTest= self::MapJsonToTest($result);
        $test = self::checkerTest($CTTest);
      
        return $test;
    }

    //Find a question by Test_id and question_id
    static function findTestForImportQuestionId($question_id, $test_id) {
        global $CFG;
        $url = $CFG->repositoryUrl."/api/tests/getTestId/" . $test_id;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json', self::getToken()));
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($result);
        $questions = ($response->questions);
        foreach ($questions as $question) {
            //after import the test search for the question with the id passed
            if ($question->id == $question_id) {
                if( in_array($question->type, $CFG->programmingLanguajes)){
               $CTQuestion = \CT\CT_Test::mapObjectToCodeQuestion($question, $test_id);
           }else{
                $CTQuestion = \CT\CT_Test::mapObjectToSQLQuestion($question, $test_id);
           }
            }
        }
        return $CTQuestion;
    }

    function createAnswer($user_id, $answer_txt) {
        //Look for answer in the db
        $answer = \CT\CT_Answer::getByUserAndQuestion($user_id, $this->getQuestionId(), $this->getCtId());
        $answer->setUserId($user_id);
        $answer->setQuestionId($this->getQuestionId());
        $answer->setAnswerTxt($answer_txt);
        
        //checks if is correct
        if ($this->preGrade($answer)) {
            $this->grade($answer);
        }
        $answer->save();
        $this->answers = $this->getAnswers();
        array_push($this->answers, $answer);
        $main = $this->getMain();
        $main->gradeUser($answer->getUserId());
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
    
    public function getTest_id() {
        return $this->test_id;
    }

    public function getStatement() {
        return $this->statement;
    }

    public function getQuestions() {
        return $this->questions;
    }

    public function setTest_id($test_id): void {
        $this->test_id = $test_id;
    }

    public function setStatement($statement): void {
        $this->statement = $statement;
    }

    public function setQuestions($questions): void {
        $this->questions = $questions;
    }
    
    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description): void {
        $this->description = $description;
    }
    public function getName() {
        return $this->name;
    }

    public function setName($name): void {
        $this->name = $name;
    }

}

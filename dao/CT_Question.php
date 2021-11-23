<?php

namespace CT;

class CT_Question implements \JsonSerializable {

    private $question_id;
    private $ct_id;
    private $question_num;
    private $testId;
    private $title;
    private $type;
    private $difficulty;
    private $answers;
    private $averageGradeUnderstability;
    private $averageGradeDifficulty;
    private $averageGradeTime;
    private $averageGrade;
    private $numberVotes;
    private $keywords;
    private $question_must;
    private $question_musnt;

    //get the question from de db
    static function withId($question_id = null) {
        
        $query = \CT\CT_DAO::getQuery('question', 'getById');
        $arr = array(
            ':question_id' => $question_id,
            ':ct_id' => $_SESSION['ct_id'],
        );
        $question = new CT_Question();
        $questions = $query['PDOX']->rowDie($query['sentence'], $arr);
        \CT\CT_DAO::setObjectPropertiesFromArray($question, $questions);
        return $question;
    }

    public function __construct($question_id = null, $test_id = null) {
        if (isset($question_id)) {
            $this->testId = $test_id;
            $question = \CT\CT_Test::findTestForImportQuestionId($question_id, $test_id);
            $this->question_id = $question_id;
            $this->ct_id = $_SESSION["ct_id"];
            $this->title = $question->getTitle();
            $this->type = $question->getType();
            $this->difficulty = $question->getDifficulty();
            $this->averageGradeUnderstability = $question->getAverageGradeUnderstability();
            $this->averageGradeDifficulty = $question->getAverageGradeDifficulty();
            $this->averageGradeTime = $question->getAverageGradeTime();
            $this->averageGrade = $question->getAverageGrade();
            $this->numberVotes = $question->getNumberVotes();
            $this->keywords = $question->getKeywords();
            $this->question_must = $question->getQuestionMust();
            $this->question_musnt = $question->getQuestionMusnt();
        }
    }

    //necessary to use json_encode with question objects
    public function jsonSerialize() {
        return [
            'question_id' => $this->getQuestionId(),
            'ct_id' => $this->getCtId(),
            'question_num' => $this->getQuestionNum(),
            'title' => $this->getTitle(),
            'type' => $this->getType(),
            'difficulty' => $this->getDifficulty(),
            'averageGradeUnderstability' => $this->getAverageGradeUnderstability(),
            'averageGradeDifficulty' => $this->getAverageGradeDifficulty(),
            'averageGradeTime' => $this->getAverageGradeTime(),
            'averageGrade' => $this->getAverageGrade(),
            'numberVotes' => $this->getNumberVotes(),
            'keywords' => $this->getKeywords(),
            'question_must' => $this->getQuestionMust(),
            'question_musnt' => $this->getQuestionMusnt()
        ];
    }

    //gets a token to use the api
    static function getToken() {

        global $CFG;
        $url = $CFG->repositoryUrl."/api/auth/signin";

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);
        curl_close($curl);

        return "Authorization: Bearer " . $result;
    }

    
    static function apiCall($urlAdd, $value = "") {
        global $CFG;
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json', self::getToken()));
        $url = $CFG->repositoryUrl."/api/tests/" . $urlAdd . $value;
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($result);

        return $response;
    }

    //returns the test of the questions
    static function findQuestions($questions) {
        global $translator;
        $response = array();
        $url = "getTestId/";

        foreach ($questions as $question) {
            $result = Self::apiCall($url, $question['test_id']);
            if (isset($result)) {
                foreach ($result->questions as $question1) {
                    if ($question1->id == $question['question_id']) {
                        $question1->question_num = $question['question_num'];
                        $question1->test_id = $question['test_id'];
                        $question1->ct_id = $question['ct_id'];

                        array_push($response, $question1);
                    }
                }
            } else {
                $_SESSION['error'] = $translator->trans('backend-messages.connection.failed');
            }
        }

        return $response;
    }

    
    function createAnswer($user_id, $answer_txt, $answer_language = null) {
        $answer = \CT\CT_Answer::getByUserAndQuestion($user_id, $this->getQuestionId(), $this->getCtId());
        if ($answer->getAnswerId() !== null) {
            $exists = true;
        } else {
            $exists = false;
        }
        $array = Array();
        
        //fill the answer
        $answer->setUserId($user_id);
        $answer->setQuestionId($this->getQuestionId());
        $answer->setAnswerTxt($answer_txt);
        $answer->setAnswerLanguage($answer_language);
        $answer->setCtId($this->getCtId());
        
        //returns if the question has been passed
        if($this->preGrade($answer)) {
            $this->grade($answer);
        }
        
        //save the answer
        $answer->save();
        $this->answers = $this->getAnswers();
        array_push($this->answers, $answer);
        $main = $this->getMain();
        $main->gradeUser($answer->getUserId());

        $array['answer'] = $answer;
        $array['exists'] = $exists;
        return $array;
    }

    
    static function findQuestionForImportByPage($page) {
        global $CFG;
        $url = $CFG->repositoryUrl . "/api/questions/getAllQuestions/" . $page;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json', self::getToken()));
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);
        curl_close($curl);
        
        $decode = json_decode($result);
        $totalPages = $decode[1];
        $questions = json_encode($decode[0]);
        $questions = self::MapJsonToQuestionsArray($questions);
        $array = ['questions' => $questions, 'totalPages' => $totalPages[0]];

        return $array;
    }

    //Find the questions on the repo by the tags
    static function findQuestionForImportByValue($value = null, $page = 0) {

        //if values is passed check if is already on the array
        if ($value) {
            CT_Test::checkerAdd($value);
        }
        $array = CT_Test::checker("questions");
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
            
            //decode the questions from json and maps to Question objects
            $questions = json_encode($decode[0]);
            $questions = self::MapJsonToQuestionsArray($questions);
            
            
            $array = ['questions' => $questions, 'totalPages' => $totalPages[0]];
        } else {
            //if not tags
            
            $array = \CT\CT_Question::findQuestionForImportByPage($page);
        }
        return $array;
    }

    static function findQuestionsForImportByDeleteValue($value) {
        
        global $CFG;
        //Deletes the value passed
        CT_Test::checkerDelete($value);
        
        //Check if there is any value left
        $array = CT_Test::checker("questions");
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
            $questions = json_encode($decode[0]);
            $questions = self::MapJsonToQuestionsArray($questions);
            $array = ['questions' => $questions, 'totalPages' => $totalPages[0]];
        } else {
            //if there is no value left
            
            $array = \CT\CT_Question::findQuestionForImportByPage(0);
        }
        return $array;
    }

    //Find question by id
    static function findQuestionForImportId($id) {
        global $CFG;
        $url = $CFG->repositoryUrl . "/api/tests/getQuestion/" . $id;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json', self::getToken()));
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);
        curl_close($curl);

        $question = json_decode($result);
        
        //check what type of question is to choose constructor
        if ($question->type == 'MYSQL') {
            $CTQuestion = CT_Test::mapObjectToSQLQuestion($question);
        } else {
            $CTQuestion = CT_Test::mapObjectToCodeQuestion($question);
        }
        return $CTQuestion;
    }

    
    static function MapJsonToQuestionsArray($json) {
        $response = json_decode($json);
        $questions = array();
        if ($response) {
            foreach ($response as $question) {

                
                //check what type of question is to choose constructor
                if ($question->type == 'MYSQL') {
                    $CTQuestion = CT_Test::mapObjectToSQLQuestion($question);
                } else {
                    $CTQuestion = CT_Test::mapObjectToCodeQuestion($question);
                }
                array_push($questions, $CTQuestion);
            }
            return $questions;
        }
    }

    function getNextQuestionNumber() {
        $query = \CT\CT_DAO::getQuery('question', 'getNextQuestionNumber');
        $arr = array(':ct_id' => $this->getCtId());
        $lastNum = $query['PDOX']->rowDie($query['sentence'], $arr)["lastNum"];
        return $lastNum + 1;
    }

    static function fixUpQuestionNumbers($ct_id) {
        $query = \CT\CT_DAO::getQuery('question', 'fixUpQuestionNumbers');
        $arr = array(':ctId' => $ct_id);
        $query['PDOX']->queryDie($query['sentence'], $arr);
    }

    /**
     * @return \CT\CT_Answer[] $answers
     */
    public function getAnswers() {
        if (!is_array($this->answers)) {

            $this->answers = array();
            $query = \CT\CT_DAO::getQuery('question', 'getAnswers');
            $arr = array(':questionId' => $this->getQuestionId(),
                ':ctId' => $this->getCtId());
            $answers = $query['PDOX']->allRowsDie($query['sentence'], $arr);
            $this->answers = \CT\CT_DAO::createObjectFromArray(\CT\CT_Answer::class, $answers);
        }

        return $this->answers;
    }

    public function getNumberAnswers() {
        return count($this->getAnswers());
    }
    
    public function getQuestionByType()
    {
        global $CFG;
        $class = $this->getMain()->getTypeProperty('class', $this->getType());
        if($class=='CT\CT_QuestionSQL' ){
            return CT_QuestionSQL::withId($this->getQuestionId());
        }else{
        return new $class($this->getQuestionId());
        }
        
        }

    /**
     * @return CT_Question
     */
    public function getQuestionParent() {
        return CT_Question::withId($this->getQuestionId());
    }

    public function setQuestionParentProperties() {
        \CT\CT_DAO::setObjectPropertiesFromArray($this, \CT\CT_DAO::setObjectPropertiesToArray($this->getQuestionParent()));
    }

    public function isNew() {
        $query = \CT\CT_DAO::getQuery('question', 'exists');

        $arr = array(
            ':question_id' => $this->getQuestionId(),
            ':ct_id' => $this->getCtId(),
        );
        $questionId = $query['PDOX']->rowDie($query['sentence'], $arr)["questionId"];
        return !(isset($questionId) && $questionId > 0);
    }

    public function save() {
        global $CFG;
        $currentTime = new \DateTime('now', new \DateTimeZone($CFG->timezone));
        $currentTime = $currentTime->format("Y-m-d H:i:s");
        if ($this->isNew()) {
            $this->setQuestionNum($this->getNextQuestionNumber());
            $query = \CT\CT_DAO::getQuery('question', 'insert');

            $arr = array(
                ':question_id' => $this->getQuestionId(),
                ':ct_id' => $this->getCtId(),
                ':question_num' => $this->getQuestionNum(),
                ':type' => $this->getType(),
                ':title' => $this->getTitle(),
                ':question_must' => $this->getQuestionMust(),
                ':question_musnt' => $this->getQuestionMusnt()
            );
            $query['PDOX']->queryDie($query['sentence'], $arr);
            
        }
    }

    public function update() {

        $query = \CT\CT_DAO::getQuery('question', 'update');

        $arr = array(
            ':question_id' => $this->getQuestionId(),
            ':ct_id' => $this->getCtId(),
            ':question_num' => $this->getQuestionNum()
        );
        $query['PDOX']->queryDie($query['sentence'], $arr);
    }

    function delete() {
        $query = \CT\CT_DAO::getQuery('question', 'delete');
        $arr = array(
            ':question_id' => $this->getQuestionId(),
            ':ct_id' => $this->getCtId()
        );
        $query['PDOX']->queryDie($query['sentence'], $arr);
    }

    protected function preGrade(CT_Answer $answer) {
        $answerTxt = $answer->getAnswerTxt();
        $preGrade = (true && 1);
        return $preGrade;
    }
    
     /**
     * @param $answerTxt
     * @param $must_musnt
     * @param $all bool true: must contain all expresions | false: shouldn't contain any
     * @return bool
     */
    protected function contains($answerTxt, $must_musnt, $all)
    {
        if (strlen($must_musnt) == 0 ) return true;
        $array_of_expressions = explode(PHP_EOL, $must_musnt);
        $i = 0;
        foreach ($array_of_expressions as $expression)
        {
            if (stripos($answerTxt, trim($expression)) !== FALSE) $i++;
        }

        $contains = ($all && $i == count($array_of_expressions)) || (!$all && $i == 0);
        return $contains;
    }

    public function getMain() {
        return new CT_Main($this->getCtId());
    }

    /**
     * @return mixed
     */
    public function getQuestionId() {
        return $this->question_id;
    }

    /**
     * @param mixed $question_id
     */
    public function setQuestionId($question_id) {
        $this->question_id = $question_id;
    }

    /**
     * @return mixed
     */
    public function getCtId() {
        return $this->ct_id;
    }

    /**
     * @param mixed $ct_id
     */
    public function setCtId($ct_id) {
        $this->ct_id = $ct_id;
    }

    /**
     * @return mixed
     */
    public function getQuestionNum() {
        return $this->question_num;
    }

    /**
     * @param mixed $question_num
     */
    public function setQuestionNum($question_num) {
        $this->question_num = $question_num;
    }

    public function getType() {
        return $this->type;
    }

    public function getDifficulty() {
        return $this->difficulty;
    }

    public function setType($type): void {
        $this->type = $type;
    }

    public function setDifficulty($difficulty): void {
        $this->difficulty = $difficulty;
    }

    public function getTestId() {
        return $this->testId;
    }

    public function setTestId($testId): void {
        $this->testId = $testId;
    }

    public function getAverageGradeDifficulty() {
        return $this->averageGradeDifficulty;
    }

    public function getAverageGradeUnderstability() {
        return $this->averageGradeUnderstability;
    }

    public function getAverageGradeTime() {
        return $this->averageGradeTime;
    }

    public function getNumberVotes() {
        return $this->numberVotes;
    }

    public function setAverageGradeDifficulty($averageGradeDifficulty): void {
        $this->averageGradeDifficulty = $averageGradeDifficulty;
    }

    public function setAverageGradeUnderstability($averageGradeUnderstability): void {
        $this->averageGradeUnderstability = $averageGradeUnderstability;
    }

    public function setAverageGradeTime($averageGradeTime): void {
        $this->averageGradeTime = $averageGradeTime;
    }

    public function setNumberVotes($numberVotes): void {
        $this->numberVotes = $numberVotes;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title): void {
        $this->title = $title;
    }

    public function getAverageGrade() {
        return $this->averageGrade;
    }

    public function setAverageGrade($averageGrade): void {
        $this->averageGrade = $averageGrade;
    }

    public function getKeywords() {
        return $this->keywords;
    }

    public function setKeywords($keywords): void {
        $this->keywords = $keywords;
    }

    public function getQuestionMust() {
        return $this->question_must;
    }

    public function getQuestionMusnt() {
        return $this->question_musnt;
    }

    public function setQuestionMust($question_must): void {
        $this->question_must = $question_must;
    }

    public function setQuestionMusnt($question_musnt): void {
        $this->question_musnt = $question_musnt;
    }

}

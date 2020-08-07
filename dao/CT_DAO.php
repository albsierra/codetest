<?php
namespace CT\DAO;

class CT_DAO {

    private $PDOX;
    private $p;

    public function __construct() {
        global $PDOX;
        global $CFG;
        $this->PDOX = $PDOX;
        $this->p = $CFG->dbprefix;
    }

    public static function getConnection() {
        global $PDOX;
        global $CFG;
        return array('PDOX' => $PDOX, 'p' => $CFG->dbprefix);
    }

    function countAnswersForQuestion($question_id) {
        $query = "SELECT COUNT(*) as total FROM {$this->p}ct_answer WHERE question_id = :questionId;";
        $arr = array(':questionId' => $question_id);
        return $this->PDOX->rowDie($query, $arr)["total"];
    }

    function getUsersWithAnswers($ct_id) {
        $query = "SELECT DISTINCT user_id FROM {$this->p}ct_answer a join {$this->p}ct_question q on a.question_id = q.question_id WHERE q.ct_id = :ctId;";
        $arr = array(':ctId' => $ct_id);
        return $this->PDOX->allRowsDie($query, $arr);
    }

    function getStudentAnswerForQuestion($question_id, $user_id) {
        $query = "SELECT * FROM {$this->p}ct_answer WHERE question_id = :questionId AND user_id = :userId; ";
        $arr = array(':questionId' => $question_id, ':userId' => $user_id);
        return $this->PDOX->rowDie($query, $arr);
    }

    function getMostRecentAnswerDate($user_id, $ct_id) {
        $query = "SELECT max(a.modified) as modified FROM {$this->p}ct_answer a join {$this->p}ct_question q on a.question_id = q.question_id WHERE a.user_id = :userId AND q.ct_id = :ctId;";
        $arr = array(':userId' => $user_id, ':ctId' => $ct_id);
        $context = $this->PDOX->rowDie($query, $arr);
        return $context['modified'];
    }

    function getNumberQuestionsAnswered($user_id, $ct_id) {
        $query = "SELECT count(*) as num_answered FROM {$this->p}ct_answer a join {$this->p}ct_question q on a.question_id = q.question_id WHERE a.user_id = :userId AND q.ct_id = :ctId AND a.answer_txt is not null;";
        $arr = array(':userId' => $user_id, ':ctId' => $ct_id);
        $context = $this->PDOX->rowDie($query, $arr);
        return $context['num_answered'];
    }

    function getStudentGrade($ct_id, $user_id) {
        $query = "SELECT grade FROM {$this->p}ct_grade WHERE ct_id = :ct_id AND user_id = :user_id";
        $arr = array(':ct_id' => $ct_id, ':user_id' => $user_id);
        $context = $this->PDOX->rowDie($query, $arr);
        return $context["grade"];
    }

    function createGrade($ct_id, $user_id, $grade, $current_time) {
        $query = "INSERT INTO {$this->p}ct_grade (ct_id, user_id, grade, modified) VALUES (:ct_id, :user_id, :grade, :currentTime);";
        $arr = array(':ct_id' => $ct_id,':user_id' => $user_id, ':grade' => $grade, ':currentTime' => $current_time);
        $this->PDOX->queryDie($query, $arr);
        return $this->PDOX->lastInsertId();
    }

    function updateGrade($ct_id, $user_id, $grade, $current_time) {
        $query = "UPDATE {$this->p}ct_grade set grade = :grade, modified = :currentTime where user_id = :user_id AND ct_id = :ct_id;";
        $arr = array(':grade' => $grade, ':currentTime' => $current_time, ':user_id' => $user_id, ':ct_id' => $ct_id);
        $this->PDOX->queryDie($query, $arr);
    }

    function findEmail($user_id) {
        $query = "SELECT email FROM {$this->p}lti_user WHERE user_id = :user_id;";
        $arr = array(':user_id' => $user_id);
        $context = $this->PDOX->rowDie($query, $arr);
        return $context["email"];
    }

    function findDisplayName($user_id) {
        $query = "SELECT displayname FROM {$this->p}lti_user WHERE user_id = :user_id;";
        $arr = array(':user_id' => $user_id);
        $context = $this->PDOX->rowDie($query, $arr);
        return $context["displayname"];
    }

    function findInstructors($context_id) {
        $query = "SELECT user_id FROM {$this->p}lti_membership WHERE context_id = :context_id AND role = '1000';";
        $arr = array(':context_id' => $context_id);
        return $this->PDOX->allRowsDie($query, $arr);
    }

    function isUserInstructor($context_id, $user_id) {
        $query = "SELECT role FROM {$this->p}lti_membership WHERE context_id = :context_id AND user_id = :user_id;";
        $arr = array(':context_id' => $context_id, ':user_id' => $user_id);
        $role = $this->PDOX->rowDie($query, $arr);
        return $role["role"] == '1000';
    }

    public static function setObjectPropertiesFromArray(&$object, $arrayProperties) {
        foreach($arrayProperties as $k => $v) {
            call_user_func_array(array($object, 'set'.preg_replace('/[^\da-z]/i', '', mb_convert_case($k, MB_CASE_TITLE))), array($v));
        }
    }

    public static function setObjectPropertiesToArray($object) {
        return (array) $object;
    }

    public static function createObjectFromArray($class, $array) {
        $arrayObject = array();
        foreach($array as $element) {
            $object = new $class();
            self::setObjectPropertiesFromArray($object, $element);
            array_push($arrayObject, $object);
        }
        return $arrayObject;
    }

    public static function getQuery($class, $name)
    {
        $connection = self::getConnection();
        $MainQueries = array(
            'getByCtId' => "SELECT * FROM {$connection['p']}ct_main WHERE ct_id = :ct_id",
            'getMain' => "SELECT ct_id FROM {$connection['p']}ct_main WHERE context_id = :context_id AND link_id = :link_id",
            'getQuestionsId' => "SELECT question_id FROM {$connection['p']}ct_question "
                . "WHERE ct_id = :ctId "
                . "order by question_num",
            'insert' => "INSERT INTO {$connection['p']}ct_main (user_id, context_id, link_id, modified) VALUES (:userId, :contextId, :linkId, :currentTime)",
            'update' => "UPDATE {$connection['p']}ct_main set "
                . "`user_id` = :user_id, "
                . "`context_id` = :context_id, "
                . "`link_id` = :link_id, "
                . "`title` = :title, "
                . "`type` = :type, "
                . "`seen_splash` = :seen_splash, "
                . "`shuffle` = :shuffle, "
                . "`points` = :points, "
                . "`modified` = :modified "
                . "WHERE ct_id = :ctId",
            'delete' => "DELETE FROM {$connection['p']}ct_main WHERE ct_id = :mainId AND user_id = :userId",
        );
        $QuestionQueries = array(
            'insert' => "INSERT INTO {$connection['p']}ct_question  "
                . "(`ct_id`, `question_num`, `question_txt`, `modified` ) "
                . "VALUES (:ctId, :question_num, :question_txt, :modified )",
            'update' => "UPDATE {$connection['p']}ct_question set "
                . "`ct_id` = :ctId, "
                . "`question_num` = :question_num, "
                . "`question_txt` = :question_txt, "
                . "`modified` = :modified "
                . "WHERE question_id = :question_id",
            'delete' => "DELETE FROM {$connection['p']}ct_question WHERE question_id = :questionId;",
            'getAnswersId' => "SELECT answer_id FROM {$connection['p']}ct_answer WHERE question_id = :questionId;",
            'fixUpQuestionNumbers' => "SET @question_num = 0; UPDATE {$connection['p']}ct_question "
                . "set question_num = (@question_num:=@question_num+1) "
                . "WHERE ct_id = :ctId ORDER BY question_num",
            'getNextQuestionNumber' => "SELECT MAX(question_num) as lastNum FROM {$connection['p']}ct_question "
                . "WHERE ct_id = :ctId",
            'findQuestionsForImport' => "SELECT q.*, m.title as tooltitle, c.title as sitetitle "
                . "FROM {$connection['p']}ct_question q "
                . "join {$connection['p']}ct_main m on q.ct_id = m.ct_id "
                . "join {$connection['p']}lti_context c on m.context_id = c.context_id "
                . "WHERE m.user_id = :userId AND m.ct_id != :ct_id",
            'getById' => "SELECT * FROM {$connection['p']}ct_question "
                . "WHERE question_id = :question_id",
        );
        $AnswerQueries = array(
            'getByAnswerId' => "SELECT * FROM {$connection['p']}ct_answer WHERE answer_id = :answer_id",
            'insert' => "INSERT INTO {$connection['p']}ct_answer "
                . "(`user_id`, `question_id`, `answer_txt`, `answer_success`, `modified`) "
                . "VALUES (:userId, :questionId, :answerTxt, :answerSuccess, :modified)",
            'update' => "UPDATE {$connection['p']}ct_answer set "
                . "`user_id` = :userId, "
                . "`question_id` = :questionId, "
                . "`answer_txt` = :answerTxt, "
                . "`answer_success` = :answerSuccess"
                . "`modified` = :modified "
                . "WHERE answer_id = :answer_id",
            'deleteOne' => "DELETE FROM {$connection['p']}ct_answer WHERE answer_id = :answerId;",
            'deleteFromQuestions' => "DELETE FROM {$connection['p']}ct_answer WHERE user_id = :userId AND question_id in (/questionsId/)",
        );
        $queries = array(
            'main' => $MainQueries,
            'question' => $QuestionQueries,
            'answer' => $AnswerQueries,
        );
        return array(
            'PDOX' => $connection['PDOX'],
            'sentence' => $queries[$class][$name],
        );
    }

}

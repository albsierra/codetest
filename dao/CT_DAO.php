<?php
namespace CT;

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

    public static function setObjectPropertiesFromArray(&$object, $arrayProperties) {
        if(is_array($arrayProperties)){
            foreach($arrayProperties as $k => $v) {
                $function = array($object, 'set'.preg_replace('/[^\da-z]/i', '', mb_convert_case($k, MB_CASE_TITLE)));
                if(is_callable($function)) call_user_func_array($function, array($v));
            }
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
            'getMain' => "SELECT ct_id FROM {$connection['p']}ct_main "
                . "WHERE context_id = :context_id AND link_id = :link_id",
            'getQuestions' => "SELECT * FROM {$connection['p']}ct_question "
                . "WHERE ct_id = :ctId "
                . "order by question_num",
            'insert' => "INSERT INTO {$connection['p']}ct_main (user_id, context_id, link_id, modified) "
                . "VALUES (:userId, :contextId, :linkId, :currentTime)",
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
            'getAnswers' => "SELECT * FROM {$connection['p']}ct_answer "
                . "WHERE question_id = :questionId;",
            'fixUpQuestionNumbers' => "SET @question_num = 0; UPDATE {$connection['p']}ct_question "
                . "set question_num = (@question_num:=@question_num+1) "
                . "WHERE ct_id = :ctId ORDER BY question_num",
            'getNextQuestionNumber' => "SELECT MAX(question_num) as lastNum "
                . "FROM {$connection['p']}ct_question "
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
            'deleteFromQuestions' => "DELETE FROM {$connection['p']}ct_answer "
                . "WHERE user_id = :userId AND question_id in (/questionsId/)",
        );
        $UserQueries = array(
            'getByUserId' => "SELECT user_id, deleted, profile_id, displayname, email "
                . "FROM {$connection['p']}lti_user WHERE user_id = :user_id",
            'getUserRoles' => "SELECT role FROM {$connection['p']}lti_membership "
                . "WHERE context_id = :context_id AND user_id = :user_id",
            'findInstructors' => "SELECT {$connection['p']}lti_user.* "
                . "FROM {$connection['p']}lti_membership JOIN {$connection['p']}lti_user USING (user_id)"
                . "WHERE context_id = :context_id AND role = :role",
            'getUsersWithAnswers' => "SELECT DISTINCT {$connection['p']}lti_user.* "
                . "FROM {$connection['p']}ct_answer a join {$connection['p']}ct_question q USING (question_id) "
                . "JOIN {$connection['p']}lti_user USING (user_id)"
                . "WHERE q.ct_id = :ctId;",
            'getAnswerForQuestion' => "SELECT * FROM {$connection['p']}ct_answer "
                . "WHERE question_id = :questionId AND user_id = :userId",
            'getMostRecentAnswerDate' => "SELECT max(a.modified) as modified "
                . "FROM {$connection['p']}ct_answer a "
                . "join {$connection['p']}ct_question q on a.question_id = q.question_id "
                . "WHERE a.user_id = :userId AND q.ct_id = :ctId",
            'getNumberQuestionsAnswered' => "SELECT count(*) as num_answered "
                . "FROM {$connection['p']}ct_answer a "
                . "join {$connection['p']}ct_question q on a.question_id = q.question_id "
                . "WHERE a.user_id = :userId AND q.ct_id = :ctId AND a.answer_txt is not null",
            'getGrade' => "SELECT * FROM {$connection['p']}ct_grade "
                . "WHERE ct_id = :ct_id AND user_id = :user_id",
        );
        $GradeQueries = array(
            'getByGradeId' => "SELECT * FROM {$connection['p']}ct_grade "
                . "WHERE grade_id = :grade_id",
            'insert' => "INSERT INTO {$connection['p']}ct_grade "
                . "(ct_id, user_id, grade, modified) "
                . "VALUES (:ctId, :userId, :grade, :modified)",
            'update' => "UPDATE {$connection['p']}ct_grade "
                . "set user_id = :userId, ct_id = :ctId, grade = :grade, modified = :modified "
                . "WHERE grade_id = :gradeId",
        );
        $queries = array(
            'main' => $MainQueries,
            'question' => $QuestionQueries,
            'answer' => $AnswerQueries,
            'user' => $UserQueries,
            'grade' => $GradeQueries,
        );
        return array(
            'PDOX' => $connection['PDOX'],
            'sentence' => $queries[$class][$name],
        );
    }

}

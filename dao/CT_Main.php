<?php


namespace CT;

use \Tsugi\Core\Result;

class CT_Main
{
    private $ct_id;
    private $user_id;
    private $context_id;
    private $link_id;
    private $title;
    private $type;
    private $seen_splash;
    private $shuffle;
    private $points;
    private $modified;
    private $questions;

    public function __construct($ct_id = null)
    {
        $context = array();
        if (isset($ct_id)) {
            $query = \CT\CT_DAO::getQuery('main', 'getByCtId');
            $arr = array(':ct_id' => $ct_id);
            $context = $query['PDOX']->rowDie($query['sentence'], $arr);
        }
        \CT\CT_DAO::setObjectPropertiesFromArray($this, $context);
    }

    public static function getMainFromContext($context_id, $link_id, $user_id = null, $current_time = null) {
        $object = self::getMain($context_id, $link_id);
        if (!$object->getCtId()) {
            $object = self::createMain($user_id, $context_id, $link_id, $current_time);
        }
        return $object;
    }

    public static function getMainsFromContext($context_id) {
        $query = \CT\CT_DAO::getQuery('main', 'getMainsFromContext');
        $arr = array(':context_id' => $context_id);
        return \CT\CT_DAO::createObjectFromArray(self::class, $query['PDOX']->allRowsDie($query['sentence'], $arr));
    }

    public static function getMain($context_id, $link_id) {
        $query = \CT\CT_DAO::getQuery('main','getMain');
        $arr = array(':context_id' => $context_id, ':link_id' => $link_id);
        $context = $query['PDOX']->rowDie($query['sentence'], $arr);
        return new self($context['ct_id']);
    }

    public static function createMain($user_id, $context_id, $link_id, $current_time) {
        $query = \CT\CT_DAO::getQuery('main','insert');
        $arr = array(':userId' => $user_id, ':contextId' => $context_id, ':linkId' => $link_id, ':currentTime' => $current_time);
        $query['PDOX']->queryDie($query['sentence'], $arr);
        return new self($query['PDOX']->lastInsertId());
    }

    /**
     * @return \CT\CT_Question[] $questions
     */
    function getQuestions() {
        // TODO Crear array de objetos Code o SQL según corresponda
        // a través de JOIN con la tabla correspondiente
        if(!is_array($this->questions)) {
            $this->questions = array();
            $query = \CT\CT_DAO::getQuery('main', 'getQuestions');
            $arr = array(':ctId' => $this->getCtId());
            $questions = $query['PDOX']->allRowsDie($query['sentence'], $arr);
            $this->questions = \CT\CT_DAO::createObjectFromArray(\CT\CT_Question::class, $questions);
        }
        return $this->questions;
    }

    /**
     * @param $context mixed An array or object with the data to insert or import.
     * @return CT_Question The created question.
     */
    function createQuestion($context) {
        global $CFG;
        if(is_array($context)) {
            $class = $this->getTypeProperty('class');
            $question = new $class();
            \CT\CT_DAO::setObjectPropertiesFromArray($question, $context);
        } else {
            $question = clone $context;
        }
        $question->setCtId($this->getCtId());
        $question->save();
        return $question;
    }

    public function getUserGrade($userId)
    {
        // Get result record for user
        $query = \CT\CT_DAO::getQuery('main','getResultUser');
        $arr = array(':user_id' => $userId, ':link_id' => $this->getLinkId());
        $row = $query['PDOX']->rowDie($query['sentence'], $arr);
        return $row;
    }

    public function getUserGradeValue($userId)
    {
        $grade = $this->getUserGrade($userId)['grade'];
        if(is_null($grade)) {
            $this->gradeUser($userId);
            $grade = $this->getUserGrade($userId)['grade'];
        }
        $value = $this->getPoints() * $grade;
        return $value;
    }

    public function gradeUser($userId, $grade = null)
    {
        if(is_null($grade)) {
            $corrects = 0;
            $totalQuestions = count($this->getQuestions());
            foreach ($this->getAnswersByUser($userId) as $answer) {
                if($answer->getAnswerSuccess()) $corrects++;
            }
            $grade = $corrects * $this->getPoints() / $totalQuestions;
        }
        $student = new \CT\CT_User($userId);
        $currentGrade = $student->getGrade($this->getCtId());
        $currentGrade->setCtId($this->getCtId());
        $currentGrade->setUserId($student->getUserId());
        $currentGrade->setGrade($grade);
        $currentGrade->save();

        $_SESSION['success'] = "Grade saved.";

        // Calculate percentage and post
        $percentage = ($grade * 1.0) / $this->getPoints();

        $row = $this->getUserGrade($userId);

        Result::gradeSendStatic($percentage, $row);
    }

    function getTypeProperty($property) {
        global $CFG;
        return $CFG->CT_Types['types'][$this->getType()][$property];
    }

    function getStudentsOrderedByDate() {
        $studentsUnordered = \CT\CT_User::getUsersWithAnswers($this->getCtId());
        $studentAndDate = array();
        foreach($studentsUnordered as $student) {
            $studentAndDate[$student->getMostRecentAnswerDate($this->getCtId())] = $student;
        }
        // Sort students by mostRecentDate desc
        krsort($studentAndDate);
        $students = array();
        $index = 0;
        foreach ($studentAndDate as $date => $user) {
            $mostRecentDate = new \DateTime($date);
            $students[$index]['user'] = $user;
            $students[$index]['isInstructor'] = $user->isInstructor($this->getContextId());
            if (!$students[$index]['isInstructor']) {
                $students[$index]['formattedMostRecentDate'] = $mostRecentDate->format("m/d/y") . " | " . $mostRecentDate->format("h:i A");
                $students[$index]['numberAnswered'] = $user->getNumberQuestionsAnswered($this->getCtId());
                $students[$index]['grade'] = $user->getGrade($this->getCtId())->getGrade();
            }
            $index++;
        }
        return $students;
    }

    private function getAnswersByUser($userId)
    {
        $query = \CT\CT_DAO::getQuery('main', 'getAnswersByUser');
        $arr = array(':ctId' => $this->getCtId(), ':userId' => $userId);
        return \CT\CT_DAO::createObjectFromArray(CT_Answer::class, $query['PDOX']->allRowsDie($query['sentence'], $arr));
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
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param mixed $user_id
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * @return mixed
     */
    public function getContextId()
    {
        return $this->context_id;
    }

    /**
     * @param mixed $context_id
     */
    public function setContextId($context_id)
    {
        $this->context_id = $context_id;
    }

    /**
     * @return mixed
     */
    public function getLinkId()
    {
        return $this->link_id;
    }

    /**
     * @param mixed $link_id
     */
    public function setLinkId($link_id)
    {
        $this->link_id = $link_id;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getSeenSplash()
    {
        return $this->seen_splash;
    }

    /**
     * @param mixed $seen_splash
     */
    public function setSeenSplash($seen_splash)
    {
        $this->seen_splash = $seen_splash;
    }

    /**
     * @return mixed
     */
    public function getShuffle()
    {
        return $this->shuffle;
    }

    /**
     * @param mixed $shuffle
     */
    public function setShuffle($shuffle)
    {
        $this->shuffle = $shuffle;
    }

    /**
     * @return mixed
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * @param mixed $points
     */
    public function setPoints($points)
    {
        $this->points = $points;
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

    public function save() {
        $query = \CT\CT_DAO::getQuery('main','update');
        $arr = array(
            ':user_id' => $this->getUserId(),
            ':context_id' => $this->getContextId(),
            ':link_id' => $this->getLinkId(),
            ':title' => $this->getTitle(),
            ':type' => $this->getType(),
            ':seen_splash' => $this->getSeenSplash(),
            ':shuffle' => $this->getShuffle(),
            ':points' => $this->getPoints(),
            ':modified' => $this->getModified(),
            ':ctId' => $this->getCtId()
        );
        $query['PDOX']->queryDie($query['sentence'], $arr);
    }

    function delete($user_id) {
        $query = \CT\CT_DAO::getQuery('main','delete');
        $arr = array(':mainId' => $this->getCtId(), ':userId' => $user_id);
        $query['PDOX']->queryDie($query['sentence'], $arr);
    }
}

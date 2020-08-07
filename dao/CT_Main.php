<?php


namespace CT\DAO;


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

    public function __construct($ct_id = null)
    {
        $context = array();
        if (isset($ct_id)) {
            $query = CT_DAO::getQuery('main', 'getByCtId');
            $arr = array(':ct_id' => $ct_id);
            $context = $query['PDOX']->rowDie($query['sentence'], $arr);
        }
        CT_DAO::setObjectPropertiesFromArray($this, $context);
    }

    public static function getMainFromContext($context_id, $link_id, $user_id = null, $current_time = null) {
        $object = self::getMain($context_id, $link_id);
        if (!$object->getCtId()) {
            $object = self::createMain($user_id, $context_id, $link_id, $current_time);
        }
        return $object;
    }

    public static function getMain($context_id, $link_id) {
        $query = CT_DAO::getQuery('main','getMain');
        $arr = array(':context_id' => $context_id, ':link_id' => $link_id);
        $context = $query['PDOX']->rowDie($query['sentence'], $arr);
        return new self($context['ct_id']);
    }

    public static function createMain($user_id, $context_id, $link_id, $current_time) {
        $query = CT_DAO::getQuery('main','insert');
        $arr = array(':userId' => $user_id, ':contextId' => $context_id, ':linkId' => $link_id, ':currentTime' => $current_time);
        $query['PDOX']->queryDie($query['sentence'], $arr);
        return new self($query['PDOX']->lastInsertId());
    }

    function getQuestions() {
        return CT_Question::getByMain($this->getCtId());
    }

    function createQuestion($questionText) {
        $question = new CT_Question();
        $question->setCtId($this->getCtId());
        $question->setQuestionTxt($questionText);
        $question->save();
        return $question;
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
        $query = CT_DAO::getQuery('main','update');
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
        $query = CT_DAO::getQuery('main','delete');
        $arr = array(':mainId' => $this->getCtId(), ':userId' => $user_id);
        $query['PDOX']->queryDie($query['sentence'], $arr);
    }

}

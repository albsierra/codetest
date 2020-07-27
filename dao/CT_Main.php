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
            $connection = CT_DAO::getConnection();
            $query = "SELECT * FROM {$connection['p']}ct_main WHERE ct_id = :ct_id";
            $arr = array(':ct_id' => $ct_id);
            $context = $connection['PDOX']->rowDie($query, $arr);
        }
        $this->setPropertiesFromArray($context);
    }

    public static function getMainFromContext($context_id, $link_id, $user_id = null, $current_time = null) {
        $object = self::getMain($context_id, $link_id);
        if (!$object->getCtId()) {
            $object = self::createMain($user_id, $context_id, $link_id, $current_time);
        }
        return $object;
    }

    public static function getMain($context_id, $link_id) {
        $connection = CT_DAO::getConnection();
        $query = "SELECT ct_id FROM {$connection['p']}ct_main WHERE context_id = :context_id AND link_id = :link_id";
        $arr = array(':context_id' => $context_id, ':link_id' => $link_id);
        $context = $connection['PDOX']->rowDie($query, $arr);
        return new self($context['ct_id']);
    }

    public static function createMain($user_id, $context_id, $link_id, $current_time) {
        $connection = CT_DAO::getConnection();
        $query = "INSERT INTO {$connection['p']}ct_main (user_id, context_id, link_id, modified) VALUES (:userId, :contextId, :linkId, :currentTime);";
        $arr = array(':userId' => $user_id, ':contextId' => $context_id, ':linkId' => $link_id, ':currentTime' => $current_time);
        $connection['PDOX']->queryDie($query, $arr);
        return new self($connection['PDOX']->lastInsertId());
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

    private function setPropertiesFromArray($arrayProperties) {
        foreach($arrayProperties as $k => $v)
            $this->$k = $v;
    }

    private function setPropertiesToArray() {
        return (array) $this;
    }

    public function save() {
        $connection = CT_DAO::getConnection();
        $query = "UPDATE {$connection['p']}ct_main set "
            . "`user_id` = :user_id, "
            . "`context_id` = :context_id, "
            . "`link_id` = :link_id, "
            . "`title` = :title, "
            . "`type` = :type, "
            . "`seen_splash` = :seen_splash, "
            . "`shuffle` = :shuffle, "
            . "`points` = :points, "
            . "`modified` = :modified "
            . "WHERE ct_id = :ctId";
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
        $connection['PDOX']->queryDie($query, $arr);
    }

    function delete($user_id) {
        $connection = CT_DAO::getConnection();
        $query = "DELETE FROM {$connection['p']}ct_main WHERE ct_id = :mainId AND user_id = :userId";
        $arr = array(':mainId' => $this->getCtId(), ':userId' => $user_id);
        $connection['PDOX']->queryDie($query, $arr);
    }

}

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

    public function __construct($context_id, $link_id, $user_id = null, $current_time = null) {
        $arrayProperties = self::getMain($context_id, $link_id);
        if (!$arrayProperties['ct_id']) {
            $arrayProperties = self::createMain($user_id, $context_id, $link_id, $current_time);
        }
        $this->setPropertiesFromArray($arrayProperties);
    }

    private function setPropertiesFromArray($arrayProperties) {
        foreach($arrayProperties as $k => $v)
            $this->$k = $v;
    }

    public static function getMain($context_id, $link_id) {
        $connection = CT_DAO::getConnection();
        $query = "SELECT * FROM {$connection['p']}ct_main WHERE context_id = :context_id AND link_id = :link_id";
        $arr = array(':context_id' => $context_id, ':link_id' => $link_id);
        $context = $connection['PDOX']->rowDie($query, $arr);
        return $context;
    }

    public static function getMainFromId($ct_id) {
        $connection = CT_DAO::getConnection();
        $query = "SELECT * FROM {$connection['p']}ct_main WHERE ct_id = :ct_id";
        $arr = array(':ct_id' => $ct_id);
        $context = $connection['PDOX']->rowDie($query, $arr);
        return $context;
    }

    public static function createMain($user_id, $context_id, $link_id, $current_time) {
        $connection = CT_DAO::getConnection();
        $query = "INSERT INTO {$connection['p']}ct_main (user_id, context_id, link_id, modified) VALUES (:userId, :contextId, :linkId, :currentTime);";
        $arr = array(':userId' => $user_id, ':contextId' => $context_id, ':linkId' => $link_id, ':currentTime' => $current_time);
        $connection['PDOX']->queryDie($query, $arr);
        return self::getMainFromId($connection['PDOX']->lastInsertId());
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

}
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

    private $PDOX;
    private $p;

    public function __construct()
    {
        global $PDOX;
        global $CFG;
        $this->PDOX = $PDOX;
        $this->p = $CFG->dbprefix;
    }


}
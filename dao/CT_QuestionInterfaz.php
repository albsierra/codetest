<?php


namespace CT;


interface CT_QuestionInterfaz
{
    public function __construct($question_id = null);

    public function save();

    function grade();
}
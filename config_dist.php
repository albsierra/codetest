<?php

// Make this require relative to the parent of the current folder
// http://stackoverflow.com/questions/24753758

require_once dirname(__DIR__)."/config.php";
require 'vendor/autoload.php';

$CFG->twig = array(
    'viewsPath' => __DIR__."/views",
    'debug' => true,
    'cachePath' => __DIR__."/tmp",
);

$CFG->CT_log = array(
    'debug' => true,
    'filePath' => __DIR__."/tmp/ctLog.log",
);

$CFG->CT_Types = array(
    'formsPath' => 'question/forms/',
    'studentsPath' => 'question/students/',
    'types' => array (
        array (
            'name' => 'sql',
            'class' => \CT\CT_QuestionSQL::class,
            'instructorForm' => 'questionSQLForm.php',
            'studentView' => 'questionSQLStudent.php',
            'sqlTypes' => array ('SELECT', 'DML'),
            'dbConnection' => array(
                'dbDriver' => 'mysql',
                'dbHostName' => 'localhost',
                'dbPort' => 3306,
                'dbUser' => 'dbUser',
                'dbPassword' => 'dbPass',
            ),
        ),
        array (
            'name' => 'programming',
            'class' => \CT\CT_QuestionCode::class,
            'instructorForm' => 'questionCodeForm.php',
            'studentView' => 'questionCodeStudent.php',
            'codeLanguages' => array (
                array( 'name' => 'PHP', 'ext' => 'php', 'command' => 'php -f'),
                array( 'name' => 'Java', 'ext' => 'java', 'command' => 'java -Duser.language=es -Duser.region=ES'),
                array( 'name' => 'Javascript', 'ext' => 'js', 'command' => 'node'),
            ),
        ),
    ),
);

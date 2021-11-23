<?php

// The SQL to uninstall this tool
$DATABASE_UNINSTALL = array(
    array( "{$CFG->dbprefix}ct_grade",
        "drop table if exists {$CFG->dbprefix}ct_grade"),
    array( "{$CFG->dbprefix}ct_sql_question",
        "drop table if exists {$CFG->dbprefix}ct_sql_question"),
    array( "{$CFG->dbprefix}ct_code_question",
        "drop table if exists {$CFG->dbprefix}ct_code_question"),
    array( "{$CFG->dbprefix}ct_answer",
        "drop table if exists {$CFG->dbprefix}ct_answer"),
    array( "{$CFG->dbprefix}ct_question",
        "drop table if exists {$CFG->dbprefix}ct_question"),
    array( "{$CFG->dbprefix}ct_main",
        "drop table if exists {$CFG->dbprefix}ct_main"),
);

// The SQL to create the tables if they don't exist
$DATABASE_INSTALL = array(
    array( "{$CFG->dbprefix}ct_main",
        "create table {$CFG->dbprefix}ct_main (
    ct_id       INTEGER NOT NULL AUTO_INCREMENT,
    user_id     INTEGER NOT NULL,
    context_id  INTEGER NOT NULL,
	link_id     INTEGER NOT NULL,
	title       VARCHAR(255) NULL,
	type     INTEGER NOT NULL DEFAULT 0,
	seen_splash BOOL NOT NULL DEFAULT 0,
	shuffle BOOL NOT NULL DEFAULT 0,
	points      FLOAT NOT NULL DEFAULT 100,
    modified    datetime NULL,
    
    PRIMARY KEY(ct_id)
	
) ENGINE = InnoDB DEFAULT CHARSET=utf8"),

    array("{$CFG->dbprefix}ct_question",
        "create table {$CFG->dbprefix}ct_question (
    question_id   VARCHAR(50) NOT NULL,
    ct_id         INTEGER NOT NULL,
    question_num  INTEGER NULL,
    title         VARCHAR (50) NOT NULL,
    type          VARCHAR (50) NOT NULL,
    question_must VARCHAR (50) ,
    question_musnt VARCHAR (50) ,
    
    CONSTRAINT `{$CFG->dbprefix}ct_ibfk_6`
        FOREIGN KEY (`ct_id`)
        REFERENCES `{$CFG->dbprefix}ct_main` (`ct_id`)
        ON DELETE CASCADE,
    PRIMARY KEY(question_id, ct_id)
	
) ENGINE = InnoDB DEFAULT CHARSET=utf8"),

    array( "{$CFG->dbprefix}ct_answer",
        "create table {$CFG->dbprefix}ct_answer (
    answer_id    INTEGER NOT NULL AUTO_INCREMENT,
    user_id      INTEGER NOT NULL,
    question_id  VARCHAR(50) NOT NULL,
    ct_id        INTEGER NOT NULL,
    answer_txt   TEXT NULL,
    answer_success BOOL NOT NULL DEFAULT 0,
    modified     datetime NULL,
    
    CONSTRAINT `{$CFG->dbprefix}ct_ibfk_7`
        FOREIGN KEY (`question_id` )
        REFERENCES `{$CFG->dbprefix}ct_question` (`question_id`)
        ON DELETE CASCADE,
        
    CONSTRAINT `{$CFG->dbprefix}ct_ibfk_8`
        FOREIGN KEY (`ct_id`)
        REFERENCES `{$CFG->dbprefix}ct_main` (`ct_id`)
        ON DELETE CASCADE,
    
    UNIQUE (user_id, question_id, ct_id),
    PRIMARY KEY(answer_id)
    
) ENGINE = InnoDB DEFAULT CHARSET=utf8"),

    array( "{$CFG->dbprefix}ct_code_question",
        "create table {$CFG->dbprefix}ct_code_question (
    question_id VARCHAR(50) NOT NULL,
    ct_id INT(11) NOT NULL,
    question_language INTEGER NOT NULL DEFAULT '1',
    question_input_test TEXT NULL DEFAULT NULL,
    question_input_grade TEXT NULL DEFAULT NULL,
    question_output_test TEXT NULL DEFAULT NULL,
    question_output_grade TEXT NULL DEFAULT NULL,
    question_solution TEXT NULL DEFAULT NULL,
    
  PRIMARY KEY (question_id, ct_id),
  CONSTRAINT `{$CFG->dbprefix}ct_ibfk_3`
    FOREIGN KEY (`question_id`, `ct_id`)
    REFERENCES `{$CFG->dbprefix}ct_question` (`question_id`, `ct_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB DEFAULT CHARACTER SET = utf8"),

    array( "{$CFG->dbprefix}ct_sql_question",
        "create table {$CFG->dbprefix}ct_sql_question (
    question_id VARCHAR(50) NOT NULL,
    ct_id INT(11) NOT NULL,
    question_dbms TINYINT NOT NULL DEFAULT 0,
    question_sql_type VARCHAR(20) NULL DEFAULT 'SELECT',
    question_database VARCHAR(100) NULL DEFAULT NULL,
    question_solution TEXT NULL DEFAULT NULL,
    question_probe TEXT NULL DEFAULT NULL,
    question_onfly LONGTEXT NULL DEFAULT NULL,
        
  PRIMARY KEY (question_id, ct_id),
  CONSTRAINT `{$CFG->dbprefix}ct_ibfk_4`
    FOREIGN KEY (`question_id`, `ct_id`)
    REFERENCES `{$CFG->dbprefix}ct_question` (`question_id`, `ct_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB DEFAULT CHARACTER SET = utf8"),

    array( "{$CFG->dbprefix}ct_grade",
        "create table {$CFG->dbprefix}ct_grade (
    grade_id        INTEGER NOT NULL AUTO_INCREMENT,
    ct_id           INTEGER NOT NULL,
    user_id         INTEGER NOT NULL,
    grade           FLOAT NOT NULL DEFAULT 0,
	modified        datetime NULL,
    
    CONSTRAINT `{$CFG->dbprefix}ct_ibfk_5`
        FOREIGN KEY (`ct_id`)
        REFERENCES `{$CFG->dbprefix}ct_main` (`ct_id`)
        ON DELETE CASCADE,
    
    PRIMARY KEY(grade_id)
    
) ENGINE = InnoDB DEFAULT CHARSET=utf8"),
);

$DATABASE_UPGRADE = function($oldversion) {
    global $CFG, $PDOX;

    // Add splash column
    if (!$PDOX->columnExists('seen_splash', "{$CFG->dbprefix}ct_main")) {
        $sql = "ALTER TABLE {$CFG->dbprefix}ct_main ADD seen_splash BOOL NOT NULL DEFAULT 0";
        echo("Upgrading: " . $sql . "<br/>\n");
        error_log("Upgrading: " . $sql);
        $q = $PDOX->queryDie($sql);
    }

    // Remove splash table
    if($PDOX->describe("{$CFG->dbprefix}ct_splash")) {
        $sql = "DROP TABLE {$CFG->dbprefix}ct_splash;";
        echo("Upgrading: " . $sql . "<br/>\n");
        error_log("Upgrading: " . $sql);
        $q = $PDOX->queryDie($sql);
    }

    // Add points column
    if (!$PDOX->columnExists('points', "{$CFG->dbprefix}ct_main")) {
        $sql = "ALTER TABLE {$CFG->dbprefix}ct_main ADD points FLOAT NOT NULL DEFAULT 100";
        echo("Upgrading: " . $sql . "<br/>\n");
        error_log("Upgrading: " . $sql);
        $q = $PDOX->queryDie($sql);
    }

    // Add title column
    if (!$PDOX->columnExists('title', "{$CFG->dbprefix}ct_main")) {
        $sql = "ALTER TABLE {$CFG->dbprefix}ct_main ADD title VARCHAR(255) NULL";
        echo("Upgrading: " . $sql . "<br/>\n");
        error_log("Upgrading: " . $sql);
        $q = $PDOX->queryDie($sql);
    }

    // Add onfly column in question_sql
    if (!$PDOX->columnExists('question_onfly', "{$CFG->dbprefix}ct_sql_question")) {
        $sql = "ALTER TABLE {$CFG->dbprefix}ct_sql_question ADD question_onfly LONGTEXT NULL DEFAULT NULL";
        echo("Upgrading: " . $sql . "<br/>\n");
        error_log("Upgrading: " . $sql);
        $q = $PDOX->queryDie($sql);
    }

    // Add answer_language column to allow student select the language of the answer
    if (!$PDOX->columnExists('answer_language', "{$CFG->dbprefix}ct_answer")) {
        $sql = "ALTER TABLE {$CFG->dbprefix}ct_answer ADD answer_language INTEGER NULL DEFAULT NULL";
        echo("Upgrading: " . $sql . "<br/>\n");
        error_log("Upgrading: " . $sql);
        $q = $PDOX->queryDie($sql);
    }

    return '202012201622';
};

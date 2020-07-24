<?php

// The SQL to uninstall this tool
$DATABASE_UNINSTALL = array(
    // Nothing
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
	seen_splash BOOL NOT NULL DEFAULT 0,
	points      FLOAT NOT NULL DEFAULT 100,
    modified    datetime NULL,
    
    PRIMARY KEY(ct_id)
	
) ENGINE = InnoDB DEFAULT CHARSET=utf8"),    
    array( "{$CFG->dbprefix}ct_question",
        "create table {$CFG->dbprefix}ct_question (
    question_id   INTEGER NOT NULL AUTO_INCREMENT,
    ct_id         INTEGER NOT NULL,
    question_num  INTEGER NULL,
    question_txt  TEXT NULL,   
    modified      datetime NULL,
    
    CONSTRAINT `{$CFG->dbprefix}ct_ibfk_1`
        FOREIGN KEY (`ct_id`)
        REFERENCES `{$CFG->dbprefix}ct_main` (`ct_id`)
        ON DELETE CASCADE,

    PRIMARY KEY(question_id)
	
) ENGINE = InnoDB DEFAULT CHARSET=utf8"),
    array( "{$CFG->dbprefix}ct_answer",
        "create table {$CFG->dbprefix}ct_answer (
    answer_id    INTEGER NOT NULL AUTO_INCREMENT,
    user_id      INTEGER NOT NULL,
    question_id  INTEGER NOT NULL,
	answer_txt   TEXT NULL,
    modified     datetime NULL,
    
    CONSTRAINT `{$CFG->dbprefix}ct_ibfk_2`
        FOREIGN KEY (`question_id`)
        REFERENCES `{$CFG->dbprefix}ct_question` (`question_id`)
        ON DELETE CASCADE,
    
    PRIMARY KEY(answer_id)
    
) ENGINE = InnoDB DEFAULT CHARSET=utf8"),
    array( "{$CFG->dbprefix}ct_grade",
        "create table {$CFG->dbprefix}ct_grade (
    grade_id        INTEGER NOT NULL AUTO_INCREMENT,
    ct_id           INTEGER NOT NULL,
    user_id         INTEGER NOT NULL,
    grade           FLOAT NOT NULL DEFAULT 0,
	modified        datetime NULL,
    
    CONSTRAINT `{$CFG->dbprefix}ct_ibfk_3`
        FOREIGN KEY (`ct_id`)
        REFERENCES `{$CFG->dbprefix}ct_main` (`ct_id`)
        ON DELETE CASCADE,
    
    PRIMARY KEY(grade_id)
    
) ENGINE = InnoDB DEFAULT CHARSET=utf8")
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

    return '201909031328';
};
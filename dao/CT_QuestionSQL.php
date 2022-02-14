<?php


namespace CT;


class CT_QuestionSQL extends CT_Question
{
    private $question_dbms;
    private $question_type;
    private $question_database;
    private $question_solution;
    private $question_probe;
    private $question_onfly;

    const DBMS_MYSQL = 0;
    const DBMS_ORACLE = 1;
    const DBMS_SQLITE = 2;
    const MUSNT = array('commit');

    public function __construct($question_id = null)
    {
        $context = array();
        if (isset($question_id)) {
            $query = \CT\CT_DAO::getQuery('questionSQL', 'getById');
            $arr = array(':question_id' => $question_id);
            $context = $query['PDOX']->rowDie($query['sentence'], $arr);
        }
        \CT\CT_DAO::setObjectPropertiesFromArray($this, $context);
        $this->setQuestionParentProperties();
    }

    public function getConnection($dbUser = null, $dbPassword = null, $dbName = null) {
        $dbms = $this->getQuestionDbms();
        $connectionConfig = $this->getMain()->getTypeProperty('dbConnections')[$dbms];

        $dbUser = $dbUser ? $dbUser : $connectionConfig['dbUser'];
        $dbPassword = $dbPassword ? $dbPassword : $connectionConfig['dbPassword'];
        $dbName = $dbName ? $dbName : $this->getQuestionDatabase();

        switch ($dbms)
        {
            case self::DBMS_MYSQL: //dsn mysql 'mysql:host=127.0.0.1;dbname=testdb'
                $dsn = "{$connectionConfig['dbDriver']}:host={$connectionConfig['dbHostName']};dbname={$dbName}";
                break;
            case self::DBMS_ORACLE: //dsn oracle 'oci:dbname=//localhost:1521/mydb'
                $dsn = "{$connectionConfig['dbDriver']}:dbname=//{$connectionConfig['dbHostName']}:{$connectionConfig['dbPort']}/{$connectionConfig['dbSID']}";
                break;
            case self::DBMS_SQLITE: //dsn sqlite currently only in memory.
                $dsn = "{$connectionConfig['dbDriver']}:{$connectionConfig['dbFile']}";
        }
        try {

            $connection =
                new \PDO(
                    $dsn,
                    $dbUser,
                    $dbPassword
                );
        }
        catch(\PDOException $e)
        {
            CT_DAO::debug($e->getMessage());
        }
        return $connection;
    }

    protected function preGrade(CT_Answer $answer)
    {
        $answerTxt = $answer->getAnswerTxt();
        $preGrade = $this->contains($answerTxt, implode ( PHP_EOL , self::MUSNT ), false);
        if(!$preGrade) {
            $answer->setAnswerSuccess(false);
        } else {
            $preGrade = parent::preGrade($answer);
        }
        return $preGrade;
    }

    public function grade($answer) {
        $outputSolution = $this->getQueryResult();
        $outputAnswer =  $this->getQueryResult($answer->getAnswerTxt());
        CT_DAO::debug(CT_Answer::getDiffWithSolution(print_r($outputSolution, true), print_r($outputAnswer, true)));

        $grade = $outputSolution === $outputAnswer ? 1 : 0;
        $answer->setAnswerSuccess($grade);
    }

    public function getQueryResult($answer = null) {
        $connection = $this->initTransaction();
        $queries = (isset($answer) ? $answer : $this->getQuestionSolution());
		foreach(explode(";", $queries) as $query) { // ; not accepted in Oracle driver.
			if($this->isQuery($query) && $resultQuery = $connection->prepare($query)) {
				$resultQuery->execute();
			}
		}
	
		if ($this->getQuestionType() == 'DML' || $this->getQuestionType() == 'DDL') {
            foreach(explode(";", $this->getQuestionProbe()) as $query) { // ; not accepted in Oracle driver.
                if($this->isQuery($query) && $resultQuery = $connection->prepare($query)) {
                    $resultQuery->execute();
                }
            }
            // We only watch the result of the last query. The last query will often be a SELECT query
		}
        $resultQueryArray = $resultQuery ? $resultQuery->fetchAll() : array();
        $resultQuery = null;
        $this->endTransaction($connection);
        return $resultQueryArray;
    }

    private function isQuery($query) {
		return strlen(trim($query)) > 1;
	}

    private function createOnflySchema(&$connection) {
        global $USER;
        $dbms = $this->getQuestionDbms();
        $connectionConfig = $this->getMain()->getTypeProperty('dbConnections')[$dbms];
        if( array_key_exists('onFly', $connectionConfig)
            && is_array($onFly = $connectionConfig['onFly'])
            && array_key_exists('allowed', $onFly)
            && $onFly['allowed']
            && strlen(trim($this->getQuestionOnfly())) > 0)
        {
            $nameAndPassword = $onFly['userPrefix'] . $this->getNameAndPasswordSuffix();
            switch ($dbms) {
                case self::DBMS_ORACLE:
                    if (
                        array_key_exists('createIsolateUserProcedure', $onFly)
                        && strlen(trim($onFly['createIsolateUserProcedure'])) > 0
                    ) {
                        $createUserSentence =
                            "BEGIN "
                            . $onFly['createIsolateUserProcedure'] . "('"
                            . $nameAndPassword . "', '"
                            . $nameAndPassword
                            . "');"
                            . "END;";
                        if ($resultQuery = $connection->prepare($createUserSentence)) {
                            $resultQuery->execute();
                        }
                        $connection = $this->getConnection($nameAndPassword, $nameAndPassword);
                    }
                    $splitSQL = preg_split('~\([^)]*\)(*SKIP)(*F)|;~', $this->sanitize($this->getQuestionOnfly()));
                    $queryString = "";
                    foreach ($splitSQL as $sqlSentence) {
                        if (strlen(trim($sqlSentence)) > 0) {
                            $queryString .=
                                "     stmtOnFly := '" . $sqlSentence . "';\n"
                                . "     EXECUTE IMMEDIATE stmtOnFly;\n";
                        }
                    }
                    $onFlyQuery =
                        "DECLARE stmtOnFly LONG;\n"
                        . " BEGIN\n"
                        . $queryString
                        . " END;\n";
                    if ($resultQuery = $connection->prepare($onFlyQuery)) {
                        $resultQuery->execute();
                    }
                    break;
                case self::DBMS_MYSQL:
                    if (
                        array_key_exists('createIsolateUserProcedure', $onFly)
                        && strlen(trim($onFly['createIsolateUserProcedure'])) > 0
                    ) {
                        $createUserSentence =
                            "CALL " . $onFly['createIsolateUserProcedure'] . "('"
                            . $nameAndPassword . "', '"
                            . $nameAndPassword
                            . "')";
                        if ($resultQuery = $connection->prepare($createUserSentence)) {
                            $resultQuery->execute();
                        }
                        $databaseName = $nameAndPassword;
                        $connection = $this->getConnection($nameAndPassword, $nameAndPassword, $databaseName);
                    }

                    $connection->exec($this->getQuestionOnfly());
                    break;
                case self::DBMS_SQLITE:
                    $connection->exec($this->getQuestionOnfly());
            }
        }
    }

    private function dropOnflySchema(&$connection) {
        $dbms = $this->getQuestionDbms();
        $connectionConfig = $this->getMain()->getTypeProperty('dbConnections')[$dbms];
        if( array_key_exists('onFly', $connectionConfig)
            && is_array($onFly = $connectionConfig['onFly'])
            && array_key_exists('allowed', $onFly)
            && $onFly['allowed']
            && strlen(trim($this->getQuestionOnfly())) > 0
            && array_key_exists('dropIsolateUserProcedure', $onFly)
            && strlen(trim($onFly['dropIsolateUserProcedure'])) > 0
        )
        {
            $nameAndPassword = $onFly['userPrefix'] . $this->getNameAndPasswordSuffix();
            switch ($dbms) {
                case self::DBMS_ORACLE:
                    $dropUserSentence =
                        "BEGIN "
                        . $onFly['dropIsolateUserProcedure'] . " ('"
                        . $nameAndPassword
                        . "');"
                        . "END;";
                    break;
                case self::DBMS_MYSQL:
                    $dropUserSentence =
                        "CALL " . $onFly['dropIsolateUserProcedure'] . "('"
                        . $nameAndPassword
                        . "')";
            }
            if($resultQuery = $connection->prepare($dropUserSentence)) {
                $resultQuery->execute();
            }
        }
    }

    private function getNameAndPasswordSuffix() {
        return $_SESSION['lti']['user_key'];
    }

    private function initTransaction() {
        $connection = $this->getConnection();
        $this->createOnflySchema($connection);
        $connection->beginTransaction();
/*        if ($this->getQuestionType() == 'DDL') {
            $this->loadDDL($connection);
        }*/ // Previously, we did it in SQLite
        return $connection;
    }

    private function endTransaction(&$connection) {
        $connection->rollback();
        // Close statement & connection to drop user
        $connection = null;
        $connection = $this->getConnection();
        $this->dropOnflySchema($connection);
/*        if ($this->getQuestionType() == 'DDL') {
            $dbms = $this->getQuestionDbms();
            $connectionConfig = $this->getMain()->getTypeProperty('dbConnections')[$dbms];
            if(file_exists($connectionConfig['dbFile'])) unlink($connectionConfig['dbFile']);
        }*/ //, Previously we did it in SQLite
    }

    private function loadDDL($connection) {
        $sentences = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'ddl_databases'. DIRECTORY_SEPARATOR . 'world_ddl.sql');
        $connection->exec($sentences);
    }

    private function sanitize($stmt) {
        $sanitizedStmt = str_replace("'", "''", trim($stmt));
        return $sanitizedStmt;
    }

    public function getQueryTable(): string
    {
        $resultQueryString = '';
        if ($this->getQuestionType() == 'SELECT') {
            $connection = $this->initTransaction();
            $resultQueryString = "<div class='table-results'><table>";
            $query = $this->getQuestionSolution();
            if($resultQuery = $connection->prepare($query)) {
                $resultQuery->execute();
                $resultQueryString .= $this->getQueryTableContent($resultQuery);
                $resultQueryString .= "</table></div>";
            }
            $resultQuery = null;
            $this->endTransaction($connection);
        }
        return $resultQueryString;
    }

    private function getQueryTableContent($resultQuery) {
        $resultQueryString = '';
        if (is_array($firstRow = $resultQuery->fetch(\PDO::FETCH_ASSOC))) {
            $resultQueryString .= $this->getHeaderQueryTable($firstRow);
            $resultQueryString .= $this->getBodyQueryTable($firstRow, $resultQuery);
        }
        return $resultQueryString;
    }

    private function getHeaderQueryTable($firstRow) {
        $columnNames = array_keys($firstRow);
        return $this->getQueryTableRow($columnNames, true);
    }

    private function getBodyQueryTable($firstRow, $resultQuery) {
        $tableBody = $this->getQueryTableRow(array_values($firstRow), false);
        while ($row = $resultQuery->fetch(\PDO::FETCH_NUM)) {
            $tableBody .= $this->getQueryTableRow($row, false);
        }
        return $tableBody;
    }

    private function getQueryTableRow($row, $header = false) {
        $tableRow = "<tr>";
        foreach ($row as $value) {
            $tableRow .= ( $header ? "<th>" : "<td>") . $value . ( $header ? "</th>" : "</td>");
        }
        $tableRow .= "</tr>";
        return $tableRow;
    }

    /**
     * @return mixed
     */
    public function getQuestionDbms()
    {
        return $this->question_dbms;
    }

    /**
     * @param mixed $question_dbms
     */
    public function setQuestionDbms($question_dbms)
    {
        $this->question_dbms = $question_dbms;
    }

    /**
     * @return mixed
     */
    public function getQuestionType()
    {
        return $this->question_type;
    }

    /**
     * @param mixed $question_type
     */
    public function setQuestionType($question_type)
    {
        $this->question_type = $question_type;
    }

    /**
     * @return mixed
     */
    public function getQuestionDatabase()
    {
        return $this->question_database;
    }

    /**
     * @param mixed $question_database
     */
    public function setQuestionDatabase($question_database)
    {
        $this->question_database = $question_database;
    }

    /**
     * @return mixed
     */
    public function getQuestionSolution()
    {
        return $this->question_solution;
    }

    /**
     * @param mixed $question_solution
     */
    public function setQuestionSolution($question_solution)
    {
        $this->question_solution = $question_solution;
    }

    /**
     * @return mixed
     */
    public function getQuestionProbe()
    {
        return $this->question_probe;
    }

    /**
     * @param mixed $question_probe
     */
    public function setQuestionProbe($question_probe)
    {
        $this->question_probe = $question_probe;
    }

    /**
     * @return String
     */
    public function getQuestionOnfly()
    {
        return $this->question_onfly;
    }

    /**
     * @param String $question_onfly
     */
    public function setQuestionOnfly($question_onfly)
    {
        $this->question_onfly = $question_onfly;
    }

    public function save() {
        $isNew = $this->isNew();
        parent::save();
        $query = \CT\CT_DAO::getQuery('questionSQL', $isNew ? 'insert' : 'update');
        $arr = array(
            ':question_id' => $this->getQuestionId(),
            ':question_dbms' => $this->getQuestionDbms(),
            ':question_type' => $this->getQuestionType(),
            ':question_database' => $this->getQuestionDatabase(),
            ':question_solution' => $this->getQuestionSolution(),
            ':question_probe' => $this->getQuestionProbe(),
            ':question_onfly' => $this->getQuestionOnfly(),
        );
        $query['PDOX']->queryDie($query['sentence'], $arr);
    }

}

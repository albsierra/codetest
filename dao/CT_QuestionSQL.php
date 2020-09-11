<?php


namespace CT;


class CT_QuestionSQL extends CT_Question
{
    private $question_dbms;
    private $question_type;
    private $question_database;
    private $question_solution;
    private $question_probe;

    const DBMS_MYSQL = 0;
    const DBMS_ORACLE = 1;

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

    public function getConnection() {
        $dbms = $this->getQuestionDbms();
        $connectionConfig = $this->getMain()->getTypeProperty('dbConnections')[$dbms];

        switch ($dbms)
        {
            case self::DBMS_MYSQL: //dsn mysql 'mysql:host=127.0.0.1;dbname=testdb'
                $dsn = "{$connectionConfig['dbDriver']}:host={$connectionConfig['dbHostName']};dbname={$this->getQuestionDatabase()}";
                break;
            case self::DBMS_ORACLE: //dsn oracle 'oci:dbname=//localhost:1521/mydb'
                $dsn = "{$connectionConfig['dbDriver']}:dbname=//{$connectionConfig['dbHostName']}:{$connectionConfig['dbPort']}/{$connectionConfig['dbSID']}";
                break;
        }
        try {

            $connection =
                new \PDO(
                    $dsn,
                    $connectionConfig['dbUser'],
                    $connectionConfig['dbPassword']
                );
        }
        catch(\PDOException $e)
        {
            echo $e->getMessage();
        }
        return $connection;
    }

    public function grade($answer) {
        $outputSolution = $this->getQueryResult();
        $outputAnswer =  $this->getQueryResult($answer->getAnswerTxt());
        CT_DAO::debug(print_r($outputSolution, true));
        CT_DAO::debug(print_r($outputAnswer, true));

        $grade = $outputSolution === $outputAnswer ? 1 : 0;
        $answer->setAnswerSuccess($grade);
    }

    private function getQueryResult($answer = null) {
        $connection = $this->getConnection();
        $connection->beginTransaction();
        $query = (isset($answer) ? $answer : $this->getQuestionSolution());
        $resultQuery = $connection->prepare($query);
        $resultQuery->execute();
        if ($this->getQuestionType() == 'DML') {
            $query = $this->getQuestionProbe();
            $resultQuery = $connection->prepare($query);
            $resultQuery->execute();
        }
        $resultArray = $resultQuery->fetchAll();
        $connection->rollBack();
        return $resultArray;
    }

    public function getQueryTable() {
        $connection = $this->getConnection();
        $resultQueryString = '';
        if ($this->getQuestionType() == 'SELECT') {
            $query = $this->getQuestionSolution();
            $resultQueryString = "<div class='table-results'><table>";
            $resultQuery = $connection->prepare($query);
            $resultQuery->execute();
            $resultQueryString .= $this->getQueryTableContent($resultQuery);
            $resultQueryString .= "</table></div>";
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
        );
        $query['PDOX']->queryDie($query['sentence'], $arr);
    }

}

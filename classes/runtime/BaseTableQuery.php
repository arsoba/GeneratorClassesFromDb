<?

/*
 * Base table query class
 */

abstract class BaseTableQuery {

    /**
     * Type TYPE_PHP_NAME of names
     */
    const TYPE_PHP_NAME = "TYPE_PHP_NAME";

    /**
     * Type TYPE_COL_NAME of names
     */
    const TYPE_COL_NAME = "TYPE_COL_NAME";

    /**
     * Type TYPE_FIELD_NAME of names
     */
    const TYPE_FIELD_NAME = "TYPE_FIELD_NAME";

    /**
     * The table name
     */
    protected $currentObjectTableName = "";

    /**
     * The columns that have been modified in current table.
     *
     * @var        array
     */
    protected $modifiedColumns = array();

    /**
     * Conditions for select and update.
     *
     * @var        array
     */
    protected $sqlConditionForFilter = array();

    /**
     * Sql LIMIT for SELECT.
     *
     * @var        string
     */
    protected $sqlLimit = "";

    /**
     * Column for SELECT
     *
     * @var        array
     */
    protected $sqlSelectColumn = array();

    /**
     * SQL ORDER for SELECT
     *
     * @var array
     */
    protected $sqlOrder = array();

    /**
     * SQL JOIN for SELECT
     *
     * @var array
     */
    protected $sqlJoin = array();

    /**
     * Array of join table
     *
     * @var array
     */
    protected $joinTable = array();

    /**
     * Attribute to determine if this has previously been saved.
     *
     * @var        boolean
     */
    protected $_new = true;

    /**
     * SQL query
     *
     * @var string
     */
    protected $sqlQuery = "";

    /**
     * Pointer to MySQL Resource connector
     *
     * @var        resource
     */
    protected $DBLink;

    /**
     * Get the pointer to MySQL Resource connector
     *
     * @param resource $DBLink the pointer to MySQL Resource connector
     */
    public function __construct(&$DBLink = null)
    {
        $this->DBLink = $DBLink;
    }

    /**
     * Get table name
     *
     * @return string
     */
    public function getCurrentObjectTableName()
    {
        return $this->currentObjectTableName;
    }

    /**
     * Set custom WHERE with AND.
     *
     * @param $field
     * @param $value
     * @param string $comparison
     * @return array
     * @see Query::createSqlCondition()
     */
    public function filterAndCustom($field, $value, $comparison = Query::EQUAL)
    {
        $this->_new = false;
        $this->sqlConditionForFilter[][Query::LOGICAL_AND] = Query::createSqlCondition($field, $value, $comparison);
    }

    /**
     * Set custom WHERE with OR.
     *
     * @param $field
     * @param $value
     * @param string $comparison
     * @param string $comparison
     * @return array
     * @see Query::createSqlCondition()
     */
    public function filterOrCustom($field, $value, $comparison = Query::EQUAL)
    {
        $this->_new = false;
        $this->sqlConditionForFilter[][Query::LOGICAL_OR] = Query::createSqlCondition($field, $value, $comparison);
    }

    /**
     * Add to $joinTable. Using for generate array of objects for return from find() and findOne()
     *
     * @param $joinTable
     * @param $key
     */
    public function addToJoinTable($joinTable, $key)
    {
        $this->joinTable[$key] = $joinTable;
    }

    /**
     * Get from array $joinTable by key. Using for generate array of objects for return from find() and findOne()
     *
     * @param $key
     * @return mixed
     */
    public function getByKeyJoinTable($key)
    {
        if (!isset($this->joinTable[$key])) {
            return null;
        } else {
            return $this->joinTable[$key];
        }
    }

    /**
     * Insert the row in the database.
     *
     * @see isModifiedColumnsNotEmpty()
     * @see doInsertArray()
     * @see modifiedColumnsForQuery()
     */
    protected function doInsert()
    {
        $this->isModifiedColumnsNotEmpty();

        $modifiedColumns = $this->modifiedColumnsForQuery();

        $this->doInsertArray($modifiedColumns);

    }

    /**
     * Insert the row from array in the database.
     *
     * @param array $array
     * @see getCurrentObjectTableName()
     * @see setSqlQuery()
     * @see resetProperties()
     */
    public function doInsertArray($array)
    {
        $sql = sprintf(
            'INSERT INTO ' . $this->getCurrentObjectTableName() . ' (%s) VALUES (%s)',
            implode(', ', array_keys($array)),
            implode(', ', $array)
        );

        $this->setSqlQuery($sql);

        Query::doSqlQuery($sql, $this->DBLink);

        $this->resetProperties();

    }

    /**
     * Update the row in the database.
     *
     * @see isModifiedColumnsNotEmpty()
     * @see modifiedColumnsForQuery()
     * @see doUpdateArray()
     */
    public function doUpdate()
    {
        $this->isModifiedColumnsNotEmpty();

        $modifiedColumns = $this->modifiedColumnsForQuery();

        $this->doUpdateArray($modifiedColumns);
    }

    /**
     * Update the row from array in the database.
     *
     * @param $array
     * @see Query::createSqlWhere()
     * @see getCurrentObjectTableName()
     * @see setSqlQuery()
     * @see Query::doSqlQuery()
     * @see resetProperties()
     */
    public function doUpdateArray ($array)
    {
        array_walk($array, create_function('&$i,$k','$i=" $k=\'$i\'";'));
        $sqlSetValues = implode(",", $array);
        $sqlWhere = Query::createSqlWhere($this->sqlConditionForFilter);

        $sql = "UPDATE " . $this->getCurrentObjectTableName() . " SET " . $sqlSetValues . $sqlWhere;

        $this->setSqlQuery($sql);

        Query::doSqlQuery($sql, $this->DBLink);

        $this->resetProperties();
    }

    /**
     * Record in the database
     *
     * @see isNew()
     * @see doInsert()
     * @see doUpdate()
     */
    public function save()
    {
        $isInsert = $this->isNew();
        if ($isInsert) {
            $this->doInsert();
        } else {
            $this->doUpdate();
        }
    }

    /**
     * Select from the DB
     * Return array of objects if the select column empty, else call doSelect()
     *
     * @see generateSqlColumnStringForQuery()
     * @see Query::createSqlWhere()
     * @see Query::createSqlOrder()
     * @see doSelect()
     * @see getCurrentObjectTableName()
     * @see getPhpName()
     * @see BaseTable::getByKeyJoinTable()
     * @see BaseTable::addToJoinTable()
     * @see doSelectRes()
     */
    public function find()
    {
        if (count($this->sqlSelectColumn) <= 0) {
            $sqlColumns = $this->generateSqlColumnStringForQuery();

            $sqlWhere = Query::createSqlWhere($this->sqlConditionForFilter);
            $sqlLimit = (!empty($this->sqlLimit)) ? $this->sqlLimit : "";
            $sqlOrder = Query::createSqlOrder($this->sqlOrder);
            $sqlJoin = Query::createSqlJoin($this->sqlJoin);

            $result = $this->doSelect($sqlColumns, $sqlJoin, $sqlWhere, $sqlOrder, $sqlLimit);

            $resultArray = array();
            $counter = 0;
            while($row = mysql_fetch_array($result, Query::ASSOC) ) {
                $currentTableName = $this->getCurrentObjectTableName();
                $currentPhpTableName = $this->getPhpName($currentTableName);
                $resultArray[$counter] = new $currentPhpTableName();
                foreach ($row as $key => $value) {
                    $dotPos = strrpos($key, '.');
                    $tableName = substr($key, 0, $dotPos);
                    $phpTableName = $this->getPhpName($tableName);
                    $columnName = substr($key, $dotPos + 1);
                    $phpColumnName = $this->getPhpName($columnName);
                    $methodName = "set" . $phpColumnName;
                    if ($tableName == $currentTableName) {
                        $resultArray[$counter]->$methodName($value);
                    } elseif (isset($this->joinTable[$tableName])) {
                        $joinTableObj = $resultArray[$counter]->getByKeyJoinTable($phpTableName);
                        if ($joinTableObj == null) {
                            $resultArray[$counter]->addToJoinTable(new $phpTableName(), $phpTableName);
                            $joinTableObj = $resultArray[$counter]->getByKeyJoinTable($phpTableName);
                        }
                        $joinTableObj->$methodName($value);
                    }
                }
                $counter++;
            }
            return $resultArray;
        } else {
            $this->doSelectRes();
        }
    }

    /**
     * Select one record from the DB
     * Return object if the select column empty, else call doSelect()
     *
     * @return array
     * @see generateSqlColumnStringForQuery()
     * @see Query::createSqlWhere()
     * @see Query::createSqlOrder()
     * @see Query::createSqlJoin()
     * @see doSelect()
     * @see getCurrentObjectTableName()
     * @see getPhpName()
     * @see BaseTable::getByKeyJoinTable()
     * @see BaseTable::addToJoinTable()
     * @see doSelectRes()
     */
    public function findOne()
    {
        if (count($this->sqlSelectColumn) <= 0) {
            $sqlColumns = $this->generateSqlColumnStringForQuery();

            $sqlWhere = Query::createSqlWhere($this->sqlConditionForFilter);
            $sqlOrder = Query::createSqlOrder($this->sqlOrder);
            $sqlJoin = Query::createSqlJoin($this->sqlJoin);
            $this->setSqlLimit(1);
            $sqlLimit = (!empty($this->sqlLimit)) ? $this->sqlLimit : "";

            $result = $this->doSelect($sqlColumns, $sqlJoin, $sqlWhere, $sqlOrder, $sqlLimit);

            $resultObject = null;
            while($row = mysql_fetch_array($result, Query::ASSOC) ) {
                $currentTableName = $this->getCurrentObjectTableName();
                $currentPhpTableName = $this->getPhpName($currentTableName);
                $resultObject = new $currentPhpTableName();
                foreach ($row as $key => $value) {
                    $dotPos = strrpos($key, '.');
                    $tableName = substr($key, 0, $dotPos);
                    $phpTableName = $this->getPhpName($tableName);
                    $columnName = substr($key, $dotPos + 1);
                    $phpColumnName = $this->getPhpName($columnName);
                    $methodName = "set" . $phpColumnName;
                    if ($tableName == $currentTableName) {
                        $resultObject->$methodName($value);
                    } elseif (isset($this->joinTable[$tableName])) {
                        $joinTableObj = $resultObject->getByKeyJoinTable($phpTableName);
                        if ($joinTableObj == null) {
                            $resultObject->addToJoinTable(new $phpTableName(), $phpTableName);
                            $joinTableObj = $resultObject->getByKeyJoinTable($phpTableName);
                        }
                        $joinTableObj->$methodName($value);
                    }
                }
            }
            return $resultObject;
        } else {
            $this->doSelectRes();
        }
    }

    /**
     * Do select and return mysql result
     *
     * @return mixed
     * @see generateSqlColumnStringForQuery()
     * @see Query::createSqlWhere()
     * @see Query::createSqlOrder()
     * @see Query::createSqlJoin()
     * @see doSelect()
     */
    public function doSelectRes()
    {
        $sqlColumns = $this->generateSqlColumnStringForQuery();
        $sqlWhere = Query::createSqlWhere($this->sqlConditionForFilter);
        $sqlOrder = Query::createSqlOrder($this->sqlOrder);
        $sqlJoin = Query::createSqlJoin($this->sqlJoin);
        $sqlLimit = (!empty($this->sqlLimit)) ? $this->sqlLimit : "";

        return $this->doSelect($sqlColumns, $sqlJoin, $sqlWhere, $sqlOrder, $sqlLimit);
    }

    /**
     * Do select and return array
     *
     * @param $resultType
     * @return array
     * @see generateSqlColumnStringForQuery()
     * @see Query::createSqlWhere()
     * @see Query::createSqlOrder()
     * @see Query::createSqlJoin()
     * @see doSelect()
     */
    public function doSelectArray($resultType = Query::BOTH)
    {
        $sqlColumns = $this->generateSqlColumnStringForQuery();
        $sqlWhere = Query::createSqlWhere($this->sqlConditionForFilter);
        $sqlOrder = Query::createSqlOrder($this->sqlOrder);
        $sqlJoin = Query::createSqlJoin($this->sqlJoin);
        $sqlLimit = (!empty($this->sqlLimit)) ? $this->sqlLimit : "";

        $result = $this->doSelect($sqlColumns, $sqlJoin, $sqlWhere, $sqlOrder, $sqlLimit);

        $resultArray = array();
        while($row = mysql_fetch_array($result, $resultType) ) {
            $resultArray[] = $row;
        }

        return $resultArray;
    }

    /**
     * Do select and return one array
     *
     * @param $resultType
     * @return array
     * @see generateSqlColumnStringForQuery()
     * @see Query::createSqlWhere()
     * @see Query::createSqlOrder()
     * @see Query::createSqlJoin()
     * @see setSqlLimit()
     * @see doSelect()
     */
    public function doSelectOneArray($resultType = Query::BOTH)
    {
        $sqlColumns = $this->generateSqlColumnStringForQuery();
        $sqlWhere = Query::createSqlWhere($this->sqlConditionForFilter);
        $sqlOrder = Query::createSqlOrder($this->sqlOrder);
        $sqlJoin = Query::createSqlJoin($this->sqlJoin);
        $this->setSqlLimit(1);
        $sqlLimit = (!empty($this->sqlLimit)) ? $this->sqlLimit : "";

        $result = $this->doSelect($sqlColumns, $sqlJoin, $sqlWhere, $sqlOrder, $sqlLimit);

        $resultArray = array();
        while($row = mysql_fetch_array($result, $resultType) ) {
            $resultArray = $row;
        }

        return $resultArray;
    }

    /**
     * Generate string with table columns for query
     *
     * @return string
     * @see getFieldNames()
     * @see getPhpName()
     * @see getFieldNames()
     */
    protected function generateSqlColumnStringForQuery()
    {
        $sqlColumns = "";
        if (count($this->sqlSelectColumn) <= 0) {
            $sqlColumnsArray = array();
            foreach ($this->getFieldNames() as $fieldName) {
                $sqlColumnsArray[] = " " . $fieldName . " AS '" . $fieldName . "' ";
            }
            $sqlColumns .= (count($sqlColumnsArray)) ? implode(",", $sqlColumnsArray) : "";

            foreach ($this->sqlJoin as $tableJoin => $joinCondition) {
                $phpJointTableName = $this->getPhpName($tableJoin)."Query";
                $this->joinTable[$tableJoin] = new $phpJointTableName();
                $joinTableColumnArray = array();
                foreach ($this->joinTable[$tableJoin]->getFieldNames() as $fieldName) {
                    $joinTableColumnArray[] = " " . $fieldName . " AS '" . $fieldName . "' ";
                }
                $sqlColumns .= (count($joinTableColumnArray)) ? "," . implode(",", $joinTableColumnArray) : "";
            }
        } else {
            $sqlColumnsArray = array();
            foreach ($this->sqlSelectColumn as $fieldName) {
                $sqlColumnsArray[] = " " . $fieldName . " AS '" . $fieldName . "' ";
            }
            $sqlColumns .= (count($sqlColumnsArray)) ? implode(",", $sqlColumnsArray) : "";
        }
        return $sqlColumns;
    }

    /**
     * Generate sql string and do query
     *
     * @param string $sqlColumns
     * @param string $sqlJoin
     * @param string $sqlWhere
     * @param string $sqlOrder
     * @param string $sqlLimit
     * @return string
     * @see getCurrentObjectTableName()
     * @see setSqlQuery()
     * @see Query::doSqlQuery()
     */
    protected function doSelect($sqlColumns, $sqlJoin, $sqlWhere, $sqlOrder, $sqlLimit)
    {
        $sql = "SELECT " . $sqlColumns . " FROM " . $this->getCurrentObjectTableName() . " " . $sqlJoin . $sqlWhere . $sqlOrder . $sqlLimit;

        $this->setSqlQuery($sql);

        return Query::doSqlQuery($sql, $this->DBLink);
    }

    /**
     * Is modifiedColumns not empty?
     *
     * @throws Exception
     */
    private function isModifiedColumnsNotEmpty()
    {
        try {
            if (count($this->modifiedColumns) <= 0) {
                throw new Exception('ModifiedColumns is empty');
            }
        } catch (Exception $e) {
            print $e->getMessage();
            exit();
        }
    }

    /**
     * Get $this->_new
     *
     * @return boolean $this->_new
     */
    private function isNew()
    {
        return $this->_new;
    }

    /**
     * Set the value of SQL LIMIT for SELECT
     *
     * @param int $v
     * @see Query::createLimitSql()
     */
    public function setSqlLimit($v)
    {
        $this->sqlLimit = Query::createSqlLimit($v);
    }

    /**
     * Add a column for SELECT
     *
     * @param string $column
     */
    public function addSelectColumn($column)
    {
        $this->sqlSelectColumn[] = $column;
    }

    /**
     * Add INNER JOIN
     *
     * @param string $left
     * @param string $right
     * @param string $comparisonType
     * @see Query::createSqlOneJoin()
     */
    public function join($left, $right, $comparisonType = Query::EQUAL)
    {
        $dotPos = strrpos($left, '.');
        $leftTableAlias = substr($left, 0, $dotPos);

        $this->sqlJoin[$leftTableAlias] = Query::createSqlOneJoin(
            Query::INNER_JOIN,
            $leftTableAlias,
            $left,
            $right,
            $comparisonType
        );
    }

    /**
     * Add LEFT JOIN
     *
     * @param string $left
     * @param string $right
     * @param string $comparisonType
     * @see Query::createSqlOneJoin()
     */
    public function leftJoin($left, $right, $comparisonType = Query::EQUAL)
    {
        $dotPos = strrpos($left, '.');
        $leftTableAlias = substr($left, 0, $dotPos);

        $this->sqlJoin[$leftTableAlias] = Query::createSqlOneJoin(
            Query::LEFT_JOIN,
            $leftTableAlias,
            $left,
            $right,
            $comparisonType
        );
    }

    /**
     * Add RIGHT JOIN
     *
     * @param string $left
     * @param string $right
     * @param string $comparisonType
     * @see Query::createSqlOneJoin()
     */
    public function rightJoin($left, $right, $comparisonType = Query::EQUAL)
    {
        $dotPos = strrpos($left, '.');
        $leftTableAlias = substr($left, 0, $dotPos);

        $this->sqlJoin[$leftTableAlias] = Query::createSqlOneJoin(
            Query::RIGHT_JOIN,
            $leftTableAlias,
            $left,
            $right,
            $comparisonType
        );
    }

    /**
     * Set $sqlQuery
     *
     * @param string $sqlQuery
     */
    private function setSqlQuery($sqlQuery)
    {
        $this->sqlQuery = $sqlQuery;
    }

    /**
     * Get $sqlQuery
     *
     * @return string
     */
    public function getSqlQuery()
    {
        return $this->sqlQuery;
    }

    /*
     * Reset the properties required for queries
     */
    private function resetProperties()
    {
        $this->modifiedColumns = array();
        $this->sqlConditionForFilter = array();
        $this->sqlLimit = "";
        $this->sqlSelectColumn = array();
        $this->sqlJoin = array();
        $this->sqlOrder = array();
    }

    /**
     * Get the first letter upper case
     *
     * @param $name
     * @return string
     */
    private function getPhpName($name)
    {
        $nameArray = explode("_", $name);
        $phpName = "";
        foreach ($nameArray as $latter) {
            $phpName .= ucfirst($latter);
        }
        return $phpName;
    }

    /**
     * Preparing an array of query
     */
     protected function modifiedColumnsForQuery ()
     {

     }

    /**
     * Returns an array of field names.
     */
    public function getFieldNames()
    {

    }

}
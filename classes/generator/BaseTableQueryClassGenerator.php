<?

/**
 * Class generate base class-table
 */

class BaseTableQueryClassGenerator {

    /**
     * Table object
     *
     * @var Table
     */
    private $table;

    /**
     * Class name
     *
     * @var
     */
    private $className;

    /**
     * Source script
     *
     * @var
     */
    private $script = "";

    /**
     * @param $table
     */
    public function __construct($table)
    {
        $this->setTable($table);
        $this->setClassName("Base" . $this->getTable()->getFirstLetterUpperCaseName() . "Query");
        $this->addPhpTag();
        $this->addOpenClass();
        $this->addTableName();
        $this->addRowNameConstants();
        $this->addRowProperties();
        $this->addFieldNames();
        $this->addGetFieldNames();
        $this->addSettersRow();
        $this->addGettersRow();
        $this->addFiltersRow();
        $this->addOrdersRow();
        $this->addModifiedColumnsForQuery();
        $this->addCloseClass();
        $this->createScriptFile();
    }

    /**
     * Write source into file
     */
    private function createScriptFile()
    {
        file_put_contents("db/" . $this->getTable()->getFirstLetterUpperCaseName() . "/model/" . $this->getClassName() . ".php", $this->getScript());
    }

    /**
     * @return string
     */
    private function getScript()
    {
        return $this->script;
    }

    /**
     * @param \Table $table
     */
    private function setTable($table)
    {
        $this->table = $table;
    }

    /**
     * @return \Table
     */
    private function getTable()
    {
        return $this->table;
    }

    /**
     * @param  $className
     */
    private function setClassName($className)
    {
        $this->className = $className;
    }

    /**
     * @return
     */
    private function getClassName()
    {
        return $this->className;
    }

    private function addPhpTag()
    {
        $this->script .= "<?

";
    }

    private function addOpenClass()
    {
        $this->script .= "
/**
 * Base class for '" . $this->getTable()->getOriginalName() . "' table.
 */";

        $this->script .= "
abstract class " . $this->getClassName() . " extends BaseTableQuery
{

";
    }

    private function addCloseClass()
    {
        $this->script .= "
}
";
    }

    private function addTableName()
    {
        $this->script .= "
    /**
     * The table name
     */
    protected \$currentObjectTableName = '" . $this->getTable()->getOriginalName() . "';

";
    }

    private function addRowNameConstants()
    {
        foreach ($this->getTable()->getRows() as $row) {
            $this->script .= "
    /**
     * The column name for the " . $row->getOriginalName() . " field
     */
    const ".$row->getUpperCaseName() ." = '" . $this->getTable()->getOriginalName() . ".".$row->getOriginalName()."';

";
        }
    }

    private function addRowProperties()
    {
        foreach ($this->getTable()->getRows() as $row) {
            $this->script .= "
    /**
     * The value for the " . $row->getOriginalName() . " field
     *
     * @var
     */
    protected \$".$row->getOriginalName() .";

";
        }
    }

    private function addSettersRow()
    {
        foreach ($this->getTable()->getRows() as $row) {
            $this->script .= "

    /**
     * Set the value of " . $row->getOriginalName() . " column.
     *
     * @param \$v new value
     * @return " . $this->getClassName() . " The current object
     */
    public function set" . $row->getFirstLetterUpperCaseName() . "(\$v)
    {
        if (\$this->" . $row->getOriginalName() . " !== \$v) {
            \$this->" . $row->getOriginalName() . " = \$v;
            \$this->modifiedColumns[] = " . $this->getClassName() . "::" . $row->getUpperCaseName() . ";
        }

        return \$this;
    }
";
        }
    }

    private function addGettersRow()
    {
        foreach ($this->getTable()->getRows() as $row) {
            $this->script .= "
    /**
     * Get " . $row->getOriginalName() . "
     *
     * @return mixed
     */
    public function get" . $row->getFirstLetterUpperCaseName() . "()
    {
        return \$this->" . $row->getOriginalName() . ";
    }

";
        }
    }

    private function addFiltersRow()
    {
        foreach ($this->getTable()->getRows() as $row) {
            $this->script .= "
    /**
     * Set the value of " . $row->getOriginalName() . " for filter with AND.
     *
     * @param \$" . $row->getOriginalName() . " value for filter
     * @param string \$comparison Comparison type
     * @see     Query::createSqlCondition()
     */
    public function filterAndBy" . $row->getFirstLetterUpperCaseName() . "(\$" . $row->getOriginalName() . ", \$comparison = Query::EQUAL)
    {
        \$this->_new = false;
        \$this->sqlConditionForFilter[][Query::LOGICAL_AND] = Query::createSqlCondition(" . $this->getClassName() . "::" . $row->getUpperCaseName() . ", \$" . $row->getOriginalName() . ", \$comparison);
    }

    /**
     * Set the value of " . $row->getOriginalName() . " for filter with OR.
     *
     * @param int \$" . $row->getOriginalName() . " value for filter
     * @param string \$comparison Comparison type
     * @see     Query::createSqlCondition()
     */
    public function filterOrBy" . $row->getFirstLetterUpperCaseName() . "(\$" . $row->getOriginalName() . ", \$comparison = Query::EQUAL)
    {
        \$this->_new = false;
        \$this->sqlConditionForFilter[][Query::LOGICAL_OR] = Query::createSqlCondition(" . $this->getClassName() . "::" . $row->getUpperCaseName() . ", \$" . $row->getOriginalName() . ", \$comparison);
    }

";
        }
    }

    private function addOrdersRow()
    {
        foreach ($this->getTable()->getRows() as $row) {
            $this->script .= "
    /**
     * Write ORDER BY " . $row->getOriginalName() . " ASC to sqlOrder
     */
    public function orderBy" . $row->getFirstLetterUpperCaseName() . "Asc()
    {
        \$this->sqlOrder[] = Query::createSqlOneOrder(" . $this->getClassName() . "::" . $row->getUpperCaseName() . ", Query::ASC);
    }

    /**
     * Write ORDER BY " . $row->getOriginalName() . " DESC to sqlOrder
     */
    public function orderBy" . $row->getFirstLetterUpperCaseName() . "Desc()
    {
        \$this->sqlOrder[] = Query::createSqlOneOrder(" . $this->getClassName() . "::" . $row->getUpperCaseName() . ", Query::DESC);
    }

";
        }
    }

    private function addModifiedColumnsForQuery()
    {
        $this->script .= "
    /**
     * Preparing an array of query
     *
     * @return array \$modifiedColumnsForQuery
     */
    protected function modifiedColumnsForQuery ()
    {
        \$modifiedColumnsForQuery = array();
";
        foreach ($this->getTable()->getRows() as $row) {
            $this->script .= "

        if (\$this->isColumnModified(" . $this->getClassName() . "::" . $row->getUpperCaseName() . ")) {
            \$modifiedColumnsForQuery[" . $this->getClassName() . "::" . $row->getUpperCaseName() . "]  = \$this->" . $row->getOriginalName() . ";
        }

";
        }
        $this->script .= "

        return \$modifiedColumnsForQuery;
    }
";
    }

    private function addFieldNames()
    {
        $phpNames = "";
        $colNames = "";
        $fieldNames = "";

        foreach ($this->getTable()->getRows() as $row) {
            $phpNames .= "'" . $row->getFirstLetterUpperCaseName() . "', ";
            $colNames .= $this->getClassName() . "::" . $row->getUpperCaseName() . ", ";
            $fieldNames .= "'" . $row->getOriginalName() . "', ";
        }

        $this->script .= "
    /**
     * Holds an array of field names
     */
    protected \$fieldNames = array (
        BaseTableQuery::TYPE_PHP_NAME => array (" . $phpNames . "),
        BaseTableQuery::TYPE_COL_NAME => array (" . $colNames . "),
        BaseTableQuery::TYPE_FIELD_NAME => array (" . $fieldNames . "),
    );
";
    }

    private function addGetFieldNames()
    {
        $this->script .= "
    /**
     * Returns an array of field names.
     *
     * @param string \$type The type of fieldnames to return:
     * @return array list of field names
     */
    public function getFieldNames(\$type = BaseTableQuery::TYPE_COL_NAME)
    {
        return \$this->fieldNames[\$type];
    }

";
    }

}
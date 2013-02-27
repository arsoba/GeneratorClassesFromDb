<?

/**
 * Class-generator
 */

class Generator {

    /**
     * Array of Table objects
     *
     * @var array
     */
    private $tables = array();

    /**
     * Pointer to MySQL Resource connector
     *
     * @var        resource
     */
    private $DBLink;

    /**
     * Get the pointer to MySQL Resource connector
     *
     * @param resource $DBLink the pointer to MySQL Resource connector
     */
    public function __construct(&$DBLink)
    {
        $this->DBLink = $DBLink;
    }

    /**
     * ROCK!  \m/.
     */
    public function letsTheRock()
    {
        mkdir ("db");
        $parser = new DataBaseParser($this->DBLink);
        $parser->getFieldsNamesFromTables();
        $this->preparingFieldsToGenerator($parser->getFieldsNames());
        foreach ($this->tables as $table) {
            mkdir("db/" . $table->getFirstLetterUpperCaseName());
            mkdir("db/" . $table->getFirstLetterUpperCaseName() . "/model");
            $baseTableQueryClass = new BaseTableQueryClassGenerator($table);
            $tableQueryClass = new TableClassQueryGenerator($table);
            $baseTableClass = new BaseTableClassGenerator($table);
            $tableClass = new TableClassGenerator($table);
        }
        $initTableClasses = new InitTableClassesGenerator($this->tables);
    }

    private function preparingFieldsToGenerator($rowsNames)
    {
        foreach ($rowsNames as $tableName => $rows) {
            $table = new Table();
            $table->setOriginalName($tableName);
            $table->setFirstLetterUpperCaseName($this->createFirstLatterUpperCaseName($tableName));
            foreach ($rows as $key => $rowsName) {
                $row = new Row();
                $row->setOriginalName($rowsName);
                $row->setFirstLetterUpperCaseName($this->createFirstLatterUpperCaseName($rowsName));
                $row->setUpperCaseName($this->createUpperCaseName($rowsName));
                $table->addRow($row);
            }
            $this->tables[] = $table;
        }
    }

    private function createFirstLatterUpperCaseName($name)
    {
        $nameArray = explode("_", $name);
        $firstLatterUpperCaseName = "";
        foreach ($nameArray as $latter) {
            $firstLatterUpperCaseName .= ucfirst($latter);
        }
        return $firstLatterUpperCaseName;
    }

    private function createUpperCaseName($name)
    {
        return strtoupper($name);
    }
}
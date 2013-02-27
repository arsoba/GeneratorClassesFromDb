<?

/**
 * Class generate base class-table
 */

class BaseTableClassGenerator {

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
        $this->setClassName("Base" . $this->getTable()->getFirstLetterUpperCaseName());
        $this->addPhpTag();
        $this->addOpenClass();
        $this->addRowNameConstants();
        $this->addRowProperties();
        $this->addSettersRow();
        $this->addGettersRow();
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
 * Base Class for '" . $this->getTable()->getOriginalName() . "' table.
 */";

        $this->script .= "
abstract class " . $this->getClassName() . " extends BaseTable
{

";
    }

    private function addCloseClass()
    {
        $this->script .= "
}
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
        \$this->" . $row->getOriginalName() . " = \$v;
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

}
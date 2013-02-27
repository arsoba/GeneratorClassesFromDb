<?

/**
 * Class generate class-table
 */

class TableClassQueryGenerator {

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
        $this->setClassName($this->getTable()->getFirstLetterUpperCaseName() . "Query");
        $this->addPhpTag();
        $this->addOpenClass();
        $this->createScriptFile();
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

    /**
     * Write source into file
     */
    private function createScriptFile()
    {
        file_put_contents("db/" . $this->getTable()->getFirstLetterUpperCaseName() . "/" . $this->getClassName() . ".php", $this->getScript());
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
 * Class for '" . $this->getTable()->getOriginalName() . "' table.
 *
 * You can add here all the methods you need
 */";

        $this->script .= "
class " . $this->getClassName() . " extends Base" .  $this->getClassName() . "
{

}
";
    }

}
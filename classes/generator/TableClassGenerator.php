<?

/**
 * Class generate base class-table
 */

class TableClassGenerator {

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
        $this->setClassName($this->getTable()->getFirstLetterUpperCaseName());
        $this->addPhpTag();
        $this->addOpenClass();
        $this->createScriptFile();
    }

    /**
     * Write source into file
     */
    private function createScriptFile()
    {
        file_put_contents("db/" . $this->getTable()->getFirstLetterUpperCaseName() . "/" . $this->getClassName() . ".php", $this->getScript());
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

require_once(\"model/Base" .  $this->getClassName() . "Query.php\");
require_once(\"model/Base" .  $this->getClassName() . ".php\");
require_once(\"" .  $this->getClassName() . "Query.php\");

/**
 * Class for '" . $this->getTable()->getOriginalName() . "' table.
 */";

        $this->script .= "
class " . $this->getClassName() . " extends Base" . $this->getClassName() . "
{

}
";
    }

}
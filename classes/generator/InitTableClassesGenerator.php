<?

/**
 * Class generate init file
 */

class InitTableClassesGenerator {

    /**
     * Table object
     *
     * @var array
     */
    private $tables;

    /**
     * Source script
     *
     * @var
     */
    private $script = "";

    /**
     * @param $tables
     */
    public function __construct($tables)
    {
        $this->setTables($tables);
        $this->addPhpTag();
        $this->addRequireList();
        $this->createScriptFile();
    }

    /**
     * @param array $tables
     */
    public function setTables($tables)
    {
        $this->tables = $tables;
    }

    /**
     * @return string
     */
    private function getScript()
    {
        return $this->script;
    }

    /**
     * Write source into file
     */
    private function createScriptFile()
    {
        file_put_contents("db/InitTableClasses.php", $this->getScript());
    }

    private function addPhpTag()
    {
        $this->script .= "<?

";
    }


    private function addRequireList()
    {
        foreach ($this->tables as $table) {
            $this->script .= "
require_once(\"" . $table->getFirstLetterUpperCaseName() . "/" . $table->getFirstLetterUpperCaseName() . ".php\");
";
        }
    }

}
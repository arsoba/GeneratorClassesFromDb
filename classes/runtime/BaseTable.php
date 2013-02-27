<?

/*
 * Base table class
 */

abstract class BaseTable {

    /**
     * Array of join table
     *
     * @var array
     */
    protected $joinTable = array();

    /**
     * @param $joinTable
     * @param $key
     */
    public function addToJoinTable($joinTable, $key)
    {
        $this->joinTable[$key] = $joinTable;
    }

    /**
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
     * Method for handling dynamic elements of the class
     *
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws Exception
     */
    public function __call($name, $arguments)
    {
        /**
         * handling getJoin
         */
        $getJoinString = "getJoin";
        $posGetJoin = strpos($name, $getJoinString);
        if ($posGetJoin === 0) {
            $tableName = substr($name, $posGetJoin + strlen($getJoinString));
            try {
                if (!isset($this->joinTable[$tableName])) {
                    throw new Exception('I dont know about this table');
                }
            } catch (Exception $e) {
                print $e->getMessage();
                exit();
            }
            return $this->joinTable[$tableName];
        }
    }

}
<?php

/**
 * Class for parse DataBase
 */

class DataBaseParser {

    /**
     * Array of tables name
     *
     * @var array $tablesNames
     */
    private $tablesNames = array();

    /**
     * Array of fieldsNames
     *
     * @var array
     */
    private $fieldsNames = array();

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
     * Get tables names from database
     */
    private function getTablesNamesFromDataBase ()
    {
        $sql = "SHOW TABLES";

        $result = $this->doSqlQuery($sql, $this->DBLink);

        $resultArray = array();
        while($row = mysql_fetch_array($result, MYSQL_NUM) ) {
            $resultArray[] = $row[0];
        }

        $this->tablesNames = $resultArray;
    }

    /**
     * Get fields names from tables
     */
    public function getFieldsNamesFromTables ()
    {
        $this->getTablesNamesFromDataBase();
        $resultArray = array();
        foreach ($this->tablesNames as $key => $tableName) {
            $sql = "SHOW COLUMNS FROM " . $tableName;

            $result = $this->doSqlQuery($sql, $this->DBLink);

            while($row = mysql_fetch_array($result, MYSQL_NUM) ) {
                $resultArray[$tableName][] = $row[0];
            }
        }
        $this->fieldsNames = $resultArray;
    }

    /**
     * Get tablesNames
     *
     * @return array
     */
    public function getTablesNames()
    {
        return $this->tablesNames;
    }

    /**
     * Get fieldsNames
     *
     * @return array
     */
    public function getFieldsNames()
    {
        return $this->fieldsNames;
    }

    /**
     * Do sql query
     *
     * @param $sql
     * @param $DBLink
     * @return resource
     * @throws Exception
     */
    private function doSqlQuery($sql, $DBLink)
    {
        try {
            if (!($result = mysql_query($sql, $DBLink))) {
                throw new Exception('Can not execute a query ' .  $sql);
            }
        } catch (Exception $e) {
            die($e->getMessage());
        }

        return $result;
    }

}
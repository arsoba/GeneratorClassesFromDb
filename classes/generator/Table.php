<?php

/**
 * Class table from DB
 */

class Table {

    /**
     * Original name
     *
     * @var string
     */
    private $originalName = "";

    /**
     * Name with first letter in upper case
     *
     * @var string
     */
    private $firstLetterUpperCaseName = "";

    /**
     * Array of Row objects
     *
     * @var array
     */
    private $rows = array();

    /**
     * @param string $firstLetterUpperCaseName
     */
    public function setFirstLetterUpperCaseName($firstLetterUpperCaseName)
    {
        $this->firstLetterUpperCaseName = $firstLetterUpperCaseName;
    }

    /**
     * @return string
     */
    public function getFirstLetterUpperCaseName()
    {
        return $this->firstLetterUpperCaseName;
    }

    /**
     * @param string $originalName
     */
    public function setOriginalName($originalName)
    {
        $this->originalName = $originalName;
    }

    /**
     * @return string
     */
    public function getOriginalName()
    {
        return $this->originalName;
    }

    /**
     * Add row to table
     *
     * @param $row
     */
    public function addRow($row)
    {
        $this->rows[] = $row;
    }

    /**
     * @return array
     */
    public function getRows()
    {
        return $this->rows;
    }



}
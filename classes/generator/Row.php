<?php

/**
 * Class row from table
 */

class Row {

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
     * Upper case name
     *
     * @var string
     */
    private $upperCaseName = "";

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
     * @param string $upperCaseName
     */
    public function setUpperCaseName($upperCaseName)
    {
        $this->upperCaseName = $upperCaseName;
    }

    /**
     * @return string
     */
    public function getUpperCaseName()
    {
        return $this->upperCaseName;
    }



}
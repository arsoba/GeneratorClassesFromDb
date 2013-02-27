<?

/**
 * Class for work with query
 */
class Query {

    /**
     * Comparison type.
     */
    const EQUAL = "=";

    /**
     * Comparison type.
     */
    const NOT_EQUAL = "<>";

    /**
     * Comparison type.
     */
    const ALT_NOT_EQUAL = "!=";

    /**
     * Comparison type.
     */
    const GREATER_THAN = ">";

    /**
     * Comparison type.
     */
    const LESS_THAN = "<";

    /**
     * Comparison type.
     */
    const GREATER_EQUAL = ">=";

    /**
     * Comparison type.
     */
    const LESS_EQUAL = "<=";

    /**
     * Comparison type.
     */
    const LIKE = " LIKE ";

    /**
     * Comparison type.
     */
    const NOT_LIKE = " NOT LIKE ";

    /**
     * Comparison type.
     */
    const IN = " IN ";

    /**
     * Comparison type.
     */
    const NOT_IN = " NOT IN ";

    /**
     * logical OR operator.
     */
    const LOGICAL_OR = " OR ";

    /**
     * logical AND operator.
     */
    const LOGICAL_AND = " AND ";

    /**
     * "Order by" qualifier - ascending
     */
    const ASC = "ASC";

    /**
     * "Order by" qualifier - descending
     */
    const DESC = "DESC";

    /**
     * LIMIT.
     */
    const LIMIT = " LIMIT ";

    /**
     * "LEFT JOIN" SQL statement
     */
    const LEFT_JOIN = "LEFT JOIN";

    /**
     * "RIGHT JOIN" SQL statement
     */
    const RIGHT_JOIN = "RIGHT JOIN";

    /**
     * "INNER JOIN" SQL statement
     */
    const INNER_JOIN = "INNER JOIN";

    /**
     * Result type MYSQL_ASSOC
     */
    const ASSOC = MYSQL_ASSOC;

    /**
     * Result type MYSQL_BOTH
     */
    const BOTH = MYSQL_BOTH;

    /**
     * Result type MYSQL_NUM
     */
    const NUM = MYSQL_NUM;

    /**
     * The method for creating the conditions for a query
     * @param string $field
     * @param mixed $value
     * @param string $comparison
     * @return string
     * @see Query::createInSqlCondition()
     * @see Query::createBasicSqlCondition()
     */
    public static function createSqlCondition ($field, $value, $comparison)
    {
        switch ($comparison) {
            case Query::IN:
            case Query::NOT_IN:
                return self::createInSqlCondition($field, $value, $comparison);
                break;
            default:
                return self::createBasicSqlCondition($field, $value, $comparison);
        }
    }

    /**
     * The method for creating the IN or NOT IN conditions for a query
     *
     * @param string $field
     * @param array $values
     * @param  string$comparison
     * @return string
     */
    public static function createInSqlCondition ($field, $values, $comparison)
    {
        if (count($values)) {
            return $field . $comparison . "(" . implode(",", $values) . ") ";
        } else {
            return ($comparison === Query::IN) ? "1<>1" : "1=1";
        }
    }

    /**
     * The method for creating the basic conditions for a query
     *
     * @param string $field
     * @param string $value
     * @param string $comparison
     * @return string
     */
    public static function createBasicSqlCondition ($field, $value, $comparison)
    {
        return $field . $comparison . "'" . $value . "'";
    }

    /**
     * The method for creating one order
     *
     * @param string $field
     * @param string $qualifier
     * @return string
     */
    public static function createSqlOneOrder ($field, $qualifier)
    {
        return $field . " " . $qualifier;
    }

    /**
     * The method for creating ORDER for query
     *
     * @param string $sqlOrder
     * @return string
     */
    public static function createSqlOrder ($sqlOrder)
    {
        return  (count($sqlOrder) >= 1) ? " ORDER BY " . implode(",", $sqlOrder) : "";
    }

    /**
     * The method for creating LIMIT SQL
     *
     * @param int $v
     * @return string
     */
    public static function createSqlLimit($v)
    {
        return Query::LIMIT . $v;
    }

    /**
     * The method for creating one join
     *
     * @param string $joinStatement
     * @param string $joinTable
     * @param string $left
     * @param string $right
     * @param string $comparisonType
     * @return string
     */
    public static function createSqlOneJoin($joinStatement, $joinTable, $left, $right, $comparisonType)
    {
        return " " . $joinStatement . " " . $joinTable . " ON " . $left . " " . $comparisonType . " " . $right;
    }

    /**
     * The method for creating JOIN SQL
     *
     * @param string $sqlJoin
     * @return string
     */
    public static function createSqlJoin($sqlJoin)
    {
        return  (count($sqlJoin) >= 1) ? implode(" ", $sqlJoin) : "";
    }

    /**
     * Create SQL for WHERE
     *
     * @param array $sqlConditions
     * @return string $sqlWhere
     */
    public static function createSqlWhere ($sqlConditions)
    {
        if(count($sqlConditions) == 1) {
            $sqlWhere = " WHERE " . implode(",", array_shift($sqlConditions));
        } elseif (count($sqlConditions) > 1) {
            $first = true;
            $sqlWhere = " WHERE ";
            foreach ($sqlConditions as $sqlCondition) {
                foreach ($sqlCondition as $logicalOperator => $condition) {
                    if ($first) {
                        $sqlWhere .= " " . $condition . " ";
                    } else {
                        $sqlWhere .= " " . $logicalOperator . " " . $condition . " ";
                    }
                    $first = false;
                }
            }
        } else {
            $sqlWhere = "";
        }
        return $sqlWhere;
    }

    /**
     * Do SQL query to database
     *
     * @param string $sqlQuery
     * @param $DBLink
     * @return mixed
     * @throws Exception
     */
    public static function doSqlQuery ($sqlQuery, &$DBLink)
    {
        try {
            if (!($result = mysql_query($sqlQuery, $DBLink))) {
                throw new Exception('Can not execute a query ' .  $sqlQuery);
            }
        } catch (Exception $e) {
            report_mysql_error($e->getMessage());
            exit();
        }

        return $result;
    }

}
<?php
declare(strict_types=1);

namespace App\Model;


class SqlGenerator
{


    /**
     * @param Dbms $dbms
     * @param string $tableName
     * @param array<string, string> $params
     * @param array<string> $selectedColumns
     * @param array<int, array<string, string>> $joins
     * @param array<int, array<string, string>> $where
     * @param string $groupBy
     * @param array<int, array<string, string>> $having
     * @param array<string, string> $orderBy
     * @param int $limit
     * @param int $offset
     * @return string
     */
    public static function generateSelect(
        Dbms $dbms,
        string $tableName,
        array &$params,
        array $selectedColumns,
        array $joins=[],
        array $where=[],
        null|string $groupBy=null,
        array $having=[],
        array $orderBy=[],
        int $limit=1000,
        int $offset=0
    ): string {
        $c = self::generateColumns($dbms, $selectedColumns);

        $j = self::generateJoins($dbms, $joins);

        $w = self::generateConditions($dbms, 'WHERE', $where, $params);

        $g = self::generateGroupBy($dbms, $groupBy);

        $h = self::generateConditions($dbms, 'HAVING', $having, $params);

        $o = self::generateOrderBy($dbms, $orderBy);

        $lof = self::generateLimitOffset($dbms,$limit, $offset);

        $tableName = ($dbms != Dbms::Pgsql) ? $tableName : '"'.$tableName.'"';

        return "SELECT $c FROM $tableName $j$w$g$h$o$lof";

    }//end generateSelect()


    /**
     * @param Dbms $dbms
     * @param string $tableName
     * @param array<string, string> $params
     * @return string
     */
    public static function generateInsert(
        Dbms $dbms,
        string $tableName,
        array $params
        ): string
    {
        $cols    = array_keys($params);
        $columns = self::generateColumns($dbms, $cols);
        $values  = self::generateValues($cols);

        $tableName = ($dbms != Dbms::Pgsql) ? $tableName : '"'.$tableName.'"';

        return "INSERT INTO $tableName ($columns) VALUES (:$values);";

    }//end generateInsert()


    /**
     * @param Dbms $dbms
     * @param string $tableName
     * @param array<string, string> $params
     * @param array<int, array<string, mixed>> $where
     * @return string
     */
    public static function generateUpdate(
        Dbms $dbms,
        string $tableName,
        array &$params,
        array $where
        ): string
    {
        $s = self::generateSet($dbms, array_keys($params));

        $w = self::generateConditions($dbms, 'WHERE', $where, $params);

        $tableName = ($dbms != Dbms::Pgsql) ? $tableName : '"'.$tableName.'"';

        return "UPDATE $tableName SET $s$w";

    }//end generateUpdate()


    /**
     * @param Dbms $dbms
     * @param string $tableName
     * @param array<string, string> $params
     * @param array<int, array<string, mixed>> $where
     * @return string
     */
    public static function generateDelete(
        Dbms $dbms,
        string $tableName,
        array &$params,
        array $where=[],
        ): string
    {
        $w = self::generateConditions($dbms, 'WHERE', $where, $params);

        $tableName = ($dbms != Dbms::Pgsql) ? $tableName : '"'.$tableName.'"';

        return "DELETE FROM $tableName $w";

    }//end generateDelete()


    /**
     * @param Dbms $dbms
     * @param array<int, string> $cols
     * @return string
     */
    protected static function generateColumns(Dbms $dbms, array $cols): string
    {
        return ($dbms != Dbms::Pgsql) ? implode(', ', $cols) : '"'.implode('", "', $cols).'"';

    }//end generateColumns()


    /**
     * @param Dbms $dbms
     * @param null|array<int, array<string, string>> $joins
     * @return string
     */
    protected static function generateJoins(Dbms $dbms, null|array $joins): string
    {
        if (empty($joins)) {
            return '';
        }

        $sql  = '';
        $isPg = ($dbms == Dbms::Pgsql);

        foreach ($joins as $j) {
            $format = $isPg ? ' %s JOIN %s ON "%s" = "%s"' : ' %s JOIN %s ON %s = "%s"';
            $sql   .= sprintf($format, $j['type'], $j['tableName'], $j['onCol1'], $j['onCol2']);
        }

        return $sql;

    }//end generateJoins()


    /**
     * @param Dbms $dbms
     * @param string $conditionType
     * @param array<int, array<string, string>> $conditions
     * @param array<string, string> $params
     * @return string
     */
    protected static function generateConditions(
        Dbms $dbms,
        string $conditionType,
        array $conditions,
        array &$params): string
    {
        if (empty($conditions)) {
            return '';
        }

        $sql = '';

        foreach ($conditions as $i => $c) {
            $operator = $c['operator'];
            $col      = ($dbms != Dbms::Pgsql) ? $c['column'] : '"'.$c['column'].'"';
            $conditionOperator = empty($sql) ? $conditionType : $c['condition_operator'];

            if ($operator != 'IN') {
                $sql .= sprintf(' %s %s %s :%s%s', $conditionOperator, $col, $operator, $c['column'], $i);
                $params[$c['column'].$i] = $c['value'];
            } else {
                $sql .= sprintf(' %s %s IN %s', $conditionOperator, $col, $c['value']);
            }
        }

        return $sql;

    }//end generateConditions()


    /**
     * @param Dbms $dbms
     * @param null|string $groupBy
     * @return string
     */
    protected static function generateGroupBy(Dbms $dbms, null|string $groupBy): string
    {
        if (empty($groupBy)) {
            return '';
        }

        return ($dbms != Dbms::Pgsql) ? " GROUP BY $groupBy" : " GROUP BY \"$groupBy\"";

    }//end generateGroupBy()


    protected static function generateLimitOffset(Dbms $dbms, int $limit=100, int $offset=0): string
    {
        return ($dbms != Dbms::Mssql) ? " LIMIT $limit OFFSET $offset" : "OFFSET $offset ROWS FETCH FIRST $limit ROWS ONLY";

    }//end generateLimitOffset()


    /**
     * @param int $limit
     * @return string
     */
    protected static function generateLimit(int $limit=100): string
    {
        return " LIMIT $limit";

    }//end generateLimit()


    /**
     * @param Dbms $dbms
     * @param array<string, string> $orderBy
     * @return string
     */
    protected static function generateOrderBy(Dbms $dbms, array $orderBy): string
    {
        $sql = ' ORDER BY ';

        foreach ($orderBy as $col => $direction) {
            $col = ($dbms != Dbms::Pgsql) ? $col : '"'.$col.'"';
            $sql .= "$col $direction ";
        }

        return $sql;

    }//end generateOrderBy()


    /**
     * @param array<int, string> $cols
     * @return string
     */
    protected static function generateValues(array $cols): string
    {
        return implode(', :', $cols);

    }//end generateValues()


    /**
     * @param Dbms $dbms
     * @param array<int, string> $cols
     * @return string
     */
    protected static function generateSet(Dbms $dbms, array $cols): string
    {
        $sql  = '';
        $isPg = ($dbms == Dbms::Pgsql);

        foreach ($cols as $c) {
            $sql .= $isPg ? " \"$c\" = :$c," : " $c = :$c,";
        }

        return substr($sql, 0, -1);

    }//end generateSet()


}//end class

<?php
declare(strict_types=1);

namespace App\Model;


class SqlGenerator
{


    /**
     * @param Dbms $dbms
     * @param string $tableName
    * @param array<string, mixed> $params
     * @param array<string> $selectedColumns
     * @param array<int, array<string, string>> $joins
    * @param array<int, array<string, mixed>> $where
     * @param string $groupBy
    * @param array<int, array<string, mixed>> $having
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

        $lof = self::generateLimitOffset($dbms, $limit, $offset);

        $tableName = self::quoteQualifiedIdentifier($dbms, $tableName);

        return "SELECT $c FROM $tableName $j$w$g$h$o$lof";

    }//end generateSelect()


    /**
     * @param Dbms $dbms
     * @param string $tableName
    * @param array<string, mixed> $params
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

        $tableName = self::quoteQualifiedIdentifier($dbms, $tableName);

        return "INSERT INTO $tableName ($columns) VALUES (:$values);";

    }//end generateInsert()


    /**
     * @param Dbms $dbms
     * @param string $tableName
    * @param array<string, mixed> $params
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

        $tableName = self::quoteQualifiedIdentifier($dbms, $tableName);

        return "UPDATE $tableName SET $s$w";

    }//end generateUpdate()


    /**
     * @param Dbms $dbms
     * @param string $tableName
    * @param array<string, mixed> $params
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

        $tableName = self::quoteQualifiedIdentifier($dbms, $tableName);

        return "DELETE FROM $tableName $w";

    }//end generateDelete()


    /**
     * @param Dbms $dbms
     * @param array<int, string> $cols
     * @return string
     */
    protected static function generateColumns(Dbms $dbms, array $cols): string
    {
        $result = [];

        foreach ($cols as $col) {
            $result[] = self::quoteSelectExpression($dbms, $col);
        }

        return implode(', ', $result);

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

        $sql = '';

        foreach ($joins as $j) {
            $type = self::normalizeJoinType((string) $j['type']);
            $tableName = self::quoteQualifiedIdentifier($dbms, $j['tableName']);
            $onCol1 = self::quoteQualifiedIdentifier($dbms, $j['onCol1']);
            $onCol2 = self::quoteQualifiedIdentifier($dbms, $j['onCol2']);

            $sql .= sprintf(' %s JOIN %s ON %s = %s', $type, $tableName, $onCol1, $onCol2);
        }

        return $sql;

    }//end generateJoins()


    /**
     * @param Dbms $dbms
     * @param string $conditionType
    * @param array<int, array<string, mixed>> $conditions
    * @param array<string, mixed> $params
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
            $operator = self::normalizeComparisonOperator((string) $c['operator']);
            $colName  = (string) $c['column'];
            $col      = self::quoteQualifiedIdentifier($dbms, $colName);
            $conditionOperator = empty($sql)
                ? strtoupper($conditionType)
                : self::normalizeConditionOperator((string) $c['condition_operator']);
            $paramBase = self::buildParamName($colName, $i);

            if ($operator !== 'IN' && $operator !== 'NOT IN') {
                $sql .= sprintf(' %s %s %s :%s', $conditionOperator, $col, $operator, $paramBase);
                $params[$paramBase] = $c['value'];
            } else {
                $value = $c['value'];

                if (!is_array($value)) {
                    $sql .= sprintf(' %s %s %s (:%s)', $conditionOperator, $col, $operator, $paramBase);
                    $params[$paramBase] = $value;
                    continue;
                }

                if (empty($value)) {
                    $sql .= sprintf(' %s 1 = 0', $conditionOperator);
                    continue;
                }

                $inPlaceholders = [];
                foreach ($value as $k => $entry) {
                    $paramName = "{$paramBase}_{$k}";
                    $inPlaceholders[] = ':' . $paramName;
                    $params[$paramName] = $entry;
                }

                $sql .= sprintf(
                    ' %s %s %s (%s)',
                    $conditionOperator,
                    $col,
                    $operator,
                    implode(', ', $inPlaceholders)
                );
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

        $groupBy = self::quoteQualifiedIdentifier($dbms, $groupBy);

        return " GROUP BY $groupBy";

    }//end generateGroupBy()


    protected static function generateLimitOffset(Dbms $dbms, int $limit=100, int $offset=0): string
    {
        return ($dbms != Dbms::Mssql) ? " LIMIT $limit OFFSET $offset" : " OFFSET $offset ROWS FETCH FIRST $limit ROWS ONLY";

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
        if (empty($orderBy)) {
            return '';
        }

        $sql = ' ORDER BY ';

        foreach ($orderBy as $col => $direction) {
            $col = self::quoteQualifiedIdentifier($dbms, $col);
            $dir = strtoupper(trim($direction));
            $dir = in_array($dir, ['ASC', 'DESC'], true) ? $dir : 'ASC';
            $sql .= "$col $dir ";
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
        $sql = '';

        foreach ($cols as $c) {
            $sql .= ' '.self::quoteQualifiedIdentifier($dbms, $c).' = :'.$c.',';
        }

        return substr($sql, 0, -1);

    }//end generateSet()


    protected static function quoteSelectExpression(Dbms $dbms, string $expression): string
    {
        $expression = trim($expression);

        if ($expression === '*') {
            return $expression;
        }

        if (str_contains($expression, '(') || str_contains($expression, ')')) {
            return $expression;
        }

        if (preg_match('/\s+AS\s+/i', $expression) === 1) {
            $parts = preg_split('/\s+AS\s+/i', $expression);
            if (is_array($parts) && count($parts) === 2) {
                return self::quoteQualifiedIdentifier($dbms, trim($parts[0]))
                    . ' AS '
                    . self::quoteQualifiedIdentifier($dbms, trim($parts[1]));
            }
        }

        return self::quoteQualifiedIdentifier($dbms, $expression);

    }//end quoteSelectExpression()


    protected static function quoteQualifiedIdentifier(Dbms $dbms, string $identifier): string
    {
        $identifier = trim($identifier);

        if ($identifier === '*') {
            return $identifier;
        }

        if (str_contains($identifier, '.')) {
            $parts = explode('.', $identifier);
            $quotedParts = [];

            foreach ($parts as $part) {
                $quotedParts[] = self::quoteIdentifier($dbms, trim($part));
            }

            return implode('.', $quotedParts);
        }

        return self::quoteIdentifier($dbms, $identifier);

    }//end quoteQualifiedIdentifier()


    protected static function quoteIdentifier(Dbms $dbms, string $identifier): string
    {
        if ($identifier === '*') {
            return $identifier;
        }

        if (self::isAlreadyQuoted($identifier)) {
            return $identifier;
        }

        return match ($dbms) {
            Dbms::Mysql => '`'.$identifier.'`',
            Dbms::Mssql => '['.$identifier.']',
            default => '"'.$identifier.'"',
        };

    }//end quoteIdentifier()


    protected static function isAlreadyQuoted(string $identifier): bool
    {
        return (
            (str_starts_with($identifier, '`') && str_ends_with($identifier, '`'))
            || (str_starts_with($identifier, '"') && str_ends_with($identifier, '"'))
            || (str_starts_with($identifier, '[') && str_ends_with($identifier, ']'))
        );

    }//end isAlreadyQuoted()


    protected static function buildParamName(string $column, int $index): string
    {
        $sanitized = preg_replace('/[^a-zA-Z0-9_]/', '_', $column);
        $sanitized = is_string($sanitized) ? $sanitized : 'param';

        return $sanitized.$index;

    }//end buildParamName()


    protected static function normalizeJoinType(string $joinType): string
    {
        $normalized = strtoupper(trim($joinType));
        $allowed = [
            'JOIN',
            'INNER JOIN',
            'LEFT JOIN',
            'LEFT OUTER JOIN',
            'RIGHT JOIN',
            'RIGHT OUTER JOIN',
            'FULL JOIN',
            'FULL OUTER JOIN',
            'CROSS JOIN',
        ];

        return in_array($normalized, $allowed, true) ? $normalized : 'JOIN';

    }//end normalizeJoinType()


    protected static function normalizeConditionOperator(string $conditionOperator): string
    {
        $normalized = strtoupper(trim($conditionOperator));

        return in_array($normalized, ['AND', 'OR'], true) ? $normalized : 'AND';

    }//end normalizeConditionOperator()


    protected static function normalizeComparisonOperator(string $operator): string
    {
        $normalized = strtoupper(trim($operator));

        $allowed = [
            '=',
            '!=',
            '<>',
            '<',
            '<=',
            '>',
            '>=',
            'LIKE',
            'NOT LIKE',
            'IN',
            'NOT IN',
            'IS',
            'IS NOT',
        ];

        return in_array($normalized, $allowed, true) ? $normalized : '=';

    }//end normalizeComparisonOperator()


}//end class


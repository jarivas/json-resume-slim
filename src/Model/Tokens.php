<?php
declare(strict_types=1);

namespace App\Model;

/**
 * @method static bool|Tokens first(array<string, mixed>|array<int, array> $criteria)
 * @method static bool|Tokens last(array<string, mixed>|array<int, array> $criteria)
 * @method static bool|Tokens[] get(array<string, mixed>|array<int, array> $criteria = [], int $offset = 0, int $limit = 100, array<string> $columns = []): bool|array
 */
class Tokens extends Model
{

    /**
     * @var string $tableName
     */
    protected static string $tableName = 'tokens';

    /**
     * @var string $primaryKey
     */
    protected static string $primaryKey = 'id';

    /**
     * @var Dbms $dbms
     */
    protected static Dbms $dbms = Dbms::Mysql;

    /**
     * @var array<string> $columns
     */
    protected static array $columns = [

        'id',
        'token',
        'expires_at',
    ];

    /**
     * @var string $id
     */
    public string $id;// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

    /**
     * @var string $token
     */
    public string $token;// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

    /**
     * @var string $expires_at
     */
    public string $expires_at;// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
}//end class

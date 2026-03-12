<?php
declare(strict_types=1);

namespace App\Model;

/**
 * @method static bool|Awards first(array<string, mixed>|array<int, array> $criteria)
 * @method static bool|Awards last(array<string, mixed>|array<int, array> $criteria)
 * @method static bool|Awards[] get(array<string, mixed>|array<int, array> $criteria = [], int $offset = 0, int $limit = 100, array<string> $columns = []): bool|array
 */
class Awards extends Model
{

    /**
     * @var string $tableName
     */
    protected static string $tableName = 'awards';

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
        'title',
        'date',
        'awarder',
        'summary',
        'basic_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * @var string $id
     */
    public string $id;// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

    /**
     * @var string $title
     */
    public string $title;// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

    /**
     * @var string $date
     */
    public string $date;// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

    /**
     * @var string $awarder
     */
    public string $awarder;// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

    /**
     * @var string $summary
     */
    public string $summary;// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

    /**
     * @var ?string $basic_id
     */
    public ?string $basic_id;// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

    /**
     * @var ?string $created_at
     */
    public ?string $created_at;// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

    /**
     * @var ?string $updated_at
     */
    public ?string $updated_at;// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

    /**
     * @var ?string $deleted_at
     */
    public ?string $deleted_at;// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
}//end class

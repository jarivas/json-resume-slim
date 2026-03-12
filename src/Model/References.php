<?php
declare(strict_types=1);

namespace App\Model;

/**
 * @method static bool|References first(array<string, mixed>|array<int, array> $criteria)
 * @method static bool|References last(array<string, mixed>|array<int, array> $criteria)
 * @method static bool|References[] get(array<string, mixed>|array<int, array> $criteria = [], int $offset = 0, int $limit = 100, array<string> $columns = []): bool|array
 */
class References extends Model
{

    /**
     * @var string $tableName
     */
    protected static string $tableName = 'references';

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
        'name',
        'reference',
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
     * @var string $name
     */
    public string $name;// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

    /**
     * @var string $reference
     */
    public string $reference;// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

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

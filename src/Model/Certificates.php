<?php
declare(strict_types=1);

namespace App\Model;

/**
 * @method static bool|Certificates first(array<string, mixed>|array<int, array> $criteria)
 * @method static bool|Certificates last(array<string, mixed>|array<int, array> $criteria)
 * @method static bool|Certificates[] get(array<string, mixed>|array<int, array> $criteria = [], int $offset = 0, int $limit = 100, array<string> $columns = []): bool|array
 */
class Certificates extends Model
{

    /**
     * @var string $tableName
     */
    protected static string $tableName = 'certificates';

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
        'date',
        'issuer',
        'url',
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
     * @var string $date
     */
    public string $date;// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

    /**
     * @var string $issuer
     */
    public string $issuer;// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

    /**
     * @var string $url
     */
    public string $url;// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

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

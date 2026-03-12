<?php
declare(strict_types=1);

namespace App\Model;

/**
 * @method static bool|Basics first(array<string, mixed>|array<int, array> $criteria)
 * @method static bool|Basics last(array<string, mixed>|array<int, array> $criteria)
 * @method static bool|Basics[] get(array<string, mixed>|array<int, array> $criteria = [], int $offset = 0, int $limit = 100, array<string> $columns = []): bool|array
 */
class Basics extends Model
{

    /**
     * @var string $tableName
     */
    protected static string $tableName = 'basics';

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
        'label',
        'email',
        'phone',
        'url',
        'summary',
        'location',
        'profiles',
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
     * @var string $label
     */
    public string $label;// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

    /**
     * @var string $email
     */
    public string $email;// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

    /**
     * @var string $phone
     */
    public string $phone;// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

    /**
     * @var ?string $url
     */
    public ?string $url;// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

    /**
     * @var ?string $summary
     */
    public ?string $summary;// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

    /**
     * @var ?string $location
     */
    public ?string $location;// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

    /**
     * @var ?string $profiles
     */
    public ?string $profiles;// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

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

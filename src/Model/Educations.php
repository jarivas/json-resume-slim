<?php
declare(strict_types=1);

namespace App\Model;

/**
 * @method static bool|Educations first(array<string, mixed>|array<int, array> $criteria)
 * @method static bool|Educations last(array<string, mixed>|array<int, array> $criteria)
 * @method static bool|Educations[] get(array<string, mixed>|array<int, array> $criteria = [], int $offset = 0, int $limit = 100, array<string> $columns = []): bool|array
 */
class Educations extends Model
{

    /**
     * @var string $tableName
     */
    protected static string $tableName = 'educations';

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
        'institution',
        'url',
        'area',
        'studyType',
        'startDate',
        'endDate',
        'score',
        'summary',
        'courses',
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
     * @var string $institution
     */
    public string $institution;// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

    /**
     * @var ?string $url
     */
    public ?string $url;// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

    /**
     * @var string $area
     */
    public string $area;// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

    /**
     * @var string $studyType
     */
    public string $studyType;// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

    /**
     * @var string $startDate
     */
    public string $startDate;// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

    /**
     * @var string $endDate
     */
    public string $endDate;// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

    /**
     * @var ?string $score
     */
    public ?string $score;// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

    /**
     * @var string $summary
     */
    public string $summary;// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

    /**
     * @var ?string $courses
     */
    public ?string $courses;// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

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

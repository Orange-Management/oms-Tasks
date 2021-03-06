<?php
/**
 * Orange Management
 *
 * PHP Version 8.0
 *
 * @package   Modules\Tasks\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace Modules\Tasks\Models;

use Modules\Admin\Models\AccountMapper;
use Modules\Calendar\Models\ScheduleMapper;
use Modules\Media\Models\MediaMapper;
use Modules\Tag\Models\TagMapper;
use phpOMS\DataStorage\Database\DataMapperAbstract;
use phpOMS\DataStorage\Database\Query\Builder;
use phpOMS\DataStorage\Database\Query\Where;
use phpOMS\DataStorage\Database\RelationType;

/**
 * Mapper class.
 *
 * @package Modules\Tasks\Models
 * @license OMS License 1.0
 * @link    https://orange-management.org
 * @since   1.0.0
 */
final class TaskMapper extends DataMapperAbstract
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    protected static array $columns = [
        'task_id'             => ['name' => 'task_id',         'type' => 'int',      'internal' => 'id'],
        'task_title'          => ['name' => 'task_title',      'type' => 'string',   'internal' => 'title'],
        'task_desc'           => ['name' => 'task_desc',       'type' => 'string',   'internal' => 'description'],
        'task_desc_raw'       => ['name' => 'task_desc_raw',   'type' => 'string',   'internal' => 'descriptionRaw'],
        'task_type'           => ['name' => 'task_type',       'type' => 'int',      'internal' => 'type'],
        'task_status'         => ['name' => 'task_status',     'type' => 'int',      'internal' => 'status'],
        'task_completion'     => ['name' => 'task_completion',     'type' => 'int',      'internal' => 'completion'],
        'task_closable'       => ['name' => 'task_closable',   'type' => 'bool',     'internal' => 'isClosable'],
        'task_editable'       => ['name' => 'task_editable',   'type' => 'bool',     'internal' => 'isEditable'],
        'task_priority'       => ['name' => 'task_priority',   'type' => 'int',      'internal' => 'priority'],
        'task_due'            => ['name' => 'task_due',        'type' => 'DateTime', 'internal' => 'due'],
        'task_done'           => ['name' => 'task_done',       'type' => 'DateTime', 'internal' => 'done'],
        'task_schedule'       => ['name' => 'task_schedule',   'type' => 'int',      'internal' => 'schedule'],
        'task_start'          => ['name' => 'task_start',      'type' => 'DateTime', 'internal' => 'start'],
        'task_created_by'     => ['name' => 'task_created_by', 'type' => 'int',      'internal' => 'createdBy', 'readonly' => true],
        'task_created_at'     => ['name' => 'task_created_at', 'type' => 'DateTimeImmutable', 'internal' => 'createdAt', 'readonly' => true],
    ];

    /**
     * Has many relation.
     *
     * @var array<string, array{mapper:string, table:string, self?:?string, external?:?string, column?:string}>
     * @since 1.0.0
     */
    protected static array $hasMany = [
        'taskElements' => [
            'mapper'       => TaskElementMapper::class,
            'table'        => 'task_element',
            'self'         => 'task_element_task',
            'external'     => null,
        ],
        'media'        => [
            'mapper'   => MediaMapper::class,
            'table'    => 'task_media',
            'external' => 'task_media_dst',
            'self'     => 'task_media_src',
        ],
        'tags'         => [
            'mapper'   => TagMapper::class,
            'table'    => 'task_tag',
            'external' => 'task_tag_dst',
            'self'     => 'task_tag_src',
        ],
    ];

    /**
     * Belongs to.
     *
     * @var array<string, array{mapper:string, external:string}>
     * @since 1.0.0
     */
    protected static array $belongsTo = [
        'createdBy' => [
            'mapper'     => AccountMapper::class,
            'external'   => 'task_created_by',
        ],
    ];

    /**
     * Has one relation.
     *
     * @var array<string, array{mapper:string, external:string, by?:string, column?:string, conditional?:bool}>
     * @since 1.0.0
     */
    protected static array $ownsOne = [
        'schedule' => [
            'mapper'     => ScheduleMapper::class,
            'external'   => 'task_schedule',
        ],
    ];

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    protected static string $table = 'task';

    /**
     * Created at.
     *
     * @var string
     * @since 1.0.0
     */
    protected static string $createdAt = 'task_created_at';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    protected static string $primaryField = 'task_id';

    /**
     * Get open tasks by createdBy
     *
     * @param int $user User
     *
     * @return Task[]
     *
     * @since 1.0.0
     */
    public static function getOpenCreatedBy(int $user) : array
    {
        $depth = 3;
        $query = self::getQuery();
        $query->where(self::$table . '_d' . $depth . '.task_created_by', '=', $user)
            ->where(self::$table . '_d' . $depth . '.task_status', '=', TaskStatus::OPEN);

        return self::getAllByQuery($query);
    }

    /**
     * Get open tasks for user
     *
     * @param int $user User
     *
     * @return Task[]
     *
     * @since 1.0.0
     */
    public static function getOpenTo(int $user) : array
    {
        $depth = 3;
        $query = self::getQuery();
        $query->innerJoin(TaskElementMapper::getTable())
                ->on(self::$table . '_d' . $depth . '.task_id', '=', TaskElementMapper::getTable() . '.task_element_task')
            ->innerJoin(AccountRelationMapper::getTable())
                ->on(TaskElementMapper::getTable() . '.task_element_id', '=', AccountRelationMapper::getTable() . '.task_account_task_element')
            ->where(self::$table . '_d' . $depth . '.task_status', '=', TaskStatus::OPEN)
            ->andWhere(AccountRelationMapper::getTable() . '.task_account_account', '=', $user)
            ->andWhere(AccountRelationMapper::getTable() . '.task_account_duty', '=', DutyType::TO);

        return self::getAllByQuery($query);
    }

    /**
     * Get open tasks for mentioned user
     *
     * @param int $user User
     *
     * @return Task[]
     *
     * @since 1.0.0
     */
    public static function getOpenAny(int $user) : array
    {
        $depth = 3;
        $query = self::getQuery();
        $query->innerJoin(TaskElementMapper::getTable())
                ->on(self::$table . '_d' . $depth . '.task_id', '=', TaskElementMapper::getTable() . '.task_element_task')
            ->innerJoin(AccountRelationMapper::getTable())
                ->on(TaskElementMapper::getTable() . '.task_element_id', '=', AccountRelationMapper::getTable() . '.task_account_task_element')
            ->where(self::$table . '_d' . $depth . '.task_status', '=', TaskStatus::OPEN)
            ->andWhere(AccountRelationMapper::getTable() . '.task_account_account', '=', $user);

        return self::getAllByQuery($query);
    }

    /**
     * Get open tasks by cc
     *
     * @param int $user User
     *
     * @return Task[]
     *
     * @since 1.0.0
     */
    public static function getOpenCC(int $user) : array
    {
        $depth = 3;
        $query = self::getQuery();
        $query->innerJoin(TaskElementMapper::getTable())
                ->on(self::$table . '_d' . $depth . '.task_id', '=', TaskElementMapper::getTable() . '.task_element_task')
            ->innerJoin(AccountRelationMapper::getTable())
                ->on(TaskElementMapper::getTable() . '.task_element_id', '=', AccountRelationMapper::getTable() . '.task_account_task_element')
            ->where(self::$table . '_d' . $depth . '.task_status', '=', TaskStatus::OPEN)
            ->andWhere(AccountRelationMapper::getTable() . '.task_account_account', '=', $user)
            ->andWhere(AccountRelationMapper::getTable() . '.task_account_duty', '=', DutyType::CC);

        return self::getAllByQuery($query);
    }

    /**
     * Get tasks created by user
     *
     * @param int $user User
     *
     * @return Task[]
     *
     * @since 1.0.0
     */
    public static function getCreatedBy(int $user) : array
    {
        $depth = 3;
        $query = self::getQuery();
        $query->where(self::$table . '_d' . $depth . '.task_created_by', '=', $user);

        return self::getAllByQuery($query);
    }

    /**
     * Get tasks sent to user
     *
     * @param int $user User
     *
     * @return Task[]
     *
     * @since 1.0.0
     */
    public static function getTo(int $user) : array
    {
        $depth = 3;
        $query = self::getQuery();
        $query->innerJoin(TaskElementMapper::getTable())
                ->on(self::$table . '_d' . $depth . '.task_id', '=', TaskElementMapper::getTable() . '.task_element_task')
            ->innerJoin(AccountRelationMapper::getTable())
                ->on(TaskElementMapper::getTable() . '.task_element_id', '=', AccountRelationMapper::getTable() . '.task_account_task_element')
            ->where(AccountRelationMapper::getTable() . '.task_account_account', '=', $user)
            ->andWhere(AccountRelationMapper::getTable() . '.task_account_duty', '=', DutyType::TO);

        return self::getAllByQuery($query);
    }

    /**
     * Get tasks cc to user
     *
     * @param int $user User
     *
     * @return Task[]
     *
     * @since 1.0.0
     */
    public static function getCC(int $user) : array
    {
        $depth = 3;
        $query = self::getQuery();
        $query->innerJoin(TaskElementMapper::getTable())
                ->on(self::$table . '_d' . $depth . '.task_id', '=', TaskElementMapper::getTable() . '.task_element_task')
            ->innerJoin(AccountRelationMapper::getTable())
                ->on(TaskElementMapper::getTable() . '.task_element_id', '=', AccountRelationMapper::getTable() . '.task_account_task_element')
            ->where(AccountRelationMapper::getTable() . '.task_account_account', '=', $user)
            ->andWhere(AccountRelationMapper::getTable() . '.task_account_duty', '=', DutyType::CC);

        return self::getAllByQuery($query);
    }

    /**
     * Get tasks that have something to do with the user
     *
     * @param int $user User
     *
     * @return Task[]
     *
     * @since 1.0.0
     */
    public static function getAny(int $user) : array
    {
        $depth = 3;
        $query = self::getQuery();
        $query->innerJoin(TaskElementMapper::getTable())
                ->on(self::$table . '_d' . $depth . '.task_id', '=', TaskElementMapper::getTable() . '.task_element_task')
            ->innerJoin(AccountRelationMapper::getTable())
                ->on(TaskElementMapper::getTable() . '.task_element_id', '=', AccountRelationMapper::getTable() . '.task_account_task_element')
            ->where(AccountRelationMapper::getTable() . '.task_account_account', '=', $user)
            ->orWhere(self::$table . '_d' . $depth . '.task_created_by', '=', $user)
            ->orderBy(TaskElementMapper::getTable() . '.' . TaskElementMapper::getCreatedAt(), 'DESC');

        return self::getAllByQuery($query);
    }

    /**
     * Get tasks that have something to do with the user
     *
     * @param int    $user      User
     * @param mixed  $pivot     Pivot
     * @param string $column    Sort column/pivot column
     * @param int    $limit     Result limit
     * @param string $order     Order of the elements
     * @param int    $relations Load relations
     * @param int    $depth     Relation depth
     *
     * @return Task[]
     *
     * @since 1.0.0
     */
    public static function getAnyBeforePivot(
        int $user,
        mixed $pivot,
        string $column = null,
        int $limit = 50,
        string $order = 'ASC',
        int $relations = RelationType::ALL,
        int $depth = 3
    ) : array
    {
        $depth     = 3;
        $userWhere = new Where(self::$db);
        $userWhere->where(AccountRelationMapper::getTable() . '.task_account_account', '=', $user)
            ->orWhere(self::$table . '_d' . $depth . '.task_created_by', '=', $user);

        $query = self::getQuery();
        $query->innerJoin(TaskElementMapper::getTable())
                ->on(self::$table . '_d' . $depth . '.task_id', '=', TaskElementMapper::getTable() . '.task_element_task')
            ->innerJoin(AccountRelationMapper::getTable())
                ->on(TaskElementMapper::getTable() . '.task_element_id', '=', AccountRelationMapper::getTable() . '.task_account_task_element')
            ->where($userWhere)
            ->andWhere(static::$table . '_d' . $depth . '.' . ($column !== null ? self::getColumnByMember($column) : static::$primaryField), '<', $pivot)
            ->orderBy(static::$table . '_d' . $depth . '.' . ($column !== null ? self::getColumnByMember($column) : static::$primaryField), $order)
            ->limit($limit);

        return self::getAllByQuery($query);
    }

    /**
     * Get tasks that have something to do with the user
     *
     * @param int    $user      User
     * @param mixed  $pivot     Pivot
     * @param string $column    Sort column/pivot column
     * @param int    $limit     Result limit
     * @param string $order     Order of the elements
     * @param int    $relations Load relations
     * @param int    $depth     Relation depth
     *
     * @return Task[]
     *
     * @since 1.0.0
     */
    public static function getAnyAfterPivot(
        int $user,
        mixed $pivot,
        string $column = null,
        int $limit = 50,
        string $order = 'ASC',
        int $relations = RelationType::ALL,
        int $depth = 3
    ) : array
    {
        $depth     = 3;
        $userWhere = new Where(self::$db);
        $userWhere->where(AccountRelationMapper::getTable() . '.task_account_account', '=', $user)
            ->orWhere(self::$table . '_d' . $depth . '.task_created_by', '=', $user);

        $query = self::getQuery();
        $query->innerJoin(TaskElementMapper::getTable())
                ->on(self::$table . '_d' . $depth . '.task_id', '=', TaskElementMapper::getTable() . '.task_element_task')
            ->innerJoin(AccountRelationMapper::getTable())
                ->on(TaskElementMapper::getTable() . '.task_element_id', '=', AccountRelationMapper::getTable() . '.task_account_task_element')
            ->where($userWhere)
            ->andWhere(static::$table . '_d' . $depth . '.' . ($column !== null ? self::getColumnByMember($column) : static::$primaryField), '>', $pivot)
            ->orderBy(static::$table . '_d' . $depth . '.' . ($column !== null ? self::getColumnByMember($column) : static::$primaryField), $order)
            ->limit($limit);

        return self::getAllByQuery($query);
    }

    /**
     * Check if a user has reading permission for a task
     *
     * @param int $user User id
     * @param int $task Task id
     *
     * @return bool
     *
     * @since 1.0.0
     */
    public static function hasReadingPermission(int $user, int $task) : bool
    {
        $depth     = 1;
        $userWhere = new Where(self::$db);
        $userWhere->where(AccountRelationMapper::getTable() . '.task_account_account', '=', $user)
            ->orWhere(self::$table . '_d' . $depth . '.task_created_by', '=', $user);

        $query = new Builder(self::$db);
        $query->selectAs(self::$table . '_d' . $depth . '.' . self::$primaryField, self::$primaryField . '_d' . $depth)
            ->fromAs(self::$table, self::$table . '_d' . $depth)
            ->innerJoin(TaskElementMapper::getTable())
                ->on(self::$table . '_d' . $depth . '.' . self::$primaryField, '=', TaskElementMapper::getTable() . '.task_element_task')
            ->innerJoin(AccountRelationMapper::getTable())
                ->on(TaskElementMapper::getTable() . '.' . TaskElementMapper::getPrimaryField(), '=', AccountRelationMapper::getTable() . '.task_account_task_element')
            ->where($userWhere)
            ->andWhere(self::$table . '_d' . $depth . '.' . self::$primaryField, '=', $task);

        return !empty(self::getAllByQuery($query, RelationType::ALL, 1));
    }

    /**
     * Count unread task
     *
     * @param int $user User
     *
     * @return int
     *
     * @since 1.0.0
     */
    public static function countUnread(int $user) : int
    {
        try {
            $query = new Builder(self::$db);

            $query->count('DISTINCT ' . self::$table . '.' . self::$primaryField)
                ->from(self::$table)
                ->innerJoin(TaskElementMapper::getTable())
                    ->on(self::$table . '.' . self::$primaryField, '=', TaskElementMapper::getTable() . '.task_element_task')
                ->innerJoin(AccountRelationMapper::getTable())
                    ->on(TaskElementMapper::getTable() . '.' . TaskElementMapper::getPrimaryField(), '=', AccountRelationMapper::getTable() . '.task_account_task_element')
                ->where(self::$table . '.task_status', '=', TaskStatus::OPEN)
                ->andWhere(AccountRelationMapper::getTable() . '.task_account_account', '=', $user);

            $sth = self::$db->con->prepare($query->toSql());
            $sth->execute();

            $fetched = $sth->fetchAll();

            if ($fetched === false) {
                return -1;
            }

            $count = $fetched[0][0] ?? 0;
        } catch (\Exception $e) {
            return -1;
        }

        return $count;
    }
}

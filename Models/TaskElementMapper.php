<?php
/**
 * Orange Management
 *
 * PHP Version 7.0
 *
 * @category   TBD
 * @package    TBD
 * @author     OMS Development Team <dev@oms.com>
 * @author     Dennis Eichhorn <d.eichhorn@oms.com>
 * @copyright  2013 Dennis Eichhorn
 * @license    OMS License 1.0
 * @version    1.0.0
 * @link       http://orange-management.com
 */
namespace Modules\Tasks\Models;

use phpOMS\DataStorage\Database\DataMapperAbstract;
use phpOMS\DataStorage\Database\Query\Builder;

class TaskElementMapper extends DataMapperAbstract
{

    /**
     * Columns.
     *
     * @var array<string, array>
     * @since 1.0.0
     */
    protected static $columns = [
        'task_element_id'         => ['name' => 'task_element_id', 'type' => 'int', 'internal' => 'id'],
        'task_element_desc'       => ['name' => 'task_element_desc', 'type' => 'string', 'internal' => 'description'],
        'task_element_status'     => ['name' => 'task_element_status', 'type' => 'int', 'internal' => 'status'],
        'task_element_due'        => ['name' => 'task_element_due', 'type' => 'DateTime', 'internal' => 'due'],
        'task_element_forwarded'  => ['name' => 'task_element_forwarded', 'type' => 'int', 'internal' => 'forwarded'],
        'task_element_task'       => ['name' => 'task_element_task', 'type' => 'int', 'internal' => 'task'],
        'task_element_created_by' => ['name' => 'task_element_created_by', 'type' => 'int', 'internal' => 'createdBy'],
        'task_element_created_at' => ['name' => 'task_element_created_at', 'type' => 'DateTime', 'internal' => 'createdAt'],
    ];

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    protected static $table = 'task_element';

    protected static $createdAt = 'task_created_at';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    protected static $primaryField = 'task_element_id';

    /**
     * Create media.
     *
     * @param Task $obj Media
     *
     * @return bool
     *
     * @since  1.0.0
     * @author Dennis Eichhorn <d.eichhorn@oms.com>
     */
    public function create($obj)
    {
        try {
            $objId = parent::create($obj);
        } catch (\Exception $e) {
            var_dump($e);

            return false;
        }

        return $objId;
    }
}

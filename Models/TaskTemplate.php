<?php
/**
 * Orange Management
 *
 * PHP Version 7.1
 *
 * @category   TBD
 * @package    TBD
 * @author     OMS Development Team <dev@oms.com>
 * @copyright  Dennis Eichhorn
 * @license    OMS License 1.0
 * @version    1.0.0
 * @link       http://orange-management.com
 */
declare(strict_types=1);
namespace Modules\Tasks\Models;

/**
 * Task class.
 *
 * @category   Modules
 * @package    Tasks
 * @author     OMS Development Team <dev@oms.com>
 * @license    OMS License 1.0
 * @link       http://orange-management.com
 * @since      1.0.0
 */
class TaskTemplate extends Task
{
    /**
     * Type.
     *
     * @var TaskType
     * @since 1.0.0
     */
    private $type = TaskType::TEMPLATE;
}

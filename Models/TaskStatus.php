<?php
/**
 * Orange Management
 *
 * PHP Version 7.1
 *
 * @category   TBD
 * @package    TBD
 * @copyright  Dennis Eichhorn
 * @license    OMS License 1.0
 * @version    1.0.0
 * @link       http://orange-management.com
 */
declare(strict_types=1);
namespace Modules\Tasks\Models;

use phpOMS\Datatypes\Enum;

/**
 * Task status enum.
 *
 * @category   Tasks
 * @package    Modules
 * @license    OMS License 1.0
 * @link       http://orange-management.com
 * @since      1.0.0
 */
abstract class TaskStatus extends Enum
{
    /* public */ const OPEN = 1;

    /* public */ const WORKING = 2;

    /* public */ const SUSPENDED = 3;

    /* public */ const CANCELED = 4;

    /* public */ const DONE = 5;
}

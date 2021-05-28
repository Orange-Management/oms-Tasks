<?php
/**
 * Orange Management
 *
 * PHP Version 8.0
 *
 * @package   Modules\Tasks\Admin\Install
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace Modules\Tasks\Admin\Install;

use phpOMS\DataStorage\Database\DatabasePool;

/**
 * Search class.
 *
 * @package Modules\Tasks\Admin\Install
 * @license OMS License 1.0
 * @link    https://orange-management.org
 * @since   1.0.0
 */
final class Search
{
    /**
     * Install navigation providing
     *
     * @param string       $path   Module path
     * @param DatabasePool $dbPool Database pool for database interaction
     *
     * @return void
     *
     * @since 1.0.0
     */
    public static function install(string $path, DatabasePool $dbPool) : void
    {
        \Modules\Search\Admin\Installer::installExternal($dbPool, ['path' => __DIR__ . '/SearchCommands.php']);
    }
}
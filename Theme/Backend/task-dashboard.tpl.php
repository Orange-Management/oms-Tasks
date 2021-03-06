<?php
/**
 * Orange Management
 *
 * PHP Version 8.0
 *
 * @package   Modules\Tasks
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

use Modules\Tasks\Models\TaskPriority;
use phpOMS\Uri\UriFactory;

/**
 * @todo Orange-Management/oms-Tasks#4
 *  Batch handle tasks in the dashboard list
 *  In the dashboard/list it should be possible to change the status of a task without going into it (changing it to done is the most important).
 *  This could be done with a button but also touch sliding/swiping should be possible for mobile.
 *  It could also make sense to implement checkboxes infront of the list items which then show a close/done etc. button which can be pressed and changes the status of all of the checked tasks.
 */

/** @var \phpOMS\Views\View $this */
/** @var \Modules\Tasks\Models\Task[] $tasks */
$tasks = $this->getData('tasks') ?? [];

$previous = empty($tasks) ? '{/prefix}task/dashboard' : '{/prefix}task/dashboard?{?}&id=' . \reset($tasks)->getId() . '&ptype=p';
$next     = empty($tasks) ? '{/prefix}task/dashboard' : '{/prefix}task/dashboard?{?}&id=' . \end($tasks)->getId() . '&ptype=n';

echo $this->getData('nav')->render(); ?>

<div class="row">
    <div class="col-xs-12">
        <div class="portlet">
            <div class="portlet-head"><?= $this->getHtml('Tasks'); ?><i class="fa fa-download floatRight download btn"></i></div>
            <table id="taskList" class="default">
                <thead>
                    <td><?= $this->getHtml('Status'); ?><i class="sort-asc fa fa-chevron-up"></i><i class="sort-desc fa fa-chevron-down"></i>
                    <td><?= $this->getHtml('Due/Priority'); ?><i class="sort-asc fa fa-chevron-up"></i><i class="sort-desc fa fa-chevron-down"></i>
                    <td class="wf-100"><?= $this->getHtml('Title'); ?><i class="sort-asc fa fa-chevron-up"></i><i class="sort-desc fa fa-chevron-down"></i>
                    <td><?= $this->getHtml('Tag'); ?>
                    <td><?= $this->getHtml('Creator'); ?><i class="sort-asc fa fa-chevron-up"></i><i class="sort-desc fa fa-chevron-down"></i>
                    <td><?= $this->getHtml('Created'); ?><i class="sort-asc fa fa-chevron-up"></i><i class="sort-desc fa fa-chevron-down"></i>
                <tfoot>
                <tbody>
                <?php
                    $c   = 0; foreach ($tasks as $key => $task) : ++$c;
                    $url = UriFactory::build('{/prefix}task/single?{?}&id=' . $task->getId());
                ?>
                    <tr tabindex="0" data-href="<?= $url; ?>">
                        <td data-label="<?= $this->getHtml('Status'); ?>">
                            <a href="<?= $url; ?>">
                                <span class="tag <?= $this->printHtml('task-status-' . $task->getStatus()); ?>">
                                    <?= $this->getHtml('S' . $task->getStatus()); ?>
                                </span>
                            </a>
                        <td data-label="<?= $this->getHtml('Due/Priority'); ?>">
                            <a href="<?= $url; ?>">
                            <?php if ($task->getPriority() === TaskPriority::NONE) : ?>
                                <?= $this->printHtml($task->due->format('Y-m-d H:i')); ?>
                            <?php else : ?>
                                <?= $this->getHtml('P' . $task->getPriority()); ?>
                            <?php endif; ?>
                            </a>
                        <td data-label="<?= $this->getHtml('Title'); ?>">
                            <a href="<?= $url; ?>"><?= $this->printHtml($task->title); ?></a>
                        <td data-label="<?= $this->getHtml('Tag'); ?>">
                            <?php $tags = $task->getTags(); foreach ($tags as $tag) : ?>
                            <a href="<?= $url; ?>">
                            <span class="tag" style="background: <?= $this->printHtml($tag->color); ?>"><?= $tag->icon !== null ? '<i class="' . $this->printHtml($tag->icon ?? '') . '"></i>' : ''; ?><?= $this->printHtml($tag->getL11n()); ?></span>
                            </a>
                            <?php endforeach; ?>
                        <td data-label="<?= $this->getHtml('Creator'); ?>">
                            <a href="<?= $url; ?>"><?= $this->printHtml($task->createdBy->name1); ?></a>
                        <td data-label="<?= $this->getHtml('Created'); ?>">
                            <a href="<?= $url; ?>"><?= $this->printHtml($task->createdAt->format('Y-m-d H:i')); ?></a>
                <?php endforeach; if ($c == 0) : ?>
                    <tr><td colspan="6" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
                <?php endif; ?>
            </table>
            <div class="portlet-foot">
                <a class="button" href="<?= UriFactory::build($previous); ?>"><?= $this->getHtml('Previous', '0', '0'); ?></a>
                <a class="button" href="<?= UriFactory::build($next); ?>"><?= $this->getHtml('Next', '0', '0'); ?></a>
            </div>
        </div>
    </div>
</div>
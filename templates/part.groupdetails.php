<h2><?php p($l->t('Group')); ?> <span><?php p($_['groupname']); ?></span></h2>

<?php p($l->t('User list')); ?>

<p><?php p($l->t('Toggle:')); ?> <a class="toggle-vis" data-column="2">Quota</a></p>

<table id="tableUsers" class="display">
    <thead>
        <tr>
            <th><?php p($l->t('Username')); ?></th>
            <th><?php p($l->t('Groups')); ?></th>
            <th><?php p($l->t('Quota')); ?></th>
            <th><?php p($l->t('Disk Usage (click in cell to get)')); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($_['userList'] as $user): ?>
        <tr>
            <td><?php p($user['uid']); ?></td>
            <td><?php print_unescaped(join(', ', array_map(function($group) {
                    return "<a href=\"" . \OC::$server->getURLGenerator()->linkToRoute('lotsofusers.page.group', ['groupname' => $group]) . "\">" . $group . "</a>";
                }, explode(', ', $user['groups'])))); ?></td>
            <td><?php p($user['quota']); ?></td>
            <td></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>


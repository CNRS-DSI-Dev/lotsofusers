<h2><?php p($l->t('User')); ?> <span><?php p($_['username']); ?></span></h2>

<div id="container">

<div id="user" class="dataBlock">
    <p class="header"><?php p($l->t('User')); ?></p>

    <p><span class="label"><?php p($l->t('Display name')); ?></span> <span><?php p($_['displayname']); ?></span> </p>
    <p><span class="label"><?php p($l->t('Language')); ?></span> <span><?php p($_['language']); ?></span></p>
    <p><span class="label"><?php p($l->t('Type')); ?></span> <span><?php p($_['usertype']); ?></span></p>

    <button id="deluser" class="louButton"><?php p($l->t('Delete this user')); ?></button>
    <button id="changepass" class="louButton"><?php p($l->t('Change local password')); ?></button>
    <input id="newpass" type="password"> <button id="validatepass" class="louButton"><?php p($l->t('Validate local password')); ?></button>
</div>

<div id="mycore" class="dataBlock">
    <p class="header"><?php p($l->t('My CoRe')); ?></span></p>

    <p><?php p($l->t('Last connection:')); ?> <span><?php p($_['lastlogin']); ?></span> </p>
</div>

<div id="disk" class="dataBlock">
    <p class="header"><?php p($l->t('Disk space')); ?></span></p>

    <div>
        <p><?php p($l->t('Quota')); ?> <span id="quotaValue"><?php p($_['quota']); ?></span> </p>
    </div>

    <div>
        <div id="quotabar"> <div style="width:<?php p($_['quotabar']);?>"></div> </div>
        <p id="diskUsage"><?php p($l->t('Disk usage')); ?> <span><?php p($_['diskusage']); ?></span> </p>
    </div>

    <label><?php p($l->t('New quota')); ?> <input type="text" name="newquota" id="newquota" /></label>
    <input type="hidden" name="username" id="username" value="<?php p($_['username']); ?>">
    <button id="setquota" class="louButton"><?php p($l->t('Set quota')); ?></button>
    <img src="<?php p(\OC::$server->getURLGenerator()->imagePath('lotsofusers', 'help.png')); ?>" />
</div>

<div id="groups" class="dataBlock">
    <p class="header"><?php p($l->t('Groups')); ?></span></p>

    <div class="user">
        <p><?php p($l->t('Member of')); ?></p>
        <div class="groupList">
        <?php foreach($_['groups'] as $group): ?>
            <div>
                <span><a href="<?php p(\OC::$server->getURLGenerator()->linkToRoute('lotsofusers.page.group', ['groupname' => $group])); ?>"><?php p($group); ?></a></span>
                <img class="action" data-gid="<?php p($group); ?>" data-uid="<?php p($_['username']); ?>" src="<?php p(\OC::$server->getURLGenerator()->imagePath('core', 'actions/delete.svg')); ?>">
            </div>
        <?php endforeach; ?>
        </div>

        <div id="addGroup">
            <input type="text" name="newgroup" id="newgroup">
        </div>
        <button id="addInGroup" data-uid="<?php p($_['username']); ?>" class="louButton"><?php p($l->t('Add in group')); ?></button>
    </div>

    <div class="admin">
        <p><?php p($l->t('Admin of')); ?></p>
        <div class="adminList">
        <?php foreach($_['groupsAdmin'] as $group): ?>
            <div>
                <span><a href="<?php p(\OC::$server->getURLGenerator()->linkToRoute('lotsofusers.page.group', ['groupname' => $group])); ?>"><?php p($group); ?></a></span>
                <img class="action" data-gid="<?php p($group); ?>" data-uid="<?php p($_['username']); ?>" src="<?php p(\OC::$server->getURLGenerator()->imagePath('core', 'actions/delete.svg')); ?>">
            </div>
        <?php endforeach; ?>
        </div>

        <div id="addAdmin">
            <input type="text" name="newAdmin" id="newAdmin">
        </div>
        <button id="setAdmin" data-uid="<?php p($_['username']); ?>" class="louButton"><?php p($l->t('Set admin')); ?></button>
    </div>
</div>

<div id="restore" class="dataBlock">
    <p class="header"><?php p($l->t('Restore requests')); ?></span></p>


</div>

<div id="migrate" class="dataBlock">
    <p class="header"><?php p($l->t('Waiting migration requests')); ?></span></p>
</div>

</div>

<h2><?php p($l->t('Advanced search')); ?></h2>


<h3><?php p($l->t('Enter your search criteria')); ?></h3>

<form id="searchForm">
<div>
    <!--<div id="mquota">
        <p><?php p($l->t('Quota (more than)')); ?></p>
        <input type="text" name="userQuota" id="userQuota">
    </div>-->
    <div id="uid">
        <p><?php p($l->t('User ID (contains)')); ?></p>
        <input type="text" name="userId" id="userId">
    </div>
    <div id="gid">
        <p><?php p($l->t('Group ID (contains)')); ?></p>
        <input type="text" name="groupId" id="groupId">
    </div>
    <div id="lastConnectionFrom">
        <p><?php p($l->t('Last connection (From)')); ?></p>
        <input type="date" name="lastUserConnectionFrom" id="lastUserConnectionFrom">
    </div>
    <div id="lastConnectionTo">
        <p><?php p($l->t('Last connection (To)')); ?></p>
        <input type="date" name="lastUserConnectionTo" id="lastUserConnectionTo">
    </div>
</div>
<div id="formAction"><input type="submit" value="<?php p($l->t('Search')); ?>"></div>
</form>

<div id="searchResults">
</div>

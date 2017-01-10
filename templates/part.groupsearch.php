<h2><?php p($l->t('Add local group')); ?></h2>

<div>
    <label for="groupname"><?php p($l->t('Group name')); ?></label>
</div>
<div class="addGroup">
    <input type="text" name="groupname" id="groupname">
    <button><?php p($l->t('Add local group')); ?></button>
</div>

<div class="addedGroup">
    <p></p>
</div>

<br>

<h2><?php p($l->t('Search group')); ?></h2>

<div class="groupSearch groupsView">
    <input type="text" placeholder="Search...">
    <p class="more">There's more...</p>
</div>


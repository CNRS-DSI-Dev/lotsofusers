<h2><?php p($l->t('Add local user')); ?></h2>

<div>
    <label for="username"><?php p($l->t('Username')); ?></label>
    <label for="password"><?php p($l->t('Password')); ?></label>
    <label for="addGroup"><?php p($l->t('Groups')); ?></label>
</div>
<div class="addUser">
    <div style="display: inline-block;"><input type="text" name="username" id="username" autocomplete="username"></div>
    <div style="display: inline-block;"><input type="password" name="password" id="password" autocomplete="new-password"></div>
    <datalist id="bip"></datalist>
    <div id="groupList">
        <input type="text" name="groups" id="addGroup" autocomplete="off">
    </div>
    <button><?php p($l->t('Add local user')); ?></button>
</div>

<div class="addedUser">
    <p></p>
</div>

<br>

<h2><?php p($l->t('Search user')); ?></h2>

<div class="userSearch">
    <input type="text" placeholder="Search...">
    <ul class="list"></ul>
    <p class="more">There's more...</p>
</div>


<?php
script('lotsofusers', 'datatables.min');
script('lotsofusers', 'app');
script('lotsofusers', 'usermodel');
script('lotsofusers', 'usercollection');
script('lotsofusers', 'usersview');
script('lotsofusers', 'script');
style('lotsofusers', 'datatables.min');
style('lotsofusers', 'style');
?>

<div id="app-group">
    <div id="app-navigation">
        <?php print_unescaped($this->inc('part.navigation')); ?>
        <?php print_unescaped($this->inc('part.settings')); ?>
    </div>

    <div id="app-content">
        <div id="app-content-wrapper">
            <?php print_unescaped($this->inc('part.groupdetails')); ?>
        </div>
    </div>
</div>

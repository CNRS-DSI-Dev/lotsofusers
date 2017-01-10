<?php
script('lotsofusers', 'app');
script('lotsofusers', 'groupmodel');
script('lotsofusers', 'groupcollection');
script('lotsofusers', 'groupsview');
script('lotsofusers', 'script');
style('lotsofusers', 'style');
?>

<div id="app-groups">
    <div id="app-navigation">
        <?php print_unescaped($this->inc('part.navigation')); ?>
        <?php print_unescaped($this->inc('part.settings')); ?>
    </div>

    <div id="app-content">
        <div id="app-content-wrapper">
            <?php print_unescaped($this->inc('part.groupsearch')); ?>
        </div>
    </div>
</div>

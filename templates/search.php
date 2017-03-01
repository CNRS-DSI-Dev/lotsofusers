<?php
script('lotsofusers', [
    'app',
    'groupmodel',
    'groupcollection',
    'groupsview',
    'script'
]);

script('core', [
    'singleselect'
]);

// vendor_script('moment', 'moment');

style('lotsofusers', 'style');
?>

<div id="app-search">
    <div id="app-navigation">
        <?php print_unescaped($this->inc('part.navigation')); ?>
        <?php print_unescaped($this->inc('part.settings')); ?>
    </div>

    <div id="app-content">
        <div id="app-content-wrapper">
            <?php print_unescaped($this->inc('part.search')); ?>
        </div>
    </div>
</div>

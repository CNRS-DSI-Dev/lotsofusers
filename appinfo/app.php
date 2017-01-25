<?php
/**
 * ownCloud - lotsofusers
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Patrick Paysant <ppaysant@linagora.com>
 * @copyright 2016 CNRS DSI
 */

namespace OCA\LotsOfUsers\AppInfo;

use OCP\AppFramework\App;

require_once __DIR__ . '/autoload.php';

$app = new Application;
$container = $app->getContainer();

$currentUser = \OC::$server->getUserSession()->getUser();
$groupManager = \OC::$server->getGroupManager();

$isAdminOrSubadmin = false;
if (!empty($currentUser)) {
    $isAdminOrSubadmin = $groupManager->getSubAdmin()->isSubAdmin($currentUser);
}

if ($isAdminOrSubadmin) {
    $container->query('\OCP\INavigationManager')->add(function () use ($container) {
        $urlGenerator = $container->query('\OCP\IURLGenerator');
        $l10n = $container->query('\OCP\IL10N');
        return [
            // the string under which your app will be referenced in owncloud
            'id' => 'lotsofusers',

            // sorting weight for the navigation. The higher the number, the higher
            // will it be listed in the navigation
            'order' => 10,

            // the route that will be shown on startup
            'href' => $urlGenerator->linkToRoute('lotsofusers.page.users'),

            // the icon that will be shown in the navigation
            // this file needs to exist in img/
            'icon' => $urlGenerator->imagePath('lotsofusers', 'app.svg'),

            // the title of your application. This will be used in the
            // navigation or on the settings page of your app
            'name' => $l10n->t('Lots Of Users'),
        ];
});
}

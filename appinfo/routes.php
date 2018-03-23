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

/**
 * Create your routes in here. The name is the lowercase name of the controller
 * without the controller part, the stuff after the hash is the method.
 * e.g. page#index -> OCA\LotsOfUsers\Controller\PageController->index()
 *
 * The controller class has to be registered in the application.php file since
 * it's instantiated in there
 */
return [
    'routes' => [
       ['verb' => 'GET',  'name' => 'page#users', 'url' => '/'],
       ['verb' => 'GET',  'name' => 'page#user', 'url' => '/users/{username}'],
       ['verb' => 'GET',  'name' => 'page#groups', 'url' => '/groups'],
       ['verb' => 'GET',  'name' => 'page#group', 'url' => '/groups/{groupname}'],
		['verb' => 'GET',  'name' => 'page#scan', 'url' => '/users/{uid}'],
       ['verb' => 'GET',  'name' => 'api#users', 'url' => '/api/v1/users/{login}'],
       ['verb' => 'GET',  'name' => 'api#groups', 'url' => '/api/v1/groups/{gid}'],
       ['verb' => 'GET',  'name' => 'api#user', 'url' => '/api/v1/user/{login}'],
       ['verb' => 'POST', 'name' => 'api#userCreate', 'url' => '/api/v1/user'],
       ['verb' => 'GET',  'name' => 'api#diskUsage', 'url' => '/api/v1/diskusage/{uid}'],
		['verb' => 'GET',  'name' => 'api#scan', 'url' => '/api/v1/scan/{uid}'],

    ]
];

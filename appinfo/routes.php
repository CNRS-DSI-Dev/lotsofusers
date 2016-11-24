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
       ['name' => 'search#users', 'url' => '/', 'verb' => 'GET'],
       ['name' => 'search#do_echo', 'url' => '/echo', 'verb' => 'POST'],
       ['name' => 'api#users', 'url' => '/api/v1/users/{login}', 'verb' => 'GET'],
       ['name' => 'api#groups', 'url' => '/api/v1/groups/{gid}', 'verb' => 'GET'],
       ['name' => 'api#user', 'url' => '/api/v1/user/{login}', 'verb' => 'GET'],
    ]
];

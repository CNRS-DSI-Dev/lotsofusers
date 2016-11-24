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

namespace OCA\LotsOfUsers\Controller;

use OCP\IRequest;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\AppFramework\Controller;

class ApiController extends Controller {
    const MAX_RESULTS = 15;

    private $userId;
    private $userManager;
    private $groupManager;

    public function __construct($AppName, IRequest $request, $UserId, \OCP\IUserManager $userManager, \OCP\IGroupManager $groupManager){
        parent::__construct($AppName, $request);
        $this->userId = $UserId;
        $this->userManager = $userManager;
        $this->groupManager = $groupManager;
    }

    /**
     * @return string JSON-ified list of users with groups
     * @NoCSRFRequired
     */
    public function users($login) {
        $usersList = $this->userManager->search($login);

        $params = [];
        $params['users'] = [];
        $params['totalCount'] = count($usersList);

        if ($params['totalCount'] > 0) {
            $slicedList = array_slice($usersList, 0, self::MAX_RESULTS);
            foreach ($slicedList as $user) {
                $groups = $this->groupManager->getUserGroupIds($user);

                $params['users'][] = [
                    'login' => $user->getUID(),
                    'groups' => $groups,
                ];
            }
        }

        return new JSONResponse($params);  // templates/main.php
    }

    /**
     * @return string JSON-ified list of groups, without custom groups
     * @NoCSRFRequired
     */
    public function groups($gid) {
        $groupsList = $this->groupManager->search($gid);

        $params = [];
        $params['groups'] = [];
        $params['totalCount'] = count($groupsList);

        if ($params['totalCount'] > 0) {
            $slicedList = array_slice($groupsList, 0, self::MAX_RESULTS);
            foreach ($slicedList as $group) {
                $params['groups'][] = [
                    'gid' => $group->getGID(),
                ];
            }
        }

        return new JSONResponse($params);  // templates/main.php
    }

}

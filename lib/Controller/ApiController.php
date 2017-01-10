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

class ApiController extends Controller
{
    const MAX_RESULTS = 15;

    private $userId;
    private $userManager;
    private $groupManager;

    public function __construct($AppName, IRequest $request, $UserId, \OCP\IUserManager $userManager, \OCP\IGroupManager $groupManager)
    {
        parent::__construct($AppName, $request);
        $this->userId = $UserId;
        $this->userManager = $userManager;
        $this->groupManager = $groupManager;
    }

    /**
     * @return string JSON-ified list of users with groups
     * @NoAdminRequired
     */
    public function users($login)
    {
        \OC_Util::checkSubAdminUser();

        $isAdmin = $this->groupManager->isAdmin($this->userId);

        $currentUser = $this->userManager->get($this->userId);
        $isSubAdmin = $this->groupManager->getSubAdmin()->isSubAdmin($currentUser);

        if (!$isSubAdmin) {
            return new DataResponse(
                [
                    'status' => 'error',
                    'data' => [
                        'message' => (string)$this->l10n->t('Authentication error')
                    ]
                ],
                Http::STATUS_FORBIDDEN
            );
        }

        $usersList = [];
        if ($isAdmin) {
            $usersList = $this->userManager->search($login);
        }
        else {
            $subAdminOfGroups = $this->groupManager->getSubAdmin()->getSubAdminsGroups($currentUser);
            foreach ($subAdminOfGroups as $group) {
                $groupUsers = $group->searchUsers($login);
                $igroupUsers = [];
                foreach($groupUsers as $user) {
                    $igroupUsers[$user->getUID()] = $user;
                }
                $usersList = array_merge($usersList, $igroupUsers);
            }
        }

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
     * @NoAdminRequired
     */
    public function groups($gid)
    {
        \OC_Util::checkSubAdminUser();

        $isAdmin = $this->groupManager->isAdmin($this->userId);

        $currentUser = $this->userManager->get($this->userId);
        $isSubAdmin = $this->groupManager->getSubAdmin()->isSubAdmin($currentUser);

        if (!$isSubAdmin) {
            return new DataResponse(
                [
                    'status' => 'error',
                    'data' => [
                        'message' => (string)$this->l10n->t('Authentication error')
                    ]
                ],
                Http::STATUS_FORBIDDEN
            );
        }

        $groupsList = [];
        if ($isAdmin) {
            $groupsList = $this->groupManager->search($gid);
        }
        else {
            $subAdminsGroups = $this->groupManager->getSubAdmin()->getSubAdminsGroups($currentUser);
            foreach($subAdminsGroups as $group) {
                if (strpos($group->getGID(), $gid) !== false) {
                    $groupsList[$group->getGID()] = $group;
                }
            }
        }

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

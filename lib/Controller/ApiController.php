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
use OCA\LotsOfUsers\Helper;
use OCA\LotsOfUsers\Service\UserService;

class ApiController extends Controller
{
    const MAX_RESULTS = 15;

    private $userId;
    private $userManager;
    private $groupManager;
    private $helper;
    private $userService;

    public function __construct($AppName, IRequest $request, $UserId, \OCP\IUserManager $userManager, \OCP\IGroupManager $groupManager, Helper $helper, UserService $userService)
    {
        parent::__construct($AppName, $request);
        $this->userId = $UserId;
        $this->userManager = $userManager;
        $this->groupManager = $groupManager;
        $this->helper = $helper;
        $this->userService = $userService;
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
     * @NoCSRFRequired
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

        $filter = Helper::getLotsOfGroupsFilter();
        if (!empty($groupsList) and !empty($filter)) {
            foreach($groupsList as $key => $group) {
                if (strpos($group->getGID(), $filter) !== false) {
                    unset($groupsList[$key]);
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
                    'usersCount' => $group->count(),
                ];
            }
        }

        return new JSONResponse($params);  // templates/main.php
    }

    /**
     * Returns disk usage for a user
     * @NoAdminRequired
     * @param  string $uid User id
     * @return json
     */
    public function diskUsage($uid)
    {
        \OC_Util::checkSubAdminUser();

        if (is_null($uid)) {
            $response = new JSONResponse();
            return array(
                'status' => 'error',
                'data' => array(
                    'msg' => 'No uid given',
                ),
            );
        }

        $user = $this->userManager->get($uid);
        $currentUser = $this->userManager->get($this->userId);

        $isAdmin = $this->groupManager->isAdmin($this->userId);

        if (!$isAdmin and !$this->groupManager->getSubAdmin()->isUserAccessible($currentUser, $user)) {
            return array(
                'status' => 'error',
                'data' => array(
                    'msg' => 'Authentication error',
                ),
            );
        }

        $diskUsage = $this->helper->diskUsage($uid)['size'];
        return new JSONResponse($diskUsage);
    }

    /**
     * Returns list of users
     * @NoAdminRequired
     * @return json
     */
    function search() {
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

        $quotaMin = \OCP\Util::computerFileSize($this->params('quotaMin', 0));
        $uidContains = $this->params('userId', '');
        $gidContains = $this->params('groupId', '');
        $lastConnectionFrom = $this->params('lastConnectionFrom', '');
        $lastConnectionTo = $this->params('lastConnectionTo', '');

        $params = [
            'quotaMin' => $quotaMin,
            'uidContains' => $uidContains,
            'gidContains' => $gidContains,
            'lastConnectionFrom' => $lastConnectionFrom,
            'lastConnectionTo' => $lastConnectionTo
        ];

        // TODO: comment gérer le critère "quota"
        $userIds = $this->userService->users($params, $this->userId, $isSubAdmin);
        // TODO: limiter le nb de résultats, indiquer le nb de résultats total
        // TODO: proposer un export CSV complet (tous les résultats)

        $usersList = [
            'users' => $userIds,
        ];

        return new JSONResponse($usersList);
    }
}

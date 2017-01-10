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

use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\RedirectResponse;
use OCP\AppFramework\Controller;
use OCP\IRequest;
use OCP\IL10N;
use OCP\IURLGenerator;
use OCA\LotsOfUsers\Helper;

class PageController extends Controller
{


    private $userId;
    private $userManager;
    private $groupManager;
    private $subAdmin;
    private $helper;
    private $l10n;
    private $urlGenerator;

    public function __construct($AppName, IRequest $request, $UserId, \OCP\IUserManager $userManager, \OCP\IGroupManager $groupManager, \OCA\LotsOfUsers\Helper $helper, IL10N $l10n, IURLGenerator $urlGenerator)
    {
        parent::__construct($AppName, $request);
        $this->userId = $UserId;
        $this->userManager = $userManager;
        $this->groupManager = $groupManager;
        $this->subAdmin = $groupManager->getSubAdmin();
        $this->helper = $helper;
        $this->l10n = $l10n;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * Display search and create user page
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function users()
    {
        \OC_Util::checkSubAdminUser();

        $params = [];
        return new TemplateResponse('lotsofusers', 'users', $params);
    }

    /**
     * Display a user details page
     * @param  [type] $username [description]
     * @return [type]           [description]
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function user($username)
    {
        \OC_Util::checkSubAdminUser();

        $user = $this->userManager->get($username);
        $currentUser = $this->userManager->get($this->userId);
        $isAdmin = $this->groupManager->isAdmin($this->userId);

        if (!$isAdmin and !$this->groupManager->getSubAdmin()->isUserAccessible($currentUser, $user)) {
            return new RedirectResponse($this->urlGenerator->linkTo('lotsofusers', ''));
        }

        // FIXME if $username null, return

        // $storageInfo = \OC_Helper::getStorageInfo($username);
        // $fs = new \OC\Files\Filesystem;
        // $fs::init($username);
        // $dirInfo = $fs::getFileInfo('/', false);
        // $infos = \OC_Helper::getStorageInfo('/', $dirInfo);

        $diskUsage = $this->helper->diskUsage($username);
        $quota = \OCP\Util::computerFileSize($user->getQuota());
        $quotabar = $diskUsage['size'] * 100 / $quota;
        $groups = $this->groupManager->getUserGroupIds($user);
        $groupsAdmin = $this->subAdmin->getSubAdminsGroups($user);
        if (!empty($groupsAdmin)) {
            $groupsAdmin = array_map(function($item) {return $item->getGID();}, $groupsAdmin);
        }

        $params = [
            'username' => $username,
            'displayname' => $user->getDisplayName(),
            'language' => mb_strtoupper(\OCP\Config::getUserValue($username, 'core', 'lang', \OCP\Config::getSystemValue('default_language', 'fr'))),
            'lastlogin' => ($user->getlastLogin() != 0) ? date('d/m/Y H:i:s', $user->getlastLogin()) : $this->l10n->t('never'),
            'quota' => $user->getQuota(),
            'diskusage' => \OCP\Util::humanFileSize($diskUsage['size']),
            'quotabar' => $quotabar . '%',
            'groups' => $groups,
            'groupsAdmin' => $groupsAdmin,
        ];
        return new TemplateResponse('lotsofusers', 'user', $params);
    }

    /**
     * Display search group page
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function groups()
    {
        \OC_Util::checkSubAdminUser();

        $params = [];
        return new TemplateResponse('lotsofusers', 'groups', $params);
    }

    /**
     * Display search group page
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function group($groupname)
    {
        \OC_Util::checkSubAdminUser();

        $params = [
            'groupname' => $groupname
        ];
        return new TemplateResponse('lotsofusers', 'group', $params);
    }
}

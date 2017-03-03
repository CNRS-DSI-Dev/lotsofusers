<?php

/**
 * ownCloud - LotsOfGroups
 *
 * @author Patrick Paysant <ppaysant@linagora.com>
 * @copyright 2014 CNRS DSI
 * @license This file is licensed under the Affero General Public License version 3 or later. See the COPYING file.
 */

namespace OCA\LotsOfUsers\Service;

class UserService
{

    protected $userManager;
    protected $userSession;

    public function __construct(\OCP\IUserManager $userManager, \OCP\IUserSession $userSession)
    {
        $this->userManager = $userManager;
        $this->userSession = $userSession;
    }

    // TEST VERSION, DO NOT USE AS IS !!!
    /**
     * Returns a list of admin and normal groups
     * @param array $param  Array of search criteria
     * @param int $currentUserId
     * @param boolean $iSubAdmin
     * @param string $limit Max nb of results
     * @return array
     */
    public function users($params, $currentUserId, $isSubAdmin, $limit=null)
    {
        $users = [];
        $clause = '';
        $parameters = [];

        // $quotaMin = $params['quotaMin'] ?? 0;
        // if (!empty($quotaMin)) {
        //     $clause = '';
        //     $parameters[':quotaMin'] = $quotaMin;
        // }
        $uidContains = $params['uidContains'] ?? '';
        if (!empty($uidContains)) {
            $clause .= ' AND oc_users.uid LIKE :uidContains ';
            $parameters[':uidContains'] = '%' . $uidContains . '%';
        }

        $gidContains = $params['gidContains'] ?? '';
        if (!empty($gidContains)) {
            $clause .= ' AND oc_group_user.gid LIKE :gidContains ';
            $parameters[':gidContains'] = '%' . $gidContains . '%';
        }

        $lastConnectionFrom = $params['lastConnectionFrom'] ?? '';
        if (!empty($lastConnectionFrom)) {
            $clause .= ' AND ocp1.configvalue > :lastConnectionFrom ';
            $parameters[':lastConnectionFrom'] = $lastConnectionFrom;
        }

        $lastConnectionTo = $params['lastConnectionTo'] ?? '';
        if (!empty($lastConnectionTo)) {
            $clause .= ' AND ocp1.configvalue < :lastConnectionTo ';
            $parameters[':lastConnectionTo'] = $lastConnectionTo;
        }

        $subAdminClause = '';
        if ($isSubAdmin) {
            $subAdminClause = ' AND ocg2.gid IN (SELECT gid FROM oc_group_admin WHERE uid = :currentUserId) ';
            $parameters[':currentUserId'] = $currentUserId;
        }

        // select oc_users.uid, ocp1.configvalue as lastConnection, ocp2.configvalue as quota, group_concat(distinct oc_group_user.gid separator ', ') as groupIds  from oc_users left join oc_preferences as ocp1 on ocp1.userid = oc_users.uid and ocp1.configkey = "lastLogin" left join oc_preferences as ocp2 on ocp2.userid = oc_users.uid and ocp2.configkey = 'quota' left join oc_group_user on oc_group_user.uid = oc_users.uid where ocp1.userid like "%lus%" group by uid;
        //
        // select oc_users.uid, ocp1.configvalue as lastConnection, ocp2.configvalue as quota, group_concat(distinct oc_group_user.gid separator ', ') as groupIds  from oc_users left join oc_preferences as ocp1 on ocp1.userid = oc_users.uid and ocp1.configkey = "lastLogin" left join oc_preferences as ocp2 on ocp2.userid = oc_users.uid and ocp2.configkey = 'quota' left join oc_group_user on oc_group_user.uid = oc_users.uid left join oc_group_user as ocg2 on ocg2.uid = oc_users.uid where ocp1.userid like "%lus%" and ocg2.gid in (select gid from oc_group_admin where uid = 'luser1') group by uid;
        $sql = 'SELECT oc_users.uid,
            ocp1.configvalue AS lastConnection,
            ocp2.configvalue AS quota,
            GROUP_CONCAT(distinct oc_group_user.gid separator ", ") AS groupIds
            FROM oc_users
            LEFT JOIN oc_preferences AS ocp1 ON ocp1.userid = oc_users.uid AND ocp1.configkey = "lastLogin"
            LEFT JOIN oc_preferences AS ocp2 ON ocp1.userid = oc_users.uid AND ocp2.configkey = "quota"
            LEFT JOIN oc_group_user ON oc_group_user.uid = oc_users.uid
            LEFT JOIN oc_group_user AS ocg2 ON ocg2.uid = oc_users.uid
            WHERE 1 = 1 ' . $clause . $subAdminClause . '
            GROUP BY uid
        ';
        $query = \OC_DB::prepare($sql, $limit);
        $result = $query->execute($parameters);
        while ($row = $result->fetchRow()) {
            $users[$row['uid']] = $row;

            $groupIds = explode(', ', $row['groupIds']);
            $users[$row['uid']]['groupIds'] = $groupIds;
        }

        return $users;
    }
}

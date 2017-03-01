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
     * @param string $limit Max nb of results
     * @return array
     */
    public function users($params, $limit=null)
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

        $lastConnectionAfter = $params['lastConnectionAfter'] ?? '';
        if (!empty($lastConnectionAfter)) {
            $clause .= ' AND ocp1.configvalue > :lastConnectionAfter ';
            $parameters[':lastConnectionAfter'] = $lastConnectionAfter;
        }

        // select oc_users.uid, ocp1.configvalue as lastConnection, ocp2.configvalue as quota, group_concat(distinct oc_group_user.gid separator ', ') as groups  from oc_users left join oc_preferences as ocp1 on ocp1.userid = oc_users.uid and ocp1.configkey = "lastLogin" left join oc_preferences as ocp2 on ocp2.userid = oc_users.uid and ocp2.configkey = 'quota' left join oc_group_user on oc_group_user.uid = oc_users.uid where ocp1.userid like "%lus%" group by uid;
        $sql = 'SELECT oc_users.uid,
            ocp1.configvalue AS lastConnection,
            ocp2.configvalue AS quota,
            GROUP_CONCAT(distinct oc_group_user.gid separator ", ") AS groupIds
            FROM oc_users
            LEFT JOIN oc_preferences AS ocp1 ON ocp1.userid = oc_users.uid AND ocp1.configkey = "lastLogin"
            LEFT JOIN oc_preferences AS ocp2 ON ocp1.userid = oc_users.uid AND ocp2.configkey = "quota"
            LEFT JOIN oc_group_user ON oc_group_user.uid = oc_users.uid
            WHERE 1 = 1 ' . $clause . '
            GROUP BY uid
        ';
        $query = \OC_DB::prepare($sql, $limit);
        $result = $query->execute($parameters);
        while ($row = $result->fetchRow()) {
            $users[$row['uid']] = $row;
        }

        return $users;
    }
}

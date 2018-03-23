<?php
// db/authordao.php

namespace OCA\LotsOfUsers;

use OCP\IDBConnection;

class Helper {

    private $db;

    public function __construct(IDBConnection $db) {
        $this->db = $db;
    }

    /**
     * Returns disk space used by a user
     * @param  string $username (userId)
     * @return array ['user_id', 'size']
     */
    public function diskUsage($username) {
        $sql = "SELECT m.user_id, fc.size
            FROM oc_mounts m, oc_filecache fc, oc_storages s
            WHERE m.mount_point = CONCAT('/', :username, '/')
                AND s.numeric_id = m.storage_id
                AND fc.storage = m.storage_id
                AND fc.path = 'files'";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':username' => $username,
        ]);

        $row = $stmt->fetch();

        return $row;
    }

    /**
     * Verify if lotsofgroups filter is enabled (see general settings screen, "Lots of Groups" section)
     * @return boolean
     */
    public static function isLotsOfGroupsFilterEnabled()
    {
        $appConfig = \OC::$server->getAppConfig();
        $result = $appConfig->getValue('lotsofgroups', 'lotsofgroups_filter_enabled', 'no');
        return ($result === 'yes') ? true : false;
    }

    /**
     * Get the lotsofgroups filter
     * @return string
     */
    public static function getLotsOfGroupsFilter()
    {
        $result = '';

        if (self::isLotsOfGroupsFilterEnabled()) {
            $appConfig = \OC::$server->getAppConfig();
            $result = $appConfig->getValue('lotsofgroups', 'lotsofgroups_filter', 'GC_');

        }

        return $result;
    }



	/**
	 * Returns scan for a user
	 * @NoAdminRequired
	 * @param  string $uid User id
	 * @return json
	 */
    	public function scan($uid){
		$scan = null;
		if(!empty($uid)){
			$scan = shell_exec("php occ files:scan ".$uid);
			if(preg_match("/\|\s*\d+\s*\|\s*\d+\s*\|\s*\d+\:\d+\:\d+\s*\|/i", $scan)){
           	 	    $scan =  array(
           	 	        'status' => 'success',
           	 	        'data' => array(
           	 	            'msg' => $scan,
           	 	        ),
           	 	    );
			}
			else{
           	 	    $scan =  array(
           	 	        'status' => 'error',
           	 	        'data' => array(
           	 	            'msg' => $scan,
           	 	        ),
           	 	    );
			}
		}
		else{
           	    $scan =  array(
           	        'status' => 'error',
           	        'data' => array(
           	            'msg' => "userid not correct",
           	        ),
           	    );
		}
		return $scan;
	}


}

<?php
// db/authordao.php

namespace OCA\LotsOfUsers;

use OCP\IDBConnection;

class Helper {

    private $db;

    public function __construct(IDBConnection $db) {
        $this->db = $db;
    }

    public function diskUsage($username) {
        $sql = "SELECT m.user_id, fc.size
            FROM oc_mounts m, oc_filecache fc, oc_storages s
            WHERE m.mount_point = CONCAT('/', :username, '/')
                AND s.numeric_id = m.storage_id
                AND fc.storage = m.storage_id
                AND fc.name = 'files'";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':username' => $username,
        ]);

        $row = $stmt->fetch();

        return $row;
    }

}

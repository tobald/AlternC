<?php

/**
 * Account Api of AlternC, used by alternc-api package
 */
class Alternc_Api_Object_Mysql extends Alternc_Api_Legacyobject {

    protected $mysql;

    function __construct($service) {
        global $mysql;
        parent::__construct($service);
        $this->mysql = $mysql;
    }

    /** API Method from legacy class method mysql->add_db)
     * @param $options a hash with parameters transmitted to legacy call
     * mandatory parameters: dbn
     * non-mandatory: passwd
     * @return Alternc_Api_Response whose content is the newly created UID
     */
    function add($options) {
        global $cuid, $mem;
        if ($this->isAdmin) {
            if (isset($options["uid"])) {
                $cuid = intval($options["uid"]);
                $mem->su($cuid);
            }
        }

        $mandatory = array("dbn");
        $defaults = array("psswd" => "");
        $missing = "";
        foreach ($mandatory as $key) {
            if (!isset($options[$key])) {
                $missing.=$key . " ";
            }
        }
        if ($missing) {
            return new Alternc_Api_Response(array("code" => self::ERR_INVALID_ARGUMENT, "message" => "Missing or invalid argument: " . $missing));
        }

        foreach ($defaults as $key => $value) {
            if (!isset($options[$key])) {
                $options[$key] = $value;
            }
        }

        $uid = $this->mysql->add_db($options["dbn"], $options["psswd"]);
        if (!$uid) {
            return $this->alterncLegacyErrorManager();
        } else {
            return new Alternc_Api_Response(array("content" => $uid));
        }
    }

    /** API Method from legacy class method admin->update_mem()
     * @param $options a hash with parameters transmitted to legacy call
     * mandatory parameters: nom, prenom, mail, 
     * non-mandatory: pass, canpass, type, duration, notes, force, create_dom, db_server_id
     * @return Alternc_Api_Response whose content is the updated UID
     */
    function update($options) {
        $defaults = array("nom", "prenom", "mail", "canpass", "enabled", "type", "duration", "notes");
        if (!isset($options["uid"])) {
            return new Alternc_Api_Response(array("code" => self::ERR_INVALID_ARGUMENT, "message" => "Missing or invalid argument: UID"));
        }
        $uid = intval($options["uid"]);
        $old = $this->admin->get($uid);
        if (!$old) {
            return new Alternc_Api_Response(array("code" => self::ERR_NOT_FOUND, "message" => "User not found"));
        }

        foreach ($defaults as $key) {
            if (!isset($options[$key])) {
                $options[$key] = $old[$key];
            }
        }
        if (!isset($options["pass"]))
            $options["pass"] = "";
        $uid = $this->admin->update_mem($uid, $options["mail"], $options["nom"], $options["prenom"], $options["pass"], $options["enabled"], $options["canpass"], $options["type"], $options["duration"], $options["notes"]);
        if (!$uid) {
            return $this->alterncLegacyErrorManager();
        } else {
            return new Alternc_Api_Response(array("content" => $uid));
        }
    }

    /** API Method from legacy class method admin->del_mem()
     * @param $options a hash with parameters transmitted to legacy call
     * mandatory parameters: uid
     * @return Alternc_Api_Response TRUE if the account has been deleted.
     */
    function del($options) {
        if (!isset($options["uid"])) {
            return new Alternc_Api_Response(array("code" => self::ERR_ALTERNC_FUNCTION, "message" => "Missing or invalid argument: UID"));
        }
        $result = $this->admin->del_mem(intval($options["uid"]));
        if (!$result) {
            return $this->alterncLegacyErrorManager();
        } else {
            return new Alternc_Api_Response(array("content" => true));
        }
    }

    /** API Method from legacy class method admin->lock_mem()
     * @param $options a hash with parameters transmitted to legacy call
     * mandatory parameters: uid
     * @return Alternc_Api_Response TRUE if the account has been locked
     */
    function lock($options) {
        if (!isset($options["uid"])) {
            return new Alternc_Api_Response(array("code" => self::ERR_ALTERNC_FUNCTION, "message" => "Missing or invalid argument: UID"));
        }
        $result = $this->admin->lock_mem(intval($options["uid"]));
        if (!$result) {
            return $this->alterncLegacyErrorManager();
        } else {
            return new Alternc_Api_Response(array("content" => true));
        }
    }

    /** API Method from legacy class method admin->unlock_mem()
     * @param $options a hash with parameters transmitted to legacy call
     * mandatory parameters: uid
     * @return Alternc_Api_Response TRUE if the account has been unlocked
     */
    function unlock($options) {
        if (!isset($options["uid"])) {
            return new Alternc_Api_Response(array("code" => self::ERR_ALTERNC_FUNCTION, "message" => "Missing or invalid argument: UID"));
        }
        $result = $this->admin->unlock_mem(intval($options["uid"]));
        if (!$result) {
            return $this->alterncLegacyErrorManager();
        } else {
            return new Alternc_Api_Response(array("content" => true));
        }
    }

    /** API Method from legacy class method admin->normal2su()
     * @param $options a hash with parameters transmitted to legacy call
     * mandatory parameters: uid
     * @return Alternc_Api_Response TRUE if the account has been set to be an administator 
     */
    function setAdmin($options) {
        if (!isset($options["uid"])) {
            return new Alternc_Api_Response(array("code" => self::ERR_ALTERNC_FUNCTION, "message" => "Missing or invalid argument: UID"));
        }
        $result = $this->admin->normal2su(intval($options["uid"]));
        if (!$result) {
            return $this->alterncLegacyErrorManager();
        } else {
            return new Alternc_Api_Response(array("content" => true));
        }
    }

    /** API Method from legacy class method admin->su2normal()
     * @param $options a hash with parameters transmitted to legacy call
     * mandatory parameters: uid
     * @return Alternc_Api_Response TRUE if the account has been set to NOT be an administrator
     */
    function unsetAdmin($options) {
        if (!isset($options["uid"])) {
            return new Alternc_Api_Response(array("code" => self::ERR_ALTERNC_FUNCTION, "message" => "Missing or invalid argument: UID"));
        }
        $result = $this->admin->su2normal(intval($options["uid"]));
        if (!$result) {
            return $this->alterncLegacyErrorManager();
        } else {
            return new Alternc_Api_Response(array("content" => true));
        }
    }

    /** API Method from legacy class method admin->get_list()
     * @param $options a hash with parameters transmitted to legacy call
     * non-mandatory parameters: ONE OF: 
     *  uid(strict), login(like %%), domain(like %%), creator(strict, by uid), 
     *  Any of: offset(int=0), count(int=+inf)
     * @return Alternc_Api_Response An array with all matching users informations as hashes
     */
    function find($options) {
        $result = false;
        if (!$result && isset($options["uid"])) {
            $result = $this->admin->get(intval($options["uid"]));
            if ($result)
                $result = array($result);
        }
        if (!$result && isset($options["login"])) {
            $result = $this->admin->get_list(1/* ALL */, "", $options["login"], "login");
        }
        if (!$result && isset($options["domain"])) {
            $result = $this->admin->get_list(1/* ALL */, "", $options["domain"], "domaine");
        }
        if (!$result && isset($options["creator"])) {
            $result = $this->admin->get_list(1/* ALL */, intval($options["creator"]));
        }
        if (!$result) {          // everybody
            $result = $this->admin->get_list(1/* ALL */, "");
        }

        if (!$result) {
            return $this->alterncLegacyErrorManager();
        } else {
            list($offset, $count) = $this->offsetAndCount($options, count($result));
            if ($offset != -1 || $count != -1) {
                $result = array_slice($result, $offset, $count);
            }
            return new Alternc_Api_Response(array("content" => $result));
        }
    }

}

// class Alternc_Api_Object_Mysql

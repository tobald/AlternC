<?php

/**
 * Domain Api of AlternC, used by alternc-api package
 */
class Alternc_Api_Object_Mailman extends Alternc_Api_Legacyobject {

    protected $mailman; // m_mailman instance

    function __construct($service) {
        global $mailman;
        parent::__construct($service);
        $this->mailman = $mailman;
    }

    /** API Method from legacy class method $mailman->add_lst
     * ($domain,$login,$owner,$password,$password2)
     * @param $options a hash with parameters transmitted to legacy call
     * mandatory parameters: $domain,$login,$owner,$password
     * non-mandatory: 
     * @return Alternc_Api_Response whose content is 
     */
    function add($options) {
        global $cuid, $mem, $oldid, $isinvited;
        if ($this->isAdmin) {
            if (isset($options["uid"])) {
                $cuid = intval($options["uid"]);
                $mem->su($cuid);
            }
        }
        $oldid = 2000;
        $isinvited = true;

        $mandatory = array("domain", "login", "owner", "passwd");
        $missing = "";
        foreach ($mandatory as $key) {
            if (!isset($options[$key])) {
                $missing.=$key . " ";
            }
        }
        if ($missing) {
            return new Alternc_Api_Response(array("code" => self::ERR_INVALID_ARGUMENT, "message" => "Missing or invalid argument: " . $missing));
        }
        $did = $this->mailman->add_lst($options["domain"], $options["login"], $options["owner"], $options["passwd"], $options["passwd"]);
        if (!$did) {
            return $this->alterncLegacyErrorManager();
        } else {
            return new Alternc_Api_Response(array("content" => $did));
        }
    }

}

// class Alternc_Api_Object_Mailman

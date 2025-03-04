<?php
/*
 ----------------------------------------------------------------------
 LICENSE

 This program is free software; you can redistribute it and/or
 modify it under the terms of the GNU General Public License (GPL)
 as published by the Free Software Foundation; either version 2
 of the License, or (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 To read the license please visit http://www.gnu.org/copyleft/gpl.html
 ----------------------------------------------------------------------
*/

/**
 * Edit a subdomain parameters 
 *
 * @copyright AlternC-Team 2000-2017 https://alternc.com/ 
 */

require_once("../class/config.php");

$fields = array (
	"domain"    => array ("post", "string", ""),
	"sub"       => array ("post", "string", ""),
	"type"      => array ("post", "string", $dom->type_local),
  	"sub_domain_id" => array ("post", "integer", 0),
	"vhost_type" => array ("post", "string", ""),
	"https_option" => array ("post", "string", ""),
);
getFields($fields);

// here we get a dynamic-named value
$dynamicvar="t_$type";
$fields = array (
  "$dynamicvar"   => array ("post", "string", ""),
);
getFields($fields);
$value=$$dynamicvar;
// The dynamic value is now in $value

$dom->lock();

$dt=$dom->domains_type_lst();
if ( (!isset($isinvited) || !$isinvited) && $dt[strtolower($type)]["enable"] != "ALL" ) {
  $msg->raise("ERROR", "dom", _("This page is restricted to authorized staff"));
  include("dom_edit.php");
  exit();
}

if ($type == "vhost" && isset($vhost_type)) $type = $vhost_type;
if (empty($sub_domain_id)) $sub_domain_id=null;
$r=$dom->set_sub_domain($domain, $sub, $type, $value, $sub_domain_id, $https_option);

$dom->unlock();

if (!$r) {
  if ($sub_domain_id!=0) {
    $noread=true;
    include("dom_subedit.php"); 
  } else {
    // it was a creation, not an edit
    include("dom_edit.php");
  }
    exit();
} else {
  $t = time();
  // TODO: we assume the cron job is at every 5 minutes
  $noread=false;
  $msg->raise("ALERT", "dom", _("The certificate will be created in the next hours. The propagation of the new domain name requires a bit of time."));
  foreach($fields as $k=>$v) unset($$k);
}
include("dom_edit.php");
exit;

?>

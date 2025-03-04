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
 * Manages default domain types, 
 * they are the subdomains automatically installed on a domain when you host it.
 * AlternC's modules such as roundcube may create new ones, you'll see them here
 *
 * @copyright AlternC-Team 2000-2017 https://alternc.com/ 
 */

require_once("../class/config.php");

if (!$admin->enabled) {
    $msg->raise("ERROR", "admin", _("This page is restricted to authorized staff"));
    echo $msg->msg_html_all();
    exit();
}

include_once("head.php");
?>
<h3><?php __("Manage defaults domains type"); ?></h3>
<hr id="topbar" />
<p><?php __("If you don't know what this page is about, don't touch anything, and read AlternC documentation about domain types"); ?></p>
<p><?php __("The Type column contains a type of available VirtualHost config on The server."); ?></p>
<p><?php __("The Setting column contains the variables to be expanded in the defaults configuration. Available values are:  "); ?></p>
<ul>
    <li><?php __("%%DOMAIN%% : the Domain name"); ?></li>
    <li><?php __("%%TARGETDOM%%: The destination domain"); ?></li>
    <li><?php __("%%SUB%% : The subdomain name"); ?></li>
    <li><?php __("%%DOMAINDIR%%: the domain directory on the file system"); ?></li>
</ul>

<br />
<?php
$fields = array(
    "domup" => array("post", "array", ""),
);
getFields($fields);

if (!empty($domup)) {
    if (!$dom->update_default_subdomains($domup)) {
        $msg->raise("ERROR", "admin", _("There was an error during the record."));
    } else {
        $msg->raise("INFO", "admin", _("Save done."));
    }
}
echo $msg->msg_html_all();

$tab = $dom->lst_default_subdomains();
?>
<form method="post" action="adm_doms_def_type.php" name="main" id="main">
  <?php csrf_get(); ?>
    <table class="tlist">
        <tr><th>&nbsp;</th><th><?php __("Sub"); ?></th><th><?php __("Type"); ?></th><th><?php __("settings"); ?></th><th><?php __("Concerned"); ?></th><th><?php __("Activation"); ?></th></tr>
        <?php for ($i = 0; $i < count($tab) + 1; $i++) { ?>
            <tr  class="lst">
                <td>
                    <?php
                    @$val = $tab[$i];
                    if (!isset($val)) $val = ['id'=>'', 'domain_type'=>'', 'concerned'=>'', 'enabled'=>'', 'sub'=>'', 'domain_type_parameter'=>'']; // fast hack to prevent undefined index errors
                    if (isset($tab[$i])) {
                        echo "<input type='hidden' name='domup[$i][id]' value='" . $val['id'] . "' />";
                    }
                    ?>
                    <div class="ina delete"><a href="dom_defdel.php?id=<?php echo $val['id']; ?>"><?php __("Delete"); ?></a></div>
                </td>

                <td><input type='text' size="16" name='domup[<?php echo $i; ?>][sub]' value="<?php echo $val['sub']; ?>"/></td>
                <?php
                $available_types = $dom->domain_types_available_for_defaults();
                if (in_array($val['domain_type'], $available_types) || !isset($val)) {
                    printf('<td><select name="domup[%s][domain_type]">', $i);
                    foreach ($available_types as $t) {
                      $selected = ($t == $val['domain_type']) ? "selected" : "";
                      printf('<option value="%s" %s>%s</option>', $t, $selected, $t);
                    }
                    print("</select></td>");
                } else { ?>
                    <td><input type="text" style="width:100px" name="domup[<?php echo $i; ?>][domain_type]" value="<?php echo $val['domain_type'] ?>" ></td>
                <?php } ?>
                <td><input type ='text' name='domup[<?php echo $i; ?>][domain_type_parameter]' value='<?php echo $val['domain_type_parameter'] ?>' /></td>
                <td><select name='domup[<?php echo $i; ?>][concerned]'>
                        <option value='MAIN' <?php if ($val['concerned'] == 'MAIN') echo "selected=\"selected\""; ?> >MAIN</option>
                        <option value='SLAVE' <?php if ($val['concerned'] == 'SLAVE') echo "selected=\"selected\""; ?> >SLAVE</option>
                        <option value='BOTH' <?php if ($val['concerned'] == 'BOTH') echo "selected=\"selected\""; ?> >BOTH</option>
                    </select>
                </td>
                <td><input type="checkbox" name="domup[<?php echo $i; ?>][enabled]" value="1" <?php if ($val['enabled'] == 1) echo "checked=\"checked\""; ?> /></td>
            </tr>
            <?php
        }
        ?>
        <tr>
            <td colspan='6'><p><input type="submit" class="inb" name="submit" value="<?php __("Save"); ?>" /></p></td>
        </tr>
    </table>
</form>
<?php include_once("foot.php"); 


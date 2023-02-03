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
 * Form to edit / add subdomains, 
 * using domaine_type table to show a synamic form.
 * 
 * @copyright AlternC-Team 2000-2017 https://alternc.com/ 
 */

require_once("../class/config.php");
include_once("head.php");

function sub_domains_edit($domain, $sub_domain_id=false) {
  global $admin, $msg, $oldid, $isedit;

$dom=new m_dom();
$dom->lock();

$r=$dom->get_domain_all($domain);
/*
if (! empty($sub)) {
   $sd=$dom->get_sub_domain_all($domain,$sub,$type,$value);
}
*/
$sd=$dom->get_sub_domain_all($sub_domain_id);

if (is_array($sd)) {
    $type=$sd['type'];
    $sub=$sd['name'];
}

$dom->unlock();

?>

<form action="dom_subdoedit.php" method="post" name="main" id="main">
   <?php csrf_get(); ?>
    <table class="dom-edit-table">
        <tr>
            <td>
            <input type="hidden" name="domain" value="<?php ehe($domain) ?>" />
            <input type="hidden" name="sub_domain_id" value="<?php echo intval($sub_domain_id); ?>" />
            <input type="hidden" name="action" value="add" />
  <?php
   if (!$isedit)
     __("Create a subdomain:"); 
?></td><td>
   <input type="text" class="int" name="sub" style="text-align:right" value="<?php if (isset($sub)) ehe($sub); ?>" size="22" id="sub" /><span id="newsubname">.<?php ehe($domain); ?></span></td>
   <td></td>
        </tr>
  <?php
   $domain_types = $dom->domain_types($sd);
   $vhost_types = array_filter($domain_types, function($i) {return $i['target'] == 'DIRECTORY';});
   if (is_array($sd) && in_array($sd['type'], array_map(function($t) {return $t['name'];}, $vhost_types))) {
       $checked = true;
       $input_value = $sd['dest'];
       $https_option = $sd['https'];
       $vhost_type_option = $sd['type'];
   } else {
       $checked = '';
       $input_value = '';
       $https_option = '';
       $vhost_type_option = strtolower($dom->default_vhost_type());
   }
   if (!empty($vhost_types)) {?>
        <tr>
          <td>
          <input type="radio" id="r_vhost" class="inc" name="type" value="vhost" <?php if ($checked) echo 'checked="checked"'; ?> OnClick="getElementById('t_vhost').focus();"/>
          <label for="r_vhost"><?php __('Locally hosted'); ?></label>
          </td>
          <td>
          <input type="text" class="int" name="t_vhost" id="t_vhost" value="<?= $input_value ?>" size="28" onKeyPress="getElementById('r_vhost').checked=true;" />
          <?php display_browser($sd['dest'], "t_vhost" ); ?> &nbsp;
          <!-- <label for="vhost_type"><?php __('Type'); ?></label> -->
          <select class="inl" name="vhost_type" id="vhost_type">
            <?php foreach ($vhost_types as $vt) { ?>
            <option value="<?= $vt['name']; ?>" <?php selected($vt['name']==$vhost_type_option); ?>><?= $vt['display_name']; ?></option>
            <?php } ?>
          </select>
          </td>
          <td>
          <select class="inl" name="https_option" id="https_option">
              <option value="https"<?php selected($https_option=="https"); ?>><?php __("HTTPS (with HTTP redirect)"); ?></option>
              <option value="http"<?php selected($https_option=="http"); ?>><?php __("HTTP (with HTTPS redirect)"); ?></option>
              <option value="both"<?php selected($https_option="both"); ?>><?php __("Both HTTP and HTTPS"); ?></option>
          </select>
          </td>
        </tr>
    <?php
    }
      $first_advanced=true;
      $lst_advanced=array();
      $other_dom_types = array_filter($domain_types, function($i) {return $i['target'] != 'DIRECTORY';});
      foreach($other_dom_types as $dt) {

        if ( (! $r['dns'] ) and ($dt['need_dns']) ) continue;
        $targval=(strtoupper($type)==strtoupper($dt['name']))?$sd['dest']:'';

        if ($dt['advanced']) {
          $lst_advanced[]=$dt['name'];
          if ($first_advanced) {
            $first_advanced=false;
            echo "<tr id='domtype_show' onClick=\"domtype_advanced_show();\"><td colspan='2'><br/><a href=\"javascript:domtype_advanced_show();\"><b>+ "; __("Show advanced options"); echo "</b></a></td></tr>";
            echo "<tr id='domtype_hide' onClick=\"domtype_advanced_hide();\" style='display:none'><td colspan='2'><br/><a href=\"javascript:domtype_advanced_hide();\"><b>- "; __("Hide advanced options"); echo "</b></a></td></tr>";
          }
        }
    ?>
    <tr id="tr_<?php echo $dt['name']; ?>">
      <td>
        <input type="radio" id="r_<?php ehe($dt['name']); ?>" class="inc" name="type" value="<?php ehe($dt['name']); ?>" <?php cbox(strtoupper($type)==strtoupper($dt['name'])); ?> OnClick="getElementById('t_<?php ehe($dt['name']); ?>').focus();"/>
        <label for="r_<?php ehe($dt['name']); ?>"><?php __($dt['description']); ?></label>
      </td>
      <td>
        <?php 

        switch ($dt['target']) {
          case "URL": ?>
              <input type="text" class="int" name="t_<?php ehe($dt['name']); ?>" id="t_<?php ehe($dt['name']); ?>" value="<?php ehe( (empty($targval)?'':$targval) ); ?>" placeholder="https://www.toto.org" size="50" onKeyPress="getElementById('r_<?php ehe($dt['name']); ?>').checked=true;" /><?php
              break;;
          case 'IP':?>
              <input type="text" class="int" name="t_<?php ehe($dt['name']); ?>" id="t_<?php ehe($dt['name']); ?>"  value="<?php ehe($targval); ?>" placeholder="1.2.3.4" size="16" onKeyPress="getElementById('r_<?php ehe($dt['name']); ?>').checked=true;" /><?php
              break;
          case 'IPV6':?>
            <input type="text" class="int" name="t_<?php ehe($dt['name']); ?>" id="t_<?php ehe($dt['name']); ?>" value="<?php ehe($targval); ?>" placeholder="2001:0db8:85a3:0000:0000:8a2e:0370:7334" size="32" onKeyPress="getElementById('r_<?php ehe($dt['name']); ?>').checked=true;" /><?php
              break;
          case 'TXT':?>
              <input type="text" class="int" name="t_<?php ehe($dt['name']); ?>" id="t_<?php ehe($dt['name']); ?>" value="<?php ehe($targval);?>" size="32" onKeyPress="getElementById('r_<?php ehe($dt['name']); ?>').checked=true;" />
              <?php
              break;
          case 'DOMAIN':?>
              <input type="text" class="int" name="t_<?php ehe($dt['name']); ?>" id="t_<?php ehe($dt['name']); ?>" value="<?php ehe($targval);?>" placeholder=<?= "toto.".$domain ?> size="32" onKeyPress="getElementById('r_<?php ehe($dt['name']); ?>').checked=true;" />
<?php
              break;
          case "NONE":
          default:
            break;
        } // switch ?>
      </td>
        <td>
<?php if ($dt['has_https_option']) { ?>

     <select class="inl" name="https_option" id="https_option">
            <option value="https"<?php selected((strtoupper($type)==strtoupper($dt['name']) && $sd["https"]=="https") || false); ?>><?php __("HTTPS (with HTTP redirect)"); ?></option>
            <option value="http"<?php selected((strtoupper($type)==strtoupper($dt['name']) && $sd["https"]=="http") || false); ?>><?php __("HTTP (with HTTPS redirect)"); ?></option>
            <option value="both"<?php selected((strtoupper($type)==strtoupper($dt['name']) && $sd["https"]=="both") || false); ?>><?php __("Both HTTP and HTTPS"); ?></option>
            </select>
<?php  } ?>
        </td>
    </tr>
    <?php } // foreach ?>

        <tr class="trbtn">
        <td colspan="2">
        <?php if ($isedit) { ?>
        <button class="inb cancel" type="button" name="cancel" onclick="document.location = 'dom_edit.php?domain=<?php echo $domain; ?>'"><?php __("Cancel"); ?></button>
        <?php } ?>
        <button type="submit" class="inb ok" name="add" onclick='return check_type_selected();'><?php
   if ($isedit) {
 __("Edit this subdomain");
} else {
 __("Add this subdomain");
} 
?></button>
</td>
        </tr>
    </table>
</form>

<script type="text/javascript">

function check_type_selected() {
  if ( $('input[name=type]:radio:checked').val() ) {
    // there is a value
    var ll = $('input[name=type]:radio:checked').val();
    var tt = $('#t_'+ll);
    if ( tt.length == 0 ) {
      // this element do not exist, so OK
      return true;
    }
    if ( tt.val() == '' ) {
      alert("<?php __("Missing value for this sub-domain"); ?>");
      return false;
    }
  
    return true;
  }
  alert("<?php __("Please select a type for this sub-domain"); ?>");
  return false;
}

function domtype_advanced_hide() { 
  <?php foreach ($lst_advanced as $adv) echo "$(\"#tr_$adv\").hide();\n"?>
  $("#domtype_show").show();
  $("#domtype_hide").hide();
}
function domtype_advanced_show() { 
  <?php foreach ($lst_advanced as $adv) echo "$(\"#tr_$adv\").show();\n"?>
  $("#domtype_show").hide();
  $("#domtype_hide").show();
}

<?php if (isset($type) && in_array($type, $lst_advanced) ) { // if it's an edit of an advanced option, we need to show the advanced options ?>
  domtype_advanced_show();
<?php } else { ?>
  domtype_advanced_hide();
<?php } // if advanced ?>

</script>
<?php
} // sub_domains_edit
?>


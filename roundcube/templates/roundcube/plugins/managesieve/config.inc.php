<?php

/***********************************************************************/
// Configuration file of managesieve's plugin of Roundcube for AlternC //
//                                                                     //
// /!\ WARNING /!\ Do not edit this file, edit the one in              //
// /etc/alternc/templates/roundcube/plugins/managesieve/               //
// and launch alternc.install again.                                   //
//                                                                     //
/***********************************************************************/

// managesieve server port. When empty the port will be determined automatically
// using getservbyname() function, with 4190 as a fallback.
$rcmail_config['managesieve_port'] = 4190;

// managesieve server address, default is localhost.
// Replacement variables supported in host name:
// %h - user's IMAP hostname
// %n - http hostname ($_SERVER['SERVER_NAME'])
// %d - domain (http hostname without the first part)
// For example %n = mail.domain.tld, %d = domain.tld
$rcmail_config['managesieve_host'] = 'localhost';

// authentication method. Can be CRAM-MD5, DIGEST-MD5, PLAIN, LOGIN, EXTERNAL
// or none. Optional, defaults to best method supported by server.
$rcmail_config['managesieve_auth_type'] = null;

// Optional managesieve authentication identifier to be used as authorization proxy.
// Authenticate as a different user but act on behalf of the logged in user.
// Works with PLAIN and DIGEST-MD5 auth.
$rcmail_config['managesieve_auth_cid'] = null;

// Optional managesieve authentication password to be used for imap_auth_cid
$rcmail_config['managesieve_auth_pw'] = null;

// use or not TLS for managesieve server connection
// Note: tls:// prefix in managesieve_host is also supported
$rcmail_config['managesieve_usetls'] = false;

// Connection scket context options
// See http://php.net/manual/en/context.ssl.php
// The example below enables server certificate validation
//$rcmail_config['managesieve_conn_options'] = array(
//  'ssl'         => array(
//     'verify_peer'  => true,
//     'verify_depth' => 3,
//     'cafile'       => '/etc/openssl/certs/ca.crt',
//   ),
// );
// Note: These can be also specified as an array of options indexed by hostname
$rcmail_config['managesieve_conn_options'] = null;

// A file with default script content (eg. spam filter)
$rcmail_config['managesieve_default'] = '/etc/dovecot/sieve/global';

// The name of the script which will be used when there's no user script
$rcmail_config['managesieve_script_name'] = 'managesieve';

// Sieve RFC says that we should use UTF-8 endcoding for mailbox names,
// but some implementations does not covert UTF-8 to modified UTF-7.
// Defaults to UTF7-IMAP
$rcmail_config['managesieve_mbox_encoding'] = 'UTF-8';

// I need this because my dovecot (with listescape plugin) uses
// ':' delimiter, but creates folders with dot delimiter
$rcmail_config['managesieve_replace_delimiter'] = '';

// disabled sieve extensions (body, copy, date, editheader, encoded-character,
// envelope, environment, ereject, fileinto, ihave, imap4flags, index,
// mailbox, mboxmetadata, regex, reject, relational, servermetadata,
// spamtest, spamtestplus, subaddress, vacation, variables, virustest, etc.
// Note: not all extensions are implemented
$rcmail_config['managesieve_disabled_extensions'] = array();

// Enables debugging of conversation with sieve server. Logs it into <log_dir>/sieve
$rcmail_config['managesieve_debug'] = false;

// Enables features described in http://wiki.kolab.org/KEP:14
$rcmail_config['managesieve_kolab_master'] = false;

// Script name extension used for scripts including. Dovecot uses '.sieve',
// Cyrus uses '.siv'. Doesn't matter if you have managesieve_kolab_master disabled.
$rcmail_config['managesieve_filename_extension'] = '.sieve';

// List of reserved script names (without extension).
// Scripts listed here will be not presented to the user.
$rcmail_config['managesieve_filename_exceptions'] = array();

// List of domains limiting destination emails in redirect action
// If not empty, user will need to select domain from a list
$rcmail_config['managesieve_domains'] = array();

// Default list of entries in header selector
$rcmail_config['managesieve_default_headers'] = array('Subject', 'From', 'To');

// Enables separate management interface for vacation responses (out-of-office)
// 0 - no separate section (default),
// 1 - add Vacation section,
// 2 - add Vacation section, but hide Filters section
$rcmail_config['managesieve_vacation'] = 0;

// Enables separate management interface for setting forwards (redirect to and copy to)
// 0 - no separate section (default),
// 1 - add Forward section,
// 2 - add Forward section, but hide Filters section
$rcmail_config['managesieve_forward'] = 0;

// Default vacation interval (in days).
// Note: If server supports vacation-seconds extension it is possible
// to define interval in seconds here (as a string), e.g. "3600s".
$rcmail_config['managesieve_vacation_interval'] = 1;

// Some servers require vacation :addresses to be filled with all
// user addresses (aliases). This option enables automatic filling
// of these on initial vacation form creation.
$rcmail_config['managesieve_vacation_addresses_init'] = false;

// Sometimes you want to always reply with mail email address
// This option enables automatic filling of :from field on initial vacation form creation.
$rcmail_config['managesieve_vacation_from_init'] = false;

// Supported methods of notify extension. Default: 'mailto'
$rcmail_config['managesieve_notify_methods'] = array('mailto');

// Enables scripts RAW editor feature
$rcmail_config['managesieve_raw_editor'] = true;

// Disabled actions
// Prevent user from performing specific actions:
// list_sets, enable_disable_set, delete_set, new_set, download_set, new_rule, delete_rule
// Note: disabling list_sets removes the Filter sets widget from the UI and means
//       the set defined in managesieve_script_name will always be used (and activated)
$rcmail_config['managesieve_disabled_actions'] = array();

// List of hosts that support managesieve.
// Activate managesieve for selected hosts only. If this is not set all hosts are allowed.
// Example: $rcmail_config['managesieve_allowed_hosts'] = array('host1.mydomain.com','host2.mydomain.com');
$rcmail_config['managesieve_allowed_hosts'] = null;

?>

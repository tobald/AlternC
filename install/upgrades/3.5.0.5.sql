CREATE OR REPLACE VIEW `alias_view` AS

-- Generate all the alias configured by the users
select
  concat(`address`.`address`,'@',`domaines`.`domaine`) AS `mail`,
  concat(if(`alternc`.`mailbox`.`id` is null,
  '',
  concat(concat(`address`.`address`,'@',`domaines`.`domaine`),'\n')),
  `recipient`.`recipients`) AS `alias`
from
  (
    ((`recipient` join `address` on(`address`.`id` = `recipient`.`address_id`))
    left join `mailbox` on(`mailbox`.`address_id` = `address`.`id`)
    )
    join `domaines` on(`domaines`.`id` = `address`.`domain_id`)
  )
where
  `address`.`enabled` = 1
  and `domaines`.`gesmx` = 1

UNION

-- Generate the alias for all the account
-- Example : account gaylord will have gaylord@FQDN
-- as an alias to his email account. FQDN can be
-- changed in variable mailname_bounce
select
  distinct concat(`m`.`login`,'@',`v`.`value`) AS `mail`,
  `m`.`mail` AS `alias`
from
  (`membres` `m` JOIN `variable` `v`)
where
  `v`.`name` = 'mailname_bounce'

UNION

-- Generate an alias alterncpanel@FQDN to admin mail
select
  distinct concat('alterncpanel','@',`v`.`value`) AS `mail`,
  `m`.`mail` AS `alias`
from
  (`membres` `m` JOIN `variable` `v`)
where
  (`v`.`name` = 'mailname_bounce' AND `m`.`uid`=2000)

;


INSERT IGNORE INTO `domaines_type` (name, description, target, entry, compatibility, only_dns, need_dns, advanced, enable) values
('delegation', 'Zone delegation', 'DOMAIN', '%SUB% NS %TARGET%', 'txt,mx,mx2,defmx,defmx2',true, true, true , 'ALL');

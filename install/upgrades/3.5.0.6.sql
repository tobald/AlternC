-- Opendkim signatures are split in 255 bytes batches and require no quotes
update domaines_type set entry='%SUB% IN TXT %TARGET%' where name="dkim";

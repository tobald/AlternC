ALTER TABLE sub_domaines MODIFY COLUMN https VARCHAR(16) NOT NULL DEFAULT '';
UPDATE variable SET value="a mx ~all" WHERE name="default_spf_value";

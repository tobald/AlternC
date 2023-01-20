-- Add global variable http_https_redirection
INSERT IGNORE INTO variable (name, value, comment) VALUES ("http_https_redirection", "https", "The default value for the redirection between http and https. Value = http -> redirect https to http. Value = https -> redirect http to https. Value = both -> both works, no redirection.");

\! echo "--- INSERTING EXAMPLE DATA ---"

\! echo "Adding Digest User List"
INSERT INTO digestauthserver.digest_users
    (name, password)
    VALUES
    ('DigestUserOne', 'Thisisthedigestpasswordincleartext'),
    ('DigestUserTwo', 'Thisisalsothedigestpasswordincleartext')
    ;

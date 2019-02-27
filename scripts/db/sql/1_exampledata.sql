\! echo "--- INSERTING EXAMPLE DATA ---"

CREATE EXTENSION pgcrypto;

\! echo "Adding User List"
INSERT INTO jwtserver.users
    (name, email, password)
    VALUES
    ('Jim', 'jim@example.com', crypt('jimpw', gen_salt('bf'))),
    ('Dave', 'davegthemighty@hotmail.com', crypt('davepw', gen_salt('bf')))
    ;

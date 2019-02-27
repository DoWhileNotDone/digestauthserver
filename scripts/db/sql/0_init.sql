\! echo "--- INIT SCRIPT ---"

DROP SCHEMA IF EXISTS jwtserver CASCADE;

DO
$do$
BEGIN
   IF NOT EXISTS (
      SELECT                       -- SELECT list can stay empty for this
      FROM   pg_catalog.pg_roles
      WHERE  rolname = 'jwtserver') THEN
      CREATE USER jwtserver WITH ENCRYPTED PASSWORD 'jwtserver';
   END IF;
END
$do$;

CREATE SCHEMA jwtserver AUTHORIZATION jwtserver;

\! echo "Creating Tables..."

\! echo "Creating User Table..."
CREATE TABLE jwtserver.users (
	id  SERIAL PRIMARY KEY,
	name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password TEXT NOT NULL,
	created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	updated_at timestamp NULL
);

select * from jwtserver.users;
\! echo "Done!"

\! echo "Granting Schema Privs..."
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA jwtserver TO jwtserver;
GRANT USAGE, SELECT ON ALL SEQUENCES IN SCHEMA jwtserver TO jwtserver;

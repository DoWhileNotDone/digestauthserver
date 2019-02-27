\! echo "--- INIT SCRIPT ---"

DROP SCHEMA IF EXISTS digestauthserver CASCADE;

DO
$do$
BEGIN
   IF NOT EXISTS (
      SELECT                       -- SELECT list can stay empty for this
      FROM   pg_catalog.pg_roles
      WHERE  rolname = 'digestauthserver') THEN
      CREATE USER digestauthserver WITH ENCRYPTED PASSWORD 'digestauthserver';
   END IF;
END
$do$;

CREATE SCHEMA digestauthserver AUTHORIZATION digestauthserver;

\! echo "Creating Tables..."

\! echo "Creating Digest User Table..."
CREATE TABLE digestauthserver.digest_users (
	id  SERIAL PRIMARY KEY,
	name TEXT NOT NULL,
  password TEXT NOT NULL,
	created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	updated_at timestamp NULL
);

select * from digestauthserver.digest_users;
\! echo "Done!"

\! echo "Creating Digest Authentication Request Table..."
CREATE TABLE digestauthserver.authentication_requests (
	id  SERIAL PRIMARY KEY,
	nonce TEXT NOT NULL,
  used BOOLEAN NOT NULL DEFAULT false,
  opaque TEXT NOT NULL,
	created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	updated_at timestamp NULL
);

select * from digestauthserver.authentication_requests;
\! echo "Done!"


\! echo "Granting Schema Privs..."
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA digestauthserver TO digestauthserver;
GRANT USAGE, SELECT ON ALL SEQUENCES IN SCHEMA digestauthserver TO digestauthserver;

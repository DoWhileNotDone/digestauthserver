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


\! echo "Granting Schema Privs..."
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA digestauthserver TO digestauthserver;
GRANT USAGE, SELECT ON ALL SEQUENCES IN SCHEMA digestauthserver TO digestauthserver;

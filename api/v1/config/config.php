<?php
  date_default_timezone_set ('Europe/Stockholm');

  /* Database details */

  define( 'DB_CONNECTION', 'sqlsrv:server = tcp:reko2.database.windows.net,1433; Database = maindb2' );
  define( 'DB_USER',       'rekoresor' );
  define( 'DB_PASSWORD',   '1qaz!QAZdb' );


  /* Provide some detals about your company */

  define( 'CI_NAME',        'Rekå Resor' );
  define( 'CI_LEGALNAME',   'Aktiebolaget Bohus Rekå-Resor' );
  define( 'CI_SLOGAN',      'mer än 60 år av reko resor' );
  define( 'CI_ORGNR',       '556176-4456' );
  define( 'CI_PSTREET',     'Box 8797' );
  define( 'CI_PZIP',        '402 76' );
  define( 'CI_PCITY',       'Göteborg' );
  define( 'CI_VSTREET',     'Aröds Industriväg 30' );
  define( 'CI_VZIP',        '422 43' );
  define( 'CI_VCITY',       'Hisings Backa' );
  define( 'CI_PHONE',       '031-22 21 20' );
  define( 'CI_EMAIL',       'info@rekoresor.se' );
  define( 'CI_WEBSITE',     'www.rekoresor.se' );


  /* Authentication */

  define('AUTH_PWD_PEPPER',            'T6ZUZmpFwqEDYP6fuZ3tzszJpUhc4NjSUdrd$uxmyAkM6r4zMmtw4TnPMAK5');
  define('AUTH_JWT_SECRET_PEPPER',     'tnqB!SH3BU$WzfArr*nfY6WDZ!C5YUv!VPYKKUHGk^Nh!528j$4J*3Nq9SGX'); //Provides a way to revoke all tokens including refresh tokens with unique secrets
  define('AUTH_API_KEY',               'XW%Gke9Dh@Y*^wgSArhSYdyEc3X#p$%j75w$zp!a');
  define('AUTH_REFRESH_KEY',           'k9!SqB!Eh%TXJ@2x@Q7PsUT^32efkUJ!GPvxCpf3J9DFhJr4Bh@2*7hEC3Je');

  /* Enviroment */

  define('ENV_DEBUG_MODE',              true);
  define('ENV_LAN_LOCK',                true);
  define('ENV_REQUIRE_JWT',             true);
  define('ENV_ACCESS_CONTROL_ENABLED',  false);
  define('ENV_IP_LOCK',                 true);  
  define('ENV_LOG_PATH',                'C:/logs/web/inetpub/');

  define('ENV_CRON_JOB',                false); //Is token refresh operation run by cron

  define('ENV_DOMAIN',               'localhost');

  if (empty($_SERVER['HTTPS']) || !filter_var($_SERVER['HTTPS'], FILTER_VALIDATE_BOOLEAN)) {
    define('ENV_FULL_DOMAIN',         'http://' . ENV_DOMAIN);
  } else {
    define('ENV_FULL_DOMAIN',         'https://' . ENV_DOMAIN);
  }
?>

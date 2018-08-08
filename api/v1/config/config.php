<?php
  date_default_timezone_set ('Europe/Stockholm');

  /* Database details */

  define( 'DB_CONNECTION', 'sqlsrv:server = tcp:reko2.database.windows.net,1433; Database = maindb2' );
  define( 'DB_USER',       'rekoresor' );
  define( 'DB_PASSWORD',   '1qaz!QAZ' );


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

  define('AUTH_PWD_PEPPER',              '+-i-v tEZ~.,K+tk+|]%%V(+5U3<*||rf%J%z7o^.uYUDGA(MN!kY9!9.MQTA$1k');
  define('AUTH_JWT_SECRET_PEPPER',       'wJ2cJDrmJd5X5yUaXE67hGC9mVWzZjcGsRsYX3fy');
  define('AUTH_JWT_WATERMARK',           'qAUHDZ78VURAX79a6rXn2Fc4fRpkmsYvsfZVKdCY');
  define('AUTH_API_TOKEN',               '%{-LY~vkJb#O>#e<%cp%7}I-Rqsg-TCj[kO!GfUCoM>/op<?8FiUjP|+& Za](p7');
  define('AUTH_RELOGIN_TOKEN',           '+4xbKVh8+RWEPPv8u+');

  /* Enviroment */

  define('ENV_DEBUG_MODE',              true);
  define('ENV_LAN_LOCK',                true);
  define('ENV_REQUIRE_JWT',             true);
  define('ENV_ACCESS_CONTROL_ENABLED',  false);
  define('ENV_IP_LOCK',                 true);  


  define('ENV_DOMAIN',               'localhost');

  if (empty($_SERVER['HTTPS']) || !filter_var($_SERVER['HTTPS'], FILTER_VALIDATE_BOOLEAN)) {
    define('ENV_FULL_DOMAIN',         'http://' . DOMAIN);
  } else {
    define('ENV_FULL_DOMAIN',         'https://' . DOMAIN);
  }
?>

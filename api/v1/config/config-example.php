<?php
  date_default_timezone_set ('Europe/Stockholm');

  /* Database details */

  define( 'DB_CONNECTION',      'First part of DB PDO connection string goes here' );
  define( 'DB_USER',            'name' );
  define( 'DB_PASSWORD',        'password' );


  /* Provide some detals about your company */

  define( 'CI_NAME',        'Short name of your company' );
  define( 'CI_LEGALNAME',   'Full legal name of your company, for invoices' );
  define( 'CI_SLOGAN',      'Your slogan' );
  define( 'CI_ORGNR',       'Organisation Number' );
  define( 'CI_PSTREET',     'Post address street or Box line' );
  define( 'CI_PZIP',        'Post or box address post code/zip including state if applicable' );
  define( 'CI_PCITY',       'Post or box address city' );
  define( 'CI_VSTREET',     'Visiting address street' );
  define( 'CI_VZIP',        'Visiting address post code/zip including state if applicable' );
  define( 'CI_VCITY',       'Visiting adress city' );
  define( 'CI_PHONE',       'Billing phone contact' );
  define( 'CI_EMAIL',       'Billing e-mail contact' );
  define( 'CI_WEBSITE',     'Website url' );


  /* Authentication */

  define('AUTH_PWD_PEPPER',              '');
  define('AUTH_JWT_SECRET_PEPPER',       '');
  define('AUTH_JWT_WATERMARK',           '');
  define('AUTH_API_TOKEN',               '');
  define('AUTH_RELOGIN_TOKEN',           '+...+');

  /* Enviroment */

  define('ENV_DEBUG_MODE',              true);
  define('ENV_LAN_LOCK',                true);
  define('ENV_REQUIRE_JWT',             true);
  define('ENV_ACCESS_CONTROL_ENABLED',  false);
  define('ENV_IP_LOCK',                 true);  
  define('ENV_LOG_PATH',                'C:/logs/');


  define('ENV_DOMAIN',               'localhost');

  if (empty($_SERVER['HTTPS']) || !filter_var($_SERVER['HTTPS'], FILTER_VALIDATE_BOOLEAN)) {
    define('ENV_FULL_DOMAIN',         'http://' . DOMAIN);
  } else {
    define('ENV_FULL_DOMAIN',         'https://' . DOMAIN);
  }
?>

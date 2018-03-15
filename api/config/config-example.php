<?php
  mb_internal_encoding("UTF-8");
  date_default_timezone_set ('Europe/Stockholm');

  /* Database details */

  define( 'DB_CONNECTION',      'First part of DB PDO connection string goes here' );
  define( 'DB_USER',            'app name' );
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


  /* Enviroment */

  define('PWD_PEPPER',              'pwd pepper');
  define('JWT_SECRET_PEPPER',       'jwt secret');
  define('JWT_WATERMARK',           'jwt watermark');
  define('API_TOKEN',               'main api token');
  define('ONCE_LOGIN_TOKEN',        '+static token for once logins+');
  define('DEBUG_MODE',              true);
  define('LAN_LOCK',                true);
  define('REQUIRE_JWT',             true);
  define('ACCESS_CONTROL_ENABLED',  false);   
  define('IP_LOCK',                 true);

  define('DOMAIN',                  'base url');

  if (empty($_SERVER['HTTPS']) || !filter_var($_SERVER['HTTPS'], FILTER_VALIDATE_BOOLEAN)) {
    define('FULL_DOMAIN',         'http://' . DOMAIN);
  } else {
    define('FULL_DOMAIN',         'https://' . DOMAIN);
  }
?>

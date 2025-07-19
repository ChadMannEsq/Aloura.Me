<?php

/**

 * The base configuration for WordPress

 *

 * The wp-config.php creation script uses this file during the installation.

 * You don't have to use the website, you can copy this file to "wp-config.php"

 * and fill in the values.

 *

 * This file contains the following configurations:

 *

 * * Database settings

 * * Secret keys

 * * Database table prefix

 * * ABSPATH

 *

 * @link https://wordpress.org/documentation/article/editing-wp-config-php/

 *

 * @package WordPress

 */


//  wp-admin: devteam / PSns2doADlyROhw41V


// ** Database settings - You can get this info from your web host ** //

/** The name of the database for WordPress */

define( 'DB_NAME', 'u644829747_wrtnw' );


/** Database username */

//define( 'DB_USER', 'root' );

define( 'DB_USER', 'u644829747_wrtnw' );


/** Database password */

//define( 'DB_PASSWORD', 'root' );

define( 'DB_PASSWORD', 'lAz8UX[znE' );


/** Database hostname */

//define( 'DB_HOST', 'localhost' );

define( 'DB_HOST', 'localhost' );


/** Database charset to use in creating database tables. */

define( 'DB_CHARSET', 'utf8mb4' );


/** The database collate type. Don't change this if in doubt. */

define( 'DB_COLLATE', '' );


/**#@+

 * Authentication unique keys and salts.

 *

 * Change these to different unique phrases! You can generate these using

 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.

 *

 * You can change these at any point in time to invalidate all existing cookies.

 * This will force all users to have to log in again.

 *

 * @since 2.6.0

 */

define( 'AUTH_KEY',         '<SDX)rAS#WJJd568SJ_}O7K$Wp+SSOsjb:#%flwLc&5JRIo-~U~>Z4<G;xU1{YaY' );

define( 'SECURE_AUTH_KEY',  'IwPeFk@jlYw*[u-y6<1yr<<MH_y55|S;h=D7+ 9?O<v_rS&GUM^,pS#w##gk4~>w' );

define( 'LOGGED_IN_KEY',    'x+Z7c`<wP%[LCPPFF}5lf&] Y-RihVQzStw;t|0J{Y^+Yqr,5ok$~Mxls8!pS_b,' );

define( 'NONCE_KEY',        '[@ac9&uv.E;&)-P:&p50D}o9-znFc/;04Afwh?P%R k)[3%FBY:c7g_]ZB`s-&<f' );

define( 'AUTH_SALT',        'g|xN~x]v>[LW0{24+9v)?O|m9Dh6z]7[[5{@I^eZV|Zpg+N]E:uR9~w<6()3an5M' );

define( 'SECURE_AUTH_SALT', '1s*1$ dj*o6,dGI!@&OebO>YV{$;FtQ^A58PVDi0e!yFR5/P2|Zz)c=IRN8?fn#r' );

define( 'LOGGED_IN_SALT',   '3_fC&6;KL`%`UZE3eL.MW ~5J|t)vCB9(pN0NtNOwp)KT;LB<q{Hp!{rjb7<FozK' );

define( 'NONCE_SALT',       'q>1(|G =+0#(LoUD-uIU8$R-3+>@]&l<!,*Yd5x?w>PO<T=^RR#@>5NpbslOUQM?' );




/**#@-*/


/**

 * WordPress database table prefix.

 *

 * You can have multiple installations in one database if you give each

 * a unique prefix. Only numbers, letters, and underscores please!

 */

$table_prefix = 'wp_';


/**

 * For developers: WordPress debugging mode.

 *

 * Change this to true to enable the display of notices during development.

 * It is strongly recommended that plugin and theme developers use WP_DEBUG

 * in their development environments.

 *

 * For information on other constants that can be used for debugging,

 * visit the documentation.

 *

 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/

 */

define('FS_METHOD', 'direct');

define('JWT_AUTH_SECRET_KEY', 'putri-wp');

define('JWT_AUTH_CORS_ENABLE', true);




// define('FS_METHOD', 'direct');

/* Add any custom values between this line and the "stop editing" line. */


if ($_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') $_SERVER['HTTPS']='on';


define( 'WP_DEBUG', true );
define( 'SCRIPT_DEBUG', true );
define( 'WP_DEBUG_LOG', '/Applications/MAMP/htdocs/putri-wp/wp-content/uploads/debug-log-manager/localhost_20240812112619695718_debug.log' );
define( 'WP_DEBUG_DISPLAY', false );
define( 'DISALLOW_FILE_EDIT', false );
/* That's all, stop editing! Happy publishing. */


/** Absolute path to the WordPress directory. */

if ( ! defined( 'ABSPATH' ) ) {

	define( 'ABSPATH', __DIR__ . '/' );

}


/** Sets up WordPress vars and included files. */

require_once ABSPATH . 'wp-settings.php';


@ini_set( 'upload_max_size' , '128M' );

@ini_set( 'post_max_size', '128M');

@ini_set( 'max_execution_time', '300' );





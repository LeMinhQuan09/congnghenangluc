<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
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

if ( !empty( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && 'https' == $_SERVER['HTTP_X_FORWARDED_PROTO'] ) {
    $_SERVER['HTTPS'] = 'on';
}

/** PHP Memory */
const WP_MEMORY_LIMIT = '512M';
const WP_MAX_MEMORY_LIMIT = '512M';

const DISALLOW_FILE_EDIT = true;
const DISALLOW_FILE_MODS = false;

/* SSL */
const FORCE_SSL_LOGIN = true;
const FORCE_SSL_ADMIN = true;

const WP_POST_REVISIONS = 1;
const EMPTY_TRASH_DAYS = 5;
const AUTOSAVE_INTERVAL = 120;

//const ALLOW_UNFILTERED_UPLOADS = true;

/** Disable WordPress core auto-update, */
const WP_AUTO_UPDATE_CORE = false;

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */

const DB_NAME = 'congnghenangluc';
const DB_USER = 'root';
const DB_PASSWORD = '';

const DB_HOST = 'localhost';
const DB_CHARSET = 'utf8mb4';
const DB_COLLATE = '';

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
define( 'AUTH_KEY',         'EXhwO>6KRbAN[7ZAjx4>RS&02koOIbCB@|re2[f~nThS0$[FIAC?cS:F5hy|X]rz' );
define( 'SECURE_AUTH_KEY',  ']K8~r;Vjh%KO<d&uy6)7]#>REHK?DpAj!XvrgOIRYxn1)DUp@W)6b)j9_Q/.8$D_' );
define( 'LOGGED_IN_KEY',    'a.@hUTG0f$YJb/G/<:d(uqJ]a_>p.|s`qga:9|KtP?wW<8g!HY#S+p%h7},@[aB ' );
define( 'NONCE_KEY',        'CEnl!9q.i WiU%K#)1GHf0OC+uIbyGNIGZJlOeuCh6<ZUv(#j8Lh^kmmJ9 #W3$G' );
define( 'AUTH_SALT',        '!>rBXZKUUCGmr^D)?$zJrfNrkAwE&2({U<,a>$6Pr }VHGrqyxz+m-OvT7XNf_oC' );
define( 'SECURE_AUTH_SALT', ']-lgFR{3.2lVhk$[r:H#nyY<,c?Lq{oS-D?K[Hfa2#W9Bk**3;$E&^kLm[AZE3K3' );
define( 'LOGGED_IN_SALT',   'si`lL!Lyl+vH$>h_BkHoG^6Ht}5%8ldLaIm-rH pCY;&H^#|!^Nr?[WCiIKY/DB-' );
define( 'NONCE_SALT',       'P[*wz__1Q[GSAP@e+]?tgol HO-I+HJ}pgAGg$cP8x=-PI7^<|y;A8dtR`O1Q4l_' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'w_';

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
const WP_DEBUG = false;

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';

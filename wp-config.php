<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'earthin7_wo5418');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'UZ@V@R^|% [s`~)z`:32|Q :_EUJz;Sb%V/ FhBAO1i/K!WYN;/uubAIAEI0^O>;');
define('SECURE_AUTH_KEY',  'i6IIeeB{CcBE)yj8Q.@2aEy,rZ?[d hPeOs<Z-:b^g3_6KN:4chE/1,{t0%5s6=}');
define('LOGGED_IN_KEY',    'OFZD`1904HKL@DvhfbQnmz 1WcHppi>|1 TmgC,ogDU9CJt%||c#7a4pKdYk>+$u');
define('NONCE_KEY',        '(HcEGg#SZWIO?)!bGA>Be2}5*dh,XaBr%4Z#G~m0gJfIK{M&4C*v};w[9IX)n|*5');
define('AUTH_SALT',        'Ykd7RllMQac/quOl2BP$#68?&5UEsxtjnX~Yyt]`7#ekm5i A2HZNoL<qH/r[|TR');
define('SECURE_AUTH_SALT', 'hD>B4<z1/UwctT~?Sk:=2{nA&yfwpXp/d3{&7^jduAG)-H=^6X?1qHUreFArwly|');
define('LOGGED_IN_SALT',   '_u:1m32DTt:j9aZ:ZOY|C0!{Li7-LQ$Ws6|c-rF_RqScD@k+fJ_O4?KoW!8tjzcy');
define('NONCE_SALT',       'izM*YHX;KJC2;D3x;+KA89Jb`.kH>G>18x@$:;vzv~Jg&wu-R[((tUExBNN5?t1:');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

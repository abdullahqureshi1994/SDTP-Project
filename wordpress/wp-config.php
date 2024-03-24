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

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'sdtp_wordpress' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '123456' );

/** Database hostname */
define( 'DB_HOST', '127.0.0.1' );

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
define( 'AUTH_KEY',         'qq80t_:K-%@>CZ_VHZC@^-)f1Kn#/v7Pt#%1>#Rk0?Q$%e|<;+zfY[^]v)^&eF8(' );
define( 'SECURE_AUTH_KEY',  '.uZ4D-ssiJw]dCeiR}`/Yr/JC#^BWot3y`~sJ?&BoO*l(B2(Ak>[Q)X)+dk.Xm#G' );
define( 'LOGGED_IN_KEY',    '~FeVS$XTz-&fE@c1ed1nlH%IF7_FriDSYa@Id`_iN>7>(7HHz:y(^iXNz!`nf^/i' );
define( 'NONCE_KEY',        '9fX8?uA|6~6<lVK!XC* 7e40gETl][OzUM/;Iw1lX`:s*fCE ,=%Po9^A^8loFwH' );
define( 'AUTH_SALT',        'yNGj)AsQblDU{z%/=Hd*kA-M#z6b*KnxN!Gb6h-%D 7^Vt<@l>U-&%.A=KInj]@A' );
define( 'SECURE_AUTH_SALT', '(g:Aq(K|[X(Lh@<GUF9SZ@yfr{wIjJ7^(ZaKm,VDcU:/KaU}`9WX-vN6<v.FozE~' );
define( 'LOGGED_IN_SALT',   '|aBu%&e]}fyo(/Bm9+pX2nZns`Y)XV_(i*6jl|e!x#EvE|<U8kPwI^ewexnmS[$&' );
define( 'NONCE_SALT',       '+yFuVFX$u.8[x-L0U}6&=odI$Bsw[YPZEWlb3OM,rbd~BWI,oLJj5/ {~s#N<-PG' );

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
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';

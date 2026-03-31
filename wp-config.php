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
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'viphoseandseals' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
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
define( 'AUTH_KEY',         '`U${;xtKpTTo#hyEhQLUGvcDgQvZwy2}NBoq:2{c+[+Cwr>olw0_4uB=/0GTrx:(' );
define( 'SECURE_AUTH_KEY',  'm?hm/M].H/Nj6ZSmCLg&8.2O^|uO5E)EcPscaIsW{4=0Uycdj)IRhfhT@QpafeTm' );
define( 'LOGGED_IN_KEY',    'V;y/5S0N#a51>a]p1iOx?m5p)g.vAlR?DHi<:?IMzy(zy#A!#&F:<kufo9_b)1WH' );
define( 'NONCE_KEY',        'XjZazJJ#ajvcF.+TpKrwf)un{Hz`2.#Y3i(nMx7@qbqn5wMb]{DDwh1[JXfsuMNe' );
define( 'AUTH_SALT',        '>O#_&ZqUemIbz5iyR7ko@M6:yM~^H:dTBL@;HkFc ESJvthApoo24M}Zgp;--z%f' );
define( 'SECURE_AUTH_SALT', 'NG>1k@n@/<C{W^-)r_4SYiouUhSnIZe M;iI~]^~{@ d;vyNQpmq.j%qYN|Pnd[(' );
define( 'LOGGED_IN_SALT',   '&L/>M&+4DE7]Z juLfH !orw%/oB~r;=IjfVuw5C{Ax`rlAk)x)P2,VgT2AK ;-1' );
define( 'NONCE_SALT',       'OkDRat2:TE*@W=_1p6.Qm5Ui#,@0rbu,e>G_P]>NSeck{HLn:iE+X;3$fq]%XC@;' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
//define( 'WP_DEBUG', false );
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', true);

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';

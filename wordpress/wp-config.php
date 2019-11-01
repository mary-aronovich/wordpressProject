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
define( 'DB_NAME', 'mary_db' );

/** MySQL database username */
define( 'DB_USER', 'mary' );

/** MySQL database password */
define( 'DB_PASSWORD', 'marysite' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '?@%PwX=e%Nk_7PFCVO=%,mZK3Wb#Nqv+&vTzd9uWJR2@y2#BzSmc5`i14#,b{kfZ' );
define( 'SECURE_AUTH_KEY',  '}H:u`L6wW&YO!jT&SOsJD=wQR&.A*F)1uW#$(cFS&4`Sh=isXbf*,)d hm?3Ik#<' );
define( 'LOGGED_IN_KEY',    'oZZ|FEW5mD}qYft#Dd0,iGCl;k|5*|et.{s=Cc+>wpCjd>4J~=.*1+ 4lo{&q,N;' );
define( 'NONCE_KEY',        'OQjDe@Kr9-y2-Q*n_7 i=L6_;3&pN!XNb<AV!(^#ZU/)_QuL#O:G$u{P*3wrnFHu' );
define( 'AUTH_SALT',        ',Qp5p=v@G)XGur*.Jo!$nt6(|PpnIz/h|ofjSi%e4CG,*~}8JKLH6>}>H_RT<ni-' );
define( 'SECURE_AUTH_SALT', 'MVY@XN(Mn(W,bP%rt/j6]87 )iTjC@{[!u.=yxf Wl)vP=u=CR$=^O}o:b#ci4M?' );
define( 'LOGGED_IN_SALT',   'U{Tz1&bDs0|,CH(ZXj3CzMdo2Y)BvJ!+HNp~PP@)BE-0H 7gRpI$4xP/M[R08b$0' );
define( 'NONCE_SALT',       'b0~D!2E6dDx|2xhgWB~2aq9a$Gr4MB`3tiSrwO5OH,5Gl^0qZRB5Vt7$&MgaIu(Q' );

/**#@-*/

/**
 * WordPress Database Table prefix.
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
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );

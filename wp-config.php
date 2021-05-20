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

// ** MySQL settings ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'bookinghostup' );

/** MySQL database username */
define( 'DB_USER', 'bookinghostup' );

/** MySQL database password */
define( 'DB_PASSWORD', "bookinghostup" );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/* Defines for ultimate member plugin */

define( 'WP_MEMORY_LIMIT', '256M' );

/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'H6xNF1s8oPlMqzoUfMCeHLtbp6+dZdmWVzvgB5LlkZ2YtD/5khshcnO5r7DR2fKUtkFFYvNobvf9RPreilNdMA==');
define('SECURE_AUTH_KEY',  'C3H5rA/lashPMVE4vVEc1mfA4aQ3reByUKsxnbDlqWZ3G6dwZAibvSHVnPhgqDxXMAdm53xav0aqApie2pwUEQ==');
define('LOGGED_IN_KEY',    '4EOZ8WY2kazdJPYGJlLjrUZJ6vf3H2LFL5A2kTTHWSAjYfi1auPDbL913ejkHhaJzeKrvH2R0D5wYCqIUy55xQ==');
define('NONCE_KEY',        'Uzp89Al6a6rQTbw2d19WFzmglU68efVw2SmPLdt/f6ltbxE2D9fAENGxIwIlkvxylYyTXZB+DrhNZ7OPFYb3Tw==');
define('AUTH_SALT',        'TTkQWojwQyJMzK7c9Y0gQJm0mhJaBKI37yAeiN2sd8v7IRh+gNlM6HPgQ9EAcemcGJlZiQdQSyTQTQIq0899GQ==');
define('SECURE_AUTH_SALT', 'QVN1DQ/Fsio207HiY5M+PK/XdIkzyKgsJN/GcSYO8+bmfGJjzTb36FrZ6No5q4+GusqyaufP23wYNzH5rhdn/A==');
define('LOGGED_IN_SALT',   'Wvcm4uFIqAT5Ka8NGPcB7XQJhZkv7nVL87gwPuLUK6AW9cvaZg1coYkifteoX8qRtwOQ3bc/poNusppeBEXH4A==');
define('NONCE_SALT',       'd+S1ChmtsLmBWoI3560ZTMSfWlqWwD8XYqBGQp+w0B1aSLQ9w75ILVMq/gB5wlMI4o7NzwCFmuZqFvaGP3BOdQ==');

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';




/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) )
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';

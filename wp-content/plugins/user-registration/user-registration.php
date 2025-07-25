<?php //phpcs:ignore
/**
 * Plugin Name: User Registration
 * Plugin URI: https://wpuserregistration.com/
 * Description: Drag and Drop user registration form and login form builder.
 * Version: 3.3.0
 * Author: WPEverest
 * Author URI: https://wpuserregistration.com
 * Text Domain: user-registration
 * Domain Path: /languages/
 *
 * @package UserRegistration
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'UserRegistration' ) ) :

	/**
	 * Main UserRegistration Class.
	 *
	 * @class   UserRegistration
	 * @version 1.0.0
	 */
	final class UserRegistration {

		/**
		 * Plugin version.
		 *
		 * @var string
		 */
		public $version = '3.3.0';

		/**
		 * Session instance.
		 *
		 * @var UR_Session|UR_Session_Handler
		 */
		public $session = null;

		/**
		 * Query instance.
		 *
		 * @var UR_Query
		 */
		public $query = null;

		/**
		 * Instance of this class.
		 *
		 * @var object
		 */
		protected static $_instance = null;

		/**
		 * Instance of this form.
		 *
		 * @var object
		 */
		public $form = null;

		/**
		 * UTM Campaign.
		 *
		 * @var string
		 */
		public $utm_campaign = 'lite-version';

		/**
		 * Return an instance of this class.
		 *
		 * @return object A single instance of this class.
		 */
		public static function instance() {
			// If the single instance hasn't been set, set it now.
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		/**
		 * Cloning is forbidden.
		 *
		 * @since 1.0
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'user-registration' ), '1.0' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Unserializing instances of this class is forbidden.
		 *
		 * @since 1.0
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'user-registration' ), '1.0' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/**
		 * UserRegistration Constructor.
		 */
		public function __construct() {
			$this->define_constants();
			$this->includes();
			$this->init_hooks();
			add_action( 'plugins_loaded', array( $this, 'objects' ), 1 );

			do_action( 'user_registration_loaded' );
		}

		/**
		 * Hook into actions and filters.
		 */
		private function init_hooks() {
			register_activation_hook( __FILE__, array( 'UR_Install', 'install' ) );
			register_shutdown_function( array( $this, 'log_errors' ) );
			add_action( 'after_setup_theme', array( $this, 'include_template_functions' ), 11 );
			add_action( 'init', array( $this, 'init' ), 0 );
			add_action( 'init', array( 'UR_Shortcodes', 'init' ) );

			add_filter( 'plugin_action_links_' . UR_PLUGIN_BASENAME, array( __CLASS__, 'plugin_action_links' ) );
			add_filter( 'plugin_row_meta', array( __CLASS__, 'plugin_row_meta' ), 10, 2 );
		}

		/**
		 * Ensures fatal errors are logged so they can be picked up in the status report.
		 *
		 * @since 3.0.5
		 */
		public function log_errors() {
			$error = error_get_last();

			if ( $error && in_array( $error['type'], array( E_ERROR, E_PARSE, E_COMPILE_ERROR, E_USER_ERROR, E_RECOVERABLE_ERROR ), true ) ) {
				$logger = ur_get_logger();
				$logger->critical(
					/* translators: 1: error message 2: file name and path 3: line number */
					sprintf( __( '%1$s in %2$s on line %3$s', 'user-registration' ), $error['message'], $error['file'], $error['line'] ) . PHP_EOL,
					array(
						'source' => 'fatal-errors',
					)
				);
			}
		}

		/**
		 * Define FT Constants.
		 */
		private function define_constants() {
			$upload_dir = apply_filters( 'user_registration_upload_dir', wp_upload_dir() );
			$this->define( 'UR_LOG_DIR', $upload_dir['basedir'] . '/ur-logs/' );
			$this->define( 'UR_UPLOAD_PATH', $upload_dir['basedir'] . '/user_registration_uploads/' );
			$this->define( 'UR_UPLOAD_URL', $upload_dir['baseurl'] . '/user_registration_uploads/' );
			$this->define( 'UR_DS', DIRECTORY_SEPARATOR );
			$this->define( 'UR_PLUGIN_FILE', __FILE__ );
			$this->define( 'UR_ABSPATH', __DIR__ . UR_DS );
			$this->define( 'UR_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
			$this->define( 'UR_VERSION', $this->version );
			$this->define( 'UR_TEMPLATE_DEBUG_MODE', false );
			$this->define( 'UR_FORM_PATH', UR_ABSPATH . 'includes' . UR_DS . 'form' . UR_DS );
			$this->define( 'UR_SESSION_CACHE_GROUP', 'ur_session_id' );
		}

		/**
		 * Define constant if not already set.
		 *
		 * @param string      $name Name.
		 * @param string|bool $value Value.
		 */
		private function define( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}

		/**
		 * What type of request is this?
		 *
		 * @param  string $type admin, ajax, cron or frontend.
		 * @return bool
		 */
		private function is_request( $type ) {
			switch ( $type ) {
				case 'admin':
					return is_admin();
				case 'ajax':
					return defined( 'DOING_AJAX' );
				case 'cron':
					return defined( 'DOING_CRON' );
				case 'frontend':
					return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
			}
		}

		/**
		 * Includes.
		 */
		private function includes() {

			/**
			 * Class autoloader.
			 */
			include_once UR_ABSPATH . 'includes/class-ur-autoloader.php';

			/**
			 * Interfaces.
			 */
			include_once UR_ABSPATH . 'includes/interfaces/class-ur-logger-interface.php';
			include_once UR_ABSPATH . 'includes/interfaces/class-ur-log-handler-interface.php';

			/**
			 * Abstract classes
			 */
			include_once UR_ABSPATH . 'includes/abstracts/abstract-ur-form-field.php';
			include_once UR_ABSPATH . 'includes/abstracts/abstract-ur-field-settings.php';
			include_once UR_ABSPATH . 'includes/abstracts/abstract-ur-log-handler.php';
			include_once UR_ABSPATH . 'includes/abstracts/abstract-ur-session.php';

			/**
			 * Core classes.
			 */
			include_once UR_ABSPATH . 'includes/functions-ur-core.php';
			include_once UR_ABSPATH . 'includes/functions-ur-form.php';
			include_once UR_ABSPATH . 'includes/class-ur-install.php';
			include_once UR_ABSPATH . 'includes/class-ur-post-types.php'; // Registers post types.
			include_once UR_ABSPATH . 'includes/class-ur-user-approval.php';
			include_once UR_ABSPATH . 'includes/class-ur-smart-tags.php'; // User Approval class.
			include_once UR_ABSPATH . 'includes/class-ur-emailer.php';
			include_once UR_ABSPATH . 'includes/class-ur-ajax.php';
			include_once UR_ABSPATH . 'includes/class-ur-query.php';
			include_once UR_ABSPATH . 'includes/class-ur-email-confirmation.php';
			include_once UR_ABSPATH . 'includes/class-ur-email-approval.php';
			include_once UR_ABSPATH . 'includes/class-ur-privacy.php';
			include_once UR_ABSPATH . 'includes/class-ur-form-block.php';
			include_once UR_ABSPATH . 'includes/class-ur-cache-helper.php';
			/**
			 * Block classes.
			 */
			include_once UR_ABSPATH . 'includes/blocks/class-ur-blocks.php';
			include_once UR_ABSPATH . 'includes/blocks/block-types/class-ur-block-abstract.php';
			include_once UR_ABSPATH . 'includes/blocks/block-types/class-ur-block-registration-form.php';
			include_once UR_ABSPATH . 'includes/blocks/block-types/class-ur-block-login-form.php';
			include_once UR_ABSPATH . 'includes/blocks/block-types/class-ur-block-myaccount.php';
			include_once UR_ABSPATH . 'includes/blocks/block-types/class-ur-block-edit-profile.php';
			include_once UR_ABSPATH . 'includes/blocks/block-types/class-ur-block-edit-password.php';

			// Validation classes.
			include_once UR_ABSPATH . 'includes/validation/class-ur-validation.php';
			include_once UR_ABSPATH . 'includes/validation/class-ur-form-validation.php';
			include_once UR_ABSPATH . 'includes/validation/class-ur-setting-validation.php';

			include_once UR_ABSPATH . 'includes/RestApi/class-ur-rest-api.php';

			/**
			 * Config classes.
			 */
			include_once UR_ABSPATH . 'includes/admin/class-ur-config.php';

			/**
			 * Plugin/Addon Updater.
			 */
			include_once UR_ABSPATH . 'includes/class-ur-plugin-updater.php';

			if ( $this->is_request( 'admin' ) ) {
				include_once UR_ABSPATH . 'includes/admin/class-ur-admin.php';
				include_once UR_ABSPATH . 'includes/abstracts/abstract-ur-meta-boxes.php';
			}

			if ( $this->is_request( 'frontend' ) ) {
				$this->frontend_includes();
			}

			if ( $this->is_request( 'frontend' ) || $this->is_request( 'cron' ) ) {
				include_once UR_ABSPATH . 'includes/class-ur-session-handler.php';
			}
			include_once UR_ABSPATH . 'includes/class-ur-cron.php';
			include_once UR_ABSPATH . 'includes/stats/class-ur-stats.php';

			$this->query = new UR_Query();
		}

		/**
		 * Include required frontend files.
		 */
		public function frontend_includes() {
			include_once UR_ABSPATH . 'includes/functions-ur-notice.php';
			include_once UR_ABSPATH . 'includes/class-ur-form-handler.php';                   // Form Handlers.
			include_once UR_ABSPATH . 'includes/class-ur-frontend-scripts.php';               // Frontend Scripts.
			include_once UR_ABSPATH . 'includes/frontend/class-ur-frontend.php';
			include_once UR_ABSPATH . 'includes/class-ur-preview.php';
		}

		/**
		 * Function used to Init UserRegistration Template Functions - This makes them pluggable by plugins and themes.
		 */
		public function include_template_functions() {
			include_once UR_ABSPATH . 'includes/functions-ur-template.php';
		}

		/**
		 * Setup Objects.
		 *
		 * @since 1.7.2
		 */
		public function objects() {
			$this->form = new UR_Form_Handler();
		}

		/**
		 * Init UserRegistration when WordPress Initialises.
		 */
		public function init() {
			// Before init action.
			do_action( 'before_user_registration_init' );

			// Set up localisation.
			$this->load_plugin_textdomain();

			// Session class, handles session data for users - can be overwritten if custom handler is needed.
			if ( $this->is_request( 'frontend' ) || $this->is_request( 'cron' ) || $this->is_request( 'admin' ) ) {
				$session_class = apply_filters( 'user_registration_session_handler', 'UR_Session_Handler' );
				$this->session = new $session_class();
			}

			// Init action.
			do_action( 'user_registration_init' );
		}

		/**
		 * Load Localisation files.
		 *
		 * Note: the first-loaded translation file overrides any following ones if the same translation is present.
		 *
		 * Locales found in:
		 *      - WP_LANG_DIR/user-registration/user-registration-LOCALE.mo
		 *      - WP_LANG_DIR/plugins/user-registration-LOCALE.mo
		 */
		public function load_plugin_textdomain() {
			$locale = is_admin() && function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
			$locale = apply_filters( 'plugin_locale', $locale, 'user-registration' );

			unload_textdomain( 'user-registration' );
			load_textdomain( 'user-registration', WP_LANG_DIR . '/user-registration/user-registration-' . $locale . '.mo' );
			load_plugin_textdomain( 'user-registration', false, plugin_basename( __DIR__ ) . '/languages' );
		}

		/**
		 * Get the plugin url.
		 *
		 * @return string
		 */
		public function plugin_url() {
			return untrailingslashit( plugins_url( '/', __FILE__ ) );
		}

		/**
		 * Get the plugin path.
		 *
		 * @return string
		 */
		public function plugin_path() {
			return untrailingslashit( plugin_dir_path( __FILE__ ) );
		}

		/**
		 * Get the template path.
		 *
		 * @return string
		 */
		public function template_path() {
			return apply_filters( 'user_registration_template_path', 'user-registration/' );
		}

		/**
		 * Get Ajax URL.
		 *
		 * @return string
		 */
		public function ajax_url() {
			return admin_url( 'admin-ajax.php', 'relative' );
		}

		/**
		 * Display action links in the Plugins list table.
		 *
		 * @param  array $actions Plugin Action links.
		 * @return array
		 */
		public static function plugin_action_links( $actions ) {
			$new_actions = array(
				'settings' => '<a href="' . admin_url( 'admin.php?page=user-registration-settings' ) . '" aria-label="' . esc_attr__( 'View User Registration settings', 'user-registration' ) . '">' . esc_html__( 'Settings', 'user-registration' ) . '</a>',
			);

			return array_merge( $new_actions, $actions );
		}

		/**
		 * Display row meta in the Plugins list table.
		 *
		 * @param  array  $plugin_meta Plugin Row Meta.
		 * @param  string $plugin_file Plugin Row Meta.
		 * @return array
		 */
		public static function plugin_row_meta( $plugin_meta, $plugin_file ) {
			if ( UR_PLUGIN_BASENAME === $plugin_file ) {
				$new_plugin_meta = array(
					'docs'    => '<a href="' . esc_url( apply_filters( 'user_registration_docs_url', 'https://docs.wpuserregistration.com/' ) ) . '" area-label="' . esc_attr__( 'View User Registration documentation', 'user-registration' ) . '">' . esc_html__( 'Docs', 'user-registration' ) . '</a>',
					'support' => '<a href="' . esc_url( apply_filters( 'user_registration_support_url', 'https://wpuserregistration.com/support/' ) ) . '" area-label="' . esc_attr__( 'Visit free customer support', 'user-registration' ) . '">' . __( 'Free support', 'user-registration' ) . '</a>',
				);

				return array_merge( $plugin_meta, $new_plugin_meta );
			}

			return (array) $plugin_meta;
		}
	}

endif;

/**
 * Check to see if UR already defined and resolve conflicts while installing PRO version.
 *
 * @since 2.0.4
 */

if ( ! function_exists( 'UR' ) ) {

	/**
	 * Main instance of UserRegistration.
	 *
	 * Returns the main instance of FT to prevent the need to use globals.
	 *
	 * @since  1.0.0
	 * @return UserRegistration
	 */
	function UR() {
		return UserRegistration::instance();
	}
} else {

	if ( ! function_exists( 'user_registration_pro_activated' ) ) {
		/**
		 * When Pro version is activated, deactivate free version.
		 */
		function user_registration_pro_activated() {
			set_transient( 'user_registration_pro_activated', true );
			user_registration_free_deactivate();
		}
	}
	add_action( 'activate_user-registration-pro/user-registration.php', 'user_registration_pro_activated' );

	if ( ! function_exists( 'user_registration_free_activated' ) ) {
		/**
		 * When user activates free version, set the value that is to be used to handle both Free and Pro activation conflict.
		 */
		function user_registration_free_activated() {

			set_transient( 'user_registration_free_activated', true );
		}
	}
	add_action( 'activate_user-registration/user-registration.php', 'user_registration_free_activated' );

	if ( ! function_exists( 'user_registration_free_deactivated' ) ) {
		/**
		 * When user deactivates free version, remove the value that was used to handle both Free and Pro activation conflict.
		 */
		function user_registration_free_deactivated() {

			global $user_registration_free_activated, $user_registration_free_deactivated;

			$user_registration_free_activated   = (bool) get_transient( 'user_registration_free_activated' );
			$user_registration_free_deactivated = true;

			delete_transient( 'user_registration_free_activated' );
		}
	}
	add_action( 'deactivate_user-registration/user-registration.php', 'user_registration_free_deactivated' );

	if ( ! function_exists( 'user_registration_free_deactivate' ) ) {
		/**
		 * Deactivate Free version if Pro is already activated.
		 *
		 * @since 1.0.0
		 */
		function user_registration_free_deactivate() {

			$plugin = 'user-registration/user-registration.php';

			deactivate_plugins( $plugin );
			do_action( 'user_registration_free_deactivate', $plugin );
			delete_transient( 'user_registration_pro_activated' );
		}
	}

	add_action( 'admin_init', 'user_registration_free_deactivate' );

	if ( ! function_exists( 'user_registration_free_notice' ) ) {
		/**
		 * When user wants to activate Free version alongside Pro, then display the message.
		 */
		function user_registration_free_notice() {

			global $user_registration_free_activated, $user_registration_free_deactivated;

			if (
				empty( $user_registration_free_activated ) ||
				empty( $user_registration_free_deactivated )
			) {
				return;
			}

			echo '<div class="notice-warning notice is-dismissible"><p>' . wp_kses_post( __( 'As <strong>User Registration Pro</strong> is active, <strong>User Registration Free</strong> is now not needed.', 'user-registration' ) ) . '</p></div>';

			if ( isset( $_GET['activate'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				unset( $_GET['activate'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			}

			unset( $user_registration_free_activated, $user_registration_free_deactivated );
		}
	}
	add_action( 'admin_notices', 'user_registration_free_notice' );

	// Do not process the plugin code further.
	return;
}
// Global for backwards compatibility.
$GLOBALS['user-registration'] = UR();

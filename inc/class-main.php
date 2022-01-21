<?php
/**
 * Loader.
 *
 * @package ThemeIsle
 */

namespace ThemeIsle\GutenbergBlocks;

/**
 * Class Main
 */
class Main {
	/**
	 * Flag to mark that the  FA has been loaded.
	 *
	 * @var bool $is_fa_loaded Is FA loaded?
	 */
	public static $is_fa_loaded = false;
	/**
	 * Define assets version.
	 *
	 * @var string $assets_version Holds assets version.
	 */
	public static $assets_version = null;

	/**
	 * Singleton.
	 *
	 * @var Main Class object.
	 */
	protected static $instance = null;

	/**
	 * Holds the module slug.
	 *
	 * @since   1.0.0
	 * @access  protected
	 * @var     string $slug The module slug.
	 */
	protected $slug = 'gutenberg-blocks';

	/**
	 * GutenbergBlocks constructor.
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function __construct() {
		$this->name        = __( 'Otter', 'otter-blocks' );
		$this->description = __( 'Blocks for Gutenberg', 'otter-blocks' );
	}

	/**
	 * Method to define hooks needed.
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function init() {
		if ( ! defined( 'THEMEISLE_BLOCKS_VERSION' ) ) {
			define( 'THEMEISLE_BLOCKS_VERSION', '1.7.0' );
			define( 'THEMEISLE_BLOCKS_DEV', false );
		}

		if ( THEMEISLE_BLOCKS_DEV ) {
			self::$assets_version = time();
		} else {
			self::$assets_version = THEMEISLE_BLOCKS_VERSION;
		}

		add_action( 'init', array( $this, 'autoload_classes' ), 9 );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ) ); // Don't change the priority or else Blocks CSS will stop working.
		add_action( 'enqueue_block_assets', array( $this, 'enqueue_block_frontend_assets' ) );
		add_filter( 'script_loader_tag', array( $this, 'filter_script_loader_tag' ), 10, 2 );

		add_action(
			'get_footer',
			static function () {
				if ( Main::$is_fa_loaded ) {
					wp_enqueue_style( 'font-awesome-5' );
					wp_enqueue_style( 'font-awesome-4-shims' );
				}
			}
		);
	}

	/**
	 * Autoload classes for each block.
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function autoload_classes() {
		load_plugin_textdomain( 'otter-blocks', false, basename( OTTER_BLOCKS_PATH ) . '/languages' );

		if ( class_exists( '\ThemeIsle\GutenbergBlocks\Registration' ) ) {
			\ThemeIsle\GutenbergBlocks\Registration::instance();
		}

		$classnames = array(
			'\ThemeIsle\GutenbergBlocks\CSS\Block_Frontend',
			'\ThemeIsle\GutenbergBlocks\CSS\CSS_Handler',
			'\ThemeIsle\GutenbergBlocks\Plugins\Block_Conditions',
			'\ThemeIsle\GutenbergBlocks\Plugins\Dashboard',
			'\ThemeIsle\GutenbergBlocks\Plugins\Options_Settings',
			'\ThemeIsle\GutenbergBlocks\Plugins\WooCommerce_Builder',
			'\ThemeIsle\GutenbergBlocks\Render\AMP\Circle_Counter_Block',
			'\ThemeIsle\GutenbergBlocks\Render\AMP\Lottie_Block',
			'\ThemeIsle\GutenbergBlocks\Render\AMP\Slider_Block',
			'\ThemeIsle\GutenbergBlocks\Render\Masonry_Variant',
			'\ThemeIsle\GutenbergBlocks\Server\Dashboard_Server',
			'\ThemeIsle\GutenbergBlocks\Server\Filter_Blocks_Server',
			'\ThemeIsle\GutenbergBlocks\Server\Plugin_Card_Server',
			'\ThemeIsle\GutenbergBlocks\Server\Template_Library_Server',
			'\ThemeIsle\GutenbergBlocks\Server\Form_Server',
		);

		foreach ( $classnames as $classname ) {
			$classname = new $classname();

			if ( method_exists( $classname, 'instance' ) ) {
				$classname->instance();
			}
		}

		if ( class_exists( '\ThemeIsle\GutenbergBlocks\Blocks_CSS' ) && get_option( 'themeisle_blocks_settings_css_module', true ) ) {
			\ThemeIsle\GutenbergBlocks\Blocks_CSS::instance();
		}

		if ( class_exists( '\ThemeIsle\GutenbergBlocks\Blocks_Animation' ) && get_option( 'themeisle_blocks_settings_blocks_animation', true ) ) {
			\ThemeIsle\GutenbergBlocks\Blocks_Animation::instance();
		}

		if ( class_exists( '\ThemeIsle\GutenbergBlocks\Blocks_Export_Import' ) ) {
			\ThemeIsle\GutenbergBlocks\Blocks_Export_Import::instance();
		}
	}

	/**
	 * Subscribe to FA enqueue.
	 *
	 * @param string $block_content Block content parsed.
	 * @param array  $block Block details.
	 *
	 * @return mixed
	 */
	public function subscribe_fa( $block_content, $block ) {
		if ( ! isset( $block['blockName'] ) ) {
			return $block_content;
		}

		if ( self::$is_fa_loaded ) {
			return $block_content;
		}

		// always load for those.
		static $always_load = [
			'themeisle-blocks/sharing-icons' => true,
			'themeisle-blocks/plugin-cards'  => true,
		];

		if ( isset( $always_load[ $block['blockName'] ] ) ) {
			self::$is_fa_loaded = true;

			return $block_content;
		}

		if ( 'themeisle-blocks/button' === $block['blockName'] ) {
			if ( isset( $block['attrs']['library'] ) && 'themeisle-icons' === $block['attrs']['library'] ) {
				return $block_content;
			}

			if ( isset( $block['attrs']['iconType'] ) ) {
				self::$is_fa_loaded = true;

				return $block_content;
			}
		}

		if ( 'themeisle-blocks/font-awesome-icons' === $block['blockName'] ) {
			if ( ! isset( $block['attrs']['library'] ) ) {
				self::$is_fa_loaded = true;

				return $block_content;
			}
		}

		if ( 'themeisle-blocks/icon-list-item' === $block['blockName'] ) {
			if ( ! isset( $block['attrs']['library'] ) ) {
				self::$is_fa_loaded = true;

				return $block_content;
			}

			if ( 'fontawesome' === $block['attrs']['library'] ) {
				self::$is_fa_loaded = true;

				return $block_content;
			}
		}

		$has_navigation_block = \WP_Block_Type_Registry::get_instance()->is_registered( 'core/navigation' );

		if ( $has_navigation_block && ( 'core/navigation-link' === $block['blockName'] || 'core/navigation-submenu' === $block['blockName'] ) ) {
			if ( isset( $block['attrs']['className'] ) && strpos( $block['attrs']['className'], 'fa-' ) !== false ) {
				self::$is_fa_loaded = true;

				return $block_content;
			}
		}

		return $block_content;
	}

	/**
	 * Load Gutenberg blocks.
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function enqueue_block_editor_assets() {
		if ( defined( 'THEMEISLE_GUTENBERG_GOOGLE_MAPS_API' ) ) {
			$api = THEMEISLE_GUTENBERG_GOOGLE_MAPS_API;
		} else {
			$api = false;
		}

		wp_enqueue_script(
			'otter-vendor',
			plugin_dir_url( $this->get_dir() ) . 'build/blocks/vendor.js',
			array( 'react', 'react-dom' ),
			self::$assets_version,
			true
		);

		$asset_file = include OTTER_BLOCKS_PATH . '/build/blocks/blocks.asset.php';

		$current_screen = get_current_screen();

		if ( 'widgets' === $current_screen->base ) {
			if ( in_array( 'wp-edit-post', $asset_file['dependencies'] ) ) {
				unset( $asset_file['dependencies'][ array_search( 'wp-editor', $asset_file['dependencies'] ) ] );
				unset( $asset_file['dependencies'][ array_search( 'wp-edit-post', $asset_file['dependencies'] ) ] );
			}
		}

		wp_enqueue_script(
			'otter-blocks',
			plugin_dir_url( $this->get_dir() ) . 'build/blocks/blocks.js',
			array_merge(
				$asset_file['dependencies'],
				array( 'otter-vendor', 'glidejs', 'lottie-player', 'macy' )
			),
			$asset_file['version'],
			true
		);

		global $pagenow;

		if ( class_exists( 'WooCommerce' ) && defined( 'NEVE_VERSION' ) && 'valid' === apply_filters( 'product_neve_license_status', false ) && true === apply_filters( 'neve_has_block_editor_module', false ) && ( 'post.php' === $pagenow || 'post-new.php' === $pagenow ) && ( ( isset( $_GET['post'] ) && 'product' === get_post_type( sanitize_text_field( $_GET['post'] ) ) ) || ( isset( $_GET['post_type'] ) && 'product' === sanitize_text_field( $_GET['post_type'] ) ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification
			$asset_file = include OTTER_BLOCKS_PATH . '/build/blocks/woocommerce.asset.php';

			wp_enqueue_script(
				'otter-blocks-woocommerce',
				plugin_dir_url( $this->get_dir() ) . 'build/blocks/woocommerce.js',
				$asset_file['dependencies'],
				$asset_file['version'],
				true
			);
		}

		wp_set_script_translations( 'otter-blocks', 'otter-blocks' );

		global $wp_roles;

		$default_fields = array();

		if ( class_exists( '\Neve_Pro\Modules\Woocommerce_Booster\Comparison_Table\Fields' ) ) {
			$fields         = new \Neve_Pro\Modules\Woocommerce_Booster\Comparison_Table\Fields();
			$default_fields = wp_json_encode( array_keys( ( $fields->get_fields() ) ) );
		}

		wp_localize_script(
			'otter-blocks',
			'themeisleGutenberg',
			array(
				'isCompatible'   => $this->is_compatible(),
				'packagePath'    => plugin_dir_url( $this->get_dir() ) . 'build/blocks/',
				'assetsPath'     => plugin_dir_url( $this->get_dir() ) . 'assets',
				'updatePath'     => admin_url( 'update-core.php' ),
				'optionsPath'    => admin_url( 'options-general.php?page=otter' ),
				'mapsAPI'        => $api,
				'themeDefaults'  => $this->get_global_defaults(),
				'imageSizes'     => function_exists( 'is_wpcom_vip' ) ? array( 'thumbnail', 'medium', 'medium_large', 'large' ) : get_intermediate_image_sizes(), // phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.get_intermediate_image_sizes_get_intermediate_image_sizes
				'themeMods'      => array(
					'listingType'   => get_theme_mod( 'neve_comparison_table_product_listing_type', 'column' ),
					'altRow'        => get_theme_mod( 'neve_comparison_table_enable_alternating_row_bg_color', false ),
					'fields'        => get_theme_mod( 'neve_comparison_table_fields', $default_fields ),
					'rowColor'      => get_theme_mod( 'neve_comparison_table_rows_background_color', 'var(--nv-site-bg)' ),
					'headerColor'   => get_theme_mod( 'neve_comparison_table_header_text_color', 'var(--nv-text-color)' ),
					'textColor'     => get_theme_mod( 'neve_comparison_table_text_color', 'var(--nv-text-color)' ),
					'borderColor'   => get_theme_mod( 'neve_comparison_table_borders_color', '#BDC7CB' ),
					'altRowColor'   => get_theme_mod( 'neve_comparison_table_alternate_row_bg_color', 'var(--nv-light-bg)' ),
					'defaultFields' => $default_fields,
				),
				'isWPVIP'        => function_exists( 'is_wpcom_vip' ),
				'canTrack'       => 'yes' === get_option( 'otter_blocks_logger_flag', false ) ? true : false,
				'userRoles'      => $wp_roles->roles,
				'hasWooCommerce' => class_exists( 'WooCommerce' ),
				'hasLearnDash'   => defined( 'LEARNDASH_VERSION' ),
				'hasNeveSupport' => array(
					'hasNeve'         => defined( 'NEVE_VERSION' ),
					'hasNevePro'      => defined( 'NEVE_VERSION' ) && 'valid' === apply_filters( 'product_neve_license_status', false ),
					'isBoosterActive' => 'valid' === apply_filters( 'product_neve_license_status', false ) && true === apply_filters( 'neve_has_block_editor_module', false ),
					'wooComparison'   => class_exists( '\Neve_Pro\Modules\Woocommerce_Booster\Comparison_Table\Options' ) ? \Neve_Pro\Modules\Woocommerce_Booster\Comparison_Table\Options::is_module_activated() : false,
					'optionsPage'     => admin_url( 'themes.php?page=neve-welcome' ),
				),
				'isBlockEditor'  => 'post' === $current_screen->base,
			)
		);

		wp_enqueue_style(
			'otter-editor',
			plugin_dir_url( $this->get_dir() ) . 'build/blocks/editor.css',
			array( 'wp-edit-blocks' ),
			$asset_file['version']
		);

		wp_enqueue_script(
			'macy',
			plugin_dir_url( $this->get_dir() ) . 'assets/macy/macy.js',
			[],
			self::$assets_version,
			true
		);
	}

	/**
	 * Load assets for our blocks.
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function enqueue_block_frontend_assets() {
		wp_register_style( 'font-awesome-5', plugin_dir_url( $this->get_dir() ) . 'assets/fontawesome/css/all.min.css', [], OTTER_BLOCKS_VERSION );
		wp_register_style( 'font-awesome-4-shims', plugin_dir_url( $this->get_dir() ) . 'assets/fontawesome/css/v4-shims.min.css', [], OTTER_BLOCKS_VERSION );

		if ( is_admin() ) {
			wp_enqueue_style( 'font-awesome-5' );
			wp_enqueue_style( 'font-awesome-4-shims' );
			return;
		}
	}

	/**
	 * Get if the version of plugin in latest.
	 *
	 * @since   1.2.0
	 * @access  public
	 */
	public function is_compatible() {
		if ( ! function_exists( 'plugins_api' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
		}

		if ( ! defined( 'OTTER_BLOCKS_VERSION' ) ) {
			return true;
		}

		$current = OTTER_BLOCKS_VERSION;

		$args = array(
			'slug'   => 'otter-blocks',
			'fields' => array(
				'version' => true,
			),
		);

		$call_api = plugins_api( 'plugin_information', $args );

		if ( is_wp_error( $call_api ) ) {
			return true;
		} else {
			if ( ! empty( $call_api->version ) ) {
				$latest = $call_api->version;
			}
		}

		return version_compare( $current, $latest, '>=' );
	}

	/**
	 * Get global defaults.
	 *
	 * @since   1.4.0
	 * @access  public
	 */
	public function get_global_defaults() {
		$defaults = get_theme_support( 'otter_global_defaults' );
		if ( ! is_array( $defaults ) ) {
			return false;
		}

		return current( $defaults );
	}

	/**
	 * Get currency symbol
	 *
	 * @param string $currency Currency.
	 *
	 * @return string
	 */
	public static function get_currency( $currency = 'USD' ) {
		$symbols = apply_filters(
			'themeisle_gutenberg_currency_symbols',
			array(
				'AED' => '&#x62f;.&#x625;',
				'AFN' => '&#x60b;',
				'ALL' => 'L',
				'AMD' => 'AMD',
				'ANG' => '&fnof;',
				'AOA' => 'Kz',
				'ARS' => '&#36;',
				'AUD' => '&#36;',
				'AWG' => 'Afl.',
				'AZN' => 'AZN',
				'BAM' => 'KM',
				'BBD' => '&#36;',
				'BDT' => '&#2547;&nbsp;',
				'BGN' => '&#1083;&#1074;.',
				'BHD' => '.&#x62f;.&#x628;',
				'BIF' => 'Fr',
				'BMD' => '&#36;',
				'BND' => '&#36;',
				'BOB' => 'Bs.',
				'BRL' => '&#82;&#36;',
				'BSD' => '&#36;',
				'BTC' => '&#3647;',
				'BTN' => 'Nu.',
				'BWP' => 'P',
				'BYR' => 'Br',
				'BYN' => 'Br',
				'BZD' => '&#36;',
				'CAD' => '&#36;',
				'CDF' => 'Fr',
				'CHF' => '&#67;&#72;&#70;',
				'CLP' => '&#36;',
				'CNY' => '&yen;',
				'COP' => '&#36;',
				'CRC' => '&#x20a1;',
				'CUC' => '&#36;',
				'CUP' => '&#36;',
				'CVE' => '&#36;',
				'CZK' => '&#75;&#269;',
				'DJF' => 'Fr',
				'DKK' => 'DKK',
				'DOP' => 'RD&#36;',
				'DZD' => '&#x62f;.&#x62c;',
				'EGP' => 'EGP',
				'ERN' => 'Nfk',
				'ETB' => 'Br',
				'EUR' => '&euro;',
				'FJD' => '&#36;',
				'FKP' => '&pound;',
				'GBP' => '&pound;',
				'GEL' => '&#x20be;',
				'GGP' => '&pound;',
				'GHS' => '&#x20b5;',
				'GIP' => '&pound;',
				'GMD' => 'D',
				'GNF' => 'Fr',
				'GTQ' => 'Q',
				'GYD' => '&#36;',
				'HKD' => '&#36;',
				'HNL' => 'L',
				'HRK' => 'kn',
				'HTG' => 'G',
				'HUF' => '&#70;&#116;',
				'IDR' => 'Rp',
				'ILS' => '&#8362;',
				'IMP' => '&pound;',
				'INR' => '&#8377;',
				'IQD' => '&#x639;.&#x62f;',
				'IRR' => '&#xfdfc;',
				'IRT' => '&#x062A;&#x0648;&#x0645;&#x0627;&#x0646;',
				'ISK' => 'kr.',
				'JEP' => '&pound;',
				'JMD' => '&#36;',
				'JOD' => '&#x62f;.&#x627;',
				'JPY' => '&yen;',
				'KES' => 'KSh',
				'KGS' => '&#x441;&#x43e;&#x43c;',
				'KHR' => '&#x17db;',
				'KMF' => 'Fr',
				'KPW' => '&#x20a9;',
				'KRW' => '&#8361;',
				'KWD' => '&#x62f;.&#x643;',
				'KYD' => '&#36;',
				'KZT' => '&#8376;',
				'LAK' => '&#8365;',
				'LBP' => '&#x644;.&#x644;',
				'LKR' => '&#xdbb;&#xdd4;',
				'LRD' => '&#36;',
				'LSL' => 'L',
				'LYD' => '&#x644;.&#x62f;',
				'MAD' => '&#x62f;.&#x645;.',
				'MDL' => 'MDL',
				'MGA' => 'Ar',
				'MKD' => '&#x434;&#x435;&#x43d;',
				'MMK' => 'Ks',
				'MNT' => '&#x20ae;',
				'MOP' => 'P',
				'MRU' => 'UM',
				'MUR' => '&#x20a8;',
				'MVR' => '.&#x783;',
				'MWK' => 'MK',
				'MXN' => '&#36;',
				'MYR' => '&#82;&#77;',
				'MZN' => 'MT',
				'NAD' => 'N&#36;',
				'NGN' => '&#8358;',
				'NIO' => 'C&#36;',
				'NOK' => '&#107;&#114;',
				'NPR' => '&#8360;',
				'NZD' => '&#36;',
				'OMR' => '&#x631;.&#x639;.',
				'PAB' => 'B/.',
				'PEN' => 'S/',
				'PGK' => 'K',
				'PHP' => '&#8369;',
				'PKR' => '&#8360;',
				'PLN' => '&#122;&#322;',
				'PRB' => '&#x440;.',
				'PYG' => '&#8370;',
				'QAR' => '&#x631;.&#x642;',
				'RMB' => '&yen;',
				'RON' => 'lei',
				'RSD' => '&#1088;&#1089;&#1076;',
				'RUB' => '&#8381;',
				'RWF' => 'Fr',
				'SAR' => '&#x631;.&#x633;',
				'SBD' => '&#36;',
				'SCR' => '&#x20a8;',
				'SDG' => '&#x62c;.&#x633;.',
				'SEK' => '&#107;&#114;',
				'SGD' => '&#36;',
				'SHP' => '&pound;',
				'SLL' => 'Le',
				'SOS' => 'Sh',
				'SRD' => '&#36;',
				'SSP' => '&pound;',
				'STN' => 'Db',
				'SYP' => '&#x644;.&#x633;',
				'SZL' => 'L',
				'THB' => '&#3647;',
				'TJS' => '&#x405;&#x41c;',
				'TMT' => 'm',
				'TND' => '&#x62f;.&#x62a;',
				'TOP' => 'T&#36;',
				'TRY' => '&#8378;',
				'TTD' => '&#36;',
				'TWD' => '&#78;&#84;&#36;',
				'TZS' => 'Sh',
				'UAH' => '&#8372;',
				'UGX' => 'UGX',
				'USD' => '&#36;',
				'UYU' => '&#36;',
				'UZS' => 'UZS',
				'VEF' => 'Bs F',
				'VES' => 'Bs.S',
				'VND' => '&#8363;',
				'VUV' => 'Vt',
				'WST' => 'T',
				'XAF' => 'CFA',
				'XCD' => '&#36;',
				'XOF' => 'CFA',
				'XPF' => 'Fr',
				'YER' => '&#xfdfc;',
				'ZAR' => '&#82;',
				'ZMW' => 'ZK',
			)
		);

		$symbol = isset( $symbols[ $currency ] ) ? $symbols[ $currency ] : '&#36;';

		return $symbol;
	}

	/**
	 * Adds async/defer attributes to enqueued / registered scripts.
	 *
	 * If #12009 lands in WordPress, this function can no-op since it would be handled in core.
	 *
	 * @link https://core.trac.wordpress.org/ticket/12009
	 *
	 * @param string $tag The script tag.
	 * @param string $handle The script handle.
	 *
	 * @return string Script HTML string.
	 */
	public function filter_script_loader_tag( $tag, $handle ) {
		foreach ( array( 'async', 'defer' ) as $attr ) {
			if ( ! wp_scripts()->get_data( $handle, $attr ) ) {
				continue;
			}
			// Prevent adding attribute when already added in #12009.
			if ( ! preg_match( ":\s$attr(=|>|\s):", $tag ) ) {
				$tag = preg_replace( ':(?=></script>):', " $attr", $tag, 1 );
			}
			// Only allow async or defer, not both.
			break;
		}

		return $tag;
	}

	/**
	 * Method to return path to child class in a Reflective Way.
	 *
	 * @return  string
	 * @since   1.0.0
	 * @access  protected
	 */
	protected function get_dir() {
		return dirname( __FILE__ );
	}

	/**
	 * Singleton method.
	 *
	 * @static
	 *
	 * @return  GutenbergBlocks
	 * @since   1.0.0
	 * @access  public
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
			self::$instance->init();
		}

		return self::$instance;
	}

	/**
	 * Throw error on object clone
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @access  public
	 * @return  void
	 * @since   1.0.0
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, 'Cheatin&#8217; huh?', '1.0.0' );
	}

	/**
	 * Disable unserializing of the class
	 *
	 * @access  public
	 * @return  void
	 * @since   1.0.0
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, 'Cheatin&#8217; huh?', '1.0.0' );
	}
}

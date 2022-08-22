<?php

class RegRuDomains_Admin
{

    /**
     * @var string 
     */
    public static $option_name = 'regru_domains';

    public static $slug = 'regru_domains';

    /**
     * @return void
     */
    public function register_hooks()
    {
        if (is_admin()){
            add_action( 'admin_init', array( 'RegRuDomains_Admin', 'register_options' ) );
            add_action( 'admin_menu', array( 'RegRuDomains_Admin', 'admin_menu' ) );

            if ( isset( $_GET['page'] ) && $_GET['page'] === self::$slug ) {
                add_action( 'admin_enqueue_scripts', array( 'RegRuDomains_Admin', 'load_admin_head_styles' ) );
            }

            add_action( 'wp_ajax_regru_save_preference', array( 'RegRuDomains_Admin', 'save_preferences' ) );
            add_action( 'wp_ajax_regru_get_preference', array( 'RegRuDomains_Admin', 'get_preferences' ) );
            add_filter( 'plugin_row_meta', array( 'RegRuDomains_Admin', 'custom_plugin_row_meta' ), 10, 2 );
        }

//        add_action( 'add_attachment', array( 'YandexDiskLibrary_Admin', 'auto_post_after_image_upload' ) );
    }

    /**
     * @return void
     */
	public static function register_options() 
    {
		register_setting( self::$option_name, self::$option_name );
	}

    /**
     * @param $links
     * @param $file
     * @return array|mixed
     */
	public static function custom_plugin_row_meta( $links, $file ) 
    {
		if ( strpos( $file, basename( REGRU_DOMAINS_PLUGIN_FILE ) ) !== false ) {
			$new_links = array(
				'configuration' => '<a href="' . admin_url( "admin.php?page=" . self::$slug ) . '">Settings</a>'
			);
			$links = array_merge( $links, $new_links );
		}

		return $links;
	}

    /**
     * Load assets
     * @return void
     */
	public static function load_admin_head_styles() 
    {
//		wp_register_style( 'bootstrap.apaiu', plugins_url( 'assets/css/bootstrap-apaiu.css', REGRU_DOMAINS_PLUGIN_FILE ), false, REGRU_DOMAINS_PLUGIN_VERSION );
//		wp_enqueue_style( 'bootstrap.apaiu' );
//
//		wp_register_style( 'sweetalert2.min.apaiu', plugins_url( 'assets/css/sweetalert2.min.css', REGRU_DOMAINS_PLUGIN_FILE ), array( "bootstrap.apaiu" ), REGRU_DOMAINS_PLUGIN_VERSION );
//		wp_enqueue_style( 'sweetalert2.min.apaiu' );
//
//		wp_register_style( 'apaip.main', plugins_url( 'assets/css/main.css', REGRU_DOMAINS_PLUGIN_FILE ), array( "bootstrap.apaiu" ), REGRU_DOMAINS_PLUGIN_VERSION );
//		wp_enqueue_style( 'apaip.main' );
//
//		wp_register_script( 'popperjs.apaiu', plugins_url( 'assets/js/popper.min.js', REGRU_DOMAINS_PLUGIN_FILE ), array( 'jquery' ), REGRU_DOMAINS_PLUGIN_VERSION, true );
//		wp_enqueue_script( "popperjs.apaiu" );
//
//		wp_register_script( 'bootstrap.min.apaiu', plugins_url( 'assets/js/bootstrap.min.js', REGRU_DOMAINS_PLUGIN_FILE ), array( 'jquery' ), REGRU_DOMAINS_PLUGIN_VERSION, true );
//		wp_enqueue_script( 'bootstrap.min.apaiu' );
//
//		wp_register_script( 'sweetalert2.min.apaiu', plugins_url( 'assets/js/sweetalert2.min.js', REGRU_DOMAINS_PLUGIN_FILE ), array( 'bootstrap.min.apaiu' ), REGRU_DOMAINS_PLUGIN_VERSION, true );
//		wp_enqueue_script( 'sweetalert2.min.apaiu' );
//
//		wp_register_script( 'apaip.main', plugins_url( 'assets/js/main.js', REGRU_DOMAINS_PLUGIN_FILE ), array(
//			'bootstrap.min.apaiu'
//		), REGRU_DOMAINS_PLUGIN_VERSION, true );
//		wp_enqueue_script( 'apaip.main' );
	}

    /**
     * Create admin menu
     * @return void
     */
	public static function admin_menu() 
    {
		$menuItems = [
			[
				'page_title'        => "REG.RU DOMAINS",
				'menu_title'        => "REG.RU DOMAINS",
				'capabilities'      => 'manage_options',
				'menu_slug'         => self::$slug,
				'callback_function' => array( 'RegRuDomains_Admin', 'admin_page_display' ),
				'menu_icon'         => "dashicons-admin-multisite",
			]
		];
		foreach ( $menuItems as $item ) {
			add_options_page( $item['page_title'], $item['menu_title'], $item['capabilities'], $item['menu_slug'], $item['callback_function']);
		}
	}

    /**
     * Save preferences
     * @return void
     */
	public static function save_preferences()
    {
	    $prfs = unserialize(get_option(self::$option_name, ""));

	    $postData = $_POST;
	    $pConfigs = json_decode(stripslashes($postData['configs']), true);

	    $preferences = [];

	    update_option(self::$option_name, serialize($preferences));

	    wp_send_json_success(['success' => true, 'prefs' => unserialize(get_option(self::$option_name, []))]);
	    exit();
	}

    /**
     * Get properties
     * @return void
     */
	public static function get_preferences()
    {
	    wp_send_json_success(['success' => true, 'prefs' => unserialize(get_option(self::$option_name, []))]);
	    exit();
	}

    /**
     * Display admin menu
     * @return void
     */
    public static function admin_page_display()
    {
        $depth = 1;
        $proxy_filter = new \App\Library\Proxy\Parser\Engines\HidemyNameFilter();
        $proxy_filter->setParameters([
            'countries' => [
                \App\Library\Proxy\Parser\Engines\HidemyNameFilter::FILTER_COUNTRY_RUSSIA,
                \App\Library\Proxy\Parser\Engines\HidemyNameFilter::FILTER_COUNTRY_TRINIDAD_AND_TOBAGO,
            ],
            'types' => [
                \App\Library\Proxy\Parser\Engines\HidemyNameFilter::FILTER_PROXY_TYPE_HTTP,
                \App\Library\Proxy\Parser\Engines\HidemyNameFilter::FILTER_PROXY_TYPE_HTTPS,
            ],
            'anon' => [
                \App\Library\Proxy\Parser\Engines\HidemyNameFilter::FILTER_ANON_NONE,
            ],
            'mixtime' => 500, // мс
        ]);
        $proxy_filter->setDepth($depth);

        $proxy_engine = new \App\Library\Proxy\Parser\Engines\HidemyNameParser();
        $proxy_engine->setFilters($proxy_filter);

        $proxy_parser = new \App\Library\Proxy\Parser\ProxyListParser($proxy_engine);
        $proxies = $proxy_parser->parse($depth);


        dump($proxies);
    }

}

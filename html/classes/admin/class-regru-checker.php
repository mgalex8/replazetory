<?php

//require_once __DIR__ . '/basic/class-oauth-grant.php';
//require_once __DIR__ . '/basic/class-oauth-revoke.php';
//require_once __DIR__ . '/basic/class-root-selection.php';

/**
 * Registers and renders the basic settings page.
 */
class RegRuDomains_Admin_Pages_RegruChecker
{

    /**
     * @var string
     */
    protected $slug = 'regru_domains_proxy_list';

	/**
	 * Register all the hooks for the page.
	 */
	public function __construct()
    {
		if ( ! is_admin() ) {
			return;
		}

		add_action( 'admin_menu', array( self::class, 'add_page' ) );
	}

	/**
	 * Adds the settings page to administration.
	 * @return void
	 */
	public static function add_page()
    {
		add_submenu_page(
            $parent_slug  = 'regru_domains', __( 'REGRU DOMAINS Checker 1', 'regru_domains' ),
            $page_title   = esc_html__( 'REGRU DOMAINS Checker 2', 'regru_domains' ),
            $capability   = 'manage_options',
            $menu_slug    = self::$slug,
            $function     = array( __CLASS__, 'html' )
        );
	}

	/**
	 * Renders the settings page.
	 * @return void
	 */
	public static function html()
    {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$help_link = 'https://napoveda.skaut.cz/dobryweb/' . substr( get_locale(), 0, 2 ) . '-skaut-google-drive-gallery';
		/* translators: 1: Start of a help link 2: End of the help link */
		add_settings_error( 'general', 'help', sprintf( esc_html__( 'See the %1$sdocumentation%2$s for more information about how to configure the plugin.', 'skaut-google-drive-gallery' ), '<a href="' . esc_url( $help_link ) . '" target="_blank">', '</a>' ), 'notice-info' );

		settings_errors();
		echo( '<div class="wrap">' );
		echo( '<h1>' . esc_html( get_admin_page_title() ) . '</h1>' );
		echo( '<form action="options.php?action=update&option_page=sgdg_basic" method="post">' );
		wp_nonce_field( 'sgdg_basic-options' );
		do_settings_sections( 'sgdg_basic' );
		submit_button( esc_html__( 'Save Changes', 'skaut-google-drive-gallery' ) );
		echo( '</form>' );
		echo( '</div>' );
	}
}

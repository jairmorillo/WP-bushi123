<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://cylwebservices.com
 * @since      1.0.0
 *
 * @package    Wp_Crawller_Plugin
 * @subpackage Wp_Crawller_Plugin/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wp_Crawller_Plugin
 * @subpackage Wp_Crawller_Plugin/admin
 * @author     Jair Morillo <jairantoniom@gmail.com>
 */
class Wp_Crawller_Plugin_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		add_action('admin_menu', array( $this, 'addPluginAdminMenu' ), 9);   
		add_action('admin_init', array( $this, 'registerAndBuildFields' ));
		$this->setDefaulOptions(); 
        

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_Crawller_Plugin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Crawller_Plugin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-crawller-plugin-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_Crawller_Plugin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Crawller_Plugin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wp-crawller-plugin-admin.js', array( 'jquery' ), $this->version, false );

	}



	public function addPluginAdminMenu() {
		//add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
		add_menu_page(  $this->plugin_name, 'WP bushi123', 'administrator', $this->plugin_name, array( $this, 'displayPluginAdminSettings' ), 'dashicons-admin-settings', 26 );
		
		//add_submenu_page( '$parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
		//add_submenu_page( $this->plugin_name, 'WP Szwego Settings', 'Settings', 'administrator', $this->plugin_name.'-settings', array( $this, 'displayPluginAdminSettings' ));
	}

	public function displayPluginAdminDashboard() {
		//require_once 'partials'.$this->plugin_name.'-admin-display.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/wp-crawller-plugin-admin-display.php';
 	}

	 public function displayPluginAdminSettings() {
		// set this var to be used in the settings-display view
		$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'general';
		if(isset($_GET['error_message'])){
			add_action('admin_notices', array($this,'pluginNameSettingsMessages'));
			do_action( 'admin_notices', $_GET['error_message'] );
		}

		//require_once 'partials/'.$this->plugin_name.'-admin-settings-display.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/wp-crawller-plugin-admin-settings-display.php';

	}


	public function pluginNameSettingsMessages($error_message){
		switch ($error_message) {
			case '1':
				$message = __( 'There was an error adding this setting. Please try again.  If this persists, shoot us an email.', 'my-text-domain' );                 
				$err_code = esc_attr( 'plugin_wp-crawller-plugin_setting' );                 
				$setting_field = 'plugin_wp-crawller-plugin_setting';                 
				break;
		}
		$type = 'error';
		add_settings_error(
			   $setting_field,
			   $err_code,
			   $message,
			   $type
		   );
	}

	public function settingsPageSettingsMessages($error_message){
		switch ($error_message) {
				case '1':
						$message = __( 'There was an error adding this setting. Please try again.  If this persists, shoot us an email.', 'my-text-domain' );                 $err_code = esc_attr( 'url_setting' );                 $setting_field = 'url_setting';                 
						break;
		}
		$type = 'error';
		add_settings_error(
					$setting_field,
					$err_code,
					$message,
					$type
			);
	}
	public function registerAndBuildFields() {
		/**
		 * First, we add_settings_section. This is necessary since all future settings must belong to one.
		 * Second, add_settings_field
		 * Third, register_setting
		 */     
		add_settings_section( 
			// ID used to identify this section and with which to register options
			'settings_page_general_section', 
			// Title to be displayed on the administration page
			'',  
			// Callback used to render the description of the section
				array( $this, 'settings_page_display_general_account' ),    
			// Page on which to add this section of options
			'settings_page_general_settings'                   
		);
		unset($args);
				$args = array (
									'type'      => 'input',
									'subtype'   => 'text',
									'id'    => 'url_setting',
									'name'      => 'url_setting',
									'required' => 'true',
									'get_options_list' => '',
									'value_type'=>'normal',
									'wp_data' => 'option'
							);

							$argskey = array (
								'type'      => 'input',
								'subtype'   => 'text',
								'id'    => 'keyword_setting',
								'name'      => 'keyword_setting',
								'required' => 'true',
								'get_options_list' => '',
								'value_type'=>'normal',
								'wp_data' => 'option'
						);
				$argscheckbox = array (
						'id'    => 'show_titles_checkbox',
						'name'      => 'show_titles_checkbox',
						'required' => 'true'					
				);

				$argscheckbox2 = array (
					'id'    => 'allow_link_to_albums',
					'name'      => 'allow_link_to_albums',
					'required' => 'true'					
			);

			$argspass = array (
				'type'      => 'input',
				'subtype'   => 'text',
				'id'    => 'pass_setting',
				'name'      => 'pass_setting',
				'required' => 'true',
				'get_options_list' => '',
				'value_type'=>'normal',
				'wp_data' => 'option'
		    );

			$argsuser = array (
				'type'      => 'input',
				'subtype'   => 'text',
				'id'    => 'user_setting',
				'name'      => 'user_setting',
				'required' => 'true',
				'get_options_list' => '',
				'value_type'=>'normal',
				'wp_data' => 'option'
		   );


		   add_settings_field(
			'user_setting',
			'User Setting',
			array( $this, 'user_setting_input_callback' ),
			'settings_page_general_settings',
			'settings_page_general_section',
			$argsuser
		); 


		add_settings_field(
			'pass_setting',
			'Pass Setting',
			array( $this, 'pass_setting_input_callback' ),
			'settings_page_general_settings',
			'settings_page_general_section',
			$argspass
		); 





		add_settings_field(
			'url_setting',
			'Url Setting',
			array( $this, 'url_setting_input_callback' ),
			'settings_page_general_settings',
			'settings_page_general_section',
			$args
		); 

		add_settings_field(
			'keyword_setting',
			'keyword Setting',
			array( $this, 'keyword_setting_input_callback' ),
			'settings_page_general_settings',
			'settings_page_general_section',
			$argskey
		);

		add_settings_field(
			'show_titles_checkbox',
			'Show titles',
			array( $this, 'show_titles_checkbox_callback' ),
			'settings_page_general_settings',
			'settings_page_general_section',
			$argscheckbox
		);

		add_settings_field(
			'allow_link_to_albums',
			'Allow link to albums',
			array( $this, 'allow_link_to_albums_checkbox_callback' ),
			'settings_page_general_settings',
			'settings_page_general_section',
			$argscheckbox2
		);



		register_setting(
			'settings_page_general_settings',
			'user_setting'
			);

		register_setting(
			'settings_page_general_settings',
			'pass_setting'
			);

		register_setting(
						'settings_page_general_settings',
						'url_setting'
						);
		register_setting(
							'settings_page_general_settings',
							'keyword_setting'
							);
		register_setting(
							'settings_page_general_settings',
							'show_titles_checkbox'
							);

		register_setting(
				'settings_page_general_settings',
				'allow_link_to_albums'
						);

	}


	public function user_setting_input_callback($args) {
		$value = get_option($args['id']); 
		echo '<input type="text" id="' . $args['id'] . '" name="' . $args['id'] . '" value="' . $value . '">';
	}

	public function pass_setting_input_callback($args) {
		$value = get_option($args['id']); 
		echo '<input type="text" id="' . $args['id'] . '" name="' . $args['id'] . '" value="' . $value . '">';
	}
	
	
	public function url_setting_input_callback($args) {
		$value = get_option($args['id']); 
		echo '<input type="text" id="' . $args['id'] . '" name="' . $args['id'] . '" value="' . $value . '">';
	}


	public function keyword_setting_input_callback($args) {
		$value = get_option($args['id']); 
		echo '<input type="text" id="' . $args['id'] . '" name="' . $args['id'] . '" value="' . $value . '">';
	}
	

	function show_titles_checkbox_callback($args) {
		$value = get_option($args['id']); 
		$checked = $value ? 'checked' : '';
		echo '<input type="checkbox" id="' . $args['id'] . '" name="' . $args['id'] . '" value="1" ' . $checked . '>';
	}


	function allow_link_to_albums_checkbox_callback($args) {
		$value = get_option($args['id']); 
		$checked = $value ? 'checked' : '';
		echo '<input type="checkbox" id="' . $args['id'] . '" name="' . $args['id'] . '" value="1" ' . $checked . '>';
	}

	public function settings_page_display_general_account() {
		echo '<p>These settings apply to all Plugin WP bushi123 functionality.</p>';
		echo'<p>To display the gallery, copy and paste this shortcode where you want the gallery to be displayed</p>';
		echo '<code> [render_list_image] </code>';
	} 


	public function setDefaulOptions(){

		if ( get_option( 'url_setting' ) === false ){
			update_option( 'url_setting', 'https://ss.bushi123.cn/');
		}
		
		if ( get_option( 'show_titles_checkbox' ) === false ){
			update_option( 'show_titles_checkbox', 1);
		}

		if ( get_option( 'allow_link_to_albums' ) === false ){
			update_option( 'allow_link_to_albums', 1);
		}

	}

}

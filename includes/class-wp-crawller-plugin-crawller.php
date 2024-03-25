<?php

/**
 * Fired during plugin activation
 *
 * @link       https://cylwebservices.com
 * @since      1.0.0
 *
 * @package    Wp_Crawller_Plugin
 * @subpackage Wp_Crawller_Plugin/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Wp_Crawller_Plugin
 * @subpackage Wp_Crawller_Plugin/includes
 * @author     Jair Morillo <jairantoniom@gmail.com>
 */
class Wp_Crawller_Plugin_Core {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */


	 public function __construct()
	 {
		 $this->make_Wp_Crawller_Plugin_shortcode();
		 $this->add_query_to_filter();
	 }

	 
	private function get_gallery_info($url,$page,$key){

		$result = [];
		$user = '';
		$pass = '';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'vendor/autoload.php';
		if( get_option('user_setting') !=false && get_option('pass_setting') !=false){

			$user = get_option('user_setting');
			$pass = get_option('pass_setting');

			if($key != ''){
				$key=$key;
			}
						
			$html = $url;	
			$web = new \Spekulatius\PHPScraper\PHPScraper; 
			$web->go($html);
		
			// go to link - login page
			$url_to_go = $web->links()[1];
			$click = $web->clickLink($url_to_go);
	
			// Login with username and password
			$form = $click->findButton('imageField');
			$click = $web->submitForm($form, ['username' => $user, 'passwd' => $pass]);
			
			// Search for specific list	of images
			$search_button = 'button';
			$search_form =$web->findFirstForm($search_button,['keyword'=>$key],'GET');
			
	
			if($page > 1 && !empty($key) && !is_null($key)){
				$url_by_key = $search_form->getUri();	//?page='.$page.'&keyword='.$key.'&cid=0';
				$url_by_page_key = str_replace('?keyword','?page='.$page.'&keyword',$url_by_key);
				$go_click = $web->clickLink($url_by_page_key);
		
				$images =  $go_click->filter("//div[@class ='main-table-and-page-wrapper']/div[@class='tusou-table-old']/table/tbody/tr/td[@class='xuhao']/img")->images(); 
				$titles = $go_click->filterTexts("//div[@class ='main-table-and-page-wrapper']/div[@class='tusou-table-old']/table/tbody/tr/td[@class='xclj']");
				$albumUrl = $go_click->filter("//div[@class ='main-table-and-page-wrapper']/div[@class='tusou-table-old']/table/tbody/tr/td[@class='xclj']/a")->links();
			}else{
				$images =  $web->filter("//div[@class ='main-table-and-page-wrapper']/div[@class='tusou-table-old']/table/tbody/tr/td[@class='xuhao']/img")->images(); 
				$titles = $web->filterTexts("//div[@class ='main-table-and-page-wrapper']/div[@class='tusou-table-old']/table/tbody/tr/td[@class='xclj']");
				$albumUrl = $web->filter("//div[@class ='main-table-and-page-wrapper']/div[@class='tusou-table-old']/table/tbody/tr/td[@class='xclj']/a")->links();
			}
	
	
			foreach ($images as $image=>$value) {
				$result[] = [
					'Name'=>$titles [$image],
					'ImagenUrl'=> $value->getUri(),
					'albumUrl'=>$albumUrl[$image]->getUri()
				];
			}

		}else{
            $result = [];
		}
		
		
		return  $result;     
	}


   public function download_from_url( $url, $title = null){
	require_once( ABSPATH . "/wp-load.php");
	require_once( ABSPATH . "/wp-admin/includes/image.php");
	require_once( ABSPATH . "/wp-admin/includes/file.php");
	require_once( ABSPATH . "/wp-admin/includes/media.php");
	
	// Download url to a temp file
	$tmp = download_url( $url );
	if ( is_wp_error( $tmp ) ) return false;
	
	// Get the filename and extension ("photo.png" => "photo", "png")
	$filename = pathinfo($url, PATHINFO_FILENAME);
	$extension = pathinfo($url, PATHINFO_EXTENSION);
	
	// An extension is required or else WordPress will reject the upload
	if ( ! $extension ) {
		// Look up mime type, example: "/photo.png" -> "image/png"
		$mime = mime_content_type( $tmp );
		$mime = is_string($mime) ? sanitize_mime_type( $mime ) : false;
		
		// Only allow certain mime types because mime types do not always end in a valid extension (see the .doc example below)
		$mime_extensions = array(
			// mime_type         => extension (no period)
			'text/plain'         => 'txt',
			'text/csv'           => 'csv',
			'application/msword' => 'doc',
			'image/jpg'          => 'jpg',
			'image/jpeg'         => 'jpeg',
			'image/gif'          => 'gif',
			'image/png'          => 'png',
			'video/mp4'          => 'mp4',
		);
		
		if ( isset( $mime_extensions[$mime] ) ) {
			// Use the mapped extension
			$extension = $mime_extensions[$mime];
		}else{
			// Could not identify extension
			@unlink($tmp);
			return false;
		}
	}
	
	
	
	// Upload by "sideloading": "the same way as an uploaded file is handled by media_handle_upload"
	$args = array(
		'name' => "$filename.$extension",
		'tmp_name' => $tmp,
	);
	
	// Do the upload
	$attachment_id = media_handle_sideload( $args, 0, $title);
	
	// Cleanup temp file
	@unlink($tmp);
	
	// Error uploading
	if ( is_wp_error($attachment_id) ) return false;
	
	// Success, return attachment ID (int)
	return (int) $attachment_id;
   }

   public function render_list_image()
   {
	    $key= '';
		$page = 1;
		$total = 6150;
		$url = get_option('url_setting');
		$show_title= get_option('show_titles_checkbox');
		$allowlink=  get_option('allow_link_to_albums');
		$class_title ='';

		if(1 != $show_title) {
			$class_title ='hidden';
		 }

		if(isset($_GET['pg'])){
			$page = $_GET['pg'];	
		}

		if(get_option( 'keyword_setting' ) !== false|| !empty(get_option( 'keyword_setting' ))){
			$key = get_option( 'keyword_setting' );
		}
		$array = $this->get_gallery_info($url,$page,$key);	
       // echo $array ;
				echo'<div class="parent">';
					foreach ($array  as $valor) { 

						$url_album = $valor['albumUrl'];

						if(1 != $allowlink){ 
							$url_album ='#';
						 }

						echo'<div class="items" >';
					    echo'<a href="'.$url_album.'" style="background-image: url('.$valor['ImagenUrl'].')"><span class="text-item '.$class_title.'">'.$valor['Name'].'</span></a>';
						echo'</div>';
					}
				echo'</div>';
				echo $this->paginate_results(5,$page,$total,$url = esc_url(get_permalink(get_the_id())));	
	            
    }

	public function  make_Wp_Crawller_Plugin_shortcode(){
	  add_shortcode('render_list_image', array($this, 'render_list_image'));
	}

	public function add_query_vars_filter( $vars ){
		$vars[] = "pg";
		return $vars;
	}

	public function add_query_to_filter(){
		add_filter( 'query_vars', array($this, 'add_query_vars_filter'));
	}

	private function paginate_results($limit = 5, $current_page ,$total_pages = null,$page_url){

        if($current_page > $limit - 2) {
			$limit = $current_page + 5;
		 }
        echo'<div class="paginations">';
		echo'<ul class="page-numbers">';
		 for($i = 1 ; $i < $limit ; $i++){
			if($i == $current_page){
                echo '<li class="page-item active"><a class="page-link" href="'.$page_url.'?pg='.$i.'">'.$i.'</a></li>';
            }else{
                echo '<li class="page-item"><a class="page-link" href="'.$page_url.'?pg='.$i.'">'.$i.'</a></li>';
            }		 
		}
		echo'</ul>';
		echo'</div>';


	}

	private function getCurrentUrl() {
		$protocol = is_ssl() ? 'https://' : 'http://';
		return ($protocol) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	}

}

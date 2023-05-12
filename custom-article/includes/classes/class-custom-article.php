<?php
// Exit if accessed directly
if (!defined('ABSPATH')) { exit; }
if (!class_exists('CUSTOM_POST')) {
    class CUSTOM_POST
    {
        /**
         * Holds a copy of itself, so it can be referenced by the class name.
         *
         * @since 3.5
         *
         * @var CUSTOM_POST
         */       
        public function __construct()
        {
            $this->init();            
                        
        }
        
        /**
         * Call when object of class created.
         */
        public function init()
        {
            /**
             * Activate plugin and create custom post type with Wordpress
             */               
                       
            add_action('init', array($this, 'custom_post_type'));	    	
	    	add_action( 'admin_init', array($this, 'add_post_meta_box' ));
			add_shortcode( 'article-lists', array($this,'article_lists' ));
			add_action( 'save_post', array($this, 'save_article_location'), 10, 2 );
			add_action( 'wp_enqueue_scripts', array($this,'custom_enqueue_scripts' ) );		
        }

        /**
		 * Include scripts.
		 */
		public function custom_enqueue_scripts() {

			wp_enqueue_script( 'jquery-min', POST_ROOT_PATH . 'assets/js/jquery-1.11.3.min.js', array( 'jquery' ), false, null );
			wp_enqueue_style( 'leaflet-css', POST_ROOT_PATH . 'assets/css/leaflet.css',  false, 'all' );
			wp_enqueue_script( 'leaflet-min', POST_ROOT_PATH . 'assets/js/leaflet.js',  false, null );			
			wp_enqueue_script( 'markercluster-min', POST_ROOT_PATH . 'assets/js/leaflet.markercluster.js', false, null );			
		}

        /**
         * Check plugin class exists or not.
         */
        public static function is_custom_post_plugin_activate()
        {
            if (!function_exists('is_plugin_active')) {
                include_once ABSPATH.'wp-admin/includes/plugin.php';
            }
            if (!is_plugin_active('custom-post/functions.php')) {
                return false;
            }
            return true;
        }

        /**
         * Create custom post type of Articles.
         */
        public function custom_post_type()
        {      
		
			$labels = array(
			'name'               => _x( 'Articles', 'cust-art' ),
			'singular_name'      => _x( 'Articles', 'cust-art' ),
			'add_new'            => _x( 'Add New', 'Articles' ),
			'add_new_item'       => __( 'Add New Articles', 'cust-art' ),
			'edit_item'          => __( 'Edit Articles', 'cust-art' ),
			'new_item'           => __( 'New Articles', 'cust-art' ),
			'all_items'          => __( 'All Articless', 'cust-art' ),
			'view_item'          => __( 'View Articles', 'cust-art' ),
			'search_items'       => __( 'Search Articless', 'cust-art' ),
			'not_found'          => __( 'No Articless found', 'cust-art' ),
			'not_found_in_trash' => __( 'No Articless found in the Trash', 'cust-art' ),
			'menu_name'          => 'Articless'
			);
		
			/**
        	* Pass the args.
        	*/

			$args = array(
			'labels'        => $labels,
			'description'   => 'Articles',
			'public'        => true,
			'menu_position' => 5,
			'supports'      => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
			'has_archive'   => true,
			);
		
			/**
        	* Call the default register function.
        	*/

			register_post_type( 'articles', $args );
             
       }
	
		/**
		 * Add custom Meta Box to Posts post type Articles.
		 */
		public function add_post_meta_box()
		{
		    
		    add_meta_box(
		        'location',
		        'Location (Address)',
		        array( $this,'custom_location'),
		        'articles',
		        'normal',
		        'core'
		    );		    
		}

		/**
		 * Add custom meta box Location for post type articles.
		 */	
		public function custom_location() 
		{
		    global $post;		   
			$location = get_post_meta( $post->ID, 'location', true );
		?>
		<div id="dynamic_form">
		    <div id="field_wrap">
		    <?php
		    if ( isset( $location ) ) 
		    {	        
		        ?>

		        <textarea name="location" rows="4" cols="50"> <?php esc_html_e( $location ); ?> </textarea>

		        <?php
		        
		    } else { ?>
		    	<div style="display:block;" id="master-row">	    
		            <div class="form_field">
		            	<textarea name="location" rows="4" cols="50"> <?php esc_html_e( $location ); ?> </textarea>
		            </div>
		    </div>
		    <?php }// endforeach
		    ?>
		    </div>	    
		</div>
		  <?php
		}
		

		/**
		 * Create shortcode wtih some parameters.
		 */
		public function article_lists( $atts ) {
		ob_start();
		// define attributes and their defaults 
		extract( shortcode_atts( array (
			
			'posts' => -1,
			
		), $atts ) );
		// define query parameters based on attributes 
		$options = array(
			'post_type' => 'articles',
			'order' => 'ASC',
			'orderby' => 'title',
			'posts_per_page' => $posts,			
		);
		$query = new WP_Query( $options );
		// run the loop based on the query 
		if ( $query->have_posts() ) { ?>
			<ul class="clothes-listing">
			<?php while ( $query->have_posts() ) : $query->the_post(); ?>
			<li id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<p><<?php the_title(); ?></p>
				<?php the_post_thumbnail('thumbnail'); ?>
			</li>
			<div  class="custom-article">
				  <div>
				    <h3>
				      <?php the_title();?>
				    </h3>				    
				  </div>
				  <div class="body">
				  	<p>
				      <?php the_content();?>
				    </p>				   
				    <p>
				    <?php echo $custom_text = get_post_meta( get_the_ID(), 'location', true ); ?>
				    <div id="mapid"></div>
				   <script type="text/javascript">
				   	
				   </script>
				    </p>				    
				  </div>				  
				</div>
			<?php endwhile;
			wp_reset_postdata(); ?>
			<style type="text/css">
			
		#mapid {
		    height: 100vh;
		    width: 100%;
		}
	</style>
			<script type="text/javascript">
				var mymap = L.map('mapid').setView([51.505, -0.09], 13);

				L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
				  attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
				  maxZoom: 18,
				  tileSize: 512,
				  zoomOffset: -1
				}).addTo(mymap);

				var markers = L.markerClusterGroup();

				<?php while ( $query->have_posts() ) : $query->the_post(); $custom_text = get_post_meta( get_the_ID(), 'location', true ); ?>
					var loc = '<?php echo $custom_text; ?>';
					var lat ='';
					var lon = '';
					$.get('https://nominatim.openstreetmap.org/search?format=json&q='+loc, function(data){
					       //console.log(data[0].lat);
					        lat = data[0].lat;
					        lon = data[0].lon;
					         var post = {
				    title: '<?php echo esc_js( get_the_title() ); ?>',
				    latitude: lat,
				    longitude: lon,
				  };
				  console.log(post);
				  var popupContent = '<div class="popup"><h3>' + post.title + '</h3></div>';
				  var marker = L.marker([post.latitude, post.longitude])
				    .bindPopup(popupContent);
				  markers.addLayer(marker);

					    });
					console.log(markers);
				 
				<?php endwhile; ?>

				mymap.addLayer(markers);
			</script>
		</ul>
		<?php
			$myvariable = ob_get_clean();
			return $myvariable;
		}
	}
		
		/**
		 * Save post action, process fields
		 */
		public function save_article_location($post_id)
		{
			if(isset($_POST['location'])){

			    update_post_meta( $post_id, 'location', $_POST['location'] );
		    } else {

		    	delete_post_meta( $post_id, 'location' );
		    }
			
		}

    }

}?>

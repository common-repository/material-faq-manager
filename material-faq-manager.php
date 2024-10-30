<?php
/**
 * Plugin Name: Material FAQ Manager
 * Plugin URI: http://www.siprasoft.in/material-faq-manager/
 * Description: This plugin simple will display your question answer with material style design also maximise answer.
 * Version: 1.0
 * Author: Jetendra Pradhan
 * Author URI: http://siprasoft.in/
 * Developer: Jetendra Pradhan
 * Developer URI: http://siprasoft.in/
 * Text Domain: material-faq-manager
 * Domain Path: /languages
 * Wordpress requires at least: 4.8.3
 * Wordpress tested up to: 4.9
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
 
if ( ! defined( 'ABSPATH' ) ) {
			exit;
		}
if( !defined( 'MATERIAL_FAQ_MANAGER_VERSION' ) ) {
	define( 'MATERIAL_FAQ_MANAGER_VERSION', '1.0' ); // Version of plugin
}		
add_action('plugins_loaded', 'material_fm_lang_load');
function material_fm_lang_load() {
	load_plugin_textdomain( 'material-faq-manager', false, dirname( plugin_basename(__FILE__) ) . '/languages/' );
} 	
function material_fm_post_type() {
	register_post_type( 'mfm_faq', array(
		'labels' => array(
		'name'               => 'FAQ',
		'singular_name'      => 'FAQ',
		'menu_name'          => 'FAQ',
		'name_admin_bar'     => 'FAQ',
		'add_new'            => 'Add New',
		'add_new_item'       => 'Add New FAQ',
		'new_item'           => 'New FAQ',
		'edit_item'          => 'Edit FAQ',
		'view_item'          => 'View FAQ',
		'all_items'          => 'All FAQ',
		'search_items'       => 'Search FAQ',
		'parent_item_colon'  => 'Parent FAQ',
		'not_found'          => 'No FAQ found.',
		'not_found_in_trash' => 'No FAQ found in Trash.'
		),
		'public'  => true,
		'capability_type' => 'post',
		'map_meta_cap' => true,
		'hierarchical' => false,
		'rewrite' => false,
		'query_var' => false,
		'delete_with_user' => true,
		'supports' => array( 'title', 'editor', 'author', 'revisions', 'post-formats' ),
	) );	
}
add_action( 'init', 'material_fm_post_type' );	
add_action( 'wp_enqueue_scripts','material_fm_style' );
function material_fm_style() {
    wp_enqueue_style( 'mfmstyle',  plugin_dir_url( __FILE__ ). 'assets/css/mfm_style.css', array(), MATERIAL_FAQ_MANAGER_VERSION );
    wp_enqueue_script( 'mfmscript', plugin_dir_url( __FILE__ ) . 'assets/js/mfm_script.js', array('jquery'), MATERIAL_FAQ_MANAGER_VERSION );
}
function material_fm_shortcode( $atts, $content = null ) {
	extract(shortcode_atts(array(
		"limit" => '',
	), $atts));
	// Define limit
	if( $limit ) { 
		$posts_per_page = $limit; 
	} else {
		$posts_per_page = '-1';
	}
	ob_start();
	// Create the Query
	$post_type 		= 'mfm_faq';
	$orderby 		= 'post_date';
	$order 			= 'DESC';
				 
        $args = array ( 
            'post_type'      => $post_type, 
            'orderby'        => $orderby, 
            'order'          => $order,
            'posts_per_page' => $posts_per_page,           
            );     
      $query = new WP_Query($args);
	//Get post type count
	$post_count = $query->post_count;
	$i = 1;
	// Displays Custom post info
	if( $post_count > 0) :
	?>
<div class="faq-accordion" data-accordion-group>
  <?php while ($query->have_posts()) : $query->the_post();
		?>
  <div class="collapse-card">
    <div class="collapse-card__heading">
      <h4 class="collapse-card__title">
        <div class="qamark"></div>
        <?php the_title(); ?>
      </h4>
    </div>
    <div class="collapse-card__body">
      <?php the_content(); ?>
    </div>
  </div>
  <?php
		$i++;
		endwhile; ?>
</div>
<?php	endif;
	// Reset query to prevent conflicts
	wp_reset_query();
	?>
 <?php 
 add_action( 'wp_footer', 'material_fm_footer_scripts' );
 function material_fm_footer_scripts(){ ?>
 <script type="text/javascript">
    jQuery('.collapse-card').paperCollapse();
 </script>
<?php } ?>   
<?php
	return ob_get_clean();
}
add_shortcode("material_fm_list", "material_fm_shortcode");

function mfm_options_page()
{
    add_submenu_page(
        'edit.php?post_type=mfm_faq',
        'Faq Settings',
        'FAQ Settings',
        'manage_options',
        'faqsetting',
        'mfm_options_page_html'
    );
		
}
function mfm_options_page_html ()
{
?>
<div class="wrap">
  <h1>FAQ Display Settings</h1>
  <h3>
    <?php _e('Place your shortcode [material_fm_list] in your page editor to display your FAQ list.', 'material-faq-manager'); ?>
  </h3>
  <p>
    <?php _e('More options are comming soon.', 'material-faq-manager'); ?>
  </p>
</div>
</div>
<?php
}
add_action('admin_menu', 'mfm_options_page');
?>
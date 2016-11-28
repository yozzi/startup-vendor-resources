<?php
/*
Plugin Name: StartUp Vendor Resources
Author: Yann Caplain
Version: 1.0.0
Text Domain: startup-vendor-resources
Domain Path: /languages
*/

if ( ! defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
}

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}





// On crée ou on supprimer le post au changement de rôle
function startup_vendor_resources_update( $user_id, $new_role, $old_role ) {
    
    if ( $new_role == 'wc_product_vendors_admin_vendor' ) {

        $user_info = get_userdata( $user_id );
        
        // insert the post and set the category
        $post_id = wp_insert_post(array (
            'post_type' => 'bookable_resource',
            'post_title' => $user_id,
            'post_content' => $user_id . ' - ' . $user_info->user_login,
            'post_status' => 'publish',
            'comment_status' => 'closed',   // if you prefer
            'ping_status' => 'closed',      // if you prefer
        ));

//        if ($post_id) {
//            // insert post meta
//            add_post_meta($post_id, '_your_custom_1', $custom1);
//            add_post_meta($post_id, '_your_custom_2', $custom2);
//            add_post_meta($post_id, '_your_custom_3', $custom3);
//        }
      
    } else {
        $my_post = get_page_by_title( $user_id, OBJECT, 'bookable_resource' );
        wp_delete_post( $my_post->ID );
    }
}

add_action('set_user_role', 'startup_vendor_resources_update', 10, 3);



// On supprime le post à la suppression de l'utilisateur
function startup_vendor_resources_delete( $user_id ) {
	global $wpdb;

        $user_info = get_userdata( $user_id );
    
     $my_post = get_page_by_title( $user_id, OBJECT, 'bookable_resource' );
        wp_delete_post( $my_post->ID );
        
}
add_action( 'delete_user', 'startup_vendor_resources_delete' );


// Cocher Has resources
// Fix with js
function startup_vendor_resources_js(){
    $user = wp_get_current_user();
    if ( in_array( 'wc_product_vendors_admin_vendor', (array) $user->roles ) || in_array( 'wc_product_vendors_manager_vendor', (array) $user->roles ) ) { ?>
        <script type="text/javascript">
                // Cocher Has ressources dans création de produit
                // Marche po
                jQuery('body.post-type-product #_wc_booking_has_resources').prop('checked', true);
                
                // Sélectionner les ressources sont assignées automatiquement
                
                
                // Pré-selectionner la resources du avec comme nom l'id du user
                // Humhum on dirait que c'est de l'ajax... Va falloir ajouter un post_meta par defaut...
            

            
        
        </script>
<?php }
}

add_action( 'admin_footer', 'startup_vendor_resources_js' );



// Préselectionner la bonne resource
<?php
if ( ! function_exists('affiliate_link') ) {

// Register Custom Post Type
    function affiliate_link() {

        $labels = array(
            'name'                => _x( 'affiliate', 'Post Type General Name', 'wp-dev' ),
            'singular_name'       => _x( 'affiliate', 'Post Type Singular Name', 'wp-dev' ),
            'menu_name'           => __( 'affiliate', 'wp-dev' ),
            'parent_item_colon'   => __( 'Parent affiliate link:', 'wp-dev' ),
            'all_items'           => __( 'All affiliate link', 'wp-dev' ),
            'view_item'           => __( 'View affiliate link', 'wp-dev' ),
            'add_new_item'        => __( 'Add New affiliate link', 'wp-dev' ),
            'add_new'             => __( 'Add New', 'wp-dev' ),
            'edit_item'           => __( 'Edit affiliate link', 'wp-dev' ),
            'update_item'         => __( 'Update affiliate link', 'wp-dev' ),
            'search_items'        => __( 'Search Item', 'wp-dev' ),
            'not_found'           => __( 'Not found', 'wp-dev' ),
            'not_found_in_trash'  => __( 'Not found in Trash', 'wp-dev' ),
        );
        $rewrite = array(
            'slug'                => 'out',
            'with_front'          => false,
            'pages'               => false,
            'feeds'               => false,
        );
        $args = array(
            'label'               => __( 'affiliate', 'wp-dev' ),
            'description'         => __( 'add your affiliate link ', 'wp-dev' ),
            'labels'              => $labels,
            'supports'            => array( 'title', ),
            'hierarchical'        => false,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_nav_menus'   => true,
            'show_in_admin_bar'   => false,
            'menu_position'       => 20,
            'can_export'          => true,
            'has_archive'         => false,
            'exclude_from_search' => true,
            'publicly_queryable'  => true,
            'rewrite'             => $rewrite,
            'capability_type'     => 'page',
        );
        register_post_type( 'affiliate', $args );
        flush_rewrite_rules();

    }

// Hook into the 'init' action
    add_action( 'init', 'affiliate_link', 0 );

}

function out_affi_link()
{
    if ( is_singular( 'affiliate' ) ) {
        global $post;
        $out_url = get_post_meta( $post->ID, '_my_meta_value_key', true );
        wp_redirect( $out_url );
    }
}

add_action('wp_head','out_affi_link');
/**
 * Adds a box to the main column on the Post and Page edit screens.
 */
function myplugin_add_meta_box() {

        add_meta_box(
            'myplugin_sectionid',
            __( 'Affiliate', 'myplugin_textdomain' ),
            'myplugin_meta_box_callback',
            'affiliate'
        );
    }

add_action( 'add_meta_boxes', 'myplugin_add_meta_box' );

/**
 * Prints the box content.
 *
 * @param WP_Post $post The object for the current post/page.
 */
function myplugin_meta_box_callback( $post ) {

    // Add an nonce field so we can check for it later.
    wp_nonce_field( 'myplugin_meta_box', 'myplugin_meta_box_nonce' );

    /*
     * Use get_post_meta() to retrieve an existing value
     * from the database and use the value for the form.
     */
    $value = get_post_meta( $post->ID, '_my_meta_value_key', true );

    echo '<label for="myplugin_new_field">';
    _e( 'Add your affiliate Url', 'myplugin_textdomain' );
    echo '</label> ';
    echo '<input type="text" id="myplugin_new_field" name="myplugin_new_field" value="' . esc_attr( $value ) . '" size="25" />';
    echo '<p> Dont\'t forget to add <code>http://</code> or <code>https://</code> </p>';
    echo '<p>To display this link on post paste this shortcode there <code>[affiliate url="'. $post->ID.'" title="Visit Site"]</code></p>';
}

/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function myplugin_save_meta_box_data( $post_id ) {

    /*
     * We need to verify this came from our screen and with proper authorization,
     * because the save_post action can be triggered at other times.
     */

    // Check if our nonce is set.
    if ( ! isset( $_POST['myplugin_meta_box_nonce'] ) ) {
        return;
    }

    // Verify that the nonce is valid.
    if ( ! wp_verify_nonce( $_POST['myplugin_meta_box_nonce'], 'myplugin_meta_box' ) ) {
        return;
    }

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // Check the user's permissions.
    if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

        if ( ! current_user_can( 'edit_page', $post_id ) ) {
            return;
        }

    } else {

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
    }

    /* OK, it's safe for us to save the data now. */

    // Make sure that it is set.
    if ( ! isset( $_POST['myplugin_new_field'] ) ) {
        return;
    }

    // Sanitize user input.
    $my_data = sanitize_text_field( $_POST['myplugin_new_field'] );

    // Update the meta field in the database.
    update_post_meta( $post_id, '_my_meta_value_key', $my_data );
}
add_action( 'save_post', 'myplugin_save_meta_box_data' );

// Add Shortcode
function amwp_affiliate_shortcode( $atts ) {

    // Attributes
    extract( shortcode_atts(
            array(
                'url' => '123',
                'title' => 'Visit Site'
            ), $atts )
    );
    $permalink = get_permalink( $url );
    return '<a href="'.$permalink.'" class="affi-button">'.$title.'</a>';
}
add_shortcode( 'affiliate', 'amwp_affiliate_shortcode' );

function amwp_affi_style()
{
    wp_enqueue_style( 'myCSS', plugins_url( '/affi-style.css', __FILE__ ) );
}

add_action( 'wp_enqueue_scripts', 'amwp_affi_style');
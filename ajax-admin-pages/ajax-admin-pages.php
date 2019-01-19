<?php
/**
 * Plugin main file starting point
 *
 * @since             1.0.0
 * @package           Ajax_In_Admin
 *
 * @wordpress-plugin
 * Plugin Name:       AJAX in WordPress Admin
 * Plugin URI:        https://github.com/dingo-d/ajax-showcase/ajax-admin-pages
 * Description:       The example of a simple plugin for adding ajax in admin pages
 * Version:           1.0.0
 * Author:            Denis Žoljom <denis.zoljom@gmail.com>
 * Author URI:        https://madebydenis.com
 * License:           MIT
 * License URI:       https://opensource.org/licenses/MIT
 * Text Domain:       ajax-in-admin
 * Requires PHP:      5.6
 */

namespace Ajax_In_Admin;

// Add menu page.
add_action( 'admin_menu', __NAMESPACE__ . '\\add_admin_page' );

/**
 * Function that will add a sample admin page
 */
function add_admin_page() {
  add_menu_page(
    esc_html__( 'Sample page', 'ajax-in-admin' ),
    esc_html__( 'Sample page', 'ajax-in-admin' ),
    'activate_plugins',
    'sample-page',
    __NAMESPACE__ . '\\add_sample_page_callback',
    'dashicons-update',
    90
  );
}

// Callback function that will render the admin page.
function add_sample_page_callback() {
  ?>
  <h1><?php esc_html_e( 'Sample page heading', 'ajax-in-admin' ); ?></h1>
  <div class="js-load-ajax"></div>
  <button class="js-call-ajax button primary"><?php esc_html_e( 'Click me!'); ?></button>
  <?php
  wp_nonce_field( 'sample_ajax_action', 'sample_ajax_nonce' );
}

// Enqueue files in admin.
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\admin_script_enqueue' );

/**
 * Function that is used to enqueue admin scripts.
 *
 * @param  string $hook Page name on which we want the script to be enqueued.
 * @return void
 * @since  1.0.0
 */
function admin_script_enqueue( $hook ) {
  /**
   * Load only on ?page=test-ajax-admin.php
   * The easiest way to find out the hook name is to go to the
   * options page and put print_r( $hook ); before the conditional. The
   * hook name will be shown on the added page.
   */
  if ( $hook !== 'toplevel_page_sample-page' ) {
    return;
  }

  wp_enqueue_script( 'test-plugin-admin-script', plugin_dir_url( __FILE__ ) . 'js/admin.js', array( 'jquery' ) );
}

// Ajax callback function.
add_action( 'wp_ajax_backend_ajax_action', __NAMESPACE__ . '\\backend_ajax_action' );

/**
 * AJAX action that will fire on plugin admin page
 *
 * @return void
 * @since  1.0.0
 */
function backend_ajax_action() {
  /*
   * We need to verify this came from the our screen and with proper authorization.
   */
  if ( isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'sample_ajax_action' ) ) {
    /**
     * Here you can do many things - database queries, data validation,
     * options saving or just plain data manipulation
     */
    $success_data = array(
      'code'     => 200,
      'response' => esc_html__( 'This is a random message of success.', 'ajax-in-admin' ),
    );

    wp_send_json_success( $success_data, $success_data['code'] );
  } else {
    $fail_data = array(
      'code'     => 500,
      'response' => esc_html__( 'Error happened.', 'ajax-in-admin' ),
    );
    wp_send_json_error( $fail_data, $fail_data['code'] );
  }
}

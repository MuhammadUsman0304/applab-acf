<?php

/**
 * Plugin Name: Applab ACF 
 * Plugin URI: https://github.com/MuhammadUsman0304/applab-portal-1
 *  Description: Post jobs on your WordPress site. ACF plugin integrated, User can apply and attach resume for the jobs, user can display jobs on other websites with the help of api
 * Author: Muhammad Usman
 *  Version: 1.0.0
 * Author URI: https://www.linkedin.com/in/muhammad-usman-b3439218b/
 * Text Domain: applab-acf
 * Domain Path: /languages
 */
defined('ABSPATH') || die("I'm just a plugin, I don't do much by calling directly :) ");
// integrating acf plugin 

define('APPLAB_ACF_PATH', plugin_dir_path(__FILE__) . '/inc/');
define('APPLAB_ACF_URL', plugin_dir_url(__FILE__) . '/inc/');
define('APPLAB_ACF_ASSEETS_URL', plugin_dir_url(__FILE__) . '/assets/');

require_once  APPLAB_ACF_PATH . 'acf-plugin/acf.php';
require_once  APPLAB_ACF_PATH . 'functions/applab-acf-cpt.php';
require_once  APPLAB_ACF_PATH . 'functions/function-applab-acf-job-listing.php';
require_once  APPLAB_ACF_PATH . 'functions/applab-acf-apis.php';
$applab_job_plugin_name = "applab_acf";

// enque styles and scripts


function applab_acf_job_wp_enqueue_styles()
{
    // register style
    wp_register_style('applab_job_wp_register_bootstrap', APPLAB_ACF_ASSEETS_URL . 'css/bootstrap.min.css');
    wp_register_style('applab_wp_register_bootstrap', APPLAB_ACF_ASSEETS_URL . 'css/bootstrap.min.css');
    // enueing styles
    wp_enqueue_style('applab_job_wp_register_bootstrap');
    wp_enqueue_style('applab_wp_register_bootstrap');
}

function applab_acf_job_wp_enqueue_scripts()
{
    // register  bs/js 
    wp_register_script('applab_job_wp_register_script', APPLAB_ACF_ASSEETS_URL . 'js/bootstrap.bundle.min.js', 'jquery', false, true);
    wp_register_script('applab_wp_register_script', APPLAB_ACF_ASSEETS_URL . 'js/bootstrap.bundle.min.js', 'jquery', false, true);

    // enueque scipts
    wp_enqueue_script('applab_job_wp_register_script');
    wp_enqueue_script('applab_wp_register_script');
}
add_action('wp_enqueue_scripts', 'applab_acf_job_wp_enqueue_styles');
add_action('wp_enqueue_scripts', 'applab_acf_job_wp_enqueue_scripts');


// integrate acf

add_filter('acf/settings/url', 'applab_acf_settings_url');

function applab_acf_settings_url($url)
{
    return APPLAB_ACF_URL . 'acf-plugin/';
}
add_action('init', 'applab_acf_register_job_post_type');


register_activation_hook(__FILE__, function () {
    require_once  APPLAB_ACF_PATH . 'functions/applab-acf-bootstrap.php';
    applab_acf_create_app_tbl();
    applab_acf_job_listing_pg();
    applab_acf_job_detail_pg();
});
add_action('rest_api_init', 'acf_job_listing_api_route');
add_action('admin_menu', function () {
    global $applab_acf_job_plugin_name;
    add_submenu_page("edit.php?post_type=job_manager_acf", "Applications", "Applications", 'edit_themes', $applab_acf_job_plugin_name . "_applications", 'applab_acf_applications');
});

function applab_acf_applications()
{
    global $wpdb;
    $applab_app_table = $wpdb->prefix . 'applab_acf_app';


    $applications = $wpdb->get_results("SELECT * FROM $applab_app_table ");
?>
    <div class="wrap">
        <h1 class="wp-heading-inline">My Jobs</h1>
        <table class="wp-list-table widefat fixed striped posts">
            <thead>
                <tr>
                    <th scope="col" class="manage-column">Applicant Name</th>
                    <th scope="col" class="manage-column">Applicant Email</th>
                    <th scope="col" class="manage-column">Job</th>
                    <th scope="col" class="manage-column">Resume</th>
                    <th scope="col" class="manage-column">Message</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($applications as $application) :

                ?>
                    <tr>
                        <td><?php echo $application->app_name; ?></td>
                        <td><?php echo $application->app_email; ?></td>
                        <td><?php echo get_the_title($application->app_job_id); ?></td>
                        <td><a download href="<?php echo $application->app_cv ?>">Download</a></td>
                        <td><?php echo $application->app_msg; ?></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>


<?php
}


add_filter('template_include', 'applab_acf_joblisting');
function applab_acf_joblisting($template)
{
    // Check if we're on a specific page or post
    if ('job_manager_acf' === get_post_type()) {
        // Look for a template file in the plugin directory
        $new_template = plugin_dir_path(__FILE__) . 'templates/template-applab-acf-job-listing.php';
        if (file_exists($new_template)) {
            return $new_template;
        }
    }
    return $template;
}


add_filter('template_include', 'applab_acf_job_detail');
function applab_acf_job_detail($template)
{
    // Check if we're on a specific page or post
    if (is_single() && 'job_manager_acf' === get_post_type()) {
        // Look for a template file in the plugin directory
        $new_template = plugin_dir_path(__FILE__) . 'templates/template-applab-acf-job-detail.php';
        if (file_exists($new_template)) {
            return $new_template;
        }
    }
    return $template;
}

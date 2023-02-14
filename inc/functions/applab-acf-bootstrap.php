<?php

/**
 * create table 
 * @package applab-acf
 */



defined('ABSPATH') || die("I'm just a plugin, I don't do much by calling directly :) ");
// creating table for application

function applab_acf_create_app_tbl()
{
    global $wpdb;
    $tbl = $wpdb->prefix;
    $table_name = $tbl . "applab_acf_app";

    $tbl_app = "CREATE TABLE IF NOT EXISTS `$table_name` (
  `app_id` int(11) NOT NULL AUTO_INCREMENT,
  `app_name` varchar(130) NOT NULL,
  `app_job_id` int(11) NOT NULL,
  `app_email` varchar(130) NOT NULL,
  `app_msg` text NOT NULL,
  `app_cv` text NOT NULL,
  PRIMARY KEY (`app_id`)
);";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($tbl_app);
}


// creating page for job  listings

// creating page for job listings on plugin activation 
function applab_acf_job_listing_pg()
{
    $job_listing_page = get_page_by_path('job_manager');
    if (!$job_listing_page) {
        $job_listing_page = array(
            'post_type' => 'page',
            'post_title' => 'Job Manager ACF',
            'post_status' => 'publish',
            'post_author' => 1,
            'post_name' => 'job-manager-acf'
        );

        wp_insert_post($job_listing_page);
    }
}
function applab_acf_job_detail_pg()
{
    $job_listing_page = get_page_by_path('job-detail');
    if (!$job_listing_page) {
        $job_listing_page = array(
            'post_type' => 'page',
            'post_title' => 'Job Detail ACF',
            'post_status' => 'publish',
            'post_author' => 1,
            'post_name' => 'job-detail-acf'
        );

        wp_insert_post($job_listing_page);
    }
}

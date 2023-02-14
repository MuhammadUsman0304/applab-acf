<?php

/**
 * api routes
 * @package applab-acf
 * 
 * http://localhost/applab/wp-json/applab-acf/myjobs => get
 * http://localhost/applab/wp-json/applab-acf/apply => post
 */
defined('ABSPATH') || die("I'm just a plugin, I don't do much by calling directly :) ");

function acf_job_listing_api_route()
{
    register_rest_route('applab-acf/', '/myjobs', array(
        'methods' => 'GET',
        'callback' => 'applab_acf_jobs_get_callback',
        'args' => array(
            'featured' => array(
                'validate_callback' => function ($param, $request, $key) {
                    return in_array($param, array('display_job_as_featured'));
                },
                'description' => __('Sort jobs by expiry date or featured status'),
                'default' => 'display_job_as_featured'
            )
        ),
    ));



    // registering apply api route
    register_rest_route('applab-acf/', '/apply', array(
        'methods' => 'POST',
        'callback' => 'applab_acf_job_post_callback',
        'args' => array(
            'app_name' => array(
                'validate_callback' => function ($param, $request, $key) {
                    return !empty($param);
                },
                'required' => true
            ),
            'app_job_id' => array(
                'validate_callback' => function ($param, $request, $key) {
                    return !empty($param);
                },
                'required' => true
            ),
            'app_email' => array(
                'validate_callback' => function ($param, $request, $key) {
                    return !empty($param);
                },
                'required' => true
            ),
            'app_msg' => array(
                'validate_callback' => function ($param, $request, $key) {
                    return !empty($param);
                },
                'required' => true
            ),
            'app_cv' => array(
                'validate_callback' => function ($param, $request, $key) {
                    return !empty($param);
                },
                'required' => true
            )
        )
    ));
}


// Callback function to fetch the job posts and return the response
function applab_acf_jobs_get_callback($request)
{
    $args = array(
        'post_type' => 'job_manager_acf',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => 'job_expire_date',
                'value' => date('Ymd'),
                'compare' => '>=',
            ),
        ),

    );


    $posts = get_posts($args);
    $jobs = array();

    foreach ($posts as $post) {
        $job_id = $post->ID;
        $job_title = $post->post_title;
        $job_desc = get_field('job_descripation', $job_id);
        $job_type = get_field('job_type', $job_id);
        $job_category = get_field('job_category', $job_id);
        $job_location = get_field('location', $job_id);
        $com_logo_url = get_field('company_logo', $job_id);
        $job_logo =  $com_logo_url['url'];
        $is_featured = get_field('display_job_as_featured', $job_id);
        $job_is_featured = implode(" ", $is_featured);
        $job_is_featured = intval($job_is_featured);

        $job_end_date = get_field('job_expire_date', $job_id);
        $job_comp_name = get_field('company_name', $job_id);

        $jobs[] = array(
            'job_id' => $job_id,
            'job_title' => $job_title,
            'job_desc' => $job_desc,
            'job_type' => $job_type,
            'job_end_date' => $job_end_date,
            'job_location' => $job_location,
            'job_comp_name' => $job_comp_name,
            'job_logo_url' => $job_logo,
            'job_is_featured' => $job_is_featured,
            'job_category' => $job_category,
        );
    }

    return $jobs;
}




// applying to jobs through api

function applab_acf_job_post_callback($request)
{
    global $wpdb;
    $params = $request->get_params();
    $applab_app_table = $wpdb->prefix . 'applab_acf_app';
    $name = sanitize_text_field($params['app_name']);
    $job_id = intval($params['app_job_id']);
    $email = sanitize_text_field($params['app_email']);
    $message = sanitize_text_field($params['app_msg']);
    $resume = sanitize_text_field($params['app_cv']);
    if (empty($name) || empty($job_id) || empty($email) || empty($message) || empty($resume)) {
        return new WP_Error('field_error', 'All fields are required', array('status' => 400));
    } else {
        $apply_data =  $wpdb->insert("$applab_app_table", array(
            'app_name' => $name,
            'app_job_id' => $job_id,
            'app_email' => $email,
            'app_msg' => $message,
            'app_cv' => $resume
        ));
        // return $apply_data;
        return array('success' => true);
    }
}

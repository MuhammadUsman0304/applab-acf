<?php

/**
 *  Template Name: job manager acf
 * @package applab-acf
 */

get_header();
echo '<div class="conainter">
        <div class="row">
            <div class="col-lg-8 mx-auto">';
$posts = get_posts(array(
    'post_type' => 'job_manager_acf',
    'post_status' => 'publish',
    'posts_per_page' => -1,
));

$jobs = array();

foreach ($posts as $post) :
    $job_id = $post->ID;
    $job_title = $post->post_title;
    $job_desc = get_field('job_descripation', $job_id);
    $job_type = get_field('job_type', $job_id);
    $job_category = get_field('job_category', $job_id);
    $job_location = get_field('location', $job_id);
    $job_is_featured = get_field('display_job_as_featured', $job_id);
    $job_end_date = get_field('job_expire_date', $job_id);
    $job_comp_name = get_field('company_name', $job_id);


?>

    <a href="<?php echo get_permalink($job_id) ?>" class="text-dark text-decoration-none">
        <div class="card border-0 my-4 p-3">
            <div class="card-header bg-white">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="card-title"><?php echo $job_title ?></h4>
                    </div>
                    <div class="col-md-6">
                        <h6 class="float-end"><?php echo $job_comp_name ?></h6>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <p><?php echo $job_title ?></p>
            </div>
            <div class="card-footer">
                <small>
                    <i class="dashicons dashicons-location"> </i> <?php echo $job_location ?> | <i class="dashicons dashicons-category"> </i> <?php echo $job_category ?> | <i class="dashicons dashicons-clock"> </i> <?php echo $job_end_date ?>
                </small>
            </div>
        </div>
    </a>



<?php
endforeach;
echo ' </div>
    </div>
    </div>';
?>




<?php get_footer(); ?>
<?php
/*
Plugin Name: Jiti Date Redirection
Description: Redirige les URLs avec des dates lors d'un changement de date et de slug.
Version: 1.0
Author: Jiti
Author URI: https://jiti.me
License: Copyleft
*/

add_action('template_redirect', function () {
    if (is_404()) {
        $request_uri = isset($_SERVER['REQUEST_URI']) ? rawurldecode($_SERVER['REQUEST_URI']) : '';

        if (preg_match('#^/([0-9]{4})/([0-9]{2})/([^/]+)/?$#', $request_uri, $matches)) {
            $old_slug = sanitize_title($matches[3]);
            global $wpdb;
            $post_id = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_wp_old_slug' AND meta_value = %s",
                    $old_slug
                )
            );
            if ($post_id) {
                $post = get_post($post_id);
                if (
                    $post &&
                    $post->post_status === 'publish' &&
                    $post->post_type === 'post'
                ) {
                    $year = date('Y', strtotime($post->post_date));
                    $month = date('m', strtotime($post->post_date));
                    $new_slug = $post->post_name;
                    $new_url = home_url("/$year/$month/$new_slug/");
                    wp_redirect($new_url, 301);
                    exit;
                }
            }
        }
    }
});

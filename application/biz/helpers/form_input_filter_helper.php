<?php
function filter_service_input($post) {
    $post['name'] = filter_trim_space($post['name']);
    $post['code'] = filter_trim_space($post['code']);
    $post['description'] = filter_trim_space($post['description']);
    $post['min_profit'] = filter_trim_space($post['min_profit']);

    return $post;
}
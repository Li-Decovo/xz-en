<?php

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;

$field_ids = [124];
for ($index = 0; $index < count($field_ids); $index++) {
    $children = $wpdb->get_col($wpdb->prepare(
        "SELECT ID FROM {$wpdb->posts} WHERE post_parent = %d AND post_type IN ('acf-field', 'acf-field-group')",
        $field_ids[$index]
    ));
    foreach (array_map('intval', $children) as $child_id) {
        if (!in_array($child_id, $field_ids, true)) {
            $field_ids[] = $child_id;
        }
    }
}

$product_ids = get_posts([
    'post_type' => 'product',
    'post_status' => 'any',
    'posts_per_page' => -1,
    'fields' => 'ids',
]);
$attachment_ids = get_posts([
    'post_type' => 'attachment',
    'post_status' => 'any',
    'posts_per_page' => -1,
    'fields' => 'ids',
]);
$post_ids = array_values(array_unique(array_merge($field_ids, array_map('intval', $product_ids), array_map('intval', $attachment_ids))));
$placeholders = implode(',', array_fill(0, count($post_ids), '%d'));
$posts = $wpdb->get_results($wpdb->prepare(
    "SELECT * FROM {$wpdb->posts} WHERE ID IN ($placeholders)",
    $post_ids
), ARRAY_A);
$meta = $wpdb->get_results($wpdb->prepare(
    "SELECT * FROM {$wpdb->postmeta} WHERE post_id IN ($placeholders)",
    $post_ids
), ARRAY_A);

$upload = wp_upload_dir();
$directory = trailingslashit($upload['basedir']) . 'codex-backups';
wp_mkdir_p($directory);
$path = trailingslashit($directory) . 'product-fields-' . gmdate('Ymd-His') . '.json';
$payload = [
    'created_at' => gmdate('c'),
    'site_url' => home_url('/'),
    'posts' => $posts,
    'postmeta' => $meta,
];
$written = file_put_contents($path, wp_json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
if ($written === false) {
    WP_CLI::error('Could not write the product field backup.');
}

WP_CLI::success($path . ' (' . size_format($written) . ')');

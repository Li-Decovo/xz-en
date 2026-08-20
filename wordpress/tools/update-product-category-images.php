<?php

if (!defined('ABSPATH') || !class_exists('WP_CLI')) {
    exit;
}

require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

$image_dir = getenv('XZ_CATEGORY_IMAGE_DIR') ?: dirname(__DIR__) . '/assets/product-category-covers';
$categories = [
    'steel-grating' => ['steel-grating.jpg', 'Steel Grating', 'Steel grating panel'],
    'reinforcing-mesh' => ['reinforcing-mesh.jpg', 'Reinforcing Mesh', 'Welded reinforcing mesh panel'],
    'lattice-girder' => ['lattice-girder.jpg', 'Lattice Girder', 'Steel lattice girder'],
    'cable-tray' => ['cable-tray.jpg', 'Wire Mesh Cable Tray', 'Wire mesh cable tray basket'],
    'fence-panel' => ['fence-panel.jpg', '3D Fence Panel', '3D welded wire fence panel'],
];

foreach ($categories as $slug => [$filename, $title, $alt]) {
    $source = trailingslashit($image_dir) . $filename;
    if (!is_readable($source)) {
        WP_CLI::warning("Missing category image: {$source}");
        continue;
    }

    $attachments = get_posts([
        'post_type' => 'attachment',
        'post_status' => 'inherit',
        'posts_per_page' => 1,
        'meta_key' => '_xz_product_category_cover',
        'meta_value' => $slug,
        'fields' => 'ids',
    ]);
    $attachment_id = $attachments ? (int) $attachments[0] : 0;

    if (!$attachment_id) {
        $temporary = wp_tempnam($filename);
        copy($source, $temporary);
        $attachment_id = media_handle_sideload([
            'name' => 'xinzhou-' . $filename,
            'tmp_name' => $temporary,
        ], 0, $title);
        if (is_wp_error($attachment_id)) {
            WP_CLI::warning($attachment_id->get_error_message());
            continue;
        }
        update_post_meta($attachment_id, '_xz_product_category_cover', $slug);
    }

    wp_update_post(['ID' => $attachment_id, 'post_title' => $title]);
    update_post_meta($attachment_id, '_wp_attachment_image_alt', $alt);

    $term = get_term_by('slug', $slug, 'product_category');
    if (!$term instanceof WP_Term) {
        WP_CLI::warning("Missing product category: {$slug}");
        continue;
    }

    update_field('category_image', $attachment_id, 'product_category_' . $term->term_id);
    WP_CLI::log("{$slug}: attachment {$attachment_id}");
}

WP_CLI::success('Product category covers updated.');

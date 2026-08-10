<?php
/**
 * Plugin Name: Xinzhou Content Display
 * Description: Front-end display helpers for ACF-managed Xinzhou products and articles.
 * Version: 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

function xz_content_asset_version(string $relative_path): string {
    $path = WPMU_PLUGIN_DIR . '/xinzhou-content/' . $relative_path;
    return file_exists($path) ? (string) filemtime($path) : '1.0.0';
}

add_action('wp_enqueue_scripts', static function (): void {
    wp_enqueue_style(
        'xinzhou-content',
        WPMU_PLUGIN_URL . '/xinzhou-content/assets/xinzhou-content.css',
        [],
        xz_content_asset_version('assets/xinzhou-content.css')
    );

    wp_register_script(
        'xinzhou-content',
        WPMU_PLUGIN_URL . '/xinzhou-content/assets/xinzhou-content.js',
        [],
        xz_content_asset_version('assets/xinzhou-content.js'),
        true
    );

    if (is_singular(['product', 'post']) || is_post_type_archive('product') || is_tax('product_category') || is_home()) {
        wp_enqueue_script('xinzhou-content');
    }
});

add_action('template_redirect', static function (): void {
    if (!is_post_type_archive('product') || empty($_GET['category']) || !taxonomy_exists('product_category')) {
        return;
    }

    $slug = sanitize_title(wp_unslash($_GET['category']));
    $term = get_term_by('slug', $slug, 'product_category');
    if (!$term instanceof WP_Term) {
        return;
    }

    $url = get_term_link($term);
    if (!is_wp_error($url)) {
        wp_safe_redirect($url, 301);
        exit;
    }
});

add_action('pre_get_posts', static function (WP_Query $query): void {
    if (is_admin() || !$query->is_main_query()) {
        return;
    }

    if ($query->is_post_type_archive('product') || $query->is_tax('product_category') || $query->is_home() || $query->is_category()) {
        $query->set('posts_per_page', 9);
    }
});

function xz_acf_image_id($value): int {
    if (is_numeric($value)) {
        return (int) $value;
    }
    if (is_array($value) && isset($value['ID'])) {
        return (int) $value['ID'];
    }
    return 0;
}

function xz_product_term_image(int $term_id): int {
    if (!function_exists('get_field')) {
        return 0;
    }
    return xz_acf_image_id(get_field('category_image', 'product_category_' . $term_id));
}

add_action('elementor/widgets/register', static function ($widgets_manager): void {
    $widget_file = WPMU_PLUGIN_DIR . '/xinzhou-content/elementor-widgets.php';
    if (!file_exists($widget_file)) {
        return;
    }

    require_once $widget_file;
    \Xinzhou\Elementor\register_widgets($widgets_manager);
});

add_action('elementor/query/xinzhou_related_products', static function (WP_Query $query): void {
    if (!is_singular('product')) {
        return;
    }

    $post_id = get_queried_object_id();
    $terms = wp_get_post_terms($post_id, 'product_category', ['fields' => 'ids']);
    $query->set('post_type', 'product');
    $query->set('post__not_in', [$post_id]);
    $query->set('posts_per_page', 3);
    if (!is_wp_error($terms) && $terms) {
        $query->set('tax_query', [[
            'taxonomy' => 'product_category',
            'field' => 'term_id',
            'terms' => $terms,
        ]]);
    }
});

add_action('elementor/query/xinzhou_related_news', static function (WP_Query $query): void {
    if (!is_singular('post')) {
        return;
    }

    $post_id = get_queried_object_id();
    $categories = wp_get_post_categories($post_id);
    $query->set('post_type', 'post');
    $query->set('post__not_in', [$post_id]);
    $query->set('posts_per_page', 3);
    if ($categories) {
        $query->set('category__in', $categories);
    }
});

add_action('elementor/query/xinzhou_home_products', static function (WP_Query $query): void {
    $query->set('post_type', 'product');
    $query->set('post_status', 'publish');
    $query->set('posts_per_page', 6);
    $query->set('orderby', 'menu_order date');
    $query->set('order', 'DESC');
});

add_action('elementor/query/xinzhou_home_cases', static function (WP_Query $query): void {
    $term = get_term_by('slug', 'cases', 'category');
    $query->set('post_type', 'post');
    $query->set('post_status', 'publish');
    $query->set('posts_per_page', 5);
    if ($term instanceof WP_Term) {
        $query->set('category__in', [(int) $term->term_id]);
    }
});

add_action('elementor/query/xinzhou_home_news', static function (WP_Query $query): void {
    $term = get_term_by('slug', 'cases', 'category');
    $query->set('post_type', 'post');
    $query->set('post_status', 'publish');
    $query->set('posts_per_page', 3);
    if ($term instanceof WP_Term) {
        $query->set('category__not_in', [(int) $term->term_id]);
    }
});

function xz_product_gallery_ids(int $post_id): array {
    $gallery = function_exists('get_field') ? get_field('product_gallery', $post_id) : [];
    $ids = [];
    foreach ((array) $gallery as $image) {
        $image_id = xz_acf_image_id($image);
        if ($image_id) {
            $ids[] = $image_id;
        }
    }
    $featured = get_post_thumbnail_id($post_id);
    if ($featured && !in_array($featured, $ids, true)) {
        array_unshift($ids, $featured);
    }
    return array_values(array_unique($ids));
}

function xz_article_heading_slug(string $heading, int $index): string {
    $slug = sanitize_title(wp_strip_all_tags($heading));
    return $slug ?: 'article-section-' . ($index + 1);
}

add_filter('the_content', static function (string $content): string {
    if (!is_singular('post') || !in_the_loop() || !is_main_query()) {
        return $content;
    }
    $index = 0;
    return (string) preg_replace_callback('/<h2([^>]*)>(.*?)<\/h2>/is', static function (array $match) use (&$index): string {
        if (preg_match('/\sid=["\'][^"\']+["\']/', $match[1])) {
            $index++;
            return $match[0];
        }
        $id = xz_article_heading_slug($match[2], $index++);
        return '<h2' . $match[1] . ' id="' . esc_attr($id) . '">' . $match[2] . '</h2>';
    }, $content);
}, 12);

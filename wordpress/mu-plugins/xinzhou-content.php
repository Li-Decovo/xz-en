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

function xz_inquiry_popup_id(): int {
    return absint(get_option('xz_inquiry_popup_id', 0));
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

    wp_register_style(
        'xinzhou-about-widgets',
        WPMU_PLUGIN_URL . '/xinzhou-content/assets/about-widgets.css',
        ['xinzhou-content'],
        xz_content_asset_version('assets/about-widgets.css')
    );

    wp_register_script(
        'xinzhou-about-widgets',
        WPMU_PLUGIN_URL . '/xinzhou-content/assets/about-widgets.js',
        [],
        xz_content_asset_version('assets/about-widgets.js'),
        true
    );

    wp_register_style(
        'xinzhou-service-widgets',
        WPMU_PLUGIN_URL . '/xinzhou-content/assets/service-widgets.css',
        ['xinzhou-content'],
        xz_content_asset_version('assets/service-widgets.css')
    );

    wp_register_style(
        'xinzhou-product-archive-widgets',
        WPMU_PLUGIN_URL . '/xinzhou-content/assets/product-archive-widgets.css',
        ['xinzhou-content'],
        xz_content_asset_version('assets/product-archive-widgets.css')
    );

    wp_register_style(
        'xinzhou-product-detail-widgets',
        WPMU_PLUGIN_URL . '/xinzhou-content/assets/product-detail-widgets.css',
        ['xinzhou-content'],
        xz_content_asset_version('assets/product-detail-widgets.css')
    );

    wp_register_style(
        'xinzhou-news-archive-widgets',
        WPMU_PLUGIN_URL . '/xinzhou-content/assets/news-archive-widgets.css',
        ['xinzhou-content'],
        xz_content_asset_version('assets/news-archive-widgets.css')
    );

    wp_register_style(
        'xinzhou-news-detail-widgets',
        WPMU_PLUGIN_URL . '/xinzhou-content/assets/news-detail-widgets.css',
        ['xinzhou-content'],
        xz_content_asset_version('assets/news-detail-widgets.css')
    );

    wp_register_style(
        'xinzhou-contact-widgets',
        WPMU_PLUGIN_URL . '/xinzhou-content/assets/contact-widgets.css',
        ['xinzhou-content'],
        xz_content_asset_version('assets/contact-widgets.css')
    );

    wp_register_style(
        'xinzhou-global-widgets',
        WPMU_PLUGIN_URL . '/xinzhou-content/assets/global-widgets.css',
        ['xinzhou-content'],
        xz_content_asset_version('assets/global-widgets.css')
    );

    wp_register_style(
        'xinzhou-page-chrome',
        WPMU_PLUGIN_URL . '/xinzhou-content/assets/page-chrome.css',
        ['xinzhou-content', 'xinzhou-global-widgets'],
        xz_content_asset_version('assets/page-chrome.css')
    );

    wp_enqueue_script('xinzhou-content');
    wp_localize_script('xinzhou-content', 'XinzhouContent', [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'inquiryPopupId' => xz_inquiry_popup_id(),
    ]);
});

function xz_product_archive_query_args(int $page, int $per_page, int $term_id = 0): array {
    $args = [
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => max(1, min(24, $per_page)),
        'paged' => max(1, $page),
        'orderby' => 'ID',
        'order' => 'ASC',
    ];
    if ($term_id > 0) {
        $args['tax_query'] = [[
            'taxonomy' => 'product_category',
            'field' => 'term_id',
            'terms' => [$term_id],
        ]];
    }
    return $args;
}

function xz_render_product_archive_card(WP_Post $post, bool $show_label = true): string {
    $terms = wp_get_post_terms($post->ID, 'product_category');
    $label = function_exists('get_field') ? (string) get_field('product_card_label', $post->ID) : '';
    if (!$label && !is_wp_error($terms) && $terms) {
        $label = $terms[0]->name;
    }
    ob_start();
    ?>
    <article class="product-archive-card" data-product-card><a href="<?php echo esc_url(get_permalink($post)); ?>"><figure><?php echo get_the_post_thumbnail($post, 'large', ['loading' => 'lazy']); ?><?php if ($show_label && $label) : ?><span><?php echo esc_html($label); ?></span><?php endif; ?></figure><h3><?php echo esc_html(get_the_title($post)); ?></h3></a></article>
    <?php
    return (string) ob_get_clean();
}

function xz_news_archive_query_args(int $page, int $per_page, int $category_id = 0, int $featured_id = 0): array {
    $args = [
        'post_type' => 'post',
        'post_status' => 'publish',
        'posts_per_page' => max(1, min(24, $per_page)),
        'paged' => max(1, $page),
        'orderby' => 'ID',
        'order' => 'ASC',
        'post__not_in' => array_filter([$featured_id]),
    ];
    if ($category_id > 0) {
        $args['cat'] = $category_id;
    } else {
        $case = get_term_by('slug', 'cases', 'category');
        if ($case instanceof WP_Term) {
            $args['category__not_in'] = [(int) $case->term_id];
        }
    }
    return $args;
}

function xz_render_news_archive_card(WP_Post $post, string $link_text = 'Read More'): string {
    $post_id = $post->ID;
    $categories = get_the_category($post_id);
    ob_start();
    ?>
    <article class="news-card"><a class="news-card__media" href="<?php echo esc_url(get_permalink($post)); ?>"><?php echo get_the_post_thumbnail($post_id, 'large', ['loading' => 'lazy']); ?><?php if ($categories) : ?><span><?php echo esc_html($categories[0]->name); ?></span><?php endif; ?></a><div class="news-card__body"><time datetime="<?php echo esc_attr(get_the_date('c', $post_id)); ?>"><?php echo esc_html(get_the_date('F j, Y', $post_id)); ?></time><h3><a href="<?php echo esc_url(get_permalink($post)); ?>"><?php echo esc_html(get_the_title($post)); ?></a></h3><p><?php echo esc_html(get_the_excerpt($post)); ?></p><a class="news-read-link" href="<?php echo esc_url(get_permalink($post)); ?>"><?php echo esc_html($link_text); ?><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg></a></div></article>
    <?php
    return (string) ob_get_clean();
}

function xz_ajax_product_archive(): void {
    $page = max(1, absint($_POST['page'] ?? 1));
    $per_page = max(1, min(24, absint($_POST['perPage'] ?? 9)));
    $term_id = absint($_POST['termId'] ?? 0);
    $show_label = !empty($_POST['showLabel']);
    $query = new WP_Query(xz_product_archive_query_args($page, $per_page, $term_id));
    $html = '';
    foreach ($query->posts as $post) {
        $html .= xz_render_product_archive_card($post, $show_label);
    }
    wp_send_json_success([
        'html' => $html,
        'page' => $page,
        'totalPages' => (int) $query->max_num_pages,
    ]);
}
add_action('wp_ajax_xz_product_archive', 'xz_ajax_product_archive');
add_action('wp_ajax_nopriv_xz_product_archive', 'xz_ajax_product_archive');

function xz_ajax_news_archive(): void {
    $page = max(1, absint($_POST['page'] ?? 1));
    $per_page = max(1, min(24, absint($_POST['perPage'] ?? 9)));
    $category_id = absint($_POST['categoryId'] ?? 0);
    $featured_id = absint($_POST['featuredId'] ?? 0);
    $link_text = sanitize_text_field(wp_unslash($_POST['linkText'] ?? 'Read More'));
    $query = new WP_Query(xz_news_archive_query_args($page, $per_page, $category_id, $featured_id));
    $html = '';
    foreach ($query->posts as $post) {
        $html .= xz_render_news_archive_card($post, $link_text);
    }
    wp_send_json_success([
        'html' => $html,
        'page' => $page,
        'totalPages' => (int) $query->max_num_pages,
    ]);
}
add_action('wp_ajax_xz_news_archive', 'xz_ajax_news_archive');
add_action('wp_ajax_nopriv_xz_news_archive', 'xz_ajax_news_archive');

add_action('wp', static function (): void {
    $popup_id = xz_inquiry_popup_id();
    if ($popup_id && class_exists('\\ElementorPro\\Modules\\Popup\\Module')) {
        \ElementorPro\Modules\Popup\Module::add_popup_to_location($popup_id);
    }
});

add_action('elementor/elements/categories_registered', static function ($elements_manager): void {
    $elements_manager->add_category('xinzhou-sections', [
        'title' => 'Xinzhou Sections',
        'icon' => 'eicon-folder',
    ]);
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

    if ($query->is_post_type_archive('product') || $query->is_tax('product_category')) {
        $query->set('posts_per_page', 9);
        $query->set('orderby', 'ID');
        $query->set('order', 'ASC');
        return;
    }

    if ($query->is_home() || $query->is_category()) {
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

add_filter('walker_nav_menu_start_el', static function ($item_output, $menu_item, $depth, $args): string {
    $classes = array_filter((array) ($menu_item->classes ?? []));
    $is_contact_card = in_array('xz-mega-menu-contact', $classes, true);
    $is_product_category = ($menu_item->object ?? '') === 'product_category';

    if (!$is_contact_card && !$is_product_category) {
        return (string) $item_output;
    }

    if ($is_contact_card) {
        $content = sprintf(
            '<span class="xz-mega-menu-card xz-mega-menu-card--contact"><span class="xz-mega-menu-card__title">%s</span><span class="xz-mega-menu-card__description">%s</span></span>',
            esc_html((string) $menu_item->title),
            esc_html((string) ($menu_item->description ?: 'Share your product, output and factory requirements with Xinzhou.'))
        );
    } else {
        $image_id = xz_product_term_image((int) $menu_item->object_id);
        $image = $image_id ? wp_get_attachment_image($image_id, 'medium_large', false, [
            'class' => 'xz-mega-menu-card__image',
            'loading' => 'eager',
            'decoding' => 'async',
        ]) : '';
        $content = sprintf(
            '<span class="xz-mega-menu-card"><span class="xz-mega-menu-card__media">%s</span><span class="xz-mega-menu-card__title">%s</span></span>',
            $image,
            esc_html((string) $menu_item->title)
        );
    }

    $output = (string) $item_output;
    $opening_end = strpos($output, '>');
    $closing_start = strripos($output, '</a>');
    if ($opening_end === false || $closing_start === false || $closing_start <= $opening_end) {
        return $output;
    }

    return substr($output, 0, $opening_end + 1) . $content . substr($output, $closing_start);
}, 10, 4);

add_action('elementor/widgets/register', static function ($widgets_manager): void {
    $widget_file = WPMU_PLUGIN_DIR . '/xinzhou-content/elementor-widgets.php';
    if (!file_exists($widget_file)) {
        return;
    }

    require_once $widget_file;
    require_once WPMU_PLUGIN_DIR . '/xinzhou-content/homepage-widgets.php';
    require_once WPMU_PLUGIN_DIR . '/xinzhou-content/about-widgets.php';
    require_once WPMU_PLUGIN_DIR . '/xinzhou-content/service-widgets.php';
    require_once WPMU_PLUGIN_DIR . '/xinzhou-content/product-archive-widgets.php';
    require_once WPMU_PLUGIN_DIR . '/xinzhou-content/product-detail-widgets.php';
    require_once WPMU_PLUGIN_DIR . '/xinzhou-content/news-archive-widgets.php';
    require_once WPMU_PLUGIN_DIR . '/xinzhou-content/news-detail-widgets.php';
    require_once WPMU_PLUGIN_DIR . '/xinzhou-content/contact-widgets.php';
    require_once WPMU_PLUGIN_DIR . '/xinzhou-content/global-widgets.php';
    \Xinzhou\Elementor\register_widgets($widgets_manager);
    \Xinzhou\Elementor\register_homepage_widgets($widgets_manager);
    \Xinzhou\Elementor\register_about_widgets($widgets_manager);
    \Xinzhou\Elementor\register_service_widgets($widgets_manager);
    \Xinzhou\Elementor\register_product_archive_widgets($widgets_manager);
    \Xinzhou\Elementor\register_product_detail_widgets($widgets_manager);
    \Xinzhou\Elementor\register_news_archive_widgets($widgets_manager);
    \Xinzhou\Elementor\register_news_detail_widgets($widgets_manager);
    \Xinzhou\Elementor\register_contact_widgets($widgets_manager);
    \Xinzhou\Elementor\register_global_widgets($widgets_manager);
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

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
    if (!is_singular(['product', 'post']) && !is_post_type_archive('product') && !is_tax('product_category') && !is_home()) {
        return;
    }

    wp_enqueue_style(
        'xinzhou-content',
        WPMU_PLUGIN_URL . '/xinzhou-content/assets/xinzhou-content.css',
        [],
        xz_content_asset_version('assets/xinzhou-content.css')
    );
    wp_enqueue_script(
        'xinzhou-content',
        WPMU_PLUGIN_URL . '/xinzhou-content/assets/xinzhou-content.js',
        [],
        xz_content_asset_version('assets/xinzhou-content.js'),
        true
    );
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

add_shortcode('xinzhou_product_archive_nav', static function (): string {
    if (!taxonomy_exists('product_category')) {
        return '';
    }

    $terms = get_terms([
        'taxonomy' => 'product_category',
        'hide_empty' => false,
        'orderby' => 'menu_order',
        'order' => 'ASC',
    ]);
    if (is_wp_error($terms) || !$terms) {
        return '';
    }

    $current = is_tax('product_category') ? get_queried_object() : null;
    $archive_url = get_post_type_archive_link('product');

    ob_start();
    ?>
    <section class="xz-product-category-nav" aria-label="Product categories">
        <div class="xz-product-category-nav__grid">
            <?php foreach ($terms as $term) :
                $image_id = xz_product_term_image((int) $term->term_id);
                $is_active = $current instanceof WP_Term && (int) $current->term_id === (int) $term->term_id;
                ?>
                <a class="xz-product-category-tile<?php echo $is_active ? ' is-active' : ''; ?>" href="<?php echo esc_url(get_term_link($term)); ?>">
                    <?php echo $image_id ? wp_get_attachment_image($image_id, 'large', false, ['loading' => 'lazy']) : ''; ?>
                    <span><?php echo esc_html($term->name); ?></span>
                </a>
            <?php endforeach; ?>
        </div>
        <div class="xz-product-category-intro">
            <h1><?php echo esc_html($current instanceof WP_Term ? $current->name : 'Products'); ?></h1>
            <?php
            $description = $current instanceof WP_Term && function_exists('get_field')
                ? get_field('category_short_description', 'product_category_' . $current->term_id)
                : 'Automated resistance welding machines and complete production lines engineered around real manufacturing requirements.';
            if ($description) :
                ?><p><?php echo esc_html($description); ?></p><?php
            endif;
            ?>
            <?php if ($current instanceof WP_Term && $archive_url) : ?>
                <a class="xz-product-category-intro__all" href="<?php echo esc_url($archive_url); ?>">View All Products</a>
            <?php endif; ?>
        </div>
    </section>
    <?php
    return (string) ob_get_clean();
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

function xz_product_related_ids(int $post_id): array {
    $related = function_exists('get_field') ? get_field('related_products', $post_id) : [];
    $ids = array_values(array_filter(array_map(static function ($item): int {
        return $item instanceof WP_Post ? (int) $item->ID : (int) $item;
    }, (array) $related)));

    if ($ids) {
        return $ids;
    }

    $terms = wp_get_post_terms($post_id, 'product_category', ['fields' => 'ids']);
    return get_posts([
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => 3,
        'post__not_in' => [$post_id],
        'fields' => 'ids',
        'tax_query' => $terms ? [[
            'taxonomy' => 'product_category',
            'field' => 'term_id',
            'terms' => $terms,
        ]] : [],
    ]);
}

add_shortcode('xinzhou_product_single', static function (): string {
    if (!is_singular('product')) {
        return '';
    }

    $post_id = get_the_ID();
    $gallery = xz_product_gallery_ids($post_id);
    $short_description = function_exists('get_field') ? get_field('product_short_description', $post_id) : '';
    $short_description = $short_description ?: get_the_excerpt($post_id);
    $parameters = function_exists('get_field') ? (array) get_field('product_key_parameters', $post_id) : [];
    $overview = function_exists('get_field') ? get_field('product_overview', $post_id) : '';
    $overview = $overview ?: get_post_field('post_content', $post_id);
    $specifications = function_exists('get_field') ? (array) get_field('product_specifications', $post_id) : [];
    $finished_products = function_exists('get_field') ? (array) get_field('product_finished_products', $post_id) : [];
    $workflow = function_exists('get_field') ? (array) get_field('product_configuration_workflow', $post_id) : [];
    $faq = function_exists('get_field') ? (array) get_field('product_faq', $post_id) : [];
    $terms = wp_get_post_terms($post_id, 'product_category');
    $primary_term = !is_wp_error($terms) && $terms ? $terms[0] : null;

    $tabs = array_filter([
        'overview' => $overview ? 'Overview' : '',
        'specifications' => $specifications ? 'Technical Specifications' : '',
        'finished-products' => $finished_products ? 'Finished Products' : '',
        'faq' => $faq ? 'FAQ' : '',
        'workflow' => $workflow ? 'Configuration & Workflow' : '',
    ]);

    ob_start();
    ?>
    <main class="xz-product-single">
        <section class="xz-product-single__hero">
            <div class="xz-product-single__grid">
                <div class="xz-product-gallery" data-xz-product-gallery>
                    <figure class="xz-product-gallery__main">
                        <?php if ($gallery) :
                            echo wp_get_attachment_image($gallery[0], 'full', false, ['data-xz-main-image' => '']);
                        endif; ?>
                    </figure>
                    <?php if (count($gallery) > 1) : ?>
                        <div class="xz-product-gallery__thumbs" aria-label="Product gallery thumbnails">
                            <?php foreach ($gallery as $index => $image_id) :
                                $full = wp_get_attachment_image_url($image_id, 'full');
                                ?>
                                <button type="button" class="<?php echo $index === 0 ? 'is-active' : ''; ?>" data-xz-gallery-thumb data-full-src="<?php echo esc_url($full); ?>" aria-pressed="<?php echo $index === 0 ? 'true' : 'false'; ?>">
                                    <?php echo wp_get_attachment_image($image_id, 'thumbnail', false, ['alt' => '']); ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="xz-product-summary">
                    <nav class="xz-product-breadcrumb" aria-label="Breadcrumb">
                        <a href="<?php echo esc_url(home_url('/')); ?>">Home</a><span>/</span>
                        <a href="<?php echo esc_url(get_post_type_archive_link('product')); ?>">Products</a>
                        <?php if ($primary_term instanceof WP_Term) : ?><span>/</span><a href="<?php echo esc_url(get_term_link($primary_term)); ?>"><?php echo esc_html($primary_term->name); ?></a><?php endif; ?>
                    </nav>
                    <?php if ($primary_term instanceof WP_Term) : ?><p class="xz-product-summary__category"><?php echo esc_html($primary_term->name); ?></p><?php endif; ?>
                    <h1><?php the_title(); ?></h1>
                    <?php if ($short_description) : ?><p class="xz-product-summary__description"><?php echo esc_html($short_description); ?></p><?php endif; ?>

                    <?php if ($parameters) : ?>
                        <dl class="xz-product-summary__facts">
                            <?php foreach (array_slice($parameters, 0, 4) as $parameter) : ?>
                                <div><dt><?php echo esc_html($parameter['product_parameter_label'] ?? ''); ?></dt><dd><?php echo esc_html($parameter['product_parameter_value'] ?? ''); ?></dd></div>
                            <?php endforeach; ?>
                        </dl>
                    <?php endif; ?>

                    <?php if ($finished_products) : ?>
                        <div class="xz-product-summary__finished">
                            <h2>Finished Products</h2>
                            <div>
                                <?php foreach (array_slice($finished_products, 0, 2) as $finished) : ?><span><?php echo esc_html($finished['finished_product_title'] ?? ''); ?></span><?php endforeach; ?>
                                <?php if (count($finished_products) > 2) : ?><a href="#xz-tab-finished-products" data-xz-open-tab="finished-products">View All</a><?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <a class="xz-product-inquiry" href="<?php echo esc_url(home_url('/contact/#inquiry')); ?>" data-inquiry-open>Send an Inquiry</a>
                </div>
            </div>
        </section>

        <?php if ($tabs) : ?>
            <section class="xz-product-information" id="product-information">
                <div class="xz-product-information__inner" data-xz-product-tabs>
                    <div class="xz-product-tabs__list" role="tablist" aria-label="Product information">
                        <?php $tab_index = 0; foreach ($tabs as $tab_key => $tab_label) : ?>
                            <button type="button" role="tab" data-xz-tab="<?php echo esc_attr($tab_key); ?>" aria-selected="<?php echo $tab_index === 0 ? 'true' : 'false'; ?>" class="<?php echo $tab_index === 0 ? 'is-active' : ''; ?>"><?php echo esc_html($tab_label); ?></button>
                        <?php $tab_index++; endforeach; ?>
                    </div>

                    <?php $panel_index = 0; foreach ($tabs as $tab_key => $tab_label) : ?>
                        <div id="xz-tab-<?php echo esc_attr($tab_key); ?>" class="xz-product-tabs__panel<?php echo $panel_index === 0 ? ' is-active' : ''; ?>" data-xz-tab-panel="<?php echo esc_attr($tab_key); ?>" <?php echo $panel_index === 0 ? '' : 'hidden'; ?>>
                            <?php if ($tab_key === 'overview') : ?>
                                <div class="xz-product-overview"><?php echo apply_filters('the_content', $overview); ?></div>
                            <?php elseif ($tab_key === 'specifications') : ?>
                                <div class="xz-product-specifications"><table><tbody>
                                <?php foreach ($specifications as $specification) : ?><tr><th><?php echo esc_html($specification['specification_parameter'] ?? ''); ?></th><td><?php echo wp_kses_post($specification['specification_value'] ?? ''); ?></td></tr><?php endforeach; ?>
                                </tbody></table></div>
                            <?php elseif ($tab_key === 'finished-products') : ?>
                                <div class="xz-finished-products-grid">
                                <?php foreach ($finished_products as $finished) : $image_id = xz_acf_image_id($finished['finished_product_image'] ?? 0); ?>
                                    <figure><?php echo $image_id ? wp_get_attachment_image($image_id, 'large') : ''; ?><figcaption><?php echo esc_html($finished['finished_product_title'] ?? ''); ?></figcaption></figure>
                                <?php endforeach; ?>
                                </div>
                            <?php elseif ($tab_key === 'workflow') : ?>
                                <div class="xz-product-workflow">
                                <?php foreach ($workflow as $index => $step) : ?><article><span><?php echo esc_html(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)); ?></span><div><h3><?php echo esc_html($step['workflow_step_title'] ?? ''); ?></h3><p><?php echo esc_html($step['workflow_step_description'] ?? ''); ?></p></div></article><?php endforeach; ?>
                                </div>
                            <?php elseif ($tab_key === 'faq') : ?>
                                <div class="xz-product-faq">
                                <?php foreach ($faq as $index => $item) : ?><details<?php echo $index === 0 ? ' open' : ''; ?>><summary><?php echo esc_html($item['faq_question'] ?? ''); ?></summary><div><?php echo wp_kses_post(wpautop($item['faq_answer'] ?? '')); ?></div></details><?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php $panel_index++; endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <?php $related_ids = xz_product_related_ids($post_id); if ($related_ids) : ?>
            <section class="xz-related-products">
                <div class="xz-related-products__inner">
                    <h2>Related Products</h2>
                    <div class="xz-related-products__grid">
                        <?php foreach ($related_ids as $related_id) : ?>
                            <article><a href="<?php echo esc_url(get_permalink($related_id)); ?>"><figure><?php echo get_the_post_thumbnail($related_id, 'large'); ?></figure><h3><?php echo esc_html(get_the_title($related_id)); ?></h3></a></article>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>
    </main>
    <?php
    return (string) ob_get_clean();
});

add_shortcode('xinzhou_breadcrumb', static function (): string {
    if (!is_singular('post')) {
        return '';
    }
    $news_page = (int) get_option('page_for_posts');
    $news_url = $news_page ? get_permalink($news_page) : home_url('/news/');
    return sprintf(
        '<nav class="xz-article-breadcrumb" aria-label="Breadcrumb"><a href="%s">Home</a><span>/</span><a href="%s">News</a></nav>',
        esc_url(home_url('/')),
        esc_url($news_url)
    );
});

add_shortcode('xinzhou_post_meta', static function (): string {
    if (!is_singular('post')) {
        return '';
    }
    $categories = get_the_category_list(', ');
    $location = function_exists('get_field') ? get_field('article_location') : '';
    $parts = ['<time datetime="' . esc_attr(get_the_date('c')) . '">' . esc_html(get_the_date('F j, Y')) . '</time>'];
    if ($categories) {
        $parts[] = '<span>' . wp_kses_post($categories) . '</span>';
    }
    if ($location) {
        $parts[] = '<span>' . esc_html($location) . '</span>';
    }
    return '<div class="xz-article-meta">' . implode('', $parts) . '</div>';
});

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

add_shortcode('xinzhou_article_sidebar', static function (): string {
    if (!is_singular('post')) {
        return '';
    }
    $content = get_post_field('post_content', get_the_ID());
    preg_match_all('/<h2\b[^>]*>(.*?)<\/h2>/is', $content, $matches);
    ob_start();
    ?>
    <aside class="xz-article-sidebar">
        <?php if (!empty($matches[1])) : ?>
            <nav class="xz-article-outline" aria-label="Article contents">
                <h2>Contents</h2>
                <ol>
                    <?php foreach ($matches[1] as $index => $heading) : ?><li><a href="#<?php echo esc_attr(xz_article_heading_slug($heading, $index)); ?>"><?php echo esc_html(wp_strip_all_tags($heading)); ?></a></li><?php endforeach; ?>
                </ol>
            </nav>
        <?php endif; ?>
        <div class="xz-article-inquiry">
            <h2>Discuss Your Project</h2>
            <p>Share your product specifications, target output and factory requirements with Xinzhou.</p>
            <a href="<?php echo esc_url(home_url('/contact/#inquiry')); ?>" data-inquiry-open>Send an Inquiry</a>
        </div>
    </aside>
    <?php
    return (string) ob_get_clean();
});

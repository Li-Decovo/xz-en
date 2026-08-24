<?php

namespace Xinzhou\Elementor;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

if (!defined('ABSPATH')) {
    exit;
}

function current_content_id(string $post_type): int {
    $candidates = [get_queried_object_id(), get_the_ID()];
    foreach ($candidates as $candidate) {
        if ($candidate && get_post_type($candidate) === $post_type) {
            return (int) $candidate;
        }
    }

    $preview = get_posts([
        'post_type' => $post_type,
        'post_status' => 'publish',
        'posts_per_page' => 1,
        'fields' => 'ids',
    ]);
    return $preview ? (int) $preview[0] : 0;
}

function current_product_term(): ?\WP_Term {
    $queried = get_queried_object();
    if ($queried instanceof \WP_Term && $queried->taxonomy === 'product_category') {
        return $queried;
    }

    $post_id = current_content_id('product');
    if ($post_id) {
        return \xz_product_primary_term($post_id);
    }

    $terms = get_terms([
        'taxonomy' => 'product_category',
        'hide_empty' => false,
        'number' => 1,
    ]);
    return !is_wp_error($terms) && $terms ? $terms[0] : null;
}

abstract class Xinzhou_Widget extends Widget_Base {
    public function get_categories(): array {
        return ['general'];
    }

    public function get_style_depends(): array {
        return ['xinzhou-content'];
    }
}

function product_category_control_options(): array {
    $terms = get_terms(['taxonomy' => 'product_category', 'hide_empty' => false]);
    if (is_wp_error($terms)) { return []; }
    $options = [];
    foreach ($terms as $term) { $options[(int) $term->term_id] = $term->name; }
    return $options;
}

function post_control_options(string $post_type = 'post'): array {
    $posts = get_posts(['post_type' => $post_type, 'post_status' => 'publish', 'posts_per_page' => -1, 'orderby' => 'date', 'order' => 'DESC']);
    $options = [];
    foreach ($posts as $post) { $options[(int) $post->ID] = get_the_title($post); }
    return $options;
}

function post_category_control_options(): array {
    $terms = get_terms(['taxonomy' => 'category', 'hide_empty' => false]);
    if (is_wp_error($terms)) { return []; }
    $options = [];
    foreach ($terms as $term) { $options[(int) $term->term_id] = $term->name; }
    return $options;
}

final class Product_Categories_Widget extends Xinzhou_Widget {
    public function get_name(): string {
        return 'xinzhou-product-categories';
    }

    public function get_title(): string {
        return 'Xinzhou Product Categories';
    }

    public function get_icon(): string {
        return 'eicon-gallery-grid';
    }

    public function get_style_depends(): array {
        return ['xinzhou-content', 'xinzhou-product-archive-widgets'];
    }

    protected function register_controls(): void {
        $this->start_controls_section('content', ['label' => 'Categories']);
        $this->add_control('category_source', [
            'label' => 'Categories to Display',
            'type' => Controls_Manager::SELECT,
            'default' => 'all',
            'options' => ['all' => 'All Categories', 'selected' => 'Selected Categories'],
        ]);
        $this->add_control('category_ids', [
            'label' => 'Select Categories',
            'type' => Controls_Manager::SELECT2,
            'multiple' => true,
            'options' => product_category_control_options(),
            'condition' => ['category_source' => 'selected'],
        ]);
        $this->add_control('hide_empty', [
            'label' => 'Hide Empty Categories',
            'type' => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => '',
        ]);
        $this->end_controls_section();
    }

    protected function render(): void {
        $homepage_layout = is_front_page() || (int) get_queried_object_id() === 19;
        if (!$homepage_layout) {
            return;
        }

        $settings = $this->get_settings_for_display();
        $term_args = [
            'taxonomy' => 'product_category',
            'hide_empty' => ($settings['hide_empty'] ?? '') === 'yes',
        ];
        if (($settings['category_source'] ?? 'all') === 'selected') {
            $selected = array_values(array_filter(array_map('intval', (array) ($settings['category_ids'] ?? []))));
            if (!$selected) { return; }
            $term_args['include'] = $selected;
        }
        $terms = get_terms($term_args);
        if (is_wp_error($terms) || !$terms) {
            return;
        }

        usort($terms, static function (\WP_Term $left, \WP_Term $right): int {
            $left_order = (int) get_term_meta($left->term_id, 'category_display_order', true);
            $right_order = (int) get_term_meta($right->term_id, 'category_display_order', true);
            return $left_order === $right_order
                ? strcasecmp($left->name, $right->name)
                : $left_order <=> $right_order;
        });

        $current = is_tax('product_category') ? current_product_term() : null;
        ?>
        <?php $archive_layout = !$homepage_layout; ?>
        <<?php echo $archive_layout ? 'section' : 'nav'; ?> class="<?php echo $archive_layout ? 'product-category-nav xz-simple-carousel is-carousel' : 'xz-product-category-nav xz-product-category-nav--homepage xz-simple-carousel is-carousel'; ?>" data-xz-simple-carousel data-visible="<?php echo $archive_layout ? '7' : '7'; ?>" data-visible-tablet="5" data-visible-mobile="2" aria-label="Product categories">
            <div class="xz-simple-carousel__controls"><button type="button" data-xz-simple-prev aria-label="Previous product categories">&#8249;</button><button type="button" data-xz-simple-next aria-label="Next product categories">&#8250;</button></div>
            <div class="<?php echo $archive_layout ? 'product-category-nav__grid' : 'xz-product-category-nav__grid'; ?>" data-xz-simple-track>
                <?php foreach ($terms as $term) :
                    $image_id = \xz_product_term_image((int) $term->term_id);
                    $active = $current && (int) $current->term_id === (int) $term->term_id;
                    ?>
                    <a class="<?php echo $archive_layout ? 'product-category-tile' : 'xz-product-category-tile'; ?><?php echo $active ? ' is-active' : ''; ?>" href="<?php echo esc_url(get_term_link($term)); ?>"<?php echo $active ? ' aria-current="page"' : ''; ?>>
                        <?php echo $image_id ? wp_get_attachment_image($image_id, 'large', false, ['loading' => 'lazy']) : ''; ?>
                        <span><?php echo esc_html($term->name); ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </<?php echo $archive_layout ? 'section' : 'nav'; ?>>
        <?php
    }
}

final class Product_Category_Content_Widget extends Xinzhou_Widget {
    private function remove_duplicate_leading_heading(string $content, string $title): string {
        if ($content === '' || $title === '') {
            return $content;
        }

        if (!preg_match('/^\s*<h([1-6])\b[^>]*>(.*?)<\/h\1>\s*/is', $content, $matches)) {
            return $content;
        }

        $normalize = static function (string $value): string {
            $value = html_entity_decode(wp_strip_all_tags($value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            return trim((string) preg_replace('/\s+/u', ' ', $value));
        };

        if (strcasecmp($normalize($matches[2]), $normalize($title)) !== 0) {
            return $content;
        }

        return (string) substr($content, strlen($matches[0]));
    }

    public function get_name(): string {
        return 'xinzhou-product-category-content';
    }

    public function get_title(): string {
        return 'Xinzhou Product Category Content';
    }

    public function get_icon(): string {
        return 'eicon-archive-title';
    }

    public function get_style_depends(): array {
        return ['xinzhou-content', 'xinzhou-product-archive-widgets'];
    }

    protected function register_controls(): void {
        $this->start_controls_section('content', ['label' => 'Content']);
        $this->add_control('content_type', [
            'label' => 'Content',
            'type' => Controls_Manager::SELECT,
            'default' => 'short',
            'options' => [
                'short' => 'Short Description',
                'detailed' => 'Detailed Description',
            ],
        ]);
        $this->add_control('show_title', [
            'label' => 'Show Category Title',
            'type' => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => 'yes',
        ]);
        $this->add_control('eyebrow', [
            'label' => 'Detailed Section Label',
            'type' => Controls_Manager::TEXT,
            'default' => 'Category Description',
            'condition' => ['content_type' => 'detailed'],
        ]);
        $this->add_control('all_products_link', [
            'label' => 'Show All Products Link',
            'type' => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => '',
        ]);
        $this->add_control('fallback_content', [
            'label' => 'Products Archive Content',
            'type' => Controls_Manager::WYSIWYG,
            'default' => 'Automated resistance welding machines and complete production lines engineered around real manufacturing requirements.',
        ]);
        $this->end_controls_section();

        $this->start_controls_section('inquiry_sidebar', [
            'label' => 'Inquiry Sidebar',
            'condition' => ['content_type' => 'detailed'],
        ]);
        $this->add_control('inquiry_label', [
            'label' => 'Label',
            'type' => Controls_Manager::TEXT,
            'default' => 'Project Inquiry',
        ]);
        $this->add_control('inquiry_title', [
            'label' => 'Title',
            'type' => Controls_Manager::TEXT,
            'default' => 'Plan Your Production Line',
        ]);
        $this->add_control('inquiry_copy', [
            'label' => 'Description',
            'type' => Controls_Manager::TEXTAREA,
            'default' => 'Share your finished product, material specifications, target output and factory layout. Xinzhou will prepare a practical equipment proposal.',
        ]);
        $this->add_control('inquiry_button', [
            'label' => 'Button Text',
            'type' => Controls_Manager::TEXT,
            'default' => 'Get a Line Proposal',
        ]);
        $this->end_controls_section();
    }

    protected function render(): void {
        $settings = $this->get_settings_for_display();
        $term = current_product_term();
        $is_archive = is_tax('product_category');
        $content_type = $settings['content_type'] ?? 'short';
        $name = $is_archive && $term ? $term->name : 'Products';
        if ($is_archive && $term && function_exists('get_field')) {
            $title_field = $content_type === 'detailed' ? 'category_detailed_title' : 'category_archive_title';
            $field_title = (string) get_field($title_field, 'product_category_' . $term->term_id);
            if ($field_title) { $name = $field_title; }
        }
        $field = $content_type === 'detailed'
            ? 'category_long_description'
            : 'category_short_description';
        $content = '';
        if ($is_archive && $term && function_exists('get_field')) {
            $content = (string) get_field($field, 'product_category_' . $term->term_id);
        }
        if (!$content && $is_archive && $term) {
            $content = term_description($term);
        }
        if (!$content && !$is_archive) {
            $content = (string) ($settings['fallback_content'] ?? '');
        }
        if ($content_type === 'detailed') {
            $content = $this->remove_duplicate_leading_heading($content, $name);
        }
        ?>
        <?php if ($content_type === 'short') : ?>
            <section class="product-category-intro"><div class="xz-container product-category-intro__inner">
                <?php if (($settings['show_title'] ?? '') === 'yes') : ?><h2><?php echo esc_html($name); ?></h2><?php endif; ?>
                <?php if ($content) : ?><div class="xz-product-category-copy"><?php echo wp_kses_post(wpautop($content)); ?></div><?php endif; ?>
                <?php if (($settings['all_products_link'] ?? '') === 'yes' && $is_archive) : ?><a class="xz-product-category-intro__all" href="<?php echo esc_url(get_post_type_archive_link('product')); ?>">View All Products</a><?php endif; ?>
            </div></section>
        <?php else : ?>
            <section class="product-category-detail" aria-labelledby="category-detail-title"><div class="xz-container product-category-detail__grid">
                <div><?php if (!empty($settings['eyebrow'])) : ?><p class="products-eyebrow"><?php echo esc_html($settings['eyebrow']); ?></p><?php endif; ?><?php if ($name) : ?><h2 id="category-detail-title"><?php echo esc_html($name); ?></h2><?php endif; ?></div>
                <div class="product-category-detail__body">
                    <?php if ($content) : ?><div class="product-category-detail__copy"><?php echo wp_kses_post(wpautop($content)); ?></div><?php endif; ?>
                    <aside class="product-category-inquiry" aria-label="Product line inquiry">
                        <?php if (!empty($settings['inquiry_label'])) : ?><p class="product-category-inquiry__label"><?php echo esc_html((string) $settings['inquiry_label']); ?></p><?php endif; ?>
                        <?php if (!empty($settings['inquiry_title'])) : ?><h3><?php echo esc_html((string) $settings['inquiry_title']); ?></h3><?php endif; ?>
                        <?php if (!empty($settings['inquiry_copy'])) : ?><p><?php echo esc_html((string) $settings['inquiry_copy']); ?></p><?php endif; ?>
                        <button class="product-category-inquiry__button" type="button" data-inquiry-open><?php echo esc_html((string) ($settings['inquiry_button'] ?? 'Get a Line Proposal')); ?><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg></button>
                    </aside>
                </div>
            </div></section>
        <?php endif; ?>
        <?php
    }
}

final class News_Categories_Widget extends Xinzhou_Widget {
    public function get_name(): string {
        return 'xinzhou-news-categories';
    }

    public function get_title(): string {
        return 'Xinzhou News Categories';
    }

    public function get_icon(): string {
        return 'eicon-post-list';
    }

    protected function register_controls(): void {
        $this->start_controls_section('content', ['label' => 'Categories']);
        $this->add_control('show_counts', [
            'label' => 'Show Post Counts',
            'type' => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => '',
        ]);
        $this->end_controls_section();
    }

    protected function render(): void {
        $settings = $this->get_settings_for_display();
        $terms = get_terms([
            'taxonomy' => 'category',
            'hide_empty' => true,
            'orderby' => 'name',
            'order' => 'ASC',
        ]);
        if (is_wp_error($terms) || !$terms) {
            return;
        }
        $current = get_queried_object();
        $news_page = (int) get_option('page_for_posts');
        ?>
        <nav class="xz-news-categories" aria-label="News categories">
            <a class="<?php echo is_home() ? 'is-active' : ''; ?>" href="<?php echo esc_url($news_page ? get_permalink($news_page) : home_url('/news/')); ?>">All News</a>
            <?php foreach ($terms as $term) : ?>
                <a class="<?php echo $current instanceof \WP_Term && (int) $current->term_id === (int) $term->term_id ? 'is-active' : ''; ?>" href="<?php echo esc_url(get_term_link($term)); ?>">
                    <?php echo esc_html($term->name); ?><?php if (($settings['show_counts'] ?? '') === 'yes') : ?> <span><?php echo esc_html((string) $term->count); ?></span><?php endif; ?>
                </a>
            <?php endforeach; ?>
        </nav>
        <?php
    }
}

final class Breadcrumbs_Widget extends Xinzhou_Widget {
    public function get_name(): string {
        return 'xinzhou-breadcrumbs';
    }

    public function get_title(): string {
        return 'Xinzhou Breadcrumbs';
    }

    public function get_icon(): string {
        return 'eicon-navigation-horizontal';
    }

    protected function render(): void {
        $post_id = get_queried_object_id();
        $post_type = get_post_type($post_id);
        $items = [['Home', home_url('/')]];
        if ($post_type === 'product') {
            $items[] = ['Products', get_post_type_archive_link('product')];
            $term = current_product_term();
            if ($term) {
                $items[] = [$term->name, get_term_link($term)];
            }
        } elseif ($post_type === 'post') {
            $news_page = (int) get_option('page_for_posts');
            $items[] = ['News', $news_page ? get_permalink($news_page) : home_url('/news/')];
        }
        ?>
        <nav class="xz-breadcrumbs" aria-label="Breadcrumb">
            <?php foreach ($items as $index => $item) : ?>
                <?php if ($index) : ?><span aria-hidden="true">/</span><?php endif; ?>
                <a href="<?php echo esc_url($item[1]); ?>"><?php echo esc_html($item[0]); ?></a>
            <?php endforeach; ?>
        </nav>
        <?php
    }
}

final class Product_Gallery_Widget extends Xinzhou_Widget {
    public function get_name(): string {
        return 'xinzhou-product-gallery';
    }

    public function get_title(): string {
        return 'Xinzhou Product Gallery';
    }

    public function get_icon(): string {
        return 'eicon-gallery-group';
    }

    public function get_script_depends(): array {
        return ['xinzhou-content'];
    }

    protected function render(): void {
        $post_id = current_content_id('product');
        $gallery = $post_id ? \xz_product_gallery_ids($post_id) : [];
        if (!$gallery) {
            return;
        }
        ?>
        <div class="xz-product-gallery" data-xz-product-gallery>
            <figure class="xz-product-gallery__main">
                <?php echo wp_get_attachment_image($gallery[0], 'full', false, ['data-xz-main-image' => '']); ?>
            </figure>
            <?php if (count($gallery) > 1) : ?>
                <div class="xz-product-gallery__thumbs" aria-label="Product gallery thumbnails">
                    <?php foreach ($gallery as $index => $image_id) : ?>
                        <button type="button" class="<?php echo $index === 0 ? 'is-active' : ''; ?>" data-xz-gallery-thumb data-full-src="<?php echo esc_url(wp_get_attachment_image_url($image_id, 'full')); ?>" data-full-alt="<?php echo esc_attr((string) get_post_meta($image_id, '_wp_attachment_image_alt', true)); ?>" data-full-title="<?php echo esc_attr((string) get_the_title($image_id)); ?>" aria-pressed="<?php echo $index === 0 ? 'true' : 'false'; ?>">
                            <?php echo wp_get_attachment_image($image_id, 'thumbnail'); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }
}

final class Article_Meta_Widget extends Xinzhou_Widget {
    public function get_name(): string {
        return 'xinzhou-article-meta';
    }

    public function get_title(): string {
        return 'Xinzhou Article Meta';
    }

    public function get_icon(): string {
        return 'eicon-post-info';
    }

    protected function register_controls(): void {
        $this->start_controls_section('content', ['label' => 'Article Meta']);
        foreach ([
            'show_date' => ['Show Date', 'yes'],
            'show_categories' => ['Show Categories', 'yes'],
            'show_location' => ['Show Location', 'yes'],
        ] as $key => $definition) {
            $this->add_control($key, [
                'label' => $definition[0],
                'type' => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default' => $definition[1],
            ]);
        }
        $this->end_controls_section();
    }

    protected function render(): void {
        $post_id = current_content_id('post');
        if (!$post_id) {
            return;
        }
        $settings = $this->get_settings_for_display();
        $items = [];
        if (($settings['show_date'] ?? '') === 'yes') {
            $items[] = '<time datetime="' . esc_attr(get_the_date('c', $post_id)) . '">' . esc_html(get_the_date('F j, Y', $post_id)) . '</time>';
        }
        if (($settings['show_categories'] ?? '') === 'yes') {
            $categories = get_the_category_list(', ', '', $post_id);
            if ($categories) {
                $items[] = '<span>' . wp_kses_post($categories) . '</span>';
            }
        }
        if (($settings['show_location'] ?? '') === 'yes' && function_exists('get_field')) {
            $location = get_field('article_location', $post_id);
            if ($location) {
                $items[] = '<span>' . esc_html($location) . '</span>';
            }
        }
        if ($items) {
            echo '<div class="xz-article-meta">' . implode('', $items) . '</div>';
        }
    }
}

final class Product_Summary_Data_Widget extends Xinzhou_Widget {
    public function get_name(): string {
        return 'xinzhou-product-summary-data';
    }

    public function get_title(): string {
        return 'Xinzhou Product Summary Data';
    }

    public function get_icon(): string {
        return 'eicon-info-box';
    }

    protected function register_controls(): void {
        $this->start_controls_section('content', ['label' => 'Content']);
        $this->end_controls_section();
    }

    protected function render(): void {
        $post_id = current_content_id('product');
        if (!$post_id || !function_exists('get_field')) {
            return;
        }
        $parameters = (array) get_field('product_key_parameters', $post_id);
        $finished = \xz_product_finished_image_ids($post_id);
        ?>
        <div class="xz-product-summary-data">
            <?php if ($parameters) : ?>
                <dl class="xz-product-summary__facts">
                    <?php foreach ($parameters as $parameter) : ?>
                        <div><dt><?php echo esc_html($parameter['product_parameter_label'] ?? ''); ?></dt><dd><?php echo esc_html($parameter['product_parameter_value'] ?? ''); ?></dd></div>
                    <?php endforeach; ?>
                </dl>
            <?php endif; ?>
            <?php if ($finished) : ?>
                <div class="xz-product-summary__finished">
                    <h2>Finished Products</h2>
                    <div>
                        <?php foreach (array_slice($finished, 0, 2) as $image_id) : ?>
                            <a href="#tab-finished-products" data-xz-open-tab="finished-products"><?php echo esc_html(\xz_attachment_display_title($image_id)); ?></a>
                        <?php endforeach; ?>
                        <a href="#tab-finished-products" data-xz-open-tab="finished-products">View All</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }
}

final class Product_Information_Widget extends Xinzhou_Widget {
    public function get_name(): string {
        return 'xinzhou-product-information';
    }

    public function get_title(): string {
        return 'Xinzhou Product Information';
    }

    public function get_icon(): string {
        return 'eicon-tabs';
    }

    public function get_script_depends(): array {
        return ['xinzhou-content'];
    }

    protected function register_controls(): void {
        $this->start_controls_section('labels', ['label' => 'Tab Labels']);
        foreach ([
            'overview_label' => 'Overview',
            'specifications_label' => 'Technical Specifications',
            'finished_label' => 'Finished Products',
            'faq_label' => 'FAQ',
            'workflow_label' => 'Configuration & Workflow',
        ] as $key => $default) {
            $this->add_control($key, [
                'label' => $default,
                'type' => Controls_Manager::TEXT,
                'default' => $default,
            ]);
        }
        $this->end_controls_section();
        $this->start_controls_section('workflow', ['label' => 'Configuration & Workflow']);
        $this->add_control('workflow_content', [
            'label' => 'Content',
            'type' => Controls_Manager::WYSIWYG,
            'default' => '<div class="product-workflow"><article><span>01</span><div><h3>Production Requirements</h3><p>Confirm the finished product, output target, material specifications and available factory space.</p></div></article><article><span>02</span><div><h3>Line Configuration</h3><p>Plan the welding machine, feeding, forming, cutting, discharge and stacking equipment as one coordinated system.</p></div></article><article><span>03</span><div><h3>Installation & Commissioning</h3><p>Complete installation guidance, production testing, operator training and technical handover.</p></div></article></div>',
        ]);
        $this->end_controls_section();
    }

    protected function render(): void {
        $post_id = current_content_id('product');
        if (!$post_id) {
            return;
        }
        $settings = $this->get_settings_for_display();
        $overview = function_exists('get_field') ? (string) get_field('product_overview_primary', $post_id) : '';
        if (!$overview) { $overview = (string) get_post_field('post_content', $post_id); }
        $overview_secondary = function_exists('get_field') ? (string) get_field('product_overview_secondary', $post_id) : '';
        $specifications = function_exists('get_field') ? (string) get_field('product_specifications', $post_id) : '';
        $finished = \xz_product_finished_image_ids($post_id);
        $faq = function_exists('get_field') ? (array) get_field('product_faq', $post_id) : [];
        $workflow = (string) ($settings['workflow_content'] ?? '');
        $tabs = array_filter([
            'overview' => $overview ? ($settings['overview_label'] ?? 'Overview') : '',
            'specifications' => $specifications ? ($settings['specifications_label'] ?? 'Technical Specifications') : '',
            'finished-products' => $finished ? ($settings['finished_label'] ?? 'Finished Products') : '',
            'faq' => $faq ? ($settings['faq_label'] ?? 'FAQ') : '',
            'workflow' => $workflow ? ($settings['workflow_label'] ?? 'Configuration & Workflow') : '',
        ]);
        if (!$tabs) {
            return;
        }
        ?>
        <div class="xz-product-information__inner" id="product-information" data-xz-product-tabs>
            <div class="xz-product-tabs__list" role="tablist" aria-label="Product information">
                <?php $index = 0; foreach ($tabs as $key => $label) : ?>
                    <button type="button" role="tab" data-xz-tab="<?php echo esc_attr($key); ?>" aria-selected="<?php echo $index === 0 ? 'true' : 'false'; ?>" class="<?php echo $index === 0 ? 'is-active' : ''; ?>"><?php echo esc_html($label); ?></button>
                <?php $index++; endforeach; ?>
            </div>
            <?php $index = 0; foreach ($tabs as $key => $label) : ?>
                <div id="xz-tab-<?php echo esc_attr($key); ?>" class="xz-product-tabs__panel<?php echo $index === 0 ? ' is-active' : ''; ?>" data-xz-tab-panel="<?php echo esc_attr($key); ?>" <?php echo $index === 0 ? '' : 'hidden'; ?>>
                    <?php if ($key === 'overview') : ?>
                        <div class="xz-product-overview"><?php echo apply_filters('the_content', $overview); ?><?php if ($overview_secondary) : ?><div class="product-overview-description"><?php echo apply_filters('the_content', $overview_secondary); ?></div><?php endif; ?></div>
                    <?php elseif ($key === 'specifications') : ?>
                        <div class="xz-product-specifications product-specifications"><div class="product-specifications__table-wrap"><?php echo wp_kses_post($specifications); ?></div></div>
                    <?php elseif ($key === 'finished-products') : ?>
                        <div class="xz-finished-products-grid">
                        <?php foreach ($finished as $image_id) : ?>
                            <figure><?php echo wp_get_attachment_image($image_id, 'large'); ?><figcaption><?php echo esc_html(\xz_attachment_display_title($image_id)); ?></figcaption></figure>
                        <?php endforeach; ?>
                        </div>
                    <?php elseif ($key === 'faq') : ?>
                        <div class="xz-product-faq">
                        <?php foreach ($faq as $index => $item) : ?><details<?php echo $index === 0 ? ' open' : ''; ?>><summary><?php echo esc_html($item['faq_question'] ?? ''); ?></summary><div><?php echo wp_kses_post(wpautop($item['faq_answer'] ?? '')); ?></div></details><?php endforeach; ?>
                        </div>
                    <?php elseif ($key === 'workflow') : ?>
                        <div class="xz-product-workflow">
                        <?php echo wp_kses_post($workflow); ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php $index++; endforeach; ?>
        </div>
        <?php
    }
}

final class Article_Toc_Widget extends Xinzhou_Widget {
    public function get_name(): string {
        return 'xinzhou-article-toc';
    }

    public function get_title(): string {
        return 'Xinzhou Article Contents';
    }

    public function get_icon(): string {
        return 'eicon-table-of-contents';
    }

    protected function register_controls(): void {
        $this->start_controls_section('content', ['label' => 'Contents']);
        $this->add_control('title', [
            'label' => 'Title',
            'type' => Controls_Manager::TEXT,
            'default' => 'Contents',
        ]);
        $this->end_controls_section();
    }

    protected function render(): void {
        $post_id = current_content_id('post');
        $content = $post_id ? get_post_field('post_content', $post_id) : '';
        preg_match_all('/<h2\b[^>]*>(.*?)<\/h2>/is', $content, $matches);
        if (empty($matches[1])) {
            return;
        }
        $settings = $this->get_settings_for_display();
        ?>
        <nav class="xz-article-outline" aria-label="Article contents">
            <h2><?php echo esc_html($settings['title'] ?? 'Contents'); ?></h2>
            <ol>
                <?php foreach ($matches[1] as $index => $heading) : ?>
                    <li><a href="#<?php echo esc_attr(\xz_article_heading_slug($heading, $index)); ?>"><?php echo esc_html(wp_strip_all_tags($heading)); ?></a></li>
                <?php endforeach; ?>
            </ol>
        </nav>
        <?php
    }
}

function register_widgets($widgets_manager): void {
    $widgets_manager->register(new Product_Categories_Widget());
    $widgets_manager->register(new Product_Category_Content_Widget());
    $widgets_manager->register(new News_Categories_Widget());
    $widgets_manager->register(new Breadcrumbs_Widget());
    $widgets_manager->register(new Product_Gallery_Widget());
    $widgets_manager->register(new Article_Meta_Widget());
    $widgets_manager->register(new Product_Summary_Data_Widget());
    $widgets_manager->register(new Product_Information_Widget());
    $widgets_manager->register(new Article_Toc_Widget());
}

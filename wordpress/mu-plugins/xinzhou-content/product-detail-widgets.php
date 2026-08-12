<?php

namespace Xinzhou\Elementor;

use Elementor\Controls_Manager;

if (!defined('ABSPATH')) { exit; }

abstract class Product_Detail_Widget_Base extends Xinzhou_Section_Widget {
    public function get_style_depends(): array {
        return ['xinzhou-content', 'xinzhou-product-detail-widgets'];
    }

    public function get_script_depends(): array {
        return ['xinzhou-content'];
    }

    protected function product_id(): int {
        return current_content_id('product');
    }
}

final class Product_Detail_Hero_Widget extends Product_Detail_Widget_Base {
    public function get_name(): string { return 'xinzhou-product-detail-hero'; }
    public function get_title(): string { return 'Xinzhou Product Detail Hero'; }
    public function get_icon(): string { return 'eicon-product-images'; }

    protected function register_controls(): void {
        $this->start_controls_section('content', ['label' => 'Content']);
        $this->add_control('parameter_limit', ['label' => 'Key Parameter Limit', 'type' => Controls_Manager::NUMBER, 'default' => 3, 'min' => 1, 'max' => 8]);
        $this->add_control('finished_limit', ['label' => 'Finished Product Limit', 'type' => Controls_Manager::NUMBER, 'default' => 2, 'min' => 1, 'max' => 6]);
        $this->add_control('finished_title', ['label' => 'Finished Products Label', 'type' => Controls_Manager::TEXT, 'default' => 'Finished Products']);
        $this->add_control('view_all_text', ['label' => 'View All Text', 'type' => Controls_Manager::TEXT, 'default' => 'View All']);
        $this->add_control('button_text', ['label' => 'Inquiry Button Text', 'type' => Controls_Manager::TEXT, 'default' => 'Send an Inquiry']);
        $this->end_controls_section();
    }

    protected function render(): void {
        $post_id = $this->product_id();
        if (!$post_id) { return; }
        $s = $this->get_settings_for_display();
        $gallery = \xz_product_gallery_ids($post_id);
        $parameters = function_exists('get_field') ? (array) get_field('product_key_parameters', $post_id) : [];
        $finished = function_exists('get_field') ? (array) get_field('product_finished_products', $post_id) : [];
        $terms = wp_get_post_terms($post_id, 'product_category');
        $term = !is_wp_error($terms) && $terms ? $terms[0] : null;
        $category_title = $term ? $term->name : '';
        if ($term && function_exists('get_field')) {
            $archive_title = (string) get_field('category_archive_title', 'product_category_' . $term->term_id);
            if ($archive_title) { $category_title = $archive_title; }
        }
        $description = get_the_excerpt($post_id);
        if (!$description && function_exists('get_field')) { $description = (string) get_field('product_short_description', $post_id); }
        ?>
        <section class="product-detail-hero" aria-labelledby="product-title"><div class="xz-container product-detail-hero__grid">
            <div class="product-gallery" data-xz-product-gallery>
                <?php if ($gallery) : ?><figure class="product-gallery__main"><?php echo wp_get_attachment_image($gallery[0], 'full', false, ['data-xz-main-image' => '']); ?></figure><?php endif; ?>
                <?php if (count($gallery) > 1) : ?><div class="product-gallery__thumbs" aria-label="Product gallery thumbnails">
                    <?php foreach ($gallery as $index => $image_id) : $image_alt = (string) get_post_meta($image_id, '_wp_attachment_image_alt', true); ?><button class="<?php echo $index === 0 ? 'is-active' : ''; ?>" type="button" aria-label="<?php echo esc_attr(sprintf('Show product image %d', $index + 1)); ?>" aria-pressed="<?php echo $index === 0 ? 'true' : 'false'; ?>" data-xz-gallery-thumb data-full-src="<?php echo esc_url(wp_get_attachment_image_url($image_id, 'full')); ?>" data-full-alt="<?php echo esc_attr($image_alt); ?>"><?php echo wp_get_attachment_image($image_id, 'thumbnail', false, ['alt' => '']); ?></button><?php endforeach; ?>
                </div><?php endif; ?>
            </div>
            <div class="product-summary">
                <nav class="product-breadcrumb" aria-label="Breadcrumb"><a href="<?php echo esc_url(home_url('/')); ?>">Home</a><span>/</span><a href="<?php echo esc_url(get_post_type_archive_link('product')); ?>">Products</a><?php if ($term) : ?><span>/</span><a href="<?php echo esc_url(get_term_link($term)); ?>"><?php echo esc_html($category_title); ?></a><?php endif; ?></nav>
                <?php if ($category_title) : ?><p class="product-summary__category"><?php echo esc_html($category_title); ?></p><?php endif; ?>
                <h1 id="product-title"><?php echo esc_html(get_the_title($post_id)); ?></h1>
                <?php if ($description) : ?><p class="product-summary__description"><?php echo esc_html(wp_strip_all_tags((string) $description)); ?></p><?php endif; ?>
                <?php if ($parameters) : ?><dl class="product-summary__facts"><?php foreach (array_slice($parameters, 0, (int) ($s['parameter_limit'] ?? 3)) as $parameter) : ?><div><dt><?php echo esc_html($parameter['product_parameter_label'] ?? ''); ?></dt><dd><?php echo esc_html($parameter['product_parameter_value'] ?? ''); ?></dd></div><?php endforeach; ?></dl><?php endif; ?>
                <?php if ($finished) : ?><div class="product-summary__block product-summary__applications"><h2><?php echo esc_html((string) ($s['finished_title'] ?? 'Finished Products')); ?></h2><div><?php foreach (array_slice($finished, 0, (int) ($s['finished_limit'] ?? 2)) as $item) : ?><span><?php echo esc_html($item['finished_product_title'] ?? ''); ?></span><?php endforeach; ?><?php if (count($finished) > (int) ($s['finished_limit'] ?? 2)) : ?><a href="#tab-finished-products" data-xz-open-tab="finished-products"><?php echo esc_html((string) ($s['view_all_text'] ?? 'View All')); ?></a><?php endif; ?></div></div><?php endif; ?>
                <div class="product-summary__actions"><a class="xz-button xz-button--primary" href="#inquiry" data-inquiry-open><?php echo esc_html((string) ($s['button_text'] ?? 'Send an Inquiry')); ?><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg></a></div>
            </div>
        </div></section>
        <?php
    }
}

final class Product_Detail_Information_Widget extends Product_Detail_Widget_Base {
    public function get_name(): string { return 'xinzhou-product-detail-information'; }
    public function get_title(): string { return 'Xinzhou Product Information Tabs'; }
    public function get_icon(): string { return 'eicon-tabs'; }

    protected function register_controls(): void {
        $this->start_controls_section('head', ['label' => 'Heading']);
        $this->add_control('eyebrow', ['label' => 'Section Label', 'type' => Controls_Manager::TEXT, 'default' => 'Product Information']);
        $this->add_control('title', ['label' => 'Title', 'type' => Controls_Manager::TEXT, 'default' => 'Engineering Details']);
        $this->end_controls_section();
        $this->start_controls_section('tabs', ['label' => 'Tab Labels']);
        foreach (['overview_label' => 'Overview', 'specifications_label' => 'Technical Specifications', 'finished_label' => 'Finished Products', 'workflow_label' => 'Configuration & Workflow', 'faq_label' => 'FAQ'] as $key => $label) {
            $this->add_control($key, ['label' => $label, 'type' => Controls_Manager::TEXT, 'default' => $label]);
        }
        $this->end_controls_section();
    }

    protected function render(): void {
        $post_id = $this->product_id();
        if (!$post_id) { return; }
        $s = $this->get_settings_for_display();
        $overview = function_exists('get_field') ? (string) get_field('product_overview', $post_id) : '';
        if (!$overview) { $overview = (string) get_post_field('post_content', $post_id); }
        $specifications = function_exists('get_field') ? (array) get_field('product_specifications', $post_id) : [];
        $finished = function_exists('get_field') ? (array) get_field('product_finished_products', $post_id) : [];
        $workflow = function_exists('get_field') ? (array) get_field('product_configuration_workflow', $post_id) : [];
        $faq = function_exists('get_field') ? (array) get_field('product_faq', $post_id) : [];
        $tabs = array_filter([
            'overview' => $overview ? ($s['overview_label'] ?? 'Overview') : '',
            'specifications' => $specifications ? ($s['specifications_label'] ?? 'Technical Specifications') : '',
            'finished-products' => $finished ? ($s['finished_label'] ?? 'Finished Products') : '',
            'workflow' => $workflow ? ($s['workflow_label'] ?? 'Configuration & Workflow') : '',
            'faq' => $faq ? ($s['faq_label'] ?? 'FAQ') : '',
        ]);
        if (!$tabs) { return; }
        ?>
        <section class="product-information" id="product-information" aria-labelledby="product-information-title"><div class="xz-container">
            <div class="product-information__head"><?php if (!empty($s['eyebrow'])) : ?><p class="product-detail-eyebrow"><?php echo esc_html((string) $s['eyebrow']); ?></p><?php endif; ?><h2 id="product-information-title"><?php echo esc_html((string) ($s['title'] ?? 'Engineering Details')); ?></h2></div>
            <div class="product-tabs" data-xz-product-tabs><div class="product-tabs__list" role="tablist" aria-label="Product information tabs">
                <?php $index = 0; foreach ($tabs as $key => $label) : ?><button class="<?php echo $index === 0 ? 'is-active' : ''; ?>" type="button" role="tab" aria-selected="<?php echo $index === 0 ? 'true' : 'false'; ?>" aria-controls="tab-<?php echo esc_attr($key); ?>" id="tab-button-<?php echo esc_attr($key); ?>" data-xz-tab="<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></button><?php $index++; endforeach; ?>
            </div>
            <?php $index = 0; foreach ($tabs as $key => $label) : ?><div class="product-tabs__panel<?php echo $index === 0 ? ' is-active' : ''; ?>" id="tab-<?php echo esc_attr($key); ?>" role="tabpanel" aria-labelledby="tab-button-<?php echo esc_attr($key); ?>" data-xz-tab-panel="<?php echo esc_attr($key); ?>"<?php echo $index === 0 ? '' : ' hidden'; ?>>
                <?php if ($key === 'overview') : ?><?php echo apply_filters('the_content', $overview); ?>
                <?php elseif ($key === 'specifications') : ?><div class="product-specifications"><div class="product-specifications__table-wrap"><table><tbody><?php foreach ($specifications as $item) : ?><tr><th scope="row"><?php echo esc_html($item['specification_parameter'] ?? ''); ?></th><td><?php echo wp_kses_post($item['specification_value'] ?? ''); ?></td></tr><?php endforeach; ?></tbody></table></div></div>
                <?php elseif ($key === 'finished-products') : ?><div class="finished-products-grid"><?php foreach ($finished as $item) : $image_id = \xz_acf_image_id($item['finished_product_image'] ?? 0); ?><figure><?php echo $image_id ? wp_get_attachment_image($image_id, 'large') : ''; ?><figcaption><?php echo esc_html($item['finished_product_title'] ?? ''); ?></figcaption></figure><?php endforeach; ?></div>
                <?php elseif ($key === 'workflow') : ?><div class="product-workflow"><?php foreach ($workflow as $step_index => $item) : ?><article><span><?php echo esc_html(str_pad((string) ($step_index + 1), 2, '0', STR_PAD_LEFT)); ?></span><div><h3><?php echo esc_html($item['workflow_step_title'] ?? ''); ?></h3><?php echo wp_kses_post($item['workflow_step_description'] ?? ''); ?></div></article><?php endforeach; ?></div>
                <?php elseif ($key === 'faq') : ?><div class="product-faq"><?php foreach ($faq as $faq_index => $item) : ?><details<?php echo $faq_index === 0 ? ' open' : ''; ?>><summary><?php echo esc_html($item['faq_question'] ?? ''); ?></summary><?php echo wp_kses_post($item['faq_answer'] ?? ''); ?></details><?php endforeach; ?></div><?php endif; ?>
            </div><?php $index++; endforeach; ?></div>
        </div></section>
        <?php
    }
}

final class Product_Detail_Related_Widget extends Product_Detail_Widget_Base {
    public function get_name(): string { return 'xinzhou-product-detail-related'; }
    public function get_title(): string { return 'Xinzhou Related Products'; }
    public function get_icon(): string { return 'eicon-products'; }

    protected function register_controls(): void {
        $this->start_controls_section('content', ['label' => 'Content']);
        $this->add_control('eyebrow', ['label' => 'Section Label', 'type' => Controls_Manager::TEXT, 'default' => 'Related Products']);
        $this->add_control('title', ['label' => 'Title', 'type' => Controls_Manager::TEXT, 'default' => 'Complete Your Production Line']);
        $this->add_control('count', ['label' => 'Product Count', 'type' => Controls_Manager::NUMBER, 'default' => 3, 'min' => 1, 'max' => 6]);
        $this->end_controls_section();
    }

    protected function related_ids(int $post_id, int $count): array {
        $ids = function_exists('get_field') ? array_map('intval', (array) get_field('related_products', $post_id, false)) : [];
        $ids = array_values(array_filter($ids, static fn(int $id): bool => $id && $id !== $post_id));
        if ($ids) { return array_slice($ids, 0, $count); }
        $terms = wp_get_post_terms($post_id, 'product_category', ['fields' => 'ids']);
        return get_posts(['post_type' => 'product', 'post_status' => 'publish', 'posts_per_page' => $count, 'post__not_in' => [$post_id], 'fields' => 'ids', 'orderby' => 'ID', 'order' => 'ASC', 'tax_query' => !is_wp_error($terms) && $terms ? [['taxonomy' => 'product_category', 'field' => 'term_id', 'terms' => $terms]] : []]);
    }

    protected function render(): void {
        $post_id = $this->product_id();
        if (!$post_id) { return; }
        $s = $this->get_settings_for_display();
        $ids = $this->related_ids($post_id, (int) ($s['count'] ?? 3));
        if (!$ids) { return; }
        ?>
        <section class="related-products" aria-labelledby="related-products-title"><div class="xz-container"><div class="related-products__head"><?php if (!empty($s['eyebrow'])) : ?><p class="product-detail-eyebrow"><?php echo esc_html((string) $s['eyebrow']); ?></p><?php endif; ?><h2 id="related-products-title"><?php echo esc_html((string) ($s['title'] ?? 'Complete Your Production Line')); ?></h2></div><div class="related-products__grid">
            <?php foreach ($ids as $related_id) : $label = function_exists('get_field') ? (string) get_field('product_card_label', $related_id) : ''; if (!$label) { $related_terms = wp_get_post_terms($related_id, 'product_category'); $label = !is_wp_error($related_terms) && $related_terms ? $related_terms[0]->name : ''; } ?><article><a href="<?php echo esc_url(get_permalink($related_id)); ?>"><figure><?php echo get_the_post_thumbnail($related_id, 'large', ['loading' => 'lazy']); ?><?php if ($label) : ?><span><?php echo esc_html($label); ?></span><?php endif; ?></figure><h3><?php echo esc_html(get_the_title($related_id)); ?></h3></a></article><?php endforeach; ?>
        </div></div></section>
        <?php
    }
}

function register_product_detail_widgets($widgets_manager): void {
    $widgets_manager->register(new Product_Detail_Hero_Widget());
    $widgets_manager->register(new Product_Detail_Information_Widget());
    $widgets_manager->register(new Product_Detail_Related_Widget());
}

<?php

namespace Xinzhou\Elementor;

use Elementor\Controls_Manager;
use Elementor\Repeater;

if (!defined('ABSPATH')) { exit; }

abstract class Product_Archive_Widget_Base extends Xinzhou_Section_Widget {
    public function get_style_depends(): array {
        return ['xinzhou-content', 'xinzhou-product-archive-widgets'];
    }
}

final class Product_Archive_Grid_Widget extends Product_Archive_Widget_Base {
    public function get_name(): string { return 'xinzhou-product-archive-grid'; }
    public function get_title(): string { return 'Xinzhou Product Archive Grid'; }
    public function get_icon(): string { return 'eicon-posts-grid'; }

    protected function register_controls(): void {
        $this->start_controls_section('content', ['label' => 'Content']);
        $this->add_control('title', ['label' => 'Title', 'type' => Controls_Manager::TEXT, 'default' => 'Machines in This Category']);
        $this->add_control('show_label', ['label' => 'Show Card Label', 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes']);
        $this->end_controls_section();
    }

    private function current_posts(): array {
        global $wp_query;
        if ($wp_query instanceof \WP_Query && !empty($wp_query->posts)) {
            return array_values(array_filter($wp_query->posts, static fn($post): bool => get_post_type($post) === 'product'));
        }
        return get_posts(['post_type' => 'product', 'post_status' => 'publish', 'posts_per_page' => 9, 'orderby' => 'ID', 'order' => 'ASC']);
    }

    protected function render(): void {
        global $wp_query;
        $s = $this->get_settings_for_display();
        $posts = $this->current_posts();
        ?>
        <section class="product-archive" aria-labelledby="product-archive-title"><div class="xz-container"><div class="product-archive__head"><h2 id="product-archive-title"><?php echo esc_html((string) ($s['title'] ?? 'Machines in This Category')); ?></h2></div><div class="product-archive__grid">
            <?php foreach ($posts as $post) :
                $terms = wp_get_post_terms($post->ID, 'product_category');
                $label = function_exists('get_field') ? (string) get_field('product_card_label', $post->ID) : '';
                if (!$label && !is_wp_error($terms) && $terms) { $label = $terms[0]->name; }
                ?>
                <article class="product-archive-card"><a href="<?php echo esc_url(get_permalink($post)); ?>"><figure><?php echo get_the_post_thumbnail($post, 'large', ['loading' => 'lazy']); ?><?php if (($s['show_label'] ?? '') === 'yes' && $label) : ?><span><?php echo esc_html($label); ?></span><?php endif; ?></figure><h3><?php echo esc_html(get_the_title($post)); ?></h3></a></article>
            <?php endforeach; ?>
        </div>
        <?php if ($wp_query instanceof \WP_Query && $wp_query->max_num_pages > 1) : ?><nav class="product-pagination" aria-label="Product pages"><?php echo wp_kses_post(paginate_links(['total' => $wp_query->max_num_pages, 'current' => max(1, get_query_var('paged')), 'type' => 'plain', 'prev_text' => '&lsaquo;', 'next_text' => '&rsaquo;'])); ?></nav><?php endif; ?>
        </div></section>
        <?php
    }
}

final class Product_Worldwide_Widget extends Product_Archive_Widget_Base {
    public function get_name(): string { return 'xinzhou-product-worldwide'; }
    public function get_title(): string { return 'Xinzhou Product Worldwide'; }
    public function get_icon(): string { return 'eicon-logo'; }

    protected function register_controls(): void {
        $this->start_controls_section('content', ['label' => 'Content']);
        $this->add_control('title', ['label' => 'Title', 'type' => Controls_Manager::TEXT, 'default' => 'Xinzhou Worldwide']);
        $r = new Repeater(); $r->add_control('image', ['label' => 'Logo', 'type' => Controls_Manager::MEDIA]); $r->add_control('link', ['label' => 'Link', 'type' => Controls_Manager::URL]);
        $this->add_control('logos', ['label' => 'Logos', 'type' => Controls_Manager::REPEATER, 'fields' => $r->get_controls()]);
        $this->end_controls_section();
    }

    protected function render(): void {
        $s = $this->get_settings_for_display();
        ?><section class="products-worldwide" aria-labelledby="products-worldwide-title"><div class="products-worldwide__container"><h2 id="products-worldwide-title"><?php echo esc_html((string) ($s['title'] ?? 'Xinzhou Worldwide')); ?></h2><div class="products-worldwide__grid"><?php foreach ((array) ($s['logos'] ?? []) as $logo) : $image_id = (int) ($logo['image']['id'] ?? 0); $image = $this->image_url((array) ($logo['image'] ?? [])); ?><a href="<?php echo esc_url($this->link_url((array) ($logo['link'] ?? []))); ?>"><?php if ($image_id) : echo wp_get_attachment_image($image_id, 'full', false, ['loading' => 'lazy']); elseif ($image) : ?><img src="<?php echo esc_url($image); ?>" alt="" loading="lazy"><?php endif; ?></a><?php endforeach; ?></div></div></section><?php
    }
}

function register_product_archive_widgets($widgets_manager): void {
    $widgets_manager->register(new Product_Archive_Grid_Widget());
    $widgets_manager->register(new Product_Worldwide_Widget());
}

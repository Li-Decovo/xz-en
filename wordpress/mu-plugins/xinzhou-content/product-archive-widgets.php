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

    protected function render(): void {
        $s = $this->get_settings_for_display();
        $object = get_queried_object();
        $term_id = $object instanceof \WP_Term && $object->taxonomy === 'product_category' ? (int) $object->term_id : 0;
        $page = max(1, get_query_var('paged'));
        $desktop_page_size = 12;
        $mobile_page_size = 4;
        $query = new \WP_Query(\xz_product_archive_query_args($page, $desktop_page_size, $term_id));
        $show_label = ($s['show_label'] ?? '') === 'yes';
        ?>
        <section class="product-archive" aria-labelledby="product-archive-title" data-xz-product-archive data-term-id="<?php echo esc_attr((string) $term_id); ?>" data-show-label="<?php echo $show_label ? '1' : '0'; ?>"><div class="xz-container"><div class="product-archive__head"><h2 id="product-archive-title"><?php echo esc_html((string) ($s['title'] ?? 'Machines in This Category')); ?></h2></div><div class="product-archive__grid" data-products-grid data-xz-ajax-grid data-page-size="<?php echo esc_attr((string) $desktop_page_size); ?>" data-page-size-mobile="<?php echo esc_attr((string) $mobile_page_size); ?>" aria-live="polite">
            <?php foreach ($query->posts as $post) { echo \xz_render_product_archive_card($post, $show_label); } ?>
        </div>
        <nav class="product-pagination" aria-label="Product pages" data-product-pagination><?php if ($query->max_num_pages > 1) { echo wp_kses_post(paginate_links(['total' => $query->max_num_pages, 'current' => $page, 'prev_text' => '&lsaquo;', 'next_text' => '&rsaquo;'])); } ?></nav>
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
        $logos = (array) ($s['logos'] ?? []);
        $carousel = count($logos) > 2;
        ?><section class="products-worldwide xz-home-worldwide" aria-labelledby="products-worldwide-title"><div class="products-worldwide__container xz-home-worldwide__inner"><h2 id="products-worldwide-title"><?php echo esc_html((string) ($s['title'] ?? 'Xinzhou Worldwide')); ?></h2><div class="xz-simple-carousel<?php echo $carousel ? ' is-carousel' : ''; ?>"<?php echo $carousel ? ' data-xz-simple-carousel data-visible="4" data-visible-tablet="3" data-visible-mobile="2"' : ''; ?>><?php if ($carousel) : ?><div class="xz-simple-carousel__controls"><button type="button" data-xz-simple-prev aria-label="Previous logos">&#8249;</button><button type="button" data-xz-simple-next aria-label="Next logos">&#8250;</button></div><?php endif; ?><div class="products-worldwide__grid xz-home-worldwide__grid"<?php echo $carousel ? ' data-xz-simple-track' : ''; ?>><?php foreach ($logos as $logo) : $image = $this->image_url((array) ($logo['image'] ?? [])); ?><a href="<?php echo esc_url($this->link_url((array) ($logo['link'] ?? []))); ?>"><?php if ($image) : echo \xz_media_image($image, 'full', ['loading' => 'lazy']); endif; ?></a><?php endforeach; ?></div></div></div></section><?php
    }
}

function register_product_archive_widgets($widgets_manager): void {
    $widgets_manager->register(new Product_Archive_Grid_Widget());
    $widgets_manager->register(new Product_Worldwide_Widget());
}

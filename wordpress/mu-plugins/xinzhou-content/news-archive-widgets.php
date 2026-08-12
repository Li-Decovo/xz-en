<?php

namespace Xinzhou\Elementor;

use Elementor\Controls_Manager;

if (!defined('ABSPATH')) { exit; }

function news_widget_post_options(): array {
    $options = [];
    foreach (get_posts(['post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => 100, 'orderby' => 'date', 'order' => 'DESC']) as $post) {
        $options[$post->ID] = $post->post_title;
    }
    return $options;
}

function news_widget_case_term_id(): int {
    $term = get_term_by('slug', 'cases', 'category');
    return $term instanceof \WP_Term ? (int) $term->term_id : 0;
}

function news_widget_current_category_id(): int {
    $object = get_queried_object();
    return $object instanceof \WP_Term && $object->taxonomy === 'category' ? (int) $object->term_id : 0;
}

function news_widget_meta(int $post_id): void {
    $categories = get_the_category($post_id);
    $location = function_exists('get_field') ? (string) get_field('article_location', $post_id) : '';
    echo '<div class="news-meta"><time datetime="' . esc_attr(get_the_date('c', $post_id)) . '">' . esc_html(get_the_date('F j, Y', $post_id)) . '</time>';
    if ($categories) { echo '<span>' . esc_html($categories[0]->name) . '</span>'; }
    if ($location) { echo '<span>' . esc_html($location) . '</span>'; }
    echo '</div>';
}

abstract class News_Archive_Widget_Base extends Xinzhou_Section_Widget {
    public function get_style_depends(): array {
        return ['xinzhou-content', 'xinzhou-news-archive-widgets'];
    }
}

final class News_Archive_Hero_Widget extends News_Archive_Widget_Base {
    public function get_name(): string { return 'xinzhou-news-archive-hero'; }
    public function get_title(): string { return 'Xinzhou News Hero'; }
    public function get_icon(): string { return 'eicon-featured-image'; }

    protected function register_controls(): void {
        $this->start_controls_section('content', ['label' => 'Content']);
        $this->add_control('background', ['label' => 'Background Image', 'type' => Controls_Manager::MEDIA]);
        $this->add_control('title', ['label' => 'Title', 'type' => Controls_Manager::TEXT, 'default' => 'Updates from Xinzhou']);
        $this->add_control('description', ['label' => 'Description', 'type' => Controls_Manager::TEXTAREA, 'default' => "Follow international exhibitions, customer exchanges and the latest developments in Xinzhou's automated welding equipment business."]);
        $this->end_controls_section();
    }

    protected function render(): void {
        $s = $this->get_settings_for_display();
        $image = $this->image_url((array) ($s['background'] ?? []));
        $current = get_queried_object();
        $title = $current instanceof \WP_Term && $current->taxonomy === 'category' ? $current->name : (string) ($s['title'] ?? 'Updates from Xinzhou');
        $description = $current instanceof \WP_Term && $current->taxonomy === 'category' && $current->description ? $current->description : (string) ($s['description'] ?? '');
        ?>
        <section class="news-hero" aria-labelledby="news-page-title"><?php if ($image) : ?><img class="news-hero__image" src="<?php echo esc_url($image); ?>" alt="Xinzhou company and manufacturing environment"><?php endif; ?><div class="news-hero__overlay"></div><div class="xz-container news-hero__content"><nav class="news-breadcrumb" aria-label="Breadcrumb"><a href="<?php echo esc_url(home_url('/')); ?>">Home</a><span>/</span><span><?php echo esc_html($title); ?></span></nav><h1 id="news-page-title"><?php echo esc_html($title); ?></h1><?php if ($description) : ?><p><?php echo esc_html(wp_strip_all_tags((string) $description)); ?></p><?php endif; ?></div></section>
        <?php
    }
}

final class News_Archive_Featured_Widget extends News_Archive_Widget_Base {
    public function get_name(): string { return 'xinzhou-news-archive-featured'; }
    public function get_title(): string { return 'Xinzhou Featured News'; }
    public function get_icon(): string { return 'eicon-post-content'; }

    protected function register_controls(): void {
        $this->start_controls_section('content', ['label' => 'Content']);
        $this->add_control('eyebrow', ['label' => 'Section Label', 'type' => Controls_Manager::TEXT, 'default' => 'Latest Update']);
        $this->add_control('title', ['label' => 'Title', 'type' => Controls_Manager::TEXT, 'default' => 'Exhibitions and Industry Connections']);
        $this->add_control('description', ['label' => 'Description', 'type' => Controls_Manager::TEXTAREA, 'default' => 'See how Xinzhou presents production line capabilities and discusses real manufacturing requirements with customers around the world.']);
        $this->add_control('featured_post', ['label' => 'Featured Article', 'type' => Controls_Manager::SELECT2, 'options' => news_widget_post_options(), 'default' => 187]);
        $this->add_control('link_text', ['label' => 'Link Text', 'type' => Controls_Manager::TEXT, 'default' => 'Read Full Story']);
        $this->end_controls_section();
    }

    private function featured_id(array $settings): int {
        $category_id = news_widget_current_category_id();
        if ($category_id) {
            $ids = get_posts(['post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => 1, 'fields' => 'ids', 'orderby' => 'ID', 'order' => 'ASC', 'cat' => $category_id]);
            return $ids ? (int) $ids[0] : 0;
        }
        return (int) ($settings['featured_post'] ?? 187);
    }

    protected function render(): void {
        $s = $this->get_settings_for_display();
        $post_id = $this->featured_id($s);
        if (!$post_id || get_post_status($post_id) !== 'publish') { return; }
        ?>
        <section class="news-latest" aria-labelledby="latest-news-title"><div class="xz-container"><div class="news-section-head"><div><?php if (!empty($s['eyebrow'])) : ?><p class="news-eyebrow"><?php echo esc_html((string) $s['eyebrow']); ?></p><?php endif; ?><h2 id="latest-news-title"><?php echo esc_html((string) ($s['title'] ?? 'Exhibitions and Industry Connections')); ?></h2></div><?php if (!empty($s['description'])) : ?><p><?php echo esc_html((string) $s['description']); ?></p><?php endif; ?></div>
            <article class="news-featured"><a class="news-featured__media" href="<?php echo esc_url(get_permalink($post_id)); ?>"><?php echo get_the_post_thumbnail($post_id, 'large', ['loading' => 'eager']); ?></a><div class="news-featured__copy"><?php news_widget_meta($post_id); ?><h2><a href="<?php echo esc_url(get_permalink($post_id)); ?>"><?php echo esc_html(get_the_title($post_id)); ?></a></h2><p><?php echo esc_html(get_the_excerpt($post_id)); ?></p><a class="news-read-link" href="<?php echo esc_url(get_permalink($post_id)); ?>"><?php echo esc_html((string) ($s['link_text'] ?? 'Read Full Story')); ?><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg></a></div></article>
        </div></section>
        <?php
    }
}

final class News_Archive_Grid_Widget extends News_Archive_Widget_Base {
    public function get_name(): string { return 'xinzhou-news-archive-grid'; }
    public function get_title(): string { return 'Xinzhou News Archive Grid'; }
    public function get_icon(): string { return 'eicon-posts-grid'; }

    protected function register_controls(): void {
        $this->start_controls_section('content', ['label' => 'Content']);
        $this->add_control('eyebrow', ['label' => 'Section Label', 'type' => Controls_Manager::TEXT, 'default' => 'News Archive']);
        $this->add_control('title', ['label' => 'Title', 'type' => Controls_Manager::TEXT, 'default' => 'More Xinzhou Updates']);
        $this->add_control('posts_per_page', ['label' => 'Posts Per Page', 'type' => Controls_Manager::NUMBER, 'default' => 9, 'min' => 3, 'max' => 24]);
        $this->add_control('link_text', ['label' => 'Link Text', 'type' => Controls_Manager::TEXT, 'default' => 'Read More']);
        $this->add_control('featured_post', ['label' => 'Article Excluded as Featured', 'type' => Controls_Manager::SELECT2, 'options' => news_widget_post_options(), 'default' => 187]);
        $this->end_controls_section();
    }

    protected function render(): void {
        $s = $this->get_settings_for_display();
        $category_id = news_widget_current_category_id();
        $featured_id = $category_id ? (int) (get_posts(['post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => 1, 'fields' => 'ids', 'orderby' => 'ID', 'order' => 'ASC', 'cat' => $category_id])[0] ?? 0) : (int) ($s['featured_post'] ?? 187);
        $args = ['post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => (int) ($s['posts_per_page'] ?? 9), 'paged' => max(1, get_query_var('paged')), 'orderby' => 'ID', 'order' => 'ASC', 'post__not_in' => array_filter([$featured_id])];
        if ($category_id) { $args['cat'] = $category_id; }
        else { $case_id = news_widget_case_term_id(); if ($case_id) { $args['category__not_in'] = [$case_id]; } }
        $query = new \WP_Query($args);
        if (!$query->have_posts()) { return; }
        ?>
        <section class="news-archive" aria-labelledby="news-archive-title"><div class="xz-container"><div class="news-archive__head"><?php if (!empty($s['eyebrow'])) : ?><p class="news-eyebrow"><?php echo esc_html((string) $s['eyebrow']); ?></p><?php endif; ?><h2 id="news-archive-title"><?php echo esc_html((string) ($s['title'] ?? 'More Xinzhou Updates')); ?></h2></div><div class="news-archive__grid">
            <?php while ($query->have_posts()) : $query->the_post(); $post_id = get_the_ID(); $categories = get_the_category($post_id); ?><article class="news-card"><a class="news-card__media" href="<?php the_permalink(); ?>"><?php echo get_the_post_thumbnail($post_id, 'large', ['loading' => 'lazy']); ?><?php if ($categories) : ?><span><?php echo esc_html($categories[0]->name); ?></span><?php endif; ?></a><div class="news-card__body"><time datetime="<?php echo esc_attr(get_the_date('c', $post_id)); ?>"><?php echo esc_html(get_the_date('F j, Y', $post_id)); ?></time><h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3><p><?php echo esc_html(get_the_excerpt($post_id)); ?></p><a class="news-read-link" href="<?php the_permalink(); ?>"><?php echo esc_html((string) ($s['link_text'] ?? 'Read More')); ?><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg></a></div></article><?php endwhile; wp_reset_postdata(); ?>
        </div><?php if ($query->max_num_pages > 1) : ?><nav class="news-pagination" aria-label="News pages"><?php echo wp_kses_post(paginate_links(['total' => $query->max_num_pages, 'current' => max(1, get_query_var('paged')), 'prev_text' => '&lsaquo;', 'next_text' => '&rsaquo;'])); ?></nav><?php endif; ?></div></section>
        <?php
    }
}

function register_news_archive_widgets($widgets_manager): void {
    $widgets_manager->register(new News_Archive_Hero_Widget());
    $widgets_manager->register(new News_Archive_Featured_Widget());
    $widgets_manager->register(new News_Archive_Grid_Widget());
}

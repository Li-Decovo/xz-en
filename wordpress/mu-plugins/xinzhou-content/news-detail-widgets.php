<?php

namespace Xinzhou\Elementor;

use Elementor\Controls_Manager;

if (!defined('ABSPATH')) { exit; }

function news_detail_content_with_ids(string $content): array {
    $headings = [];
    $index = 0;
    $content = (string) preg_replace_callback('/<h2([^>]*)>(.*?)<\/h2>/is', static function (array $match) use (&$headings, &$index): string {
        $plain = wp_strip_all_tags($match[2]);
        if (preg_match('/\sid=["\']([^"\']+)["\']/', $match[1], $id_match)) {
            $id = $id_match[1];
        } else {
            $id = \xz_article_heading_slug($plain, $index);
            $match[1] .= ' id="' . esc_attr($id) . '"';
        }
        $headings[] = ['id' => $id, 'text' => $plain];
        $index++;
        return '<h2' . $match[1] . '>' . $match[2] . '</h2>';
    }, $content);
    return [$content, $headings];
}

function news_detail_category(int $post_id): ?\WP_Term {
    $categories = get_the_category($post_id);
    return $categories ? $categories[0] : null;
}

abstract class News_Detail_Widget_Base extends Xinzhou_Section_Widget {
    public function get_style_depends(): array {
        return ['xinzhou-content', 'xinzhou-news-detail-widgets'];
    }

    protected function post_id(): int {
        return current_content_id('post');
    }
}

final class News_Detail_Hero_Widget extends News_Detail_Widget_Base {
    public function get_name(): string { return 'xinzhou-news-detail-hero'; }
    public function get_title(): string { return 'Xinzhou News Detail Hero'; }
    public function get_icon(): string { return 'eicon-featured-image'; }

    protected function register_controls(): void {
        $this->start_controls_section('content', ['label' => 'Content']);
        $this->add_control('background', ['label' => 'Background Image', 'type' => Controls_Manager::MEDIA]);
        $this->add_control('news_label', ['label' => 'News Breadcrumb Label', 'type' => Controls_Manager::TEXT, 'default' => 'News']);
        $this->end_controls_section();
    }

    protected function render(): void {
        $post_id = $this->post_id();
        if (!$post_id) { return; }
        $s = $this->get_settings_for_display();
        $image = $this->image_url((array) ($s['background'] ?? []));
        $category = news_detail_category($post_id);
        $news_page = (int) get_option('page_for_posts');
        ?>
        <section class="article-hero" aria-labelledby="article-title"><?php if ($image) : ?><img class="article-hero__image" src="<?php echo esc_url($image); ?>" alt="Xinzhou news and company updates"><?php endif; ?><div class="article-hero__overlay"></div><div class="xz-container article-hero__content"><nav class="article-breadcrumb" aria-label="Breadcrumb"><a href="<?php echo esc_url(home_url('/')); ?>">Home</a><span>/</span><a href="<?php echo esc_url($news_page ? get_permalink($news_page) : home_url('/news/')); ?>"><?php echo esc_html((string) ($s['news_label'] ?? 'News')); ?></a><?php if ($category) : ?><span>/</span><span><?php echo esc_html($category->name); ?></span><?php endif; ?></nav><h1 id="article-title"><?php echo esc_html(get_the_title($post_id)); ?></h1></div></section>
        <?php
    }
}

final class News_Detail_Body_Widget extends News_Detail_Widget_Base {
    public function get_name(): string { return 'xinzhou-news-detail-body'; }
    public function get_title(): string { return 'Xinzhou News Article Body'; }
    public function get_icon(): string { return 'eicon-post-content'; }

    protected function register_controls(): void {
        $this->start_controls_section('article', ['label' => 'Article']);
        $this->add_control('toc_title', ['label' => 'Contents Title', 'type' => Controls_Manager::TEXT, 'default' => 'Article Contents']);
        $this->add_control('back_text', ['label' => 'Back Link Text', 'type' => Controls_Manager::TEXT, 'default' => 'Back to All News']);
        $this->end_controls_section();
        $this->start_controls_section('inquiry', ['label' => 'Inquiry Box']);
        $this->add_control('inquiry_label', ['label' => 'Label', 'type' => Controls_Manager::TEXT, 'default' => 'Equipment Inquiry']);
        $this->add_control('inquiry_title', ['label' => 'Title', 'type' => Controls_Manager::TEXT, 'default' => 'Planning an Automated Welding Project?']);
        $this->add_control('inquiry_copy', ['label' => 'Description', 'type' => Controls_Manager::TEXTAREA, 'default' => 'Share your finished product, production target and factory conditions with the Xinzhou team.']);
        $this->add_control('button_text', ['label' => 'Button Text', 'type' => Controls_Manager::TEXT, 'default' => 'Send an Inquiry']);
        $this->add_control('email', ['label' => 'Email', 'type' => Controls_Manager::TEXT, 'default' => 'xinzhou@weldercn.com']);
        $this->end_controls_section();
    }

    protected function render(): void {
        $post_id = $this->post_id();
        if (!$post_id) { return; }
        $s = $this->get_settings_for_display();
        [$content, $headings] = news_detail_content_with_ids((string) get_post_field('post_content', $post_id));
        $category = news_detail_category($post_id);
        $location = function_exists('get_field') ? (string) get_field('article_location', $post_id) : '';
        $caption = function_exists('get_field') ? (string) get_field('article_caption', $post_id) : '';
        $news_page = (int) get_option('page_for_posts');
        $news_url = $news_page ? get_permalink($news_page) : home_url('/news/');
        $email = sanitize_email((string) ($s['email'] ?? 'xinzhou@weldercn.com'));
        ?>
        <div class="xz-container article-layout"><article class="article-main-column"><figure class="article-cover"><?php echo get_the_post_thumbnail($post_id, 'full', ['loading' => 'eager']); ?><?php if ($caption) : ?><figcaption><?php echo esc_html($caption); ?></figcaption><?php endif; ?></figure><div class="article-meta"><time datetime="<?php echo esc_attr(get_the_date('c', $post_id)); ?>"><?php echo esc_html(get_the_date('F j, Y', $post_id)); ?></time><?php if ($category) : ?><span><?php echo esc_html($category->name); ?></span><?php endif; ?><?php if ($location) : ?><span><?php echo esc_html($location); ?></span><?php endif; ?></div><?php if (get_the_excerpt($post_id)) : ?><p class="article-lead"><?php echo esc_html(get_the_excerpt($post_id)); ?></p><?php endif; ?><div class="article-content"><?php echo apply_filters('the_content', $content); ?></div><div class="article-back"><a href="<?php echo esc_url($news_url); ?>"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M19 12H5M11 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg><?php echo esc_html((string) ($s['back_text'] ?? 'Back to All News')); ?></a></div></article>
            <aside class="article-sidebar"><?php if ($headings) : ?><nav class="article-outline" aria-label="Article outline"><p><?php echo esc_html((string) ($s['toc_title'] ?? 'Article Contents')); ?></p><ol><?php foreach ($headings as $heading) : ?><li><a href="#<?php echo esc_attr($heading['id']); ?>"><?php echo esc_html($heading['text']); ?></a></li><?php endforeach; ?></ol></nav><?php endif; ?><div class="article-inquiry"><p class="article-inquiry__label"><?php echo esc_html((string) ($s['inquiry_label'] ?? 'Equipment Inquiry')); ?></p><h2><?php echo esc_html((string) ($s['inquiry_title'] ?? 'Planning an Automated Welding Project?')); ?></h2><p><?php echo esc_html((string) ($s['inquiry_copy'] ?? '')); ?></p><a class="xz-button xz-button--primary" href="#inquiry" data-inquiry-open><?php echo esc_html((string) ($s['button_text'] ?? 'Send an Inquiry')); ?><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg></a><?php if ($email) : ?><a class="article-inquiry__email" href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a><?php endif; ?></div></aside>
        </div>
        <?php
    }
}

final class News_Detail_Related_Widget extends News_Detail_Widget_Base {
    public function get_name(): string { return 'xinzhou-news-detail-related'; }
    public function get_title(): string { return 'Xinzhou Related News'; }
    public function get_icon(): string { return 'eicon-posts-grid'; }

    protected function register_controls(): void {
        $this->start_controls_section('content', ['label' => 'Content']);
        $this->add_control('eyebrow', ['label' => 'Section Label', 'type' => Controls_Manager::TEXT, 'default' => 'More Updates']);
        $this->add_control('title', ['label' => 'Title', 'type' => Controls_Manager::TEXT, 'default' => 'Related News']);
        $this->add_control('view_all_text', ['label' => 'View All Text', 'type' => Controls_Manager::TEXT, 'default' => 'View All News']);
        $this->add_control('count', ['label' => 'Article Count', 'type' => Controls_Manager::NUMBER, 'default' => 3, 'min' => 1, 'max' => 6]);
        $this->end_controls_section();
    }

    private function related_ids(int $post_id, int $count): array {
        $case_id = news_widget_case_term_id();
        $category_ids = wp_get_post_categories($post_id);
        $args = ['post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => $count, 'fields' => 'ids', 'post__not_in' => [$post_id], 'orderby' => 'ID', 'order' => 'ASC'];
        if ($category_ids) { $args['category__in'] = $category_ids; }
        if ($case_id) { $args['category__not_in'] = [$case_id]; }
        $ids = get_posts($args);
        if (count($ids) < $count) {
            $fill = get_posts(['post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => $count - count($ids), 'fields' => 'ids', 'post__not_in' => array_merge([$post_id], $ids), 'category__not_in' => $case_id ? [$case_id] : [], 'orderby' => 'ID', 'order' => 'ASC']);
            $ids = array_merge($ids, $fill);
        }
        return array_slice(array_map('intval', $ids), 0, $count);
    }

    protected function render(): void {
        $post_id = $this->post_id();
        if (!$post_id) { return; }
        $s = $this->get_settings_for_display();
        $ids = $this->related_ids($post_id, (int) ($s['count'] ?? 3));
        if (!$ids) { return; }
        $news_page = (int) get_option('page_for_posts');
        $news_url = $news_page ? get_permalink($news_page) : home_url('/news/');
        ?>
        <section class="related-news" aria-labelledby="related-news-title"><div class="xz-container"><div class="related-news__head"><div><?php if (!empty($s['eyebrow'])) : ?><p class="article-eyebrow"><?php echo esc_html((string) $s['eyebrow']); ?></p><?php endif; ?><h2 id="related-news-title"><?php echo esc_html((string) ($s['title'] ?? 'Related News')); ?></h2></div><a href="<?php echo esc_url($news_url); ?>"><?php echo esc_html((string) ($s['view_all_text'] ?? 'View All News')); ?><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg></a></div><div class="related-news__grid">
            <?php foreach ($ids as $related_id) : $category = news_detail_category($related_id); ?><article><a href="<?php echo esc_url(get_permalink($related_id)); ?>"><figure><?php echo get_the_post_thumbnail($related_id, 'large', ['loading' => 'lazy']); ?><?php if ($category) : ?><span><?php echo esc_html($category->name); ?></span><?php endif; ?></figure><time datetime="<?php echo esc_attr(get_the_date('c', $related_id)); ?>"><?php echo esc_html(get_the_date('F j, Y', $related_id)); ?></time><h3><?php echo esc_html(get_the_title($related_id)); ?></h3></a></article><?php endforeach; ?>
        </div></div></section>
        <?php
    }
}

function register_news_detail_widgets($widgets_manager): void {
    $widgets_manager->register(new News_Detail_Hero_Widget());
    $widgets_manager->register(new News_Detail_Body_Widget());
    $widgets_manager->register(new News_Detail_Related_Widget());
}

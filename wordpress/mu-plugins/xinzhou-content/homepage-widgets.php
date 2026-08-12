<?php

namespace Xinzhou\Elementor;

use Elementor\Controls_Manager;
use Elementor\Repeater;

if (!defined('ABSPATH')) {
    exit;
}

abstract class Xinzhou_Section_Widget extends Xinzhou_Widget {
    public function get_categories(): array {
        return ['xinzhou-sections'];
    }

    protected function image_url(array $image): string {
        if (!empty($image['id'])) {
            $url = wp_get_attachment_image_url((int) $image['id'], 'full');
            if ($url) {
                return $url;
            }
        }
        return (string) ($image['url'] ?? '');
    }

    protected function link_url(array $link): string {
        return (string) ($link['url'] ?? '');
    }
}

final class Home_Split_Widget extends Xinzhou_Section_Widget {
    public function get_name(): string { return 'xinzhou-home-split'; }
    public function get_title(): string { return 'Xinzhou Image & Content'; }
    public function get_icon(): string { return 'eicon-image-box'; }

    protected function register_controls(): void {
        $this->start_controls_section('content', ['label' => 'Content']);
        $this->add_control('eyebrow', ['label' => 'Small Title', 'type' => Controls_Manager::TEXT, 'default' => 'Smart Factory Solutions']);
        $this->add_control('title', ['label' => 'Title', 'type' => Controls_Manager::TEXTAREA, 'default' => 'Xinzhou Smart Welding Systems for Modern Production']);
        $this->add_control('lead', ['label' => 'Highlighted Description', 'type' => Controls_Manager::TEXTAREA, 'default' => '27+ years of R&D, in-house manufacturing and turnkey automation for steel grating, wire mesh and custom resistance welding lines.']);
        $this->add_control('description', ['label' => 'Description', 'type' => Controls_Manager::WYSIWYG, 'default' => '<p>From factory layout planning to equipment manufacturing, commissioning and long-term remote support, Xinzhou helps global manufacturers build stable, efficient and scalable welding production systems.</p>']);
        $this->add_control('button_text', ['label' => 'Button Text', 'type' => Controls_Manager::TEXT, 'default' => 'View More']);
        $this->add_control('button_link', ['label' => 'Button Link', 'type' => Controls_Manager::URL, 'default' => ['url' => '/about-xinzhou/']]);
        $this->end_controls_section();

        $this->start_controls_section('media', ['label' => 'Media']);
        $this->add_control('image', ['label' => 'Image', 'type' => Controls_Manager::MEDIA]);
        $this->add_control('image_position', ['label' => 'Image Position', 'type' => Controls_Manager::SELECT, 'default' => 'left', 'options' => ['left' => 'Left', 'right' => 'Right']]);
        $this->add_control('show_play', ['label' => 'Show Play Button', 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes']);
        $this->add_control('video_link', ['label' => 'Video Link', 'type' => Controls_Manager::URL, 'condition' => ['show_play' => 'yes']]);
        $this->end_controls_section();
    }

    protected function render(): void {
        $s = $this->get_settings_for_display();
        $image = $this->image_url((array) ($s['image'] ?? []));
        $reverse = ($s['image_position'] ?? 'left') === 'right';
        ?>
        <section class="xz-home-split<?php echo $reverse ? ' xz-home-split--reverse' : ''; ?>">
            <div class="xz-home-split__inner">
                <div class="xz-home-split__media">
                    <?php if ($image) : ?><img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr(wp_strip_all_tags((string) ($s['title'] ?? ''))); ?>"><?php endif; ?>
                    <?php $video_url = $this->link_url((array) ($s['video_link'] ?? [])); if (($s['show_play'] ?? '') === 'yes' && $video_url) : ?><a class="xz-home-play" href="<?php echo esc_url($video_url); ?>" data-xz-video-open aria-label="Play video"><span></span></a><?php endif; ?>
                </div>
                <div class="xz-home-split__copy">
                    <?php if (!empty($s['eyebrow'])) : ?><div class="xz-home-eyebrow"><?php echo esc_html($s['eyebrow']); ?></div><?php endif; ?>
                    <h2><?php echo esc_html((string) ($s['title'] ?? '')); ?></h2>
                    <?php if (!empty($s['lead'])) : ?><p class="xz-home-split__lead"><?php echo esc_html($s['lead']); ?></p><?php endif; ?>
                    <div class="xz-home-split__description"><?php echo wp_kses_post((string) ($s['description'] ?? '')); ?></div>
                    <?php if (!empty($s['button_text'])) : ?><a class="xz-home-button" href="<?php echo esc_url($this->link_url((array) ($s['button_link'] ?? []))); ?>"><?php echo esc_html($s['button_text']); ?></a><?php endif; ?>
                </div>
            </div>
        </section>
        <?php if (($s['show_play'] ?? '') === 'yes' && $video_url) : ?><dialog class="xz-video-dialog" data-xz-video-dialog aria-label="Video player"><button class="xz-video-dialog__close" type="button" data-xz-video-close aria-label="Close video"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path></svg></button><div class="xz-video-dialog__stage" data-xz-video-stage></div></dialog><?php endif; ?>
        <?php
    }
}

final class Home_Carousel_Widget extends Xinzhou_Section_Widget {
    public function get_name(): string { return 'xinzhou-home-carousel'; }
    public function get_title(): string { return 'Xinzhou Content Carousel'; }
    public function get_icon(): string { return 'eicon-slider-push'; }
    public function get_script_depends(): array { return ['xinzhou-content']; }

    protected function register_controls(): void {
        $this->start_controls_section('query', ['label' => 'Content Source']);
        $this->add_control('source', ['label' => 'Source', 'type' => Controls_Manager::SELECT, 'default' => 'products', 'options' => ['products' => 'Products', 'news' => 'News', 'manual' => 'Manual Cards']]);
        $this->add_control('selected_products', ['label' => 'Select Products', 'type' => Controls_Manager::SELECT2, 'multiple' => true, 'options' => post_control_options('product'), 'description' => 'Leave empty to show the latest products.', 'condition' => ['source' => 'products']]);
        $this->add_control('count', ['label' => 'Number of Items', 'type' => Controls_Manager::NUMBER, 'default' => 6, 'min' => 4, 'max' => 20, 'condition' => ['source!' => 'manual']]);
        $this->add_control('autoplay', ['label' => 'Autoplay', 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes']);
        $this->add_control('autoplay_speed', ['label' => 'Autoplay Speed (ms)', 'type' => Controls_Manager::NUMBER, 'default' => 4200, 'min' => 1500, 'max' => 15000, 'condition' => ['autoplay' => 'yes']]);

        $repeater = new Repeater();
        $repeater->add_control('image', ['label' => 'Image', 'type' => Controls_Manager::MEDIA]);
        $repeater->add_control('category', ['label' => 'Category', 'type' => Controls_Manager::TEXT]);
        $repeater->add_control('title', ['label' => 'Title', 'type' => Controls_Manager::TEXTAREA]);
        $repeater->add_control('description', ['label' => 'Description', 'type' => Controls_Manager::TEXTAREA]);
        $repeater->add_control('link', ['label' => 'Link', 'type' => Controls_Manager::URL]);
        $this->add_control('cards', ['label' => 'Manual Cards', 'type' => Controls_Manager::REPEATER, 'fields' => $repeater->get_controls(), 'condition' => ['source' => 'manual']]);
        $this->end_controls_section();

        $this->start_controls_section('labels', ['label' => 'Labels']);
        $this->add_control('button_text', ['label' => 'Button Text', 'type' => Controls_Manager::TEXT, 'default' => 'View More']);
        $this->end_controls_section();
    }

    private function query_cards(array $s): array {
        $source = $s['source'] ?? 'products';
        if ($source === 'manual') {
            $cards = [];
            foreach ((array) ($s['cards'] ?? []) as $item) {
                $cards[] = [
                    'image' => $this->image_url((array) ($item['image'] ?? [])),
                    'category' => (string) ($item['category'] ?? ''),
                    'title' => (string) ($item['title'] ?? ''),
                    'description' => (string) ($item['description'] ?? ''),
                    'url' => $this->link_url((array) ($item['link'] ?? [])),
                ];
            }
            return $cards;
        }

        $args = [
            'post_type' => $source === 'products' ? 'product' : 'post',
            'post_status' => 'publish',
            'posts_per_page' => max(4, (int) ($s['count'] ?? 6)),
            'orderby' => $source === 'products' ? ['menu_order' => 'ASC', 'date' => 'DESC'] : ['date' => 'DESC'],
        ];
        if ($source === 'products') {
            $selected = array_values(array_filter(array_map('intval', (array) ($s['selected_products'] ?? []))));
            if ($selected) {
                $args['post__in'] = $selected;
                $args['posts_per_page'] = count($selected);
                $args['orderby'] = 'post__in';
            } else {
                $args['orderby'] = ['date' => 'DESC'];
            }
        }
        if ($source === 'news') {
            $cases = get_term_by('slug', 'cases', 'category');
            if ($cases instanceof \WP_Term) {
                $args['category__not_in'] = [(int) $cases->term_id];
            }
        }
        $query = new \WP_Query($args);
        $cards = [];
        foreach ($query->posts as $post) {
            $terms = $source === 'products' ? wp_get_post_terms($post->ID, 'product_category') : get_the_category($post->ID);
            $cards[] = [
                'image' => get_the_post_thumbnail_url($post->ID, 'large') ?: '',
                'category' => !is_wp_error($terms) && $terms ? $terms[0]->name : '',
                'title' => get_the_title($post),
                'description' => get_the_excerpt($post),
                'url' => get_permalink($post),
            ];
        }
        wp_reset_postdata();
        return $cards;
    }

    protected function render(): void {
        $s = $this->get_settings_for_display();
        $cards = $this->query_cards($s);
        if (!$cards) {
            return;
        }
        ?>
        <section class="xz-home-carousel" data-xz-carousel data-autoplay="<?php echo ($s['autoplay'] ?? '') === 'yes' ? 'true' : 'false'; ?>" data-speed="<?php echo esc_attr((string) ($s['autoplay_speed'] ?? 4200)); ?>">
            <div class="xz-home-carousel__inner">
                <div class="xz-home-carousel__controls" aria-label="Carousel controls">
                    <button type="button" data-xz-carousel-prev aria-label="Previous"><span aria-hidden="true">&#8249;</span></button>
                    <button type="button" data-xz-carousel-next aria-label="Next"><span aria-hidden="true">&#8250;</span></button>
                </div>
                <div class="xz-home-carousel__viewport">
                    <div class="xz-home-carousel__track" data-xz-carousel-track>
                        <?php foreach ($cards as $card) : ?>
                            <article class="xz-home-carousel-card">
                                <a class="xz-home-carousel-card__media" href="<?php echo esc_url($card['url']); ?>"><?php if ($card['image']) : ?><img src="<?php echo esc_url($card['image']); ?>" alt="<?php echo esc_attr($card['title']); ?>" loading="lazy"><?php endif; ?></a>
                                <div class="xz-home-carousel-card__body">
                                    <?php if ($card['category']) : ?><span class="xz-home-carousel-card__category"><?php echo esc_html($card['category']); ?></span><?php endif; ?>
                                    <h3><a href="<?php echo esc_url($card['url']); ?>"><?php echo esc_html($card['title']); ?></a></h3>
                                    <?php if ($card['description']) : ?><p><?php echo esc_html(wp_trim_words(wp_strip_all_tags($card['description']), 22)); ?></p><?php endif; ?>
                                    <a class="xz-home-arrow-link" href="<?php echo esc_url($card['url']); ?>"><?php echo esc_html((string) ($s['button_text'] ?? 'View More')); ?></a>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>
        <?php
    }
}

final class Home_Stories_Widget extends Xinzhou_Section_Widget {
    public function get_name(): string { return 'xinzhou-home-stories'; }
    public function get_title(): string { return 'Xinzhou Alternating Stories'; }
    public function get_icon(): string { return 'eicon-columns'; }

    protected function register_controls(): void {
        $this->start_controls_section('content', ['label' => 'Story Rows']);
        $repeater = new Repeater();
        $repeater->add_control('image', ['label' => 'Image', 'type' => Controls_Manager::MEDIA]);
        $repeater->add_control('image_position', ['label' => 'Image Position', 'type' => Controls_Manager::SELECT, 'default' => 'left', 'options' => ['left' => 'Left', 'right' => 'Right']]);
        $repeater->add_control('eyebrow', ['label' => 'Small Title', 'type' => Controls_Manager::TEXT]);
        $repeater->add_control('title', ['label' => 'Title', 'type' => Controls_Manager::TEXTAREA]);
        $repeater->add_control('lead', ['label' => 'Highlighted Description', 'type' => Controls_Manager::TEXTAREA]);
        $repeater->add_control('description', ['label' => 'Description', 'type' => Controls_Manager::WYSIWYG]);
        $repeater->add_control('button_text', ['label' => 'Button Text', 'type' => Controls_Manager::TEXT, 'default' => 'View More']);
        $repeater->add_control('button_link', ['label' => 'Button Link', 'type' => Controls_Manager::URL]);
        $this->add_control('stories', ['label' => 'Rows', 'type' => Controls_Manager::REPEATER, 'fields' => $repeater->get_controls(), 'title_field' => '{{{ title }}}']);
        $this->end_controls_section();
    }

    protected function render(): void {
        $s = $this->get_settings_for_display();
        ?>
        <section class="xz-home-stories">
            <div class="xz-home-stories__inner">
                <?php foreach ((array) ($s['stories'] ?? []) as $story) :
                    $reverse = ($story['image_position'] ?? 'left') === 'right';
                    $image = $this->image_url((array) ($story['image'] ?? []));
                    ?>
                    <article class="xz-home-story<?php echo $reverse ? ' xz-home-story--reverse' : ''; ?>">
                        <div class="xz-home-story__media"><?php if ($image) : ?><img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr((string) ($story['title'] ?? '')); ?>" loading="lazy"><?php endif; ?></div>
                        <div class="xz-home-story__copy">
                            <?php if (!empty($story['eyebrow'])) : ?><div class="xz-home-eyebrow"><?php echo esc_html($story['eyebrow']); ?></div><?php endif; ?>
                            <h2><?php echo esc_html((string) ($story['title'] ?? '')); ?></h2>
                            <?php if (!empty($story['lead'])) : ?><p class="xz-home-story__lead"><?php echo esc_html($story['lead']); ?></p><?php endif; ?>
                            <div class="xz-home-story__description"><?php echo wp_kses_post((string) ($story['description'] ?? '')); ?></div>
                            <?php if (!empty($story['button_text'])) : ?><a class="xz-home-button" href="<?php echo esc_url($this->link_url((array) ($story['button_link'] ?? []))); ?>"><?php echo esc_html($story['button_text']); ?></a><?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
        <?php
    }
}

final class Home_Cases_Widget extends Xinzhou_Section_Widget {
    public function get_name(): string { return 'xinzhou-home-cases'; }
    public function get_title(): string { return 'Xinzhou Case Showcase'; }
    public function get_icon(): string { return 'eicon-gallery-justified'; }
    public function get_script_depends(): array { return ['xinzhou-content']; }

    protected function register_controls(): void {
        $this->start_controls_section('content', ['label' => 'Cases']);
        $case_term = get_term_by('slug', 'cases', 'category');
        $this->add_control('category_id', ['label' => 'Post Category', 'type' => Controls_Manager::SELECT, 'default' => $case_term instanceof \WP_Term ? (string) $case_term->term_id : '', 'options' => post_category_control_options()]);
        $this->add_control('selected_posts', ['label' => 'Select Posts', 'type' => Controls_Manager::SELECT2, 'multiple' => true, 'options' => post_control_options('post'), 'description' => 'Leave empty to show the latest posts from the selected category.']);
        $this->add_control('count', ['label' => 'Number of Cases', 'type' => Controls_Manager::NUMBER, 'default' => 5, 'min' => 1, 'max' => 10]);
        $this->add_control('label', ['label' => 'Card Label', 'type' => Controls_Manager::TEXT, 'default' => 'Case']);
        $this->end_controls_section();
    }

    protected function render(): void {
        $s = $this->get_settings_for_display();
        $selected = array_values(array_filter(array_map('intval', (array) ($s['selected_posts'] ?? []))));
        $args = ['post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => max(1, (int) ($s['count'] ?? 5)), 'orderby' => 'date', 'order' => 'DESC'];
        if ($selected) {
            $args['post__in'] = $selected;
            $args['posts_per_page'] = count($selected);
            $args['orderby'] = 'post__in';
        } elseif (!empty($s['category_id'])) {
            $args['category__in'] = [(int) $s['category_id']];
        }
        $query = new \WP_Query($args);
        if (!$query->have_posts()) {
            return;
        }
        $posts = $query->posts;
        $carousel = count($posts) > 5;
        ?>
        <section class="xz-home-cases"><div class="xz-home-cases__carousel xz-simple-carousel<?php echo $carousel ? ' is-carousel' : ''; ?>"<?php echo $carousel ? ' data-xz-simple-carousel data-visible="5"' : ''; ?>>
            <?php if ($carousel) : ?><div class="xz-simple-carousel__pagination" data-xz-simple-pagination aria-label="Case carousel navigation"></div><?php endif; ?>
            <div class="xz-home-cases__grid"<?php echo $carousel ? ' data-xz-simple-track' : ''; ?>>
            <?php foreach ($posts as $post) : ?>
                <a class="xz-home-case" href="<?php echo esc_url(get_permalink($post)); ?>">
                    <?php echo get_the_post_thumbnail($post, 'large', ['loading' => 'lazy']); ?>
                    <span class="xz-home-case__overlay"><small><?php echo esc_html((string) ($s['label'] ?? 'Case')); ?></small><strong><?php echo esc_html(get_the_title($post)); ?></strong></span>
                </a>
            <?php endforeach; wp_reset_postdata(); ?>
            </div>
        </div></section>
        <?php
    }
}

final class Home_Worldwide_Widget extends Xinzhou_Section_Widget {
    public function get_name(): string { return 'xinzhou-home-worldwide'; }
    public function get_title(): string { return 'Xinzhou Worldwide Logos'; }
    public function get_icon(): string { return 'eicon-logo'; }
    public function get_script_depends(): array { return ['xinzhou-content']; }

    protected function register_controls(): void {
        $this->start_controls_section('content', ['label' => 'Content']);
        $this->add_control('title', ['label' => 'Title', 'type' => Controls_Manager::TEXT, 'default' => 'Xinzhou Worldwide']);
        $repeater = new Repeater();
        $repeater->add_control('image', ['label' => 'Logo', 'type' => Controls_Manager::MEDIA]);
        $repeater->add_control('link', ['label' => 'Link', 'type' => Controls_Manager::URL]);
        $this->add_control('logos', ['label' => 'Logos', 'type' => Controls_Manager::REPEATER, 'fields' => $repeater->get_controls()]);
        $this->end_controls_section();
    }

    protected function render(): void {
        $s = $this->get_settings_for_display();
        ?>
        <section class="xz-home-worldwide"><div class="xz-home-worldwide__inner">
            <h2><?php echo esc_html((string) ($s['title'] ?? 'Xinzhou Worldwide')); ?></h2>
            <?php $logos = (array) ($s['logos'] ?? []); $carousel = count($logos) > 4; ?>
            <div class="xz-simple-carousel<?php echo $carousel ? ' is-carousel' : ''; ?>"<?php echo $carousel ? ' data-xz-simple-carousel data-visible="4"' : ''; ?>>
            <?php if ($carousel) : ?><div class="xz-simple-carousel__controls"><button type="button" data-xz-simple-prev aria-label="Previous logos">&#8249;</button><button type="button" data-xz-simple-next aria-label="Next logos">&#8250;</button></div><?php endif; ?>
            <div class="xz-home-worldwide__grid"<?php echo $carousel ? ' data-xz-simple-track' : ''; ?>>
                <?php foreach ($logos as $logo) : $image_id = (int) ($logo['image']['id'] ?? 0); $image = $this->image_url((array) ($logo['image'] ?? [])); ?>
                    <a href="<?php echo esc_url($this->link_url((array) ($logo['link'] ?? []))); ?>"><?php if ($image_id) : echo wp_get_attachment_image($image_id, 'full', false, ['loading' => 'lazy']); elseif ($image) : ?><img src="<?php echo esc_url($image); ?>" alt="" loading="lazy"><?php endif; ?></a>
                <?php endforeach; ?>
            </div>
            </div>
        </div></section>
        <?php
    }
}

function register_homepage_widgets($widgets_manager): void {
    $widgets_manager->register(new Home_Split_Widget());
    $widgets_manager->register(new Home_Carousel_Widget());
    $widgets_manager->register(new Home_Stories_Widget());
    $widgets_manager->register(new Home_Cases_Widget());
    $widgets_manager->register(new Home_Worldwide_Widget());
}

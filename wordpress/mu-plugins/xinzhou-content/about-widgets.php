<?php

namespace Xinzhou\Elementor;

use Elementor\Controls_Manager;
use Elementor\Repeater;

if (!defined('ABSPATH')) {
    exit;
}

abstract class About_Widget_Base extends Xinzhou_Section_Widget {
    public function get_style_depends(): array {
        return ['xinzhou-content', 'xinzhou-about-widgets'];
    }

    protected function add_heading_controls(string $section_label, string $label_default, string $title_default): void {
        $this->start_controls_section($section_label, ['label' => 'Heading']);
        $this->add_control('label', ['label' => 'Small Title', 'type' => Controls_Manager::TEXT, 'default' => $label_default]);
        $this->add_control('title', ['label' => 'Title', 'type' => Controls_Manager::TEXTAREA, 'default' => $title_default]);
        $this->end_controls_section();
    }
}

final class About_Hero_Widget extends About_Widget_Base {
    public function get_name(): string { return 'xinzhou-about-hero'; }
    public function get_title(): string { return 'Xinzhou About Hero'; }
    public function get_icon(): string { return 'eicon-post-content'; }

    protected function register_controls(): void {
        $this->start_controls_section('content', ['label' => 'Content']);
        $this->add_control('breadcrumb_label', ['label' => 'Breadcrumb Label', 'type' => Controls_Manager::TEXT, 'default' => 'About Us']);
        $this->add_control('title', ['label' => 'Title', 'type' => Controls_Manager::TEXTAREA, 'default' => '28 Years of Expertise in Automated Welding Equipment.']);
        $this->add_control('description', ['label' => 'Description', 'type' => Controls_Manager::WYSIWYG]);
        $this->end_controls_section();
        $this->start_controls_section('media', ['label' => 'Media']);
        $this->add_control('image', ['label' => 'Image', 'type' => Controls_Manager::MEDIA]);
        $this->add_control('caption_title', ['label' => 'Caption Title', 'type' => Controls_Manager::TEXT, 'default' => 'Automated Welding Lines']);
        $this->add_control('caption_text', ['label' => 'Caption Text', 'type' => Controls_Manager::TEXT, 'default' => 'Research, design, manufacturing and global supply.']);
        $this->end_controls_section();
    }

    protected function render(): void {
        $s = $this->get_settings_for_display();
        $image = $this->image_url((array) ($s['image'] ?? []));
        ?>
        <section class="about-history-hero" aria-labelledby="about-history-title">
            <div class="xz-container about-history-hero__grid">
                <div class="about-history-hero__copy">
                    <nav class="about-breadcrumb" aria-label="Breadcrumb"><a href="<?php echo esc_url(home_url('/')); ?>">Home</a><span aria-hidden="true">/</span><span><?php echo esc_html((string) ($s['breadcrumb_label'] ?? 'About Us')); ?></span></nav>
                    <h1 id="about-history-title"><?php echo esc_html((string) ($s['title'] ?? '')); ?></h1>
                    <div class="about-history-hero__text"><?php echo wp_kses_post((string) ($s['description'] ?? '')); ?></div>
                </div>
                <figure class="about-history-hero__media">
                    <?php if ($image) : ?><img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr((string) ($s['title'] ?? '')); ?>"><?php endif; ?>
                    <?php if (!empty($s['caption_title']) || !empty($s['caption_text'])) : ?><figcaption><strong><?php echo esc_html((string) ($s['caption_title'] ?? '')); ?></strong><span><?php echo esc_html((string) ($s['caption_text'] ?? '')); ?></span></figcaption><?php endif; ?>
                </figure>
            </div>
        </section>
        <?php
    }
}

final class About_Factory_Widget extends About_Widget_Base {
    public function get_name(): string { return 'xinzhou-about-factory'; }
    public function get_title(): string { return 'Xinzhou About Factory'; }
    public function get_icon(): string { return 'eicon-gallery-grid'; }

    protected function register_controls(): void {
        $this->add_heading_controls('heading', 'Our Factory', 'Modern Manufacturing for Standard and Customized Projects');
        $this->start_controls_section('copy', ['label' => 'Description']);
        $this->add_control('description', ['label' => 'Text', 'type' => Controls_Manager::WYSIWYG]);
        $this->end_controls_section();
        $this->start_controls_section('highlights', ['label' => 'Factory Highlights']);
        $r = new Repeater();
        $r->add_control('image', ['label' => 'Image', 'type' => Controls_Manager::MEDIA]);
        $r->add_control('title', ['label' => 'Title', 'type' => Controls_Manager::TEXTAREA]);
        $this->add_control('items', ['label' => 'Highlights', 'type' => Controls_Manager::REPEATER, 'fields' => $r->get_controls(), 'title_field' => '{{{ title }}}']);
        $this->end_controls_section();
    }

    protected function render(): void {
        $s = $this->get_settings_for_display();
        ?>
        <section class="about-factory-section" aria-labelledby="about-factory-title"><div class="xz-container about-factory__layout">
            <div class="about-factory__visual"><div class="about-factory__highlights">
                <?php foreach ((array) ($s['items'] ?? []) as $item) : $image = $this->image_url((array) ($item['image'] ?? [])); ?>
                    <article class="about-factory-highlight"><figure class="about-factory-highlight__media"><?php if ($image) : ?><img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr((string) ($item['title'] ?? '')); ?>" loading="lazy"><?php endif; ?></figure><div class="about-factory-highlight__body"><h3><?php echo esc_html((string) ($item['title'] ?? '')); ?></h3></div></article>
                <?php endforeach; ?>
            </div></div>
            <div class="about-factory__content"><div class="about-factory__head"><p class="about-section-label"><?php echo esc_html((string) ($s['label'] ?? '')); ?></p><h2 id="about-factory-title"><?php echo esc_html((string) ($s['title'] ?? '')); ?></h2></div><div class="about-factory__copy"><?php echo wp_kses_post((string) ($s['description'] ?? '')); ?></div></div>
        </div></section>
        <?php
    }
}

final class About_Team_Widget extends About_Widget_Base {
    public function get_name(): string { return 'xinzhou-about-team'; }
    public function get_title(): string { return 'Xinzhou About Team'; }
    public function get_icon(): string { return 'eicon-person'; }

    protected function register_controls(): void {
        $this->add_heading_controls('heading', 'Our Professional Team', 'Specialists for Every Stage of Your Project');
        $this->start_controls_section('members', ['label' => 'Team Members']);
        $r = new Repeater();
        $r->add_control('image', ['label' => 'Image', 'type' => Controls_Manager::MEDIA]);
        $r->add_control('title', ['label' => 'Title', 'type' => Controls_Manager::TEXTAREA]);
        $r->add_control('description', ['label' => 'Description', 'type' => Controls_Manager::TEXTAREA]);
        $this->add_control('items', ['label' => 'Members', 'type' => Controls_Manager::REPEATER, 'fields' => $r->get_controls(), 'title_field' => '{{{ title }}}']);
        $this->end_controls_section();
    }

    protected function render(): void {
        $s = $this->get_settings_for_display();
        ?>
        <section class="about-professional-team-section" aria-labelledby="about-professional-team-title"><div class="xz-container">
            <div class="about-professional-team__head"><p class="about-section-label"><?php echo esc_html((string) ($s['label'] ?? '')); ?></p><h2 id="about-professional-team-title"><?php echo esc_html((string) ($s['title'] ?? '')); ?></h2></div>
            <div class="about-professional-team__grid"><?php foreach ((array) ($s['items'] ?? []) as $index => $item) : $image = $this->image_url((array) ($item['image'] ?? [])); ?>
                <article class="about-professional-team__item"><figure class="about-professional-team__media"><?php if ($image) : ?><img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr((string) ($item['title'] ?? '')); ?>" loading="lazy"><?php endif; ?></figure><div class="about-professional-team__body"><span><?php echo esc_html(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)); ?></span><h3><?php echo esc_html((string) ($item['title'] ?? '')); ?></h3><p><?php echo esc_html((string) ($item['description'] ?? '')); ?></p></div></article>
            <?php endforeach; ?></div>
        </div></section>
        <?php
    }
}

final class About_Equipment_Widget extends About_Widget_Base {
    public function get_name(): string { return 'xinzhou-about-equipment'; }
    public function get_title(): string { return 'Xinzhou About Equipment'; }
    public function get_icon(): string { return 'eicon-products'; }

    protected function register_controls(): void {
        $this->add_heading_controls('heading', 'Our Products', 'Intelligent Welding Equipment and Turnkey Production Lines');
        $this->start_controls_section('content', ['label' => 'Content']);
        $this->add_control('intro', ['label' => 'Introduction', 'type' => Controls_Manager::TEXTAREA, 'default' => 'Xinzhou specializes in the manufacturing of intelligent welding equipment and turnkey production lines, including:']);
        $this->add_control('footer', ['label' => 'Footer Text', 'type' => Controls_Manager::TEXTAREA, 'default' => 'We also provide complete turnkey solutions from project planning to production line installation.']);
        $this->add_control('hide_empty', ['label' => 'Hide Empty Categories', 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => '']);
        $this->end_controls_section();
        $this->start_controls_section('titles', ['label' => 'Card Title Overrides']);
        $r = new Repeater();
        $r->add_control('term_slug', ['label' => 'Category Slug', 'type' => Controls_Manager::TEXT]);
        $r->add_control('title', ['label' => 'Display Title', 'type' => Controls_Manager::TEXTAREA]);
        $this->add_control('title_overrides', ['label' => 'Overrides', 'type' => Controls_Manager::REPEATER, 'fields' => $r->get_controls(), 'title_field' => '{{{ title }}}']);
        $this->end_controls_section();
    }

    protected function render(): void {
        $s = $this->get_settings_for_display();
        $terms = get_terms(['taxonomy' => 'product_category', 'hide_empty' => ($s['hide_empty'] ?? '') === 'yes']);
        if (is_wp_error($terms)) { $terms = []; }
        usort($terms, static function (\WP_Term $a, \WP_Term $b): int { return ((int) get_term_meta($a->term_id, 'category_display_order', true)) <=> ((int) get_term_meta($b->term_id, 'category_display_order', true)); });
        $title_overrides = [];
        foreach ((array) ($s['title_overrides'] ?? []) as $override) {
            $slug = sanitize_title((string) ($override['term_slug'] ?? ''));
            if ($slug) {
                $title_overrides[$slug] = (string) ($override['title'] ?? '');
            }
        }
        ?>
        <section class="about-equipment-section" aria-labelledby="about-equipment-title"><div class="xz-container">
            <div class="about-equipment__head"><div><p class="about-section-label"><?php echo esc_html((string) ($s['label'] ?? '')); ?></p><h2 id="about-equipment-title"><?php echo esc_html((string) ($s['title'] ?? '')); ?></h2></div><p><?php echo esc_html((string) ($s['intro'] ?? '')); ?></p></div>
            <div class="about-equipment__grid"><?php foreach ($terms as $term) : $image_id = \xz_product_term_image((int) $term->term_id); ?>
                <article class="about-equipment-item"><a href="<?php echo esc_url(get_term_link($term)); ?>"><figure class="about-equipment-item__media"><?php echo $image_id ? wp_get_attachment_image($image_id, 'large', false, ['loading' => 'lazy']) : ''; ?></figure><div class="about-equipment-item__body"><h3><?php echo esc_html($title_overrides[$term->slug] ?? $term->name); ?></h3></div></a></article>
            <?php endforeach; ?></div>
            <?php if (!empty($s['footer'])) : ?><div class="about-equipment__footer"><p><?php echo esc_html($s['footer']); ?></p></div><?php endif; ?>
        </div></section>
        <?php
    }
}

final class About_Quality_Widget extends About_Widget_Base {
    public function get_name(): string { return 'xinzhou-about-quality'; }
    public function get_title(): string { return 'Xinzhou About Certifications'; }
    public function get_icon(): string { return 'eicon-check-circle'; }

    protected function register_controls(): void {
        $this->add_heading_controls('heading', 'Our Certifications', 'Quality Is Our Commitment.');
        $this->start_controls_section('content', ['label' => 'Content']);
        $this->add_control('description', ['label' => 'Description', 'type' => Controls_Manager::WYSIWYG]);
        $this->add_control('list_label', ['label' => 'Certification Label', 'type' => Controls_Manager::TEXT, 'default' => 'Our certifications include:']);
        $this->end_controls_section();
        $this->start_controls_section('certificates', ['label' => 'Certificates']);
        $r = new Repeater(); $r->add_control('image', ['label' => 'Image', 'type' => Controls_Manager::MEDIA]); $r->add_control('title', ['label' => 'Title', 'type' => Controls_Manager::TEXTAREA]);
        $this->add_control('items', ['label' => 'Certificates', 'type' => Controls_Manager::REPEATER, 'fields' => $r->get_controls(), 'title_field' => '{{{ title }}}']);
        $this->end_controls_section();
    }

    protected function render(): void {
        $s = $this->get_settings_for_display();
        ?>
        <section class="about-quality-section" aria-labelledby="about-quality-title"><div class="xz-container about-quality__grid">
            <div class="about-quality__copy"><p class="about-section-label"><?php echo esc_html((string) ($s['label'] ?? '')); ?></p><h2 id="about-quality-title"><?php echo esc_html((string) ($s['title'] ?? '')); ?></h2><?php echo wp_kses_post((string) ($s['description'] ?? '')); ?><p class="about-quality__list-label"><?php echo esc_html((string) ($s['list_label'] ?? '')); ?></p></div>
            <div class="about-quality__certificates"><?php foreach ((array) ($s['items'] ?? []) as $item) : $image = $this->image_url((array) ($item['image'] ?? [])); ?><article class="about-quality-certificate"><figure class="about-quality-certificate__media"><?php if ($image) : ?><img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr((string) ($item['title'] ?? '')); ?>" loading="lazy"><?php endif; ?></figure><div class="about-quality-certificate__body"><h3><?php echo esc_html((string) ($item['title'] ?? '')); ?></h3></div></article><?php endforeach; ?></div>
        </div></section>
        <?php
    }
}

final class About_Customers_Widget extends About_Widget_Base {
    public function get_name(): string { return 'xinzhou-about-customers'; }
    public function get_title(): string { return 'Xinzhou About Customers'; }
    public function get_icon(): string { return 'eicon-globe'; }

    protected function register_controls(): void {
        $this->add_heading_controls('heading', 'Our Customers', 'Production Lines Delivered Across Global Markets');
        $this->start_controls_section('content', ['label' => 'Content']);
        $this->add_control('description', ['label' => 'Description', 'type' => Controls_Manager::TEXTAREA]);
        $this->add_control('image', ['label' => 'Image', 'type' => Controls_Manager::MEDIA]);
        $this->end_controls_section();
        $this->start_controls_section('regions', ['label' => 'Regions']);
        $r = new Repeater(); $r->add_control('image', ['label' => 'Flag/Icon', 'type' => Controls_Manager::MEDIA]); $r->add_control('title', ['label' => 'Region', 'type' => Controls_Manager::TEXT]);
        $this->add_control('items', ['label' => 'Regions', 'type' => Controls_Manager::REPEATER, 'fields' => $r->get_controls(), 'title_field' => '{{{ title }}}']);
        $this->end_controls_section();
    }

    protected function render(): void {
        $s = $this->get_settings_for_display(); $image = $this->image_url((array) ($s['image'] ?? []));
        ?>
        <section class="about-customers-section" aria-labelledby="about-customers-title"><div class="xz-container about-customers__grid">
            <figure class="about-customers__media"><?php if ($image) : ?><img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr((string) ($s['title'] ?? '')); ?>" loading="lazy"><?php endif; ?></figure>
            <div class="about-customers__copy"><p class="about-section-label"><?php echo esc_html((string) ($s['label'] ?? '')); ?></p><h2 id="about-customers-title"><?php echo esc_html((string) ($s['title'] ?? '')); ?></h2><p><?php echo esc_html((string) ($s['description'] ?? '')); ?></p><ul class="about-customers__regions"><?php foreach ((array) ($s['items'] ?? []) as $item) : $flag = $this->image_url((array) ($item['image'] ?? [])); ?><li><?php if ($flag) : ?><img class="about-customers__flag" src="<?php echo esc_url($flag); ?>" alt="" loading="lazy"><?php endif; ?><strong><?php echo esc_html((string) ($item['title'] ?? '')); ?></strong></li><?php endforeach; ?></ul></div>
        </div></section>
        <?php
    }
}

final class About_Timeline_Widget extends About_Widget_Base {
    public function get_name(): string { return 'xinzhou-about-timeline'; }
    public function get_title(): string { return 'Xinzhou About Timeline'; }
    public function get_icon(): string { return 'eicon-time-line'; }
    public function get_script_depends(): array { return ['xinzhou-about-widgets']; }

    protected function register_controls(): void {
        $this->add_heading_controls('heading', 'Development History', "Key Milestones in Xinzhou's Manufacturing Growth");
        $this->start_controls_section('milestones', ['label' => 'Milestones']);
        $r = new Repeater(); $r->add_control('year', ['label' => 'Year', 'type' => Controls_Manager::TEXT]); $r->add_control('image', ['label' => 'Image', 'type' => Controls_Manager::MEDIA]); $r->add_control('description', ['label' => 'Description', 'type' => Controls_Manager::TEXTAREA]);
        $this->add_control('items', ['label' => 'Milestones', 'type' => Controls_Manager::REPEATER, 'fields' => $r->get_controls(), 'title_field' => '{{{ year }}}']);
        $this->end_controls_section();
    }

    protected function render(): void {
        $s = $this->get_settings_for_display();
        ?>
        <section class="about-timeline-section" aria-labelledby="about-timeline-title"><div class="xz-container"><div class="about-timeline-section__head"><p class="about-section-label"><?php echo esc_html((string) ($s['label'] ?? '')); ?></p><h2 id="about-timeline-title"><?php echo esc_html((string) ($s['title'] ?? '')); ?></h2></div>
            <div class="about-timeline-carousel" data-history-carousel><div class="about-timeline-viewport"><div class="about-timeline-track" data-history-track><?php foreach ((array) ($s['items'] ?? []) as $item) : $image = $this->image_url((array) ($item['image'] ?? [])); ?>
                <article class="about-timeline-card"><div class="about-timeline-card__year"><?php echo esc_html((string) ($item['year'] ?? '')); ?></div><div class="about-timeline-card__node" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M8 2v4"></path><path d="M16 2v4"></path><rect width="18" height="18" x="3" y="4" rx="2"></rect><path d="M3 10h18"></path></svg></div><div class="about-timeline-card__body"><figure class="about-timeline-card__image"><?php if ($image) : ?><img src="<?php echo esc_url($image); ?>" alt="" loading="lazy"><?php endif; ?></figure><p><?php echo esc_html((string) ($item['description'] ?? '')); ?></p></div></article>
            <?php endforeach; ?></div></div></div>
        </div></section>
        <?php
    }
}

function register_about_widgets($widgets_manager): void {
    $widgets_manager->register(new About_Hero_Widget());
    $widgets_manager->register(new About_Factory_Widget());
    $widgets_manager->register(new About_Team_Widget());
    $widgets_manager->register(new About_Equipment_Widget());
    $widgets_manager->register(new About_Quality_Widget());
    $widgets_manager->register(new About_Customers_Widget());
    $widgets_manager->register(new About_Timeline_Widget());
}

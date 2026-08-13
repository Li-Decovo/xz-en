<?php

namespace Xinzhou\Elementor;

use Elementor\Controls_Manager;
use Elementor\Repeater;

if (!defined('ABSPATH')) {
    exit;
}

abstract class Service_Widget_Base extends Xinzhou_Section_Widget {
    public function get_style_depends(): array {
        return ['xinzhou-content', 'xinzhou-service-widgets'];
    }
}

final class Service_Content_Widget extends Service_Widget_Base {
    public function get_name(): string { return 'xinzhou-service-content'; }
    public function get_title(): string { return 'Xinzhou Service Content'; }
    public function get_icon(): string { return 'eicon-image-box'; }

    protected function register_controls(): void {
        $this->start_controls_section('layout', ['label' => 'Layout']);
        $this->add_control('variant', [
            'label' => 'Section Type',
            'type' => Controls_Manager::SELECT,
            'default' => 'custom',
            'options' => [
                'hero' => 'Consultation Hero',
                'custom' => 'Customized Solution',
                'layout' => 'Production Layout',
                'onsite' => 'On-site Training',
            ],
        ]);
        $this->end_controls_section();

        $this->start_controls_section('content', ['label' => 'Content']);
        $this->add_control('breadcrumb_label', ['label' => 'Breadcrumb Label', 'type' => Controls_Manager::TEXT, 'default' => 'Services', 'condition' => ['variant' => 'hero']]);
        $this->add_control('title', ['label' => 'Title', 'type' => Controls_Manager::TEXTAREA]);
        $this->add_control('description', ['label' => 'Description', 'type' => Controls_Manager::WYSIWYG]);
        $this->add_control('list_label', ['label' => 'List Label', 'type' => Controls_Manager::TEXT, 'condition' => ['variant' => ['layout', 'onsite']]]);
        $r = new Repeater(); $r->add_control('text', ['label' => 'Item', 'type' => Controls_Manager::TEXT]);
        $this->add_control('items', ['label' => 'List Items', 'type' => Controls_Manager::REPEATER, 'fields' => $r->get_controls(), 'title_field' => '{{{ text }}}', 'condition' => ['variant' => ['layout', 'onsite']]]);
        $this->add_control('result', ['label' => 'Result / Highlighted Text', 'type' => Controls_Manager::TEXTAREA, 'condition' => ['variant' => 'onsite']]);
        $this->end_controls_section();

        $this->start_controls_section('media', ['label' => 'Media']);
        $this->add_control('image', ['label' => 'Image', 'type' => Controls_Manager::MEDIA]);
        $this->end_controls_section();
    }

    private function render_list(array $items, string $class): void {
        if (!$items) { return; }
        echo '<ul class="' . esc_attr($class) . '">';
        foreach ($items as $item) { echo '<li>' . esc_html((string) ($item['text'] ?? '')) . '</li>'; }
        echo '</ul>';
    }

    protected function render(): void {
        $s = $this->get_settings_for_display();
        $variant = (string) ($s['variant'] ?? 'custom');
        $image = $this->image_url((array) ($s['image'] ?? []));
        if ($variant === 'hero') : ?>
            <section class="services-consultation-hero" aria-labelledby="services-consultation-title"><div class="xz-container services-consultation-hero__grid"><div class="services-consultation-hero__copy"><nav class="services-breadcrumb" aria-label="Breadcrumb"><a href="<?php echo esc_url(home_url('/')); ?>">Home</a><span aria-hidden="true">/</span><span><?php echo esc_html((string) ($s['breadcrumb_label'] ?? 'Services')); ?></span></nav><h1 id="services-consultation-title"><?php echo esc_html((string) ($s['title'] ?? '')); ?></h1><div class="services-consultation-hero__text"><?php echo wp_kses_post((string) ($s['description'] ?? '')); ?></div></div><figure class="services-consultation-hero__media"><?php if ($image) : echo \xz_media_image($image); endif; ?></figure></div></section>
        <?php elseif ($variant === 'custom') : ?>
            <section class="services-custom-section" aria-labelledby="services-custom-title"><div class="xz-container services-custom__grid"><figure class="services-custom__media"><?php if ($image) : echo \xz_media_image($image, 'full', ['loading' => 'lazy']); endif; ?></figure><div class="services-custom__copy"><h2 id="services-custom-title"><?php echo esc_html((string) ($s['title'] ?? '')); ?></h2><?php echo wp_kses_post((string) ($s['description'] ?? '')); ?></div></div></section>
        <?php elseif ($variant === 'layout') : ?>
            <section class="services-layout-section" aria-labelledby="services-layout-title"><div class="xz-container services-layout__grid"><div class="services-layout__copy"><h2 id="services-layout-title"><?php echo esc_html((string) ($s['title'] ?? '')); ?></h2><?php echo wp_kses_post((string) ($s['description'] ?? '')); ?><?php if (!empty($s['list_label'])) : ?><p class="services-layout__list-label"><?php echo esc_html($s['list_label']); ?></p><?php endif; ?><?php $this->render_list((array) ($s['items'] ?? []), 'services-layout__list'); ?></div><figure class="services-layout__media"><?php if ($image) : echo \xz_media_image($image, 'full', ['loading' => 'lazy']); endif; ?></figure></div></section>
        <?php else : ?>
            <section class="services-onsite-section" aria-labelledby="services-onsite-title"><div class="xz-container services-onsite__grid"><figure class="services-onsite__media"><?php if ($image) : echo \xz_media_image($image, 'full', ['loading' => 'lazy']); endif; ?></figure><div class="services-onsite__copy"><h2 id="services-onsite-title"><?php echo esc_html((string) ($s['title'] ?? '')); ?></h2><?php echo wp_kses_post((string) ($s['description'] ?? '')); ?><?php if (!empty($s['list_label'])) : ?><p><?php echo esc_html($s['list_label']); ?></p><?php endif; ?><?php $this->render_list((array) ($s['items'] ?? []), 'services-onsite__list'); ?><?php if (!empty($s['result'])) : ?><p class="services-onsite__result"><?php echo esc_html($s['result']); ?></p><?php endif; ?></div></div></section>
        <?php endif;
    }
}

final class Service_Assurance_Widget extends Service_Widget_Base {
    public function get_name(): string { return 'xinzhou-service-assurance'; }
    public function get_title(): string { return 'Xinzhou Service Assurance'; }
    public function get_icon(): string { return 'eicon-check-circle'; }

    protected function register_controls(): void {
        $this->start_controls_section('content', ['label' => 'Assurance Items']);
        $r = new Repeater();
        $r->add_control('title', ['label' => 'Title', 'type' => Controls_Manager::TEXTAREA]);
        $r->add_control('description', ['label' => 'Description', 'type' => Controls_Manager::WYSIWYG]);
        $this->add_control('items', ['label' => 'Items', 'type' => Controls_Manager::REPEATER, 'fields' => $r->get_controls(), 'title_field' => '{{{ title }}}']);
        $this->end_controls_section();
    }

    protected function render(): void {
        $s = $this->get_settings_for_display();
        ?><section class="services-assurance-section" aria-label="Online support and warranty"><div class="xz-container services-assurance__grid"><?php foreach ((array) ($s['items'] ?? []) as $item) : ?><article class="services-assurance__item"><h2><?php echo esc_html((string) ($item['title'] ?? '')); ?></h2><?php echo wp_kses_post((string) ($item['description'] ?? '')); ?></article><?php endforeach; ?></div></section><?php
    }
}

function register_service_widgets($widgets_manager): void {
    $widgets_manager->register(new Service_Content_Widget());
    $widgets_manager->register(new Service_Assurance_Widget());
}

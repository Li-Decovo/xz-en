<?php

namespace Xinzhou\Elementor;

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Repeater;

if (!defined('ABSPATH')) { exit; }

abstract class Contact_Widget_Base extends Xinzhou_Section_Widget {
    public function get_style_depends(): array {
        return ['xinzhou-content', 'xinzhou-contact-widgets'];
    }
}

function contact_icon(string $name): string {
    $icons = [
        'email' => '<rect width="20" height="16" x="2" y="4" rx="2" stroke="currentColor" stroke-width="2"></rect><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>',
        'phone' => '<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.79 19.79 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.12.9.33 1.78.62 2.62a2 2 0 0 1-.45 2.11L8 9.73a16 16 0 0 0 6 6l1.28-1.28a2 2 0 0 1 2.11-.45c.84.29 1.72.5 2.62.62A2 2 0 0 1 22 16.92Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>',
        'location' => '<path d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path><circle cx="12" cy="10" r="3" stroke="currentColor" stroke-width="2"></circle>',
        'file' => '<path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path><path d="M14 2v6h6M8 13h8M8 17h8M8 9h2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>',
        'workflow' => '<rect x="16" y="16" width="6" height="6" rx="1" stroke="currentColor" stroke-width="2"></rect><rect x="2" y="16" width="6" height="6" rx="1" stroke="currentColor" stroke-width="2"></rect><rect x="9" y="2" width="6" height="6" rx="1" stroke="currentColor" stroke-width="2"></rect><path d="M5 16v-3a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v3M12 8v3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>',
        'message' => '<path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v8Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path><path d="M8 10h.01M12 10h.01M16 10h.01" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"></path>',
    ];
    return '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true">' . ($icons[$name] ?? $icons['message']) . '</svg>';
}

function contact_social_icon(string $name): string {
    $paths = [
        'linkedin' => 'M4.98 3.5C4.98 4.88 3.86 6 2.48 6S0 4.88 0 3.5 1.12 1 2.48 1s2.5 1.12 2.5 2.5ZM.5 8h4V24h-4V8Zm7 0h3.83v2.19h.05c.53-1.01 1.84-2.08 3.79-2.08 4.05 0 4.8 2.67 4.8 6.14V24h-4v-6.91c0-1.65-.03-3.76-2.29-3.76-2.29 0-2.64 1.79-2.64 3.64V24h-4V8Z',
        'facebook' => 'M22 12a10 10 0 1 0-11.56 9.88v-6.99H7.9V12h2.54V9.8c0-2.5 1.49-3.89 3.77-3.89 1.09 0 2.23.2 2.23.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56V12h2.77l-.44 2.89h-2.33v6.99A10 10 0 0 0 22 12Z',
        'tiktok' => 'M19.59 6.69A4.83 4.83 0 0 1 16 5.13V16.3a5.3 5.3 0 1 1-5.3-5.3c.4 0 .79.04 1.17.13v2.73a2.73 2.73 0 1 0 1.56 2.47V0h2.61a4.83 4.83 0 0 0 4.84 4.84v1.85c-.45 0-.88-.04-1.29-.1Z',
    ];
    return '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="' . esc_attr($paths[$name] ?? $paths['linkedin']) . '"></path></svg>';
}

final class Contact_Hero_Widget extends Contact_Widget_Base {
    public function get_name(): string { return 'xinzhou-contact-hero'; }
    public function get_title(): string { return 'Xinzhou Contact Hero'; }
    public function get_icon(): string { return 'eicon-image-box'; }

    protected function register_controls(): void {
        $this->start_controls_section('content', ['label' => 'Content']);
        $this->add_control('title', ['label' => 'Title', 'type' => Controls_Manager::TEXTAREA, 'default' => 'Discuss Your Welding Equipment Project with Our Team']);
        $this->add_control('description', ['label' => 'Description', 'type' => Controls_Manager::TEXTAREA, 'default' => 'Share your finished product, production capacity, factory layout and technical requirements. Our sales and engineering teams will help define the next practical step.']);
        $this->add_control('image', ['label' => 'Image', 'type' => Controls_Manager::MEDIA]);
        $this->end_controls_section();
    }

    protected function render(): void {
        $s = $this->get_settings_for_display(); $image = $this->image_url((array) ($s['image'] ?? []));
        ?><section class="contact-hero" aria-labelledby="contact-page-title"><div class="xz-container contact-hero__grid"><div class="contact-hero__copy"><nav class="contact-breadcrumb" aria-label="Breadcrumb"><a href="<?php echo esc_url(home_url('/')); ?>">Home</a><span>/</span><span>Contact</span></nav><h1 id="contact-page-title"><?php echo esc_html((string) ($s['title'] ?? '')); ?></h1><p><?php echo esc_html((string) ($s['description'] ?? '')); ?></p></div><?php if ($image) : ?><figure class="contact-hero__media"><img src="<?php echo esc_url($image); ?>" alt="Xinzhou welding equipment manufacturing workshop"></figure><?php endif; ?></div></section><?php
    }
}

final class Contact_Inquiry_Widget extends Contact_Widget_Base {
    public function get_name(): string { return 'xinzhou-contact-inquiry'; }
    public function get_title(): string { return 'Xinzhou Contact Inquiry'; }
    public function get_icon(): string { return 'eicon-form-horizontal'; }

    protected function register_controls(): void {
        $this->start_controls_section('information', ['label' => 'Contact Information']);
        foreach (['eyebrow' => ['Section Label', 'Start a Conversation'], 'title' => ['Title', 'Contact Our Sales Team'], 'intro' => ['Introduction', 'Tell us what you plan to produce and the support you need. More complete product and factory information helps our team prepare a more accurate response.'], 'email' => ['Email', 'xinzhou@weldercn.com'], 'phone' => ['Phone', '+86 180 6723 1686'], 'office' => ['Head Office', 'Ningbo, Zhejiang, China'], 'note_title' => ['Note Title', 'Project Information'], 'note_copy' => ['Note Description', 'Product drawings, sample photos, material specifications and target output are helpful for technical evaluation.']] as $key => $definition) {
            $this->add_control($key, ['label' => $definition[0], 'type' => in_array($key, ['intro', 'note_copy'], true) ? Controls_Manager::TEXTAREA : Controls_Manager::TEXT, 'default' => $definition[1]]);
        }
        $this->add_control('linkedin', ['label' => 'LinkedIn URL', 'type' => Controls_Manager::URL, 'default' => ['url' => 'https://www.linkedin.com/company/xinzhouwelding']]);
        $this->add_control('facebook', ['label' => 'Facebook URL', 'type' => Controls_Manager::URL, 'default' => ['url' => 'https://www.facebook.com/xinzhouwelder']]);
        $this->add_control('tiktok', ['label' => 'TikTok URL', 'type' => Controls_Manager::URL, 'default' => ['url' => 'https://www.tiktok.com/@xinzhouwelder']]);
        $this->end_controls_section();
        $this->start_controls_section('form', ['label' => 'Inquiry Form']);
        $this->add_control('form_label', ['label' => 'Form Label', 'type' => Controls_Manager::TEXT, 'default' => 'Equipment Inquiry']);
        $this->add_control('form_title', ['label' => 'Form Title', 'type' => Controls_Manager::TEXT, 'default' => 'Tell Us What You Need to Produce']);
        $this->add_control('form_id', ['label' => 'Fluent Form ID', 'type' => Controls_Manager::NUMBER, 'default' => 1, 'min' => 1]);
        $this->end_controls_section();
    }

    protected function render(): void {
        $s = $this->get_settings_for_display();
        $email = sanitize_email((string) ($s['email'] ?? '')); $phone = (string) ($s['phone'] ?? '');
        ?>
        <section class="contact-inquiry" id="inquiry" aria-labelledby="contact-inquiry-title"><div class="xz-container contact-inquiry__grid"><div class="contact-information"><p class="contact-eyebrow"><?php echo esc_html((string) ($s['eyebrow'] ?? '')); ?></p><h2 id="contact-inquiry-title"><?php echo esc_html((string) ($s['title'] ?? '')); ?></h2><p class="contact-information__intro"><?php echo esc_html((string) ($s['intro'] ?? '')); ?></p><div class="contact-information__list">
            <article><span><?php echo contact_icon('email'); ?></span><div><h3>Email</h3><a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a></div></article>
            <article><span><?php echo contact_icon('phone'); ?></span><div><h3>Phone</h3><a href="tel:<?php echo esc_attr(preg_replace('/[^+\d]/', '', $phone)); ?>"><?php echo esc_html($phone); ?></a></div></article>
            <article><span><?php echo contact_icon('location'); ?></span><div><h3>Head Office</h3><p><?php echo esc_html((string) ($s['office'] ?? '')); ?></p></div></article>
        </div><div class="contact-information__note"><strong><?php echo esc_html((string) ($s['note_title'] ?? '')); ?></strong><p><?php echo esc_html((string) ($s['note_copy'] ?? '')); ?></p></div><div class="contact-social" aria-label="Xinzhou social media"><p>Follow Xinzhou</p><div><?php foreach (['linkedin', 'facebook', 'tiktok'] as $network) : $url = $this->link_url((array) ($s[$network] ?? [])); if (!$url) { continue; } ?><a href="<?php echo esc_url($url); ?>" target="_blank" rel="noreferrer" aria-label="Xinzhou on <?php echo esc_attr(ucfirst($network)); ?>"><?php echo contact_social_icon($network); ?></a><?php endforeach; ?></div></div></div>
        <div class="contact-form-panel"><p class="contact-form-panel__label"><?php echo esc_html((string) ($s['form_label'] ?? '')); ?></p><h2><?php echo esc_html((string) ($s['form_title'] ?? '')); ?></h2><div class="xz-contact-form-widget"><?php echo do_shortcode('[fluentform id="' . absint($s['form_id'] ?? 1) . '"]'); ?></div></div></div></section>
        <?php
    }
}

final class Contact_Process_Widget extends Contact_Widget_Base {
    public function get_name(): string { return 'xinzhou-contact-process'; }
    public function get_title(): string { return 'Xinzhou Contact Process'; }
    public function get_icon(): string { return 'eicon-flow'; }

    protected function register_controls(): void {
        $this->start_controls_section('content', ['label' => 'Content']);
        $this->add_control('eyebrow', ['label' => 'Section Label', 'type' => Controls_Manager::TEXT, 'default' => 'How We Support']);
        $this->add_control('title', ['label' => 'Title', 'type' => Controls_Manager::TEXT, 'default' => 'A Clear Start for Your Equipment Project']);
        $repeater = new Repeater();
        $repeater->add_control('icon', ['label' => 'Icon', 'type' => Controls_Manager::ICONS, 'default' => ['value' => 'far fa-file-alt', 'library' => 'fa-regular']]);
        $repeater->add_control('title', ['label' => 'Title', 'type' => Controls_Manager::TEXT]);
        $repeater->add_control('description', ['label' => 'Description', 'type' => Controls_Manager::TEXTAREA]);
        $this->add_control('steps', ['label' => 'Steps', 'type' => Controls_Manager::REPEATER, 'fields' => $repeater->get_controls(), 'default' => [
            ['icon' => ['value' => 'far fa-file-alt', 'library' => 'fa-regular'], 'title' => 'Share Your Requirement', 'description' => 'Send the finished product, specifications, target output and available factory information.'],
            ['icon' => ['value' => 'fas fa-sitemap', 'library' => 'fa-solid'], 'title' => 'Plan the Right Solution', 'description' => 'Our sales and engineering teams review machine selection, configuration and production line layout.'],
            ['icon' => ['value' => 'far fa-comments', 'library' => 'fa-regular'], 'title' => 'Continue Technical Discussion', 'description' => 'Confirm specifications, quotation, layout and service requirements before the project moves forward.'],
        ]]);
        $this->end_controls_section();
    }

    protected function render(): void {
        $s = $this->get_settings_for_display(); ?>
        <section class="contact-process" aria-labelledby="contact-process-title"><div class="xz-container"><div class="contact-process__head"><p class="contact-eyebrow"><?php echo esc_html((string) ($s['eyebrow'] ?? '')); ?></p><h2 id="contact-process-title"><?php echo esc_html((string) ($s['title'] ?? '')); ?></h2></div><div class="contact-process__grid"><?php foreach ((array) ($s['steps'] ?? []) as $step) : ?><article><span><?php if (is_array($step['icon'] ?? null)) { Icons_Manager::render_icon($step['icon'], ['aria-hidden' => 'true']); } else { echo contact_icon((string) ($step['icon'] ?? 'file')); } ?></span><h3><?php echo esc_html(wp_strip_all_tags((string) ($step['title'] ?? ''))); ?></h3><p><?php echo esc_html(wp_strip_all_tags((string) ($step['description'] ?? ''))); ?></p></article><?php endforeach; ?></div></div></section><?php
    }
}

final class Contact_Location_Widget extends Contact_Widget_Base {
    public function get_name(): string { return 'xinzhou-contact-location'; }
    public function get_title(): string { return 'Xinzhou Contact Location'; }
    public function get_icon(): string { return 'eicon-google-maps'; }

    protected function register_controls(): void {
        $this->start_controls_section('content', ['label' => 'Content']);
        $this->add_control('image', ['label' => 'Image', 'type' => Controls_Manager::MEDIA]);
        $this->add_control('eyebrow', ['label' => 'Section Label', 'type' => Controls_Manager::TEXT, 'default' => 'Xinzhou Manufacturing Base']);
        $this->add_control('title', ['label' => 'Title', 'type' => Controls_Manager::TEXTAREA, 'default' => 'Connected to Manufacturing, Engineering and Global Service']);
        $this->add_control('description', ['label' => 'Description', 'type' => Controls_Manager::TEXTAREA, 'default' => 'Xinzhou operates manufacturing facilities in Ningbo and Jiaxing, supported by experienced sales, engineering, assembly and after-sales teams.']);
        $this->add_control('address', ['label' => 'Address', 'type' => Controls_Manager::TEXT, 'default' => 'Ningbo, Zhejiang, China']);
        $this->end_controls_section();
    }

    protected function render(): void {
        $s = $this->get_settings_for_display(); $image = $this->image_url((array) ($s['image'] ?? [])); ?>
        <section class="contact-location" aria-labelledby="contact-location-title"><div class="contact-location__grid"><?php if ($image) : ?><figure><img src="<?php echo esc_url($image); ?>" alt="Ningbo Xinzhou Welding Equipment manufacturing base"></figure><?php endif; ?><div><p class="contact-eyebrow"><?php echo esc_html((string) ($s['eyebrow'] ?? '')); ?></p><h2 id="contact-location-title"><?php echo esc_html((string) ($s['title'] ?? '')); ?></h2><p><?php echo esc_html((string) ($s['description'] ?? '')); ?></p><address><?php echo contact_icon('location'); ?><span><?php echo esc_html((string) ($s['address'] ?? '')); ?></span></address></div></div></section><?php
    }
}

function register_contact_widgets($widgets_manager): void {
    $widgets_manager->register(new Contact_Hero_Widget());
    $widgets_manager->register(new Contact_Inquiry_Widget());
    $widgets_manager->register(new Contact_Process_Widget());
    $widgets_manager->register(new Contact_Location_Widget());
}

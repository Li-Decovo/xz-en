<?php

namespace Xinzhou\Elementor;

use Elementor\Controls_Manager;

if (!defined('ABSPATH')) { exit; }

abstract class Global_Widget_Base extends Xinzhou_Section_Widget {
    public function get_style_depends(): array {
        return ['xinzhou-content', 'xinzhou-global-widgets'];
    }
}

final class Global_Prefooter_Widget extends Global_Widget_Base {
    public function get_name(): string { return 'xinzhou-global-prefooter'; }
    public function get_title(): string { return 'Xinzhou Global Pre-Footer'; }
    public function get_icon(): string { return 'eicon-footer'; }

    protected function register_controls(): void {
        $this->start_controls_section('subscribe', ['label' => 'Subscription']);
        $this->add_control('subscribe_title', ['label' => 'Title', 'type' => Controls_Manager::TEXT, 'default' => 'Subscribe to Our Updates']);
        $this->add_control('subscribe_copy', ['label' => 'Description', 'type' => Controls_Manager::TEXTAREA, 'default' => 'Receive product updates, exhibition news and automation insights from Xinzhou.']);
        $this->add_control('subscribe_form_id', ['label' => 'Fluent Form ID', 'type' => Controls_Manager::NUMBER, 'default' => 2, 'min' => 1]);
        $this->end_controls_section();

        $this->start_controls_section('sales', ['label' => 'Sales']);
        $this->add_control('sales_title', ['label' => 'Title', 'type' => Controls_Manager::TEXT, 'default' => 'Sales & Project Team']);
        $this->add_control('sales_copy', ['label' => 'Description', 'type' => Controls_Manager::TEXTAREA, 'default' => 'Tell us your product size, output target and factory layout. Our engineers will match a practical welding line plan for your production.']);
        $this->add_control('sales_button_text', ['label' => 'Button Text', 'type' => Controls_Manager::TEXT, 'default' => 'Find Now']);
        $this->end_controls_section();

        $this->start_controls_section('support', ['label' => 'Technical Support']);
        $this->add_control('support_title', ['label' => 'Title', 'type' => Controls_Manager::TEXT, 'default' => 'Technical Support']);
        $this->add_control('support_copy', ['label' => 'Description', 'type' => Controls_Manager::TEXTAREA, 'default' => 'Need help with line configuration, commissioning or after-sales service? Connect with Xinzhou for reliable technical assistance.']);
        $this->add_control('support_button_text', ['label' => 'Button Text', 'type' => Controls_Manager::TEXT, 'default' => 'Find Out More']);
        $this->add_control('support_button_link', ['label' => 'Button Link', 'type' => Controls_Manager::URL, 'default' => ['url' => '/services/']]);
        $this->end_controls_section();

        $this->start_controls_section('highlight', ['label' => 'Highlight']);
        $this->add_control('highlight_title', ['label' => 'Title', 'type' => Controls_Manager::TEXT, 'default' => 'Share Your Requirement']);
        $this->add_control('highlight_copy', ['label' => 'Description', 'type' => Controls_Manager::TEXTAREA, 'default' => 'From steel grating and reinforcing mesh to custom resistance welding automation, Xinzhou builds solutions around real production needs.']);
        $this->end_controls_section();
    }

    protected function render(): void {
        $s = $this->get_settings_for_display();
        $support_url = $this->link_url((array) ($s['support_button_link'] ?? [])) ?: home_url('/services/');
        ?>
        <section class="inquiry-strip-section" aria-label="Xinzhou inquiry and support"><div class="inquiry-strip-container inquiry-strip-grid">
            <article class="inquiry-strip-card"><h2 class="inquiry-strip-title"><?php echo esc_html((string) ($s['subscribe_title'] ?? '')); ?></h2><div class="inquiry-strip-form xz-global-subscribe-form"><?php echo do_shortcode('[fluentform id="' . absint($s['subscribe_form_id'] ?? 2) . '"]'); ?></div><p><?php echo esc_html((string) ($s['subscribe_copy'] ?? '')); ?></p></article>
            <article class="inquiry-strip-card"><h2 class="inquiry-strip-title"><?php echo esc_html((string) ($s['sales_title'] ?? '')); ?></h2><p><?php echo esc_html((string) ($s['sales_copy'] ?? '')); ?></p><a class="inquiry-strip-button" href="#inquiry" data-inquiry-open><?php echo esc_html((string) ($s['sales_button_text'] ?? '')); ?></a></article>
            <article class="inquiry-strip-card"><h2 class="inquiry-strip-title"><?php echo esc_html((string) ($s['support_title'] ?? '')); ?></h2><p><?php echo esc_html((string) ($s['support_copy'] ?? '')); ?></p><a class="inquiry-strip-button" href="<?php echo esc_url($support_url); ?>"><?php echo esc_html((string) ($s['support_button_text'] ?? '')); ?></a></article>
            <article class="inquiry-strip-card inquiry-strip-card--highlight"><h2 class="inquiry-strip-title"><?php echo esc_html((string) ($s['highlight_title'] ?? '')); ?></h2><p><?php echo esc_html((string) ($s['highlight_copy'] ?? '')); ?></p></article>
        </div></section>
        <?php
    }
}

final class Global_Inquiry_Modal_Widget extends Global_Widget_Base {
    public function get_name(): string { return 'xinzhou-global-inquiry-modal'; }
    public function get_title(): string { return 'Xinzhou Inquiry Modal'; }
    public function get_icon(): string { return 'eicon-form-vertical'; }

    public function get_script_depends(): array {
        return ['xinzhou-content'];
    }

    protected function register_controls(): void {
        $this->start_controls_section('content', ['label' => 'Inquiry Modal']);
        $this->add_control('label', ['label' => 'Label', 'type' => Controls_Manager::TEXT, 'default' => 'Equipment Inquiry']);
        $this->add_control('title', ['label' => 'Title', 'type' => Controls_Manager::TEXT, 'default' => 'Get Your Line Proposal']);
        $this->add_control('form_id', ['label' => 'Fluent Form ID', 'type' => Controls_Manager::NUMBER, 'default' => 1, 'min' => 1]);
        $this->end_controls_section();
    }

    protected function render(): void {
        if (is_page(24) || is_page('contact')) {
            return;
        }

        $s = $this->get_settings_for_display();
        ?>
        <dialog class="xz-inquiry-dialog" aria-labelledby="xz-inquiry-title"><div class="xz-inquiry-dialog__head"><p class="xz-inquiry-dialog__label"><?php echo esc_html((string) ($s['label'] ?? '')); ?></p><h2 id="xz-inquiry-title"><?php echo esc_html((string) ($s['title'] ?? '')); ?></h2><button class="xz-inquiry-dialog__close" type="button" aria-label="Close inquiry form" data-inquiry-close><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path></svg></button></div><div class="xz-inquiry-dialog__form"><?php echo do_shortcode('[fluentform id="' . absint($s['form_id'] ?? 1) . '"]'); ?></div></dialog>
        <?php
    }
}

function register_global_widgets($widgets_manager): void {
    $widgets_manager->register(new Global_Prefooter_Widget());
    $widgets_manager->register(new Global_Inquiry_Modal_Widget());
}

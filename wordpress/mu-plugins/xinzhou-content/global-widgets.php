<?php

namespace Xinzhou\Elementor;

use Elementor\Controls_Manager;

if (!defined('ABSPATH')) { exit; }

abstract class Global_Widget_Base extends Xinzhou_Section_Widget {
    public function get_style_depends(): array {
        return ['xinzhou-content', 'xinzhou-global-widgets'];
    }
}

function global_menu_options(): array {
    $options = ['' => 'Automatic'];
    foreach (wp_get_nav_menus() as $menu) {
        $options[(string) $menu->term_id] = $menu->name;
    }
    return $options;
}

function global_menu_items(string $menu_id = ''): array {
    $menu = $menu_id ? wp_get_nav_menu_object((int) $menu_id) : null;
    if (!$menu) {
        $menus = wp_get_nav_menus();
        $menu = $menus[0] ?? null;
    }
    if (!$menu) { return []; }
    return array_values(array_filter((array) wp_get_nav_menu_items($menu->term_id), static function ($item): bool {
        return (int) $item->menu_item_parent === 0;
    }));
}

function global_social_icon(string $network): string {
    $icons = [
        'linkedin' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6.5 8.2H3.2V21h3.3V8.2ZM4.8 3A1.9 1.9 0 1 0 4.8 6.8 1.9 1.9 0 0 0 4.8 3ZM21 13.7c0-3.9-2.1-5.8-4.9-5.8-2.3 0-3.3 1.3-3.8 2.1V8.2H9V21h3.3v-6.3c0-1.7.3-3.3 2.4-3.3 2 0 2.1 1.9 2.1 3.4V21H20l1-7.3Z"/></svg>',
        'facebook' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M22 12a10 10 0 1 0-11.6 9.9v-7H7.9V12h2.5V9.8c0-2.5 1.5-3.9 3.8-3.9 1.1 0 2.2.2 2.2.2v2.5h-1.2c-1.3 0-1.7.8-1.7 1.6V12h2.8l-.5 2.9h-2.3v7A10 10 0 0 0 22 12Z"/></svg>',
        'tiktok' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19.6 6.7A4.8 4.8 0 0 1 16 5.1v11.2a5.3 5.3 0 1 1-4.1-5.2v2.8a2.7 2.7 0 1 0 1.5 2.4V0H16a4.8 4.8 0 0 0 4.9 4.8v1.9h-1.3Z"/></svg>',
    ];
    return $icons[$network] ?? '';
}

final class Global_Header_Widget extends Global_Widget_Base {
    public function get_name(): string { return 'xinzhou-global-header'; }
    public function get_title(): string { return 'Xinzhou Page Header'; }
    public function get_icon(): string { return 'eicon-header'; }
    public function get_style_depends(): array { return ['xinzhou-page-chrome']; }
    public function get_script_depends(): array { return ['xinzhou-content']; }

    protected function register_controls(): void {
        $this->start_controls_section('brand', ['label' => 'Brand & Navigation']);
        $this->add_control('logo', ['label' => 'Logo', 'type' => Controls_Manager::MEDIA, 'default' => ['url' => home_url('/wp-content/uploads/xinzhou-home-assets/site-logo.webp')]]);
        $this->add_control('menu_id', ['label' => 'WordPress Menu', 'type' => Controls_Manager::SELECT, 'options' => global_menu_options(), 'default' => '']);
        $this->end_controls_section();
        $this->start_controls_section('header_inquiry', ['label' => 'Header Inquiry Button']);
        $this->add_control('cta_text', ['label' => 'Button Text', 'type' => Controls_Manager::TEXT, 'default' => 'Get Your Line Proposal']);
        $this->end_controls_section();
        $this->start_controls_section('social', ['label' => 'Social Media']);
        $this->add_control('social_label', ['label' => 'Label', 'type' => Controls_Manager::TEXT, 'default' => 'Follow Xinzhou']);
        $this->add_control('linkedin', ['label' => 'LinkedIn URL', 'type' => Controls_Manager::URL, 'default' => ['url' => 'https://www.linkedin.com/']]);
        $this->add_control('facebook', ['label' => 'Facebook URL', 'type' => Controls_Manager::URL, 'default' => ['url' => 'https://www.facebook.com/']]);
        $this->add_control('tiktok', ['label' => 'TikTok URL', 'type' => Controls_Manager::URL, 'default' => ['url' => 'https://www.tiktok.com/@xinzhouwelder']]);
        $this->end_controls_section();
        $this->start_controls_section('mega', ['label' => 'Products Mega Menu']);
        $this->add_control('mega_cta_title', ['label' => 'Inquiry Card Title', 'type' => Controls_Manager::TEXT, 'default' => 'Discuss Your Project']);
        $this->add_control('mega_cta_copy', ['label' => 'Inquiry Card Text', 'type' => Controls_Manager::TEXTAREA, 'default' => 'Share your product, output and factory requirements with Xinzhou.']);
        $this->end_controls_section();
    }

    protected function render(): void {
        $s = $this->get_settings_for_display();
        $items = global_menu_items((string) ($s['menu_id'] ?? ''));
        $logo_id = (int) ($s['logo']['id'] ?? 0);
        $logo_url = $this->image_url((array) ($s['logo'] ?? []));
        $terms = get_terms(['taxonomy' => 'product_category', 'hide_empty' => false]);
        if (is_wp_error($terms)) { $terms = []; }
        usort($terms, static function (\WP_Term $a, \WP_Term $b): int { return ((int) get_term_meta($a->term_id, 'category_display_order', true)) <=> ((int) get_term_meta($b->term_id, 'category_display_order', true)); });
        $current_path = trim((string) wp_parse_url(wp_unslash($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH), '/');
        $is_active = static function ($item) use ($current_path): bool {
            $path = trim((string) wp_parse_url((string) $item->url, PHP_URL_PATH), '/');
            if ($path === '') { return $current_path === ''; }
            return $current_path === $path || str_starts_with($current_path . '/', $path . '/');
        };
        ?>
        <div class="xz-page-header-shell">
            <div class="xz-page-topbar"><div class="xz-page-topbar__inner"><span><?php echo esc_html((string) ($s['social_label'] ?? '')); ?></span><?php foreach (['linkedin', 'facebook', 'tiktok'] as $network) : $url = $this->link_url((array) ($s[$network] ?? [])); if ($url) : ?><a href="<?php echo esc_url($url); ?>" target="_blank" rel="noreferrer" aria-label="Xinzhou on <?php echo esc_attr(ucfirst($network)); ?>"><?php echo global_social_icon($network); ?></a><?php endif; endforeach; ?></div></div>
            <header class="xz-page-header"><div class="xz-page-header__inner">
                <a class="xz-page-header__brand" href="<?php echo esc_url(home_url('/')); ?>"><?php if ($logo_id) : echo wp_get_attachment_image($logo_id, 'full'); elseif ($logo_url) : echo \xz_media_image($logo_url); endif; ?></a>
                <nav class="xz-page-header__nav" aria-label="Primary navigation"><?php foreach ($items as $item) : $products = str_contains(strtolower((string) $item->title), 'product'); $active = $is_active($item); if ($products) : ?><div class="xz-page-header__products"><a class="<?php echo $active ? 'is-active' : ''; ?>" href="<?php echo esc_url($item->url); ?>"<?php echo $active ? ' aria-current="page"' : ''; ?>><?php echo esc_html($item->title); ?></a><div class="xz-page-header__mega" aria-label="Product categories"><?php foreach ($terms as $term) : $image_id = \xz_product_term_image((int) $term->term_id); ?><a class="xz-page-header__mega-card" href="<?php echo esc_url(get_term_link($term)); ?>"><?php echo $image_id ? wp_get_attachment_image($image_id, 'medium_large', false, ['loading' => 'eager']) : ''; ?><span><?php echo esc_html($term->name); ?></span></a><?php endforeach; ?><a class="xz-page-header__mega-card xz-page-header__mega-card--contact" href="#inquiry" data-inquiry-open><span><?php echo esc_html((string) ($s['mega_cta_title'] ?? '')); ?></span><small><?php echo esc_html((string) ($s['mega_cta_copy'] ?? '')); ?></small></a></div></div><?php else : ?><a class="<?php echo $active ? 'is-active' : ''; ?>" href="<?php echo esc_url($item->url); ?>"<?php echo $active ? ' aria-current="page"' : ''; ?>><?php echo esc_html($item->title); ?></a><?php endif; endforeach; ?></nav>
                <a class="xz-page-header__cta" href="#inquiry" data-inquiry-open><?php echo esc_html((string) ($s['cta_text'] ?? '')); ?></a>
                <button class="xz-page-header__toggle" type="button" aria-label="Open menu" aria-expanded="false" data-xz-page-menu-toggle><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg></button>
            </div></header>
            <div class="xz-page-mobile-menu" data-xz-page-mobile-menu><nav><?php foreach ($items as $item) : ?><a href="<?php echo esc_url($item->url); ?>"><?php echo esc_html($item->title); ?></a><?php endforeach; ?></nav></div>
        </div>
        <?php
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

final class Global_Main_Footer_Widget extends Global_Widget_Base {
    public function get_name(): string { return 'xinzhou-global-main-footer'; }
    public function get_title(): string { return 'Xinzhou Global Footer'; }
    public function get_icon(): string { return 'eicon-footer'; }
    public function get_style_depends(): array { return ['xinzhou-page-chrome']; }
    public function get_script_depends(): array { return ['xinzhou-content']; }

    protected function register_controls(): void {
        $this->start_controls_section('brand', ['label' => 'Company']);
        $this->add_control('logo', ['label' => 'Logo', 'type' => Controls_Manager::MEDIA, 'default' => ['url' => home_url('/wp-content/uploads/xinzhou-home-assets/site-logo.webp')]]);
        $this->add_control('brand_copy', ['label' => 'Company Text', 'type' => Controls_Manager::TEXTAREA, 'default' => 'Xinzhou provides automated resistance welding equipment and complete production line solutions, supported by engineering, manufacturing and global technical service.']);
        $this->add_control('inquiry_button', ['label' => 'Inquiry Button Text', 'type' => Controls_Manager::TEXT, 'default' => 'Send an Inquiry']);
        $this->end_controls_section();

        $this->start_controls_section('navigation', ['label' => 'Navigation']);
        $this->add_control('menu_id', ['label' => 'WordPress Menu', 'type' => Controls_Manager::SELECT, 'options' => global_menu_options(), 'default' => '']);
        $this->add_control('menu_title', ['label' => 'Menu Heading', 'type' => Controls_Manager::TEXT, 'default' => 'Main Menu']);
        $this->add_control('products_title', ['label' => 'Products Heading', 'type' => Controls_Manager::TEXT, 'default' => 'Product Categories']);
        $this->end_controls_section();

        $this->start_controls_section('contact', ['label' => 'Contact Information']);
        $this->add_control('contact_title', ['label' => 'Heading', 'type' => Controls_Manager::TEXT, 'default' => 'Contact Xinzhou']);
        $this->add_control('email', ['label' => 'Email', 'type' => Controls_Manager::TEXT, 'default' => 'xinzhou@weldercn.com']);
        $this->add_control('phone', ['label' => 'Phone', 'type' => Controls_Manager::TEXT, 'default' => '+86 180 6723 1686']);
        $this->add_control('address', ['label' => 'Address', 'type' => Controls_Manager::TEXTAREA, 'default' => 'Ningbo, Zhejiang, China']);
        $this->add_control('whatsapp', ['label' => 'WhatsApp', 'type' => Controls_Manager::TEXT, 'default' => '+86 574 82566933']);
        $this->add_control('copyright', ['label' => 'Copyright', 'type' => Controls_Manager::TEXT, 'default' => 'Copyright © Xinzhou Welding Equipment. All rights reserved.']);
        $this->end_controls_section();

        $this->start_controls_section('social', ['label' => 'Social Media']);
        $this->add_control('linkedin', ['label' => 'LinkedIn URL', 'type' => Controls_Manager::URL, 'default' => ['url' => 'https://www.linkedin.com/']]);
        $this->add_control('facebook', ['label' => 'Facebook URL', 'type' => Controls_Manager::URL, 'default' => ['url' => 'https://www.facebook.com/']]);
        $this->add_control('tiktok', ['label' => 'TikTok URL', 'type' => Controls_Manager::URL, 'default' => ['url' => 'https://www.tiktok.com/@xinzhouwelder']]);
        $this->end_controls_section();
    }

    protected function render(): void {
        $s = $this->get_settings_for_display();
        $items = global_menu_items((string) ($s['menu_id'] ?? ''));
        $terms = get_terms(['taxonomy' => 'product_category', 'hide_empty' => false]);
        if (is_wp_error($terms)) { $terms = []; }
        usort($terms, static function (\WP_Term $a, \WP_Term $b): int { return ((int) get_term_meta($a->term_id, 'category_display_order', true)) <=> ((int) get_term_meta($b->term_id, 'category_display_order', true)); });
        $logo_id = (int) ($s['logo']['id'] ?? 0);
        $logo_url = $this->image_url((array) ($s['logo'] ?? []));
        ?>
        <section class="xz-page-footer"><div class="xz-page-footer__inner">
            <div class="xz-page-footer__brand"><?php if ($logo_id) : echo wp_get_attachment_image($logo_id, 'full'); elseif ($logo_url) : echo \xz_media_image($logo_url); endif; ?><p><?php echo esc_html((string) ($s['brand_copy'] ?? '')); ?></p><button type="button" data-inquiry-open><?php echo esc_html((string) ($s['inquiry_button'] ?? 'Send an Inquiry')); ?></button></div>
            <div><h2><?php echo esc_html((string) ($s['menu_title'] ?? '')); ?></h2><ul><?php foreach ($items as $item) : ?><li><a href="<?php echo esc_url($item->url); ?>"><?php echo esc_html($item->title); ?></a></li><?php endforeach; ?></ul></div>
            <div><h2><?php echo esc_html((string) ($s['products_title'] ?? '')); ?></h2><ul><?php foreach ($terms as $term) : ?><li><a href="<?php echo esc_url(get_term_link($term)); ?>"><?php echo esc_html($term->name); ?></a></li><?php endforeach; ?></ul></div>
            <div><h2><?php echo esc_html((string) ($s['contact_title'] ?? '')); ?></h2><ul class="xz-page-footer__contact"><li><?php echo esc_html((string) ($s['email'] ?? '')); ?></li><li><?php echo esc_html((string) ($s['phone'] ?? '')); ?></li><li><?php echo esc_html((string) ($s['address'] ?? '')); ?></li><li><?php echo esc_html((string) ($s['whatsapp'] ?? '')); ?></li></ul><div class="xz-page-footer__socials"><?php foreach (['linkedin', 'facebook', 'tiktok'] as $network) : $url = $this->link_url((array) ($s[$network] ?? [])); if ($url) : ?><a href="<?php echo esc_url($url); ?>" target="_blank" rel="noreferrer" aria-label="Xinzhou on <?php echo esc_attr(ucfirst($network)); ?>"><?php echo global_social_icon($network); ?></a><?php endif; endforeach; ?></div></div>
        </div><div class="xz-page-footer__bottom"><p><?php echo esc_html((string) ($s['copyright'] ?? '')); ?></p></div></section>
        <?php
    }
}

final class Global_Footer_Widget extends Global_Widget_Base {
    public function get_name(): string { return 'xinzhou-global-footer-page'; }
    public function get_title(): string { return 'Xinzhou Page Footer'; }
    public function get_icon(): string { return 'eicon-footer'; }
    public function get_style_depends(): array { return ['xinzhou-page-chrome']; }
    public function get_script_depends(): array { return ['xinzhou-content']; }

    protected function register_controls(): void {
        $this->start_controls_section('prefooter', ['label' => 'Pre-Footer']);
        $this->add_control('subscribe_title', ['label' => 'Subscribe Title', 'type' => Controls_Manager::TEXT, 'default' => 'Subscribe to Our Updates']);
        $this->add_control('subscribe_copy', ['label' => 'Subscribe Text', 'type' => Controls_Manager::TEXTAREA, 'default' => 'Receive product updates, exhibition news and automation insights from Xinzhou.']);
        $this->add_control('subscribe_form_id', ['label' => 'Subscribe Fluent Form ID', 'type' => Controls_Manager::NUMBER, 'default' => 2, 'min' => 1]);
        $this->add_control('sales_title', ['label' => 'Sales Title', 'type' => Controls_Manager::TEXT, 'default' => 'Sales & Project Team']);
        $this->add_control('sales_copy', ['label' => 'Sales Text', 'type' => Controls_Manager::TEXTAREA, 'default' => 'Tell us your product size, output target and factory layout. Our engineers will match a practical welding line plan for your production.']);
        $this->add_control('sales_button', ['label' => 'Sales Button', 'type' => Controls_Manager::TEXT, 'default' => 'Find Now']);
        $this->add_control('support_title', ['label' => 'Support Title', 'type' => Controls_Manager::TEXT, 'default' => 'Technical Support']);
        $this->add_control('support_copy', ['label' => 'Support Text', 'type' => Controls_Manager::TEXTAREA, 'default' => 'Need help with line configuration, commissioning or after-sales service? Connect with Xinzhou for reliable technical assistance.']);
        $this->add_control('support_button', ['label' => 'Support Button', 'type' => Controls_Manager::TEXT, 'default' => 'Find Out More']);
        $this->add_control('support_link', ['label' => 'Support Link', 'type' => Controls_Manager::URL, 'default' => ['url' => '/services/']]);
        $this->add_control('highlight_title', ['label' => 'Highlight Title', 'type' => Controls_Manager::TEXT, 'default' => 'Share Your Requirement']);
        $this->add_control('highlight_copy', ['label' => 'Highlight Text', 'type' => Controls_Manager::TEXTAREA, 'default' => 'From steel grating and reinforcing mesh to custom resistance welding automation, Xinzhou builds solutions around real production needs.']);
        $this->end_controls_section();

        $this->start_controls_section('footer', ['label' => 'Main Footer']);
        $this->add_control('logo', ['label' => 'Logo', 'type' => Controls_Manager::MEDIA, 'default' => ['url' => home_url('/wp-content/uploads/xinzhou-home-assets/site-logo.webp')]]);
        $this->add_control('brand_copy', ['label' => 'Company Text', 'type' => Controls_Manager::TEXTAREA, 'default' => 'Xinzhou provides automated resistance welding equipment and complete production line solutions, supported by engineering, manufacturing and global technical service.']);
        $this->add_control('inquiry_button', ['label' => 'Inquiry Button', 'type' => Controls_Manager::TEXT, 'default' => 'Send an Inquiry']);
        $this->add_control('menu_id', ['label' => 'WordPress Menu', 'type' => Controls_Manager::SELECT, 'options' => global_menu_options(), 'default' => '']);
        $this->add_control('menu_title', ['label' => 'Menu Heading', 'type' => Controls_Manager::TEXT, 'default' => 'Main Menu']);
        $this->add_control('products_title', ['label' => 'Products Heading', 'type' => Controls_Manager::TEXT, 'default' => 'Product Categories']);
        $this->add_control('contact_title', ['label' => 'Contact Heading', 'type' => Controls_Manager::TEXT, 'default' => 'Contact Xinzhou']);
        $this->add_control('email', ['label' => 'Email', 'type' => Controls_Manager::TEXT, 'default' => 'xinzhou@weldercn.com']);
        $this->add_control('phone', ['label' => 'Phone', 'type' => Controls_Manager::TEXT, 'default' => '+86 180 6723 1686']);
        $this->add_control('address', ['label' => 'Address', 'type' => Controls_Manager::TEXTAREA, 'default' => 'Ningbo, Zhejiang, China']);
        $this->add_control('whatsapp', ['label' => 'WhatsApp', 'type' => Controls_Manager::TEXT, 'default' => '+86 574 82566933']);
        $this->add_control('copyright', ['label' => 'Copyright', 'type' => Controls_Manager::TEXT, 'default' => 'Copyright © Xinzhou Welding Equipment. All rights reserved.']);
        $this->end_controls_section();

        $this->start_controls_section('social', ['label' => 'Social Media']);
        $this->add_control('linkedin', ['label' => 'LinkedIn URL', 'type' => Controls_Manager::URL, 'default' => ['url' => 'https://www.linkedin.com/']]);
        $this->add_control('facebook', ['label' => 'Facebook URL', 'type' => Controls_Manager::URL, 'default' => ['url' => 'https://www.facebook.com/']]);
        $this->add_control('tiktok', ['label' => 'TikTok URL', 'type' => Controls_Manager::URL, 'default' => ['url' => 'https://www.tiktok.com/@xinzhouwelder']]);
        $this->end_controls_section();

        $this->start_controls_section('modal', ['label' => 'Inquiry Popup']);
        $this->add_control('modal_label', ['label' => 'Label', 'type' => Controls_Manager::TEXT, 'default' => 'Equipment Inquiry']);
        $this->add_control('modal_title', ['label' => 'Title', 'type' => Controls_Manager::TEXT, 'default' => 'Get Your Line Proposal']);
        $this->add_control('modal_form_id', ['label' => 'Fluent Form ID', 'type' => Controls_Manager::NUMBER, 'default' => 1, 'min' => 1]);
        $this->end_controls_section();
    }

    protected function render(): void {
        $s = $this->get_settings_for_display();
        $items = global_menu_items((string) ($s['menu_id'] ?? ''));
        $terms = get_terms(['taxonomy' => 'product_category', 'hide_empty' => false]);
        if (is_wp_error($terms)) { $terms = []; }
        usort($terms, static function (\WP_Term $a, \WP_Term $b): int { return ((int) get_term_meta($a->term_id, 'category_display_order', true)) <=> ((int) get_term_meta($b->term_id, 'category_display_order', true)); });
        $logo_id = (int) ($s['logo']['id'] ?? 0);
        $logo_url = $this->image_url((array) ($s['logo'] ?? []));
        $support_url = $this->link_url((array) ($s['support_link'] ?? [])) ?: home_url('/services/');
        ?>
        <footer class="xz-page-footer-shell">
            <section class="inquiry-strip-section" aria-label="Xinzhou inquiry and support"><div class="inquiry-strip-container inquiry-strip-grid">
                <article class="inquiry-strip-card"><h2 class="inquiry-strip-title"><?php echo esc_html((string) ($s['subscribe_title'] ?? '')); ?></h2><div class="inquiry-strip-form xz-global-subscribe-form"><?php echo do_shortcode('[fluentform id="' . absint($s['subscribe_form_id'] ?? 2) . '"]'); ?></div><p><?php echo esc_html((string) ($s['subscribe_copy'] ?? '')); ?></p></article>
                <article class="inquiry-strip-card"><h2 class="inquiry-strip-title"><?php echo esc_html((string) ($s['sales_title'] ?? '')); ?></h2><p><?php echo esc_html((string) ($s['sales_copy'] ?? '')); ?></p><a class="inquiry-strip-button" href="#inquiry" data-inquiry-open><?php echo esc_html((string) ($s['sales_button'] ?? '')); ?></a></article>
                <article class="inquiry-strip-card"><h2 class="inquiry-strip-title"><?php echo esc_html((string) ($s['support_title'] ?? '')); ?></h2><p><?php echo esc_html((string) ($s['support_copy'] ?? '')); ?></p><a class="inquiry-strip-button" href="<?php echo esc_url($support_url); ?>"><?php echo esc_html((string) ($s['support_button'] ?? '')); ?></a></article>
                <article class="inquiry-strip-card inquiry-strip-card--highlight"><h2 class="inquiry-strip-title"><?php echo esc_html((string) ($s['highlight_title'] ?? '')); ?></h2><p><?php echo esc_html((string) ($s['highlight_copy'] ?? '')); ?></p></article>
            </div></section>
            <section class="xz-page-footer"><div class="xz-page-footer__inner">
                <div class="xz-page-footer__brand"><?php if ($logo_id) : echo wp_get_attachment_image($logo_id, 'full'); elseif ($logo_url) : echo \xz_media_image($logo_url); endif; ?><p><?php echo esc_html((string) ($s['brand_copy'] ?? '')); ?></p><button type="button" data-inquiry-open><?php echo esc_html((string) ($s['inquiry_button'] ?? 'Send an Inquiry')); ?></button></div>
                <div><h2><?php echo esc_html((string) ($s['menu_title'] ?? '')); ?></h2><ul><?php foreach ($items as $item) : ?><li><a href="<?php echo esc_url($item->url); ?>"><?php echo esc_html($item->title); ?></a></li><?php endforeach; ?></ul></div>
                <div><h2><?php echo esc_html((string) ($s['products_title'] ?? '')); ?></h2><ul><?php foreach ($terms as $term) : ?><li><a href="<?php echo esc_url(get_term_link($term)); ?>"><?php echo esc_html($term->name); ?></a></li><?php endforeach; ?></ul></div>
                <div><h2><?php echo esc_html((string) ($s['contact_title'] ?? '')); ?></h2><ul class="xz-page-footer__contact"><li><?php echo esc_html((string) ($s['email'] ?? '')); ?></li><li><?php echo esc_html((string) ($s['phone'] ?? '')); ?></li><li><?php echo esc_html((string) ($s['address'] ?? '')); ?></li><li><?php echo esc_html((string) ($s['whatsapp'] ?? '')); ?></li></ul><div class="xz-page-footer__socials"><?php foreach (['linkedin', 'facebook', 'tiktok'] as $network) : $url = $this->link_url((array) ($s[$network] ?? [])); if ($url) : ?><a href="<?php echo esc_url($url); ?>" target="_blank" rel="noreferrer" aria-label="Xinzhou on <?php echo esc_attr(ucfirst($network)); ?>"><?php echo global_social_icon($network); ?></a><?php endif; endforeach; ?></div></div>
            </div><div class="xz-page-footer__bottom"><p><?php echo esc_html((string) ($s['copyright'] ?? '')); ?></p></div></section>
        </footer>
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
    $widgets_manager->register(new Global_Header_Widget());
    $widgets_manager->register(new Global_Prefooter_Widget());
    $widgets_manager->register(new Global_Main_Footer_Widget());
    $widgets_manager->register(new Global_Footer_Widget());
}

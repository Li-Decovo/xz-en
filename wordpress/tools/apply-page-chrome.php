<?php

if (!defined('ABSPATH')) {
    fwrite(STDERR, "Run this file with wp eval-file.\n");
    exit(1);
}

$header_id = 13;
$footer_id = 32;
$upload = wp_upload_dir();
$backup_dir = trailingslashit($upload['basedir']) . 'xinzhou-backups';
wp_mkdir_p($backup_dir);
$backup_path = trailingslashit($backup_dir) . 'elementor-page-chrome-' . gmdate('Ymd-His') . '.json';

$backup = [];
foreach ([$header_id, $footer_id] as $post_id) {
    $backup[$post_id] = [
        'post' => get_post($post_id),
        'elementor_data' => get_post_meta($post_id, '_elementor_data', true),
        'elementor_page_settings' => get_post_meta($post_id, '_elementor_page_settings', true),
    ];
}
file_put_contents($backup_path, wp_json_encode($backup, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

$menu_id = '';
$locations = get_nav_menu_locations();
foreach (['primary', 'main-menu', 'header', 'menu-1'] as $location) {
    if (!empty($locations[$location])) {
        $menu_id = (string) $locations[$location];
        break;
    }
}
if (!$menu_id) {
    foreach (wp_get_nav_menus() as $menu) {
        $titles = array_map(static fn($item): string => strtolower((string) $item->title), (array) wp_get_nav_menu_items($menu->term_id));
        if (in_array('products', $titles, true)) {
            $menu_id = (string) $menu->term_id;
            break;
        }
    }
}

$logo_url = home_url('/wp-content/uploads/xinzhou-home-assets/site-logo.webp');
$logo = ['id' => attachment_url_to_postid($logo_url), 'url' => $logo_url];

$widget = static function (string $id, string $type, array $settings): array {
    return ['id' => $id, 'elType' => 'widget', 'settings' => $settings, 'elements' => [], 'widgetType' => $type];
};
$document = static function (string $id, array $element): array {
    return [[
        'id' => $id,
        'elType' => 'container',
        'settings' => [
            'content_width' => 'full',
            'flex_direction' => 'column',
            'gap' => ['unit' => 'px', 'size' => 0, 'sizes' => []],
            'padding' => ['unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => true],
            'margin' => ['unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => true],
        ],
        'elements' => [$element],
        'isInner' => false,
    ]];
};

$header_settings = [
    'logo' => $logo,
    'menu_id' => $menu_id,
    'cta_text' => 'Get Your Line Proposal',
    'social_label' => 'Follow Xinzhou',
    'linkedin' => ['url' => 'https://www.linkedin.com/'],
    'facebook' => ['url' => 'https://www.facebook.com/'],
    'tiktok' => ['url' => 'https://www.tiktok.com/@xinzhouwelder'],
    'mega_cta_title' => 'Discuss Your Project',
    'mega_cta_copy' => 'Share your product, output and factory requirements with Xinzhou.',
];

$footer_settings = [
    'subscribe_title' => 'Subscribe to Our Updates',
    'subscribe_copy' => 'Receive product updates, exhibition news and automation insights from Xinzhou.',
    'subscribe_form_id' => 2,
    'sales_title' => 'Sales & Project Team',
    'sales_copy' => 'Tell us your product size, output target and factory layout. Our engineers will match a practical welding line plan for your production.',
    'sales_button' => 'Find Now',
    'support_title' => 'Technical Support',
    'support_copy' => 'Need help with line configuration, commissioning or after-sales service? Connect with Xinzhou for reliable technical assistance.',
    'support_button' => 'Find Out More',
    'support_link' => ['url' => '/services/'],
    'highlight_title' => 'Share Your Requirement',
    'highlight_copy' => 'From steel grating and reinforcing mesh to custom resistance welding automation, Xinzhou builds solutions around real production needs.',
    'logo' => $logo,
    'brand_copy' => 'Xinzhou provides automated resistance welding equipment and complete production line solutions, supported by engineering, manufacturing and global technical service.',
    'inquiry_button' => 'Send an Inquiry',
    'menu_id' => $menu_id,
    'menu_title' => 'Main Menu',
    'products_title' => 'Product Categories',
    'contact_title' => 'Contact Xinzhou',
    'email' => 'xinzhou@weldercn.com',
    'phone' => '+86 180 6723 1686',
    'address' => 'Ningbo, Zhejiang, China',
    'whatsapp' => '+86 574 82566933',
    'copyright' => 'Copyright © Xinzhou Welding Equipment. All rights reserved.',
    'linkedin' => ['url' => 'https://www.linkedin.com/'],
    'facebook' => ['url' => 'https://www.facebook.com/'],
    'tiktok' => ['url' => 'https://www.tiktok.com/@xinzhouwelder'],
    'modal_label' => 'Equipment Inquiry',
    'modal_title' => 'Get Your Line Proposal',
    'modal_form_id' => 1,
];

$prefooter_settings = $footer_settings;
$prefooter_settings['sales_button_text'] = $footer_settings['sales_button'];
$prefooter_settings['support_button_text'] = $footer_settings['support_button'];
$prefooter_settings['support_button_link'] = $footer_settings['support_link'];
$modal_settings = ['label' => $footer_settings['modal_label'], 'title' => $footer_settings['modal_title'], 'form_id' => $footer_settings['modal_form_id']];

$footer_document = $document('xzpgfoot', $widget('xzpreft1', 'xinzhou-global-prefooter', $prefooter_settings));
$footer_document[0]['elements'][] = $widget('xzmainft', 'xinzhou-global-main-footer', $footer_settings);
$footer_document[0]['elements'][] = $widget('xzmodal1', 'xinzhou-global-inquiry-modal', $modal_settings);

$documents = [
    $header_id => $document('xzpghead', $widget('xzpghdw1', 'xinzhou-global-header', $header_settings)),
    $footer_id => $footer_document,
];

foreach ($documents as $post_id => $data) {
    update_post_meta($post_id, '_elementor_data', wp_slash(wp_json_encode($data)));
    update_post_meta($post_id, '_elementor_edit_mode', 'builder');
    $settings = (array) get_post_meta($post_id, '_elementor_page_settings', true);
    $settings['custom_css'] = '';
    $settings['content_width'] = 'full';
    update_post_meta($post_id, '_elementor_page_settings', $settings);
    delete_post_meta($post_id, '_elementor_element_cache');
    delete_post_meta($post_id, '_elementor_page_assets');
    delete_post_meta($post_id, '_elementor_css');
}

echo "Replaced header {$header_id} and footer {$footer_id} with page-style widgets.\n";
echo "Menu ID: " . ($menu_id ?: 'automatic') . "\n";
echo "Backup: {$backup_path}\n";

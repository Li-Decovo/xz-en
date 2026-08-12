<?php

if (!defined('ABSPATH')) {
    fwrite(STDERR, "Run this file with wp eval-file.\n");
    exit(1);
}

$post_id = 32;
$data = json_decode((string) get_post_meta($post_id, '_elementor_data', true), true);
$source = [];

$find_settings = static function (array $elements) use (&$find_settings, &$source): void {
    foreach ($elements as $element) {
        if (($element['widgetType'] ?? '') === 'xinzhou-global-footer-page') {
            $source = (array) ($element['settings'] ?? []);
            return;
        }
        $find_settings((array) ($element['elements'] ?? []));
        if ($source) { return; }
    }
};
$find_settings((array) $data);

if (!$source) {
    fwrite(STDERR, "Combined global footer widget was not found on template {$post_id}.\n");
    exit(1);
}

$pick = static function (array $keys) use ($source): array {
    return array_intersect_key($source, array_flip($keys));
};
$widget = static function (string $id, string $type, array $settings): array {
    return ['id' => $id, 'elType' => 'widget', 'settings' => $settings, 'elements' => [], 'widgetType' => $type];
};

$prefooter = $pick(['subscribe_title', 'subscribe_copy', 'subscribe_form_id', 'sales_title', 'sales_copy', 'sales_button', 'support_title', 'support_copy', 'support_button', 'support_link', 'highlight_title', 'highlight_copy']);
$prefooter['sales_button_text'] = $prefooter['sales_button'] ?? 'Find Now';
$prefooter['support_button_text'] = $prefooter['support_button'] ?? 'Find Out More';
$prefooter['support_button_link'] = $prefooter['support_link'] ?? ['url' => '/services/'];

$main_footer = $pick(['logo', 'brand_copy', 'inquiry_button', 'menu_id', 'menu_title', 'products_title', 'contact_title', 'email', 'phone', 'address', 'whatsapp', 'copyright', 'linkedin', 'facebook', 'tiktok']);
$modal = [
    'label' => $source['modal_label'] ?? 'Equipment Inquiry',
    'title' => $source['modal_title'] ?? 'Get Your Line Proposal',
    'form_id' => $source['modal_form_id'] ?? 1,
];

$backup_dir = trailingslashit(wp_upload_dir()['basedir']) . 'xinzhou-backups';
wp_mkdir_p($backup_dir);
$backup = trailingslashit($backup_dir) . 'footer-before-component-split-' . gmdate('Ymd-His') . '.json';
file_put_contents($backup, wp_json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

$replacement = [[
    'id' => 'xzfootpg',
    'elType' => 'container',
    'settings' => [
        'content_width' => 'full',
        'flex_direction' => 'column',
        'gap' => ['unit' => 'px', 'size' => 0, 'sizes' => []],
        'padding' => ['unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => true],
        'margin' => ['unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => true],
    ],
    'elements' => [
        $widget('xzpreft1', 'xinzhou-global-prefooter', $prefooter),
        $widget('xzmainft', 'xinzhou-global-main-footer', $main_footer),
        $widget('xzmodal1', 'xinzhou-global-inquiry-modal', $modal),
    ],
    'isInner' => false,
]];

update_post_meta($post_id, '_elementor_data', wp_slash(wp_json_encode($replacement)));
delete_post_meta($post_id, '_elementor_element_cache');
delete_post_meta($post_id, '_elementor_page_assets');
delete_post_meta($post_id, '_elementor_css');

echo "Split footer template {$post_id} into three Elementor widgets.\n";
echo "Backup: {$backup}\n";

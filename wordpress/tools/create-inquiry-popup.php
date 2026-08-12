<?php

if (!defined('ABSPATH')) {
    fwrite(STDERR, "Run this file with wp eval-file.\n");
    exit(1);
}

$title = 'Xinzhou Inquiry Popup';
$popup_id = absint(get_option('xz_inquiry_popup_id', 0));
if (!$popup_id || get_post_type($popup_id) !== 'elementor_library') {
    $existing = get_page_by_title($title, OBJECT, 'elementor_library');
    $popup_id = $existing instanceof WP_Post ? (int) $existing->ID : 0;
}
if (!$popup_id) {
    $popup_id = wp_insert_post([
        'post_title' => $title,
        'post_type' => 'elementor_library',
        'post_status' => 'publish',
    ], true);
    if (is_wp_error($popup_id)) {
        fwrite(STDERR, $popup_id->get_error_message() . "\n");
        exit(1);
    }
}

$widget = static function (string $id, string $type, array $settings): array {
    return ['id' => $id, 'elType' => 'widget', 'settings' => $settings, 'elements' => [], 'widgetType' => $type];
};
$container = static function (string $id, array $settings, array $elements): array {
    return ['id' => $id, 'elType' => 'container', 'settings' => $settings, 'elements' => $elements, 'isInner' => true];
};
$spacing = static function (int $top, int $right, int $bottom, int $left): array {
    return ['unit' => 'px', 'top' => (string) $top, 'right' => (string) $right, 'bottom' => (string) $bottom, 'left' => (string) $left, 'isLinked' => false];
};

$data = [[
    'id' => 'xzpopup0',
    'elType' => 'container',
    'settings' => [
        'css_classes' => 'xz-inquiry-popup',
        'content_width' => 'full',
        'flex_direction' => 'column',
        'gap' => ['unit' => 'px', 'size' => 0, 'sizes' => []],
        'padding' => $spacing(0, 0, 0, 0),
        'background_background' => 'classic',
        'background_color' => '#ffffff',
    ],
    'elements' => [
        $container('xzpophead', [
            'css_classes' => 'xz-inquiry-popup__head',
            'content_width' => 'full',
            'flex_direction' => 'column',
            'justify_content' => 'center',
            'gap' => ['unit' => 'px', 'size' => 10, 'sizes' => []],
            'padding' => $spacing(30, 76, 26, 32),
            'background_background' => 'classic',
            'background_color' => '#0f172a',
        ], [
            $widget('xzpoplbl', 'heading', ['css_classes' => 'xz-inquiry-popup__label', 'title' => 'Equipment Inquiry', 'header_size' => 'p']),
            $widget('xzpopttl', 'heading', ['css_classes' => 'xz-inquiry-popup__title', 'title' => 'Get Your Line Proposal', 'header_size' => 'h2']),
        ]),
        $container('xzpopbody', [
            'css_classes' => 'xz-inquiry-popup__body',
            'content_width' => 'full',
            'padding' => $spacing(30, 32, 32, 32),
        ], [
            $widget('xzpopfrm', 'shortcode', ['css_classes' => 'xz-inquiry-popup__form', 'shortcode' => '[fluentform id="1"]']),
        ]),
    ],
    'isInner' => false,
]];

wp_update_post(['ID' => $popup_id, 'post_title' => $title, 'post_status' => 'publish']);
wp_set_object_terms($popup_id, 'popup', 'elementor_library_type', false);
update_post_meta($popup_id, '_elementor_template_type', 'popup');
update_post_meta($popup_id, '_elementor_edit_mode', 'builder');
update_post_meta($popup_id, '_elementor_data', wp_slash(wp_json_encode($data)));
update_post_meta($popup_id, '_elementor_page_settings', [
    'width' => ['unit' => 'px', 'size' => 760, 'sizes' => []],
    'height_type' => 'auto',
    'horizontal_position' => 'center',
    'vertical_position' => 'center',
    'overlay' => 'yes',
    'close_button' => 'yes',
    'entrance_animation' => 'fadeInUp',
    'exit_animation' => 'fadeOutDown',
    'entrance_animation_duration' => ['unit' => 's', 'size' => 0.3, 'sizes' => []],
    'prevent_scroll' => 'yes',
    'overlay_background_color' => 'rgba(2, 6, 23, 0.72)',
]);
update_post_meta($popup_id, '_elementor_popup_display_settings', ['triggers' => [], 'timing' => []]);
delete_post_meta($popup_id, '_elementor_element_cache');
delete_post_meta($popup_id, '_elementor_page_assets');
delete_post_meta($popup_id, '_elementor_css');
update_option('xz_inquiry_popup_id', $popup_id, false);

$footer_id = 32;
$footer_data = json_decode((string) get_post_meta($footer_id, '_elementor_data', true), true);
$backup_dir = trailingslashit(wp_upload_dir()['basedir']) . 'xinzhou-backups';
wp_mkdir_p($backup_dir);
$backup = trailingslashit($backup_dir) . 'footer-before-native-popup-' . gmdate('Ymd-His') . '.json';
file_put_contents($backup, wp_json_encode($footer_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

$remove_old_modal = static function (array $elements) use (&$remove_old_modal): array {
    $filtered = [];
    foreach ($elements as $element) {
        if (($element['widgetType'] ?? '') === 'xinzhou-global-inquiry-modal') { continue; }
        $element['elements'] = $remove_old_modal((array) ($element['elements'] ?? []));
        $filtered[] = $element;
    }
    return $filtered;
};
$footer_data = $remove_old_modal((array) $footer_data);
update_post_meta($footer_id, '_elementor_data', wp_slash(wp_json_encode($footer_data)));
delete_post_meta($footer_id, '_elementor_element_cache');
delete_post_meta($footer_id, '_elementor_page_assets');
delete_post_meta($footer_id, '_elementor_css');

echo "Created native Elementor popup {$popup_id}: {$title}\n";
echo "Removed the legacy modal widget from footer template {$footer_id}.\n";
echo "Backup: {$backup}\n";

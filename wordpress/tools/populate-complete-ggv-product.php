<?php

if (!defined('ABSPATH')) {
    exit;
}

$product = get_page_by_path('ggv-series-automatic-steel-grating-welding-line', OBJECT, 'product');
if (!$product instanceof WP_Post) {
    WP_CLI::error('The GGV product was not found.');
}

if (!function_exists('update_field')) {
    WP_CLI::error('ACF is not available.');
}

$product_id = (int) $product->ID;
$short_description = '<p>A heavy-duty automated production system engineered for high-volume industrial steel grating manufacturing, combining resistance welding, servo pulling and HMI + PLC control.</p>';
$overview_primary = '<h3>Fully Integrated Steel Grating Production</h3><p>The GGV Series is Xinzhou\'s heavy-duty engineering system for the high-volume production of industrial steel gratings. It integrates flat load bars with automatically fed twisted cross bars using transformer technology and microcomputer controls.</p><p>The line is managed through an HMI + PLC system and driven by high-precision servo pulling. Standard and customized configurations are available according to grating specifications, output targets, factory layout and required automation level.</p>';
$overview_secondary = '<h3>Performance, Feeding and Intelligent Control</h3><p>The water-cooled transformer delivers high welding current, efficient cooling and long service life. During production, a heavy-duty hydraulic correction mechanism realigns the steel grating panel to maintain a flat and uniform finished result.</p><p>Automatic servo cross bar feeding, dual-rod stocking and the load bar pre-feeding system reduce waiting time between welding cycles. HMI + PLC control provides clear visual operation, while servo pulling allows the cross bar pitch to be adjusted through the touch screen according to the required product specification.</p><p>Integrated IoT functions support remote monitoring, fault diagnosis and program updates, helping Xinzhou\'s technical team respond efficiently to overseas service requirements.</p>';
$specifications = <<<'HTML'
<h3>GGV Series Model Range</h3>
<p>Final specifications can be configured according to the required grating size, material and production target.</p>
<table>
<thead>
<tr><th scope="col">Parameter</th><th scope="col">GGV-1250-1000</th><th scope="col">GGV-1600-1000</th><th scope="col">GGV-2000-1000</th><th scope="col">GGV-3000-1200</th></tr>
</thead>
<tbody>
<tr><th scope="row">Welding Width</th><td>600-1000 mm</td><td>600-1000 mm</td><td>600-1000 mm</td><td>600-1200 mm</td></tr>
<tr><th scope="row">Load Bar Thickness</th><td>2-6 mm</td><td>2-8 mm</td><td>2-10 mm</td><td>2-8 mm</td></tr>
<tr><th scope="row">Load Bar Height</th><td>20-70 mm</td><td>20-70 mm</td><td>20-70 mm</td><td>20-100 mm</td></tr>
<tr><th scope="row">Cross Bar Diameter</th><td>4-6 mm</td><td>4-8 mm</td><td>4-8 mm</td><td>4-8 mm</td></tr>
<tr><th scope="row">Welding Stroke</th><td colspan="4">12-14 strokes per minute</td></tr>
<tr><th scope="row">Cross Bar Feed</th><td colspan="4">24-28 bars per minute</td></tr>
<tr><th scope="row">Input Power</th><td colspan="4">380V, 50Hz / Customized</td></tr>
</tbody>
</table>
HTML;

$parameters = [
    ['product_parameter_label' => 'Welding Width', 'product_parameter_value' => '600-1200 mm'],
    ['product_parameter_label' => 'Load Bar Thickness', 'product_parameter_value' => '2-10 mm'],
    ['product_parameter_label' => 'Load Bar Height', 'product_parameter_value' => '20-100 mm'],
    ['product_parameter_label' => 'Cross Bar Diameter', 'product_parameter_value' => '4-8 mm'],
    ['product_parameter_label' => 'Welding Stroke', 'product_parameter_value' => '12-14 strokes/min'],
    ['product_parameter_label' => 'Control System', 'product_parameter_value' => 'HMI + PLC'],
];

$faq = [
    [
        'faq_question' => 'Can the steel grating production line be customized?',
        'faq_answer' => '<p>Yes. The machine model, welding width, load bar range, cross bar specification, output and automation configuration can be planned according to your product and factory requirements.</p>',
    ],
    [
        'faq_question' => 'What finished products can the GGV Series produce?',
        'faq_answer' => '<p>The line is designed for industrial steel grating panels, platform grating, walkway grating and other welded grating products within the configured specification range.</p>',
    ],
    [
        'faq_question' => 'Can auxiliary equipment be included in the production line?',
        'faq_answer' => '<p>Yes. Twisted bar forming, edge trimming, panel cutting, binding bar welding, side discharge and stacking equipment can be integrated according to the required workflow.</p>',
    ],
    [
        'faq_question' => 'Do you provide installation and operator training?',
        'faq_answer' => '<p>Xinzhou can provide on-site installation, commissioning, production testing, operator training and maintenance guidance for overseas projects.</p>',
    ],
    [
        'faq_question' => 'Can the input voltage be customized?',
        'faq_answer' => '<p>The standard input power is 380V, 50Hz. The electrical configuration can be customized according to the power conditions in the customer\'s factory.</p>',
    ],
];

wp_update_post(wp_slash([
    'ID' => $product_id,
    'post_excerpt' => wp_strip_all_tags($short_description),
    'post_content' => $overview_primary,
]));

update_field('field_xz_product_sort_order', 100, $product_id);
update_field('field_xz_product_short_description', $short_description, $product_id);
update_field('field_xz_product_gallery', [159, 161, 182, 183, 181], $product_id);
update_field('field_xz_product_key_parameters', $parameters, $product_id);
update_field('field_xz_product_overview_primary', $overview_primary, $product_id);
update_field('field_xz_product_overview_image', 181, $product_id);
update_field('field_xz_product_overview_secondary', $overview_secondary, $product_id);
update_field('field_xz_product_specifications', $specifications, $product_id);
update_field('field_xz_product_finished_products', [184, 185, 186], $product_id);
update_field('field_xz_product_faq', $faq, $product_id);
update_field('field_xz_related_products', [162, 164, 166], $product_id);

set_post_thumbnail($product_id, 159);
update_post_meta($product_id, 'rank_math_title', 'GGV Series Steel Grating Welding Production Line | Xinzhou');
update_post_meta($product_id, 'rank_math_description', 'GGV Series fully automatic intelligent steel grating welding production line with HMI, PLC and servo-controlled production.');
update_post_meta($product_id, 'rank_math_focus_keyword', 'steel grating welding production line');

$attachments = [
    159 => ['GGV Series Steel Grating Welding Production Line', 'GGV fully automatic steel grating welding production line'],
    161 => ['GGV Steel Grating Main Resistance Welding Machine', 'GGV steel grating main resistance welding machine'],
    182 => ['GGV Steel Grating Welding Machine Side View', 'Side view of GGV steel grating welding machine'],
    183 => ['GGV Steel Grating Welding Machine Front View', 'Front view of GGV steel grating welding machine'],
    181 => ['GGV Steel Grating Production Line Installed in Workshop', 'GGV steel grating production line installed in a workshop'],
    184 => ['Industrial Steel Grating Panels', 'Industrial steel grating panel produced by the GGV line'],
    185 => ['Heavy-Duty Platform Grating', 'Heavy-duty platform grating produced by the GGV line'],
    186 => ['Walkway & Access Grating', 'Steel walkway grating produced by the GGV line'],
];

foreach ($attachments as $attachment_id => [$title, $alt]) {
    if (get_post_type($attachment_id) !== 'attachment') {
        continue;
    }
    wp_update_post(['ID' => $attachment_id, 'post_title' => $title]);
    update_post_meta($attachment_id, '_wp_attachment_image_alt', $alt);
}

clean_post_cache($product_id);
WP_CLI::success('Complete GGV product content updated for product ' . $product_id . '.');

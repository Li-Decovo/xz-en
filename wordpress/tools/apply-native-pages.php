<?php

if (!defined('ABSPATH')) {
    exit;
}

function xznp_container(string $id, string $classes, array $elements = [], array $settings = [], bool $inner = true): array {
    $settings = array_merge([
        'content_width' => 'full',
        'css_classes' => $classes,
    ], $settings);
    return [
        'id' => $id,
        'elType' => 'container',
        'settings' => $settings,
        'elements' => $elements,
        'isInner' => $inner,
    ];
}

function xznp_widget(string $id, string $type, array $settings = []): array {
    return [
        'id' => $id,
        'elType' => 'widget',
        'settings' => $settings,
        'elements' => [],
        'widgetType' => $type,
    ];
}

function xznp_heading(string $id, string $title, string $size = 'h2', string $class = ''): array {
    return xznp_widget($id, 'heading', [
        'title' => $title,
        'header_size' => $size,
        '_css_classes' => $class,
    ]);
}

function xznp_text(string $id, string $html, string $class = ''): array {
    return xznp_widget($id, 'text-editor', [
        'editor' => $html,
        '_css_classes' => $class,
    ]);
}

function xznp_image(string $id, string $url, string $class = ''): array {
    return xznp_widget($id, 'image', [
        'image' => [
            'id' => attachment_url_to_postid($url),
            'url' => $url,
        ],
        'image_size' => 'full',
        '_css_classes' => $class,
    ]);
}

function xznp_media_setting(string $url): array {
    return [
        'id' => attachment_url_to_postid($url),
        'url' => $url,
    ];
}

function xznp_button(string $id, string $text, string $url, string $class = '', bool $inquiry = false): array {
    return xznp_widget($id, 'button', [
        'text' => $text,
        'link' => [
            'url' => $url,
            'is_external' => '',
            'nofollow' => '',
            'custom_attributes' => $inquiry ? 'data-inquiry-open|true' : '',
        ],
        '_css_classes' => $class,
    ]);
}

function xznp_posts(string $id, string $query_id, int $count, string $class = ''): array {
    $post_type = str_contains($query_id, 'product') ? 'product' : 'post';
    return xznp_widget($id, 'posts', [
        '_skin' => 'classic',
        'classic_columns' => 3,
        'classic_columns_tablet' => 2,
        'classic_columns_mobile' => 1,
        'posts_per_page' => $count,
        'show_image' => 'yes',
        'image_size' => 'large',
        'show_title' => 'yes',
        'show_excerpt' => 'yes',
        'excerpt_length' => 20,
        'show_read_more' => 'yes',
        'read_more_text' => 'View More',
        'pagination_type' => '',
        'posts_post_type' => $post_type,
        'posts_query_id' => $query_id,
        '_css_classes' => $class,
    ]);
}

function xznp_section_head(string $prefix, string $kicker, string $title, string $copy = ''): array {
    $elements = [
        xznp_heading($prefix . '01', $kicker, 'div', 'xz-native-kicker'),
        xznp_heading($prefix . '02', $title, 'h2', 'xz-native-section-title'),
    ];
    if ($copy) {
        $elements[] = xznp_text($prefix . '03', '<p>' . esc_html($copy) . '</p>', 'xz-native-section-copy');
    }
    return xznp_container($prefix . '00', 'xz-native-section-head', $elements);
}

function xznp_media_card(string $prefix, string $url, string $title, string $copy = ''): array {
    $elements = [
        xznp_image($prefix . '01', $url, 'xz-native-card-image'),
        xznp_heading($prefix . '02', $title, 'h3', 'xz-native-card-title'),
    ];
    if ($copy) {
        $elements[] = xznp_text($prefix . '03', '<p>' . esc_html($copy) . '</p>', 'xz-native-card-copy');
    }
    return xznp_container($prefix . '00', 'xz-native-media-card', $elements);
}

function xznp_save_page(int $post_id, array $data, string $css): void {
    update_post_meta($post_id, '_elementor_data', wp_slash(wp_json_encode($data)));
    update_post_meta($post_id, '_elementor_page_settings', [
        'hide_title' => 'yes',
        'custom_css' => $css,
    ]);
    delete_post_meta($post_id, '_elementor_element_cache');
    delete_post_meta($post_id, '_elementor_page_assets');
    delete_post_meta($post_id, '_elementor_css');
}

$home_asset = static fn(string $file): string => home_url('/wp-content/uploads/xinzhou-home-assets/' . $file);
$about_asset = static fn(string $file): string => home_url('/wp-content/uploads/xinzhou-about-assets/' . $file);
$service_asset = static fn(string $file): string => home_url('/wp-content/uploads/xinzhou-services-assets/' . $file);

$home = [
    xznp_container('hroot001', 'xz-native-home', [
        xznp_container('hcat0001', 'xz-native-category-section', [
            xznp_widget('hcat0002', 'xinzhou-product-categories', [
                'layout' => 'homepage',
                'hide_empty' => '',
                'show_description' => '',
                'image_size' => 'large',
            ]),
        ]),
        xznp_widget('hsplit01', 'xinzhou-home-split', [
            'eyebrow' => 'Smart Factory Solutions',
            'title' => 'Xinzhou Smart Welding Systems for Modern Production',
            'lead' => '27+ years of R&D, in-house manufacturing and turnkey automation for steel grating, wire mesh and custom resistance welding lines.',
            'description' => '<p>From factory layout planning to equipment manufacturing, commissioning and long-term remote support, Xinzhou helps global manufacturers build stable, efficient and scalable welding production systems.</p>',
            'button_text' => 'View More',
            'button_link' => ['url' => '/about-xinzhou/'],
            'image' => xznp_media_setting($home_asset('factory-smart.webp')),
            'image_position' => 'left',
            'show_play' => 'yes',
            'video_link' => ['url' => '#'],
        ]),
        xznp_widget('hcarousel01', 'xinzhou-home-carousel', [
            'source' => 'products',
            'count' => 6,
            'autoplay' => 'yes',
            'autoplay_speed' => 4200,
            'button_text' => 'View More',
        ]),
        xznp_widget('hstories01', 'xinzhou-home-stories', [
            'stories' => [
                [
                    '_id' => 'story001',
                    'image' => xznp_media_setting($home_asset('factory-fabrication.webp')),
                    'image_position' => 'left',
                    'title' => 'Built on Real In-House Fabrication Capacity',
                    'lead' => 'From steel structure processing to complete line assembly, Xinzhou keeps core manufacturing inside its own factory.',
                    'description' => '<p>A 16,000 m2 production base, heavy-duty fabrication capability and experienced assembly teams allow us to control welding line quality from frame machining to final equipment testing.</p>',
                    'button_text' => 'View More',
                    'button_link' => ['url' => '/about-xinzhou/'],
                ],
                [
                    '_id' => 'story002',
                    'image' => xznp_media_setting($home_asset('factory-assembly.webp')),
                    'image_position' => 'right',
                    'title' => 'Engineering Support from Layout to Commissioning',
                    'lead' => 'We help manufacturers plan complete welding production systems around output targets, factory space and product specifications.',
                    'description' => '<p>Xinzhou combines HMI + PLC control, auxiliary equipment integration, on-site commissioning guidance, operator training and long-term remote service into one practical project workflow.</p>',
                    'button_text' => 'View More',
                    'button_link' => ['url' => '/services/'],
                ],
            ],
        ]),
        xznp_widget('hcases001', 'xinzhou-home-cases', [
            'count' => 5,
            'label' => 'Case',
        ]),
        xznp_widget('hworld001', 'xinzhou-home-worldwide', [
            'title' => 'Xinzhou Worldwide',
            'logos' => [
                ['_id' => 'logo001', 'image' => xznp_media_setting($home_asset('site-logo.webp')), 'link' => ['url' => '#'], 'alt' => 'Xinzhou Resistance Welder'],
                ['_id' => 'logo002', 'image' => xznp_media_setting($home_asset('site-logo.webp')), 'link' => ['url' => '#'], 'alt' => 'Xinzhou Resistance Welder'],
                ['_id' => 'logo003', 'image' => xznp_media_setting($home_asset('site-logo.webp')), 'link' => ['url' => '#'], 'alt' => 'Xinzhou Resistance Welder'],
                ['_id' => 'logo004', 'image' => xznp_media_setting($home_asset('site-logo.webp')), 'link' => ['url' => '#'], 'alt' => 'Xinzhou Resistance Welder'],
            ],
        ]),
    ], [], false),
];

$home_css = <<<'CSS'
selector .xz-native-home{font-family:Inter,Arial,sans-serif;color:#111827;padding:0!important;}
selector .xz-native-category-section{padding:32px 0 40px;border-bottom:1px solid #e2e8f0;}
@media(max-width:640px){selector .xz-native-category-section{padding:24px 0 32px;}}
CSS;

$factory_cards = [
    ['factory-highlight-workshop.webp', 'Modern Production Workshops'],
    ['factory-highlight-cnc.webp', 'Precision CNC Machining Equipment'],
    ['factory-highlight-assembly.webp', 'Professional Assembly Lines'],
    ['factory-highlight-quality.webp', 'Strict Quality Inspection Procedures'],
    ['factory-highlight-capacity.webp', 'Large-Scale Manufacturing Capacity'],
    ['factory-highlight-team.webp', 'Professional Team'],
];
$factory_elements = [];
foreach ($factory_cards as $index => $card) {
    $factory_elements[] = xznp_media_card('afc' . str_pad((string) $index, 4, '0', STR_PAD_LEFT), $about_asset($card[0]), $card[1]);
}

$team_cards = [
    ['team-sales.webp', 'Experienced Sales Team', "Our international sales engineers understand customers' production requirements and provide prompt, professional communication throughout every project."],
    ['team-engineering.webp', 'Professional Engineering Team', "Our experienced mechanical, electrical and automation engineers develop customized solutions according to customers' factory layout, production capacity and budget."],
    ['team-after-sales.webp', 'Dedicated After-sales Team', 'We provide comprehensive technical support after delivery, ensuring every production line operates efficiently and reliably.'],
    ['team-overseas-service.webp', 'Overseas Service Team', 'Our overseas service engineers are available for on-site installation, commissioning, operator training and technical support worldwide, helping customers start production quickly.'],
];
$team_elements = [];
foreach ($team_cards as $index => $card) {
    $team_elements[] = xznp_media_card('atm' . str_pad((string) $index, 4, '0', STR_PAD_LEFT), $about_asset($card[0]), $card[1], $card[2]);
}

$timeline = [
    ['1999', 'timeline-1999.webp', 'Established Ningbo Yinzhou Xinya Welding Equipment Factory.'],
    ['2003', 'timeline-2003.webp', 'Officially registered Ningbo Yinzhou Xinzhou Welding Equipment Co., Ltd. and established a long-term strategic partnership with AUX Group.'],
    ['2006', 'timeline-2006.webp', 'Relocated to a new manufacturing facility and officially renamed the company to Ningbo Xinzhou Welding Equipment Co., Ltd.'],
    ['2009', 'timeline-2009.webp', 'Recognized as a National High-Tech Enterprise.'],
    ['2014', 'timeline-2014.webp', 'Acquired 55 acres of land in Tongxiang, Zhejiang, to invest in and construct Zhejiang Yizhou Mechanical Technology Co., Ltd.'],
    ['2017', 'timeline-2017.webp', 'Zhejiang Yizhou Mechanical Technology Co., Ltd. was officially established, with annual sales reaching 120 million RMB.'],
    ['2020', 'timeline-2020.webp', "In July, Yizhou's Phase II new facility (66 acres) was officially put into operation. In the same year, Ningbo Xinzhou's Phase II factory (12,932 square meters) was constructed and put into use."],
];
$timeline_elements = [];
foreach ($timeline as $index => $item) {
    $prefix = 'atl' . str_pad((string) $index, 4, '0', STR_PAD_LEFT);
    $timeline_elements[] = xznp_container($prefix . '0', 'xz-native-timeline-card', [
        xznp_heading($prefix . '1', $item[0], 'h3', 'xz-native-timeline-year'),
        xznp_image($prefix . '2', $about_asset($item[1])),
        xznp_text($prefix . '3', '<p>' . esc_html($item[2]) . '</p>'),
    ]);
}

$about = [
    xznp_container('aroot001', 'xz-native-about', [
        xznp_container('aher0001', 'xz-native-about-hero xz-native-about-split', [
            xznp_container('aher0002', 'xz-native-about-copy', [
                xznp_heading('aher0003', 'Home / About Us', 'div', 'xz-native-kicker'),
                xznp_heading('aher0004', '28 Years of Expertise in Automated Welding Equipment.', 'h1'),
                xznp_text('aher0005', '<p>Founded in 1998, Ningbo Xinzhou Welding Equipment Co., Ltd. has been dedicated to the research, design, manufacturing and global supply of automated welding equipment and complete production lines for more than 28 years.</p><p>With extensive industry experience and continuous technological innovation, we serve customers in over 80 countries and regions worldwide.</p><p>From standalone machines to turnkey production lines, we provide customized automation solutions that improve efficiency, reduce labor costs and enhance product quality.</p>'),
            ]),
            xznp_container('aher0006', 'xz-native-about-media', [xznp_image('aher0007', $about_asset('history-hero.webp'))]),
        ]),
        xznp_container('afac0001', 'xz-native-about-section xz-native-about-factory', [
            xznp_container('afac0002', 'xz-native-about-factory-grid', $factory_elements),
            xznp_container('afac0003', 'xz-native-about-copy', [
                xznp_heading('afac0004', 'Our Factory', 'div', 'xz-native-kicker'),
                xznp_heading('afac0005', 'Modern Manufacturing for Standard and Customized Projects', 'h2'),
                xznp_text('afac0006', '<p>We have two factories in Ningbo and Jiaxing with nearly 300 employees and strong manufacturing capacity for standard machines and customized production-line projects.</p><p>Core fabrication, machining, assembly, electrical integration and final testing are coordinated inside our manufacturing system to control quality and delivery.</p>'),
            ]),
        ]),
        xznp_container('ateam001', 'xz-native-about-section xz-native-muted', [
            xznp_section_head('athd0', 'Our Professional Team', 'Specialists for Every Stage of Your Project'),
            xznp_container('ateam002', 'xz-native-about-card-grid xz-native-about-team-grid', $team_elements),
        ]),
        xznp_container('aeqp0001', 'xz-native-about-section', [
            xznp_section_head('aehd0', 'Our Products', 'Intelligent Welding Equipment and Turnkey Production Lines', 'Product categories are maintained in the Product Categories area and update here automatically.'),
            xznp_widget('aeqp0002', 'xinzhou-product-categories', ['hide_empty' => '', 'show_description' => '', 'image_size' => 'large']),
        ]),
        xznp_container('aqua0001', 'xz-native-about-section xz-native-muted xz-native-quality', [
            xznp_container('aqua0002', 'xz-native-about-copy', [
                xznp_heading('aqua0003', 'Our Certifications', 'div', 'xz-native-kicker'),
                xznp_heading('aqua0004', 'Quality Is Our Commitment.', 'h2'),
                xznp_text('aqua0005', '<p>Xinzhou operates under a strict quality management system and continuously improves manufacturing standards to ensure reliable performance and long service life.</p><p><strong>Our certifications include ISO 9001 Quality Management System and CE Certification.</strong></p>'),
            ]),
            xznp_container('aqua0006', 'xz-native-certificate-grid', [
                xznp_media_card('acert001', $about_asset('certificate-iso-9001.webp'), 'ISO 9001 Quality Management System'),
                xznp_media_card('acert002', $about_asset('certificate-ce.webp'), 'CE Certification'),
            ]),
        ]),
        xznp_container('acus0001', 'xz-native-about-section xz-native-about-split xz-native-customers', [
            xznp_container('acus0002', 'xz-native-about-media', [xznp_image('acus0003', $about_asset('global-customers.webp'))]),
            xznp_container('acus0004', 'xz-native-about-copy', [
                xznp_heading('acus0005', 'Our Customers', 'div', 'xz-native-kicker'),
                xznp_heading('acus0006', 'Production Lines Delivered Across Global Markets', 'h2'),
                xznp_text('acus0007', '<p>Xinzhou has delivered machines and production lines to customers across Europe, the Middle East, Southeast Asia, South America, North America and Africa.</p><ul><li>Europe</li><li>Middle East</li><li>Southeast Asia</li><li>South America</li><li>North America</li><li>Africa</li></ul>'),
            ]),
        ]),
        xznp_container('ahis0001', 'xz-native-about-section xz-native-timeline-section', [
            xznp_section_head('ahhd0', 'Development History', "Key Milestones in Xinzhou's Manufacturing Growth"),
            xznp_container('ahis0002', 'xz-native-timeline-grid', $timeline_elements),
        ]),
    ], [], false),
];

$about_css = <<<'CSS'
selector .xz-native-about{font-family:Inter,Arial,sans-serif;color:#111827;}
selector .xz-native-about-hero,selector .xz-native-about-section{width:min(100%,1850px);margin:0 auto;padding:84px 24px;}
selector .xz-native-about-split,selector .xz-native-about-factory,selector .xz-native-quality{display:flex;flex-direction:row;align-items:stretch;gap:0;}
selector .xz-native-about-copy,selector .xz-native-about-media{width:50%;justify-content:center;}
selector .xz-native-about-copy{padding:clamp(38px,5vw,90px);}
selector .xz-native-about-copy h1,selector .xz-native-about-copy h2{color:#0f172a;font-size:clamp(30px,2.7vw,44px);line-height:1.14;}
selector .xz-native-about-copy .elementor-widget-text-editor{color:#475569;font-size:17px;line-height:1.75;}
selector .xz-native-about-media img{width:100%;height:100%;min-height:520px;object-fit:cover;}
selector .xz-native-kicker .elementor-heading-title{color:#d84120;font-size:13px;font-weight:800;text-transform:uppercase;}
selector .xz-native-muted{width:100%;max-width:none;background:#f6f7f9;}
selector .xz-native-muted>.e-con-inner{width:min(100%,1850px);margin:0 auto;}
selector .xz-native-section-head{max-width:900px;margin-bottom:34px;gap:10px;}
selector .xz-native-section-title .elementor-heading-title{font-size:clamp(30px,2.5vw,42px);color:#0f172a;}
selector .xz-native-section-copy{color:#64748b;font-size:16px;line-height:1.7;}
selector .xz-native-about-factory-grid{display:grid;width:50%;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px;}
selector .xz-native-about-factory>.xz-native-about-copy{width:50%;}
selector .xz-native-about-card-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:18px;}
selector .xz-native-about .xz-product-category-nav__grid{width:100%;padding:0;}
selector .xz-native-media-card{overflow:hidden;border:1px solid #e2e8f0;background:#fff;}
selector .xz-native-media-card img{width:100%;aspect-ratio:4/3;object-fit:cover;}
selector .xz-native-card-title .elementor-heading-title{padding:14px 16px;font-size:16px;line-height:1.4;color:#111827;}
selector .xz-native-card-copy{padding:0 16px 18px;color:#64748b;font-size:14px;line-height:1.6;}
selector .xz-native-quality>.xz-native-about-copy,selector .xz-native-certificate-grid{width:50%;}
selector .xz-native-certificate-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px;align-items:start;}
selector .xz-native-certificate-grid .xz-native-card-title .elementor-heading-title{font-size:15px;text-align:center;}
selector .xz-native-customers ul{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px 28px;margin:24px 0 0;padding:0;list-style:none;font-weight:700;}
selector .xz-native-customers li{padding:12px 0;border-bottom:1px solid #e2e8f0;}
selector .xz-native-timeline-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:18px;}
selector .xz-native-timeline-card{overflow:hidden;border:1px solid #e2e8f0;background:#fff;}
selector .xz-native-timeline-card img{width:100%;aspect-ratio:4/3;object-fit:cover;}
selector .xz-native-timeline-year .elementor-heading-title{padding:18px 18px 0;color:#d84120;font-size:28px;}
selector .xz-native-timeline-card .elementor-widget-text-editor{padding:0 18px 20px;color:#64748b;line-height:1.65;}
@media(max-width:1000px){selector .xz-native-about-split,selector .xz-native-about-factory,selector .xz-native-quality{flex-direction:column!important;}selector .xz-native-about-copy,selector .xz-native-about-media,selector .xz-native-about-factory-grid,selector .xz-native-about-factory>.xz-native-about-copy,selector .xz-native-quality>.xz-native-about-copy,selector .xz-native-certificate-grid{width:100%!important;}selector .xz-native-about-card-grid,selector .xz-native-timeline-grid{grid-template-columns:repeat(2,minmax(0,1fr));}}
@media(max-width:640px){selector .xz-native-about-hero,selector .xz-native-about-section{padding:58px 16px;}selector .xz-native-about-copy{padding:36px 0;}selector .xz-native-about-media img{min-height:0;aspect-ratio:4/3;}selector .xz-native-about-card-grid,selector .xz-native-about-factory-grid,selector .xz-native-certificate-grid,selector .xz-native-timeline-grid{grid-template-columns:1fr;}}
CSS;

$about_factory_items = [];
foreach ($factory_cards as $index => $card) {
    $about_factory_items[] = [
        '_id' => 'factory' . str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT),
        'image' => xznp_media_setting($about_asset($card[0])),
        'title' => $card[1],
    ];
}

$about_team_items = [];
foreach ($team_cards as $index => $card) {
    $about_team_items[] = [
        '_id' => 'team' . str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT),
        'image' => xznp_media_setting($about_asset($card[0])),
        'title' => $card[1],
        'description' => $card[2],
    ];
}

$about_timeline_items = [];
foreach ($timeline as $index => $item) {
    $about_timeline_items[] = [
        '_id' => 'year' . $item[0],
        'year' => $item[0],
        'image' => xznp_media_setting($about_asset($item[1])),
        'description' => $item[2],
    ];
}

$about = [
    xznp_container('aroot002', 'xz-about-page about-main', [
        xznp_widget('abhero01', 'xinzhou-about-hero', [
            'breadcrumb_label' => 'About Us',
            'title' => '28 Years of Expertise in Automated Welding Equipment.',
            'description' => '<p>Founded in 1998, Ningbo Xinzhou Welding Equipment Co., Ltd. has been dedicated to the research, design, manufacturing and global supply of automated welding equipment and complete production lines for more than 28 years.</p><p>With extensive industry experience and continuous technological innovation, we have become one of China\'s leading manufacturers of intelligent welding equipment, serving customers in over 80 countries and regions worldwide.</p><p>From standalone machines to turnkey production lines, we provide customized automation solutions that help customers improve production efficiency, reduce labor costs and enhance product quality.</p>',
            'image' => xznp_media_setting($about_asset('history-hero.webp')),
            'caption_title' => 'Automated Welding Lines',
            'caption_text' => 'Research, design, manufacturing and global supply.',
        ]),
        xznp_widget('abfactory01', 'xinzhou-about-factory', [
            'label' => 'Our Factory',
            'title' => 'Modern Manufacturing for Standard and Customized Projects',
            'description' => '<p>We have two factories each in NINGBO and JIAXING with nearly 300 employees and 50 engineers, covering an area of 16,000 square meters.</p><p>Our modern manufacturing bases are equipped with advanced CNC machining centers, precision processing equipment, automated assembly workshops and comprehensive testing facilities.</p><p>With strict quality control throughout every production stage, we ensure every machine meets international standards before delivery.</p><p>Our production capacity allows us to efficiently complete both standard and customized projects for customers around the world.</p>',
            'items' => $about_factory_items,
        ]),
        xznp_widget('abteam01', 'xinzhou-about-team', [
            'label' => 'Our Professional Team',
            'title' => 'Specialists for Every Stage of Your Project',
            'items' => $about_team_items,
        ]),
        xznp_widget('abequipment01', 'xinzhou-about-equipment', [
            'label' => 'Our Products',
            'title' => 'Intelligent Welding Equipment and Turnkey Production Lines',
            'intro' => 'Xinzhou specializes in the manufacturing of intelligent welding equipment and turnkey production lines, including:',
            'footer' => 'We also provide complete turnkey solutions from project planning to production line installation.',
            'hide_empty' => '',
            'title_overrides' => [
                ['_id' => 'product001', 'term_slug' => 'ibc-tank', 'title' => 'IBC Tank Production Line'],
                ['_id' => 'product002', 'term_slug' => 'steel-grating', 'title' => 'Steel Grating Welding Machine'],
                ['_id' => 'product003', 'term_slug' => 'reinforcing-mesh', 'title' => 'Reinforcing Mesh Welding Machine'],
                ['_id' => 'product004', 'term_slug' => 'lattice-girder', 'title' => 'Lattice Girder Welding Line'],
                ['_id' => 'product005', 'term_slug' => 'cable-tray', 'title' => 'Cable Tray Mesh Welding Machine'],
                ['_id' => 'product006', 'term_slug' => 'fence-panel', 'title' => '3D Fence Panel Production Line'],
                ['_id' => 'product007', 'term_slug' => 'resistance-welding', 'title' => 'Resistance Spot Welding Machine'],
            ],
        ]),
        xznp_widget('abquality01', 'xinzhou-about-quality', [
            'label' => 'Our Certifications',
            'title' => 'Quality Is Our Commitment.',
            'description' => '<p>Xinzhou operates under a strict quality management system and continuously improves manufacturing standards to ensure reliable performance and long service life.</p>',
            'list_label' => 'Our certifications include:',
            'items' => [
                ['_id' => 'cert001', 'image' => xznp_media_setting($about_asset('certificate-iso-9001.webp')), 'title' => 'ISO 9001 Quality Management System'],
                ['_id' => 'cert002', 'image' => xznp_media_setting($about_asset('certificate-ce.webp')), 'title' => 'CE Certification'],
            ],
        ]),
        xznp_widget('abcustomers01', 'xinzhou-about-customers', [
            'label' => 'Our Customers',
            'title' => 'Production Lines Delivered Across Global Markets',
            'description' => 'Over the years, Xinzhou has successfully delivered dozens of machines and production lines to customers across Europe, the Middle East, Southeast Asia, South America, North America and Africa.',
            'image' => xznp_media_setting($about_asset('global-customers.webp')),
            'items' => [
                ['_id' => 'region001', 'image' => xznp_media_setting($about_asset('flags/region-europe.svg')), 'title' => 'Europe'],
                ['_id' => 'region002', 'image' => xznp_media_setting($about_asset('flags/region-middle-east.svg')), 'title' => 'Middle East'],
                ['_id' => 'region003', 'image' => xznp_media_setting($about_asset('flags/region-southeast-asia.svg')), 'title' => 'Southeast Asia'],
                ['_id' => 'region004', 'image' => xznp_media_setting($about_asset('flags/region-south-america.svg')), 'title' => 'South America'],
                ['_id' => 'region005', 'image' => xznp_media_setting($about_asset('flags/region-north-america.svg')), 'title' => 'North America'],
                ['_id' => 'region006', 'image' => xznp_media_setting($about_asset('flags/region-africa.svg')), 'title' => 'Africa'],
            ],
        ]),
        xznp_widget('abtimeline01', 'xinzhou-about-timeline', [
            'label' => 'Development History',
            'title' => "Key Milestones in Xinzhou's Manufacturing Growth",
            'items' => $about_timeline_items,
        ]),
    ], [], false),
];

$about_css = <<<'CSS'
selector .xz-about-page{padding:0!important;gap:0!important;}
selector .xz-about-page>.elementor-element{width:100%;}
CSS;

$services = [
    xznp_container('sroot001', 'xz-native-services', [
        xznp_container('sher0001', 'xz-native-service-split xz-native-service-hero', [
            xznp_container('sher0002', 'xz-native-service-copy', [
                xznp_heading('sher0003', 'Home / Services', 'div', 'xz-native-kicker'),
                xznp_heading('sher0004', 'Professional Sales Consultation', 'h1'),
                xznp_text('sher0005', '<p>Our experienced sales managers provide one-to-one consultation throughout the entire purchasing process.</p><p>From product selection to technical discussions and project planning, we ensure fast communication and efficient support for every customer.</p>'),
            ]),
            xznp_container('sher0006', 'xz-native-service-media', [xznp_image('sher0007', $service_asset('sales-consultation.webp'))]),
        ]),
        xznp_container('scus0001', 'xz-native-service-section xz-native-service-split xz-native-muted', [
            xznp_container('scus0002', 'xz-native-service-media', [xznp_image('scus0003', $service_asset('custom-solution.webp'))]),
            xznp_container('scus0004', 'xz-native-service-copy', [
                xznp_heading('scus0005', 'Free Customized Solution', 'h2'),
                xznp_text('scus0006', '<p>Every factory has different production requirements.</p><p>Our engineering team offers free customized production solutions based on product specifications, production capacity, factory layout and investment budget, ensuring a cost-effective solution for your business.</p>'),
            ]),
        ]),
        xznp_container('slay0001', 'xz-native-service-section xz-native-service-split xz-native-service-dark', [
            xznp_container('slay0002', 'xz-native-service-copy', [
                xznp_heading('slay0003', 'Free Production Line Layout Design', 'h2'),
                xznp_text('slay0004', '<p>Before manufacturing begins, we provide a professional production-line layout to optimize factory space and production efficiency.</p><p><strong>Our layout includes:</strong></p><ul><li>Machine Arrangement</li><li>Material Flow</li><li>Utility Requirements</li><li>Production Workflow Optimization</li></ul>'),
            ]),
            xznp_container('slay0005', 'xz-native-service-media', [xznp_image('slay0006', $service_asset('production-line-layout.webp'))]),
        ]),
        xznp_container('sass0001', 'xz-native-service-assurance', [
            xznp_container('sass0002', 'xz-native-service-assurance-item', [
                xznp_heading('sass0003', '7x24 Online Support', 'h2'),
                xznp_text('sass0004', '<p>Our international support team is available 24 hours a day, 7 days a week.</p><p>Whether you have technical questions, project inquiries or after-sales needs, we are ready to respond promptly.</p>'),
            ]),
            xznp_container('sass0005', 'xz-native-service-assurance-item', [
                xznp_heading('sass0006', 'One-Year Warranty', 'h2'),
                xznp_text('sass0007', '<p>All Xinzhou machines are supplied with a one-year warranty.</p><p>During the warranty period, we provide free replacement of defective parts caused by manufacturing issues together with professional technical assistance.</p>'),
            ]),
        ]),
        xznp_container('sons0001', 'xz-native-service-section xz-native-service-split xz-native-muted', [
            xznp_container('sons0002', 'xz-native-service-media', [xznp_image('sons0003', $service_asset('onsite-installation-training.webp'))]),
            xznp_container('sons0004', 'xz-native-service-copy', [
                xznp_heading('sons0005', 'On-site Installation & Training', 'h2'),
                xznp_text('sons0006', '<p>Our experienced engineers can travel to your factory for:</p><ul><li>Machine Installation</li><li>Equipment Commissioning</li><li>Production Testing</li><li>Operator Training</li><li>Maintenance Guidance</li></ul><blockquote>This ensures your production line starts running smoothly in the shortest possible time.</blockquote>'),
            ]),
        ]),
    ], [], false),
];

$services_css = <<<'CSS'
selector .xz-native-services{font-family:Inter,Arial,sans-serif;color:#111827;}
selector .xz-native-service-hero,selector .xz-native-service-section{width:min(100%,1850px);margin:0 auto;}
selector .xz-native-service-split,selector .xz-native-service-assurance{display:flex;flex-direction:row;align-items:stretch;gap:0;}
selector .xz-native-service-copy,selector .xz-native-service-media{width:50%;justify-content:center;}
selector .xz-native-service-copy{padding:clamp(46px,6vw,104px);}
selector .xz-native-service-copy h1,selector .xz-native-service-copy h2,selector .xz-native-service-assurance h2{color:#0f172a;font-size:clamp(30px,2.7vw,42px);line-height:1.12;}
selector .xz-native-service-copy .elementor-widget-text-editor,selector .xz-native-service-assurance .elementor-widget-text-editor{color:#475569;font-size:17px;line-height:1.75;}
selector .xz-native-service-media img{width:100%;height:100%;min-height:520px;object-fit:cover;}
selector .xz-native-kicker .elementor-heading-title{color:#d84120;font-size:13px;font-weight:800;text-transform:uppercase;}
selector .xz-native-muted{width:100%;max-width:none;background:#f6f7f9;}
selector .xz-native-muted>.e-con-inner{width:min(100%,1850px);margin:0 auto;display:flex;}
selector .xz-native-service-dark{width:100%;max-width:none;background:#0f172a;color:#fff;}
selector .xz-native-service-dark>.e-con-inner{width:min(100%,1850px);margin:0 auto;display:flex;}
selector .xz-native-service-dark h2{color:#fff;}
selector .xz-native-service-dark .elementor-widget-text-editor{color:rgba(255,255,255,.76);}
selector .xz-native-service-copy ul{margin:22px 0 0;padding:0;list-style:none;border-top:1px solid currentColor;}
selector .xz-native-service-copy li{padding:14px 0;border-bottom:1px solid currentColor;font-weight:700;}
selector .xz-native-service-copy blockquote{margin:28px 0 0;padding:0 0 0 18px;border-left:4px solid #d84120;color:#475569;font-weight:600;}
selector .xz-native-service-assurance{width:100%;border-top:1px solid #e2e8f0;border-bottom:1px solid #e2e8f0;}
selector .xz-native-service-assurance-item{position:relative;width:50%;padding:84px clamp(44px,5vw,90px);background:#fff;}
selector .xz-native-service-assurance-item:first-child:after{content:"";position:absolute;top:50%;right:0;width:3px;height:120px;background:#d84120;transform:translate(50%,-50%);}
@media(max-width:900px){selector .xz-native-service-split,selector .xz-native-service-assurance,selector .xz-native-muted>.e-con-inner,selector .xz-native-service-dark>.e-con-inner{flex-direction:column!important;}selector .xz-native-service-copy,selector .xz-native-service-media,selector .xz-native-service-assurance-item{width:100%!important;}selector .xz-native-service-assurance-item:first-child:after{top:auto;right:auto;bottom:0;left:50%;width:100px;height:3px;transform:translate(-50%,50%);}}
@media(max-width:640px){selector .xz-native-service-copy,selector .xz-native-service-assurance-item{padding:46px 16px;}selector .xz-native-service-media img{min-height:0;aspect-ratio:4/3;}}
CSS;

xznp_save_page(19, $home, $home_css);
xznp_save_page(20, $about, $about_css);
xznp_save_page(21, $services, $services_css);

// Replace Fluent Forms shortcodes with the plugin's native Elementor widget and remove the duplicated pre-footer.
function xznp_update_contact_elements(array $elements): array {
    $updated = [];
    foreach ($elements as $element) {
        $classes = (string) ($element['settings']['css_classes'] ?? '');
        if (str_contains($classes, 'xz-contact-prefooter')) {
            continue;
        }
        if (($element['widgetType'] ?? '') === 'shortcode') {
            $shortcode = (string) ($element['settings']['shortcode'] ?? '');
            if (preg_match('/fluentform id=["\'](\d+)["\']/', $shortcode, $match)) {
                $element['widgetType'] = 'fluent-form-widget';
                $element['settings'] = [
                    'form_list' => $match[1],
                    'theme_style' => '',
                    '_css_classes' => $element['settings']['_css_classes'] ?? '',
                ];
            }
        }
        if (!empty($element['elements']) && is_array($element['elements'])) {
            $element['elements'] = xznp_update_contact_elements($element['elements']);
        }
        $updated[] = $element;
    }
    return $updated;
}

$contact_data = json_decode((string) get_post_meta(24, '_elementor_data', true), true);
if (is_array($contact_data)) {
    update_post_meta(24, '_elementor_data', wp_slash(wp_json_encode(xznp_update_contact_elements($contact_data))));
    delete_post_meta(24, '_elementor_element_cache');
    delete_post_meta(24, '_elementor_page_assets');
    delete_post_meta(24, '_elementor_css');
}

$prefooter = xznp_container('gpre0001', 'xz-global-prefooter', [
    xznp_container('gpre0002', 'xz-global-prefooter-inner', [
        xznp_container('gpre0003', 'xz-global-prefooter-card', [
            xznp_heading('gpre0004', 'Subscribe to Our Updates', 'h2'),
            xznp_widget('gpre0005', 'fluent-form-widget', ['form_list' => '2', 'theme_style' => '', '_css_classes' => 'xz-global-subscribe-form']),
            xznp_text('gpre0006', '<p>Receive product updates, exhibition news and automation insights from Xinzhou.</p>'),
        ]),
        xznp_container('gpre0007', 'xz-global-prefooter-card', [
            xznp_heading('gpre0008', 'Sales & Project Team', 'h2'),
            xznp_text('gpre0009', '<p>Tell us your product size, output target and factory layout. Our engineers will match a practical welding-line plan.</p>'),
            xznp_button('gpre0010', 'Find Now', '/contact/#inquiry', 'xz-global-prefooter-button', true),
        ]),
        xznp_container('gpre0011', 'xz-global-prefooter-card', [
            xznp_heading('gpre0012', 'Technical Support', 'h2'),
            xznp_text('gpre0013', '<p>Need help with line configuration, commissioning or after-sales service? Connect with Xinzhou.</p>'),
            xznp_button('gpre0014', 'Find Out More', '/services/', 'xz-global-prefooter-button'),
        ]),
        xznp_container('gpre0015', 'xz-global-prefooter-card xz-global-prefooter-card--highlight', [
            xznp_heading('gpre0016', 'Share Your Requirement', 'h2'),
            xznp_text('gpre0017', '<p>From steel grating and reinforcing mesh to custom resistance welding automation, Xinzhou builds solutions around real production needs.</p>'),
            xznp_button('gpre0018', 'Send an Inquiry', '/contact/#inquiry', 'xz-global-prefooter-button xz-global-prefooter-button--light', true),
        ]),
    ]),
]);

$footer_data = json_decode((string) get_post_meta(32, '_elementor_data', true), true);
if (is_array($footer_data) && isset($footer_data[0]['elements'])) {
    $footer_data[0]['elements'] = array_values(array_filter($footer_data[0]['elements'], static function (array $element): bool {
        return !str_contains((string) ($element['settings']['css_classes'] ?? ''), 'xz-global-prefooter');
    }));
    array_unshift($footer_data[0]['elements'], $prefooter);
    update_post_meta(32, '_elementor_data', wp_slash(wp_json_encode($footer_data)));
    $footer_settings = (array) get_post_meta(32, '_elementor_page_settings', true);
    $footer_css = preg_replace('/\/\* XZ GLOBAL PREFOOTER START \*\/.*?\/\* XZ GLOBAL PREFOOTER END \*\//s', '', (string) ($footer_settings['custom_css'] ?? ''));
    $footer_settings['custom_css'] = rtrim((string) $footer_css) . <<<'CSS'

/* XZ GLOBAL PREFOOTER START */
selector .xz-global-prefooter{width:100%;padding:66px 24px;background:#e5e7eb;color:#111827;font-family:Inter,Arial,sans-serif;}
selector .xz-global-prefooter-inner{display:grid;width:min(100%,1440px);margin:0 auto;grid-template-columns:1.05fr 1fr 1fr 1.2fr;gap:clamp(30px,4vw,70px);}
selector .xz-global-prefooter-card{align-items:flex-start;min-height:250px;}
selector .xz-global-prefooter-card .elementor-heading-title{color:#0f172a;font-size:20px;line-height:1.15;text-transform:uppercase;}
selector .xz-global-prefooter-card .elementor-widget-text-editor{color:#111827;font-size:15px;line-height:1.6;}
selector .xz-global-prefooter-button{margin-top:auto;}
selector .xz-global-prefooter-button .elementor-button{border:2px solid #111827;border-radius:0;color:#111827;background:transparent;font-size:13px;font-weight:800;text-transform:uppercase;}
selector .xz-global-prefooter-button .elementor-button:hover{border-color:#d84120;color:#fff;background:#d84120;}
selector .xz-global-prefooter-card--highlight{padding:34px;background:#d84120;color:#fff;}
selector .xz-global-prefooter-card--highlight .elementor-heading-title,selector .xz-global-prefooter-card--highlight .elementor-widget-text-editor{color:#fff;}
selector .xz-global-prefooter-button--light .elementor-button{border-color:#fff;color:#fff;}
selector .xz-global-subscribe-form .ff-el-form-control{border-radius:0!important;}
selector .xz-global-subscribe-form .ff-btn-submit{border-radius:0!important;background:#d84120!important;color:#fff!important;}
@media(max-width:1000px){selector .xz-global-prefooter-inner{grid-template-columns:repeat(2,minmax(0,1fr));}selector .xz-global-prefooter-card{min-height:220px;}}
@media(max-width:640px){selector .xz-global-prefooter{padding:52px 16px;}selector .xz-global-prefooter-inner{grid-template-columns:1fr;}selector .xz-global-prefooter-card{min-height:0;}selector .xz-global-prefooter-button{margin-top:18px;}}
/* XZ GLOBAL PREFOOTER END */
CSS;
    update_post_meta(32, '_elementor_page_settings', $footer_settings);
    delete_post_meta(32, '_elementor_element_cache');
    delete_post_meta(32, '_elementor_page_assets');
    delete_post_meta(32, '_elementor_css');
}

echo "Updated Home, About, Services, Contact forms and Global Footer.\n";

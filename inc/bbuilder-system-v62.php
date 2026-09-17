<?php
/**
 * Shared v3.8.10.74 BBuilder demo/admin consistency layer.
 *
 * Goals:
 * - BBuilder Row/Column are the only grid primitives.
 * - BBuilder Div is used only for neutral/semantic wrappers.
 * - Icon Card replaces hand-built icon/card HTML.
 * - Swiper is used for hero/gallery/carousel regions.
 * - Managed demo pages are rebuilt with valid block serialization.
 * - Legacy Group/Columns and malformed heading markup are repaired once.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! function_exists( 'wpbb_child_v62_svg_icon' ) ) {
    function wpbb_child_v62_svg_icon( $context = '' ) {
        $context = strtolower( (string) $context );
        $paths = array(
            'calendar' => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M7 3v4M17 3v4M3 10h18"/><path d="M8 14h2M14 14h2M8 18h2"/>',
            'ticket'   => '<path d="M4 7a2 2 0 0 0 2-2h12a2 2 0 0 0 2 2v2a3 3 0 0 0 0 6v2a2 2 0 0 0-2 2H6a2 2 0 0 0-2-2v-2a3 3 0 0 0 0-6V7Z"/><path d="M13 9v2M13 15v2"/>',
            'home'     => '<path d="m3 11 9-8 9 8"/><path d="M5 10v10h14V10M9 20v-6h6v6"/>',
            'tools'    => '<path d="M14.7 6.3a4 4 0 0 0-5 5L3 18l3 3 6.7-6.7a4 4 0 0 0 5-5l-2.4 2.4-3-3 2.4-2.4Z"/>',
            'chart'    => '<path d="M4 19V9M10 19V5M16 19v-7M22 19H2"/>',
            'book'     => '<path d="M4 5a3 3 0 0 1 3-2h5v17H7a3 3 0 0 0-3 2V5Z"/><path d="M20 5a3 3 0 0 0-3-2h-5v17h5a3 3 0 0 1 3 2V5Z"/>',
            'bed'      => '<path d="M3 20v-8h18v8M3 16h18M6 12V8h5a3 3 0 0 1 3 3v1"/><path d="M3 20v2M21 20v2"/>',
            'shield'   => '<path d="M12 3 20 6v6c0 5-3.4 8-8 9-4.6-1-8-4-8-9V6l8-3Z"/><path d="m9 12 2 2 4-4"/>',
            'truck'    => '<path d="M3 6h11v10H3zM14 10h4l3 3v3h-7z"/><circle cx="7" cy="18" r="2"/><circle cx="18" cy="18" r="2"/>',
            'heart'    => '<path d="M20.8 5.6a5.5 5.5 0 0 0-7.8 0L12 6.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8L12 22l8.9-8.6a5.5 5.5 0 0 0-.1-7.8Z"/>',
            'food'     => '<path d="M7 3v7M4 3v4a3 3 0 0 0 6 0V3M7 10v11"/><path d="M16 3v18M16 3c3 1 4 4 4 7h-4"/>',
            'plane'    => '<path d="M22 2 9 13"/><path d="m22 2-7 20-4-9-9-4 20-7Z"/>',
            'shirt'    => '<path d="m8 4 4 2 4-2 5 3-3 5-2-1v10H8V11l-2 1-3-5 5-3Z"/>',
            'device'   => '<rect x="3" y="4" width="18" height="13" rx="2"/><path d="M8 21h8M12 17v4"/>',
            'search'   => '<circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/>',
            'mail'     => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m4 7 8 6 8-6"/>',
            'spark'    => '<path d="m12 3 1.5 4.5L18 9l-4.5 1.5L12 15l-1.5-4.5L6 9l4.5-1.5L12 3Z"/><path d="m19 14 .8 2.2L22 17l-2.2.8L19 20l-.8-2.2L16 17l2.2-.8L19 14Z"/>',
        );
        $key = 'spark';
        $rules = array(
            'calendar' => 'calendar|date|event|booking|reserve|appointment',
            'ticket' => 'ticket|reservation|admission',
            'home' => 'home|property|house|building|venue|room|stay',
            'tools' => 'service|repair|maintenance|support|setup|workshop|parts',
            'chart' => 'business|growth|strategy|finance|compare|performance',
            'book' => 'course|learn|lesson|knowledge|guide|education',
            'bed' => 'hotel|room|guest|accommodation',
            'shield' => 'insurance|cover|policy|secure|safety|protection',
            'truck' => 'logistics|delivery|shipment|freight|fleet',
            'heart' => 'health|medical|doctor|care|clinic|patient',
            'food' => 'restaurant|menu|food|dining|table',
            'plane' => 'travel|trip|destination|holiday|flight',
            'shirt' => 'clothes|fashion|women|men|kids|style',
            'device' => 'tech|device|product|computer|smart|audio|screen',
            'search' => 'search|finder|discover|browse|filter',
            'mail' => 'contact|enquiry|response|message',
        );
        foreach ( $rules as $candidate => $pattern ) {
            if ( preg_match( '/(?:' . $pattern . ')/i', $context ) ) { $key = $candidate; break; }
        }
        return '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $paths[ $key ] . '</svg>';
    }
}

if ( ! function_exists( 'wpbb_child_v62_block' ) ) {
    function wpbb_child_v62_block( $name, $attrs = array(), $inner = '', $self = false ) {
        $json = $attrs ? ' ' . wp_json_encode( $attrs, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) : '';
        if ( $self ) return '<!-- wp:' . $name . $json . ' /-->';
        return '<!-- wp:' . $name . $json . ' -->' . $inner . '<!-- /wp:' . $name . ' -->';
    }
}

if ( ! function_exists( 'wpbb_child_v62_heading' ) ) {
    function wpbb_child_v62_heading( $text, $level = 2, $class = '' ) {
        $level = max( 1, min( 6, (int) $level ) );
        $attrs = array();
        if ( 2 !== $level ) $attrs['level'] = $level;
        if ( $class ) $attrs['className'] = $class;
        $cls = $class ? ' class="wp-block-heading ' . esc_attr( $class ) . '"' : ' class="wp-block-heading"';
        return wpbb_child_v62_block( 'heading', $attrs, '<h' . $level . $cls . '>' . esc_html( $text ) . '</h' . $level . '>' );
    }
}

if ( ! function_exists( 'wpbb_child_v62_paragraph' ) ) {
    function wpbb_child_v62_paragraph( $text, $class = '' ) {
        $attrs = $class ? array( 'className' => $class ) : array();
        $cls = $class ? ' class="' . esc_attr( $class ) . '"' : '';
        return wpbb_child_v62_block( 'paragraph', $attrs, '<p' . $cls . '>' . wp_kses_post( $text ) . '</p>' );
    }
}

if ( ! function_exists( 'wpbb_child_v62_image' ) ) {
    function wpbb_child_v62_image( $url, $class = '' ) {
        if ( ! $url ) return '';
        $attrs = array( 'sizeSlug' => 'large', 'linkDestination' => 'none' );
        if ( $class ) $attrs['className'] = $class;
        $cls = trim( 'wp-block-image size-large ' . $class );
        return wpbb_child_v62_block( 'image', $attrs, '<figure class="' . esc_attr( $cls ) . '"><img src="' . esc_url( $url ) . '" alt=""/></figure>' );
    }
}

if ( ! function_exists( 'wpbb_child_v62_icon_card' ) ) {
    function wpbb_child_v62_icon_card( $title, $text, $class = '', $link_text = '', $link_url = '', $align = 'left' ) {
        $attrs = array(
            'mediaType' => 'svg',
            'svgCode' => wpbb_child_v62_svg_icon( $title . ' ' . $text ),
            'title' => (string) $title,
            'text' => (string) $text,
            'linkText' => (string) $link_text,
            'linkUrl' => (string) $link_url,
            'iconSize' => '52px',
            'contentAlign' => in_array( $align, array( 'left', 'center', 'right' ), true ) ? $align : 'left',
            'autoTextColor' => true,
            'boxShadowClass' => 'shadow-sm',
            'className' => trim( 'wpbb-v62-icon-card ' . $class ),
        );
        return wpbb_child_v62_block( 'wpbb/icon-card', $attrs, '', true );
    }
}


if ( ! function_exists( 'wpbb_child_v64_content_pack' ) ) {
    function wpbb_child_v64_content_pack( $id ) {
        $packs = array( 'automotive' => array( 'hero_title' => 'Find the car, part or service without the guesswork.', 'hero_text' => 'Compare vehicles, book a test drive, arrange servicing and find the right parts from one clear dealership journey.', 'services_heading' => 'Everything a driver needs before and after the keys change hands.', 'services' => array( array( 'Vehicle finder', 'Filter by body style, fuel type, budget and the features that matter.' ), array( 'Service booking', 'Choose a workshop slot for servicing, MOTs, diagnostics or repairs.' ), array( 'Parts & accessories', 'Find compatible essentials with practical fitment guidance.' ) ), 'industries_heading' => 'Built for different ways people buy and run a vehicle.', 'industries' => array( array( 'Family cars', 'Space, safety and running-cost information together.' ), array( 'Electric & hybrid', 'Range, charging and ownership guidance without jargon.' ), array( 'Business fleets', 'A clearer route for multi-vehicle enquiries and servicing.' ), array( 'Approved used', 'Condition, history and next-step information presented consistently.' ) ), 'about_title' => 'A dealership experience designed around confident decisions.', 'about_text' => 'The demo connects discovery, comparison, booking and aftercare so customers can move from research to a useful next step without losing context.', 'stats' => array( array( '120+', 'demo vehicles organised' ), array( '3', 'clear buying routes' ), array( '24h', 'test-drive response target' ), array( '1', 'joined-up aftercare journey' ) ), 'process' => array( array( '01', 'Search', 'Narrow the range by practical needs, not endless scrolling.' ), array( '02', 'Compare', 'Keep key specifications, costs and availability easy to scan.' ), array( '03', 'Book', 'Reserve a test drive, service slot or callback from the same journey.' ) ), 'faq' => array( array( 'Can I reserve a vehicle online?', 'Yes. The demo supports a clear reservation or enquiry route from each vehicle.' ), array( 'Can servicing be booked separately?', 'Yes. Workshop bookings can sit alongside sales without mixing the journeys.' ), array( 'Can parts link to compatible vehicles?', 'Yes. Product and fitment content can be structured around the vehicles you support.' ) ), 'cta_title' => 'Turn vehicle research into a useful next step.', 'cta_text' => 'Give buyers and existing customers one consistent place to browse, compare, book and ask for help.', 'hero_slides' => array( array( 'eyebrow' => 'Everything a driver needs before and after the k', 'title' => 'Find the car, part or service without the guesswork.', 'text' => 'Compare vehicles, book a test drive, arrange servicing and find the right parts from one clear dealership journey.' ), array( 'eyebrow' => 'Explore', 'title' => 'Vehicle finder', 'text' => 'Filter by body style, fuel type, budget and the features that matter.' ), array( 'eyebrow' => 'Plan the next step', 'title' => 'A dealership experience designed around confident decisions.', 'text' => 'The demo connects discovery, comparison, booking and aftercare so customers can move from research to a useful next step without losing context.' ) ), 'page_labels' => array( 'about' => 'About', 'services' => 'Services', 'industries' => 'Use cases', 'contact' => 'Contact' ), 'services_eyebrow' => 'What we help with', 'industries_eyebrow' => 'Use cases', 'about_eyebrow' => 'Why this structure works', 'process_eyebrow' => 'How it works', 'process_heading' => 'A clear route from interest to action.', 'faq_heading' => 'Practical questions, answered before they become friction.' ), 'building' => array( 'hero_title' => 'Building services made clear from the first enquiry.', 'hero_text' => 'Show what you do, where you work, when you are available and how customers can request the right trade without chasing information.', 'services_heading' => 'Practical service routes for planned work and urgent jobs.', 'services' => array( array( 'Project enquiries', 'Capture scope, location, timing and budget before the first call.' ), array( 'Planned maintenance', 'Present recurring maintenance and compliance services clearly.' ), array( 'Repairs & callouts', 'Give urgent jobs a fast route to the right team.' ) ), 'industries_heading' => 'A structure that works across property types and project sizes.', 'industries' => array( array( 'Homes & renovations', 'Clear scopes for extensions, refurbishments and repairs.' ), array( 'Commercial sites', 'Maintenance, fit-out and facilities requirements in one journey.' ), array( 'Landlords & agents', 'Repeatable routes for inspections, repairs and compliance work.' ), array( 'New-build support', 'Trade packages, scheduling and project contact information.' ) ), 'about_title' => 'A service website that feels as organised as the work on site.', 'about_text' => 'This demo puts services, coverage, proof, project examples and enquiry details into a predictable structure that customers can understand quickly.', 'stats' => array( array( '12', 'service categories' ), array( '4', 'property routes' ), array( '24/7', 'urgent enquiry option' ), array( '1', 'consistent project brief' ) ), 'process' => array( array( '01', 'Describe the job', 'Capture property, scope and urgency.' ), array( '02', 'Match the service', 'Route the enquiry to the right trade or maintenance team.' ), array( '03', 'Plan the next step', 'Confirm a visit, quotation or project discussion.' ) ), 'faq' => array( array( 'Do you support urgent repairs?', 'The demo includes a dedicated urgent route that can be connected to the correct contact method.' ), array( 'Can customers upload photos?', 'Yes. A BBuilder form can include file uploads for useful pre-visit context.' ), array( 'Can coverage areas be shown clearly?', 'Yes. Service areas can be grouped by location and linked to relevant services.' ) ), 'cta_title' => 'Make every building enquiry easier to qualify.', 'cta_text' => 'Use structured service pages and practical forms so the right information reaches the right team sooner.', 'hero_slides' => array( array( 'eyebrow' => 'Practical service routes for planned work and ur', 'title' => 'Building services made clear from the first enquiry.', 'text' => 'Show what you do, where you work, when you are available and how customers can request the right trade without chasing information.' ), array( 'eyebrow' => 'Explore', 'title' => 'Project enquiries', 'text' => 'Capture scope, location, timing and budget before the first call.' ), array( 'eyebrow' => 'Plan the next step', 'title' => 'A service website that feels as organised as the work on site.', 'text' => 'This demo puts services, coverage, proof, project examples and enquiry details into a predictable structure that customers can understand quickly.' ) ), 'page_labels' => array( 'about' => 'About', 'services' => 'Services', 'industries' => 'Use cases', 'contact' => 'Contact' ), 'services_eyebrow' => 'What we help with', 'industries_eyebrow' => 'Use cases', 'about_eyebrow' => 'Why this structure works', 'process_eyebrow' => 'How it works', 'process_heading' => 'A clear route from interest to action.', 'faq_heading' => 'Practical questions, answered before they become friction.' ), 'business' => array( 'hero_title' => 'Clear digital work for ambitious organisations.', 'hero_text' => 'Strategy, websites and content systems designed to make complex offers easier to understand, publish and improve.', 'services_heading' => 'A senior digital team without disconnected disciplines.', 'services' => array( array( 'Digital strategy', 'Research, proposition structure and a practical roadmap.' ), array( 'Web design systems', 'Accessible page systems built for real editorial teams.' ), array( 'Content operations', 'Governance, templates and workflows that reduce publishing friction.' ) ), 'industries_heading' => 'Useful patterns for organisations with complex offers.', 'industries' => array( array( 'Professional services', 'Clarify expertise and lead journeys without flattening the offer.' ), array( 'Technology teams', 'Explain products, integrations and use cases with reusable components.' ), array( 'Membership organisations', 'Connect information, resources and conversion routes.' ), array( 'Multi-region teams', 'Create a stable system for localised content and shared governance.' ) ), 'about_title' => 'A calmer way to turn strategy into an editable website.', 'about_text' => 'The demo separates reusable structure from sector-specific content so teams can publish quickly without losing hierarchy, accessibility or consistency.', 'stats' => array( array( '3', 'connected disciplines' ), array( '6', 'reusable content patterns' ), array( '1', 'shared design system' ), array( '0', 'HTML required for routine editing' ) ), 'process' => array( array( '01', 'Frame the problem', 'Agree audiences, decisions and measures of success.' ), array( '02', 'Design the system', 'Turn repeated content needs into reusable blocks and patterns.' ), array( '03', 'Launch and improve', 'Publish, measure and iterate without rebuilding the foundation.' ) ), 'faq' => array( array( 'Can the design system grow?', 'Yes. New patterns can be added without changing the core editing model.' ), array( 'Will editors need custom HTML?', 'Routine content should be handled with BBuilder and native WordPress blocks.' ), array( 'Can different teams share the same components?', 'Yes. Shared blocks and consistent grid rules are designed for that purpose.' ) ), 'cta_title' => 'Build a website your team can actually operate.', 'cta_text' => 'Start with a clear content model, a useful component system and a practical publishing workflow.', 'hero_slides' => array( array( 'eyebrow' => 'A senior digital team without disconnected disci', 'title' => 'Clear digital work for ambitious organisations.', 'text' => 'Strategy, websites and content systems designed to make complex offers easier to understand, publish and improve.' ), array( 'eyebrow' => 'Explore', 'title' => 'Digital strategy', 'text' => 'Research, proposition structure and a practical roadmap.' ), array( 'eyebrow' => 'Plan the next step', 'title' => 'A calmer way to turn strategy into an editable website.', 'text' => 'The demo separates reusable structure from sector-specific content so teams can publish quickly without losing hierarchy, accessibility or consistency.' ) ), 'page_labels' => array( 'about' => 'About', 'services' => 'Services', 'industries' => 'Use cases', 'contact' => 'Contact' ), 'services_eyebrow' => 'What we help with', 'industries_eyebrow' => 'Use cases', 'about_eyebrow' => 'Why this structure works', 'process_eyebrow' => 'How it works', 'process_heading' => 'A clear route from interest to action.', 'faq_heading' => 'Practical questions, answered before they become friction.' ), 'courses' => array( 'hero_title' => 'Courses built around progress, not just video.', 'hero_text' => 'Help learners find the right course, understand the outcome, move through lessons and see what comes next.', 'services_heading' => 'A learning journey that stays clear from enrolment to completion.', 'services' => array( array( 'Course discovery', 'Filter by topic, level, format and expected outcome.' ), array( 'Lesson experience', 'Combine video, reading, downloads and practical tasks.' ), array( 'Progress & checks', 'Use quizzes, milestones and completion states to keep momentum visible.' ) ), 'industries_heading' => 'Flexible enough for different kinds of learning.', 'industries' => array( array( 'Professional skills', 'Structured pathways for role-based development.' ), array( 'Customer education', 'Product onboarding and practical how-to learning.' ), array( 'Internal academies', 'Reusable training for distributed teams.' ), array( 'Short courses', 'Focused modules that make a single outcome easy to understand.' ) ), 'about_title' => 'A course platform that makes the learning path visible.', 'about_text' => 'This demo prioritises course fit, lesson structure and progress so learners always know what they are working toward and what to do next.', 'stats' => array( array( '12', 'demo learning paths' ), array( '3', 'course levels' ), array( '6', 'lesson content types' ), array( '1', 'consistent learner dashboard' ) ), 'process' => array( array( '01', 'Choose', 'Find a course that matches the learner goal.' ), array( '02', 'Learn', 'Move through lessons with clear resources and tasks.' ), array( '03', 'Check progress', 'Use milestones and knowledge checks to confirm understanding.' ) ), 'faq' => array( array( 'Can courses mix video and text?', 'Yes. Native media, text and BBuilder components can be combined within lesson content.' ), array( 'Can different levels use the same design?', 'Yes. The same component system can support beginner through advanced learning.' ), array( 'Can the platform support downloadable resources?', 'Yes. Files can be attached to lessons or supporting resource sections.' ) ), 'cta_title' => 'Make the next learning step obvious.', 'cta_text' => 'Use structured course pages, reusable lesson patterns and visible progress to reduce learner friction.', 'hero_slides' => array( array( 'eyebrow' => 'A learning journey that stays clear from enrolme', 'title' => 'Courses built around progress, not just video.', 'text' => 'Help learners find the right course, understand the outcome, move through lessons and see what comes next.' ), array( 'eyebrow' => 'Explore', 'title' => 'Course discovery', 'text' => 'Filter by topic, level, format and expected outcome.' ), array( 'eyebrow' => 'Plan the next step', 'title' => 'A course platform that makes the learning path visible.', 'text' => 'This demo prioritises course fit, lesson structure and progress so learners always know what they are working toward and what to do next.' ) ), 'page_labels' => array( 'about' => 'About', 'services' => 'Services', 'industries' => 'Use cases', 'contact' => 'Contact' ), 'services_eyebrow' => 'What we help with', 'industries_eyebrow' => 'Use cases', 'about_eyebrow' => 'Why this structure works', 'process_eyebrow' => 'How it works', 'process_heading' => 'A clear route from interest to action.', 'faq_heading' => 'Practical questions, answered before they become friction.' ), 'hotel' => array( 'hero_title' => 'A hotel website designed around the stay.', 'hero_text' => 'Let guests compare rooms, understand the atmosphere, discover useful extras and move to booking without hunting for details.', 'services_heading' => 'Everything guests need before they arrive.', 'services' => array( array( 'Room finder', 'Compare room types, occupancy, amenities and availability.' ), array( 'Stay details', 'Keep check-in, breakfast, parking and practical information easy to find.' ), array( 'Booking enquiries', 'Give direct booking and special-request routes a clear place.' ) ), 'industries_heading' => 'Content that supports different reasons for staying.', 'industries' => array( array( 'City breaks', 'Rooms, transport and nearby highlights together.' ), array( 'Business stays', 'Fast access to Wi-Fi, workspace and arrival information.' ), array( 'Weekend escapes', 'Package the room with food, wellbeing and local experiences.' ), array( 'Group bookings', 'A clear enquiry route for events, teams and celebrations.' ) ), 'about_title' => 'The useful details of a stay, presented before guests need to ask.', 'about_text' => 'The demo connects room discovery, hotel facilities, local context and booking routes so the website feels like part of the guest experience.', 'stats' => array( array( '6', 'demo room types' ), array( '24/7', 'arrival information' ), array( '4', 'stay-planning routes' ), array( '1', 'direct booking journey' ) ), 'process' => array( array( '01', 'Choose a room', 'Compare capacity, style and useful amenities.' ), array( '02', 'Plan the stay', 'Review dining, transport, check-in and local information.' ), array( '03', 'Book or enquire', 'Move into the correct direct booking or group enquiry route.' ) ), 'faq' => array( array( 'Can guests request an early check-in?', 'Yes. Special requests can be captured during booking or through a dedicated form.' ), array( 'Can room types have separate galleries?', 'Yes. Each room can have its own images and amenity details.' ), array( 'Can group stays use a different form?', 'Yes. Group enquiries can capture dates, rooms and event requirements separately.' ) ), 'cta_title' => 'Make the website feel like the start of the stay.', 'cta_text' => 'Bring rooms, practical information and direct booking into one calm guest journey.', 'hero_slides' => array( array( 'eyebrow' => 'Everything guests need before they arrive.', 'title' => 'A hotel website designed around the stay.', 'text' => 'Let guests compare rooms, understand the atmosphere, discover useful extras and move to booking without hunting for details.' ), array( 'eyebrow' => 'Explore', 'title' => 'Room finder', 'text' => 'Compare room types, occupancy, amenities and availability.' ), array( 'eyebrow' => 'Plan the next step', 'title' => 'The useful details of a stay, presented before guests need to ask.', 'text' => 'The demo connects room discovery, hotel facilities, local context and booking routes so the website feels like part of the guest experience.' ) ), 'page_labels' => array( 'about' => 'About', 'services' => 'Services', 'industries' => 'Use cases', 'contact' => 'Contact' ), 'services_eyebrow' => 'What we help with', 'industries_eyebrow' => 'Use cases', 'about_eyebrow' => 'Why this structure works', 'process_eyebrow' => 'How it works', 'process_heading' => 'A clear route from interest to action.', 'faq_heading' => 'Practical questions, answered before they become friction.' ), 'insurance' => array( 'hero_title' => 'Insurance packages that explain the cover before the quote.', 'hero_text' => 'Help people understand what is protected, compare relevant options and move into a quote request with the right context.', 'services_heading' => 'Clear cover routes instead of a wall of policy language.', 'services' => array( array( 'Cover finder', 'Start with the person, property or risk that needs protection.' ), array( 'Product detail', 'Explain benefits, limits and important exclusions in plain language.' ), array( 'Quote requests', 'Capture enough information to route a useful quotation.' ) ), 'industries_heading' => 'A flexible structure for different insurance needs.', 'industries' => array( array( 'Home & property', 'Buildings, contents and landlord cover journeys.' ), array( 'Motor & mobility', 'Personal, commercial and specialist vehicle products.' ), array( 'Business protection', 'Liability, property and operational risk content.' ), array( 'Travel & lifestyle', 'Short-term and annual cover explained around real scenarios.' ) ), 'about_title' => 'A cover journey designed to reduce uncertainty before the form.', 'about_text' => 'The demo separates product explanation, eligibility guidance and quote capture so customers can make sense of the offer before providing details.', 'stats' => array( array( '8', 'demo cover products' ), array( '4', 'customer need routes' ), array( '3', 'quote stages' ), array( '1', 'consistent policy summary pattern' ) ), 'process' => array( array( '01', 'Identify the need', 'Choose what needs protecting and the relevant context.' ), array( '02', 'Understand the cover', 'Compare benefits, conditions and next-step information.' ), array( '03', 'Request a quote', 'Send a structured enquiry with the details the team needs.' ) ), 'faq' => array( array( 'Can exclusions be shown clearly?', 'Yes. Product pages can separate key benefits, exclusions and supporting documents.' ), array( 'Can quote forms change by product?', 'Yes. Dynamic forms can capture different fields for different cover types.' ), array( 'Can policy documents be downloadable?', 'Yes. BBuilder file blocks can provide policy wording and supporting documents.' ) ), 'cta_title' => 'Make complex cover easier to understand.', 'cta_text' => 'Use a product-led structure that explains the essentials before asking customers to complete a quote.', 'hero_slides' => array( array( 'eyebrow' => 'Clear cover routes instead of a wall of policy l', 'title' => 'Insurance packages that explain the cover before the quote.', 'text' => 'Help people understand what is protected, compare relevant options and move into a quote request with the right context.' ), array( 'eyebrow' => 'Explore', 'title' => 'Cover finder', 'text' => 'Start with the person, property or risk that needs protection.' ), array( 'eyebrow' => 'Plan the next step', 'title' => 'A cover journey designed to reduce uncertainty before the form.', 'text' => 'The demo separates product explanation, eligibility guidance and quote capture so customers can make sense of the offer before providing details.' ) ), 'page_labels' => array( 'about' => 'About', 'services' => 'Services', 'industries' => 'Use cases', 'contact' => 'Contact' ), 'services_eyebrow' => 'What we help with', 'industries_eyebrow' => 'Use cases', 'about_eyebrow' => 'Why this structure works', 'process_eyebrow' => 'How it works', 'process_heading' => 'A clear route from interest to action.', 'faq_heading' => 'Practical questions, answered before they become friction.' ), 'logistics' => array( 'hero_title' => 'Logistics services built around the load, route and hand-off.', 'hero_text' => 'Make service options, coverage, timing and shipment enquiries clear before goods start moving.', 'services_heading' => 'A practical route from shipment need to the right service.', 'services' => array( array( 'Service finder', 'Match freight type, urgency and destination to a suitable service.' ), array( 'Route options', 'Explain local, national and international transport choices.' ), array( 'Shipment enquiries', 'Capture collection, delivery, load and timing details in one form.' ) ), 'industries_heading' => 'Structured for different operational requirements.', 'industries' => array( array( 'Retail distribution', 'Store replenishment, returns and peak-volume planning.' ), array( 'Manufacturing', 'Inbound materials and outbound finished-goods movement.' ), array( 'Ecommerce', 'Parcel, pallet and fulfilment routes for growing order volumes.' ), array( 'Project freight', 'Planned transport for irregular, oversized or time-sensitive loads.' ) ), 'about_title' => 'A logistics website that speaks in operational decisions.', 'about_text' => 'The demo focuses on what is moving, where it needs to go, when it matters and what information is needed for a useful quotation.', 'stats' => array( array( '4', 'service routes' ), array( '3', 'shipment stages' ), array( '24h', 'quote response target' ), array( '1', 'shared tracking language' ) ), 'process' => array( array( '01', 'Describe the shipment', 'Add collection, delivery, size and timing.' ), array( '02', 'Choose the route', 'Match the requirement to the right transport service.' ), array( '03', 'Plan the hand-off', 'Confirm quote, collection details and delivery expectations.' ) ), 'faq' => array( array( 'Can urgent deliveries have a separate route?', 'Yes. Same-day and time-critical enquiries can be prioritised with a dedicated form.' ), array( 'Can customers send pallet dimensions?', 'Yes. Forms can capture dimensions, weight and supporting files.' ), array( 'Can tracking information be linked later?', 'Yes. Shipment references and external tracking tools can be connected to the customer journey.' ) ), 'cta_title' => 'Make shipment enquiries useful from the first message.', 'cta_text' => 'Collect the route, load and timing details your operations team needs without adding friction.', 'hero_slides' => array( array( 'eyebrow' => 'A practical route from shipment need to the righ', 'title' => 'Logistics services built around the load, route and hand-off.', 'text' => 'Make service options, coverage, timing and shipment enquiries clear before goods start moving.' ), array( 'eyebrow' => 'Explore', 'title' => 'Service finder', 'text' => 'Match freight type, urgency and destination to a suitable service.' ), array( 'eyebrow' => 'Plan the next step', 'title' => 'A logistics website that speaks in operational decisions.', 'text' => 'The demo focuses on what is moving, where it needs to go, when it matters and what information is needed for a useful quotation.' ) ), 'page_labels' => array( 'about' => 'About', 'services' => 'Services', 'industries' => 'Use cases', 'contact' => 'Contact' ), 'services_eyebrow' => 'What we help with', 'industries_eyebrow' => 'Use cases', 'about_eyebrow' => 'Why this structure works', 'process_eyebrow' => 'How it works', 'process_heading' => 'A clear route from interest to action.', 'faq_heading' => 'Practical questions, answered before they become friction.' ), 'medicine' => array( 'hero_title' => 'Specialist care that is easier to find and book.', 'hero_text' => 'Connect patients with the right doctor, service and appointment route while keeping practical information easy to understand.', 'services_heading' => 'A patient journey organised around the reason for visiting.', 'services' => array( array( 'Doctor finder', 'Browse clinicians by specialty, expertise and availability.' ), array( 'Specialities', 'Explain services, symptoms and referral information clearly.' ), array( 'Appointments', 'Offer a direct route to request or book the right appointment type.' ) ), 'industries_heading' => 'Useful structures for different kinds of patient care.', 'industries' => array( array( 'Primary consultations', 'Clear routes for common concerns and first appointments.' ), array( 'Specialist clinics', 'Detailed service pages with clinician and referral context.' ), array( 'Diagnostics', 'Preparation, appointment and result information in one place.' ), array( 'Follow-up care', 'Explain review appointments and what patients should bring.' ) ), 'about_title' => 'A healthcare website designed to reduce uncertainty before arrival.', 'about_text' => 'The demo keeps clinicians, services, appointment routes and practical patient information connected so people can act with confidence.', 'stats' => array( array( '8', 'demo specialities' ), array( '6', 'clinician profiles' ), array( '3', 'appointment routes' ), array( '1', 'consistent patient information model' ) ), 'process' => array( array( '01', 'Find the right service', 'Start from a specialty, clinician or reason for visiting.' ), array( '02', 'Review practical details', 'Understand preparation, location and appointment type.' ), array( '03', 'Request an appointment', 'Send the relevant details through a clear booking route.' ) ), 'faq' => array( array( 'Can patients choose a clinician?', 'Yes. Doctor profiles can link directly to the relevant appointment route.' ), array( 'Can preparation instructions vary by service?', 'Yes. Each service can show its own preparation and arrival guidance.' ), array( 'Can forms avoid collecting unnecessary information?', 'Yes. Dynamic forms can be kept specific to the appointment purpose.' ) ), 'cta_title' => 'Help patients reach the right care with less friction.', 'cta_text' => 'Connect specialities, clinicians and appointment routes in one consistent experience.', 'hero_slides' => array( array( 'eyebrow' => 'A patient journey organised around the reason fo', 'title' => 'Specialist care that is easier to find and book.', 'text' => 'Connect patients with the right doctor, service and appointment route while keeping practical information easy to understand.' ), array( 'eyebrow' => 'Explore', 'title' => 'Doctor finder', 'text' => 'Browse clinicians by specialty, expertise and availability.' ), array( 'eyebrow' => 'Plan the next step', 'title' => 'A healthcare website designed to reduce uncertainty before arrival.', 'text' => 'The demo keeps clinicians, services, appointment routes and practical patient information connected so people can act with confidence.' ) ), 'page_labels' => array( 'about' => 'About', 'services' => 'Services', 'industries' => 'Use cases', 'contact' => 'Contact' ), 'services_eyebrow' => 'What we help with', 'industries_eyebrow' => 'Use cases', 'about_eyebrow' => 'Why this structure works', 'process_eyebrow' => 'How it works', 'process_heading' => 'A clear route from interest to action.', 'faq_heading' => 'Practical questions, answered before they become friction.' ), 'realestate' => array( 'hero_title' => 'Find a home that fits the way you live.', 'hero_text' => 'Browse properties, understand the detail, arrange a viewing and get useful local context without jumping between disconnected pages.', 'services_heading' => 'A property journey built around real decisions.', 'services' => array( array( 'Property finder', 'Filter by location, price, bedrooms and property type.' ), array( 'Listing detail', 'Show the space, key facts, location and next steps consistently.' ), array( 'Viewings', 'Make it simple to request a viewing or ask the agent a question.' ) ), 'industries_heading' => 'Flexible for different property journeys.', 'industries' => array( array( 'Buying a home', 'Discovery, comparison and viewing routes for buyers.' ), array( 'Renting', 'Availability, tenancy details and practical move-in information.' ), array( 'Selling', 'Valuation, marketing and instruction journeys for vendors.' ), array( 'New developments', 'Structured plots, availability and specification content.' ) ), 'about_title' => 'A property website that keeps context around every listing.', 'about_text' => 'The demo combines discovery, listing detail, local information and agent contact so users can move from browsing to a useful conversation quickly.', 'stats' => array( array( '24', 'demo listings' ), array( '6', 'search filters' ), array( '3', 'property journeys' ), array( '1', 'consistent viewing request' ) ), 'process' => array( array( '01', 'Search', 'Narrow the market by location, price and practical needs.' ), array( '02', 'Explore', 'Review images, facts, floorplan context and local information.' ), array( '03', 'Arrange', 'Request a viewing, valuation or agent callback.' ) ), 'faq' => array( array( 'Can listings show different property statuses?', 'Yes. Availability, under-offer and sold states can be represented clearly.' ), array( 'Can users request a viewing from a property page?', 'Yes. Each listing can connect directly to a structured viewing enquiry.' ), array( 'Can local area content be reused?', 'Yes. Area guides can support multiple listings without duplicating the same copy.' ) ), 'cta_title' => 'Turn property browsing into better conversations.', 'cta_text' => 'Give buyers, renters and vendors a clear path from search to the right agent action.', 'hero_slides' => array( array( 'eyebrow' => 'A property journey built around real decisions.', 'title' => 'Find a home that fits the way you live.', 'text' => 'Browse properties, understand the detail, arrange a viewing and get useful local context without jumping between disconnected pages.' ), array( 'eyebrow' => 'Explore', 'title' => 'Property finder', 'text' => 'Filter by location, price, bedrooms and property type.' ), array( 'eyebrow' => 'Plan the next step', 'title' => 'A property website that keeps context around every listing.', 'text' => 'The demo combines discovery, listing detail, local information and agent contact so users can move from browsing to a useful conversation quickly.' ) ), 'page_labels' => array( 'about' => 'About', 'services' => 'Services', 'industries' => 'Use cases', 'contact' => 'Contact' ), 'services_eyebrow' => 'What we help with', 'industries_eyebrow' => 'Use cases', 'about_eyebrow' => 'Why this structure works', 'process_eyebrow' => 'How it works', 'process_heading' => 'A clear route from interest to action.', 'faq_heading' => 'Practical questions, answered before they become friction.' ), 'restaurant' => array( 'hero_title' => 'A restaurant website that lets the menu do the convincing.', 'hero_text' => 'Show the food, atmosphere, opening details and reservation route without making guests search for the basics.', 'services_heading' => 'Everything guests need to decide where and when to eat.', 'services' => array( array( 'Menus', 'Present dishes, dietary notes and seasonal changes clearly.' ), array( 'Reservations', 'Make table requests simple across dates, times and party sizes.' ), array( 'Private dining', 'Give groups and celebrations a dedicated enquiry route.' ) ), 'industries_heading' => 'Content for different dining occasions.', 'industries' => array( array( 'Lunch & casual dining', 'Fast access to menus, opening times and availability.' ), array( 'Dinner experiences', 'Food, atmosphere and booking details in one story.' ), array( 'Groups & celebrations', 'Menus, capacities and enquiry requirements for larger parties.' ), array( 'Seasonal events', 'Special menus and one-off dates without rebuilding the site.' ) ), 'about_title' => 'A digital front door that feels as considered as the dining room.', 'about_text' => 'The demo puts menus, imagery, practical details and reservations in a simple rhythm so guests can make a decision quickly.', 'stats' => array( array( '4', 'menu sections' ), array( '7', 'days of opening details' ), array( '3', 'booking routes' ), array( '1', 'clear dietary-information pattern' ) ), 'process' => array( array( '01', 'Browse the menu', 'See dishes, prices and dietary information.' ), array( '02', 'Choose the occasion', 'Review dining room, group and seasonal options.' ), array( '03', 'Reserve', 'Request a table or send a private-dining enquiry.' ) ), 'faq' => array( array( 'Can dietary information be shown per dish?', 'Yes. Menu items can include structured dietary labels and notes.' ), array( 'Can seasonal menus be swapped easily?', 'Yes. Reusable menu blocks make short-term changes manageable.' ), array( 'Can large groups use a separate enquiry?', 'Yes. Private dining can have its own fields and capacity information.' ) ), 'cta_title' => 'Make the next reservation feel effortless.', 'cta_text' => 'Let the menu, atmosphere and practical information lead naturally into the right booking route.', 'hero_slides' => array( array( 'eyebrow' => 'Everything guests need to decide where and when ', 'title' => 'A restaurant website that lets the menu do the convincing.', 'text' => 'Show the food, atmosphere, opening details and reservation route without making guests search for the basics.' ), array( 'eyebrow' => 'Explore', 'title' => 'Menus', 'text' => 'Present dishes, dietary notes and seasonal changes clearly.' ), array( 'eyebrow' => 'Plan the next step', 'title' => 'A digital front door that feels as considered as the dining room.', 'text' => 'The demo puts menus, imagery, practical details and reservations in a simple rhythm so guests can make a decision quickly.' ) ), 'page_labels' => array( 'about' => 'About', 'services' => 'Services', 'industries' => 'Use cases', 'contact' => 'Contact' ), 'services_eyebrow' => 'What we help with', 'industries_eyebrow' => 'Use cases', 'about_eyebrow' => 'Why this structure works', 'process_eyebrow' => 'How it works', 'process_heading' => 'A clear route from interest to action.', 'faq_heading' => 'Practical questions, answered before they become friction.' ), 'travel' => array( 'hero_title' => 'Travel that feels considered from the first search.', 'hero_text' => 'Help travellers compare destinations, understand what is included and move into a trip enquiry with the right expectations.', 'services_heading' => 'A clearer path from inspiration to a practical itinerary.', 'services' => array( array( 'Trip finder', 'Browse by destination, style, duration and time of year.' ), array( 'Destination guides', 'Combine useful local context with relevant trips.' ), array( 'Tailored enquiries', 'Capture dates, travellers, interests and flexibility in one request.' ) ), 'industries_heading' => 'Different journeys, one consistent planning system.', 'industries' => array( array( 'City breaks', 'Short trips with transport, neighbourhood and timing context.' ), array( 'Adventure travel', 'Activity level, equipment and itinerary details upfront.' ), array( 'Family holidays', 'Room, pace and child-friendly information easy to compare.' ), array( 'Tailor-made trips', 'A structured starting point for bespoke itinerary design.' ) ), 'about_title' => 'A travel website that balances inspiration with useful detail.', 'about_text' => 'The demo pairs strong destination imagery with dates, inclusions, pace and enquiry routes so inspiration can become a realistic plan.', 'stats' => array( array( '18', 'demo itineraries' ), array( '9', 'destination guides' ), array( '4', 'trip styles' ), array( '1', 'tailored enquiry flow' ) ), 'process' => array( array( '01', 'Explore', 'Start with a destination, season or travel style.' ), array( '02', 'Compare', 'Review pace, inclusions, dates and practical requirements.' ), array( '03', 'Plan', 'Send a structured enquiry or continue into the relevant booking route.' ) ), 'faq' => array( array( 'Can trips show what is included?', 'Yes. Inclusions, exclusions and practical notes can be structured per itinerary.' ), array( 'Can bespoke trips use a different form?', 'Yes. Tailor-made enquiries can collect dates, interests and flexibility.' ), array( 'Can destination guides link to several trips?', 'Yes. Shared destination content can connect to multiple relevant itineraries.' ) ), 'cta_title' => 'Turn travel inspiration into a plan that feels possible.', 'cta_text' => 'Bring destination stories, practical trip details and tailored enquiries into one clear journey.', 'hero_slides' => array( array( 'eyebrow' => 'A clearer path from inspiration to a practical i', 'title' => 'Travel that feels considered from the first search.', 'text' => 'Help travellers compare destinations, understand what is included and move into a trip enquiry with the right expectations.' ), array( 'eyebrow' => 'Explore', 'title' => 'Trip finder', 'text' => 'Browse by destination, style, duration and time of year.' ), array( 'eyebrow' => 'Plan the next step', 'title' => 'A travel website that balances inspiration with useful detail.', 'text' => 'The demo pairs strong destination imagery with dates, inclusions, pace and enquiry routes so inspiration can become a realistic plan.' ) ), 'page_labels' => array( 'about' => 'About', 'services' => 'Services', 'industries' => 'Use cases', 'contact' => 'Contact' ), 'services_eyebrow' => 'What we help with', 'industries_eyebrow' => 'Use cases', 'about_eyebrow' => 'Why this structure works', 'process_eyebrow' => 'How it works', 'process_heading' => 'A clear route from interest to action.', 'faq_heading' => 'Practical questions, answered before they become friction.' ), 'clothes' => array( 'hero_title' => 'Everyday pieces, made to stay in your wardrobe.', 'hero_text' => 'A fashion store demo built around fit, useful product detail and collections that are easy to browse without visual clutter.', 'services_heading' => 'Shopping tools that support confident wardrobe choices.', 'services' => array( array( 'Collection discovery', 'Browse new arrivals, essentials and seasonal edits.' ), array( 'Fit & size guidance', 'Keep measurements, fit notes and product detail close to the choice.' ), array( 'Wishlist & basket', 'Move from inspiration to saved pieces and checkout without losing context.' ) ), 'industries_heading' => 'A merchandising system for the way people actually shop.', 'industries' => array( array( 'Women', 'Editorial collections and practical wardrobe staples.' ), array( 'Men', 'Clear categories, fit information and everyday essentials.' ), array( 'Kids', 'Simple sizing and durable product information for growing wardrobes.' ), array( 'Accessories', 'Add-on products that work across multiple collections.' ) ), 'about_title' => 'A store experience that gives the product enough room to speak.', 'about_text' => 'The demo combines editorial imagery, consistent product cards, useful fit information and a calm checkout path for a more confident purchase.', 'stats' => array( array( '5', 'demo collections' ), array( '3', 'fit-information layers' ), array( '4', 'shopping routes' ), array( '1', 'consistent product card system' ) ), 'process' => array( array( '01', 'Browse', 'Start with a collection, category or useful filter.' ), array( '02', 'Check the fit', 'Review sizing, material and practical product details.' ), array( '03', 'Choose', 'Save, add to basket or continue into checkout.' ) ), 'faq' => array( array( 'Can size information vary by product?', 'Yes. Product pages can carry item-specific fit and measurement guidance.' ), array( 'Can collections reuse the same products?', 'Yes. Products can appear in several editorial or category views without duplicating data.' ), array( 'Can product cards stay equal height?', 'Yes. The shared commerce layout keeps card bodies and actions aligned.' ) ), 'cta_title' => 'Build a calmer path from collection to checkout.', 'cta_text' => 'Use consistent product information, strong imagery and clear shopping actions across the whole store.', 'hero_slides' => array( array( 'eyebrow' => 'Shopping tools that support confident wardrobe c', 'title' => 'Everyday pieces, made to stay in your wardrobe.', 'text' => 'A fashion store demo built around fit, useful product detail and collections that are easy to browse without visual clutter.' ), array( 'eyebrow' => 'Explore', 'title' => 'Collection discovery', 'text' => 'Browse new arrivals, essentials and seasonal edits.' ), array( 'eyebrow' => 'Plan the next step', 'title' => 'A store experience that gives the product enough room to speak.', 'text' => 'The demo combines editorial imagery, consistent product cards, useful fit information and a calm checkout path for a more confident purchase.' ) ), 'page_labels' => array( 'about' => 'About', 'services' => 'Services', 'industries' => 'Use cases', 'contact' => 'Contact' ), 'services_eyebrow' => 'What we help with', 'industries_eyebrow' => 'Use cases', 'about_eyebrow' => 'Why this structure works', 'process_eyebrow' => 'How it works', 'process_heading' => 'A clear route from interest to action.', 'faq_heading' => 'Practical questions, answered before they become friction.' ), 'events' => array( 'hero_title' => 'Events people can discover, book and remember.', 'hero_text' => 'Calendar discovery, clear venue details, useful ticket actions and attendee information in one joined-up event experience.', 'services_heading' => 'The event journey, organised from discovery to attendance.', 'services' => array( array( 'Calendar & discovery', 'Compare dates, categories and venues without losing context.' ), array( 'Tickets & reservations', 'Connect event detail to reservations or WooCommerce ticket products.' ), array( 'Venues & organisers', 'Keep capacity, location and organiser information structured.' ) ), 'industries_heading' => 'Built for programmes with different rhythms and audiences.', 'industries' => array( array( 'Conferences', 'Schedules, speakers, venues and ticket routes for multi-session events.' ), array( 'Music & culture', 'Image-led discovery for performances, exhibitions and evening programmes.' ), array( 'Workshops', 'Capacity-aware sessions with practical attendee requirements.' ), array( 'Community events', 'Accessible discovery for free, paid and local programmes.' ) ), 'about_title' => 'A calmer path from finding an event to arriving prepared.', 'about_text' => 'The demo keeps date, venue, availability, ticket choice and practical attendee information visible across the same event journey.', 'stats' => array( array( '6', 'demo events' ), array( '5', 'venue profiles' ), array( '4', 'event categories' ), array( '1', 'shared ticket workflow' ) ), 'process' => array( array( '01', 'Discover', 'Filter by date, category or venue.' ), array( '02', 'Book', 'Reserve a place or buy the linked ticket product.' ), array( '03', 'Attend', 'Save the date and review the practical details before arrival.' ) ), 'faq' => array( array( 'Can an event use reservations instead of WooCommerce?', 'Yes. Events can use a native reservation route or a linked ticket product.' ), array( 'Can each event have its own gallery?', 'Yes. Event cards and detail pages can use event-specific galleries.' ), array( 'Can venue information be reused?', 'Yes. Venue and organiser records can support multiple events without duplicating the same details.' ) ), 'cta_title' => 'Give every event a useful route to attendance.', 'cta_text' => 'Connect discovery, venue information and ticket actions in a structure organisers can keep up to date.', 'hero_slides' => array( array( 'eyebrow' => 'The event journey, organised from discovery to a', 'title' => 'Events people can discover, book and remember.', 'text' => 'Calendar discovery, clear venue details, useful ticket actions and attendee information in one joined-up event experience.' ), array( 'eyebrow' => 'Explore', 'title' => 'Calendar & discovery', 'text' => 'Compare dates, categories and venues without losing context.' ), array( 'eyebrow' => 'Plan the next step', 'title' => 'A calmer path from finding an event to arriving prepared.', 'text' => 'The demo keeps date, venue, availability, ticket choice and practical attendee information visible across the same event journey.' ) ), 'page_labels' => array( 'about' => 'About', 'services' => 'Services', 'industries' => 'Use cases', 'contact' => 'Contact' ), 'services_eyebrow' => 'What we help with', 'industries_eyebrow' => 'Use cases', 'about_eyebrow' => 'Why this structure works', 'process_eyebrow' => 'How it works', 'process_heading' => 'A clear route from interest to action.', 'faq_heading' => 'Practical questions, answered before they become friction.' ), 'tech' => array( 'hero_title' => 'Better technology for work and home.', 'hero_text' => 'A specialist technology store with clear comparisons, practical buying advice and product pages that help people choose with confidence.', 'services_heading' => 'Useful buying tools around the catalogue, not on top of it.', 'services' => array( array( 'Product search', 'Find devices by category, feature, price and intended use.' ), array( 'Buying advice', 'Explain the differences that matter before customers compare models.' ), array( 'Setup & support', 'Keep compatibility, setup and aftercare information connected to the product.' ) ), 'industries_heading' => 'Product journeys organised around what customers are trying to do.', 'industries' => array( array( 'Home working', 'Laptops, displays, peripherals and connectivity for focused setups.' ), array( 'Creative work', 'Devices and storage selected around performance and workflow.' ), array( 'Smart home', 'Compatible products grouped by everyday use and ecosystem.' ), array( 'Small business', 'Reliable hardware bundles and support routes for teams.' ) ), 'about_title' => 'A tech store that makes specifications easier to use.', 'about_text' => 'The demo combines product discovery, comparison, compatibility notes and buying advice so technical detail supports a decision instead of overwhelming it.', 'stats' => array( array( '24', 'demo products' ), array( '8', 'buying-guide topics' ), array( '4', 'use-case collections' ), array( '1', 'consistent comparison language' ) ), 'process' => array( array( '01', 'Find', 'Start from a category, use case or product search.' ), array( '02', 'Compare', 'Review specifications, compatibility and practical differences.' ), array( '03', 'Buy with confidence', 'Add to basket or quote with the right support information nearby.' ) ), 'faq' => array( array( 'Can products be compared by specifications?', 'Yes. Structured attributes can support practical comparison views.' ), array( 'Can buying guides link directly to products?', 'Yes. Editorial guidance can surface relevant products without duplicating product data.' ), array( 'Can business customers request a quote?', 'Yes. Quote actions can sit alongside normal basket purchasing where appropriate.' ) ), 'cta_title' => 'Make technical choices feel simpler.', 'cta_text' => 'Use better product structure, useful buying advice and a clean commerce journey from search to support.', 'hero_slides' => array( array( 'eyebrow' => 'Useful buying tools around the catalogue, not on', 'title' => 'Better technology for work and home.', 'text' => 'A specialist technology store with clear comparisons, practical buying advice and product pages that help people choose with confidence.' ), array( 'eyebrow' => 'Explore', 'title' => 'Product search', 'text' => 'Find devices by category, feature, price and intended use.' ), array( 'eyebrow' => 'Plan the next step', 'title' => 'A tech store that makes specifications easier to use.', 'text' => 'The demo combines product discovery, comparison, compatibility notes and buying advice so technical detail supports a decision instead of overwhelming it.' ) ), 'page_labels' => array( 'about' => 'About', 'services' => 'Services', 'industries' => 'Use cases', 'contact' => 'Contact' ), 'services_eyebrow' => 'What we help with', 'industries_eyebrow' => 'Use cases', 'about_eyebrow' => 'Why this structure works', 'process_eyebrow' => 'How it works', 'process_heading' => 'A clear route from interest to action.', 'faq_heading' => 'Practical questions, answered before they become friction.' ) );
        return isset( $packs[ $id ] ) && is_array( $packs[ $id ] ) ? $packs[ $id ] : array();
    }
}

if ( ! function_exists( 'wpbb_child_v62_profile' ) ) {
    function wpbb_child_v62_profile() {
        static $profile = null;
        if ( is_array( $profile ) ) return $profile;
        $profile = apply_filters( 'wp_theme_demo_profile', array() );
        $profile = is_array( $profile ) ? $profile : array();
        $id = (string) ( $profile['id'] ?? '' );
        $pack = wpbb_child_v64_content_pack( $id );
        if ( $pack ) $profile = array_replace_recursive( $profile, $pack );

        $extras = array(
            'automotive' => array( 'gallery_heading'=>'Vehicles, workshop details and ownership moments in context.', 'contact_heading'=>'Talk to the right automotive team.', 'contact_text'=>'Tell us whether you are buying, servicing, renting or looking for a compatible part.', 'priorities_heading'=>'What a confident vehicle journey needs.' ),
            'building' => array( 'gallery_heading'=>'Projects, trades and finished spaces that show the work clearly.', 'contact_heading'=>'Tell us what needs building, fixing or maintaining.', 'contact_text'=>'Share the property type, location, scope and timing so the enquiry reaches the right trade.', 'priorities_heading'=>'What makes a building-services enquiry useful.' ),
            'business' => array( 'gallery_heading'=>'Strategy, delivery and measurable outcomes from real client work.', 'contact_heading'=>'Start with the business problem, not a generic contact form.', 'contact_text'=>'Tell us what needs to change, who it affects and what a useful outcome looks like.', 'priorities_heading'=>'What keeps a professional service website credible.' ),
            'courses' => array( 'gallery_heading'=>'Learning moments, course materials and progress made visible.', 'contact_heading'=>'Ask about the right course or learning route.', 'contact_text'=>'Tell us the subject, learner level and outcome you are working towards.', 'priorities_heading'=>'What helps learners choose and keep moving.' ),
            'hotel' => array( 'gallery_heading'=>'Rooms, shared spaces and local details guests want to see before booking.', 'contact_heading'=>'Plan a stay with the details already clear.', 'contact_text'=>'Ask about dates, rooms, accessibility, group stays or practical arrival information.', 'priorities_heading'=>'What makes a hotel journey feel reassuring.' ),
            'insurance' => array( 'gallery_heading'=>'Cover, claims support and customer guidance presented without clutter.', 'contact_heading'=>'Get the right insurance question to the right team.', 'contact_text'=>'Tell us whether you need a quote, policy help, renewal support or claims guidance.', 'priorities_heading'=>'What builds confidence around cover.' ),
            'logistics' => array( 'gallery_heading'=>'Freight, vehicles, warehousing and hand-offs across the delivery journey.', 'contact_heading'=>'Describe the load, route and timing.', 'contact_text'=>'Share collection, destination, cargo and service requirements for a useful logistics response.', 'priorities_heading'=>'What makes a logistics enquiry actionable.' ),
            'medicine' => array( 'gallery_heading'=>'Care settings, specialist services and patient information in a calmer system.', 'contact_heading'=>'Find the right route to care or information.', 'contact_text'=>'Choose the relevant service and share only the practical details needed for a response.', 'priorities_heading'=>'What helps healthcare information feel clear and safe.' ),
            'realestate' => array( 'gallery_heading'=>'Homes, neighbourhoods and viewing details shown with enough context to decide.', 'contact_heading'=>'Talk about the property, move or viewing you need.', 'contact_text'=>'Tell us whether you are buying, selling, letting or arranging a viewing.', 'priorities_heading'=>'What makes a property journey easier to trust.' ),
            'restaurant' => array( 'gallery_heading'=>'Food, tables and atmosphere that help guests choose the right visit.', 'contact_heading'=>'Plan a table, group booking or event.', 'contact_text'=>'Share the date, party size and any dining requirements so the team can respond usefully.', 'priorities_heading'=>'What turns restaurant browsing into a confident booking.' ),
            'travel' => array( 'gallery_heading'=>'Destinations, stays and trip details that make planning feel tangible.', 'contact_heading'=>'Tell us the trip you have in mind.', 'contact_text'=>'Share dates, destination ideas, traveller numbers and the kind of experience you want.', 'priorities_heading'=>'What makes travel planning feel simpler.' ),
            'clothes' => array( 'gallery_heading'=>'Fits, fabrics and everyday styling shown beyond a product thumbnail.', 'contact_heading'=>'Need help with fit, stock or an order?', 'contact_text'=>'Choose the product or order topic and give the team the details needed to help.', 'priorities_heading'=>'What makes fashion commerce easier to browse.' ),
            'events' => array( 'gallery_heading'=>'Stages, audiences, workshops and venues behind the event programme.', 'contact_heading'=>'Plan an event, booking or group requirement.', 'contact_text'=>'Tell us the event, date, venue or ticket question and we will route it to the right person.', 'priorities_heading'=>'What makes event discovery and attendance feel connected.' ),
            'tech' => array( 'gallery_heading'=>'Technology in the workspaces and everyday setups it is actually chosen for.', 'contact_heading'=>'Need help choosing, setting up or supporting a product?', 'contact_text'=>'Tell us the device, use case or order question so the right product or support route is clear.', 'priorities_heading'=>'What makes technical buying advice genuinely useful.' ),
        );
        if ( isset( $extras[ $id ] ) ) $profile = array_replace_recursive( $profile, $extras[ $id ] );
        return $profile;
    }
}

if ( ! function_exists( 'wpbb_child_v62_url' ) ) {
    function wpbb_child_v62_url( $key, $fallback = '#' ) {
        if ( function_exists( 'wp_theme_demo_page_url' ) ) {
            $url = wp_theme_demo_page_url( $key );
            if ( $url ) return $url;
        }
        return $fallback;
    }
}

if ( ! function_exists( 'wpbb_child_v62_section_heading' ) ) {
    function wpbb_child_v62_section_heading( $eyebrow, $heading, $class = '' ) {
        $inner = wpbb_child_v62_paragraph( $eyebrow, 'wp-theme-sector-eyebrow' ) . wpbb_child_v62_heading( $heading, 2 );
        return wpbb_child_v62_block( 'wpbb/row', array( 'customClasses' => trim( 'wp-theme-section-heading ' . $class ), 'gutterX' => 'gx-4', 'gutterY' => 'gy-3' ),
            wpbb_child_v62_block( 'wpbb/column', array( 'xs' => 12 ), $inner )
        );
    }
}


/**
 * v3.8.10.67 full-width section shell with a real Bootstrap container inside.
 * This mirrors the tested sector-demo structure: section backgrounds span the
 * viewport while text, cards and media stay on the same content grid.
 */
if ( ! function_exists( 'wpbb_child_v67_section' ) ) {
    function wpbb_child_v67_section( $utility_classes, $inner, $class_name = 'wpbb-v64-section' ) {
        $outer_classes = trim( 'wpbb-v67-section-shell px-0 ' . (string) $utility_classes );
        $inner_block = wpbb_child_v62_block(
            'wpbb/bootstrap-div',
            array( 'containerClass' => 'container', 'utilityClasses' => 'wpbb-v67-section-inner' ),
            $inner
        );
        return wpbb_child_v62_block(
            'wpbb/bootstrap-div',
            array( 'containerClass' => 'container-fluid', 'utilityClasses' => $outer_classes, 'className' => (string) $class_name ),
            $inner_block
        );
    }
}

if ( ! function_exists( 'wpbb_child_v62_cards_row' ) ) {
    function wpbb_child_v62_cards_row( $items, $class = '', $max_columns = 3, $link_text = '', $link_url = '' ) {
        $items = is_array( $items ) ? $items : array();
        if ( ! $items ) return '';
        $count = count( $items );
        $lg = $max_columns >= 4 ? 3 : ( $max_columns === 3 ? 4 : ( $max_columns === 2 ? 6 : 12 ) );
        if ( $count === 2 ) $lg = 6;
        if ( $count === 1 ) $lg = 12;
        $cols = '';
        foreach ( $items as $item ) {
            $title = is_array( $item ) ? (string) ( $item[0] ?? '' ) : '';
            $text  = is_array( $item ) ? (string) ( $item[1] ?? '' ) : '';
            $card = wpbb_child_v62_icon_card( $title, $text, $class, $link_text, $link_url );
            $cols .= wpbb_child_v62_block( 'wpbb/column', array( 'xs' => 12, 'md' => 6, 'lg' => $lg, 'customClasses' => 'd-flex' ), $card );
        }
        return wpbb_child_v62_block( 'wpbb/row', array( 'customClasses' => 'wpbb-v62-card-grid', 'gutterX' => 'gx-4', 'gutterY' => 'gy-4' ), $cols );
    }
}


/**
 * v3.8.10.65 demo media pool.
 *
 * Prefer explicit profile media, then fill the slider/gallery from the active
 * child theme's own photo assets. This prevents BBuilder's generic fallback
 * slides from appearing after a demo repair and keeps every sector visual.
 */
if ( ! function_exists( 'wpbb_child_v63_demo_image_pool' ) ) {
    function wpbb_child_v63_demo_image_pool( $profile, $limit = 8 ) {
        $urls = array();
        $push = static function( $url ) use ( &$urls ) {
            $url = trim( (string) $url );
            if ( '' !== $url && ! in_array( $url, $urls, true ) ) $urls[] = $url;
        };
        $push( $profile['hero_image'] ?? '' );
        $push( $profile['about_image'] ?? '' );
        foreach ( (array) ( $profile['hero_slides'] ?? array() ) as $slide ) {
            if ( is_array( $slide ) ) $push( $slide['image'] ?? '' );
        }
        foreach ( (array) ( $profile['gallery_images'] ?? array() ) as $image ) $push( $image );

        $root = trailingslashit( get_stylesheet_directory() ) . 'assets/img';
        $base = trailingslashit( get_stylesheet_directory_uri() ) . 'assets/img/';
        if ( is_dir( $root ) ) {
            try {
                $iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $root, FilesystemIterator::SKIP_DOTS ) );
                foreach ( $iterator as $file ) {
                    if ( count( $urls ) >= max( 3, (int) $limit ) ) break;
                    if ( ! $file->isFile() ) continue;
                    $ext = strtolower( (string) $file->getExtension() );
                    if ( ! in_array( $ext, array( 'jpg', 'jpeg', 'png', 'webp', 'avif' ), true ) ) continue;
                    $name = strtolower( $file->getBasename() );
                    if ( preg_match( '/(?:logo|icon|favicon|flag|avatar|placeholder|screenshot|sprite|qr|badge)/', $name ) ) continue;
                    $relative = ltrim( str_replace( array( $root, DIRECTORY_SEPARATOR ), array( '', '/' ), $file->getPathname() ), '/' );
                    $push( $base . $relative );
                }
            } catch ( Exception $e ) {
                // A missing/unreadable optional asset directory must never break the demo importer.
            }
        }
        return array_slice( $urls, 0, max( 1, (int) $limit ) );
    }
}

if ( ! function_exists( 'wpbb_child_v63_demo_slides' ) ) {
    function wpbb_child_v63_demo_slides( $profile ) {
        $images = wpbb_child_v63_demo_image_pool( $profile, 6 );
        $primary_label = (string) ( $profile['primary_label'] ?? __( 'Explore', 'wp-bbuilder' ) );
        $primary_url = (string) ( $profile['primary_url'] ?? '#' );
        $secondary_label = (string) ( $profile['secondary_label'] ?? '' );
        $secondary_url = (string) ( $profile['secondary_url'] ?? '' );
        $slides = array();

        foreach ( (array) ( $profile['hero_slides'] ?? array() ) as $slide ) {
            if ( ! is_array( $slide ) ) continue;
            if ( empty( $slide['image'] ) && $images ) $slide['image'] = $images[ count( $slides ) % count( $images ) ];
            $eyebrow = trim( (string) ( $slide['eyebrow'] ?? '' ) );
            if ( '' === $eyebrow || strlen( $eyebrow ) > 38 ) $eyebrow = (string) ( $profile['eyebrow'] ?? $profile['services_eyebrow'] ?? __( 'Featured', 'wp-bbuilder' ) );
            if ( 'Explore' === $eyebrow ) $eyebrow = (string) ( $profile['services_eyebrow'] ?? $profile['eyebrow'] ?? $eyebrow );
            if ( 'Plan the next step' === $eyebrow ) $eyebrow = (string) ( $profile['about_eyebrow'] ?? $profile['eyebrow'] ?? $eyebrow );
            $slide['eyebrow'] = $eyebrow;
            $slide['type'] = 'hero';
            $slides[] = $slide;
        }

        $service = array_values( (array) ( $profile['services'] ?? array() ) );
        $industry = array_values( (array) ( $profile['industries'] ?? array() ) );
        $service_title = is_array( $service[0] ?? null ) ? (string) ( $service[0][0] ?? '' ) : (string) ( $service[0] ?? '' );
        $service_text = is_array( $service[0] ?? null ) ? (string) ( $service[0][1] ?? '' ) : '';
        $industry_title = is_array( $industry[0] ?? null ) ? (string) ( $industry[0][0] ?? '' ) : (string) ( $industry[0] ?? '' );
        $industry_text = is_array( $industry[0] ?? null ) ? (string) ( $industry[0][1] ?? '' ) : '';

        $fallbacks = array(
            array(
                'eyebrow' => (string) ( $profile['eyebrow'] ?? '' ),
                'title' => (string) ( $profile['hero_title'] ?? '' ),
                'text' => (string) ( $profile['hero_text'] ?? '' ),
                'buttonText' => $primary_label, 'buttonUrl' => $primary_url,
                'secondaryText' => $secondary_label, 'secondaryUrl' => $secondary_url,
            ),
            array(
                'eyebrow' => (string) ( $profile['services_eyebrow'] ?? __( 'What we do', 'wp-bbuilder' ) ),
                'title' => (string) ( $profile['services_heading'] ?? $service_title ),
                'text' => $service_text ?: (string) ( $profile['hero_text'] ?? '' ),
                'buttonText' => $secondary_label ?: $primary_label,
                'buttonUrl' => $secondary_url ?: $primary_url,
            ),
            array(
                'eyebrow' => (string) ( $profile['about_eyebrow'] ?? __( 'Why it works', 'wp-bbuilder' ) ),
                'title' => (string) ( $profile['about_title'] ?? $industry_title ?: $profile['hero_title'] ?? '' ),
                'text' => (string) ( $profile['about_text'] ?? $industry_text ?: $profile['hero_text'] ?? '' ),
                'buttonText' => $primary_label, 'buttonUrl' => $primary_url,
            ),
        );

        $target = max( 3, min( 4, count( $images ) ?: 3 ) );
        for ( $i = count( $slides ); $i < $target; $i++ ) {
            $seed = $fallbacks[ $i % count( $fallbacks ) ];
            $seed['type'] = 'hero';
            if ( $images ) $seed['image'] = $images[ $i % count( $images ) ];
            $slides[] = $seed;
        }
        return array_slice( $slides, 0, 4 );
    }
}

if ( ! function_exists( 'wpbb_child_v62_front_content' ) ) {
    function wpbb_child_v62_front_content( $profile ) {
        $brand = (string) ( $profile['palette']['theme_brand_color'] ?? '#4B3FCE' );
        $surface = (string) ( $profile['palette']['theme_surface_color'] ?? '#ffffff' );
        $hero_image = (string) ( $profile['hero_image'] ?? '' );
        $about_image = (string) ( $profile['about_image'] ?? $hero_image );
        $primary_label = (string) ( $profile['primary_label'] ?? __( 'Explore', 'wp-bbuilder' ) );
        $primary_url = (string) ( $profile['primary_url'] ?? '#' );
        $secondary_label = (string) ( $profile['secondary_label'] ?? '' );
        $secondary_url = (string) ( $profile['secondary_url'] ?? '' );

        // Always materialise a visual, sector-specific hero slider. A previous
        // generic recovery path could leave self-closing Swiper placeholders,
        // which then rendered BBuilder's stock text-only demo slides.
        $slides = wpbb_child_v63_demo_slides( $profile );
        $content = wpbb_child_v62_block( 'wpbb/row', array( 'containerClass' => 'container-fluid', 'customClasses' => 'wp-theme-sector-hero wpbb-v62-hero', 'gutterX' => 'gx-0', 'gutterY' => 'gy-0' ),
            wpbb_child_v62_block( 'wpbb/column', array( 'xs' => 12 ), wpbb_child_v62_block( 'wpbb/swiper', array( 'slides' => $slides, 'slidesJson' => wp_json_encode( $slides, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ), 'slidesPerView' => 1, 'slidesTablet' => 1, 'slidesMobile' => 1, 'spaceBetween' => 0, 'speed' => 700, 'loop' => count( $slides ) > 1, 'rewind' => true, 'autoplay' => true, 'autoplayDelay' => 8500, 'pauseOnHover' => true, 'demoStyle' => 'hero', 'showPagination' => true, 'showNavigation' => true ), '', true ) )
        );

        // Optional sector-specific dynamic block immediately after the hero.
        if ( 'events' === ( $profile['id'] ?? '' ) && shortcode_exists( 'wpbb_event_calendar' ) ) {
            $content .= wpbb_child_v67_section(
                'wp-theme-section-shell wpbb-events-home-calendar',
                wpbb_child_v62_block( 'wpbb/row', array( 'gutterX' => 'gx-0', 'gutterY' => 'gy-0' ),
                    wpbb_child_v62_block( 'wpbb/column', array( 'xs' => 12 ), wpbb_child_v62_block( 'shortcode', array(), '[wpbb_event_calendar view="list" limit="6"]' ) )
                )
            );
        }

        $services = (array) ( $profile['services'] ?? array() );
        if ( $services ) {
            $inner = wpbb_child_v62_section_heading( (string) ( $profile['services_eyebrow'] ?? __( 'Services', 'wp-bbuilder' ) ), (string) ( $profile['services_heading'] ?? __( 'Purpose-built sections, consistent structure.', 'wp-bbuilder' ) ) );
            $inner .= wpbb_child_v62_cards_row( $services, 'wp-theme-sector-card motion-fade-up', 3 );
            $content .= wpbb_child_v67_section( 'wp-theme-section-shell wp-theme-services-section', $inner );
        }

        if ( $about_image || ! empty( $profile['about_title'] ) ) {
            $left = wpbb_child_v62_image( $about_image, 'wp-theme-sector-media-text__media' );
            $right = wpbb_child_v62_paragraph( (string) ( $profile['about_eyebrow'] ?? __( 'About', 'wp-bbuilder' ) ), 'wp-theme-sector-eyebrow' );
            $right .= wpbb_child_v62_heading( (string) ( $profile['about_title'] ?? $profile['hero_title'] ?? '' ), 2 );
            $right .= wpbb_child_v62_paragraph( (string) ( $profile['about_text'] ?? $profile['hero_text'] ?? '' ) );
            if ( $secondary_label && $secondary_url ) {
                $right .= wpbb_child_v62_block( 'wpbb/button', array( 'text' => $secondary_label, 'url' => $secondary_url, 'btnClass' => 'btn btn-primary' ), '', true );
            }
            $about_row = wpbb_child_v62_block( 'wpbb/row', array( 'customClasses' => 'align-items-start wp-theme-sector-media-text', 'gutterX' => 'gx-5', 'gutterY' => 'gy-5' ),
                wpbb_child_v62_block( 'wpbb/column', array( 'xs' => 12, 'lg' => 6 ), $left ) .
                wpbb_child_v62_block( 'wpbb/column', array( 'xs' => 12, 'lg' => 6 ), $right )
            );
            $content .= wpbb_child_v67_section( 'wp-theme-section-shell wp-theme-about-section', $about_row );
        }

        $industries = (array) ( $profile['industries'] ?? array() );
        if ( $industries ) {
            $inner = wpbb_child_v62_section_heading( (string) ( $profile['industries_eyebrow'] ?? __( 'Solutions', 'wp-bbuilder' ) ), (string) ( $profile['industries_heading'] ?? __( 'Built around real use cases.', 'wp-bbuilder' ) ) );
            $inner .= wpbb_child_v62_cards_row( $industries, 'wp-theme-sector-card motion-fade-up', 4 );
            $content .= wpbb_child_v67_section( 'wp-theme-section-shell wp-theme-industries-section', $inner );
        }

        $stats = (array) ( $profile['stats'] ?? array() );
        if ( $stats ) {
            $cols = '';
            foreach ( array_slice( $stats, 0, 4 ) as $stat ) {
                $cols .= wpbb_child_v62_block( 'wpbb/column', array( 'xs' => 6, 'lg' => 3 ), wpbb_child_v62_block( 'wpbb/fun-fact', array( 'number' => (string) ( $stat[0] ?? '' ), 'label' => (string) ( $stat[1] ?? '' ), 'styleVariant' => 'sector-proof', 'className' => 'wp-theme-sector-proof__item' ), '', true ) );
            }
            $content .= wpbb_child_v67_section( 'wp-theme-home-stats', wpbb_child_v62_block( 'wpbb/row', array( 'gutterX' => 'gx-3', 'gutterY' => 'gy-3' ), $cols ) );
        }

        // Gallery/portfolio carousel uses the same sector-specific media, not abstract placeholders.
        $gallery_slides = array();
        foreach ( array_slice( $slides, 0, 4 ) as $slide ) {
            if ( empty( $slide['image'] ) ) continue;
            $gallery_slides[] = array( 'type' => 'gallery', 'eyebrow' => __( 'Gallery', 'wp-bbuilder' ), 'title' => (string) ( $slide['title'] ?? '' ), 'text' => '', 'image' => (string) $slide['image'] );
        }
        if ( count( $gallery_slides ) < 2 && $about_image ) $gallery_slides[] = array( 'type'=>'gallery', 'eyebrow'=>__( 'Gallery', 'wp-bbuilder' ), 'title'=>(string)($profile['about_title']??''), 'text'=>'', 'image'=>$about_image );
        if ( $gallery_slides ) {
            $inner = wpbb_child_v62_section_heading( (string) ( $profile['gallery_eyebrow'] ?? __( 'Gallery', 'wp-bbuilder' ) ), (string) ( $profile['gallery_heading'] ?? __( 'A closer look at the work, people and places behind the service.', 'wp-bbuilder' ) ) );
            $inner .= wpbb_child_v62_block( 'wpbb/row', array(), wpbb_child_v62_block( 'wpbb/column', array( 'xs' => 12 ), wpbb_child_v62_block( 'wpbb/swiper', array( 'slides' => $gallery_slides, 'slidesJson' => wp_json_encode( $gallery_slides, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ), 'slidesPerView' => 3, 'slidesTablet' => 2, 'slidesMobile' => 1, 'spaceBetween' => 22, 'speed' => 650, 'demoStyle' => 'gallery', 'showNavigation' => false ), '', true ) ) );
            $content .= wpbb_child_v67_section( 'wp-theme-section-shell wp-theme-gallery-section', $inner );
        }

        if ( ! empty( $profile['commerce'] ) ) {
            $inner = wpbb_child_v62_section_heading( (string) ( $profile['shop_eyebrow'] ?? __( 'Shop', 'wp-bbuilder' ) ), (string) ( $profile['shop_heading'] ?? __( 'Find the right product without the noise.', 'wp-bbuilder' ) ) );
            $inner .= wpbb_child_v62_block( 'wpbb/row', array(), wpbb_child_v62_block( 'wpbb/column', array( 'xs' => 12 ), wpbb_child_v62_block( 'wpbb/catalogue', array( 'title' => '', 'postsToShow' => 8, 'postType' => 'product', 'taxonomy' => 'product_cat', 'sortBy' => 'menu_order', 'sortOrder' => 'ASC', 'className' => 'wp-theme-home-product-catalogue' ), '', true ) ) );
            $content .= wpbb_child_v67_section( 'wp-theme-section-shell wp-theme-shop-section', $inner );
        }

        $process = (array) ( $profile['process'] ?? array() );
        if ( $process ) {
            $cards = array();
            foreach ( $process as $item ) $cards[] = array( trim( (string) ( $item[0] ?? '' ) . ' ' . (string) ( $item[1] ?? '' ) ), (string) ( $item[2] ?? '' ) );
            $inner = wpbb_child_v62_section_heading( (string) ( $profile['process_eyebrow'] ?? __( 'How it works', 'wp-bbuilder' ) ), (string) ( $profile['process_heading'] ?? __( 'A clear next step.', 'wp-bbuilder' ) ) );
            $inner .= wpbb_child_v62_cards_row( $cards, 'wp-theme-sector-card wp-theme-process-card motion-fade-up', 3 );
            $content .= wpbb_child_v67_section( 'wp-theme-section-shell wp-theme-process-section', $inner );
        }

        $faq_heading = (string) ( $profile['faq_heading'] ?? '' );
        if ( $faq_heading ) {
            $details = '';
            $faq_items = ! empty( $profile['faq'] ) && is_array( $profile['faq'] ) ? $profile['faq'] : array();
            foreach ( $faq_items as $faq ) {
                $details .= wpbb_child_v62_block( 'details', array(), '<details class="wp-block-details"><summary>' . esc_html( $faq[0] ) . '</summary>' . wpbb_child_v62_paragraph( $faq[1] ) . '</details>' );
            }
            $faq_inner = wpbb_child_v62_paragraph( __( 'FAQ', 'wp-bbuilder' ), 'wp-theme-sector-eyebrow' ) . wpbb_child_v62_heading( $faq_heading, 2 ) . $details;
            $content .= wpbb_child_v67_section( 'wp-theme-section-shell wp-theme-faq-section', wpbb_child_v62_block( 'wpbb/row', array(), wpbb_child_v62_block( 'wpbb/column', array( 'xs' => 12 ), $faq_inner ) ) );
        }

        if ( ! empty( $profile['cta_title'] ) ) {
            $content .= wpbb_child_v62_block( 'wpbb/cta-section', array( 'title' => (string) $profile['cta_title'], 'text' => (string) ( $profile['cta_text'] ?? '' ), 'buttonText' => __( 'Start a conversation', 'wp-bbuilder' ), 'buttonUrl' => wpbb_child_v62_url( 'contact', '#' ), 'className' => 'wp-theme-home-cta wp-theme-home-cta-bbuilder' ), '', true );
        }
        return $content;
    }
}

if ( ! function_exists( 'wpbb_child_v64_cta' ) ) {
    function wpbb_child_v64_cta( $profile ) {
        if ( empty( $profile['cta_title'] ) ) return '';
        return wpbb_child_v62_block( 'wpbb/cta-section', array(
            'title' => (string) $profile['cta_title'],
            'text' => (string) ( $profile['cta_text'] ?? '' ),
            'buttonText' => __( 'Start a conversation', 'wp-bbuilder' ),
            'buttonUrl' => wpbb_child_v62_url( 'contact', '#' ),
            'className' => 'wp-theme-home-cta wp-theme-home-cta-bbuilder'
        ), '', true );
    }
}

if ( ! function_exists( 'wpbb_child_v64_stats_row' ) ) {
    function wpbb_child_v64_stats_row( $stats ) {
        $cols = '';
        foreach ( array_slice( (array) $stats, 0, 4 ) as $stat ) {
            $cols .= wpbb_child_v62_block( 'wpbb/column', array( 'xs'=>6, 'lg'=>3 ),
                wpbb_child_v62_block( 'wpbb/fun-fact', array(
                    'number'=>(string)($stat[0]??''), 'label'=>(string)($stat[1]??''),
                    'styleVariant'=>'sector-proof', 'className'=>'wp-theme-sector-proof__item'
                ), '', true )
            );
        }
        return $cols ? wpbb_child_v62_block( 'wpbb/row', array( 'gutterX'=>'gx-3','gutterY'=>'gy-3' ), $cols ) : '';
    }
}

if ( ! function_exists( 'wpbb_child_v64_contact_fields' ) ) {
    function wpbb_child_v64_contact_fields( $profile ) {
        $service_options = array();
        foreach ( (array) ( $profile['services'] ?? array() ) as $service ) if ( is_array($service) && !empty($service[0]) ) $service_options[] = (string)$service[0];
        return array(
            array('type'=>'text','name'=>'name','label'=>__('Name','wp-bbuilder'),'required'=>true,'width'=>6,'breakpoint'=>'md'),
            array('type'=>'email','name'=>'email','label'=>__('Email','wp-bbuilder'),'required'=>true,'width'=>6,'breakpoint'=>'md'),
            array('type'=>'phone','name'=>'phone','label'=>__('Phone','wp-bbuilder'),'required'=>false,'width'=>6,'breakpoint'=>'md'),
            array('type'=>'select','name'=>'topic','label'=>__('What can we help with?','wp-bbuilder'),'required'=>true,'width'=>6,'breakpoint'=>'md','options'=>implode("\n",$service_options)),
            array('type'=>'textarea','name'=>'message','label'=>__('Tell us a little about what you need','wp-bbuilder'),'required'=>true,'width'=>12,'breakpoint'=>'md'),
        );
    }
}

if ( ! function_exists( 'wpbb_child_v62_simple_page_content' ) ) {
    function wpbb_child_v62_simple_page_content( $profile, $key ) {
        $label = (string) ( $profile['page_labels'][ $key ] ?? ucfirst( $key ) );
        $content = '';
        $about_image = (string) ( $profile['about_image'] ?? $profile['hero_image'] ?? '' );

        if ( 'services' === $key ) {
            $content .= wpbb_child_v67_section( 'wp-theme-section-shell wpbb-v64-page-hero',
                wpbb_child_v62_section_heading( (string)($profile['services_eyebrow']??$label), (string)($profile['services_heading']??$label) ) );
            $content .= wpbb_child_v67_section( 'wp-theme-section-shell wp-theme-services-section',
                wpbb_child_v62_cards_row( (array)($profile['services']??array()), 'wp-theme-sector-card motion-fade-up', 3 ) );
            $media = wpbb_child_v62_block('wpbb/row',array('customClasses'=>'align-items-center','gutterX'=>'gx-5','gutterY'=>'gy-5'),
                wpbb_child_v62_block('wpbb/column',array('xs'=>12,'lg'=>6),wpbb_child_v62_image($about_image,'wp-theme-sector-media-text__media')) .
                wpbb_child_v62_block('wpbb/column',array('xs'=>12,'lg'=>6),wpbb_child_v62_paragraph((string)($profile['about_eyebrow']??__('Approach','wp-bbuilder')),'wp-theme-sector-eyebrow').wpbb_child_v62_heading((string)($profile['about_title']??''),2).wpbb_child_v62_paragraph((string)($profile['about_text']??'')))
            );
            $content .= wpbb_child_v67_section( 'wp-theme-section-shell wp-theme-about-section', $media );
            if ( !empty($profile['process']) ) {
                $cards=array(); foreach((array)$profile['process'] as $p) $cards[]=array(trim((string)($p[0]??'').' '.(string)($p[1]??'')),(string)($p[2]??''));
                $content .= wpbb_child_v67_section( 'wp-theme-section-shell wp-theme-process-section', wpbb_child_v62_section_heading((string)($profile['process_eyebrow']??__('How it works','wp-bbuilder')),(string)($profile['process_heading']??'')) . wpbb_child_v62_cards_row($cards,'wp-theme-sector-card wp-theme-process-card',3));
            }
            $content .= wpbb_child_v64_cta($profile);
        } elseif ( 'industries' === $key ) {
            $content .= wpbb_child_v67_section( 'wp-theme-section-shell wpbb-v64-page-hero', wpbb_child_v62_section_heading((string)($profile['industries_eyebrow']??$label),(string)($profile['industries_heading']??$label)));
            $content .= wpbb_child_v67_section( 'wp-theme-section-shell wp-theme-industries-section', wpbb_child_v62_cards_row((array)($profile['industries']??array()),'wp-theme-sector-card motion-fade-up',4));
            if (!empty($profile['stats'])) $content .= wpbb_child_v67_section( 'wp-theme-section-shell wp-theme-home-stats', wpbb_child_v64_stats_row($profile['stats']) );
            $content .= wpbb_child_v64_cta($profile);
        } elseif ( 'about' === $key ) {
            $content .= wpbb_child_v67_section( 'wp-theme-section-shell wpbb-v64-page-hero', wpbb_child_v62_section_heading((string)($profile['about_eyebrow']??$label),(string)($profile['about_title']??$label)));
            $row = wpbb_child_v62_block('wpbb/column',array('xs'=>12,'lg'=>6),wpbb_child_v62_image($about_image,'wp-theme-sector-media-text__media'));
            $row .= wpbb_child_v62_block('wpbb/column',array('xs'=>12,'lg'=>6),wpbb_child_v62_paragraph((string)($profile['about_eyebrow']??$label),'wp-theme-sector-eyebrow').wpbb_child_v62_heading((string)($profile['about_title']??$label),2).wpbb_child_v62_paragraph((string)($profile['about_text']??$profile['hero_text']??'')));
            $content .= wpbb_child_v67_section( 'wp-theme-section-shell wp-theme-about-section', wpbb_child_v62_block('wpbb/row',array('customClasses'=>'align-items-start wp-theme-sector-media-text','gutterX'=>'gx-5','gutterY'=>'gy-5'),$row) );
            if (!empty($profile['stats'])) $content .= wpbb_child_v67_section( 'wp-theme-section-shell wp-theme-home-stats', wpbb_child_v64_stats_row($profile['stats']) );
            $values = array_slice((array)($profile['industries']??array()),0,3);
            if($values) $content .= wpbb_child_v67_section( 'wp-theme-section-shell', wpbb_child_v62_section_heading(__('What matters here','wp-bbuilder'),(string)($profile['priorities_heading']??__('Useful structure for the sector.','wp-bbuilder'))).wpbb_child_v62_cards_row($values,'wp-theme-sector-card',3));
            if(!empty($profile['process'])) { $cards=array(); foreach((array)$profile['process'] as $p) $cards[]=array(trim((string)($p[0]??'').' '.(string)($p[1]??'')),(string)($p[2]??'')); $content .= wpbb_child_v67_section( 'wp-theme-section-shell wp-theme-process-section', wpbb_child_v62_section_heading((string)($profile['process_eyebrow']??__('How it works','wp-bbuilder')),(string)($profile['process_heading']??'')).wpbb_child_v62_cards_row($cards,'wp-theme-sector-card wp-theme-process-card',3)); }
            $content .= wpbb_child_v64_cta($profile);
        } elseif ( 'contact' === $key ) {
            $contact_heading = (string) ( $profile['contact_heading'] ?? __( 'Talk to the right person and get a useful response.', 'wp-bbuilder' ) );
            $contact_text = (string) ( $profile['contact_text'] ?? '' );
            $hero_inner = wpbb_child_v62_section_heading( __( 'Contact', 'wp-bbuilder' ), $contact_heading );
            if ( $contact_text ) $hero_inner .= wpbb_child_v62_block( 'wpbb/row', array( 'gutterX'=>'gx-4','gutterY'=>'gy-3' ), wpbb_child_v62_block( 'wpbb/column', array( 'xs'=>12,'lg'=>8 ), wpbb_child_v62_paragraph( $contact_text, 'wpbb-v64-contact-intro' ) ) );
            $content .= wpbb_child_v67_section( 'wp-theme-section-shell wpbb-v64-page-hero', $hero_inner );
            $services = array_values( (array) ( $profile['services'] ?? array() ) );
            $contact_items = array();
            foreach ( array_slice( $services, 0, 3 ) as $service ) {
                if ( ! is_array( $service ) ) continue;
                $contact_items[] = array( (string) ( $service[0] ?? '' ), (string) ( $service[1] ?? '' ) );
            }
            if ( ! $contact_items ) $contact_items = array( array(__('Enquiries','wp-bbuilder'),$contact_text), array(__('Response','wp-bbuilder'),__('Your message is routed by topic.','wp-bbuilder')), array(__('Next step','wp-bbuilder'),__('Receive the most relevant practical response.','wp-bbuilder')) );
            $left = wpbb_child_v62_cards_row($contact_items,'wp-theme-sector-card wp-theme-contact-card',1);
            $fields=wpbb_child_v64_contact_fields($profile);
            $form=wpbb_child_v62_block('wpbb/dynamic-form',array('formTitle'=>__('Send us a message','wp-bbuilder'),'showTitle'=>true,'submitText'=>__('Send message','wp-bbuilder'),'stylePreset'=>'soft','buttonClass'=>'btn btn-primary','fields'=>$fields,'fieldsJson'=>wp_json_encode($fields,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE),'className'=>'wp-theme-contact-form'),'',true);
            $row=wpbb_child_v62_block('wpbb/column',array('xs'=>12,'lg'=>5),$left).wpbb_child_v62_block('wpbb/column',array('xs'=>12,'lg'=>7),$form);
            $content .= wpbb_child_v67_section( 'wp-theme-section-shell wp-theme-contact-section', wpbb_child_v62_block('wpbb/row',array('gutterX'=>'gx-5','gutterY'=>'gy-5','customClasses'=>'align-items-start'),$row) );
        }
        return $content;
    }
}

if ( ! function_exists( 'wpbb_child_v62_repair_serialized_content' ) ) {
    function wpbb_child_v62_repair_serialized_content( $content ) {
        if ( ! is_string( $content ) || '' === trim( $content ) ) return $content;
        $content = str_replace( '<!-- wp:wpbb/row --></div><!-- /wp:group -->', '<!-- /wp:wpbb/row --></div><!-- /wp:group -->', $content );
        // Explicit heading levels prevent Gutenberg h2/h3 validation mismatches.
        $content = preg_replace_callback( '~<!--\\s*wp:heading(?:\\s+(\\{.*?\\}))?\\s*-->\\s*<h([1-6])([^>]*)>~s', static function( $m ) {
            $level = max( 1, min( 6, (int) $m[2] ) );
            $attrs = array();
            if ( ! empty( $m[1] ) ) { $decoded = json_decode( $m[1], true ); if ( is_array( $decoded ) ) $attrs = $decoded; }
            if ( 2 !== $level ) $attrs['level'] = $level; else unset( $attrs['level'] );
            return '<!-- wp:heading' . ( $attrs ? ' ' . wp_json_encode( $attrs, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) : '' ) . ' --><h' . $level . $m[3] . '>';
        }, $content );
        // Known legacy raw item that sat directly inside a BBuilder Column.
        /* v115: partner heading wrapping is handled context-safely by wpbb_child_v115_normalize_partner_markup(). */
        return $content;
    }
}

if ( ! function_exists( 'wpbb_child_v62_is_managed_demo_page' ) ) {
    function wpbb_child_v62_is_managed_demo_page( $page_id ) {
        if ( ! $page_id || 'page' !== get_post_type( $page_id ) ) return false;
        if ( '1' === (string) get_post_meta( $page_id, '_wp_theme_demo_managed', true ) ) return true;
        if ( get_post_meta( $page_id, '_wpbb_child_bbuilder_version', true ) ) return true;
        return false;
    }
}

if ( ! function_exists( 'wpbb_child_v62_has_placeholder_dynamic_blocks' ) ) {
    function wpbb_child_v62_has_placeholder_dynamic_blocks( $content ) {
        $content = (string) $content;
        if ( '' === $content ) return false;
        foreach ( array(
            '<!-- wp:wpbb/swiper /-->',
            '<!-- wp:wpbb/icon-card /-->',
            '<!-- wp:wpbb/fun-fact /-->',
            '<!-- wp:wpbb/catalogue /-->',
        ) as $needle ) {
            if ( false !== strpos( $content, $needle ) ) return true;
        }
        return false;
    }
}

if ( ! function_exists( 'wpbb_child_v71_is_source_language' ) ) {
    /** Only rebuild the canonical English/source demo page, never a managed translation. */
    function wpbb_child_v71_is_source_language( $post_id ) {
        if ( function_exists( 'pll_get_post_language' ) ) {
            $lang = sanitize_key( (string) pll_get_post_language( $post_id, 'slug' ) );
            if ( $lang && 'en' !== $lang ) return false;
        }
        return true;
    }
}

if ( ! function_exists( 'wpbb_child_v71_demo_profile' ) ) {
    function wpbb_child_v71_demo_profile() {
        if ( function_exists( 'wp_theme_get_demo_profile' ) ) return (array) wp_theme_get_demo_profile();
        return function_exists( 'wpbb_child_v62_profile' ) ? (array) wpbb_child_v62_profile() : array();
    }
}


if ( ! function_exists( 'wpbb_child_v72_repair_canonical_markup' ) ) {
    /**
     * Keep parent-generated rich demo markup valid for Gutenberg/BBuilder.
     *
     * The parent partner strip historically emitted its heading as raw HTML
     * directly inside wpbb/column. Column saves InnerBlocks.Content only, so
     * Gutenberg treated that raw paragraph as invalid column content. Repair
     * only the known serialization defects; do not rewrite authored layout.
     */
    function wpbb_child_v72_repair_canonical_markup( $content ) {
        if ( ! is_string( $content ) || '' === trim( $content ) ) return $content;

        $content = str_replace(
            '<!-- wp:wpbb/row --></div><!-- /wp:group -->',
            '<!-- /wp:wpbb/row --></div><!-- /wp:group -->',
            $content
        );

        // Raw partner heading directly inside a BBuilder Column -> real core paragraph block.
        $content = preg_replace_callback(
            '~(<!--\\s*wp:wpbb/column\\b[^>]*-->\\s*)(<p class="wp-theme-partners-heading">.*?</p>)~s',
            static function( $m ) {
                return $m[1] . '<!-- wp:paragraph {"className":"wp-theme-partners-heading"} -->' . $m[2] . '<!-- /wp:paragraph -->';
            },
            $content
        );

        // Keep heading level metadata aligned with the actual h1-h6 element.
        $content = preg_replace_callback(
            '~<!--\\s*wp:heading(?:\\s+(\\{.*?\\}))?\\s*-->\\s*<h([1-6])([^>]*)>~s',
            static function( $m ) {
                $level = max( 1, min( 6, (int) $m[2] ) );
                $attrs = array();
                if ( ! empty( $m[1] ) ) {
                    $decoded = json_decode( $m[1], true );
                    if ( is_array( $decoded ) ) $attrs = $decoded;
                }
                if ( 2 !== $level ) $attrs['level'] = $level; else unset( $attrs['level'] );
                return '<!-- wp:heading' . ( $attrs ? ' ' . wp_json_encode( $attrs, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) : '' ) . ' --><h' . $level . $m[3] . '>';
            },
            $content
        );

        return $content;
    }
}

if ( ! function_exists( 'wpbb_child_v71_canonical_home_content' ) ) {
    /** The parent sector importer is the canonical rich-demo serializer. */
    function wpbb_child_v71_canonical_home_content( $profile = array() ) {
        if ( function_exists( 'wp_theme_build_sector_homepage_content' ) ) {
            $content = (string) wp_theme_build_sector_homepage_content();
        } elseif ( function_exists( 'wp_theme_demo_homepage_content' ) ) {
            $content = (string) wp_theme_demo_homepage_content();
        } else {
            $content = function_exists( 'wpbb_child_v62_front_content' ) ? (string) wpbb_child_v62_front_content( $profile ) : '';
        }
        if ( function_exists( 'wpbb_child_v72_repair_canonical_markup' ) ) $content = wpbb_child_v72_repair_canonical_markup( $content );
        if ( function_exists( 'wpbb_jobs_repair_canonical_home_content' ) ) $content = wpbb_jobs_repair_canonical_home_content( $content, $profile );
        return $content;
    }
}

if ( ! function_exists( 'wpbb_child_v71_canonical_page_content' ) ) {
    function wpbb_child_v71_canonical_page_content( $slug, $profile ) {
        if ( function_exists( 'wp_theme_sector_page_content' ) ) {
            $content = (string) wp_theme_sector_page_content( $slug, $profile );
        } else {
            $content = function_exists( 'wpbb_child_v62_simple_page_content' ) ? (string) wpbb_child_v62_simple_page_content( $profile, $slug ) : '';
        }
        return function_exists( 'wpbb_child_v72_repair_canonical_markup' ) ? wpbb_child_v72_repair_canonical_markup( $content ) : $content;
    }
}

if ( ! function_exists( 'wpbb_child_v71_sync_managed_translations' ) ) {
    /** Re-sync only translations created by Starter Setup after repairing English source content. */
    function wpbb_child_v71_sync_managed_translations( $source_ids ) {
        $source_ids = array_values( array_unique( array_filter( array_map( 'absint', (array) $source_ids ) ) ) );
        if ( ! $source_ids || ! function_exists( 'wp_theme_demo_create_polylang_translations' ) || ! function_exists( 'wp_theme_demo_polylang_languages' ) ) return;
        $languages = array_keys( (array) wp_theme_demo_polylang_languages() );
        if ( $languages ) wp_theme_demo_create_polylang_translations( $source_ids, $languages );
    }
}

if ( ! function_exists( 'wpbb_child_v62_rebuild_demo_pages' ) ) {
    /**
     * v3.8.10.74 canonical demo repair.
     *
     * Older child releases rebuilt the English source at priority 999 with a
     * compact BBuilder fallback. Polylang had already copied the richer parent
     * version at priority 5, which is why translated pages could look better
     * than English. The parent sector importer is now the sole content source.
     */
    function wpbb_child_v62_rebuild_demo_pages( $force = false ) {
        if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) return;

        $stylesheet = sanitize_key( get_stylesheet() );
        $done_key   = 'wpbb_child_v72_demo_system_' . $stylesheet;
        $front      = absint( get_option( 'page_on_front' ) );
        $front_content = $front ? (string) get_post_field( 'post_content', $front, 'raw' ) : '';
        $placeholder = $front && wpbb_child_v62_has_placeholder_dynamic_blocks( $front_content );
        $already = '3.8.10.75' === (string) get_option( $done_key );
        if ( ! $force && ! $placeholder && $already ) return;

        $profile = wpbb_child_v71_demo_profile();
        if ( empty( $profile['id'] ) ) {
            update_option( $done_key, '3.8.10.75', false );
            return;
        }

        $source_ids = array();

        if ( $front && wpbb_child_v62_is_managed_demo_page( $front ) && wpbb_child_v71_is_source_language( $front ) ) {
            $needs_refresh = $force || $placeholder || '3.8.10.75' !== (string) get_post_meta( $front, '_wpbb_child_bbuilder_version', true );
            if ( $needs_refresh ) {
                $clean = wpbb_child_v71_canonical_home_content( $profile );
                if ( '' !== trim( $clean ) ) {
                    wp_update_post( array( 'ID' => $front, 'post_content' => $clean ) );
                    update_post_meta( $front, '_wpbb_child_bbuilder_version', '3.8.10.75' );
                    update_post_meta( $front, '_wp_theme_demo_managed', '1' );
                    clean_post_cache( $front );
                }
            }
            $source_ids[] = $front;
        }

        foreach ( array( 'about', 'services', 'industries', 'contact' ) as $slug ) {
            $page = get_page_by_path( $slug );
            if ( ! $page instanceof WP_Post || ! wpbb_child_v62_is_managed_demo_page( $page->ID ) || ! wpbb_child_v71_is_source_language( $page->ID ) ) continue;
            $needs_refresh = $force || '3.8.10.75' !== (string) get_post_meta( $page->ID, '_wpbb_child_bbuilder_version', true );
            if ( $needs_refresh ) {
                $clean = wpbb_child_v71_canonical_page_content( $slug, $profile );
                if ( '' !== trim( $clean ) ) {
                    wp_update_post( array( 'ID' => $page->ID, 'post_content' => $clean ) );
                    update_post_meta( $page->ID, '_wpbb_child_bbuilder_version', '3.8.10.75' );
                    update_post_meta( $page->ID, '_wp_theme_demo_managed', '1' );
                    clean_post_cache( $page->ID );
                }
            }
            $source_ids[] = $page->ID;
        }

        // Starter Setup owns translation generation. Existing localized demo pages
        // are deliberately left intact here because they may already contain
        // better human/edited translations than the shared phrase dictionary.
        update_option( $done_key, '3.8.10.75', false );
    }
    add_action( 'admin_init', 'wpbb_child_v62_rebuild_demo_pages', 80 );
}

if ( ! function_exists( 'wpbb_child_v62_after_demo_import' ) ) {
    function wpbb_child_v62_after_demo_import( $page_id = 0, $profile = array() ) {
        delete_option( 'wpbb_child_v72_demo_system_' . sanitize_key( get_stylesheet() ) );
        // Run after Polylang's initial copy, but serialize English from the same
        // canonical parent content. Existing localized pages are preserved.
        if ( is_admin() && current_user_can( 'manage_options' ) ) wpbb_child_v62_rebuild_demo_pages( true );
    }
    add_action( 'wp_theme_after_demo_import', 'wpbb_child_v62_after_demo_import', 999, 2 );
}

if ( ! function_exists( 'wpbb_child_v62_normalize_on_save' ) ) {
    /** Never rewrite arbitrary editor content while a user saves a page. */
    function wpbb_child_v62_normalize_on_save( $data, $postarr ) {
        return $data;
    }
}

if ( ! function_exists( 'wpbb_child_v62_enqueue_system_css' ) ) {
    function wpbb_child_v62_enqueue_system_css() {
        $path = get_stylesheet_directory() . '/assets/theme-system-v62.css';
        if ( is_readable( $path ) ) {
            wp_enqueue_style( 'wpbb-child-system-v62', get_stylesheet_directory_uri() . '/assets/theme-system-v62.css', array(), '3.8.10.75' );
        }
    }
    add_action( 'wp_enqueue_scripts', 'wpbb_child_v62_enqueue_system_css', 140 );
    add_action( 'enqueue_block_editor_assets', 'wpbb_child_v62_enqueue_system_css', 140 );
}


/* v3.8.10.65: safe, explicit BBuilder demo maintenance UI. */
if ( ! function_exists( 'wpbb_child_v65_demo_admin_menu' ) ) {
    function wpbb_child_v65_demo_admin_menu() {
        if ( ! current_user_can( 'manage_options' ) ) return;
        add_theme_page(
            __( 'BBuilder Demo', 'wp-bbuilder' ),
            __( 'BBuilder Demo', 'wp-bbuilder' ),
            'manage_options',
            'wpbb-child-demo',
            'wpbb_child_v65_demo_admin_page'
        );
    }
    add_action( 'admin_menu', 'wpbb_child_v65_demo_admin_menu', 80 );
}
if ( ! function_exists( 'wpbb_child_v65_demo_admin_page' ) ) {
    function wpbb_child_v65_demo_admin_page() {
        if ( ! current_user_can( 'manage_options' ) ) return;
        $managed = get_posts( array( 'post_type'=>'page', 'post_status'=>'any', 'posts_per_page'=>-1, 'fields'=>'ids', 'meta_key'=>'_wp_theme_demo_managed', 'meta_value'=>'1' ) );
        $notice = isset( $_GET['wpbb-demo-refreshed'] ) ? '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Managed demo pages refreshed.', 'wp-bbuilder' ) . '</p></div>' : '';
        echo '<div class="wrap"><h1>' . esc_html__( 'BBuilder Demo', 'wp-bbuilder' ) . '</h1>' . wp_kses_post( $notice );
        echo '<p>' . esc_html__( 'Demo maintenance is explicit in this release. Existing pages are never silently rewritten just because an administrator opens wp-admin.', 'wp-bbuilder' ) . '</p>';
        echo '<div class="card" style="max-width:760px;padding:8px 20px 20px"><h2>' . esc_html__( 'Managed demo content', 'wp-bbuilder' ) . '</h2>';
        echo '<p><strong>' . esc_html( sprintf( _n( '%d managed page detected', '%d managed pages detected', count( $managed ), 'wp-bbuilder' ), count( $managed ) ) ) . '</strong></p>';
        echo '<p>' . esc_html__( 'Use Refresh only when you intentionally want to rebuild the theme-managed Home, About, Services, Industries and Contact demo pages from the current sector content pack.', 'wp-bbuilder' ) . '</p>';
        echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '"><input type="hidden" name="action" value="wpbb_child_refresh_demo">';
        wp_nonce_field( 'wpbb_child_refresh_demo' );
        submit_button( __( 'Refresh managed demo pages', 'wp-bbuilder' ), 'primary', 'submit', false );
        echo '</form></div></div>';
    }
}
if ( ! function_exists( 'wpbb_child_v65_handle_demo_refresh' ) ) {
    function wpbb_child_v65_handle_demo_refresh() {
        if ( ! current_user_can( 'manage_options' ) ) wp_die( esc_html__( 'You do not have permission to refresh demo content.', 'wp-bbuilder' ) );
        check_admin_referer( 'wpbb_child_refresh_demo' );
        wpbb_child_v62_rebuild_demo_pages( true );
        wp_safe_redirect( add_query_arg( 'wpbb-demo-refreshed', '1', admin_url( 'themes.php?page=wpbb-child-demo' ) ) );
        exit;
    }
    add_action( 'admin_post_wpbb_child_refresh_demo', 'wpbb_child_v65_handle_demo_refresh' );
}

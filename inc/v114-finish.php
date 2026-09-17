<?php
/** WP BBTheme child suite 3.8.11.14 - child-only settings, editor, legal, editorial and hero finish. */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wpbb_child_v114_hero_relative' ) ) {
    function wpbb_child_v114_hero_relative() { return 'assets/img/demo/hero-v114.jpg'; }
}

if ( ! function_exists( 'wpbb_child_v114_enqueue' ) ) {
    function wpbb_child_v114_enqueue() {
        $v = wp_get_theme()->get( 'Version' );
        wp_enqueue_style( 'wpbb-suite-v114', get_stylesheet_directory_uri() . '/assets/suite-v114.css', array( 'wpbb-suite-v113' ), $v );
        $rel = wpbb_child_v114_hero_relative();
        if ( is_readable( get_stylesheet_directory() . '/' . $rel ) ) {
            wp_add_inline_style( 'wpbb-suite-v114', ':root{--suite-v114-hero:url("' . esc_url( get_stylesheet_directory_uri() . '/' . $rel ) . '");}' );
        }
    }
    add_action( 'wp_enqueue_scripts', 'wpbb_child_v114_enqueue', PHP_INT_MAX );
}

/* ---- General theme settings ------------------------------------------------ */
if ( ! function_exists( 'wpbb_child_v114_dark_disabled' ) ) {
    function wpbb_child_v114_dark_disabled() { return (bool) get_option( 'wpbb_child_disable_dark_mode', false ); }
}
if ( ! function_exists( 'wpbb_child_v114_translations_disabled' ) ) {
    function wpbb_child_v114_translations_disabled() { return (bool) get_option( 'wpbb_child_disable_translations', false ); }
}
if ( ! function_exists( 'wpbb_child_v114_body_classes' ) ) {
    function wpbb_child_v114_body_classes( $classes ) {
        if ( wpbb_child_v114_dark_disabled() ) $classes[] = 'wpbb-dark-mode-disabled';
        if ( wpbb_child_v114_translations_disabled() ) $classes[] = 'wpbb-translations-disabled';
        return array_values( array_unique( $classes ) );
    }
    add_filter( 'body_class', 'wpbb_child_v114_body_classes', PHP_INT_MAX );
}
if ( ! function_exists( 'wpbb_child_v114_light_mode_guard' ) ) {
    function wpbb_child_v114_light_mode_guard() {
        if ( ! wpbb_child_v114_dark_disabled() ) return;
        echo '<script id="wpbb-v114-light-guard">(function(){try{localStorage.removeItem("wpThemeMode");var h=document.documentElement;h.classList.remove("is-dark-theme");h.classList.add("wpbb-dark-mode-disabled");h.removeAttribute("data-theme");}catch(e){}})();</script>';
    }
    add_action( 'wp_head', 'wpbb_child_v114_light_mode_guard', 2 );
    add_action( 'wp_footer', 'wpbb_child_v114_light_mode_guard', PHP_INT_MAX );
}

if ( ! function_exists( 'wpbb_child_v114_settings_menu' ) ) {
    function wpbb_child_v114_settings_menu() {
        if ( ! current_user_can( 'manage_options' ) ) return;
        add_theme_page( __( 'Theme Settings', 'wp-bbtheme-child' ), __( 'Theme Settings', 'wp-bbtheme-child' ), 'manage_options', 'wpbb-child-theme-settings', 'wpbb_child_v114_settings_page' );
    }
    add_action( 'admin_menu', 'wpbb_child_v114_settings_menu', 85 );
}
if ( ! function_exists( 'wpbb_child_v114_settings_page' ) ) {
    function wpbb_child_v114_settings_page() {
        if ( ! current_user_can( 'manage_options' ) ) return;
        $dark = wpbb_child_v114_dark_disabled();
        $translations = wpbb_child_v114_translations_disabled();
        $saved = ! empty( $_GET['wpbb-settings-saved'] );
        echo '<div class="wrap"><h1>' . esc_html__( 'Theme Settings', 'wp-bbtheme-child' ) . '</h1>';
        if ( $saved ) echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Theme settings saved.', 'wp-bbtheme-child' ) . '</p></div>';
        echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" id="wpbb-v114-settings-form">';
        echo '<input type="hidden" name="action" value="wpbb_child_save_theme_settings">';
        wp_nonce_field( 'wpbb_child_save_theme_settings' );
        echo '<table class="form-table" role="presentation"><tbody>';
        echo '<tr><th scope="row">' . esc_html__( 'Dark mode', 'wp-bbtheme-child' ) . '</th><td><label><input type="checkbox" name="disable_dark_mode" value="1" ' . checked( $dark, true, false ) . '> ' . esc_html__( 'Disable the dark-mode button and dark-mode functionality', 'wp-bbtheme-child' ) . '</label><p class="description">' . esc_html__( 'The site is kept in light mode and the header theme toggle is hidden.', 'wp-bbtheme-child' ) . '</p></td></tr>';
        echo '<tr><th scope="row">' . esc_html__( 'Translations', 'wp-bbtheme-child' ) . '</th><td><label><input type="checkbox" id="wpbb-disable-translations" name="disable_translations" value="1" ' . checked( $translations, true, false ) . '> ' . esc_html__( 'Disable translations and keep English only', 'wp-bbtheme-child' ) . '</label><p class="description">' . esc_html__( 'When first enabled, non-English Pages and Posts managed through Polylang are moved to Trash. The language switcher is hidden and future demo-import translations are cleaned up as well.', 'wp-bbtheme-child' ) . '</p></td></tr>';
        echo '</tbody></table>';
        submit_button( __( 'Save theme settings', 'wp-bbtheme-child' ) );
        echo '</form>';
        if ( ! $translations ) echo '<script>document.getElementById("wpbb-v114-settings-form").addEventListener("submit",function(e){var c=document.getElementById("wpbb-disable-translations");if(c&&c.checked&&!window.confirm("Disable translations and move non-English Pages and Posts to Trash?")){e.preventDefault();}});</script>';
        echo '</div>';
    }
}

if ( ! function_exists( 'wpbb_child_v114_trash_non_english_content' ) ) {
    function wpbb_child_v114_trash_non_english_content() {
        if ( ! function_exists( 'pll_get_post_language' ) ) return 0;
        $count = 0;
        $front = absint( get_option( 'page_on_front' ) );
        if ( $front && 'en' !== sanitize_key( (string) pll_get_post_language( $front, 'slug' ) ) && function_exists( 'pll_get_post' ) ) {
            $english_front = absint( pll_get_post( $front, 'en' ) );
            if ( $english_front && 'publish' === get_post_status( $english_front ) ) update_option( 'page_on_front', $english_front );
        }
        $posts = get_posts( array(
            'post_type' => array( 'page', 'post' ), 'post_status' => array( 'publish','draft','pending','private','future' ),
            'posts_per_page' => -1, 'fields' => 'ids', 'no_found_rows' => true,
        ) );
        foreach ( $posts as $post_id ) {
            $lang = sanitize_key( (string) pll_get_post_language( $post_id, 'slug' ) );
            if ( $lang && 0 !== strpos( $lang, 'en' ) ) {
                if ( wp_trash_post( $post_id ) ) $count++;
            }
        }
        return $count;
    }
}
if ( ! function_exists( 'wpbb_child_v114_save_settings' ) ) {
    function wpbb_child_v114_save_settings() {
        if ( ! current_user_can( 'manage_options' ) ) wp_die( esc_html__( 'You do not have permission to change theme settings.', 'wp-bbtheme-child' ) );
        check_admin_referer( 'wpbb_child_save_theme_settings' );
        $old_translations = wpbb_child_v114_translations_disabled();
        $dark = ! empty( $_POST['disable_dark_mode'] );
        $translations = ! empty( $_POST['disable_translations'] );
        update_option( 'wpbb_child_disable_dark_mode', $dark ? 1 : 0, false );
        update_option( 'wpbb_child_disable_translations', $translations ? 1 : 0, false );
        if ( $translations && ! $old_translations ) wpbb_child_v114_trash_non_english_content();
        wp_safe_redirect( add_query_arg( 'wpbb-settings-saved', '1', admin_url( 'themes.php?page=wpbb-child-theme-settings' ) ) );
        exit;
    }
    add_action( 'admin_post_wpbb_child_save_theme_settings', 'wpbb_child_v114_save_settings' );
}
if ( ! function_exists( 'wpbb_child_v114_translation_cleanup_after_import' ) ) {
    function wpbb_child_v114_translation_cleanup_after_import() { if ( wpbb_child_v114_translations_disabled() ) wpbb_child_v114_trash_non_english_content(); }
    add_action( 'wp_theme_after_demo_import', 'wpbb_child_v114_translation_cleanup_after_import', PHP_INT_MAX );
}
if ( ! function_exists( 'wpbb_child_v114_force_english_redirect' ) ) {
    function wpbb_child_v114_force_english_redirect() {
        if ( is_admin() || wp_doing_ajax() || ! wpbb_child_v114_translations_disabled() || ! function_exists( 'pll_current_language' ) ) return;
        $lang = sanitize_key( (string) pll_current_language( 'slug' ) );
        if ( ! $lang || 0 === strpos( $lang, 'en' ) ) return;
        $target = '';
        if ( is_singular() && function_exists( 'pll_get_post' ) ) {
            $english = absint( pll_get_post( get_queried_object_id(), 'en' ) );
            if ( $english ) $target = get_permalink( $english );
        }
        if ( ! $target && function_exists( 'pll_home_url' ) ) $target = pll_home_url( 'en' );
        if ( ! $target ) $target = home_url( '/' );
        wp_safe_redirect( $target, 302 ); exit;
    }
    add_action( 'template_redirect', 'wpbb_child_v114_force_english_redirect', 0 );
}

/* ---- Gutenberg/BBuilder serialization repair ------------------------------ */
if ( ! function_exists( 'wpbb_child_v114_repair_serialized_content' ) ) {
    function wpbb_child_v114_repair_serialized_content( $content ) {
        if ( ! is_string( $content ) || '' === $content ) return $content;
        $content = str_replace( '<!-- wp:wpbb/row --></div><!-- /wp:group -->', '<!-- /wp:wpbb/row --></div><!-- /wp:group -->', $content );
        $content = preg_replace_callback(
            '~(<!--\\s*wp:wpbb/column\\b[^>]*-->\\s*)(<p class="wp-theme-partners-heading">.*?</p>)~s',
            static function( $m ) {
                if ( false !== strpos( $m[1] . $m[2], '<!-- wp:paragraph' ) ) return $m[0];
                return $m[1] . '<!-- wp:paragraph {"className":"wp-theme-partners-heading"} -->' . $m[2] . '<!-- /wp:paragraph -->';
            },
            $content
        );
        return $content;
    }
}
if ( ! function_exists( 'wpbb_child_v114_repair_existing_blocks' ) ) {
    function wpbb_child_v114_repair_existing_blocks() {
        $ids = get_posts( array( 'post_type'=>'page','post_status'=>array('publish','draft','pending','private','future'),'posts_per_page'=>-1,'fields'=>'ids','no_found_rows'=>true ) );
        foreach ( $ids as $id ) {
            $old = (string) get_post_field( 'post_content', $id, 'raw' );
            if ( false === strpos( $old, 'wp-theme-partners-heading' ) && false === strpos( $old, '<!-- wp:wpbb/row --></div><!-- /wp:group -->' ) ) continue;
            $new = wpbb_child_v114_repair_serialized_content( $old );
            if ( $new !== $old ) { wp_update_post( wp_slash( array( 'ID'=>$id, 'post_content'=>$new ) ) ); clean_post_cache( $id ); }
        }
    }
}
if ( ! function_exists( 'wpbb_child_v114_filter_post_data' ) ) {
    function wpbb_child_v114_filter_post_data( $data, $postarr ) {
        if ( 'page' === ( $data['post_type'] ?? '' ) && ! empty( $data['post_content'] ) ) $data['post_content'] = wpbb_child_v114_repair_serialized_content( $data['post_content'] );
        return $data;
    }
    add_filter( 'wp_insert_post_data', 'wpbb_child_v114_filter_post_data', 90, 2 );
}

/* WordPress now expects editor iframe styles through enqueue_block_assets. Remove the old child
   enqueue_block_editor_assets hook that triggers the iframe warning, while leaving plugin warnings alone. */
remove_action( 'enqueue_block_editor_assets', 'wpbb_child_v62_enqueue_system_css', 140 );
if ( ! function_exists( 'wpbb_child_v114_editor_system_css' ) ) {
    function wpbb_child_v114_editor_system_css() {
        if ( ! is_admin() ) return;
        $path = get_stylesheet_directory() . '/assets/theme-system-v62.css';
        if ( is_readable( $path ) ) wp_enqueue_style( 'wpbb-child-system-v62', get_stylesheet_directory_uri() . '/assets/theme-system-v62.css', array(), wp_get_theme()->get( 'Version' ) );
    }
    add_action( 'enqueue_block_assets', 'wpbb_child_v114_editor_system_css', 140 );
}

/* ---- Hero/media repair ----------------------------------------------------- */
if ( ! function_exists( 'wpbb_child_v114_demo_profile' ) ) {
    function wpbb_child_v114_demo_profile( $profile ) {
        if ( ! is_array( $profile ) ) $profile = array();
        $rel = wpbb_child_v114_hero_relative();
        $url = '';
        if ( ( is_admin() || doing_action( 'wp_theme_after_demo_import' ) ) && function_exists( 'wpbb_child_v110_attachment_url' ) ) $url = (string) wpbb_child_v110_attachment_url( $rel, (string) ( $profile['name'] ?? wp_get_theme()->get('Name') ) . ' hero' );
        if ( ! $url && is_readable( get_stylesheet_directory() . '/' . $rel ) ) $url = get_stylesheet_directory_uri() . '/' . $rel;
        if ( $url ) {
            $profile['hero_image'] = $url;
            if ( ! empty( $profile['hero_slides'] ) && is_array( $profile['hero_slides'] ) ) foreach ( $profile['hero_slides'] as $i=>$slide ) if ( is_array($slide) ) $profile['hero_slides'][$i]['image']=$url;
        }
        return $profile;
    }
    add_filter( 'wp_theme_demo_profile', 'wpbb_child_v114_demo_profile', PHP_INT_MAX );
}
if ( ! function_exists( 'wpbb_child_v114_repair_hero_pages' ) ) {
    function wpbb_child_v114_repair_hero_pages() {
        $rel = wpbb_child_v114_hero_relative();
        $url = function_exists('wpbb_child_v110_attachment_url') ? (string) wpbb_child_v110_attachment_url( $rel, wp_get_theme()->get('Name') . ' hero' ) : '';
        if ( ! $url && is_readable( get_stylesheet_directory() . '/' . $rel ) ) $url = get_stylesheet_directory_uri() . '/' . $rel;
        if ( ! $url ) return;
        $pages=get_posts(array('post_type'=>'page','post_status'=>array('publish','draft','private','pending'),'posts_per_page'=>-1,'fields'=>'ids','no_found_rows'=>true));
        foreach($pages as $page_id){
            $old=(string)get_post_field('post_content',$page_id,'raw'); if(''===$old||false===strpos($old,'wpbb/swiper')) continue;
            $new=(string)preg_replace_callback('~<!--\\s+wp:wpbb/swiper\\s+(\\{.*?\\})\\s+/-->~s',static function($m)use($url){
                $a=json_decode($m[1],true); if(!is_array($a)||'hero'!==sanitize_key((string)($a['demoStyle']??''))) return $m[0];
                if(!empty($a['slides'])&&is_array($a['slides'])) foreach($a['slides'] as $i=>$s) if(is_array($s)) $a['slides'][$i]['image']=$url;
                if(!empty($a['slidesJson'])&&is_string($a['slidesJson'])){$s=json_decode($a['slidesJson'],true);if(is_array($s)){foreach($s as $i=>$v)if(is_array($v))$s[$i]['image']=$url;$a['slidesJson']=wp_json_encode($s,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);}}
                return '<!-- wp:wpbb/swiper '.wp_json_encode($a,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE).' /-->';
            },$old);
            $new=wpbb_child_v114_repair_serialized_content($new);
            if($new!==$old){wp_update_post(wp_slash(array('ID'=>$page_id,'post_content'=>$new)));clean_post_cache($page_id);}
        }
    }
}
if ( ! function_exists( 'wpbb_child_v114_repair_once' ) ) {
    function wpbb_child_v114_repair_once() {
        if ( ! is_admin() || ! current_user_can('manage_options') || wp_doing_ajax() ) return;
        $key='wpbb_child_v114_repair_'.sanitize_key(get_stylesheet());
        if ( '3.8.11.14' === (string)get_option($key) ) return;
        wpbb_child_v114_repair_existing_blocks();
        wpbb_child_v114_repair_hero_pages();
        if ( function_exists('wpbb_child_v112_repair_media') ) wpbb_child_v112_repair_media();
        if ( wpbb_child_v114_translations_disabled() ) wpbb_child_v114_trash_non_english_content();
        update_option($key,'3.8.11.14',false);
    }
    add_action('admin_init','wpbb_child_v114_repair_once',101000);
    add_action('wp_theme_after_demo_import','wpbb_child_v114_repair_once',101000);
}

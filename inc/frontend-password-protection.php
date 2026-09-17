<?php
/**
 * Frontend password protection shared by the maintained WP Base child themes.
 *
 * Controls are rendered inside Theme Settings > General. The settings use
 * suite-wide option names, so changing the active child theme does not reset
 * the gate or expose the site unexpectedly.
 */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wpbb_child_demo_protection_keys' ) ) {
    function wpbb_child_demo_protection_keys() {
        return array(
            'enabled'  => 'wpbb_demo_protection_enabled',
            'hash'     => 'wpbb_demo_protection_hash',
            'revision' => 'wpbb_demo_protection_revision',
        );
    }
}

if ( ! function_exists( 'wpbb_child_demo_protection_migrate' ) ) {
    function wpbb_child_demo_protection_migrate() {
        $keys = wpbb_child_demo_protection_keys();
        $stylesheet = sanitize_key( (string) get_stylesheet() );
        $legacy_enabled = 'wpbb_demo_protection_enabled_' . $stylesheet;
        $legacy_hash = 'wpbb_demo_protection_hash_' . $stylesheet;

        if ( false === get_option( $keys['enabled'], false ) ) {
            $value = get_option( $legacy_enabled, false );
            add_option( $keys['enabled'], false === $value ? '1' : ( '0' === (string) $value ? '0' : '1' ), '', false );
        }
        if ( false === get_option( $keys['hash'], false ) ) {
            $value = get_option( $legacy_hash, false );
            add_option( $keys['hash'], $value ? (string) $value : wp_hash_password( 'wp@demo' ), '', false );
        }
        if ( false === get_option( $keys['revision'], false ) ) {
            add_option( $keys['revision'], '1', '', false );
        }
    }
}
add_action( 'init', 'wpbb_child_demo_protection_migrate', 1 );

if ( ! function_exists( 'wpbb_child_demo_protection_enabled' ) ) {
    function wpbb_child_demo_protection_enabled() {
        $keys = wpbb_child_demo_protection_keys();
        return '0' !== (string) get_option( $keys['enabled'], '1' );
    }
}

if ( ! function_exists( 'wpbb_child_demo_cookie_name' ) ) {
    function wpbb_child_demo_cookie_name() {
        return 'wpbb_demo_access';
    }
}

if ( ! function_exists( 'wpbb_child_demo_cookie_path' ) ) {
    function wpbb_child_demo_cookie_path() {
        $path = (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH );
        return '' === $path ? '/' : trailingslashit( $path );
    }
}

if ( ! function_exists( 'wpbb_child_demo_access_signature' ) ) {
    function wpbb_child_demo_access_signature( $expires ) {
        $keys = wpbb_child_demo_protection_keys();
        $data = implode( '|', array(
            'wpbb-demo-access-v2',
            (string) (int) $expires,
            (string) home_url( '/' ),
            (string) get_option( $keys['hash'], '' ),
            (string) get_option( $keys['revision'], '1' ),
        ) );
        return hash_hmac( 'sha256', $data, wp_salt( 'auth' ) );
    }
}

if ( ! function_exists( 'wpbb_child_demo_access_cookie_value' ) ) {
    function wpbb_child_demo_access_cookie_value( $expires ) {
        return 'v2.' . (int) $expires . '.' . wpbb_child_demo_access_signature( $expires );
    }
}

if ( ! function_exists( 'wpbb_child_demo_set_cookie' ) ) {
    function wpbb_child_demo_set_cookie( $expires, $value = null ) {
        if ( headers_sent() ) return false;
        $options = array(
            'expires'  => (int) $expires,
            'path'     => wpbb_child_demo_cookie_path(),
            'secure'   => is_ssl(),
            'httponly' => true,
            'samesite' => 'Lax',
        );
        if ( defined( 'COOKIE_DOMAIN' ) && COOKIE_DOMAIN ) $options['domain'] = COOKIE_DOMAIN;
        $value = null === $value ? wpbb_child_demo_access_cookie_value( $expires ) : (string) $value;
        $result = setcookie( wpbb_child_demo_cookie_name(), $value, $options );
        if ( $result ) {
            if ( $expires > time() && '' !== $value ) $_COOKIE[ wpbb_child_demo_cookie_name() ] = $value;
            else unset( $_COOKIE[ wpbb_child_demo_cookie_name() ] );
        }
        return $result;
    }
}

if ( ! function_exists( 'wpbb_child_demo_has_access' ) ) {
    function wpbb_child_demo_has_access() {
        if ( current_user_can( 'manage_options' ) ) return true;
        $cookie = isset( $_COOKIE[ wpbb_child_demo_cookie_name() ] ) ? sanitize_text_field( wp_unslash( $_COOKIE[ wpbb_child_demo_cookie_name() ] ) ) : '';
        $parts = explode( '.', $cookie, 3 );
        if ( 3 !== count( $parts ) || 'v2' !== $parts[0] || ! ctype_digit( $parts[1] ) ) return false;
        $expires = (int) $parts[1];
        return $expires >= time() && hash_equals( wpbb_child_demo_access_signature( $expires ), (string) $parts[2] );
    }
}

if ( ! function_exists( 'wpbb_child_demo_is_exempt_request' ) ) {
    function wpbb_child_demo_is_exempt_request() {
        if ( is_admin() || wp_doing_ajax() || wp_doing_cron() ) return true;
        if ( defined( 'WP_CLI' ) && WP_CLI ) return true;
        if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) return true;
        if ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) return true;
        return false;
    }
}

if ( ! function_exists( 'wpbb_child_demo_private_headers' ) ) {
    function wpbb_child_demo_private_headers() {
        if ( ! wpbb_child_demo_protection_enabled() || headers_sent() ) return;
        if ( ! defined( 'DONOTCACHEPAGE' ) ) define( 'DONOTCACHEPAGE', true );
        nocache_headers();
        header( 'Vary: Cookie', false );
        header( 'X-Robots-Tag: noindex, nofollow, noarchive', true );
    }
}
add_action( 'send_headers', 'wpbb_child_demo_private_headers', 0 );

if ( ! function_exists( 'wpbb_child_demo_render_gate' ) ) {
    function wpbb_child_demo_render_gate() {
        if ( ! wpbb_child_demo_protection_enabled() || wpbb_child_demo_is_exempt_request() || wpbb_child_demo_has_access() ) return;

        $keys = wpbb_child_demo_protection_keys();
        $error = '';
        $method = isset( $_SERVER['REQUEST_METHOD'] ) ? strtoupper( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) ) : 'GET';
        $action = isset( $_POST['wpbb_demo_action'] ) ? sanitize_key( wp_unslash( $_POST['wpbb_demo_action'] ) ) : '';
        if ( 'POST' === $method && 'unlock' === $action ) {
            $nonce = isset( $_POST['wpbb_demo_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['wpbb_demo_nonce'] ) ) : '';
            $password = isset( $_POST['wpbb_demo_password'] ) ? (string) wp_unslash( $_POST['wpbb_demo_password'] ) : '';
            if ( ! wp_verify_nonce( $nonce, 'wpbb_child_demo_unlock' ) ) {
                $error = __( 'The form expired. Refresh the page and try again.', 'wp-theme' );
            } elseif ( wp_check_password( $password, (string) get_option( $keys['hash'], '' ) ) ) {
                $duration = (int) apply_filters( 'wpbb_child_demo_access_duration', DAY_IN_SECONDS );
                $duration = max( 5 * MINUTE_IN_SECONDS, min( YEAR_IN_SECONDS, $duration ) );
                $expires = time() + $duration;
                if ( wpbb_child_demo_set_cookie( $expires ) ) {
                    $redirect = isset( $_POST['wpbb_demo_redirect_to'] ) ? (string) wp_unslash( $_POST['wpbb_demo_redirect_to'] ) : '/';
                    wp_safe_redirect( wp_validate_redirect( $redirect, home_url( '/' ) ) );
                    exit;
                }
                $error = __( 'Access could not be saved because the browser rejected the cookie.', 'wp-theme' );
            } else {
                $error = __( 'That password is not correct. Please try again.', 'wp-theme' );
            }
        }

        status_header( 200 );
        nocache_headers();
        $brand = sanitize_hex_color( (string) apply_filters( 'wpbb_child_demo_protection_brand_color', '#253E5B' ) ) ?: '#253E5B';
        $site_name = (string) get_bloginfo( 'name' );
        $theme_name = (string) wp_get_theme()->get( 'Name' );
        $request_uri = isset( $_SERVER['REQUEST_URI'] ) ? (string) wp_unslash( $_SERVER['REQUEST_URI'] ) : '/';
        ?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="robots" content="noindex,nofollow,noarchive">
<title><?php echo esc_html( sprintf( __( '%s — Private preview', 'wp-theme' ), $site_name ) ); ?></title>
<style>
:root{color-scheme:light dark;--brand:<?php echo esc_html( $brand ); ?>}*{box-sizing:border-box}body{margin:0;min-height:100vh;display:grid;place-items:center;padding:24px;background:#f3f5f8;color:#111827;font:16px/1.55 system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif}.wpbb-demo-lock{width:min(100%,470px);padding:38px;border:1px solid #d9dee8;border-radius:22px;background:#fff;box-shadow:0 28px 80px rgba(15,23,42,.14)}.wpbb-demo-lock__eyebrow{margin:0 0 12px;color:var(--brand);font-size:12px;font-weight:800;letter-spacing:.12em;text-transform:uppercase}.wpbb-demo-lock h1{margin:0 0 12px;font-size:34px;line-height:1.08;letter-spacing:-.04em}.wpbb-demo-lock p{margin:0 0 22px;color:#5f6878}.wpbb-demo-lock label{display:block;margin-bottom:7px;font-weight:700}.wpbb-demo-lock input{width:100%;height:50px;padding:0 14px;border:1px solid #cdd4df;border-radius:10px;background:#fff;color:#111827;font:inherit}.wpbb-demo-lock button{width:100%;min-height:50px;margin-top:12px;border:0;border-radius:10px;background:var(--brand);color:#fff;font:inherit;font-weight:800;cursor:pointer}.wpbb-demo-lock__error{padding:11px 13px;border-radius:9px;background:#fff0f0;color:#9b1c1c}.wpbb-demo-lock__meta{margin-top:20px;text-align:center;font-size:12px}@media(prefers-color-scheme:dark){body{background:#09111c;color:#f8fafc}.wpbb-demo-lock{background:#111d2b;border-color:#2b3b4d}.wpbb-demo-lock p{color:#c5d0dc}.wpbb-demo-lock input{background:#0b1623;border-color:#3a4a5c;color:#fff}.wpbb-demo-lock__error{background:#3b171b;color:#ffd2d6}}
</style>
</head>
<body>
<main class="wpbb-demo-lock">
<p class="wpbb-demo-lock__eyebrow"><?php echo esc_html( $theme_name ); ?></p>
<h1><?php esc_html_e( 'Private website preview', 'wp-theme' ); ?></h1>
<p><?php esc_html_e( 'Enter the frontend password to continue.', 'wp-theme' ); ?></p>
<?php if ( $error ) : ?><p class="wpbb-demo-lock__error" role="alert"><?php echo esc_html( $error ); ?></p><?php endif; ?>
<form method="post">
<input type="hidden" name="wpbb_demo_action" value="unlock">
<input type="hidden" name="wpbb_demo_redirect_to" value="<?php echo esc_attr( $request_uri ); ?>">
<?php wp_nonce_field( 'wpbb_child_demo_unlock', 'wpbb_demo_nonce' ); ?>
<label for="wpbb-demo-password"><?php esc_html_e( 'Password', 'wp-theme' ); ?></label>
<input id="wpbb-demo-password" name="wpbb_demo_password" type="password" autocomplete="current-password" autofocus required>
<button type="submit"><?php esc_html_e( 'View website', 'wp-theme' ); ?></button>
</form>
<p class="wpbb-demo-lock__meta"><?php echo esc_html( $site_name ); ?></p>
</main>
</body>
</html><?php
        exit;
    }
}
add_action( 'template_redirect', 'wpbb_child_demo_render_gate', -1000 );

if ( ! function_exists( 'wpbb_child_demo_apply_protection_settings' ) ) {
    /**
     * Save the shared protection state. Returns true when anything changed.
     */
    function wpbb_child_demo_apply_protection_settings( $enabled, $password = '' ) {
        $keys = wpbb_child_demo_protection_keys();
        $enabled = (bool) $enabled;
        $password = (string) $password;
        $changed = false;

        $new_enabled = $enabled ? '1' : '0';
        $old_enabled = (string) get_option( $keys['enabled'], '1' );
        if ( $old_enabled !== $new_enabled ) {
            update_option( $keys['enabled'], $new_enabled, false );
            $changed = true;
        }

        if ( '' !== $password ) {
            update_option( $keys['hash'], wp_hash_password( $password ), false );
            $changed = true;
        }

        if ( $changed ) {
            update_option( $keys['revision'], (string) ( (int) get_option( $keys['revision'], 1 ) + 1 ), false );
            wpbb_child_demo_set_cookie( time() - HOUR_IN_SECONDS, '' );
        }

        return $changed;
    }
}

if ( ! function_exists( 'wpbb_child_demo_save_protection' ) ) {
    /** Standalone fallback-page save handler. */
    function wpbb_child_demo_save_protection() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'You are not allowed to change these settings.', 'wp-theme' ) );
        }
        check_admin_referer( 'wpbb_child_demo_save_protection' );

        $enabled  = isset( $_POST['enabled'] ) && '1' === (string) wp_unslash( $_POST['enabled'] );
        $password = isset( $_POST['password'] ) ? (string) wp_unslash( $_POST['password'] ) : '';
        wpbb_child_demo_apply_protection_settings( $enabled, $password );

        $redirect = add_query_arg(
            array( 'page' => 'wp-theme-settings', 'wpbb_protection_saved' => '1' ),
            admin_url( 'options-general.php' )
        );
        wp_safe_redirect( $redirect );
        exit;
    }
}
add_action( 'admin_post_wpbb_child_demo_save_protection', 'wpbb_child_demo_save_protection' );

if ( ! function_exists( 'wpbb_child_demo_save_protection_ajax' ) ) {
    /** Save button used by the embedded General-tab controls. */
    function wpbb_child_demo_save_protection_ajax() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'You are not allowed to change these settings.', 'wp-theme' ) ), 403 );
        }
        check_ajax_referer( 'wpbb_child_demo_protection_settings', 'nonce' );

        $enabled  = isset( $_POST['enabled'] ) && '1' === (string) wp_unslash( $_POST['enabled'] );
        $password = isset( $_POST['password'] ) ? (string) wp_unslash( $_POST['password'] ) : '';
        wpbb_child_demo_apply_protection_settings( $enabled, $password );

        wp_send_json_success(
            array(
                'enabled' => $enabled,
                'status'  => $enabled ? __( 'Enabled', 'wp-theme' ) : __( 'Disabled', 'wp-theme' ),
                'message' => __( 'Protection settings saved.', 'wp-theme' ),
            )
        );
    }
}
add_action( 'wp_ajax_wpbb_child_demo_save_protection_ajax', 'wpbb_child_demo_save_protection_ajax' );

if ( ! function_exists( 'wpbb_child_demo_save_protection_with_theme_settings' ) ) {
    /**
     * Save the embedded fields when the normal ACF Theme Settings Update button
     * is used. The controls intentionally live in the parent form; no nested form.
     */
    function wpbb_child_demo_save_protection_with_theme_settings( $post_id = '' ) {
        if ( ! current_user_can( 'manage_options' ) || empty( $_POST['wpbb_demo_protection_present'] ) ) {
            return;
        }
        $nonce = isset( $_POST['wpbb_demo_protection_settings_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['wpbb_demo_protection_settings_nonce'] ) ) : '';
        if ( ! wp_verify_nonce( $nonce, 'wpbb_child_demo_protection_settings' ) ) {
            return;
        }

        $enabled  = isset( $_POST['wpbb_demo_protection_enabled_ui'] ) && '1' === (string) wp_unslash( $_POST['wpbb_demo_protection_enabled_ui'] );
        $password = isset( $_POST['wpbb_demo_protection_password_ui'] ) ? (string) wp_unslash( $_POST['wpbb_demo_protection_password_ui'] ) : '';
        wpbb_child_demo_apply_protection_settings( $enabled, $password );
    }
}
add_action( 'acf/save_post', 'wpbb_child_demo_save_protection_with_theme_settings', 20 );

if ( ! function_exists( 'wpbb_child_demo_protection_admin_styles' ) ) {
    function wpbb_child_demo_protection_admin_styles() {
        $page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';
        if ( 'wp-theme-settings' !== $page || ! current_user_can( 'manage_options' ) ) return;
        ?>
        <style id="wpbb-demo-protection-settings-css">
        .wpbb-demo-protection-settings{box-sizing:border-box;margin:18px 0 24px;padding:22px 24px;border:1px solid #cdd6e3;border-left:4px solid #253e5b;border-radius:12px;background:#fff;box-shadow:0 1px 2px rgba(15,23,42,.04)}
        .wpbb-demo-protection-settings__heading{display:flex;flex-wrap:wrap;align-items:center;gap:10px;margin:0 0 8px}
        .wpbb-demo-protection-settings__status{display:inline-flex;align-items:center;min-height:24px;padding:2px 9px;border-radius:999px;background:#e8f3ec;color:#126b35;font-size:12px;font-weight:700}
        .wpbb-demo-protection-settings__status.is-disabled{background:#f1f1f1;color:#50575e}
        .wpbb-demo-protection-settings p{max-width:920px}
        .wpbb-demo-protection-settings__saved,.wpbb-demo-protection-settings__result.is-success{color:#16813b;font-weight:700}
        .wpbb-demo-protection-settings__result.is-error{color:#b32d2e;font-weight:700}
        .wpbb-demo-protection-settings__controls{display:grid;grid-template-columns:minmax(230px,.8fr) minmax(300px,1.2fr) auto;gap:16px;align-items:end;max-width:1120px;margin-top:18px}
        .wpbb-demo-protection-settings__toggle{display:flex;gap:9px;align-items:center;min-height:42px;margin:0;padding:9px 11px;border:1px solid #dcdcde;border-radius:8px;background:#f8f9fa}
        .wpbb-demo-protection-settings__toggle input{margin:0}
        .wpbb-demo-protection-settings__password{display:block;min-width:0;margin:0}
        .wpbb-demo-protection-settings__password strong{display:block;margin-bottom:6px}
        .wpbb-demo-protection-settings__password input{box-sizing:border-box;width:100%;max-width:none;min-height:42px}
        .wpbb-demo-protection-settings__controls>.button{min-height:42px;white-space:nowrap;justify-self:start}
        .wpbb-demo-protection-settings__result{grid-column:1/-1;margin:0;min-height:18px}
        @media(max-width:1100px){.wpbb-demo-protection-settings__controls{grid-template-columns:minmax(220px,.8fr) minmax(280px,1.2fr)}.wpbb-demo-protection-settings__controls>.button{grid-column:1/-1}}
        @media(max-width:782px){.wpbb-demo-protection-settings{padding:18px}.wpbb-demo-protection-settings__controls{grid-template-columns:1fr}.wpbb-demo-protection-settings__controls>.button{grid-column:auto}}
        </style>
        <?php
    }
}
add_action( 'admin_head', 'wpbb_child_demo_protection_admin_styles', 90 );

if ( ! function_exists( 'wpbb_child_demo_protection_admin_script' ) ) {
    /**
     * ACF message fields sanitize form controls from their message HTML. Keep
     * only inert placeholders in the field markup, then create the checkbox and
     * password input in the DOM after ACF has rendered the General tab.
     */
    function wpbb_child_demo_protection_admin_script() {
        $page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';
        if ( 'wp-theme-settings' !== $page || ! current_user_can( 'manage_options' ) ) return;
        ?>
        <script id="wpbb-demo-protection-settings-js">
        (function(){
            'use strict';

            function bootProtectionControls(){
                var root=document.querySelector('.wpbb-demo-protection-settings[data-wpbb-protection]');
                if(!root || root.dataset.wpbbReady==='1') return;

                var controls=root.querySelector('[data-wpbb-protection-controls]');
                var status=root.querySelector('[data-wpbb-protection-status]');
                var result=root.querySelector('[data-wpbb-protection-result]');
                var button=root.querySelector('[data-wpbb-protection-save]');
                if(!controls || !button) return;

                root.dataset.wpbbReady='1';

                var enabled=root.getAttribute('data-enabled')==='1';
                var toggleLabel=document.createElement('label');
                toggleLabel.className='wpbb-demo-protection-settings__toggle';
                var toggle=document.createElement('input');
                toggle.type='checkbox';
                toggle.name='wpbb_demo_protection_enabled_ui';
                toggle.value='1';
                toggle.checked=enabled;
                toggle.setAttribute('data-wpbb-protection-enabled','');
                var toggleText=document.createElement('strong');
                toggleText.textContent=root.getAttribute('data-enable-label')||'Enable frontend password protection';
                toggleLabel.appendChild(toggle);
                toggleLabel.appendChild(toggleText);

                var passwordLabel=document.createElement('label');
                passwordLabel.className='wpbb-demo-protection-settings__password';
                passwordLabel.setAttribute('for','wpbb-demo-new-password');
                var passwordTitle=document.createElement('strong');
                passwordTitle.textContent=root.getAttribute('data-password-label')||'Change password';
                var password=document.createElement('input');
                password.id='wpbb-demo-new-password';
                password.className='regular-text';
                password.type='password';
                password.name='wpbb_demo_protection_password_ui';
                password.autocomplete='new-password';
                password.placeholder=root.getAttribute('data-password-placeholder')||'Leave blank to keep the current password';
                password.setAttribute('data-wpbb-protection-password','');
                passwordLabel.appendChild(passwordTitle);
                passwordLabel.appendChild(password);

                var present=document.createElement('input');
                present.type='hidden';
                present.name='wpbb_demo_protection_present';
                present.value='1';
                var nonce=document.createElement('input');
                nonce.type='hidden';
                nonce.name='wpbb_demo_protection_settings_nonce';
                nonce.value=root.getAttribute('data-nonce')||'';

                controls.insertBefore(toggleLabel,button);
                controls.insertBefore(passwordLabel,button);
                controls.appendChild(present);
                controls.appendChild(nonce);

                function setResult(message,type){
                    if(!result) return;
                    result.textContent=message||'';
                    result.className='wpbb-demo-protection-settings__result'+(type?' is-'+type:'');
                }

                button.addEventListener('click',function(){
                    var data=new FormData();
                    data.append('action','wpbb_child_demo_save_protection_ajax');
                    data.append('nonce',root.getAttribute('data-nonce')||'');
                    data.append('enabled',toggle.checked?'1':'0');
                    data.append('password',password.value||'');
                    button.disabled=true;
                    setResult(root.getAttribute('data-saving-label')||'Saving…','');
                    fetch(window.ajaxurl||'<?php echo esc_js( admin_url( 'admin-ajax.php' ) ); ?>',{
                        method:'POST',
                        credentials:'same-origin',
                        body:data
                    })
                    .then(function(response){
                        if(!response.ok) throw new Error('HTTP '+response.status);
                        return response.json();
                    })
                    .then(function(response){
                        if(!response || !response.success){
                            throw new Error(response && response.data && response.data.message ? response.data.message : (root.getAttribute('data-failed-label')||'Save failed.'));
                        }
                        if(status){
                            status.textContent=response.data.status;
                            status.classList.toggle('is-disabled',!response.data.enabled);
                        }
                        root.setAttribute('data-enabled',response.data.enabled?'1':'0');
                        toggle.checked=!!response.data.enabled;
                        password.value='';
                        setResult(response.data.message,'success');
                    })
                    .catch(function(error){
                        setResult(error && error.message ? error.message : (root.getAttribute('data-failed-label')||'Save failed.'),'error');
                    })
                    .finally(function(){ button.disabled=false; });
                });
            }

            document.addEventListener('DOMContentLoaded',bootProtectionControls);
            document.addEventListener('acf/setup_fields',bootProtectionControls);
            if(window.acf && typeof window.acf.addAction==='function'){
                window.acf.addAction('ready',bootProtectionControls);
                window.acf.addAction('append',bootProtectionControls);
            }
            setTimeout(bootProtectionControls,250);
        }());
        </script>
        <?php
    }
}
add_action( 'admin_footer', 'wpbb_child_demo_protection_admin_script', 90 );

if ( ! function_exists( 'wpbb_child_demo_protection_settings_markup' ) ) {
    /**
     * Embedded controls for the parent's General tab. ACF message fields strip
     * input elements, so this markup contains only safe placeholders; the actual
     * form controls are created by wpbb_child_demo_protection_admin_script().
     */
    function wpbb_child_demo_protection_settings_markup() {
        $enabled = wpbb_child_demo_protection_enabled();
        $nonce   = wp_create_nonce( 'wpbb_child_demo_protection_settings' );
        ob_start();
        ?>
        <section
            class="wpbb-demo-protection-settings"
            data-wpbb-protection
            data-enabled="<?php echo $enabled ? '1' : '0'; ?>"
            data-nonce="<?php echo esc_attr( $nonce ); ?>"
            data-enable-label="<?php echo esc_attr__( 'Enable frontend password protection', 'wp-theme' ); ?>"
            data-password-label="<?php echo esc_attr__( 'Change password', 'wp-theme' ); ?>"
            data-password-placeholder="<?php echo esc_attr__( 'Leave blank to keep the current password', 'wp-theme' ); ?>"
            data-saving-label="<?php echo esc_attr__( 'Saving…', 'wp-theme' ); ?>"
            data-failed-label="<?php echo esc_attr__( 'Save failed.', 'wp-theme' ); ?>"
            aria-labelledby="wpbb-demo-protection-title"
        >
            <h2 class="wpbb-demo-protection-settings__heading" id="wpbb-demo-protection-title">
                <?php esc_html_e( 'Frontend Password Protection', 'wp-theme' ); ?>
                <span class="wpbb-demo-protection-settings__status<?php echo $enabled ? '' : ' is-disabled'; ?>" data-wpbb-protection-status><?php echo $enabled ? esc_html__( 'Enabled', 'wp-theme' ) : esc_html__( 'Disabled', 'wp-theme' ); ?></span>
            </h2>
            <p><?php esc_html_e( 'Protect the complete public website while keeping WordPress administration, AJAX, cron, REST and XML-RPC available. The setting is shared when switching between the maintained child themes.', 'wp-theme' ); ?></p>
            <p><?php esc_html_e( 'Initial password:', 'wp-theme' ); ?> <code>wp@demo</code>. <?php esc_html_e( 'Leave the password field blank to keep the existing password. Visitor access lasts 24 hours by default.', 'wp-theme' ); ?></p>
            <div class="wpbb-demo-protection-settings__controls" data-wpbb-protection-controls>
                <button class="button button-primary" type="button" data-wpbb-protection-save><?php esc_html_e( 'Save protection settings', 'wp-theme' ); ?></button>
                <p class="wpbb-demo-protection-settings__result" data-wpbb-protection-result aria-live="polite"></p>
            </div>
            <p class="description"><?php esc_html_e( 'Use the controls above to enable/disable the gate or change its password. Purge page, server and CDN caches after changing this setting.', 'wp-theme' ); ?></p>
        </section>
        <?php
        return trim( (string) ob_get_clean() );
    }
}

if ( ! function_exists( 'wpbb_child_demo_add_general_settings' ) ) {
    function wpbb_child_demo_add_general_settings( $markup ) {
        return (string) $markup . wpbb_child_demo_protection_settings_markup();
    }
}
add_filter( 'wp_theme_general_settings_extension_markup', 'wpbb_child_demo_add_general_settings', 20 );

if ( ! function_exists( 'wpbb_child_demo_fallback_settings_page' ) ) {
    function wpbb_child_demo_fallback_settings_page() {
        $enabled = wpbb_child_demo_protection_enabled();
        echo '<div class="wrap"><h1>' . esc_html__( 'Theme Settings', 'wp-theme' ) . '</h1>';
        ?>
        <section class="wpbb-demo-protection-settings">
            <h2><?php esc_html_e( 'Frontend Password Protection', 'wp-theme' ); ?></h2>
            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                <input type="hidden" name="action" value="wpbb_child_demo_save_protection">
                <?php wp_nonce_field( 'wpbb_child_demo_save_protection' ); ?>
                <p><label><input type="checkbox" name="enabled" value="1" <?php checked( $enabled ); ?>> <strong><?php esc_html_e( 'Enable frontend password protection', 'wp-theme' ); ?></strong></label></p>
                <p><label><strong><?php esc_html_e( 'Change password', 'wp-theme' ); ?></strong><br><input class="regular-text" type="password" name="password" autocomplete="new-password" placeholder="<?php esc_attr_e( 'Leave blank to keep the current password', 'wp-theme' ); ?>"></label></p>
                <?php submit_button( __( 'Save protection settings', 'wp-theme' ) ); ?>
            </form>
        </section>
        <?php
        echo '</div>';
    }
}

if ( ! function_exists( 'wpbb_child_demo_register_fallback_settings' ) ) {
    function wpbb_child_demo_register_fallback_settings() {
        global $submenu;
        foreach ( (array) ( $submenu['options-general.php'] ?? array() ) as $item ) {
            if ( 'wp-theme-settings' === ( $item[2] ?? '' ) ) return;
        }
        add_options_page( __( 'Theme Settings', 'wp-theme' ), __( 'Theme Settings', 'wp-theme' ), 'manage_options', 'wp-theme-settings', 'wpbb_child_demo_fallback_settings_page' );
    }
}
add_action( 'admin_menu', 'wpbb_child_demo_register_fallback_settings', 999 );

<?php
namespace SocialShareBar;

add_action( 'wp', __NAMESPACE__ . '\sharebar_init' );
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\register_scripts' );

function register_scripts() {
    wp_register_style( 'socialsharebar', SOCIALSHAREBAR_URL . 'public/css/sharebar.css' );
}

function enqueue_scripts() {
    wp_enqueue_style( 'socialsharebar' );
}

function sharebar_init() {
    if ( ! ( is_single() || is_page() ) ) {
        return;
    }

    $options = get_option( 'socialsharebar_options' );

    if ( ! key_exists( get_post_type(), $options['types'] ) ) {
        return;
    }

    $locations = $options['locations'];
    if ( 0 < count( $locations ) ) {
        add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\enqueue_scripts' );
    }

    if ( isset( $locations['below_title'] ) && 'on' === $locations['below_title'] ) {
        add_filter( 'the_title', __NAMESPACE__ . '\display_below_post_title', 99, 2 );
    }

    if ( isset( $locations['after_content'] ) && 'on' === $locations['after_content'] ) {
        add_filter( 'the_content', __NAMESPACE__ . '\display_after_content', 99 );
    }

    if ( isset( $locations['featured_image'] ) && 'on' === $locations['featured_image'] ) {
        add_filter( 'post_thumbnail_html', __NAMESPACE__ . '\display_in_featured_image', 99, 5 );
    }

    if ( isset( $locations['left'] ) && 'on' === $locations['left'] ) {
        add_action( 'wp_footer', __NAMESPACE__ . '\display_floating_left', 99 );
    }
}

function display_below_post_title( $title, $post_id ) {
    if ( !in_the_loop() ) {
        return $title;
    }
    remove_filter( 'the_title', __NAMESPACE__ . '\display_below_post_title', 99 );
    return sprintf(
        '%s <div class="socialsharebar ssb-after-title">%s</div>',
        $title,
        get_sharebar_code( get_permalink() )
    );
}

function display_in_featured_image( $html, $post_id, $post_thumbnail_id, $size, $attr ) {
    if ( ! in_the_loop() ) {
        return $html;
    }
    remove_filter( 'post_thumbnail_html', __NAMESPACE__ . '\display_in_featured_image', 99 );
    return sprintf(
        '%s<div class="socialsharebar socialsharebar-featured-image">%s</div>',
        $html,
        get_sharebar_code( get_permalink() )
    );
}

function display_floating_left() {
    printf(
        '<div class="socialsharebar socialsharebar-float">%s</div>',
        get_sharebar_code( get_permalink() )
    );
}

function display_after_content( $content ) {
    if ( ! in_the_loop() ) {
        return $content;
    }
    return sprintf(
        '%s<div class="socialsharebar ssb-after-content">%s</div>',
        $content,
        get_sharebar_code( get_permalink() )
    );
}

function get_sharebar_code($link = null) {
    $options = get_option( 'socialsharebar_options' );
    $color = isset( $options['color'] ) ? $options['color'] : '';
    ob_start();
    foreach($options['networks'] as $network => $state) {
        echo get_share_button_code( $network, $link, $color );
    }
    return ob_get_clean();
}

function get_share_button_code( $network, $link, $color = null ) {
    ob_start();
    switch( strtolower( $network ) ) {
        case 'facebook':
            print facebook_button( $link, $color );
            break;
        case 'twitter':
            print twitter_button( $link, $color );
            print '</span>';
            break;
        case 'pinterest':
            print pinterest_button( $link, $color );
            break;
        case 'linkedin':
            print linkedin_button( $link, $color );
            break;
        case 'whatsapp':
            if ( wp_is_mobile() ) {
                print whatsapp_button( $link, $color );
            }
            break;
        default: break;
    }
    return ob_get_clean();
}

function facebook_button( $permalink, $color = null) {
    return generate_button(
        'facebook',
        "https://www.facebook.com/sharer.php?u=" . urlencode( $permalink ),
        ! empty( $color ) ? $color : '#4172BB'
    );
}

function twitter_button( $permalink, $color = null ) {
    return generate_button(
        'twitter',
        "https://twitter.com/share?url=" . urlencode( $permalink ),
        ! empty( $color ) ? $color : '#1DA1F2'
    );
}

function pinterest_button( $permalink, $color = null ) {
    return generate_button(
        'pinterest',
        "https://pinterest.com/pin/create/bookmarklet/?url=" . urlencode( $permalink ),
        ! empty( $color ) ? $color : '#BD081C'
    );
}

function linkedin_button( $permalink, $color = null ) {
    return generate_button(
        'linkedin',
        "https://www.linkedin.com/shareArticle?url=" . urlencode( $permalink ),
        ! empty( $color ) ? $color : '#0077B5'
    );
}

function whatsapp_button( $permalink, $color = null ) {
    return generate_button(
        'whatsapp',
        "https://wa.me/?text=" . urlencode( $permalink ),
        ! empty( $color ) ? $color : '#25D366'
    );
}

function generate_button( $icon, $link, $color = null ) {
    $options = get_option( 'socialsharebar_options' );
    $size = $options['size'];
    ob_start();
    include SOCIALSHAREBAR_PATH . 'public/images/' . $icon . '.svg';
    $icon = ob_get_clean();

    return sprintf(
        '<a href="%s" target="_blank" class="socialsharebar-link socialsharebar-link-%s"%s><span class="socialsharebar-icon">%s</span></a>',
        esc_attr( $link ),
        esc_attr( $size ),
        ! empty( $color ) ? 'style="background-color: ' . esc_attr($color) . '"' : '',
        ! empty( $icon ) ? $icon : ''
    );
}

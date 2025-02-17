<?php
namespace SocialShareBar;

add_shortcode( 'sharing_bar', __NAMESPACE__ . '\shortcode' );

function shortcode( $atts ) {
    wp_enqueue_style( 'socialsharebar' );

    $atts = shortcode_atts( [
        'post' => null,
        'link' => null
    ], $atts );
    
    $link = null;
    
    if ( !empty( $atts['post'] ) && empty( $atts['link'] ) ) {
        $link = get_permalink( $atts['post'] );
    }
    
    if ( !empty( $atts['link'] ) ) {
        $link = $atts['link'];
    }

    if ( empty( $link ) && in_the_loop() ) {
        $link = get_permalink();   
    }
    
    return sprintf( '<div class="socialsharebar socialsharebar-featured-image">%s</div>', get_sharebar_code( $link ) );
}
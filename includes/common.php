<?php
namespace SocialShareBar;

function networks() {
    $networks = [
        'Facebook',
        'Twitter',
        'Pinterest',
        'LinkedIn',
        'WhatsApp'
    ];

    $options = get_option( 'socialsharebar_options' );
    if ( isset( $options['network_order' ] ) ) {
        $ordered_networks = array_flip( $options['network_order'] );
        $new_networks = array_diff( $ordered_networks, $networks );
        foreach( $new_networks as $network ) {
            $ordered_networks []= $network;
        }
        $networks = $ordered_networks;
        unset( $ordered_networks, $new_networks );
    }

    return $networks;
}

function sizes() {
    return [
        'small',
        'medium',
        'large'
    ];
}

function locations() {
    return [
        'below_title' => __( 'Below the post title' ),
        'left' => __( 'Floating on the left' ),
        'after_content' => __( 'After the post content' ),
        'featured_image' => __( 'Inside the featured image' )
    ];
}
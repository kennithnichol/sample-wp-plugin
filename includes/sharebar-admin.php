<?php
namespace SocialShareBar;

add_action( 'admin_menu', __NAMESPACE__ . '\register_menu_item' );
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\admin_scripts' );
add_action( 'admin_init', __NAMESPACE__ . '\settings_init' );

function admin_scripts( $hook ) {
    if ( 'settings_page_socialsharebar' !== $hook ) {
        return;
    }
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_style( 'sharebar-styles', SOCIALSHAREBAR_URL . 'admin/css/settings.css' );
    wp_enqueue_script( 'sharebar-scripts', SOCIALSHAREBAR_URL . 'admin/js/settings.js', ['jquery-ui-sortable', 'wp-color-picker'], false, true );
}

function register_menu_item() {
    add_options_page(
        'Social Share Bar',
        __( 'Social Share Bar' ),
        'manage_options',
        'socialsharebar',
        __NAMESPACE__ . '\options_page'
    );
}

function options_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
            return;
    }

    settings_errors( 'socialsharebar_messages' );
    ?>
    <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        <form action="options.php" method="post">
            <?php
            settings_fields( 'socialsharebar' );
            do_settings_sections( 'socialsharebar' );
            submit_button( 'Save Settings' );
            ?>
        </form>
    </div>
<?php
}

function settings_init() {
    if ( ! get_option( 'socialsharebar_options' ) ) {
        foreach( networks() as $network ) {
            $networks[$network] = 'on';
        }
        $defaults = [
            'types' => ['post' => 'on', 'page' => 'on'],
            'size' => 'medium',
            'locations' => [
                'left' => 'on',
                'after_content' => 'on'
            ],
            'networks' => $networks
        ];
        add_option( 'socialsharebar_options',
            $defaults
        );
    }

    register_setting( 'socialsharebar', 'socialsharebar_options' );

    add_settings_section(
        'socialsharebar_settings',
        __( 'Settings' ),
        __NAMESPACE__ . '\settings_cb',
        'socialsharebar'
    );

    add_settings_field(
        'socialsharebar_types',
        __( 'Post Types' ),
        __NAMESPACE__ . '\settings_types_cb',
        'socialsharebar',
        'socialsharebar_settings'
    );

    add_settings_field(
        'socialsharebar_networks',
        __( 'Social Networks' ),
        __NAMESPACE__ . '\settings_networks_cb',
        'socialsharebar',
        'socialsharebar_settings'
    );

    add_settings_field(
        'socialsharebar_button_size',
        __( 'Button Size' ),
        __NAMESPACE__ . '\settings_button_size_cb',
        'socialsharebar',
        'socialsharebar_settings'
    );

    add_settings_field(
        'socialsharebar_locations',
        __( 'Sharebar Locations' ),
        __NAMESPACE__ . '\settings_locations_cb',
        'socialsharebar',
        'socialsharebar_settings'
    );

    add_settings_field(
        'socialsharebar_button_color',
        __( 'Button Color' ),
        __NAMESPACE__ . '\settings_button_color_cb',
        'socialsharebar',
        'socialsharebar_settings',
        [
            'description' => __( 'Choose a color for the all buttons, or leave it blank to use the defeault social network colors.' )
        ]
    );
}

function settings_cb( $args ) {
    printf( '<p id="%s">%s</p>',
        esc_attr( $args['id'] ),
        esc_html( 'Configure the share bar to suit your needs.' )
    );
}

function settings_types_cb( $args ) {
    $options = get_option( 'socialsharebar_options' );
    $types = get_post_types( ['public' => true] );
    foreach( $types as $post_type ) {
        $saved_value = isset( $options['types'][$post_type] ) ? $options['types'][$post_type] : '';
        printf(
            '<p><input type="checkbox" name="socialsharebar_options[types][%s]" id="%s" %s> <label for="%2$s">%s</label></p>',
            esc_attr( $post_type ),
            esc_attr( "socialsharebar_type_$post_type" ),
            checked( $saved_value, 'on' , false ),
            esc_html( $post_type )
        );
    }
}

function settings_networks_cb( $args ) {
    $options = get_option( 'socialsharebar_options' );
    $networks = networks();
    print '<ul id="sharebar-networks">';
    foreach( $networks as $index => $network ) {
        $saved_value = isset( $options['networks'][$network] ) ? $options['networks'][$network] : '';
        $position = isset( $options['network_order'][$network] ) ? $options['network_order'][$network] : $index;
        printf(
            '<li class="ui-state-default ui-sortable-handle"><span class="dashicons dashicons-menu-alt2"></span><input type="checkbox" name="socialsharebar_options[networks][%s]" id="%s" %s> <label for="%2$s">%s</label><input class="sharebar-network-order" type="hidden" name="socialsharebar_options[network_order][%1$s]" value="%s" /></li>',
            esc_attr( $network ),
            esc_attr( "socialsharebar_type_$network" ),
            checked( $saved_value, 'on' , false ),
            esc_html( $network ),
            esc_attr( $position )
        );
    }
    print '</ul>';
}

function settings_button_size_cb( $args ) {
    $options = get_option( 'socialsharebar_options' );
    $sizes = sizes();

    foreach( $sizes as $size ) {
        $saved_value = isset( $options['size'] ) ? $options['size'] : '';
        printf(
            '<p><input type="radio" name="socialsharebar_options[size]" id="%s" value="%s" %s> <label for="%1$s">%s</label></p>',
            esc_attr( "socialsharebar_type_$size" ),
            esc_attr( $size ),
            checked( $saved_value, $size , false ),
            esc_html( $size )
        );
    }
}

function settings_locations_cb( $args ) {
    $options = get_option( 'socialsharebar_options' );
    $locations = locations();

    foreach( $locations as $location => $label ) {
        $saved_value = isset( $options['locations'][$location] ) ? $options['locations'][$location] : '';
        printf(
            '<p><input type="checkbox" name="socialsharebar_options[locations][%s]" id="%s" %s> <label for="%2$s">%s</label></p>',
            esc_attr( $location ),
            esc_attr( "socialsharebar_location_$location" ),
            checked( $saved_value, 'on', false ),
            esc_html( $label )
        );
    }
}

function settings_button_color_cb( $args ) {
    $options = get_option( 'socialsharebar_options' );
    printf(
        '<p><input type="text" value="%s" class="button-color-picker" name="socialsharebar_options[color]" id="%s" /></p><p class="description">%s</p>',
        isset( $options['color'] ) ? esc_attr( $options['color'] ) : '',
        esc_attr( "socialsharebar_button_color" ),
        $args['description']
    );
}
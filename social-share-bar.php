<?php
/**
 * Plugin Name: Social Share Bar
 * Author: Ken Nichol
 *
 * Description: Automatically display selected social network sharing buttons on posts and/or pages.
 *
 * Version: 1.0.0
 *
 * License: GPLv2
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace SocialShareBar;

if ( ! function_exists( 'add_filter' ) ) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit();
}

define( 'SOCIALSHAREBAR_URL', plugin_dir_url( __FILE__ ) );
define( 'SOCIALSHAREBAR_PATH', plugin_dir_path( __FILE__ ) );

require_once SOCIALSHAREBAR_PATH . 'includes/common.php';
require_once SOCIALSHAREBAR_PATH . 'includes/sharebar.php';
require_once SOCIALSHAREBAR_PATH . 'includes/shortcode.php';

if ( is_admin() ) {
    require_once SOCIALSHAREBAR_PATH . 'includes/sharebar-admin.php';    
}


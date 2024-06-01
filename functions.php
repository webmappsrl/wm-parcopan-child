<?php
// Exit if accessed directly
if (!defined('ABSPATH')) exit;

require('shortcodes/single_track.php');
require('shortcodes/single_poi.php');
require('shortcodes/grid_track.php');
require('shortcodes/grid_poi.php');
require('shortcodes/single_layer.php');

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if (!function_exists('chld_thm_cfg_locale_css')) :
    function chld_thm_cfg_locale_css($uri)
    {
        if (empty($uri) && is_rtl() && file_exists(get_template_directory() . '/rtl.css'))
            $uri = get_template_directory_uri() . '/rtl.css';
        return $uri;
    }
endif;
add_filter('locale_stylesheet_uri', 'chld_thm_cfg_locale_css');

// END ENQUEUE PARENT ACTION

//Swiper Slider CSS da CDN
function child_theme_enqueue_swiper()
{
    wp_enqueue_style('swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css');
    wp_enqueue_script('swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', array(), null, true);
}
add_action('wp_enqueue_scripts', 'child_theme_enqueue_swiper');

// Lightbox2 CSS and JS from CDN
function child_theme_enqueue_lightbox2_cdn()
{
    wp_enqueue_style('lightbox2-css', 'https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css');
    wp_enqueue_script('lightbox2-js', 'https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js', array('jquery'), '', true);
    // Configura Lightbox2
    add_action('wp_footer', 'configure_lightbox2');
}
add_action('wp_enqueue_scripts', 'child_theme_enqueue_lightbox2_cdn');

// Configura Lightbox2
function configure_lightbox2()
{
?>
    <script>
        lightbox.option({
            'fadeDuration': 50, // Durata della fade in millisecondi (0.2 secondi)
            'resizeDuration': 50, // Durata del resize in millisecondi (0.2 secondi)
            'wrapAround': true
        });
    </script>
<?php
}

//Font awesome
function load_font_awesome()
{
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css');
}
add_action('wp_enqueue_scripts', 'load_font_awesome');

//Slug
function wm_custom_slugify($title)
{
    $title = iconv('UTF-8', 'ASCII//TRANSLIT', $title);
    $title = str_replace('–', '-', $title);
    $title = str_replace("’", '', $title);
    $title = preg_replace('!\s+!', ' ', $title);
    $slug = sanitize_title_with_dashes($title);
    return $slug;
}

//Hide Featured image if empity
function hide_featured_image_without_bg_image()
{
?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var sections = document.querySelectorAll('section.wm_featured_image');

            sections.forEach(function(section) {
                var bgImageDiv = section.querySelector('div[style*="background-image"]');

                if (!bgImageDiv || bgImageDiv.style.backgroundImage === 'none' || !bgImageDiv.style.backgroundImage.includes('url')) {
                    section.style.display = 'none';
                }
            });
        });
    </script>
<?php
}
add_action('wp_footer', 'hide_featured_image_without_bg_image');

//ACF Gallery with Swiper Slider and Lightbox
function acf_gallery_swiper_shortcode($atts)
{
    $atts = shortcode_atts(array(
        'field' => '',
        'post_id' => false,
    ), $atts);

    $images = get_field($atts['field'], $atts['post_id']);

    if (!$images) {
        return '';
    }

    $output = '<div class="wm-acf-gallery swiper-container acf-gallery-swiper">';
    $output .= '<div class="swiper-wrapper">';
    foreach ($images as $image) {
        $img_url = $image['url'];
        $img_alt = $image['alt'];
        $output .= '<div class="swiper-slide">';
        $output .= '<a href="' . $img_url . '" data-lightbox="acf-gallery" data-title="' . $img_alt . '">';
        $output .= '<img src="' . $img_url . '" alt="' . $img_alt . '">';
        $output .= '</a>';
        $output .= '</div>';
    }
    $output .= '</div>';
    $output .= '<div class="swiper-pagination"></div>';
    $output .= '<div class="swiper-button-prev"></div>';
    $output .= '<div class="swiper-button-next"></div>';
    $output .= '</div>';

    // Initialize Swiper Slider
    $output .= '<script>
        document.addEventListener("DOMContentLoaded", function() {
            var swiper = new Swiper(".acf-gallery-swiper", {
                slidesPerView: 1,
                spaceBetween: 10,
                breakpoints: {
                    768: {
                        slidesPerView: 3,
                        spaceBetween: 20
                    },
                },
                freeMode: true,
                loop: true,
                pagination: {
                    el: ".swiper-pagination",
                    clickable: true,
                },
                navigation: {
                    nextEl: ".swiper-button-next",
                    prevEl: ".swiper-button-prev",
                },
            });
        });
    </script>';

    return $output;
}
add_shortcode('acf_gallery_swiper', 'acf_gallery_swiper_shortcode');

// Hide wm-acf-gallery if empity
function hide_acf_gallery_without_images()
{
?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var galleries = document.querySelectorAll('.wm-acf-gallery');

            galleries.forEach(function(gallery) {
                var images = gallery.querySelectorAll('img');

                if (!images.length) {
                    gallery.style.display = 'none';
                }
            });
        });
    </script>
<?php
}
add_action('wp_footer', 'hide_acf_gallery_without_images');

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
    add_action('wp_footer', 'configure_lightbox2');
}
add_action('wp_enqueue_scripts', 'child_theme_enqueue_lightbox2_cdn');

// Configuration Lightbox2
function configure_lightbox2()
{
?>
    <script>
        lightbox.option({
            'fadeDuration': 50,
            'resizeDuration': 50,
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

// Hide wm-acf-gallery if empty and adjust container classes
function hide_acf_gallery_and_adjust_container()
{
?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var galleries = document.querySelectorAll('.wm-acf-gallery');

            galleries.forEach(function(gallery) {
                var images = gallery.querySelectorAll('img');

                if (images.length === 0) {
                    var galleryContainer = gallery.closest('div.wm-gallery-container');
                    if (galleryContainer) {
                        galleryContainer.style.display = 'none';
                    }

                    var contentContainer = document.querySelector('div.wm-content-container');
                    if (contentContainer) {
                        contentContainer.style.width = '100%';
                    }
                }
            });
        });
    </script>
<?php
}
add_action('wp_footer', 'hide_acf_gallery_and_adjust_container');

// Change 'Non categorizzato' and 'Uncategorized' to 'News' in breadcrumb
function change_breadcrumb_text()
{
?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var breadcrumbItems = document.querySelectorAll('.wm-breadcrumb .g-breadcrumbs-item span[itemprop="name"]');
            breadcrumbItems.forEach(function(item) {
                if (item.textContent.trim() === 'Non categorizzato' || item.textContent.trim() === 'Uncategorized') {
                    item.textContent = 'News';
                    var parentLink = item.closest('a[itemprop="item"]');
                    if (parentLink) {
                        parentLink.href = '/news/';
                    }
                }
            });
        });
    </script>
    <?php
}
add_action('wp_footer', 'change_breadcrumb_text');

// Post excerpt
function display_post_excerpt($atts)
{
    global $post;
    $excerpt = get_post_field('post_excerpt', $post->ID);
    if (!empty($excerpt)) {
        return '<div class="custom-excerpt">' . $excerpt . '</div>';
    }
    return '';
}
add_shortcode('post_excerpt', 'display_post_excerpt');

// Validation for the Tax Code
function add_codice_fiscale_validation_script()
{
    if (is_checkout()) {
    ?>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                $('form.checkout').on('submit', function(event) {
                    var codiceFiscale = $('#additional_wooccm0').val();

                    if (codiceFiscale.length !== 16) {
                        event.preventDefault();
                        alert('Il Codice Fiscale deve essere di esattamente 16 caratteri.');
                    }
                });
            });
        </script>
<?php
    }
}
add_action('wp_footer', 'add_codice_fiscale_validation_script');

// Validate the Tax Code on the server side
function validate_codice_fiscale($posted)
{
    $codiceFiscale = isset($_POST['additional_wooccm0']) ? $_POST['additional_wooccm0'] : '';

    if (strlen($codiceFiscale) !== 16) {
        wc_add_notice(__('Il Codice Fiscale deve essere di esattamente 16 caratteri.'), 'error');
    }
}
add_action('woocommerce_checkout_process', 'validate_codice_fiscale');

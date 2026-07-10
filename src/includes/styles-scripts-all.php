<?php
class AssetManager
{
    protected static array $css = [];
    protected static array $js = [];

    public static function css(string|array $assets): void
    {
        foreach ((array)$assets as $asset) {
            self::$css[$asset] = true;
        }
    }

    public static function js(string|array $assets): void
    {
        foreach ((array)$assets as $asset) {
            self::$js[$asset] = true;
        }
    }

    /**
     * Get file version based on modified time (safe, avoids PHP Warning)
     */
    public static function get_version(string $file_path, string $default_version = '1.0.0'): string|int
    {
        return file_exists($file_path) ? filemtime($file_path) : $default_version;
    }

    public static function enqueue(): void
    {
        $theme_dir = get_template_directory();

        foreach (array_keys(self::$css) as $asset) {
            $file_path = $theme_dir . "/assets/css/{$asset}.css";
            wp_enqueue_style(
                $asset,
                get_theme_file_uri("/assets/css/{$asset}.css"),
                [],
                self::get_version($file_path)
            );
        }

        foreach (array_keys(self::$js) as $asset) {
            $file_path = $theme_dir . "/assets/js/{$asset}.js";
            wp_enqueue_script(
                $asset,
                get_theme_file_uri("/assets/js/{$asset}.js"),
                [],
                self::get_version($file_path),
                ['in_footer' => true, 'strategy' => 'defer']
            );
        }
    }
}


add_action('wp_enqueue_scripts', function () {
    $themeUrl = get_template_directory_uri();
    $themeDir = get_template_directory();

    // Internal helper function for safe versioning
    $get_version = function ($path) use ($themeDir) {
        return AssetManager::get_version($themeDir . $path);
    };

    // 1. CSS Files
    wp_enqueue_style( 'scrollable-css', $themeUrl . '/assets/vendor/scrollable/scrollable.css', array(), '1.0.1' );
    wp_enqueue_style( 'mCustomScrollbar-css', $themeUrl . '/assets/vendor/mCustomScrollbar/jquery.mCustomScrollbar.css', array(), '1.0.1' );
    wp_enqueue_style( 'slick-css', $themeUrl . '/assets/vendor/slick/slick.css', array(), '1.8.1' );
    wp_enqueue_style( 'style-css', $themeUrl . '/assets/css/common.css', array(), $get_version('/assets/css/common.css') );


    // 2. JS Files - Use WordPress default jQuery to ensure maximum compatibility
    wp_enqueue_script( 'jquery' );

    wp_enqueue_script( 'scrollable-js', $themeUrl . '/assets/vendor/scrollable/scrollable.js', array( 'jquery' ), '1.0', array(
        'strategy'  => 'defer',
        'in_footer' => true,
    ));
    wp_enqueue_script( 'mCustomScrollbar-js', $themeUrl . '/assets/vendor/mCustomScrollbar/jquery.mCustomScrollbar.js', array( 'jquery' ), '1.0', array(
        'strategy'  => 'defer',
        'in_footer' => true,
    ));
    wp_enqueue_script( 'slick-js', $themeUrl . '/assets/vendor/slick/slick.min.js', array( 'jquery' ), '1.8.1', array(
        'strategy'  => 'defer',
        'in_footer' => true,
    ));
    
    wp_enqueue_script( 'inview-js', $themeUrl . '/assets/js/inview.js', array( 'jquery' ), '1.0', array(
        'strategy'  => 'defer',
        'in_footer' => true,
    ));
    wp_enqueue_script( 'cookie-js', $themeUrl . '/assets/js/cookie.js', array( 'jquery' ), $get_version('/assets/js/cookie.js'), array(
        'strategy'  => 'defer',
        'in_footer' => true,
    ));
    wp_enqueue_script( 'common-js', $themeUrl . '/assets/js/common.js', array( 'jquery' ), $get_version('/assets/js/common.js'), array(
        'strategy'  => 'defer',
        'in_footer' => true,
    ));

    // 3. Enqueue static assets registered via AssetManager class
    AssetManager::enqueue();
}, 999);
?>
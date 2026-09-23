<?php
/**
 * Template Name: Component Showcase
 * Template Post Type: page
 * Description: Dedicated FLOCSS Component Library & Styleguide for Developers
 */

// =============================================================================
// 1. BẢO VỆ PRODUCTION & CHỐNG ẢNH HƯỞNG SITE CHÍNH
// =============================================================================
// Chỉ cho phép truy cập ở môi trường Local/Development hoặc Admin đã đăng nhập
$is_local_env = in_array( $_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1', 'localhost'] )
    || ( defined( 'WP_DEBUG' ) && WP_DEBUG )
    || ( defined( 'WP_ENVIRONMENT_TYPE' ) && in_array( wp_get_environment_type(), ['local', 'development'] ) );

if ( ! $is_local_env && ! current_user_can( 'manage_options' ) ) {
    global $wp_query;
    $wp_query->set_404();
    status_header( 404 );
    nocache_headers();
    include get_query_template( '404' );
    exit;
}

// Chặn triệt để Googlebot và công cụ tìm kiếm index trang nội bộ này
add_action( 'wp_head', function() {
    echo '<meta name="robots" content="noindex, nofollow">' . "\n";
}, 1 );

// =============================================================================
// 2. TỰ ĐỘNG QUÉT & LẤY TRỰC TIẾP TÊN FILE TRONG includes/components/
// Người dùng thêm bất kỳ file .php nào vào thư mục này sẽ tự động xuất hiện trên sidebar & content!
// =============================================================================
function cs_get_dynamic_component_modules() {
    $components_dir = get_template_directory() . '/includes/components';
    if ( ! is_dir( $components_dir ) ) {
        $components_dir = __DIR__ . '/includes/components';
    }

    if ( ! is_dir( $components_dir ) ) {
        return [
            'components' => [],
            'layouts'    => [],
            'all'        => [],
        ];
    }

    $raw_files  = scandir( $components_dir );
    $components = [];
    $layouts    = [];

    foreach ( $raw_files as $file ) {
        if ( $file === '.' || $file === '..' ) continue;
        
        $file_path = $components_dir . '/' . $file;
        if ( ! is_file( $file_path ) ) continue;

        // Chỉ lấy các file .php (bỏ qua file ẩn hoặc file tạm)
        if ( strtolower( pathinfo( $file, PATHINFO_EXTENSION ) ) !== 'php' ) continue;
        if ( str_starts_with( $file, '.' ) || str_starts_with( $file, '~' ) ) continue;

        // Lấy trực tiếp tên file (slug và title)
        $slug  = pathinfo( $file, PATHINFO_FILENAME );
        $title = $slug; 

        // Đọc nội dung file để phân loại theo FLOCSS:
        // c-* (Components) hay l-* (Layout & Structure)
        $content = file_get_contents( $file_path );
        $type = 'component'; // Mặc định là Component (c-*)

        // Kiểm tra class layout l-* gốc (loại trừ l-container)
        $has_layout_class = (bool) preg_match( '/\bclass=["\'][^"\']*\bl-(?!container\b)[a-zA-Z0-9_-]+/i', $content );
        $has_component_class = (bool) preg_match( '/\bclass=["\'][^"\']*\bc-[a-zA-Z0-9_-]+/i', $content );

        // Các slug layout tiêu chuẩn
        $known_layout_slugs = ['flexs', 'grids', 'tbls', 'flex', 'grid', 'table', 'tables', 'layout', 'layouts'];

        if ( in_array( strtolower( $slug ), $known_layout_slugs, true ) || str_starts_with( strtolower( $slug ), 'l-' ) ) {
            $type = 'layout';
        } elseif ( $has_layout_class && ! $has_component_class ) {
            $type = 'layout';
        } elseif ( $has_layout_class && $has_component_class ) {
            // Nếu có cả 2, kiểm tra class FLOCSS chính xuất hiện đầu tiên
            if ( preg_match( '/\bclass=["\'][^"\']*\b([cl])-(?!container\b)[a-zA-Z0-9_-]+/i', $content, $m ) ) {
                $type = ( strtolower( $m[1] ) === 'l' ) ? 'layout' : 'component';
            }
        }

        $item = [
            'slug'  => $slug,
            'file'  => $file,
            'title' => $title,
            'type'  => $type,
        ];

        if ( $type === 'layout' ) {
            $layouts[] = $item;
        } else {
            $components[] = $item;
        }
    }

    // Sắp xếp tự nhiên theo tên file a-z trong từng nhóm
    usort( $components, function( $a, $b ) {
        return strnatcasecmp( $a['slug'], $b['slug'] );
    } );

    usort( $layouts, function( $a, $b ) {
        return strnatcasecmp( $a['slug'], $b['slug'] );
    } );

    // Xếp Components (c-*) TRÊN Layout & Structure (l-*)
    $ordered_all = array_merge( $components, $layouts );

    return [
        'components' => $components,
        'layouts'    => $layouts,
        'all'        => $ordered_all,
    ];
}

// Quét toàn bộ component trực tiếp từ thư mục includes/components/
$component_data  = cs_get_dynamic_component_modules();
$components_list = $component_data['components'];
$layouts_list    = $component_data['layouts'];
$ordered_all     = $component_data['all'];
$total_modules   = count( $ordered_all );

get_header();
?>

<script>
    // Đảm bảo body có class định danh cho trang showcase (không ảnh hưởng trang khác)
    if (document.body) {
        document.body.classList.add('is-component-showcase-page');
    }
</script>

<style>
/* ==========================================================================
   COMPONENT SHOWCASE - SCOPED CSS (ISOLATED TO NOT AFFECT THE MAIN SITE)
   ========================================================================== */

/* Ẩn Header, Footer, Banner Cookie của theme chính CHỈ RIÊNG trên trang này */
body:has(.cs-app) .c-header,
body:has(.cs-app) .c-footer,
body:has(.cs-app) .c-cookiewrap,
body:has(.cs-app) .c-totop,
body.is-component-showcase-page .c-header,
body.is-component-showcase-page .c-footer,
body.is-component-showcase-page .c-cookiewrap,
body.is-component-showcase-page .c-totop {
    display: none !important;
}

body:has(.cs-app),
body.is-component-showcase-page {
    margin: 0 !important;
    padding: 0 !important;
    background-color: #f8fafc !important;
}

body:has(.cs-app) .l-wrapper,
body.is-component-showcase-page .l-wrapper {
    overflow: visible !important;
    max-width: 100% !important;
    margin: 0 !important;
    padding: 0 !important;
}

html:has(.cs-app),
html.is-component-showcase-page {
    scroll-behavior: auto !important; /* Tránh xung đột với smooth scroll JS */
}

/* Base Container */
.cs-app {
    display: flex;
    width: 100%;
    min-height: 100vh;
    background: #f8fafc;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    color: var(--base, #3E3E3E);
    position: relative;
    box-sizing: border-box;
}

.cs-app *, .cs-app *::before, .cs-app *::after {
    box-sizing: border-box;
}

/* ==========================================
   FIXED SIDEBAR
   ========================================== */
.cs-sidebar {
    width: 290px;
    flex-shrink: 0;
    position: sticky;
    top: 0;
    height: 100vh;
    background: #ffffff;
    border-right: 1px solid #e2e8f0;
    box-shadow: 2px 0 8px rgba(0, 0, 0, 0.03);
    display: flex;
    flex-direction: column;
    z-index: 100;
}

.cs-sidebar__header {
    padding: 20px 18px 16px;
    border-bottom: 1px solid #f1f5f9;
}

.cs-brand {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 12px;
}

.cs-brand__icon {
    width: 32px;
    height: 32px;
    background: var(--primary, #e60012);
    color: #fff;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 900;
    font-size: 16px;
    box-shadow: 0 4px 8px rgba(230, 0, 18, 0.25);
    flex-shrink: 0;
}

.cs-brand__text {
    font-size: 14px;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: 0.5px;
    line-height: 1.2;
}

.cs-brand__sub {
    font-size: 11px;
    color: #64748b;
    font-weight: 500;
}

/* Search Box */
.cs-search {
    position: relative;
}

.cs-search__input {
    width: 100%;
    padding: 8px 12px 8px 34px;
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    font-size: 13px;
    color: #1e293b;
    outline: none;
    transition: all 0.2s ease;
}

.cs-search__input:focus {
    background: #fff;
    border-color: var(--primary, #e60012);
    box-shadow: 0 0 0 3px rgba(230, 0, 18, 0.15);
}

.cs-search__icon {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 13px;
    color: #94a3b8;
    pointer-events: none;
}

/* Nav Menu */
.cs-nav {
    flex: 1;
    overflow-y: auto;
    padding: 14px 12px;
}

.cs-nav::-webkit-scrollbar {
    width: 5px;
}
.cs-nav::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.cs-nav__group-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 10px 4px;
    margin: 6px 0 3px;
}

.cs-nav__group-title {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: #94a3b8;
}

.cs-nav__group-count {
    font-size: 10px;
    font-weight: 700;
    font-family: Consolas, monospace;
    background: #f1f5f9;
    color: #64748b;
    padding: 1px 6px;
    border-radius: 10px;
}

.cs-nav__list {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    gap: 3px;
}

.cs-nav__link {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 12px;
    border-radius: 6px;
    color: #475569;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.15s ease;
}

.cs-nav__link:hover {
    background: #f8fafc;
    color: var(--primary, #e60012);
    transform: translateX(3px);
}

.cs-nav__link.is-active {
    background: rgba(230, 0, 18, 0.08);
    color: var(--primary, #e60012);
    font-weight: 700;
    border-left: 3px solid var(--primary, #e60012);
}

.cs-nav__badge {
    font-size: 10px;
    font-family: Consolas, Monaco, monospace;
    background: #f1f5f9;
    color: #64748b;
    padding: 2px 6px;
    border-radius: 4px;
}

.cs-sidebar__footer {
    padding: 12px 16px;
    border-top: 1px solid #f1f5f9;
    background: #f8fafc;
    font-size: 11px;
    color: #64748b;
    line-height: 1.5;
}

.cs-sidebar__footer code {
    background: #e2e8f0;
    padding: 1px 4px;
    border-radius: 3px;
    font-family: Consolas, monospace;
    color: #334155;
}

.cs-sidebar__footer strong {
    color: var(--primary, #e60012);
}

/* ==========================================
   MAIN WORKSPACE
   ========================================== */
.cs-main {
    flex: 1;
    min-width: 0;
    padding: 30px 40px 100px;
}

.cs-topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-bottom: 20px;
    margin-bottom: 30px;
    border-bottom: 1px solid #e2e8f0;
    gap: 20px;
    flex-wrap: wrap;
}

.cs-topbar__title {
    font-size: 22px;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 4px;
}

.cs-topbar__desc {
    font-size: 13px;
    color: #64748b;
    margin: 0;
}

.cs-topbar__desc code {
    background: #e2e8f0;
    padding: 2px 5px;
    border-radius: 4px;
    font-family: Consolas, monospace;
}


/* Section Header */
.cs-section {
    scroll-margin-top: 24px;
    margin-bottom: 45px;
}

.cs-section__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-bottom: 10px;
    margin-bottom: 16px;
    border-bottom: 2px solid #e2e8f0;
}

.cs-section__title-wrap {
    display: flex;
    align-items: center;
    gap: 12px;
}

.cs-section__title {
    font-size: 17px;
    font-weight: 800;
    color: #0f172a;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
    border-left: 4px solid var(--primary, #e60012);
    padding-left: 10px;
}

.cs-badge-cat {
    font-size: 11px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 6px;
    letter-spacing: 0.3px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.cs-badge-cat--component {
    background: #eef2ff;
    color: #4f46e5;
    border: 1px solid #c7d2fe;
}

.cs-badge-cat--layout {
    background: #ecfdf5;
    color: #059669;
    border: 1px solid #a7f3d0;
}

.cs-section__file {
    font-size: 12px;
    font-family: Consolas, Monaco, monospace;
    background: #fff;
    color: #475569;
    padding: 4px 10px;
    border-radius: 4px;
    border: 1px solid #cbd5e1;
    display: flex;
    align-items: center;
    gap: 6px;
}

/* Component Grid & Card Groups */
.cs-grid-cards {
    display: flex;
    flex-direction: column;
    gap: 24px;
    width: 100%;
}

.cs-cards-group {
    width: 100%;
}

.cs-cards-group--full {
    display: flex;
    flex-direction: column;
    gap: 18px;
    width: 100%;
}

/* Khối p-component__item--inline-block: Item hiển thị DẠNG 2 CỘT */
.cs-cards-group--inline-block {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 18px;
    width: 100%;
    align-items: stretch;
}

/* ==========================================
   COMPONENT CARD
   ========================================== */
.cs-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    transition: all 0.2s ease;
    min-width: 0;
    box-sizing: border-box;
    overflow: hidden;
}

.cs-card:hover {
    border-color: #cbd5e1;
    box-shadow: 0 6px 14px -3px rgba(0, 0, 0, 0.06);
}

/* Khối p-component__item: Item hiển thị dạng FULL WIDTH (như Hình 1) */
.cs-card--full {
    width: 100%;
    flex: 0 0 100%;
    display: flex;
    flex-direction: column;
}

.cs-card--full .cs-card__preview {
    display: block;
    width: 100%;
}

/* Khối p-component__item--inline-block: Card hiển thị dạng 2 CỘT */
.cs-card--inline-block {
    width: 100%;
    min-width: 0;
    display: flex;
    flex-direction: column;
}

.cs-card--inline-block .cs-card__preview {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 80px;
    width: 100%;
}

/* Card Header: Tiêu đề bên trái, nút thao tác bên phải */
.cs-card__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 14px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    gap: 12px;
    width: 100%;
    box-sizing: border-box;
}

.cs-card__title {
    font-size: 13px;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.4;
    word-break: break-word;
    margin: 0;
    flex: 1;
    min-width: 0;
    text-align: left;
}

.cs-card__actions {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-shrink: 0;
}

/* Card Footer: Class nằm giữa footer */
.cs-card__footer {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 9px 14px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    width: 100%;
    box-sizing: border-box;
}

.cs-card__classes {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    align-items: center;
    gap: 6px;
    width: 100%;
}

/* Interactive Class Badges (Click to copy) */
.cs-card__class-tag {
    font-size: 11px;
    font-family: Consolas, Monaco, monospace;
    background: #fee2e2;
    color: var(--primary, #e60012);
    padding: 2px 7px;
    border-radius: 4px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s ease;
    display: inline-block;
    user-select: none;
}

.cs-card__class-tag:hover {
    background: #fecaca;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(230, 0, 18, 0.15);
}

/* Action Buttons */
.cs-btn-action {
    padding: 5px 10px;
    font-size: 11px;
    font-weight: 600;
    border-radius: 5px;
    cursor: pointer;
    border: 1px solid transparent;
    transition: all 0.15s ease;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    line-height: 1;
    white-space: nowrap;
}

.cs-btn-action--copy {
    background: var(--primary, #e60012);
    color: #fff;
}
.cs-btn-action--copy:hover {
    background: #c5000f;
}

.cs-btn-action--secondary {
    background: #fff;
    color: #475569;
    border-color: #cbd5e1;
}
.cs-btn-action--secondary:hover {
    background: #f1f5f9;
    color: #0f172a;
    border-color: #94a3b8;
}

/* Preview Area */
.cs-card__preview {
    padding: 24px 20px;
    background: #ffffff;
    min-height: 80px;
    transition: max-width 0.25s ease, background 0.2s ease;
    box-sizing: border-box;
    flex: 1;
}

.cs-card__preview.is-framed {
    border: 1px dashed #cbd5e1;
    border-radius: 6px;
    margin-top: 10px;
    margin-bottom: 10px;
}


.cs-card__preview.cs-preview--grid {
    background-color: #ffffff !important;
    background-image: 
        linear-gradient(45deg, #f1f5f9 25%, transparent 25%), 
        linear-gradient(-45deg, #f1f5f9 25%, transparent 25%), 
        linear-gradient(45deg, transparent 75%, #f1f5f9 75%), 
        linear-gradient(-45deg, transparent 75%, #f1f5f9 75%) !important;
    background-size: 16px 16px !important;
    background-position: 0 0, 0 8px, 8px -8px, -8px 0px !important;
}

.cs-card__preview .c-mv {
    margin-top: 0;
}

.p-component__item {
    display: block;
    width: 100%;
}
.p-component__item--inline-block {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 15px;
}

/* Code Accordion */
.cs-card__code {
    display: none;
    background: #0f172a;
    padding: 14px 16px;
    border-top: 1px solid #1e293b;
}

.cs-card__code.is-open {
    display: block;
}

.cs-card__code pre {
    margin: 0;
    color: #e2e8f0;
    font-family: Consolas, Monaco, "Courier New", monospace;
    font-size: 12px;
    line-height: 1.6;
    overflow-x: auto;
    white-space: pre-wrap;
    word-break: break-all;
}

/* Floating Toast */
.cs-toast {
    position: fixed;
    bottom: 24px;
    right: 24px;
    background: #0f172a;
    color: #fff;
    padding: 12px 18px;
    border-radius: 8px;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    font-weight: 600;
    z-index: 99999;
    transform: translateY(100px);
    opacity: 0;
    pointer-events: none;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    border-left: 4px solid var(--primary, #e60012);
}

.cs-toast.is-show {
    transform: translateY(0);
    opacity: 1;
}

/* Responsive Styles */
@media (max-width: 991px) {
    .cs-app {
        flex-direction: column;
    }
    .cs-sidebar {
        width: 100%;
        height: auto;
        position: static;
    }
    .cs-topbar {
        flex-direction: column;
        align-items: flex-start;
    }
    .cs-main {
        padding: 20px 16px;
    }
}

@media (max-width: 767px) {
    .cs-cards-group--inline-block {
        grid-template-columns: 1fr;
    }
    .p-component__item--inline-block {
        grid-template-columns: 1fr;
    }
    .cs-card__header {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }
    .cs-card__actions {
        width: 100%;
        justify-content: flex-start;
    }
}
</style>

<div class="cs-app">
    <!-- =======================================================
         FIXED SIDEBAR (TỰ ĐỘNG LẤY TÊN FILE TRỰC TIẾP)
         ======================================================= -->
    <aside class="cs-sidebar">
        <div class="cs-sidebar__header">
            <div class="cs-brand">
                <div class="cs-brand__icon">J</div>
                <div>
                    <div class="cs-brand__text">JLINE COMPONENT</div>
                    <div class="cs-brand__sub">Design System & Code Library</div>
                </div>
            </div>
            <div class="cs-search">
                <span class="cs-search__icon">🔍</span>
                <input type="text" id="cs-filter-input" class="cs-search__input" placeholder="Search class or file..." autocomplete="off">
            </div>
        </div>

        <nav class="cs-nav">
            <?php if ( empty( $ordered_all ) ) : ?>
                <div style="padding: 16px 12px; color: #94a3b8; font-size: 13px; line-height: 1.5;">
                    ⚠️ Chưa tìm thấy file component nào trong <code>includes/components/</code>. Hãy thêm file <code>.php</code> để bắt đầu.
                </div>
            <?php else : ?>
                <?php if ( ! empty( $components_list ) ) : ?>
                    <div class="cs-nav__group-header">
                        <span class="cs-nav__group-title">Components (c-*)</span>
                        <span class="cs-nav__group-count"><?php echo count( $components_list ); ?></span>
                    </div>
                    <ul class="cs-nav__list">
                        <?php foreach ( $components_list as $mod ) : ?>
                            <li>
                                <a href="#sec-<?php echo esc_attr( $mod['slug'] ); ?>" class="cs-nav__link">
                                    <span>🧩 <?php echo esc_html( $mod['title'] ); ?></span>
                                    <span class="cs-nav__badge"><?php echo esc_html( $mod['file'] ); ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <?php if ( ! empty( $layouts_list ) ) : ?>
                    <div class="cs-nav__group-header" style="margin-top: 16px;">
                        <span class="cs-nav__group-title">Layout & Structure (l-*)</span>
                        <span class="cs-nav__group-count"><?php echo count( $layouts_list ); ?></span>
                    </div>
                    <ul class="cs-nav__list">
                        <?php foreach ( $layouts_list as $mod ) : ?>
                            <li>
                                <a href="#sec-<?php echo esc_attr( $mod['slug'] ); ?>" class="cs-nav__link">
                                    <span>📐 <?php echo esc_html( $mod['title'] ); ?></span>
                                    <span class="cs-nav__badge"><?php echo esc_html( $mod['file'] ); ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            <?php endif; ?>
        </nav>

        <div class="cs-sidebar__footer">
            💡 <strong>Tự động nhận diện:</strong> Chỉ cần thêm file mới vào <code>src/includes/components/*.php</code>, hệ thống sẽ tự sinh Sidebar & Cards!
        </div>
    </aside>

    <!-- =======================================================
         MAIN WORKSPACE: TỰ ĐỘNG TẠO SECTION TỪ FILE PHP
         ======================================================= -->
    <main class="cs-main">
        <header class="cs-topbar">
            <div>
                <h1 class="cs-topbar__title">FLOCSS Component Library</h1>
                <p class="cs-topbar__desc">Tất cả component được quét tự động từ <code>src/includes/components/</code>. Click vào class tag hoặc nút Copy để lấy mã nguồn.</p>
            </div>
        </header>

        <!-- DYNAMIC SECTIONS: TỰ ĐỘNG SINH SECTION THEO TỪNG FILE QUÉT ĐƯỢC -->
        <?php if ( empty( $ordered_all ) ) : ?>
            <div style="background: #fff; border: 1px dashed #cbd5e1; padding: 40px; border-radius: 8px; text-align: center; color: #64748b;">
                <h3>Chưa có component nào</h3>
                <p>Thêm các file <code>.php</code> vào thư mục <code>src/includes/components/</code>, trang sẽ tự động nhận diện và hiển thị tại đây!</p>
            </div>
        <?php else : ?>
            <?php foreach ( $ordered_all as $mod ) : ?>
                <section id="sec-<?php echo esc_attr( $mod['slug'] ); ?>" class="cs-section" data-title="<?php echo esc_attr( $mod['title'] ); ?>" data-file="<?php echo esc_attr( $mod['file'] ); ?>" data-type="<?php echo esc_attr( $mod['type'] ); ?>">
                    <div class="cs-raw-content">
                        <?php get_template_part( 'includes/components/' . $mod['slug'] ); ?>
                    </div>
                </section>
            <?php endforeach; ?>
        <?php endif; ?>

    </main>
</div>

<!-- Floating Toast -->
<div id="cs-toast" class="cs-toast">
    <span>✅</span>
    <span id="cs-toast-msg">Copied to clipboard!</span>
</div>

<script>
/* ==========================================================================
   UNIVERSAL COMPONENT EXTRACTOR & SHOWCASE ENGINE (PRO)
   - Zero hardcoding: tự động bóc tách mọi component mang class c-* hoặc l-*
   - Interactive class tags: click bất kỳ class token nào để copy ngay lập tức
   - Viewport & background switcher cho kiểm tra responsive thời gian thực
   ========================================================================== */
(function() {
    let toastTimeout = null;

    window.showToast = function(msg) {
        const toast = document.getElementById('cs-toast');
        const toastMsg = document.getElementById('cs-toast-msg');
        if (!toast || !toastMsg) return;
        toastMsg.textContent = msg;
        toast.classList.add('is-show');
        if (toastTimeout) clearTimeout(toastTimeout);
        toastTimeout = setTimeout(() => {
            toast.classList.remove('is-show');
        }, 2000);
    };

    window.copyRaw = function(text) {
        if (!text) return;
        const copyText = text.startsWith('.') ? text : '.' + text;
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(copyText).then(() => {
                showToast(`Copied class: "${copyText}"`);
            });
        } else {
            const ta = document.createElement('textarea');
            ta.value = copyText;
            document.body.appendChild(ta);
            ta.select();
            document.execCommand('copy');
            document.body.removeChild(ta);
            showToast(`Copied class: "${copyText}"`);
        }
    };

    window.copySnippetFromCard = function(btn) {
        const card = btn.closest('.cs-card');
        if (!card) return;
        const codeBlock = card.querySelector('.cs-card__code pre code');
        let htmlToCopy = '';
        if (codeBlock) {
            htmlToCopy = codeBlock.textContent.trim();
        } else {
            const preview = card.querySelector('.cs-card__preview');
            if (preview) {
                const container = preview.querySelector('.l-container');
                const target = (container && container.firstElementChild) ? container.firstElementChild : preview.firstElementChild || preview;
                htmlToCopy = target.outerHTML || target.innerHTML.trim();
            }
        }

        if (htmlToCopy) {
            htmlToCopy = normalizeIndentation(htmlToCopy);
            htmlToCopy = htmlToCopy.replace(/\bhref=["']#["']/gi, 'href=""');
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(htmlToCopy).then(() => {
                    showToast('Copied HTML snippet to clipboard!');
                });
            } else {
                const ta = document.createElement('textarea');
                ta.value = htmlToCopy;
                document.body.appendChild(ta);
                ta.select();
                document.execCommand('copy');
                document.body.removeChild(ta);
                showToast('Copied HTML snippet to clipboard!');
            }

            const originalText = btn.innerHTML;
            btn.innerHTML = '✅ Copied!';
            btn.style.background = '#10b981';
            btn.style.borderColor = '#10b981';
            btn.style.color = '#fff';
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.style.background = '';
                btn.style.borderColor = '';
                btn.style.color = '';
            }, 1200);
        }
    };

    window.toggleCodeAccordion = function(btn) {
        const card = btn.closest('.cs-card');
        if (!card) return;
        const code = card.querySelector('.cs-card__code');
        if (!code) return;
        code.classList.toggle('is-open');
        btn.textContent = code.classList.contains('is-open') ? 'Hide Code' : 'Code';
    };

    // Định dạng tiêu đề component từ các class FLOCSS
    function formatComponentTitle(fullClass) {
        if (!fullClass) return 'Component';
        const tokens = fullClass.split(/\s+/).filter(c => /^[cl]-/.test(c) && c !== 'l-container');
        if (tokens.length === 0) return 'Component';

        const primary = tokens[tokens.length - 1] || tokens[0];

        if (primary.includes('c-title')) return 'Bilingual Title (EN / JP)';
        if (primary.includes('c-mv')) return 'Main Visual Banner';
        if (primary.includes('c-loading')) return 'Loading Animation';
        if (primary.includes('c-bread')) return 'Breadcrumb Navigation';

        let formatted = primary
            .replace(/^c-ttl(\d+)/, 'Heading $1px ')
            .replace(/^c-txt(\d+)/, 'Text $1px ')
            .replace(/^c-btn/, 'Button ')
            .replace(/^c-link/, 'Link ')
            .replace(/^c-list-([a-z]+)/, 'List ($1) ')
            .replace(/^l-grid--cl(\d+)/, 'Grid $1 Columns ')
            .replace(/^l-grid/, 'Grid 2 Columns ')
            .replace(/^l-flex/, 'Flex ')
            .replace(/^l-tbl/, 'Table ')
            .replace(/--arrow-between/, 'Arrow Between ')
            .replace(/--arrow-center/, 'Arrow Center ')
            .replace(/--arrow/, 'Arrow ')
            .replace(/--reverse/, '(Reverse) ')
            .replace(/--line/, 'Line ')
            .replace(/--dot/, 'Dot ')
            .replace(/--blank/, 'External Blank ')
            .replace(/--middle/, 'Middle ')
            .replace(/--center/, 'Center ')
            .replace(/--item/, 'Item ')
            .replace(/^[cl]-/, '')
            .replace(/[-_]+/g, ' ')
            .trim();

        formatted = formatted.split(' ').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ');
        return formatted || primary;
    }

    // Chuẩn hóa lùi đầu dòng (de-indent) sao cho thẻ ngoài cùng ở cột 0 và các thẻ con đều thụt lề chuẩn
    function normalizeIndentation(html) {
        if (!html) return '';
        const trimmed = html.trim();
        const lines = trimmed.split(/\r?\n/);
        if (lines.length <= 1) return trimmed;

        const lastLine = lines[lines.length - 1];
        const lastMatch = lastLine.match(/^(\s+)</);
        let baseIndent = '';

        if (lastMatch) {
            baseIndent = lastMatch[1];
        } else {
            let minLen = Infinity;
            for (let i = 1; i < lines.length; i++) {
                const line = lines[i];
                if (line.trim().length > 0) {
                    const m = line.match(/^(\s*)/);
                    const len = m ? m[1].length : 0;
                    if (len < minLen) {
                        minLen = len;
                        baseIndent = m[1];
                    }
                }
            }
        }

        if (baseIndent.length > 0) {
            return lines.map((line, idx) => {
                if (idx === 0) return line.trimStart();
                if (line.startsWith(baseIndent)) {
                    return line.slice(baseIndent.length);
                }
                return line.trimStart();
            }).join('\n');
        }

        return trimmed;
    }

    // Xây dựng Card động với từng class badge có thể click để copy
    function buildCard(el, title, fullClass, isInline = false) {
        let snippetEl = el;
        let effectiveClass = fullClass;

        if (el.classList && el.classList.contains('l-container') && el.firstElementChild) {
            snippetEl = el.firstElementChild;
            effectiveClass = snippetEl.className || fullClass;
        }

        effectiveClass = (effectiveClass || '').trim().replace(/\s+/g, ' ');

        // Chuẩn hóa href="#" thành href="" trên preview DOM
        if (snippetEl.tagName === 'A' && snippetEl.getAttribute('href') === '#') {
            snippetEl.setAttribute('href', '');
        }
        snippetEl.querySelectorAll('a[href="#"]').forEach(a => a.setAttribute('href', ''));

        const card = document.createElement('div');
        // Gán class phân biệt khối full width (Hình 1) và inline-block (Hình 2)
        card.className = `cs-card ${isInline ? 'cs-card--inline-block' : 'cs-card--full'}`;
        card.setAttribute('data-search', `${title} ${effectiveClass}`.toLowerCase());

        // Chuẩn hóa: thay thế mọi href="#" thành href="" và format thụt lề chuẩn
        let cleanHtml = snippetEl.outerHTML;
        cleanHtml = cleanHtml.replace(/\bhref=["']#["']/gi, 'href=""');
        cleanHtml = normalizeIndentation(cleanHtml);

        // Escape HTML cho code block
        const escaped = cleanHtml
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');

        // Tách các token class thành các badge tương tác riêng biệt
        const classTokens = effectiveClass.split(/\s+/).filter(Boolean);
        const badgesHtml = classTokens.map(c => {
            const cleanC = c.replace(/^\./, '');
            return `<span class="cs-card__class-tag" title="Click to copy .${cleanC}" onclick="event.stopPropagation(); copyRaw('${cleanC}')">.${cleanC}</span>`;
        }).join(' ');

        card.innerHTML = `
            <div class="cs-card__header">
                <div class="cs-card__title">${title}</div>
                <div class="cs-card__actions">
                    <button type="button" class="cs-btn-action cs-btn-action--copy" onclick="copySnippetFromCard(this)">📋 Copy HTML</button>
                    <button type="button" class="cs-btn-action cs-btn-action--secondary" onclick="copyRaw('${effectiveClass}')">Class</button>
                    <button type="button" class="cs-btn-action cs-btn-action--secondary" onclick="toggleCodeAccordion(this)">Code</button>
                </div>
            </div>
            <div class="cs-card__preview"></div>
            ${badgesHtml ? `
            <div class="cs-card__footer">
                <div class="cs-card__classes">${badgesHtml}</div>
            </div>
            ` : ''}
            <div class="cs-card__code">
                <pre><code>${escaped}</code></pre>
            </div>
        `;

        card.querySelector('.cs-card__preview').appendChild(el);
        return card;
    }

    // Universal Component Extractor: bóc tách đệ quy mọi phần tử c-* hoặc l-*
    function extractComponentItems(container, inheritedInline = false) {
        const items = [];
        const seenElements = new Set();

        // Trích xuất comment chú thích ngay phía trước phần tử (nếu có)
        function getPrecedingComment(node) {
            if (!node) return null;
            let prev = node.previousSibling;
            while (prev) {
                if (prev.nodeType === 8) { // Comment node <!-- ... -->
                    let text = (prev.nodeValue || '').trim();
                    text = text.replace(/^[\s*#-]+/, '').replace(/[\s*#-]+$/, '').trim();
                    if (text && !text.startsWith('[') && !text.startsWith('vite') && !text.startsWith('webpack')) {
                        return text;
                    }
                } else if (prev.nodeType === 1) { // Gặp thẻ element khác thì dừng
                    break;
                }
                prev = prev.previousSibling;
            }
            return null;
        }

        function processNode(node, currentWrapper = null) {
            if (!node || node.nodeType !== 1) return;

            const classStr = typeof node.className === 'string' ? node.className : '';
            let wrapper = currentWrapper;

            // Kiểm tra nếu node là component wrapper (p-component__item, etc.)
            if (classStr.includes('p-component__')) {
                wrapper = node;
                Array.from(node.children).forEach(child => processNode(child, wrapper));
                return;
            }

            // Multi-child l-container wrapper
            if (node.classList.contains('l-container') && node.children.length > 1) {
                Array.from(node.children).forEach(child => processNode(child, wrapper));
                return;
            }

            // Nếu phần tử được bọc trong l-container đơn lẻ, lấy phần tử component bên trong
            let snippetTarget = node;
            if (node.classList && node.classList.contains('l-container') && node.children.length === 1) {
                snippetTarget = node.firstElementChild;
            }

            const classList = Array.from(snippetTarget.classList || []);
            const flocssClass = classList.find(c => /^[cl]-[a-zA-Z0-9_-]+/.test(c) && c !== 'l-container' && !c.startsWith('p-component'));

            if (flocssClass) {
                if (!seenElements.has(snippetTarget)) {
                    seenElements.add(snippetTarget);

                    // Trích xuất chú thích từ comment code (nếu không có sẽ fallback về null)
                    let commentTitle = getPrecedingComment(snippetTarget) || getPrecedingComment(node);
                    if (!commentTitle && node.parentElement && node.parentElement !== container) {
                        commentTitle = getPrecedingComment(node.parentElement);
                    }
                    if (!commentTitle) {
                        commentTitle = getPrecedingComment(container);
                    }

                    const isInline = wrapper ? (
                        wrapper.classList.contains('p-component__item--inline-block') || 
                        (typeof wrapper.className === 'string' && wrapper.className.includes('inline-block'))
                    ) : false;

                    items.push({
                        domElement: node,
                        snippetElement: snippetTarget,
                        tagClass: flocssClass,
                        fullClass: snippetTarget.className || '',
                        commentTitle: commentTitle,
                        isInline: isInline,
                        wrapperNode: wrapper
                    });
                }
                return;
            }

            // Nếu node này không khớp trực tiếp, kiểm tra các con của nó
            Array.from(node.children).forEach(child => processNode(child, wrapper));
        }

        Array.from(container.children).forEach(child => processNode(child, null));

        // Fallback: nếu không tìm thấy khớp nào, lấy phần tử con đầu tiên
        if (items.length === 0 && container.firstElementChild) {
            const el = container.firstElementChild;
            const inner = el.querySelector('[class*="c-"], [class*="l-"]') || el;
            const cls = Array.from(inner.classList || []).find(c => /^[cl]-/.test(c)) || 'component';
            let commentTitle = getPrecedingComment(inner) || getPrecedingComment(el);
            if (!commentTitle && el.parentElement && el.parentElement !== container) {
                commentTitle = getPrecedingComment(el.parentElement);
            }
            if (!commentTitle) {
                commentTitle = getPrecedingComment(container);
            }
            const isInline = el.classList.contains('p-component__item--inline-block') || 
                (typeof el.className === 'string' && el.className.includes('inline-block'));
            items.push({
                domElement: el,
                snippetElement: inner,
                tagClass: cls,
                fullClass: inner.className || '',
                commentTitle: commentTitle,
                isInline: isInline,
                wrapperNode: el
            });
        }

        return items;
    }

    // Biến đổi nội dung thô thành các Cards hiển thị chuyên nghiệp
    function transformSections() {
        const sections = document.querySelectorAll('.cs-section');
        let totalItems = 0;

        sections.forEach(sec => {
            const title = sec.getAttribute('data-title') || '';
            const file = sec.getAttribute('data-file') || '';
            const type = sec.getAttribute('data-type') || 'component';
            const raw = sec.querySelector('.cs-raw-content');
            if (!raw) return;

            const typeBadge = type === 'layout' 
                ? '<span class="cs-badge-cat cs-badge-cat--layout">📐 Layout (l-*)</span>' 
                : '<span class="cs-badge-cat cs-badge-cat--component">🧩 Component (c-*)</span>';

            // Header Section
            const header = document.createElement('div');
            header.className = 'cs-section__header';
            header.innerHTML = `
                <div class="cs-section__title-wrap">
                    <h2 class="cs-section__title">${title}</h2>
                    ${typeBadge}
                </div>
                <span class="cs-section__file">📁 ${file}</span>
            `;

            const cardsContainer = document.createElement('div');
            cardsContainer.className = 'cs-grid-cards';

            const items = extractComponentItems(raw);

            let currentGroup = null;
            let currentWrapper = null;
            let currentIsInline = null;

            items.forEach((item) => {
                const isInline = !!item.isInline;
                const wrapper = item.wrapperNode;

                const shouldNewGroup = !currentGroup || 
                    (wrapper ? wrapper !== currentWrapper : (currentWrapper !== null || isInline !== currentIsInline));

                if (shouldNewGroup) {
                    currentGroup = document.createElement('div');
                    currentGroup.className = `cs-cards-group ${isInline ? 'cs-cards-group--inline-block' : 'cs-cards-group--full'}`;
                    cardsContainer.appendChild(currentGroup);
                    currentWrapper = wrapper;
                    currentIsInline = isInline;
                }

                const fullCls = (item.fullClass || item.tagClass || '').trim();
                // Ưu tiên lấy chú thích từ comment code; nếu không có thì lấy tên đặt của component đó để hiển thị
                const name = item.commentTitle || formatComponentTitle(fullCls);
                const card = buildCard(item.domElement, name, fullCls, isInline);
                currentGroup.appendChild(card);
                totalItems++;
            });

            if (items.length === 0) {
                const emptyNotice = document.createElement('div');
                emptyNotice.className = 'cs-card cs-card--full';
                emptyNotice.innerHTML = '<div class="cs-card__preview" style="color: #94a3b8; font-style: italic; padding: 20px; text-align: center;">Chưa có component nào trong module này. Hãy thêm class FLOCSS (c-* hoặc l-*) để hiển thị.</div>';
                cardsContainer.appendChild(emptyNotice);
            }

            // Loại bỏ container thô và thêm header + content
            raw.remove();
            sec.appendChild(header);
            sec.appendChild(cardsContainer);
        });

    }

    // Thực thi bóc tách và render Card
    transformSections();

    // Live Search
    const searchInput = document.getElementById('cs-filter-input');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const val = e.target.value.toLowerCase().trim();
            const cards = document.querySelectorAll('.cs-card');
            const sections = document.querySelectorAll('.cs-section');

            cards.forEach(card => {
                const text = card.textContent.toLowerCase();
                const searchMeta = (card.getAttribute('data-search') || '').toLowerCase();
                if (text.includes(val) || searchMeta.includes(val)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });

            // Ẩn nhóm cards trống khi filter
            document.querySelectorAll('.cs-cards-group').forEach(group => {
                const visibleInGroup = group.querySelectorAll('.cs-card:not([style*="display: none"])');
                group.style.display = (visibleInGroup.length === 0 && val !== '') ? 'none' : '';
            });

            sections.forEach(sec => {
                const visible = sec.querySelectorAll('.cs-card:not([style*="display: none"])');
                sec.style.display = (visible.length === 0 && val !== '') ? 'none' : '';
            });

            // Đồng bộ ẩn/hiện mục trên sidebar tương ứng khi tìm kiếm
            navLinks.forEach(link => {
                const text = link.textContent.toLowerCase();
                const hash = link.getAttribute('href');
                const targetSec = hash ? document.querySelector(hash) : null;
                const isSecVisible = targetSec && targetSec.style.display !== 'none';
                if (val === '' || text.includes(val) || isSecVisible) {
                    link.parentElement.style.display = '';
                } else {
                    link.parentElement.style.display = 'none';
                }
            });
        });
    }


    // =========================================================================
    // SMOOTH ANCHOR SCROLL & SCROLLSPY (KHÔNG BỊ KHỰNG / GIẬT LAG)
    // =========================================================================
    const navLinks = document.querySelectorAll('.cs-nav__link');
    const sections = document.querySelectorAll('.cs-section');
    let isClickScrolling = false;
    let scrollTimeout = null;

    // Chặn triệt để jQuery của theme (common.js) ở Capture Phase để cuộn mượt 60fps
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const hash = this.getAttribute('href');
            if (!hash || !hash.startsWith('#')) return;

            const target = document.querySelector(hash);
            if (!target) return;

            e.preventDefault();
            e.stopPropagation();
            if (e.stopImmediatePropagation) e.stopImmediatePropagation();

            // Active ngay lập tức trên sidebar
            navLinks.forEach(l => l.classList.remove('is-active'));
            this.classList.add('is-active');

            // Khóa ScrollSpy tạm thời trong lúc đang cuộn để tránh giật class
            isClickScrolling = true;
            if (scrollTimeout) clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(() => {
                isClickScrolling = false;
            }, 750);

            // Cuộn mượt bằng native compositor
            const targetTop = target.getBoundingClientRect().top + window.pageYOffset - 20;
            window.scrollTo({
                top: Math.max(0, targetTop),
                behavior: 'smooth'
            });

            // Cập nhật hash trên URL mượt mà
            if (history.pushState) {
                history.pushState(null, null, hash);
            }
        }, true); // useCapture = true: chạy trước jQuery common.js
    });

    // ScrollSpy tối ưu hiệu năng bằng requestAnimationFrame (không gây Forced Reflow)
    let ticking = false;
    window.addEventListener('scroll', () => {
        if (isClickScrolling) return;

        if (!ticking) {
            window.requestAnimationFrame(() => {
                let current = '';
                const scrollPos = window.scrollY + 100;

                sections.forEach(section => {
                    const top = section.offsetTop;
                    const height = section.offsetHeight;
                    if (scrollPos >= top && scrollPos < top + height) {
                        current = '#' + section.getAttribute('id');
                    }
                });

                if (current) {
                    navLinks.forEach(link => {
                        link.classList.toggle('is-active', link.getAttribute('href') === current);
                    });
                }
                ticking = false;
            });
            ticking = true;
        }
    }, { passive: true });
})();
</script>

<?php
get_footer();
?>
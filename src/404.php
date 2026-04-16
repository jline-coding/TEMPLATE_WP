<?php
get_header();
?>
<!-- ↓↓ main ↓↓ -->
<main class="p-404">
    <section class="c-mv c-mv01">
        <ul class="c-bread">
            <li class="c-bread__item"><a href="<?php echo esc_url(home_url('/')); ?>" class="c-bread__link">オフセット岩村 TOP</a></li>
            <li class="c-bread__item">404 見つかりません</li>
        </ul>
        <div class="c-mv__inner">
            <div class="c-mv__content">
                <h1 class="c-title">
                    <span class="c-title__jp">404 見つかりません</span>
                    <span class="c-title__en">404 NOT FOUND</span>
                </h1>
            </div>
        </div>
    </section>
    <div class="p-404__inner">
        <div class="l-container">
            <p class="c_txt16" data-aos="fade-up">お探しのページは見つかりません。</br>一時的にアクセスできない状態か、移動もしくは削除されてしまった可能性があります。</p>
            <div class="p-404__btn" data-aos="fade-up">         
                <a href="<?php echo esc_url(home_url('/')); ?>" class="c-btn03">トップへ戻る</a> 
            </div>
        </div>        
    </div>
    <!-- ↑↑ 404 ↑↑ -->
</main>
<!-- ↑↑ main ↑↑ -->
<?php
get_footer();
?>

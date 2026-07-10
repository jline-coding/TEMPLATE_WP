<?php
get_header();
?>
<!-- ↓↓ main ↓↓ -->
<main>
    <!-- ↓↓ Manvisual ↓↓ -->
    <?php get_template_part( 'includes/templates/mv', null, [
        'jp_title' => 'ダミータイトル'
        'en_title' => 'dummy'
    ] ); ?>
    <!-- ↑↑ Manvisual ↑↑ -->
     
    <!-- ↓↓ Breadcrumb ↓↓ -->
    <div class="l-container">
        <?php get_template_part( 'includes/templates/breadcrumb' ); ?>
    </div>
    <!-- ↑↑ Breadcrumb ↑↑ -->
</main>
<!-- ↑↑ main ↑↑ -->
<?php
get_footer();
?>
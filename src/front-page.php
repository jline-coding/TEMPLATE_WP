<?php
get_header();
?>
<main class="p-top">
    <section class="p-top__mv">
        <div class="p-top__mv__sldier">
            <div class="c-slider">
                <div class="c-slider__item">
                    <figure class="c-slider__img">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/top/img_mv01.webp" alt="モダンなコワーキングスペース。長い木製テーブルに観葉植物、カウンター、そして黒い天井にスポットライト">
                    </figure>
                </div>
                <div class="c-slider__item">
                    <figure class="c-slider__img">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/top/img_mv02.webp" alt="モダンで清潔感のあるクリニックの待合室。グレーのソファ、波模様の壁、大きなモニター、そして絵画が飾られている">
                    </figure>
                </div>
                <div class="c-slider__item">
                    <figure class="c-slider__img">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/top/img_mv03.webp" alt="レトロな雰囲気の部屋。ユニークなデザインの青いレザーチェア、木製キャビネット、本棚、そして暖かい照明">
                    </figure>
                </div>
                <div class="c-slider__item">
                    <figure class="c-slider__img">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/top/img_mv04.webp" alt="晴れた日の田中医院の外観。白い壁と淡いピンクのタイル、バルコニー、そして駐輪場には自転車が2台停まっている。">
                    </figure>
                </div>
            </div>
        </div>
        <div class="p-top__mv__content">
            <h2 class="c-ttl44">建築の悩みをトータルサービスで解決。<br>技術と実績でお客様の最適解をご提案。</h2>
            <ul class="c-list">
                <li class="c-list__item">Technology</li>
                <li class="c-list__item">Track record</li>
                <li class="c-list__item">Solution</li>
            </ul>
        </div>
    </section>
    <section class="p-top__news">
        <?php 
            $args_news = array(
                'post_type' => 'news', 
                'posts_per_page' => 2,
                'post_status'    => 'publish',
                'orderby'        => 'date',
                'order'          => 'DESC',
            );      
            $query_news = new WP_Query($args_news);
        ?>
        <div class="p-top__news__head" data-aos="fade-up">
            <h2 class="c-title">
                <span class="c-title__jp">お知らせ</span>
                <span class="c-title__en">NEWS</span>
            </h2>
            <div class="p-top__news__more">
                <a href="<?php echo esc_url(home_url('/news/')); ?>" class="c-more">
                    <span class="c-more__content">LEARN MORE</span>
                    <svg class="c-more__icon" xmlns="http://www.w3.org/2000/svg" width="25.506" height="5.901" viewBox="0 0 25.506 5.901">
                        <path d="M3097,1756.586h24l-6.7-5" transform="translate(-3097.004 -1751.185)" fill="none" stroke="#afaead" stroke-width="1"/>
                    </svg>
                </a>
            </div>
        </div>
        <div class="p-top__news__inner">
            <?php if ($query_news->have_posts()) : ?>
            <div class="c-listnews">
                <?php while ($query_news->have_posts()) : $query_news->the_post(); ?>
                <div class="c-listnews__item" data-aos="fade-up">
                    <a href="<?php echo esc_url(get_the_permalink()); ?>" class="c-listnews__inner">
                        <span class="c-listnews__date"><?php echo esc_html(get_the_date('Y.m.d')); ?></span>
                        <span class="c-listnews__content"><?php echo esc_html(get_the_title()); ?></span>
                    </a>
                </div>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
            <?php else: ?>
                <p class="c-txt16" data-aos="fade-up">現在記事がございません。</p>
            <?php endif; ?>
        </div>
        <div class="p-top__news__content">
            <div class="p-top__news__imgs">
                <figure class="p-top__news__img01">
                    <img data-aos="fade-up" src="<?php echo get_template_directory_uri(); ?>/assets/images/top/img_01.webp" alt="カフェのようなコワーキングスペース。長い木製テーブルに観ợ植物、奥にはカウンターキッチンとスポットライト照明">
                </figure>
                <figure class="p-top__news__img02">
                    <img data-aos="fade-up" src="<?php echo get_template_directory_uri(); ?>/assets/images/top/img_02.webp" alt="白を基調とした広々としたオフィスエリア。整然と並ぶ白いデスクと黒のオフィスチェア、清潔感のある明るい照明">
                </figure>
            </div>
            <div class="p-top__news__flex">
                <div class="p-top__news__left">
                    <h3 class="c-ttl36" data-aos="fade-up">「工事して終わり」ではなく、<br class="pc">店舗・オフィス・工場の価値向上と<br class="pc">事業成長を見据えたパートナーとして<br class="pc">サポートいたします。</h3>
                    <p class="c-txt16" data-aos="fade-up">2006年の創業以来、建設業界の現場で培ってきた知識と経験を活かし、近畿トータルサービスは単なる仲介や管理にとどまらず、構造的・技術的な視点から建設課題にアプローチしてきました。<br><br>これからも、建物の状態、土地の特性、法規制、施工履歴までを読み解き、最適な解決策を迅速にご提案し続けていきます。<br><br>「施工に関する困りごとを、建設の知恵で解決する」<br>── それが、私たち「近畿トータルサービス」です。</p>
                    <div class="p-top__news__btn" data-aos="fade-up">
                        <a href="<?php echo esc_url(home_url('/reason/')); ?>" class="c-more01 c-more01--blue">
                            <span class="c-more01__content">LEARN MORE</span>
                            <svg class="c-more01__icon" xmlns="http://www.w3.org/2000/svg" width="25.506" height="5.901" viewBox="0 0 25.506 5.901">
                                <path d="M3097,1756.586h24l-6.7-5" transform="translate(-3097.004 -1751.185)" fill="none" stroke="#afaead" stroke-width="1"/>
                            </svg>
                        </a>
                    </div>
                </div>
                <figure class="p-top__news__img03">
                    <img data-aos="fade-up" src="<?php echo get_template_directory_uri(); ?>/assets/images/top/img_03.webp" alt="木目調の階段状の座席があるリラックススペース。黄色とグレーの円形クッションが並び、天井は黒でインダストリアルな雰囲気">
                </figure>
            </div>
        </div>
    </section>
    <section class="p-top__service">
        <div class="p-top__service__inner">
            <h2 class="c-title sp" data-aos="fade-up">
                <span class="c-title__jp">事業内容</span>
                <span class="c-title__en">SERVICE</span>
            </h2>
            <h3 class="c-ttl48 sp" data-aos="fade-up">建設の知見で、<br>企業の課題に応える。</h3>
            <div class="p-top__service__imgs" data-aos="fade-up">
                <figure class="p-top__service__img js-moreimg active" id="service_01">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/top/img_service_01.webp" alt="ヘルメットと作業着を着用した男女の技術者が、複雑な機械設備を点検している様子。一人はメモを取り、もう一人は機器を確認している">
                </figure>
                <figure class="p-top__service__img js-moreimg" id="service_02">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/top/img_service_02.webp" alt="大きな木製の本棚があるオフィスのラウンジエリア。中央には植栽のあるテーブルとモダンな椅子があり、落ち着いた照明">
                </figure>
                <figure class="p-top__service__img js-moreimg" id="service_03">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/top/img_service_03.webp" alt="林純薬工業のロゴが入ったスタイリッシュな自立看板があるビルのエントランス。ガラス張りの外壁と石畳の地面">
                </figure>
            </div>
            <div class="p-top__service__content">
                <h2 class="c-title pc" data-aos="fade-up">
                    <span class="c-title__jp">事業内容</span>
                    <span class="c-title__en">SERVICE</span>
                </h2>
                <h3 class="c-ttl48 pc" data-aos="fade-up">建設の知見で、<br>企業の課題に応える。</h3>
                <ul class="c-listbtn">
                    <li class="c-listbtn__item" data-aos="fade-up">
                        <a href="<?php echo esc_url(home_url('/business/#anchor_business_01')); ?>" class="c-link01 js-morelink" data-id="service_01">
                            <span class="c-link01__content">工場営繕</span>
                            <svg class="c-link01__icon" xmlns="http://www.w3.org/2000/svg" width="25.5" height="9" viewBox="0 0 25.5 9"><line x2="20" transform="translate(0.5 4.5)" fill="none" stroke="#cdcccc" stroke-linecap="round" stroke-width="1"/><path d="M4.5,0,9,8H0Z" transform="translate(25.5) rotate(90)" fill="#cdcccc"/></svg>
                        </a>
                    </li>
                    <li class="c-listbtn__item" data-aos="fade-up">
                        <a href="<?php echo esc_url(home_url('/business/#anchor_business_02')); ?>" class="c-link01 js-morelink" data-id="service_02">
                            <span class="c-link01__content">店舗・オフィス改装</span>
                            <svg class="c-link01__icon" xmlns="http://www.w3.org/2000/svg" width="25.5" height="9" viewBox="0 0 25.5 9"><line x2="20" transform="translate(0.5 4.5)" fill="none" stroke="#cdcccc" stroke-linecap="round" stroke-width="1"/><path d="M4.5,0,9,8H0Z" transform="translate(25.5) rotate(90)" fill="#cdcccc"/></svg>
                        </a>
                    </li>
                    <li class="c-listbtn__item" data-aos="fade-up">
                        <a href="<?php echo esc_url(home_url('/business/#anchor_business_03')); ?>" class="c-link01 js-morelink" data-id="service_03">
                            <span class="c-link01__content">テント・サイン<span class="c-link01__small">（看板）</span>事業</span>
                            <svg class="c-link01__icon" xmlns="http://www.w3.org/2000/svg" width="25.5" height="9" viewBox="0 0 25.5 9"><line x2="20" transform="translate(0.5 4.5)" fill="none" stroke="#cdcccc" stroke-linecap="round" stroke-width="1"/><path d="M4.5,0,9,8H0Z" transform="translate(25.5) rotate(90)" fill="#cdcccc"/></svg>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </section>
    <section class="p-top__works">
        <?php 
            $args_works = array(
                'post_type' => 'works', 
                'posts_per_page' => 6,
                'post_status'    => 'publish',
                'orderby'        => 'date',
                'order'          => 'DESC',
            );      
            $query_works = new WP_Query($args_works);
        ?>
        <div class="p-top__works__inner">
            <div class="p-top__works__head">
                <h2 class="c-title" data-aos="fade-up">
                    <span class="c-title__jp">施工事例</span>
                    <span class="c-title__en">WORKS</span>
                </h2>
                <div class="p-top__works__btn" data-aos="fade-up">
                    <a href="<?php echo esc_url(home_url('/works/')); ?>" class="c-more01">
                        <span class="c-more01__content">LEARN MORE</span>
                        <svg class="c-more01__icon" xmlns="http://www.w3.org/2000/svg" width="25.506" height="5.901" viewBox="0 0 25.506 5.901">
                            <path d="M3097,1756.586h24l-6.7-5" transform="translate(-3097.004 -1751.185)" fill="none" stroke="#afaead" stroke-width="1"/>
                        </svg>
                    </a>
                </div>
            </div>
            <?php if ($query_works->have_posts()) : ?>
            <div class="p-top__works__content c-slider01-wrap" data-aos="fade-up">
                <span class="c-slider01__next">
                    <svg xmlns="http://www.w3.org/2000/svg" width="78" height="78" viewBox="0 0 78 78">
                        <g transform="translate(-410 -3569)">
                            <circle cx="39" cy="39" r="39" transform="translate(410 3569)" fill="#37557d" opacity="0.88"/>
                            <g transform="translate(440.612 3596.494)">
                            <path id="arrow-2" data-name="arrow" d="M3419.237,4598.272l-11.388,11.389,11.388,11.388" transform="translate(-3407.848 -4598.272)" fill="none" stroke="#fff" stroke-linecap="round" stroke-width="2"/>
                            </g>
                        </g>
                    </svg>
                </span>
                <span class="c-slider01__prev">
                    <svg xmlns="http://www.w3.org/2000/svg" width="78" height="78" viewBox="0 0 78 78">
                        <g transform="translate(-410 -3569)">
                            <circle cx="39" cy="39" r="39" transform="translate(410 3569)" fill="#37557d" opacity="0.88"/>
                            <g transform="translate(440.612 3596.494)">
                            <path id="arrow-2" data-name="arrow" d="M3419.237,4598.272l-11.388,11.389,11.388,11.388" transform="translate(-3407.848 -4598.272)" fill="none" stroke="#fff" stroke-linecap="round" stroke-width="2"/>
                            </g>
                        </g>
                    </svg>
                </span>
                <div class="p-top__works__slider">
                    <div class="c-slider01">
                        <?php while ($query_works->have_posts()) : $query_works->the_post(); $cate = get_the_terms(get_the_ID(), 'works-cate');?>
                        <div class="c-slider01__item">
                            <a href="<?php the_permalink(); ?>" class="c-slider01__inner">
                                <?php $thumb_id = get_post_thumbnail_id(get_the_ID(), 'full'); ?>
                                <div class="c-slider01__img">
                                    <figure class="c-slider01__img__content">
                                        <?php echo $thumb_id ? wp_get_attachment_image($thumb_id, 'full') : '<img src="'.esc_url(get_template_directory_uri().'/assets/images/common/img_dummy_01.webp').'" alt="'.esc_attr(get_the_title()).'">'; ?>
                                    </figure>
                                </div>
                                <div class="c-slider01__content">
                                    <?php if (!empty($cate) && is_array($cate)):?>
                                        <div class="c-tags">
                                            <?php foreach ($cate as $term):?>
                                                <?php if ($term->parent == 0) : ?>
                                                    <span class="c-tag">
                                                        <?php echo esc_html($term->name); ?>
                                                    </span>
                                                <?php endif; ?>
                                            <?php endforeach; ?>  
                                        </div>
                                    <?php  endif; ?>
                                    <h3 class="c-ttl18"><?php echo esc_html(get_the_title()); ?></h3>
                                    <?php if (!empty($cate) && is_array($cate)):?>
                                        <div class="c-labels">
                                            <?php foreach ($cate as $term):?>
                                                <?php if ($term->parent != 0) : ?>
                                                    <span class="c-label">
                                                        <?php echo esc_html($term->name); ?>
                                                    </span>
                                                <?php endif; ?>
                                            <?php endforeach; ?>  
                                        </div>
                                    <?php  endif; ?>
                                </div>
                            </a>
                        </div>
                        <?php endwhile; wp_reset_postdata(); ?>  
                    </div>
                </div>
            </div>
            <?php else: ?>
                <p class="c-txt16" data-aos="fade-up">現在記事がございません。</p>
            <?php endif; ?>
        </div>
    </section>
    <section class="p-top__company">
        <div class="p-top__company__inner">
            <div class="p-top__company__slider">
                <div class="c-slider02">
                    <figure class="c-slider02__img">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/top/img_company_01.webp" alt="木目調の書棚と大きなテーブルがある、落ち着いた雰囲気のモダンなオフィス休憩スペース">
                    </figure>
                    <figure class="c-slider02__img">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/top/img_company_02.webp" alt="ヘルメットを被り、クリップボードを持って青空の下に立つ笑顔の男性作業員">
                    </figure>
                    <figure class="c-slider02__img">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/top/img_company_04.webp" alt="「林純薬工業」のロゴが掲げられた、白を基調とした清潔感のある受付カウンター">
                    </figure>
                    <figure class="c-slider02__img">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/top/img_company_05.webp" alt="リフォーム中または点検中の室内で、タブレットを手に窓際を確認する作業服姿のスタッフ">
                    </figure>
                    <figure class="c-slider02__img">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/top/img_company_06.webp" alt="「林純薬工業」の看板が設置された、石造りの重厚なビルのエントランス外観">
                    </figure>
                </div>
                <div class="c-slider02 c-slider02--rtl">
                    <figure class="c-slider02__img">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/top/img_company_07.webp" alt="資料を確認しながら上方を向いて微笑む、ヘルメットと作業服姿の女性スタッフ">
                    </figure>
                    <figure class="c-slider02__img">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/top/img_company_08.webp" alt="複数のモニターと検査機器が設置された、白く清潔な臨床検査室または診察室">
                    </figure>
                    <figure class="c-slider02__img">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/top/img_company_09.webp" alt="アート作品が飾られた、個性的でクリエイティブなデザインのプライベートオフィス">
                    </figure>
                    <figure class="c-slider02__img">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/top/img_company_10.webp" alt="作業現場でヘルメットのつばに手をかけ、真剣な表情で空を見上げる男性作業員">
                    </figure>
                    <figure class="c-slider02__img">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/top/img_company_11.webp" alt="足場で作業を行う、腰道具を装着した建設現場の作業員の背面ショット">
                    </figure>
                    <figure class="c-slider02__img">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/top/img_company_12.webp" alt="木目調のテーブルとデザイナーズチェアが並ぶ、夜景が見えるおしゃれなレストランの店内">
                    </figure>
                </div>
            </div>
            <div class="p-top__company__content">
                <h2 class="c-title" data-aos="fade-up">
                    <span class="c-title__jp">企業情報</span>
                    <span class="c-title__en">COMPANY</span>
                </h2>
                <h3 class="c-ttl48" data-aos="fade-up">地域に根ざし、<br class="sp">信頼を築く。</h3>
                <ul class="c-listbtn">
                    <li class="c-listbtn__item" data-aos="fade-up">
                        <a href="<?php echo esc_url(home_url('/company/#ttl_02')); ?>" class="c-link01">
                            <span class="c-link01__content">企業理念</span>
                            <svg class="c-link01__icon" xmlns="http://www.w3.org/2000/svg" width="25.5" height="9" viewBox="0 0 25.5 9"><line x2="20" transform="translate(0.5 4.5)" fill="none" stroke="#cdcccc" stroke-linecap="round" stroke-width="1"/><path d="M4.5,0,9,8H0Z" transform="translate(25.5) rotate(90)" fill="#cdcccc"/></svg>
                        </a>
                    </li>
                    <li class="c-listbtn__item" data-aos="fade-up">
                        <a href="<?php echo esc_url(home_url('/company/#ttl_01')); ?>" class="c-link01">
                            <span class="c-link01__content">代表挨拶</span>
                            <svg class="c-link01__icon" xmlns="http://www.w3.org/2000/svg" width="25.5" height="9" viewBox="0 0 25.5 9"><line x2="20" transform="translate(0.5 4.5)" fill="none" stroke="#cdcccc" stroke-linecap="round" stroke-width="1"/><path d="M4.5,0,9,8H0Z" transform="translate(25.5) rotate(90)" fill="#cdcccc"/></svg>
                        </a>
                    </li>
                    <li class="c-listbtn__item" data-aos="fade-up">
                        <a href="<?php echo esc_url(home_url('/company/#ttl_03')); ?>" class="c-link01">
                            <span class="c-link01__content">会社概要</span>
                            <svg class="c-link01__icon" xmlns="http://www.w3.org/2000/svg" width="25.5" height="9" viewBox="0 0 25.5 9"><line x2="20" transform="translate(0.5 4.5)" fill="none" stroke="#cdcccc" stroke-linecap="round" stroke-width="1"/><path d="M4.5,0,9,8H0Z" transform="translate(25.5) rotate(90)" fill="#cdcccc"/></svg>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </section>
    <section class="p-top__recruit">
        <figure class="p-top__recruit__bg">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/common/bg_recruit.webp" alt="ノートパソコンを持ち、笑顔でこちらを見つめる若手ビジネスマンとビジネスウーマンの男女">
        </figure>
        <div class="p-top__recruit__inner">
            <div class="p-top__recruit__content">
                <h2 class="c-title" data-aos="fade-up">
                    <span class="c-title__jp">採用情報</span>
                    <span class="c-title__en">RECRUIT</span>
                </h2>
                <p class="c-txt16" data-aos="fade-up">建設と施工のプロフェッショナルとして、地域の課題に向き合い、解決していく。<br>その力は、現場の経験だけでなく、人との信頼から生まれます。<br>近畿トータルサービスでは、技術と誠実さを兼ね備えた仲間を募集しています。</p>
                <div class="p-top__recruit__btn" data-aos="fade-up">
                    <a href="<?php echo esc_url(home_url('/recruit/')); ?>" class="c-more01 c-more01--blue">
                        <span class="c-more01__content">LEARN MORE</span>
                        <svg class="c-more01__icon" xmlns="http://www.w3.org/2000/svg" width="25.506" height="5.901" viewBox="0 0 25.506 5.901">
                            <path d="M3097,1756.586h24l-6.7-5" transform="translate(-3097.004 -1751.185)" fill="none" stroke="#afaead" stroke-width="1"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>
<?php
get_footer();
?>
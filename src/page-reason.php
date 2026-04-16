<?php
get_header();
?>
<main class="p-reason">
    <?php get_template_part('includes/template', 'bread'); ?>
    <section class="c-mv">
        <div class="c-mv__inner">
            <div class="c-mv__content">
                <h1 class="c-mv__title">
                    <span class="c-mv__title__jp"><?php echo esc_html(get_the_title()) ?></span>
                    <?php if(get_the_excerpt()): ?>
                    <span class="c-mv__title__en"><?php echo esc_html(get_the_excerpt()) ?></span>
                    <?php endif; ?>
                </h1>
            </div>
            <figure class="c-mv__img">
                <?php echo has_post_thumbnail() ? get_the_post_thumbnail(get_the_ID(), 'large') : '<img src="'.esc_url(get_template_directory_uri().'/assets/images/common/img_dummy_01.webp').'" alt="'.esc_attr(get_the_title()).'">'; ?>
            </figure>
        </div>
    </section>
    <section class="p-reason01">
        <div class="p-reason01__inner">
            <figure class="p-reason01__img">
                <img data-aos="fade-up" src="<?php echo get_template_directory_uri(); ?>/assets/images/reason/img_reason_01.webp" alt="朝焼けの街を見下ろす作業員、女性、スーツ姿の男性のろしろ姿。未来への展望を象徴するイメージ">
            </figure>
            <div class="p-reason01__content">
                <h2 class="c-ttl36" data-aos="fade-up">「工事して終わり」ではなく、<br class="pc">店舗・オフィス・工場の価値向上と<br class="pc">事業成長を見据えたパートナーとして<br class="pc">サポートいたします。</h2>
                <p class="c-txt16" data-aos="fade-up">2006年の創業以来、建設業界の現場で培ってきた知識と経験を活かし、近畿トータルサービスは単なる仲介や管理にとどまらず、構造的・技術的な視点から建設課題にアプローチしてきました。<br><br>これからも、建物の状態、土地の特性、法規制、施工履歴までを読み解き、最適な解決策を迅速にご提案し続けていきます。<br><br>「施工に関する困りごとを、建設の知恵で解決する」<br>── それが、私たち「近畿トータルサービス」です。</p>
            </div>
        </div>
    </section>
    <section class="p-reason02">
        <div class="p-reason02__inner">
            <h2 class="c-title" data-aos="fade-up">
                <span class="c-title__jp">近畿トータルサービスが選ばれる3つの理由</span>
                <span class="c-title__en">REASON</span>
            </h2>
            <p class="c-txt16" data-aos="fade-up">建設業の専門性、多業種対応の柔軟性、自社施工による一貫体制。<br>近畿トータルサービスだからこそできる、課題解決力と提案力があります。</p>
            <div class="c-list01">
                <div class="c-list01__item" data-aos="fade-up">
                    <div class="c-list01__inner">
                        <figure class="c-list01__img">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/reason/img_reason_02.webp" alt="ヘルメットと図面を持つ作業員と、近代的なビル群や道路を重ねたダブル露出のイメージ">
                        </figure>
                        <div class="c-list01__content">
                            <h3 class="c-ttl28 c-ttl28--line01 c-ttl28--center">迅速な対応と納期の早さ</h3>
                            <p class="c-txt16">お客様の要望に素早く対応し、迅速な施工を実現します。<br>経験豊富なスタッフと効率的な管理体制により、納期遵守を徹底しています。<br>急ぎのお客様の対応も可能です！</p>
                        </div>
                    </div>
                </div>
                <div class="c-list01__item" data-aos="fade-up">
                    <div class="c-list01__inner">
                        <figure class="c-list01__img">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/reason/img_reason_03.webp" alt="デスクで図面を指さしながら打ち合わせをするビジネスパーソンの手元">
                        </figure>
                        <div class="c-list01__content">
                            <h3 class="c-ttl28 c-ttl28--line01 c-ttl28--center">実用性と機能性に優れた設計</h3>
                            <p class="c-txt16">お客様の店舗内での使いやすさを最優先に考え、実用性と機能性に優れた内装設計を提供します。<br>限られたスペースや建物の特性を十分に考慮し、最適なレイアウトを実現します。</p>
                        </div>
                    </div>
                </div>
                <div class="c-list01__item" data-aos="fade-up">
                    <div class="c-list01__inner">
                        <figure class="c-list01__img">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/reason/img_reason_04.webp" alt="電卓と拡大鏡、青い積み木が置かれたデスク。緻密な計算や調査をイメージ">
                        </figure>
                        <div class="c-list01__content">
                            <h3 class="c-ttl28 c-ttl28--line01 c-ttl28--center">高いコストパフォーマンス</h3>
                            <p class="c-txt16">当社の内装工事サービスは、高品質な仕上がりを維持しながら、お客様の予算に合わせた柔軟な提案を行います。長年の経験と効率的な施工プロセスにより、最適な価格設定を実現します。</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="p-reason03">
        <div class="p-reason03__inner" data-aos="fade-up">
            <figure class="p-reason03__img">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/reason/img_reason_05.webp" alt="明るいリビングで手帳を確認するスーツ姿の男性の背中">
            </figure>
            <div class="p-reason03__content">
                <h2 class="c-ttl48">“満足”の先に、<br class="sp">“信頼”がある。</h2>
                <p class="c-txt16">お客様の不安を取り除き、安心して暮らせる・使える空間をつくること。<br>そのために、私たちは“見えない問題”にも真摯に向き合い、誠実な施工を心がけています。<br>2006年の創業以来培ってきた現場経験を活かし、信頼される仕事をこれからも積み重ねていきます。</p>
            </div>
        </div>
        <div class="p-reason03__slider" data-aos="fade-up">
            <div class="c-slider03">
                <figure class="c-slider03__img">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/reason/img_reason_slider_01.webp" alt="レンガ壁とネオンサインが特徴的な、温かみのあるカフェの店内風景">
                </figure>
                <figure class="c-slider03__img">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/reason/img_reason_slider_02.webp" alt="観葉植物とカラフルなスツールが配置された、開放的でモダンなオフィスのリフレッシュスペース">
                </figure>
                <figure class="c-slider03__img">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/reason/img_reason_slider_03.webp" alt="木目調のアイランドキッチンと、波打つようなデザインの天井が印象的なラウンジエリア">
                </figure>
                <figure class="c-slider03__img">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/reason/img_reason_slider_04.webp" alt="青空の下に設置された「パリ・サンジェルマン」公式ストアの屋外イベントテント">
                </figure>
            </div>
        </div>
    </section>
    <section class="p-reason__works">
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
        <div class="p-reason__works__inner">
            <div class="p-reason__works__head">
                <h2 class="c-title" data-aos="fade-up">
                    <span class="c-title__jp">施工事例</span>
                    <span class="c-title__en">WORKS</span>
                </h2>
                <div class="p-reason__works__btn" data-aos="fade-up">
                    <a href="<?php echo esc_url(home_url('/works/')); ?>" class="c-more01">
                        <span class="c-more01__content">LEARN MORE</span>
                        <svg class="c-more01__icon" xmlns="http://www.w3.org/2000/svg" width="25.506" height="5.901" viewBox="0 0 25.506 5.901">
                            <path d="M3097,1756.586h24l-6.7-5" transform="translate(-3097.004 -1751.185)" fill="none" stroke="#afaead" stroke-width="1"/>
                        </svg>
                    </a>
                </div>
            </div>
            <?php if ($query_works->have_posts()) : ?>
            <div class="p-reason__works__content c-slider01-wrap" data-aos="fade-up">
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
                <div class="p-reason__works__slider">
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
    <div class="p-reason04">
        <div class="l-container">
            <h2 class="c-title" data-aos="fade-up">
                <span class="c-title__jp">主要取引先</span>
                <span class="c-title__en">BUSINESS PARTNER</span>
            </h2>
            <div class="c-list02">
                <div class="c-list02__item" data-aos="fade-up">
                    <a href="https://www.inaba.co.jp/" target="_blank" class="c-btn01">因幡電機産業株式会社</a>
                </div>
                <div class="c-list02__item" data-aos="fade-up">
                    <a href="https://www.nakazawa-kenpan.co.jp/" target="_blank" class="c-btn01">ナカザワ建販株式会社</a>
                </div>
                <div class="c-list02__item" data-aos="fade-up">
                    <a href="https://www.sedia-system.co.jp/" target="_blank" class="c-btn01">SEDIA SYSTEM</a>
                </div>
                <div class="c-list02__item" data-aos="fade-up">
                    <a href="https://osaka-daimatsu.co.jp/" target="_blank" class="c-btn01">株式会社大松</a>
                </div>
                <div class="c-list02__item" data-aos="fade-up">
                    <a href="https://www.tak.co.jp/ja/" target="_blank" class="c-btn01">高島株式会社</a>
                </div>
                <div class="c-list02__item" data-aos="fade-up">
                    <a href="https://www.artplanning.co.jp/" target="_blank" class="c-btn01">アートプランニング株式会社</a>
                </div>
                <div class="c-list02__item" data-aos="fade-up">
                    <a href="https://www.takeden.co.jp/" target="_blank" class="c-btn01">株式会社たけでん</a>
                </div>
                <div class="c-list02__item" data-aos="fade-up">
                    <a href="https://kiko-c.co.jp/" target="_blank" class="c-btn01">株式会社キコークリエイト</a>
                </div>
                <div class="c-list02__item" data-aos="fade-up">
                    <a href="https://www.takara-net.co.jp/" target="_blank" class="c-btn01">タカラ通商株式会社</a>
                </div>
                <div class="c-list02__item" data-aos="fade-up">
                    <a href="https://www.az-com.co.jp/" target="_blank" class="c-btn01">株式会社<br>関西丸和ロジスティクス</a>
                </div>
                <div class="c-list02__item" data-aos="fade-up">
                    <a href="https://www.yanmarmarche.com/" target="_blank" class="c-btn01">ヤンマーマルシェ株式会社</a>
                </div>
                <div class="c-list02__item" data-aos="fade-up">
                    <a href="https://www.naiki.co.jp/" target="_blank" class="c-btn01">株式会社ナイキ</a>
                </div>
                <div class="c-list02__item" data-aos="fade-up">
                    <a href="https://www.nihonkensetsu.co.jp/" target="_blank" class="c-btn01">日本建設株式会社</a>
                </div>
                <div class="c-list02__item" data-aos="fade-up">
                    <a href="https://www.fukunishi.com/" target="_blank" class="c-btn01">福西電機株式会社</a>
                </div>
                <div class="c-list02__item" data-aos="fade-up">
                    <a href="https://kohnan.co.jp/" target="_blank" class="c-btn01">幸南食糧株式会社</a>
                </div>
                <div class="c-list02__item" data-aos="fade-up">
                    <a href="https://www.d-infi.com/" target="_blank" class="c-btn01">ダイハツインフィニアース<br>株式会社</a>
                </div>
                <div class="c-list02__item" data-aos="fade-up">
                    <a href="https://www.nipponhoist.co.jp/" target="_blank" class="c-btn01">日本ホイスト株式会社</a>
                </div>
                <div class="c-list02__item" data-aos="fade-up">
                    <a href="https://rptopla.co.jp/" target="_blank" class="c-btn01">RP東プラ株式会社</a>
                </div>
                <div class="c-list02__item" data-aos="fade-up">
                    <a href="https://www.panasonic.com/jp/about.html" target="_blank" class="c-btn01">パナソニック株式会社</a>
                </div>
                <div class="c-list02__item" data-aos="fade-up">
                    <a href="https://www.lixil.co.jp/" target="_blank" class="c-btn01">株式会社LIXIL</a>
                </div>
                <div class="c-list02__item" data-aos="fade-up">
                    <a href="https://jp.toto.com/" target="_blank" class="c-btn01">TOTO株式会社</a>
                </div>
                <div class="c-list02__item" data-aos="fade-up">
                    <a href="https://www.laserck.com/" target="_blank" class="c-btn01">株式会社レザック</a>
                </div>
                <div class="c-list02__item" data-aos="fade-up">
                    <a href="https://www.daikin.co.jp/" target="_blank" class="c-btn01">ダイキン工業株式会社</a>
                </div>
                <div class="c-list02__item" data-aos="fade-up">
                    <a href="https://www.global.toshiba/jp/top.html" target="_blank" class="c-btn01">株式会社東芝</a>
                </div>
                <div class="c-list02__item" data-aos="fade-up">
                    <a href="https://www.koizumiseiki.co.jp/" target="_blank" class="c-btn01">小泉成器株式会社</a>
                </div>
                <div class="c-list02__item" data-aos="fade-up">
                    <a href="https://www.sanlitu.com/" target="_blank" class="c-btn01">株式会社三立工業</a>
                </div>
                <div class="c-list02__item" data-aos="fade-up">
                    <a href="https://hardycreate.com/" target="_blank" class="c-btn01">株式会社ハーディクリエイト</a>
                </div>
                <div class="c-list02__item" data-aos="fade-up">
                    <a href="https://www.sanshinkinzoku.co.jp/" target="_blank" class="c-btn01">三進金属工業株式会社</a>
                </div>
                <div class="c-list02__item" data-aos="fade-up">
                    <a href="https://www.takiron-ci.co.jp/" target="_blank" class="c-btn01">タキロンシーアイ株式会社</a>
                </div>
                <div class="c-list02__item" data-aos="fade-up">
                    <a href="https://www.takiron-ci.co.jp/corporate/group/tech/" target="_blank" class="c-btn01">タキロンテック株式会社</a>
                </div>
                <div class="c-list02__item" data-aos="fade-up">
                    <a href="https://www.kansai-mazda.co.jp/" target="_blank" class="c-btn01">株式会社関西マツダ</a>
                </div>
            </div>
            <p class="c-txt12 u-fw--500" data-aos="fade-up">※順不同</p>
        </div>
    </div>
</main>
<?php
get_footer();
?>
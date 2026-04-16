<?php
get_header();
?>
<main class="p-business">
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
    <section class="p-business01">
        <div class="l-container">
            <h2 class="c-title" data-aos="fade-up">
                <span class="c-title__jp">これまで培った技術と現場力で、建設業界の課題を“安心”に変える。</span>
                <span class="c-title__en">OUR BUSINESS</span>
            </h2>
            <p class="c-txt16" data-aos="fade-up">近畿トータルサービスは、2006年の創業以来積み重ねてきた建築の知識と経験をもとに、建設業界に潜む問題点を確実に見つけ出し、中古物件に対する不安を取り除くことを大切にしています。<br>
							お客様が安心して暮らせる・使える快適な空間を提供するため、誠実な対応と確かな技術でサポートしています。</p>
            <div class="c-anchor">
                <div class="c-anchor__item" data-aos="fade-up">
                    <a href="#anchor_business_01" class="c-btn02">
                        <span class="c-btn02__content">工場営繕</span>
                        <svg class="c-btn02__icon" xmlns="http://www.w3.org/2000/svg" width="15.882" height="5" viewBox="0 0 15.882 5"><line x2="12.611" transform="translate(0.5 2.5)" fill="none" stroke="#cdcccc" stroke-linecap="round" stroke-width="1"/><path d="M2.5,0,5,5H0Z" transform="translate(15.882) rotate(90)" fill="#cdcccc"/></svg>
                    </a>
                </div>
                <div class="c-anchor__item" data-aos="fade-up">
                    <a href="#anchor_business_02" class="c-btn02">
                        <span class="c-btn02__content">店舗・オフィス改装</span>
                        <svg class="c-btn02__icon" xmlns="http://www.w3.org/2000/svg" width="15.882" height="5" viewBox="0 0 15.882 5"><line x2="12.611" transform="translate(0.5 2.5)" fill="none" stroke="#cdcccc" stroke-linecap="round" stroke-width="1"/><path d="M2.5,0,5,5H0Z" transform="translate(15.882) rotate(90)" fill="#cdcccc"/></svg>
                    </a>
                </div>
                <div class="c-anchor__item" data-aos="fade-up">
                    <a href="#anchor_business_03" class="c-btn02">
                        <span class="c-btn02__content">テント・サイン<span class="c-btn02__small">（看板）</span>事業</span>
                        <svg class="c-btn02__icon" xmlns="http://www.w3.org/2000/svg" width="15.882" height="5" viewBox="0 0 15.882 5"><line x2="12.611" transform="translate(0.5 2.5)" fill="none" stroke="#cdcccc" stroke-linecap="round" stroke-width="1"/><path d="M2.5,0,5,5H0Z" transform="translate(15.882) rotate(90)" fill="#cdcccc"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </section>
    <section class="p-business02">
        <div class="c-box" id="anchor_business_01">
            <figure class="c-box__img">
                <img data-aos="fade-up" src="<?php echo get_template_directory_uri(); ?>/assets/images/business/img_business_01.webp" alt="設備点検を行う2人の作業員。1人がチェックリストを記入し、もう1人が機械を確認している様子">
            </figure>
            <div class="c-box__content">
                <h2 class="c-title c-title--line" data-aos="fade-up">
                    <span class="c-title__jp">工場営繕</span>
                    <span class="c-title__en">FACTORY MAINTENANCE</span>
                </h2>
                <h3 class="c-ttl28" data-aos="fade-up">工場の“困った”を、<br class="pc">改修・修繕・設備工事まで<br class="pc">ワンストップで解決。</h3>
            </div>
        </div>
        <div class="l-container">
            <div class="c-box01">
                <div class="c-box01__content">
                    <h3 class="c-ttl28" data-aos="fade-up">破損1点、不具合1ヶ所でも現地を確認し、<br class="pc">状況にあわせた最適な工事をご提案・施工いたします。</h3>
                    <p class="c-txt16" data-aos="fade-up">建築・設備のリニューアル・入居工事・原状回復工事などを承ります。長年使用されてきた工場内の設備の損傷は、重大な事故やトラブルにつながってしまう危険性があります。大きなリスクを背負ったままにすることなく、安全に作業できる環境にするためのサポートを行わせていただきます。<br>また、ただ原状回復を行うだけではなく、エネルギー効率や今後のメンテナンスを考慮した設備への対応などもご相談していただくことが可能です。</p>
                </div>
                <figure class="c-box01__img">
                    <img data-aos="fade-up" src="<?php echo get_template_directory_uri(); ?>/assets/images/business/img_business_02.webp" alt="工場内で配管設備を前に打ち合わせをする3人の技術者">
                </figure>
            </div>
        </div>
    </section>
		<section class="p-business06">
        <div class="l-container">
            <h2 class="c-ttl28" data-aos="fade-up">このようなお悩みを近畿トータルサービスは解決します。</h2>
            <div class="c-list02 c-list02--cl2">
                <div class="c-list02__item" data-aos="fade-up">
                    <span class="c-btn01 c-btn01--big c-btn01--radius u-fw--500">その場しのぎの修繕を繰り返している為、耐久性・安全面が不安。</span>
                </div>
                <div class="c-list02__item" data-aos="fade-up">
                    <span class="c-btn01 c-btn01--big c-btn01--radius u-fw--500">品質管理のために環境を整えたい。</span>
                </div>
                <div class="c-list02__item" data-aos="fade-up">
                    <span class="c-btn01 c-btn01--big c-btn01--radius u-fw--500">気温による作業環境が過酷で換気も悪く粉塵もたまりやすい。</span>
                </div>
                <div class="c-list02__item" data-aos="fade-up">
                    <span class="c-btn01 c-btn01--big c-btn01--radius u-fw--500">転落など事故のリスクがある危険箇所は分かっているが手をつけられていない。</span>
                </div>
                <div class="c-list02__item" data-aos="fade-up">
                    <span class="c-btn01 c-btn01--big c-btn01--radius u-fw--500">工事区分が分かれていて管理が面倒。</span>
                </div>
                <div class="c-list02__item" data-aos="fade-up">
                    <span class="c-btn01 c-btn01--big c-btn01--radius u-fw--500">稼働を止める期間を最小限にしたい、工場を動かしながら工事したい。</span>
                </div>
            </div>
        </div>
    </section>
		<section class="p-business03">
        <h2 class="c-ttl32" data-aos="fade-up">近畿トータルサービスが<br class="sp">できること</h2>
        <div class="c-box02">
            <figure class="c-box02__img" data-aos="fade-up">
                <span class="c-label01">
                    <span class="c-label01__en">SOLUTION</span>
                    <span class="c-label01__num">01</span>
                </span>
                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/business/img_business_03.webp" alt="大型機械が並ぶ清潔で整理整頓された工場の全景">
            </figure>
            <div class="c-box02__content">
                <h2 class="c-ttl28" data-aos="fade-up">工場改修全般工事</h2>
                <p class="c-txt16" data-aos="fade-up">操業中の部分改修から大規模リニューアルまで、工場建屋・内部レイアウト・設備・配管・動線変更を含む幅広い改修工事に対応します。<br>
									生産工程や工期制約を踏まえた工程管理により、稼働への影響を最小限に抑えつつ、老朽化対策や生産効率向上につながる最適な改修計画をご提案します。</p>
            </div>
        </div>
        <div class="c-box02 c-box02--reverse">
            <figure class="c-box02__img" data-aos="fade-up">
                <span class="c-label01">
                    <span class="c-label01__en">SOLUTION</span>
                    <span class="c-label01__num">02</span>
                </span>
                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/business/img_business_04.webp" alt="ヘルメットのライトを点灯させ、タブレットを手にする作業員の笑顔">
            </figure>
            <div class="c-box02__content">
                <h2 class="c-ttl28" data-aos="fade-up">機械設備工事</h2>
                <p class="c-txt16" data-aos="fade-up">製造・建設現場における各種機械設備の新設・更新・移設・撤去まで一貫して対応します。<br>
									重量物の搬入据付や既存設備との取り合いを考慮した施工計画により、安全性・作業性・将来の拡張性を確保。稼働停止時間を抑え、安定した生産体制の構築を支援します。</p>
            </div>
        </div>
        <div class="c-box02">
            <figure class="c-box02__img" data-aos="fade-up">
                <span class="c-label01">
                    <span class="c-label01__en">SOLUTION</span>
                    <span class="c-label01__num">03</span>
                </span>
                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/business/img_business_05.webp" alt="工場の天井に設置された空調設備と照明システム">
            </figure>
            <div class="c-box02__content">
                <h2 class="c-ttl28" data-aos="fade-up">工場環境改善工事</h2>
                <p class="c-txt16" data-aos="fade-up">騒音・振動・粉塵・臭気・排気など、工場特有の環境課題に対して、現場に最適な改善工事を実施します。<br>
									周辺環境への配慮や法令遵守はもちろん、作業者の負担軽減と安定操業の両立を目指し、持続可能な工場環境づくりをサポートします。</p>
            </div>
        </div>
        <div class="c-box02 c-box02--reverse">
            <figure class="c-box02__img" data-aos="fade-up">
                <span class="c-label01">
                    <span class="c-label01__en">SOLUTION</span>
                    <span class="c-label01__num">04</span>
                </span>
                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/business/img_business_06.webp" alt="多数の工作機械が稼働する工場の全景">
            </figure>
            <div class="c-box02__content">
                <h2 class="c-ttl28" data-aos="fade-up">労働環境改善工事</h2>
                <p class="c-txt16" data-aos="fade-up">作業動線の見直し、空調・照明の最適化、作業スペースの改善など、働きやすい職場づくりを目的とした工事を行います。<br>
									作業効率の向上だけでなく、疲労軽減や人材定着にもつながる快適な労働環境を実現します。</p>
            </div>
        </div>
        <div class="c-box02">
            <figure class="c-box02__img" data-aos="fade-up">
                <span class="c-label01">
                    <span class="c-label01__en">SOLUTION</span>
                    <span class="c-label01__num">05</span>
                </span>
                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/business/img_business_07.webp" alt="電気設備の配線工事を行う作業員">
            </figure>
            <div class="c-box02__content">
                <h2 class="c-ttl28" data-aos="fade-up">安全対策工事</h2>
                <p class="c-txt16" data-aos="fade-up">転倒・落下・巻き込まれなどの労働災害を未然に防ぐため、危険箇所の洗い出しから対策工事まで一貫して対応します。<br>
									安全柵・防護設備・転落防止・区画表示など、現場に即した実効性の高い安全対策を提案・施工します。</p>
            </div>
        </div>
        <div class="c-box02 c-box02--reverse">
            <figure class="c-box02__img" data-aos="fade-up">
                <span class="c-label01">
                    <span class="c-label01__en">SOLUTION</span>
                    <span class="c-label01__num">06</span>
                </span>
                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/business/img_business_072.webp" alt="地盤改良工事で使用される大型のアースオーガー">
            </figure>
            <div class="c-box02__content">
                <h2 class="c-ttl28" data-aos="fade-up">特殊設備工事</h2>
                <p class="c-txt16" data-aos="fade-up">製造ライン専用設備や特殊用途設備など、高度な技術と経験を要する工事にも対応可能です。<br>
									設計意図を正確に理解し、現場条件に合わせた柔軟な施工で、信頼性の高い設備導入を実現します。</p>
            </div>
        </div>
    </section>
    <section class="p-business__works p-business04">
        <?php 
            $args_works01 = array(
                'post_type' => 'works', 
                'posts_per_page' => 6,
                'post_status'    => 'publish',
                'orderby'        => 'date',
                'order'          => 'DESC',
                'tax_query' => array(
                    array(
                        'taxonomy' => 'works-cate',
                        'field'    => 'slug',
                        'terms'    => array('工場営繕'),
                    ),
                ),
            );      
            $query_works01 = new WP_Query($args_works01);
        ?>
        <div class="p-business__works__inner">
            <div class="p-business__works__head">
                <h2 class="c-title" data-aos="fade-up">
                    <span class="c-title__jp">施工事例</span>
                    <span class="c-title__en">WORKS</span>
                </h2>
                <div class="p-business__works__btn" data-aos="fade-up">
                    <a href="<?php echo esc_url(home_url('/works/category/工場営繕/')); ?>" class="c-more01">
                        <span class="c-more01__content">LEARN MORE</span>
                        <svg class="c-more01__icon" xmlns="http://www.w3.org/2000/svg" width="25.506" height="5.901" viewBox="0 0 25.506 5.901">
                            <path d="M3097,1756.586h24l-6.7-5" transform="translate(-3097.004 -1751.185)" fill="none" stroke="#afaead" stroke-width="1"/>
                        </svg>
                    </a>
                </div>
            </div>
            <?php if ($query_works01->have_posts()) : ?>
            <div class="p-business__works__content c-slider01-wrap" data-aos="fade-up">
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
                <div class="p-business__works__slider">
                    <div class="c-slider01">
                        <?php while ($query_works01->have_posts()) : $query_works01->the_post(); $cate = get_the_terms(get_the_ID(), 'works-cate');?>
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
    <section class="p-business05">
        <div class="c-box c-box--reverse" id="anchor_business_02">
            <figure class="c-box__img">
                <img data-aos="fade-up" src="<?php echo get_template_directory_uri(); ?>/assets/images/business/img_business_08.webp" alt="木目調の書棚と大きなテーブルがあるモダンで開放的なオフィス休憩スペース">
            </figure>
            <div class="c-box__content">
                <h2 class="c-title c-title--line" data-aos="fade-up">
                    <span class="c-title__jp">店舗・オフィス改装</span>
                    <span class="c-title__en">STORE & OFFICE RENOVATION</span>
                </h2>
                <h3 class="c-ttl28" data-aos="fade-up">空間・設備・環境まで。<br class="pc">店舗・オフィスを丸ごと最適化。</h3>
                <h4 class="c-ttl20" data-aos="fade-up">限られた条件でも、内装から設備・環境・安全対策まで柔軟に対応します。</h4>
            </div>
        </div>
    </section>
    <section class="p-business06">
        <div class="l-container">
            <h2 class="c-ttl28" data-aos="fade-up">このようなお悩みを近畿トータルサービスは解決します。</h2>
            <div class="c-list02 c-list02--cl2">
                <div class="c-list02__item" data-aos="fade-up">
                    <span class="c-btn01 c-btn01--big c-btn01--radius u-fw--500">新店舗の内装やディスプレイ配置が未定で、顧客の利便性やスタッフの実用性が心配。</span>
                </div>
                <div class="c-list02__item" data-aos="fade-up">
                    <span class="c-btn01 c-btn01--big c-btn01--radius u-fw--500">既存店舗の改装やリニューアルを考えているがどこから手を付けていいかわからない。</span>
                </div>
                <div class="c-list02__item" data-aos="fade-up">
                    <span class="c-btn01 c-btn01--big c-btn01--radius u-fw--500">限られたスペースで効果的なレイアウトをどうすれば実現できるか悩んでいる。</span>
                </div>
                <div class="c-list02__item" data-aos="fade-up">
                    <span class="c-btn01 c-btn01--big c-btn01--radius u-fw--500">照明の明るさや空調環境にムラがあり、作業効率の低下や来客時の印象悪化につながっている。</span>
                </div>
                <div class="c-list02__item" data-aos="fade-up">
                    <span class="c-btn01 c-btn01--big c-btn01--radius u-fw--500">セキュリティ設備が老朽化しており、どこまで改修すべきか判断できない。</span>
                </div>
            </div>
        </div>
    </section>
    <section class="p-business07">
        <div class="p-business07__inner">
            <h2 class="c-ttl32" data-aos="fade-up">近畿トータルサービスが<br class="sp">できること</h2>
            <div class="c-listsolution">
                <div class="c-listsolution__item" data-aos="fade-up">
                    <div class="c-listsolution__inner">
                        <figure class="c-listsolution__img">
                            <span class="c-label01">
                                <span class="c-label01__en">SOLUTION</span>
                                <span class="c-label01__num">01</span>
                            </span>
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/business/img_business_09.webp" alt="木の温もりを感じるカフェ風の休憩スペース">
                        </figure>
                        <div class="c-listsolution__content">
                            <h3 class="c-ttl28">店舗改装・内装工事</h3>
                            <p class="c-txt16">店舗コンセプトや動線設計の見直し、イメージ刷新、老朽化対策など、多様な店舗改装ニーズに対応します。<br>
															営業しながらの段階的改装工事にも対応可能で、状況に合わせた柔軟な工法・工程をご提案します。。</p>
                        </div>
                    </div>
                </div>
                <div class="c-listsolution__item" data-aos="fade-up">
                    <div class="c-listsolution__inner">
                        <figure class="c-listsolution__img">
                            <span class="c-label01">
                                <span class="c-label01__en">SOLUTION</span>
                                <span class="c-label01__num">02</span>
                            </span>
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/business/img_business_10.webp" alt="オープンなオフィス環境でリラックスして打ち合わせをする社員たち">
                        </figure>
                        <div class="c-listsolution__content">
                            <h3 class="c-ttl28">オフィスレイアウト工事</h3>
                            <p class="c-txt16">働き方に合わせたレイアウト最適化やゾーニング変更、フリーアドレス導入、会議室増設など対応します。<br>
															現場調査を踏まえ、業務効率・生産性向上・コミニュケーション改善につながる空間を構築します。</p>
                        </div>
                    </div>
                </div>
                <div class="c-listsolution__item" data-aos="fade-up">
                    <div class="c-listsolution__inner">
                        <figure class="c-listsolution__img">
                            <span class="c-label01">
                                <span class="c-label01__en">SOLUTION</span>
                                <span class="c-label01__num">03</span>
                            </span>
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/business/img_business_11.webp" alt="建設現場に配置された排水・給水の配管設備">
                        </figure>
                        <div class="c-listsolution__content">
                            <h3 class="c-ttl28">設備・給排水工事</h3>
                            <p class="c-txt16">空調・電気・ネットワーク・給排水設備の更新・増設に対応します。<br>既存テナント内での制約にも柔軟に対応し、店舗・オフィスの維持管理向上、安全性確保に貢献します。</p>
                        </div>
                    </div>
                </div>
                <div class="c-listsolution__item" data-aos="fade-up">
                    <div class="c-listsolution__inner">
                        <figure class="c-listsolution__img">
                            <span class="c-label01">
                                <span class="c-label01__en">SOLUTION</span>
                                <span class="c-label01__num">04</span>
                            </span>
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/business/img_business_12.webp" alt="吹き抜け構造の開放的なオフィス内観">
                        </figure>
                        <div class="c-listsolution__content">
                            <h3 class="c-ttl28">労働環境・快適性改善工事</h3>
                            <p class="c-txt16">採光調整、照明改善、空調最適化、動線改善、遮音対策など、働きやすさ向上を目的とした改修工事に対応します。<br>
															現場環境の改善とともに、生産性向上や従業員満足度向上につながる環境整備を実現します。</p>
                        </div>
                    </div>
                </div>
                <div class="c-listsolution__item" data-aos="fade-up">
                    <div class="c-listsolution__inner">
                        <figure class="c-listsolution__img">
                            <span class="c-label01">
                                <span class="c-label01__en">SOLUTION</span>
                                <span class="c-label01__num">05</span>
                            </span>
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/business/img_business_13.webp" alt="天井に設置された最新のドーム型ネットワークカメラ">
                        </figure>
                        <div class="c-listsolution__content">
                            <h3 class="c-ttl28">セキュリティ・防災対策工事</h3>
                            <p class="c-txt16">入退室管理、防犯カメラ、セキュリティガラス、火災報知設備などの防犯・防災対策工事に対応します。<br>
															テナント特性や利用環境に合わせ、安心安全な店舗・オフィスづくりをサポートします。</p>
                        </div>
                    </div>
                </div>
                <div class="c-listsolution__item" data-aos="fade-up">
                    <div class="c-listsolution__inner">
                        <figure class="c-listsolution__img">
                            <span class="c-label01">
                                <span class="c-label01__en">SOLUTION</span>
                                <span class="c-label01__num">06</span>
                            </span>
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/business/img_business_14.webp" alt="明るく広々とした自動車販売店のショールーム">
                        </figure>
                        <div class="c-listsolution__content">
                            <h3 class="c-ttl28">特殊用途空間工事</h3>
                            <p class="c-txt16">ショールーム・スタジオ・バックヤード・カウンセリングルームなど、特殊用途空間にも対応します。<br>
															用途に合わせた音響・照明・什器・設備計画まで一貫して提案し、高い実用性とデザイン性を実現します。</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <section class="p-business__works p-business08">
        <?php 
            $args_works02 = array(
                'post_type' => 'works', 
                'posts_per_page' => 6,
                'post_status'    => 'publish',
                'orderby'        => 'date',
                'order'          => 'DESC',
                'tax_query' => array(
                    array(
                        'taxonomy' => 'works-cate',
                        'field'    => 'slug',
                        'terms'    => array('店舗・オフィス改装'),
                    ),
                ),
            );      
            $query_works02 = new WP_Query($args_works02);
        ?>
        <div class="p-business__works__inner">
            <div class="p-business__works__head">
                <h2 class="c-title" data-aos="fade-up">
                    <span class="c-title__jp">施工事例</span>
                    <span class="c-title__en">WORKS</span>
                </h2>
                <div class="p-business__works__btn" data-aos="fade-up">
                    <a href="<?php echo esc_url(home_url('/works/category/店舗・オフィス改装/')); ?>" class="c-more01">
                        <span class="c-more01__content">LEARN MORE</span>
                        <svg class="c-more01__icon" xmlns="http://www.w3.org/2000/svg" width="25.506" height="5.901" viewBox="0 0 25.506 5.901">
                            <path d="M3097,1756.586h24l-6.7-5" transform="translate(-3097.004 -1751.185)" fill="none" stroke="#afaead" stroke-width="1"/>
                        </svg>
                    </a>
                </div>
            </div>
            <?php if ($query_works02->have_posts()) : ?>
            <div class="p-business__works__content c-slider01-wrap" data-aos="fade-up">
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
                <div class="p-business__works__slider">
                    <div class="c-slider01">
                        <?php while ($query_works02->have_posts()) : $query_works02->the_post(); $cate = get_the_terms(get_the_ID(), 'works-cate');?>
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
    <section class="p-business09">
        <div class="c-box" id="anchor_business_03">
            <figure class="c-box__img">
                <img data-aos="fade-up" src="<?php echo get_template_directory_uri(); ?>/assets/images/business/img_business_15.webp" alt="林純薬工業のロゴが入ったスタイリッシュな自立看板があるビルのエントランス。ガラス張りの外壁と石畳の地面">
            </figure>
            <div class="c-box__content">
                <h2 class="c-title c-title--line" data-aos="fade-up">
                    <span class="c-title__jp">テント・サイン<span class="c-title__small">（看板）</span>事業</span>
                    <span class="c-title__en">TENT & SIGNBORD PRODUCTION</span>
                </h2>
                <h3 class="c-ttl28" data-aos="fade-up">ニーズに合わせた<br class="pc">オリジナルテント&amp;看板製作。</h3>
                <h4 class="c-ttl20" data-aos="fade-up">古い 見えない 伝わらない。<br>まとめて解決する安心の看板・テント施工</h4>
            </div>
        </div>
    </section>
    <section class="p-business10">
        <div class="l-container">
            <h2 class="c-ttl28" data-aos="fade-up">このようなお悩みを近畿トータルサービスは解決します。</h2>
            <div class="c-list02 c-list02--cl2">
                <div class="c-list02__item" data-aos="fade-up">
                    <span class="c-btn01 c-btn01--left c-btn01--radius u-fw--500">看板が古くなり、店の印象が悪く見えてしまう。</span>
                </div>
                <div class="c-list02__item" data-aos="fade-up">
                    <span class="c-btn01 c-btn01--left c-btn01--radius u-fw--500">テントや外装が劣化して、安全面が心配。</span>
                </div>
                <div class="c-list02__item" data-aos="fade-up">
                    <span class="c-btn01 c-btn01--left c-btn01--radius u-fw--500">冷暖房をつけているが、入口付近が快適にならない。</span>
                </div>
                <div class="c-list02__item" data-aos="fade-up">
                    <span class="c-btn01 c-btn01--left c-btn01--radius u-fw--500">夜になると看板が目立たない。</span>
                </div>
                <div class="c-list02__item" data-aos="fade-up">
                    <span class="c-btn01 c-btn01--left c-btn01--radius u-fw--500">看板が古くて色あせている・壊れている。</span>
                </div>
            </div>
        </div>
    </section>
    <section class="p-business11">
        <div class="p-business11__inner">
            <h2 class="c-ttl32" data-aos="fade-up">近畿トータルサービスが<br class="sp">できること</h2>
            <div class="c-listsolution">
                <div class="c-listsolution__item" data-aos="fade-up">
                    <div class="c-listsolution__inner">
                        <figure class="c-listsolution__img">
                            <span class="c-label01">
                                <span class="c-label01__en">SOLUTION</span>
                                <span class="c-label01__num">01</span>
                            </span>
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/business/img_business_16.webp" alt="田中医院の看板。ピンクと青の聴診器と家のロゴマークが特徴">
                        </figure>
                        <div class="c-listsolution__content">
                            <h3 class="c-ttl28">サイン<span class="c-ttl28--small">（看板）</span>の新設・交換工事</h3>
                            <p class="c-txt16">壁面看板や突き出し看板・案内表示など、各種看板の新設・交換工事に対応しております。設置場所や建物条件、用途を確認したうえで老朽化による入替や表示内容変更に伴う更新工事もいたします。</p>
                        </div>
                    </div>
                </div>
                <div class="c-listsolution__item" data-aos="fade-up">
                    <div class="c-listsolution__inner">
                        <figure class="c-listsolution__img">
                            <span class="c-label01">
                                <span class="c-label01__en">SOLUTION</span>
                                <span class="c-label01__num">02</span>
                            </span>
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/business/img_business_17.webp" alt="屋外に設置されたベージュの大型サンシェード（オーニング）">
                        </figure>
                        <div class="c-listsolution__content">
                            <h3 class="c-ttl28">各種テントの新設・張替え</h3>
													<p class="c-txt16">固定式テント・開閉式テント・商業用テント・オーニングなど幅広く対応いたします。<br>新設はもちろん、老朽化による張替工事や仕様変更に伴う入替工事までワンストップで実施いたします。</p>
                        </div>
                    </div>
                </div>
                <div class="c-listsolution__item" data-aos="fade-up">
                    <div class="c-listsolution__inner">
                        <figure class="c-listsolution__img">
                            <span class="c-label01">
                                <span class="c-label01__en">SOLUTION</span>
                                <span class="c-label01__num">03</span>
                            </span>
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/business/img_business_18.webp" alt="「BISTRO Akiji」のネオン看板。緑色の鮮やかなライトアップ">
                        </figure>
                        <div class="c-listsolution__content">
                            <h3 class="c-ttl28">オーダーメイド設計・製作</h3>
                            <p class="c-txt16">既製品では対応できないサイズや形状にも、オーダーメイドで対応可能です。<br>ロゴ・文字配置・素材などニーズに合わせて最適なご提案をいたします。</p>
                        </div>
                    </div>
                </div>
                <div class="c-listsolution__item" data-aos="fade-up">
                    <div class="c-listsolution__inner">
                        <figure class="c-listsolution__img">
                            <span class="c-label01">
                                <span class="c-label01__en">SOLUTION</span>
                                <span class="c-label01__num">04</span>
                            </span>
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/business/img_business_19.webp" alt="林純薬工業の自立型看板。白く光るロゴと社名が都会の街並みに映える">
                        </figure>
                        <div class="c-listsolution__content">
                            <h3 class="c-ttl28">安全性・耐久性を考慮した<br class="pc">施工・取り付け</h3>
                            <p class="c-txt16">設置場所の強度や風圧、使用環境を考慮し、安全性・耐久性を重視した施工を行います。<br>無理な施工はせず、”長く使える”を前提に工事をご提案します。</p>
                        </div>
                    </div>
                </div>
                <div class="c-listsolution__item" data-aos="fade-up">
                    <div class="c-listsolution__inner">
                        <figure class="c-listsolution__img">
                            <span class="c-label01">
                                <span class="c-label01__en">SOLUTION</span>
                                <span class="c-label01__num">05</span>
                            </span>
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/business/img_business_20.webp" alt="木製フレームの案内板。1階の会議室と2階の社長室・役員室の場所を示している">
                        </figure>
                        <div class="c-listsolution__content">
                            <h3 class="c-ttl28">環境にも配慮した機能性素材の<br class="pc">ご提案</h3>
                            <p class="c-txt16">日差しや風雨などの自然環境にも配慮し、UVカットや防災、遮熱効果のある素材を活用することで、室内環境の快適性向上や省エネルギーにも貢献できる施工を心がけています。</p>
                        </div>
                    </div>
                </div>
                <div class="c-listsolution__item" data-aos="fade-up">
                    <div class="c-listsolution__inner">
                        <figure class="c-listsolution__img">
                            <span class="c-label01">
                                <span class="c-label01__en">SOLUTION</span>
                                <span class="c-label01__num">06</span>
                            </span>
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/business/img_business_21.webp" alt="通りに置かれた黒いスタンド看板（A型看板）。背景には歩行者がぼやけて写っている">
                        </figure>
                        <div class="c-listsolution__content">
                            <h3 class="c-ttl28">既存設備の撤去・処分も対応</h3>
                            <p class="c-txt16">古くなった看板やテントの撤去、処分までまとめて対応可能です。<br>高所作業の撤去にも安全管理を徹底したうえで作業を行います。</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <section class="p-business__works p-business12">
        <?php 
            $args_works03 = array(
                'post_type' => 'works', 
                'posts_per_page' => 6,
                'post_status'    => 'publish',
                'orderby'        => 'date',
                'order'          => 'DESC',
                'tax_query' => array(
                    array(
                        'taxonomy' => 'works-cate',
                        'field'    => 'slug',
                        'terms'    => array('テント・サイン（看板）事業'),
                    ),
                ),
            );      
            $query_works03 = new WP_Query($args_works03);
        ?>
        <div class="p-business__works__inner">
            <div class="p-business__works__head">
                <h2 class="c-title" data-aos="fade-up">
                    <span class="c-title__jp">施工事例</span>
                    <span class="c-title__en">WORKS</span>
                </h2>
                <div class="p-business__works__btn" data-aos="fade-up">
                    <a href="<?php echo esc_url(home_url('/works/category/テント・サイン（看板）事業/')); ?>" class="c-more01">
                        <span class="c-more01__content">LEARN MORE</span>
                        <svg class="c-more01__icon" xmlns="http://www.w3.org/2000/svg" width="25.506" height="5.901" viewBox="0 0 25.506 5.901">
                            <path d="M3097,1756.586h24l-6.7-5" transform="translate(-3097.004 -1751.185)" fill="none" stroke="#afaead" stroke-width="1"/>
                        </svg>
                    </a>
                </div>
            </div>
            <?php if ($query_works03->have_posts()) : ?>
            <div class="p-business__works__content c-slider01-wrap" data-aos="fade-up">
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
                <div class="p-business__works__slider">
                    <div class="c-slider01">
                        <?php while ($query_works03->have_posts()) : $query_works03->the_post(); $cate = get_the_terms(get_the_ID(), 'works-cate');?>
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
</main>
<?php
get_footer();
?>
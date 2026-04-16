<?php
get_header();
?>
<main class="p-recruit">
    <?php get_template_part('includes/template', 'bread'); ?>
    <section class="c-mv c-mv02">
        <div class="c-mv__inner">
            <div class="c-mv__content">
                <h1 class="c-mv__title">
                    <span class="c-mv__title__jp"><?php echo esc_html(get_the_title()) ?></span>
                    <?php if (get_the_excerpt()): ?>
                        <span class="c-mv__title__en"><?php echo esc_html(get_the_excerpt()) ?></span>
                    <?php endif; ?>
                </h1>
            </div>
            <figure class="c-mv__img">
                <?php echo has_post_thumbnail() ? get_the_post_thumbnail(get_the_ID(), 'large') : '<img src="' . esc_url(get_template_directory_uri() . '/assets/images/common/img_dummy_01.webp') . '" alt="' . esc_attr(get_the_title()) . '">'; ?>
            </figure>
        </div>
    </section>
    <section class="p-recruit__message">
        <div class="l-container">
            <div class="p-recruit__message01">
                <figure class="p-recruit__message01__img" data-aos="fade-up">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/recruit/img_message01.webp" alt="笑顔で談笑しながら屋外を歩くスーツ姿の若手ビジネスパーソン男女4人">
                </figure>
                <div class="p-recruit__message01__content">
                    <h2 class="c-title" data-aos="fade-up">
                        <span class="c-title__jp">人事担当者からのメッセージ</span>
                        <span class="c-title__en">MESSAGE</span>
                    </h2>
                    <h3 class="c-ttl28" data-aos="fade-up">
                        私たちが大切にしているのは、<br>“技術”だけではありません。
                    </h3>
                </div>
            </div>
            <div class="p-recruit__message02">
                <figure class="p-recruit__message02__img" data-aos="fade-up">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/recruit/img_message02.webp" alt="オフィスでファイルを持ち、カメラに向かって微笑むスーツ姿の女性社員">
                </figure>
                <div class="p-recruit__message02__content" data-aos="fade-up">
                    <p>
                        お客様の不安に寄り添い、快適に安心してご利用いただける空間をつくるためには、現場で働く一人ひとりの“人柄”や“姿勢”が欠かせません。<br>
                        <br>
                        近畿トータルサービスは、建設業としての専門性を活かしながら、地域に根ざした誠実な仕事を続けてきました。<br>
                        未経験でも、経験者でも、私たちの想いに共感してくださる方と一緒に働けることを楽しみにしています。<br>
                        “誰かの役に立ちたい”という気持ちがある方、ぜひ一度お話ししましょう。
                    </p>
                </div>
            </div>
        </div>
    </section>
    <section class="p-recruit__personal">
        <div class="l-container">
            <h2 class="c-title" data-aos="fade-up">
                <span class="c-title__jp">近畿トータルサービスが求める“人”</span>
                <span class="c-title__en">PERSONALITY</span>
            </h2>
            <ul class="p-recruit__personal__list">
                <li class="item" data-aos="fade-up">
                    <figure class="item__img">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/recruit/img_personality01.webp" alt="チームワークを象徴する、円になって手を重ね合わせるビジネスパーソンたちの手元">
                    </figure>
                    <div class="item__content">
                        <div class="item__box">
                            <span class="item__box__text">PERSONALITY</span>
                            <span class="item__box__num">01</span>
                        </div>
                        <h3 class="c-ttl24">時代を切り拓く<br>チャレンジ精神</h3>
                        <p>建設業界は、これから新しい形へと進化していきます。現状維持にとどまらず、自ら挑戦し続ける人を求めています。</p>
                    </div>
                </li>
                <li class="item" data-aos="fade-up">
                    <figure class="item__img">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/recruit/img_personality02.webp" alt="カフェのようなカジュアルな空間でパソコン画面を見ながら相談する男女3人の社員">
                    </figure>
                    <div class="item__content">
                        <div class="item__box">
                            <span class="item__box__text">PERSONALITY</span>
                            <span class="item__box__num">02</span>
                        </div>
                        <h3 class="c-ttl24">やっぱり<br>仕事は明朗快活に</h3>
                        <p>明るく元気な対応で、お客様や仲間を笑顔にできる人を求めています。</p>
                    </div>
                </li>
                <li class="item" data-aos="fade-up">
                    <figure class="item__img">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/recruit/img_personality03.webp" alt="資料を手に、相手の話を笑顔で聞くスーツ姿の男性ビジネスパーソン">
                    </figure>
                    <div class="item__content">
                        <div class="item__box">
                            <span class="item__box__text">PERSONALITY</span>
                            <span class="item__box__num">03</span>
                        </div>
                        <h3 class="c-ttl24">人と人を繋ぐ<br>コミュニケーション</h3>
                        <p>相手の声に耳を傾け、誠実に応えることで信頼関係を築くことができる人を私たちは求めています。</p>
                    </div>
                </li>
            </ul>
        </div>
    </section>
    <section class="p-recruit__benefits">
        <figure class="p-recruit__benefits__mv" data-aos="fade-up">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/recruit/img_benefits_mv.webp" alt="木目調のテーブルとモダンな椅子が並ぶ、開放的で落ち着いた雰囲気のオフィス休憩スペース・ラウンジ">
        </figure>
        <div class="l-container">
            <h2 class="c-title" data-aos="fade-up">
                <span class="c-title__jp">福利厚生・制度</span>
                <span class="c-title__en">BEPLOYEE BENEFITS</span>
            </h2>
            <p class="p-recruit__benefits__lead" data-aos="fade-up">
                従業員たちが最大限のパフォーマンスを発揮するため働く環境も自らの手で創り上げていきます。<br>1人ひとりの想いをカタチにするフィールドが、近畿トータルサービスにはあります。
            </p>
            <ul class="p-recruit__benefits__list">
                <li class="item" data-aos="fade-up">
                    <figure class="item__img">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/recruit/img_benefits_photo01.webp" alt="「福利厚生」と書かれた赤いハートの周りに並ぶ、人々を象徴 wooden block のアイコン">
                    </figure>
                    <div class="item__content">
                        <h3 class="c-ttl20">各種手当</h3>
                        <!-- p>通勤手当、役職手当、家族手当、住宅手当などが支給されます。雇用保険、労災保険、健康保険、厚生年金など、各種社会保険が完備されています。</p -->
                    </div>
                </li>
                <li class="item" data-aos="fade-up">
                    <figure class="item__img">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/recruit/img_benefits_photo02.webp" alt="休日を家族で楽しみ、笑顔で街を歩く若い夫婦と小さな女の子">
                    </figure>
                    <div class="item__content">
                        <h3 class="c-ttl20">休日・休暇制度</h3>
                        <!-- p>週休2日制で、土・日・祝日が基本の休日です。ただし、月に1回、平日に休みがあり、その代わりに土曜日に半日出勤があります。</p -->
                    </div>
                </li>
                <li class="item" data-aos="fade-up">
                    <figure class="item__img">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/recruit/img_benefits_photo03.webp" alt="「寿」や「御祝」の文字が書かれた、華やかな水引飾りのある伝統的な日本の祝儀袋">
                    </figure>
                    <div class="item__content">
                        <h3 class="c-ttl20">慶弔見舞金制度</h3>
                        <!-- p>従業員の慶事や弔事にお祝い金や弔慰金・見舞金などの支給を行う制度です。</p -->
                    </div>
                </li>
                <li class="item" data-aos="fade-up">
                    <figure class="item__img">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/recruit/img_benefits_photo04.webp" alt="ノートパソコンの横で、ピンクのシャープペンシルを使いノートにメモを取るデスクワークの様子">
                    </figure>
                    <div class="item__content">
                        <h3 class="c-ttl20">資格取得支援制度</h3>
                        <!-- p>業務に必要な資格を取得するために、受験費用や学習費用を会社がサポートする制度です。</p -->
                    </div>
                </li>
            </ul>
        </div>
    </section>
    <section class="p-recruit__info">
        <div class="l-container">
            <div class="p-recruit__info__inner">
                <h2 class="c-title" data-aos="fade-up">
                    <span class="c-title__jp">募集要項</span>
                    <span class="c-title__en">RECRUITMENTS</span>
                </h2>
                <?php 
                    $args_tab = array(
                        'post_type'      => 'recruit_tab',
                        'posts_per_page' => -1,
                        'post_status'    => 'publish',
                        'orderby'        => 'menu_order',
                        'order'          => 'ASC',
                    );      
                    $query_tab = new WP_Query($args_tab);
                    $tabs_data = [];
                    if ($query_tab->have_posts()) :
                        while ($query_tab->have_posts()) : $query_tab->the_post();

                            $tabs_data[] = [
                                'id'             => get_the_ID(),
                                'title'          => get_the_title(),
                                'jobtype'        => get_field('recruit_tab_jobtype'),
                                'qualifications' => get_field('recruit_tab_qualifications'),
                                'worktime'       => get_field('recruit_tab_worktime'),
                                'worklocation'   => get_field('recruit_tab_worklocation'),
                                'daysoff'        => get_field('recruit_tab_daysoff'),
                                'salary'         => get_field('recruit_tab_salary'),
                                'benefits'       => get_field('recruit_tab_benefits'),
                                'howtoapply'     => get_field('recruit_tab_howtoapply'),
                            ];

                        endwhile;
                    endif;

                    wp_reset_postdata();
                ?>
                <div class="p-recruit__info__board js-taps" data-aos="fade-up">
                    <?php if (!empty($tabs_data)) : ?>

                    <div class="p-recruit__info__tabs" data-aos="fade-up">
                        <?php foreach ($tabs_data as $index => $tab) : ?>
                        <button type="button" class="p-recruit__info__tab js-tab <?php echo ($index === 0) ? 'is-active' : ''; ?>" data-id="tab_content_<?php echo esc_attr($tab['id']); ?>"><?php echo esc_html($tab['title']); ?></button>
                        <?php endforeach; ?>
                    </div>

                    <div class="p-recruit__info__content">
                        <?php foreach ($tabs_data as $index => $tab) : ?>
                        <ul class="p-recruit__info__list js-tab-panel <?php echo ($index === 0) ? 'is-active' : ''; ?>" id="tab_content_<?php echo esc_attr($tab['id']); ?>">
                            <li class="c-tbl">
                                <?php
                                $fields = [
                                    '募集職種' => $tab['title'],
                                    '雇用形態' => $tab['jobtype'],
                                    '応募資格' => $tab['qualifications'],
                                    '就業時間' => $tab['worktime'],
                                    '就業場所' => $tab['worklocation'],
                                    '休日'     => $tab['daysoff'],
                                    '給与'     => $tab['salary'],
                                    '福利厚生' => $tab['benefits'],
                                    '応募方法' => $tab['howtoapply'],
                                ];

                                foreach ($fields as $label => $value) :
                                    if (!$value) continue;
                                ?>
                                <dl class="c-tbl__item" data-aos="fade-up">
                                    <dt class="c-tbl__ttl"><?php echo esc_html($label); ?></dt>
                                    <dd class="c-tbl__content"><?php echo wp_kses_post($value); ?></dd>
                                </dl>
                                <?php endforeach; ?>
                                
                            </li>
                        </ul>
                        <?php endforeach; ?>
                    </div>

                    <?php else: ?>
                        <p class="c-txt16" data-aos="fade-up">現在、募集はしておりません。</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
    <?php if ($query_tab->have_posts()) : ?>
    <section class="p-recruit__entry">
        <figure class="p-recruit__entry__img" data-aos="fade-up">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/recruit/img_entry.webp" alt="フィスで笑顔で両手を上げて喜ぶ、活気に満ちた男女5人の若手社員チーム">
        </figure>
        <div class="l-container">
            <div class="p-recruit__entry__inner" data-aos="fade-up">
                <h2 class="c-ttl36" id="form_entry">人を大切にする会社で、あなたの才能を発揮しよう。</h2>
                <h3 class="c-title" data-aos="fade-up">
                    <span class="c-title__jp">採用エントリーフォーム</span>
                    <span class="c-title__en">ENTRY</span>
                </h3>
                <div class="c-form01" data-aos="fade-up">
                    <?php 
                        if (have_posts()) : while (have_posts()) : the_post(); 
                            the_content(); 
                        endwhile; endif; 
                    ?> 
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>
</main>
<?php
get_footer();
?>
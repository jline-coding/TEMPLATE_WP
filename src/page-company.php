<?php
get_header();
?>
<main class="p-company">
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
    <section class="p-company__greetings" id="ttl_01">
        <div class="p-company__greetings__inner">
            <figure class="p-company__greetings__img">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/company/img_company_01.webp" alt="デスクに座り、穏やかな表情で微笑む代表者のポートレート">
            </figure>
            <div class="p-company__greetings__content">
                <h2 class="c-title" data-aos="fade-up">
                    <span class="c-title__jp">代表挨拶</span>
                    <span class="c-title__en">GREETINGS</span>
                </h2>
                <h3 class="c-ttl28" data-aos="fade-up">人と人への感謝の気持ち<br>最高の「ありがとう」のために<br>私たち近畿トータルサービスは努力を惜しみません。</h3>
                <p class="c-txt16" data-aos="fade-up">当社は2006年に創業し、2014年に法人化しました。<br>創業当初は年間140件のリフォーム工事から始まり、現場一つひとつに向き合いながら実績を積み重ねてきました。<br>現在は、店舗・工場建築を中心に、建築業としてさらに挑戦を続けています。<br><br>決して順調な道のりではありませんでした。<br>数えきれない困難を乗り越え、ここまで来られたのは、支えてくれた仲間や取引先、関わってくださったすべての方々のおかげです。<br><br>会社は人がいてこそ成り立つもの。<br>この想いを何より大切にし、時代の流れに柔軟に向き合いながら、社会と共存できる強い組織をつくっていきたいと考えています。</p>
                <p class="c-txt12" data-aos="fade-up">近畿トータルサービス株式会社<br>代表取締役</p>
                <figure class="p-company__greetings__name">
                    <img data-aos="fade-up" src="<?php echo get_template_directory_uri(); ?>/assets/images/company/img_company_name.svg" alt="仲里 清次">
                </figure>
                
            </div>
        </div>
    </section>
    <section class="p-company__philosophy" id="ttl_02">
        <div class="p-company__philosophy__main">
            <figure class="p-company__philosophy__icon" data-aos="fade-up">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/common/icon_01.svg" alt="">
            </figure>
            <div class="l-container">
                <h2 class="c-title" data-aos="fade-up">
                    <span class="c-title__jp">企業理念</span>
                    <span class="c-title__en">PHILOSOPHY</span>
                </h2>
                <h3 class="c-ttl48" data-aos="fade-up">誠実な仕事で、地域の安心と未来をつくる。</h3>
                <p class="c-txt16" data-aos="fade-up">私たち近畿トータルサービスは、社員一人ひとりの人間力を高め、豊かな生活を実現することを企業活動の原点としております。<br>そして、法人ユーザー様には、働く環境をより良くし、そこで働く方々の意欲や満足度を高める工事・サービスを提供してまいります。<br>また、社会的問題である地球環境を守る取組みとして、不要なものを廃棄せず、限りある貴重な資源として再生・活用する取り組みを推進します。<br>さらに、有効に使われていない土地を、人々の暮らしや地域の発展に貢献する価値ある空間へと生まれ変わらせる事業にも力を注いでまいります。<br><br>近畿トータルサービスは、社員・お客様・地域社会・地球環境がともに豊かになる未来の実現に向けて、挑戦と進化を続けてまいります。</p>
            </div>
        </div>
        <div class="c-slider04" data-aos="fade-up">
            <figure class="c-slider04__img">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/company/img_company_slider_01.webp" alt="「KINKI TOTAL SERVICE」の看板がある倉庫風のオフィス外観">
            </figure>
            <figure class="c-slider04__img img_even">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/company/img_company_slider_02.webp" alt="ガラス扉越しに見る広々とした会議室とリラックススペース">
            </figure>
            <figure class="c-slider04__img">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/company/img_company_slider_03.webp" alt="建材カタログや資料が整然と並ぶ木製の棚">
            </figure>
            <figure class="c-slider04__img img_even">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/company/img_company_slider_04.webp" alt="室内でアコースティックギターを弾くスーツ姿の男性">
            </figure>
            <figure class="c-slider04__img">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/company/img_company_slider_05.webp" alt="デスクとメッシュチェアが並ぶ清潔感のあるオフィス執務室">
            </figure>
            <figure class="c-slider04__img img_even">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/company/img_company_slider_06.webp" alt="ダーツやビリヤード台がある社員用のアミューズメントスペース">
            </figure>
            <figure class="c-slider04__img">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/company/img_company_slider_07.webp" alt="ヘルメットや工具が整理された倉庫内の作業・収納スペース">
            </figure>
            <figure class="c-slider04__img img_even">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/company/img_company_slider_08.webp" alt="オフィスの緑の中に置かれたリアルな亀の置物">
            </figure>
        </div>
    </section>
    <section class="p-company__outline" id="ttl_03">
        <div class="l-container">
            <div class="p-company__outline__inner">
                <h2 class="c-title" data-aos="fade-up">
                    <span class="c-title__jp">会社概要</span>
                    <span class="c-title__en">OUTLINE</span>
                </h2>
                <div class="p-company__outline__list">
                    <div class="p-company__outline__item">
                        <div class="c-tbl">
                            <div class="c-tbl__item" data-aos="fade-up">
                                <div class="c-tbl__ttl">社名（商号）</div>
                                <div class="c-tbl__content">近畿トータルサービス株式会社</div>
                            </div>
                            <div class="c-tbl__item" data-aos="fade-up">
                                <div class="c-tbl__ttl">本社</div>
                                <div class="c-tbl__content">
                                    〒559-0024<br>大阪府大阪市住之江区新北島3-6-1<br>
                                    <a href="https://maps.app.goo.gl/3E4TRZQ96DhREsKA9" class="c-map" target="_blank" rel="noopener noreferrer">
                                        <svg class="c-map__icon" xmlns="http://www.w3.org/2000/svg" width="10" height="13.877" viewBox="0 0 10 13.877">
                                            <circle id="Ellipse_2187" data-name="Ellipse 2187" cx="5" cy="5" r="5" fill="#37557d"/>
                                            <path d="M3112.793-3.5l5,8.981,5-8.981Z" transform="translate(-3112.793 8.396)" fill="#37557d"/>
                                            <circle cx="2" cy="2" r="2" transform="translate(3 3)" fill="#fff"/>
                                        </svg>
                                        <span class="c-map__content">Google MAP</span>
                                    </a>
                                    <div class="c-tbl__tel">
                                        <a href="tel:06-6686-1121" class="c-tel01">Tel. 06-6686-1121</a><br><span class="c-tel01">Fax. 06-6686-0202</span>
                                    </div>
                                </div>
                            </div>
                            <div class="c-tbl__item" data-aos="fade-up">
                                <div class="c-tbl__ttl">設立</div>
                                <div class="c-tbl__content">平成18年1月（個人創業）<br>平成26年2月（法人に改組）</div>
                            </div>
                            <div class="c-tbl__item" data-aos="fade-up">
                                <div class="c-tbl__ttl">代表取締役</div>
                                <div class="c-tbl__content">仲里 淸次</div>
                            </div>
                            <div class="c-tbl__item" data-aos="fade-up">
                                <div class="c-tbl__ttl">資本金</div>
                                <div class="c-tbl__content">20,000,000円</div>
                            </div>
                            <div class="c-tbl__item" data-aos="fade-up">
                                <div class="c-tbl__ttl">従業員数</div>
                                <div class="c-tbl__content">7名</div>
                            </div>
                            <div class="c-tbl__item" data-aos="fade-up">
                                <div class="c-tbl__ttl">資格取得者</div>
                                <div class="c-tbl__content">一級建築施工管理技士：2名<br>二級建築士：1名</div>
                            </div>
                            <div class="c-tbl__item" data-aos="fade-up">
                                <div class="c-tbl__ttl">加盟/登録団体</div>
                                <div class="c-tbl__content">一般社団法人大阪市産業経営協会<br>産業人クラブ（日刊工業新聞会）</div>
                            </div>
                        </div>
                    </div>
                    <div class="p-company__outline__item">
                        <div class="c-tbl">
                            <div class="c-tbl__item" data-aos="fade-up">
                                <div class="c-tbl__ttl">事業内容</div>
                                <div class="c-tbl__content">
                                    <ul class="c-listdot">
                                        <li class="c-listdot__item">工場営繕</li>
                                        <li class="c-listdot__item">屋根・外壁・外構工事</li>
                                        <li class="c-listdot__item">エクステリア工事</li>
                                        <li class="c-listdot__item">店舗改装工事</li>
                                        <li class="c-listdot__item">土木工事・総合建設</li>
                                        <li class="c-listdot__item">各種設計・施行</li>
                                        <li class="c-listdot__item">工場機械設備</li>
                                        <li class="c-listdot__item">機械設計</li>
                                        <li class="c-listdot__item">省エネ設備</li>
                                        <li class="c-listdot__item">テント入替・張替工事</li>
                                        <li class="c-listdot__item">サイン（看板）設置・交換工事</li>
                                        <li class="c-listdot__item">各種販売</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="c-tbl__item" data-aos="fade-up">
                                <div class="c-tbl__ttl">建設業許可</div>
                                <div class="c-tbl__content">
                                    大阪府知事許可（特-7）第141078号
                                    <p class="c-txt12">とび・土木工事業/鋼構造物工事業/板金工事業/塗装工事業/熱絶縁工事業/左官工事業/石工事業/鉄筋工事業/ガラス工事業/防水工事業/建具工事業/解体工事業/屋外広告業</p>
                                    <ul class="c-liststar c-txt12">
                                        <li class="c-liststar__item">内装仕上工事業許可</li>
                                        <li class="c-liststar__item">建築/大工/屋根/タイル・れんか・ブロック工事業</li>
                                        <li class="c-liststar__item">土木/管/機械器具設置工事業</li>
                                    </ul>
                                    産業廃棄物収集運搬許可　許可番号02700177721
                                </div>
                            </div>
                            <div class="c-tbl__item" data-aos="fade-up">
                                <div class="c-tbl__ttl">取引銀行</div>
                                <div class="c-tbl__content">
                                    <ul class="c-listdot">
                                        <li class="c-listdot__item">大阪信用金庫 住之江支店</li>
                                        <li class="c-listdot__item">りそな銀行 平林支店</li>
                                        <li class="c-listdot__item">大阪シティ信用金庫　住吉支店</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="c-tbl__item" data-aos="fade-up">
                                <div class="c-tbl__note">
                                    <p class="c-txt12">※2026年4月現在</p> 
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-company__logo">
                <div class="p-company__logo__item" data-aos="fade-up">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/company/img_logo_01.webp" alt="SECURITY ACTION セキュリティ対策自己宣言">
                </div>
                <div class="p-company__logo__item" data-aos="fade-up">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/company/img_logo_02.webp" alt="健康経営優良法人">
                </div>
            </div>
        </div>
    </section>
</main>
<?php
get_footer();
?>
<?php
get_header();
?>
<main class="p-privacy">
    <section class="c-mv c-mv01">
        <?php get_template_part('includes/template', 'bread'); ?>
        <div class="c-mv__inner">
            <div class="c-mv__content">
                <h1 class="c-mv__title">
                    <span class="c-mv__title__jp"><?php echo esc_html(get_the_title()) ?></span>
                    <?php if(get_the_excerpt()): ?>
                    <span class="c-mv__title__en"><?php echo esc_html(get_the_excerpt()) ?></span>
                    <?php endif; ?>
                </h1>
            </div>
        </div>
    </section>
    <div class="p-privacy__content">
        <div class="l-container">
            <h2 class="c-ttl32" data-aos="fade-up">個人情報保護について</h2>
            <p data-aos="fade-up">
                近畿トータルサービス株式会社（以下、「当方」）は、各種サービスのご提供にあたり、お客様の個人情報をお預かりしております。<br>
                当方は、個人情報に関する法令を遵守し、個人情報の適切な取り扱いを実現致します。
            </p>
            <div class="p-privacy__block" data-aos="fade-up">
                <h3 class="c-ttl24">個人情報の取得について</h3>
                <p>当方は、偽りその他不正の手段によらず適正に個人情報を取得致します。</p>
            </div>
            <div class="p-privacy__block" data-aos="fade-up">
                <h3 class="c-ttl24">個人情報の利用について</h3>
                <p>
                    当方は、個人情報を以下の利用目的の達成に必要な範囲内で、利用致します。<br>
                    以下に定めのない目的で個人情報を利用する場合、あらかじめご本人の同意を得た上で行ないます。<br>
                    お問い合せ・お見積のご依頼に対する回答及び資料送付<br>
                    必要に応じてお客様に連絡を行なうため<br>
                    各種サービスに関する情報提供
                </p>
            </div>
            <div class="p-privacy__block" data-aos="fade-up">
                <h3 class="c-ttl24">個人情報の安全管理について</h3>
                <p>
                    当方は、取り扱う個人情報の漏洩、滅失またはき損の防止その他の個人情報の安全管理のために必要かつ適切な措置を講じます。
                </p>
            </div>
            <div class="p-privacy__block" data-aos="fade-up">
                <h3 class="c-ttl24">個人情報の委託について</h3>
                <p>
                    当方は、個人情報の取り扱いの全部または一部を第三者に委託する場合は、当該第三者について厳正な調査を行い、 取り扱いを委託された個人情報の安全管理が図られるよう当該第三者に対する必要かつ適切な監督を行います。
                </p>
            </div>
            <div class="p-privacy__block" data-aos="fade-up">
                <h3 class="c-ttl24">個人情報の第三者提供について</h3>
                <p>
                    当方は、個人情報保護法等の法令に定めのある場合を除き、個人情報をあらかじめご本人の同意を得ることなく、第三者に提供致しません。
                </p>
            </div>
            <div class="p-privacy__block" data-aos="fade-up">
                <h3 class="c-ttl24">個人情報の開示・訂正等について</h3>
                <p>
                    当方は、ご本人から自己の個人情報についての開示の請求がある場合、速やかに開示を致します。 その際、ご本人であることが確認できない場合には、開示に応じません。<br>
                    個人情報の内容に誤りがあり、ご本人から訂正・追加・削除の請求がある場合、調査の上、速やかにこれらの請求に対応致します。 その際、ご本人であることが確認できない場合には、これらの請求に応じません。<br>
                    当方の個人情報の取り扱いにつきまして、上記の請求・お問い合わせ等ございましたら、下記お問い合わせ先までご連絡くださいますようお願い申し上げます。
                </p>
            </div>
            <div class="p-privacy__block" data-aos="fade-up">
                <h3 class="c-ttl24">アクセス解析ツールについて</h3>
                <p>
                    当サイトでは、Googleによるアクセス解析ツール「Googleアナリティクス」を利用しています。<br>
                    このGoogleアナリティクスはアクセス情報の収集のためにCookieを使用しています。このアクセス情報は匿名で収集されており、個人を特定するものではありません。<br>
                    GoogleアナリティクスのCookieは、運営上合理的な期間が経過した後、消去いたします。<br>
                    Googleアナリティクスの利用規約に関して確認したい場合は、ここをクリックしてください。また、「ユーザーが Google パートナーのサイトやアプリを使用する際の Google によるデータ使用」に関して確認したい場合は、ここをクリックしてください。
                    
                </p>
            </div>
            <div class="p-privacy__block" id="ttl_policy">
                <h3 class="c-ttl24" data-aos="fade-up">Cookieによる個人情報の取得</h3>
                <p data-aos="fade-up">
                    当サイトは、訪問者のコンピュータにCookieを送信することがあります。<br>
                    Cookie（クッキー）とは、ウェブサイトを利用したときに、ブラウザとサーバーとの間で送受信した利用履歴や入力内容などを、訪問者のコンピュータにファイルとして保存しておく仕組みです。<br>
                    訪問者がブラウザの設定でCookieの送受信を許可している場合、ウェブサイトは、訪問者のブラウザからCookieキーを取得できます。<br>
                    なお、訪問者のブラウザはプライバシー保護のため、そのウェブサイトのサーバーが送受信したCookieのみを送信します。<br>
                    当サイトに残されたCookie は、運営上合理的な期間が経過した後、消去いたします。<br>
                    訪問者は、ブラウザの設定にてCookieの受け入れを拒否することもできますが、快適にご利用いただくために、Cookieを有効にしていただくことをお勧めいたします。<br>
                    <br>
                    設定方法は、ブラウザにより異なります。Cookieに関する設定方法は、お使いのブラウザの「ヘルプ」メニューでご確認ください。当サイトを利用することで、上記方法・目的においてGoogle等が行うこうしたデータ処理につき許可を与えたものとみなします。
                </p>
            </div>
            <div class="p-privacy__block" data-aos="fade-up">
                <h3 class="c-ttl24">本方針の変更</h3>
                <p>
                    本方針の内容は変更されることがあります。<br>
                    変更後の本方針については、当方が別途定める場合を除いて、当サイトに掲載した時から効力を生じるものとします。
                </p>
            </div>
            <div class="p-privacy__sign" data-aos="fade-up">
                <p class="p-privacy__sign__txt">近畿トータルサービス株式会社<br>代表取締役</p>
                <p class="p-privacy__sign__name">仲里 淸次</p>
            </div>
        </div>
    </div>
</main>
<?php
get_footer();
?>
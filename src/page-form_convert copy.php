<?php 
get_header();
?>
<!-- ↓↓ main ↓↓ -->
<main class="p-contact">
    <div class="p-contact__inner">
        <div class="l-container">
            <div class="c-form h-adr">
                <span class="p-country-name" style="display:none;">Japan</span>
                <div class="c-form__body">
                    <div class="c-form__item">
                        <div class="c-form__ttl">
                            <span class="c-form__head c-txt16 u-fw--500 wpf-title">氏名<span class="c-form__rq">必須</span></span>
                        </div>
                        <div class="c-form__content">
                            <input class="c-input" type="text" name="your-name" placeholder="ダミーテキストが入ります。" required="required">
                            <p class="c-form__txt14 c-txt14">※ダミーテキストが入ります。ダミーテキストが入ります。</p>
                        </div>
                    </div>
                    <div class="c-form__item">
                        <div class="c-form__ttl">
                            <span class="c-form__head c-txt16 u-fw--500 wpf-title">都道府県<span class="c-form__rq">必須</span></span>
                        </div>
                        <div class="c-form__content">
                            <div class="c-select">
                                <select name="your-select" required="required">
                                    <option value="" selected="selected">【ダミーテキストが入ります。】</option>
                                    <option value="ダミーテキストが入ります1。">ダミーテキストが入ります1。</option>
                                    <option value="ダミーテキストが入ります2。">ダミーテキストが入ります2。</option>
                                    <option value="ダミーテキストが入ります3。">ダミーテキストが入ります3。</option>
                                    <option value="ダミーテキストが入ります4。">ダミーテキストが入ります4。</option>
                                    <option value="ダミーテキストが入ります5。">ダミーテキストが入ります5。</option>
                                    <option value="ダミーテキストが入ります6。">ダミーテキストが入ります6。</option>
                                    <option value="ダミーテキストが入ります7。">ダミーテキストが入ります7。</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="c-form__item">
                        <div class="c-form__ttl">
                            <span class="c-form__head c-txt16 u-fw--500 wpf-title">フリガナ<span class="c-form__rq">必須</span></span>
                        </div>
                        <div class="c-form__content">
                            <input class="c-input" type="text" name="your-name-kana" placeholder="ダミーテキストが入ります。" required="required">
                        </div>
                    </div>
                    <div class="c-form__item">
                        <div class="c-form__ttl">
                            <span class="c-form__head c-txt16 u-fw--500 wpf-title">ダミーテキスト1<span class="c-form__rq">必須</span></span>
                        </div>
                        <div class="c-form__content">
                            <div class="c-form__checkboxs">
                                <div class="c-checkbox">
                                    <label><input type="checkbox" name="your-dummy-text1" value="ダミーテキスト1" required="required">ダミーテキスト1</label>
                                </div>
                                <div class="c-checkbox">
                                    <label><input type="checkbox" name="your-dummy-text1" value="ダミーテキスト2" required="required">ダミーテキスト2</label>
                                </div>
                                <div class="c-checkbox">
                                    <label><input type="checkbox" name="your-dummy-text1" value="ダミーテキスト3" required="required">ダミーテキスト3</label>
                                </div>
                                <div class="c-checkbox">
                                    <label><input type="checkbox" name="your-dummy-text1" value="ダミーテキスト4" required="required">ダミーテキスト4</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="c-form__item">
                        <div class="c-form__ttl">
                            <span class="c-form__head c-txt16 u-fw--500 wpf-title">ダミーテキスト2<span class="c-form__rq">必須</span></span>
                        </div>
                        <div class="c-form__content">
                            <div class="c-form__radios">
                                <div class="c-radio">
                                    <label><input type="radio" name="your-dummy-text2" value="ダミーテキスト1" required="required">ダミーテキスト1</label>
                                </div>
                                <div class="c-radio">
                                    <label><input type="radio" name="your-dummy-text2" value="ダミーテキスト2" required="required">ダミーテキスト2</label>
                                </div>
                                <div class="c-radio">
                                    <label><input type="radio" name="your-dummy-text2" value="ダミーテキスト3" required="required">ダミーテキスト3</label>
                                </div>
                                <div class="c-radio">
                                    <label><input type="radio" name="your-dummy-text2" value="ダミーテキスト4" required="required">ダミーテキスト4</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="c-form__item">
                        <div class="c-form__ttl">
                            <span class="c-form__head c-txt16 u-fw--500 wpf-title">生年月日</span>
                        </div>
                        <div class="c-form__content">
                            <input class="c-input js-datepicker" type="text" name="your-birthday" aria-invalid="false" placeholder="ダミーテキストが入ります。">
                        </div>
                    </div>
                    <div class="c-form__item">
                        <div class="c-form__ttl">
                            <span class="c-form__head c-txt16 u-fw--500 wpf-title">メールアドレス<span class="c-form__rq">必須</span></span>
                        </div>
                        <div class="c-form__content">
                            <input class="c-input" type="email" name="your-email" placeholder="ダミーテキストが入ります。" required="required">
                        </div>
                    </div>
                    <div class="c-form__item">
                        <div class="c-form__ttl">
                            <span class="c-form__head c-txt16 u-fw--500 wpf-title">メールアドレス（確認）<span class="c-form__rq">必須</span></span>
                        </div>
                        <div class="c-form__content">
                            <input class="c-input" type="your-email-re" name="confirm_email" placeholder="ダミーテキストが入ります。" required="required">
                        </div>
                    </div>
                    <div class="c-form__item">
                        <div class="c-form__ttl">
                            <span class="c-form__head c-txt16 u-fw--500 wpf-title">電話番号</span>
                        </div>
                        <div class="c-form__content">
                            <input class="c-input" type="tel" name="your-tel" placeholder="ダミーテキストが入ります。">
                        </div>
                    </div>
                    <div class="c-form__item">
                        <div class="c-form__ttl">
                            <span class="c-form__head c-txt16 u-fw--500 wpf-title">郵便番号<span class="c-form__rq">必須</span></span>
                            <p class="c-form__txt16 c-txt16">ダミーテキストが入ります。ダミーテキストが入ります。</p>
                        </div>
                        <div class="c-form__content">
                            <input class="c-input p-postal-code" type="text" name="your-zip" placeholder="記入例）0000000" required="required">
                            <p class="c-form__txt14 c-txt14">※ダミーテキストが入ります。ダミーテキストが入ります。</p>
                        </div>
                    </div>
                    <div class="c-form__item">
                        <div class="c-form__ttl">
                            <span class="c-form__head c-txt16 u-fw--500 wpf-title">住所<span class="c-form__rq">必須</span></span>
                        </div>
                        <div class="c-form__content">
                            <div class="c-form__child">
                                <input class="c-input p-region p-locality p-street-address" type="text" name="your-address" placeholder="ダミーテキストが入ります。" required="required">
                            </div>
                        </div>
                    </div>
                    <div class="c-form__item">
                        <div class="c-form__ttl">
                            <span class="c-form__head c-txt16 u-fw--500 wpf-title">番地・建物名</span>
                        </div>
                        <div class="c-form__content">
                            <div class="c-form__child">
                                <input class="c-input" type="text" name="your-building" placeholder="ダミーテキストが入ります。" >
                            </div>
                        </div>
                    </div>
                    <div class="c-form__item">
                        <div class="c-form__ttl">
                            <span class="c-form__head c-txt16 u-fw--500 wpf-title">備考</span>
                            <p class="c-form__txt16 c-txt16">ダミーテキストが入ります。ダミーテキストが入ります。</p>
                        </div>
                        <div class="c-form__content">
                            <textarea class="c-textarea" name="your-message" rows="60" cols="60"></textarea>
                        </div>
                    </div>
                    <div class="c-form__item">
                        <div class="c-form__ttl">
                            <span class="c-form__head c-txt16 u-fw--500 wpf-title">添付ファイル<span class="c-form__rq">必須</span></span>
                            <p class="c-form__txt16 c-txt16">ダミーテキストが入ります。ダミーテキストが入ります。</p>
                        </div>
                        <div class="c-form__content">
                            <div class="c-file js-file">
                                <label class="c-file__inner js-file__inner" for="file_01">
                                    <input id="file_01" type="file" name="your-file" accept=".jpg,.jpeg,.png,.gif,.pdf,.docx,.xlsx,.pptx" required="required">
                                    <span class="c-file__content js-file__content">添付する</span>
                                </label>
                                <span class="c-file__clear js-file-clear" style="display: none;">×</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="c-form-pravicy">
                    <h2 class="c-form-pravicy__title">ダミーテキストが入ります。ダミーテキストが入ります。</h2>
                    <div class="c-form-pravicy__inner js-mCustomScrollbar">
                        <div class="c-form-pravicy__item">
                            <p class="c-form-pravicy__txt16 c-txt16">ダミーテキストが入ります。ダミーテキストが入ります。ダミーテキストが入ります。ダミーテキストが入ります。ダミーテキストが入ります。ダミーテキストが入ります。ダミーテキストが入ります。ダミーテキストが入ります。ダミーテキストが入ります。ダミーテキストが入ります。</p>
                        </div>
                        <div class="c-form-pravicy__item">
                            <p class="c-form-pravicy__txt18 c-txt18 u-fw--500">1. ダミーテキストが入ります。ダミーテキストが入ります。</p>
                            <p class="c-form-box__txt16 c-txt16">ダミーテキストが入ります。ダミーテキストが入ります。ダミーテキストが入ります。ダミーテキストが入ります。ダミーテキストが入ります。ダミーテキストが入ります。</p>
                            <p class="c-form-box__txt16 c-txt16">ダミーテキストが入ります。ダミーテキストが入ります。ダミーテキストが入ります。ダミーテキストが入ります。ダミーテキストが入ります。ダミーテキストが入ります。ダミーテキストが入ります。ダミーテキストが入ります。ダミーテキストが入ります。ダミーテキストが入ります。</p>
                            <p class="c-form-box__txt16 c-txt16">ダミーテキストが入ります。ダミーテキストが入ります。ダミーテキストが入ります。ダミーテキストが入ります。ダミーテキストが入ります。ダミーテキストが入ります。</p>
                        </div>
                        <div class="c-form-pravicy__item">
                            <p class="c-form-pravicy__txt18 c-txt18 u-fw--500">2. ダミーテキストが入ります。ダミーテキストが入ります。</p>
                            <p class="c-form-box__txt16 c-txt16">ダミーテキストが入ります。ダミーテキストが入ります。ダミーテキストが入ります。ダミーテキストが入ります。ダミーテキストが入ります。ダミーテキストが入ります。</p>
                        </div>
                        <div class="c-form-pravicy__item">
                            <p class="c-form-pravicy__txt18 c-txt18 u-fw--500">3. ダミーテキストが入ります。ダミーテキストが入ります。</p>
                            <p class="c-form-box__txt16 c-txt16">ダミーテキストが入ります。ダミーテキストが入ります。ダミーテキストが入ります。ダミーテキストが入ります。</p>
                            <p class="c-form-box__txt16 c-txt16">ダミーテキストが入ります。ダミーテキストが入ります。ダミーテキストが入ります。ダミーテキストが入ります。ダミーテキストが入ります。ダミーテキストが入ります。</p>
                        </div>
                    </div>
                    <div class="c-form-verify">
                        <div class="c-form-verify__inner c-checkbox wpf-acceptance">
                            <label><input type="checkbox" required="required" name="your-consent" value="同意する">ダミーテキストが入ります。ダミーテキストが入ります。</label>
                        </div>
                    </div>
                </div>
                <div class="c-form__btns">
                    <button type="submit">入力内容を確認</button>
                </div>
            </div>
        </div>
    </div>
</main>
<!-- ↑↑ main ↑↑ -->
<?php
get_footer();
?>
=== Custom Snow Monkey Forms ===
Contributors: site-development-team
Tags: snow monkey forms, validation, conditional fields, mail routing, image upload
Requires at least: 6.6
Requires PHP: 7.4
Stable tag: 2.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Snow Monkey Forms にリアルタイム検証、条件分岐、送信先ルーティング、画像添付管理を追加します。

== Description ==

Snow Monkey Forms のコアファイルを変更せず、公式フックと REST API を利用して次の機能を追加します。

* 項目別のリアルタイム検証とサーバー側再検証
* AND / OR、15種類の演算子に対応した入力項目の表示条件
* 優先度、先勝ち／全一致、To / Cc / Bcc / Reply-To に対応した送信先条件
* 画像の拡張子、MIME、容量、縦横サイズ検証
* 管理者通知・自動返信への画像自動添付
* Snow Monkey Forms 配下の日本語管理画面
* ユーザーごとに切り替え可能な日本語・ベトナム語管理画面

送信先メールアドレスはフロントエンド設定 API へ含めません。ブラウザ検証を回避した送信もサーバー側で再検証します。

== Installation ==

1. Snow Monkey Forms をインストールして有効化します。
2. このプラグインを `wp-content/plugins/custom-snow-monkey-forms` に配置して有効化します。
3. 「Snow Monkey Forms → 拡張フォーム設定」を開きます。
4. 対象フォームを選び、必要なルールを登録します。

== Frequently Asked Questions ==

= Snow Monkey Forms 本体を変更しますか？ =

変更しません。公式のフィルターフックと DOM イベントを使用します。

= 画像をメール本文に `{file-name}` と書く必要がありますか？ =

「管理者通知へ自動添付」を有効にした画像項目は不要です。Snow Monkey Forms 標準方式もそのまま利用できます。

= 条件で非表示になった必須項目はどうなりますか？ =

ブラウザ側で無効化し、サーバー側でも必須検証から除外します。「非表示項目の値を破棄」が有効な場合はメールにも残りません。

== Changelog ==

= 2.1.0 =
* 管理画面に日本語／ベトナム語の言語切り替えを追加。
* 言語設定を WordPress ユーザーごとに保存。

= 2.0.0 =
* ルールエンジンと管理画面を全面刷新。
* 1.x のフォームメタ設定を自動読み込み。

= 1.0.0 =
* Initial release.

# Custom Snow Monkey Forms

Snow Monkey Forms 用の拡張プラグインです。コアを変更せず、フォーム単位の管理画面から以下を設定できます。

- リアルタイム + サーバーサイド検証
- 入力項目の条件分岐（AND / OR）
- 管理者メール送信先の条件分岐
- 画像ファイルの形式・容量・寸法検証と自動添付
- 管理者ごとに保存される日本語／ベトナム語の管理画面切り替え

## 対応方針

- Snow Monkey Forms 9.0 以降の REST 送信フローを前提とし、12.x / 13.x の公開 API と互換になるよう実装しています。
- WordPress 6.6 以上、PHP 7.4 以上を必須とします。
- JavaScript が無効、またはブラウザ側処理が改変された場合も、条件・値・画像を PHP で再検証します。
- 宛先ルールは公開 REST レスポンスから除外します。

## 利用手順

1. Snow Monkey Forms でフォームと各 field の `name` を作成します。
2. WordPress 管理画面の「Snow Monkey Forms → 拡張フォーム設定」を開きます。
3. 対象フォームを選択し、各タブでルールを追加します。
4. 保存後、実際の埋め込みページで入力・確認・戻る・送信をテストします。

管理画面右上の「管理画面の言語 / Ngôn ngữ quản trị」から `日本語` または `Tiếng Việt` を選択できます。選択内容は WordPress ユーザー単位で保存されるため、同じサイトを日本人・ベトナム人の管理者がそれぞれ自分の言語で利用できます。フォーム設定や送信データには影響しません。

条件ルールが複数同じ対象項目に設定された場合、すべてのルールが表示判定になった時だけ表示されます。送信先ルールの条件が一つもない場合は安全のため一致しません。常時使う宛先は Snow Monkey Forms 本体の管理者宛メール設定を利用してください。

## 主な拡張ポイント

本プラグイン自身の保存データは form post meta の `_csmf_config` に格納されます。外部コードから値を更新する場合も `CSMF_Config::save()` を通してスキーマを正規化してください。

Snow Monkey Forms との接続には以下を使用します。

- `snow_monkey_forms/control/attributes`
- `snow_monkey_forms/spam/validate`
- `snow_monkey_forms/administrator_mailer/args`
- `snow_monkey_forms/auto_reply_mailer/args`
- `smf.input`, `smf.back`, `smf.invalid`

独自要件は次の add-on filter で拡張できます。

- `csmf/config` — 正規化後のフォーム設定
- `csmf/frontend_config` — 公開してよいフロント設定（宛先は追加しないでください）
- `csmf/conditional/hidden_fields` — サーバー側の非表示項目判定
- `csmf/validation/errors` — サーバー検証エラー
- `csmf/mail/args` — 最終メール引数
- `csmf/mail/attachments` — 最終添付ファイル一覧

## セキュリティ

- 管理画面保存は nonce と `edit_post` capability を検証します。
- フロント設定 API は Snow Monkey Forms の form hash を検証します。
- メールアドレスは改行を除去し `sanitize_email()` / `is_email()` を通します。
- 画像は拡張子だけでなく、実ファイルの画像 MIME と `getimagesize()` の結果を確認します。
- 保存ファイルのパス生成は Snow Monkey Forms の `Directory` API に委譲します。

## 開発時チェック

```sh
node --check assets/js/frontend.js
node --check assets/js/admin.js
php -l custom-snow-monkey-forms.php
```

<?php
/**
 * Per-user Japanese/Vietnamese admin language support.
 *
 * @package CustomSnowMonkeyForms
 */

defined( 'ABSPATH' ) || exit;

final class CSMF_I18n {
	const USER_META_KEY = '_csmf_admin_language';

	/** @return string ja|vi */
	public static function get_language() {
		$user_id  = get_current_user_id();
		$language = $user_id ? get_user_meta( $user_id, self::USER_META_KEY, true ) : '';
		if ( in_array( $language, array( 'ja', 'vi' ), true ) ) {
			return $language;
		}
		$locale = function_exists( 'get_user_locale' ) ? get_user_locale( $user_id ) : get_locale();
		return 0 === strpos( strtolower( (string) $locale ), 'vi' ) ? 'vi' : 'ja';
	}

	/** @return bool */
	public static function set_language( $language, $user_id = 0 ) {
		$language = sanitize_key( $language );
		$user_id  = $user_id ? absint( $user_id ) : get_current_user_id();
		if ( ! $user_id || ! in_array( $language, array( 'ja', 'vi' ), true ) ) {
			return false;
		}
		return false !== update_user_meta( $user_id, self::USER_META_KEY, $language );
	}

	/** @return string */
	public static function t( $key, $fallback = '' ) {
		$strings = self::strings( self::get_language() );
		return isset( $strings[ $key ] ) ? $strings[ $key ] : $fallback;
	}

	/** @return array<string,string> */
	public static function strings( $language = '' ) {
		$language = in_array( $language, array( 'ja', 'vi' ), true ) ? $language : self::get_language();
		$ja = array(
			'menu_title' => '拡張フォーム設定', 'settings' => '設定', 'page_title' => 'Snow Monkey Forms 拡張設定',
			'page_description' => 'リアルタイム検証・入力項目の条件分岐・送信先ルーティング・画像添付をフォーム単位で管理します。',
			'language' => '管理画面の言語', 'language_ja' => '日本語', 'language_vi' => 'Tiếng Việt', 'target_form' => '対象フォーム',
			'untitled' => '無題', 'edit_form' => 'フォーム本体を編集', 'settings_saved' => '設定を保存しました。',
			'no_forms' => 'Snow Monkey Forms のフォームがありません。先にフォームを作成してください。',
			'no_permission' => 'このページへアクセスする権限がありません。', 'no_edit_permission' => 'このフォームを編集する権限がありません。',
			'invalid_config' => '設定データの形式が正しくありません。前の画面に戻って再度お試しください。',
			'tab_general' => '基本設定', 'tab_validation' => 'リアルタイム検証', 'tab_conditions' => '入力項目の条件分岐',
			'tab_recipients' => '送信先の条件分岐', 'tab_uploads' => 'ファイル・画像添付', 'tab_postal' => '住所自動入力', 'tab_diagnostics' => '診断',
			'nav_label' => '設定カテゴリ', 'javascript_required' => 'この設定画面には JavaScript が必要です。', 'save_changes' => '変更を保存',
			'select_field' => '項目を選択', 'field_not_detected' => '現在フォームに未検出', 'delete' => '削除',
			'condition_field' => '判定項目', 'operator' => '演算子', 'compare_value' => '比較値', 'compare_value_hint' => '複数値はカンマ区切り',
			'delete_condition' => '条件を削除', 'add_condition' => '判定条件を追加', 'conditions' => '判定条件',
			'validation_rule' => '検証ルール', 'enabled' => '有効', 'target_field' => '対象項目', 'validation_type' => '検証タイプ',
			'parameter' => 'パラメータ', 'parameter_hint' => '文字数・比較項目名・正規表現など', 'error_message' => 'エラーメッセージ',
			'error_message_hint' => '空欄の場合は標準文言を使用', 'display_rule' => '表示ルール', 'visibility_target' => '表示を切り替える項目',
			'show_when' => '条件成立時に表示', 'hide_when' => '条件成立時に非表示', 'action' => '動作',
			'scope_item' => '項目行全体（推奨）', 'scope_field' => '入力コントロールのみ', 'target_scope' => '対象範囲',
			'relation_all' => 'すべての条件（AND）', 'relation_any' => 'いずれかの条件（OR）', 'relation' => '条件の結合',
			'recipient_rule' => '送信先ルール', 'admin_label' => '管理用名称', 'admin_label_hint' => '例：東京支店',
			'priority' => '優先度（小さい順）', 'to_required' => 'To（必須）', 'to_hint' => '複数はカンマ区切り。{email} のような項目タグも使用可能',
			'comma_hint' => '複数はカンマ区切り', 'subject_prefix' => '件名プレフィックス', 'subject_hint' => '例：[東京] ',
			'upload_rule' => 'ファイル・画像フィールド設定', 'file_field' => 'ファイル項目', 'image_required' => 'ファイルを必須にする', 'extensions' => '許可拡張子',
			'max_mb' => '最大容量（MB）', 'min_width' => '最小横幅（px、画像のみ・0は無制限）', 'max_width' => '最大横幅（px、画像のみ・0は無制限）',
			'min_height' => '最小高さ（px、画像のみ・0は無制限）', 'max_height' => '最大高さ（px、画像のみ・0は無制限）',
			'attach_admin' => '管理者通知へ自動添付', 'attach_admin_help' => 'メール本文に {field-name} がなくても添付します。',
			'attach_reply' => '自動返信へも添付', 'attach_reply_help' => '個人情報や容量に注意してください。',
			'postal_title' => '郵便番号・住所自動入力', 'postal_description' => '郵便番号（7桁）を入力すると、該当する都道府県・市区町村・町域を自動で入力します（YubinBango連携）。',
			'enable_postal_autofill' => 'このフォームで住所自動入力を有効化',
			'postal_field_label' => '郵便番号項目（7桁）', 'region_field_label' => '都道府県項目', 'locality_field_label' => '市区町村項目', 'street_field_label' => '町域・番地項目',
			'postal_help' => 'フォーム内の各入力項目を対応する住所欄にマッピングしてください。不要な欄は「項目を選択」のままで構いません。',
			'general_title' => '基本設定', 'general_description' => 'フォーム全体の挙動を設定します。',
			'validation_title' => 'リアルタイム検証', 'validation_description' => 'ブラウザ側の即時フィードバックとサーバー側の再検証を同じルールで実行します。',
			'conditions_title' => '入力項目の条件分岐', 'conditions_description' => '回答内容に応じて項目行または入力コントロールを表示・非表示にします。',
			'recipients_title' => '送信先の条件分岐', 'recipients_description' => '条件に一致する宛先へ管理者通知をルーティングします。宛先情報はブラウザへ公開されません。',
			'uploads_title' => 'ファイル・画像添付', 'uploads_description' => '形式・容量・画像寸法を検証し、安全にメールへ添付します。',
			'diagnostics_title' => '診断', 'diagnostics_description' => '検出したフォーム項目と実行環境を確認します。',
			'enable_form' => 'このフォームで拡張機能を有効化', 'enable_realtime' => 'リアルタイム検証を有効化',
			'hide_error_empty' => '入力前はエラーを表示しない', 'focus_first_error' => '送信時に最初のエラーへフォーカス',
			'clear_hidden' => '非表示項目の値を破棄', 'clear_hidden_help' => '誤送信を防ぐため推奨。サーバー側でも適用されます。',
			'delete_uninstall' => 'アンインストール時に設定を削除', 'validation_timing' => '検証タイミング',
			'timing_blur_input' => '入力中＋フォーカスアウト', 'timing_input' => '入力中', 'timing_blur' => 'フォーカスアウト',
			'route_mode' => '送信先ルール方式', 'route_first' => '最初に一致したルールのみ', 'route_merge' => '一致したすべての宛先を統合',
			'add_validation' => '検証ルールを追加', 'add_display' => '表示ルールを追加', 'add_recipient' => '送信先ルールを追加',
			'add_upload' => 'ファイル・画像設定を追加', 'addon' => 'アドオン', 'active' => '有効', 'detected_fields' => '検出項目数',
			'browser' => 'ブラウザ', 'type' => 'タイプ', 'dirty' => '未保存の変更があります',
			'empty_rules' => 'ルールはまだありません。下のボタンから追加できます。', 'saving' => '保存しています…',
		);

		$vi = array(
			'menu_title' => 'Cấu hình biểu mẫu mở rộng', 'settings' => 'Cài đặt', 'page_title' => 'Cấu hình mở rộng Snow Monkey Forms',
			'page_description' => 'Quản lý kiểm tra thời gian thực, điều kiện trường nhập liệu, định tuyến người nhận, tệp/ảnh đính kèm và tự động điền địa chỉ theo từng biểu mẫu.',
			'language' => 'Ngôn ngữ quản trị', 'language_ja' => '日本語', 'language_vi' => 'Tiếng Việt', 'target_form' => 'Biểu mẫu áp dụng',
			'untitled' => 'Không có tiêu đề', 'edit_form' => 'Sửa biểu mẫu gốc', 'settings_saved' => 'Đã lưu cấu hình.',
			'no_forms' => 'Chưa có Snow Monkey Form. Hãy tạo biểu mẫu trước.',
			'no_permission' => 'Bạn không có quyền truy cập trang này.', 'no_edit_permission' => 'Bạn không có quyền chỉnh sửa biểu mẫu này.',
			'invalid_config' => 'Dữ liệu cấu hình không hợp lệ. Hãy quay lại và thử lại.',
			'tab_general' => 'Cài đặt chung', 'tab_validation' => 'Kiểm tra thời gian thực', 'tab_conditions' => 'Điều kiện trường nhập liệu',
			'tab_recipients' => 'Điều kiện người nhận', 'tab_uploads' => 'Tệp & Ảnh đính kèm', 'tab_postal' => 'Tự động điền địa chỉ', 'tab_diagnostics' => 'Chẩn đoán',
			'nav_label' => 'Nhóm cài đặt', 'javascript_required' => 'Trang cấu hình này cần JavaScript.', 'save_changes' => 'Lưu thay đổi',
			'select_field' => 'Chọn trường', 'field_not_detected' => 'không tìm thấy trong biểu mẫu hiện tại', 'delete' => 'Xóa',
			'condition_field' => 'Trường điều kiện', 'operator' => 'Toán tử', 'compare_value' => 'Giá trị so sánh', 'compare_value_hint' => 'Nhiều giá trị cách nhau bằng dấu phẩy',
			'delete_condition' => 'Xóa điều kiện', 'add_condition' => 'Thêm điều kiện', 'conditions' => 'Các điều kiện',
			'validation_rule' => 'Quy tắc kiểm tra', 'enabled' => 'Bật', 'target_field' => 'Trường áp dụng', 'validation_type' => 'Kiểu kiểm tra',
			'parameter' => 'Tham số', 'parameter_hint' => 'Số ký tự, tên trường so sánh, biểu thức chính quy…', 'error_message' => 'Thông báo lỗi',
			'error_message_hint' => 'Để trống để dùng thông báo mặc định', 'display_rule' => 'Quy tắc hiển thị', 'visibility_target' => 'Trường cần ẩn/hiện',
			'show_when' => 'Hiện khi điều kiện đúng', 'hide_when' => 'Ẩn khi điều kiện đúng', 'action' => 'Hành động',
			'scope_item' => 'Toàn bộ dòng trường (khuyên dùng)', 'scope_field' => 'Chỉ ô nhập liệu', 'target_scope' => 'Phạm vi áp dụng',
			'relation_all' => 'Tất cả điều kiện (AND)', 'relation_any' => 'Một trong các điều kiện (OR)', 'relation' => 'Cách kết hợp',
			'recipient_rule' => 'Quy tắc người nhận', 'admin_label' => 'Tên quản lý', 'admin_label_hint' => 'Ví dụ: Chi nhánh Tokyo',
			'priority' => 'Độ ưu tiên (số nhỏ chạy trước)', 'to_required' => 'To (bắt buộc)', 'to_hint' => 'Nhiều địa chỉ cách nhau bằng dấu phẩy. Có thể dùng thẻ trường như {email}',
			'comma_hint' => 'Nhiều địa chỉ cách nhau bằng dấu phẩy', 'subject_prefix' => 'Tiền tố tiêu đề', 'subject_hint' => 'Ví dụ: [Tokyo] ',
			'upload_rule' => 'Cấu hình trường tệp / ảnh', 'file_field' => 'Trường tệp tin', 'image_required' => 'Bắt buộc chọn tệp', 'extensions' => 'Phần mở rộng được phép',
			'max_mb' => 'Dung lượng tối đa (MB)', 'min_width' => 'Chiều rộng tối thiểu (px, chỉ áp dụng cho ảnh)', 'max_width' => 'Chiều rộng tối đa (px, chỉ áp dụng cho ảnh)',
			'min_height' => 'Chiều cao tối thiểu (px, chỉ áp dụng cho ảnh)', 'max_height' => 'Chiều cao tối đa (px, chỉ áp dụng cho ảnh)',
			'attach_admin' => 'Tự đính kèm vào email quản trị', 'attach_admin_help' => 'Tệp vẫn được đính kèm khi nội dung email không có {field-name}.',
			'attach_reply' => 'Đính kèm cả vào email tự động trả lời', 'attach_reply_help' => 'Cần cân nhắc dữ liệu cá nhân và dung lượng email.',
			'postal_title' => 'Tự động điền địa chỉ qua mã bưu điện', 'postal_description' => 'Khi người dùng nhập mã bưu điện Nhật Bản (7 số), địa chỉ Tỉnh/Thành, Quận/Huyện, Phường/Xã sẽ được tự động điền (tích hợp YubinBango).',
			'enable_postal_autofill' => 'Bật tự động điền địa chỉ cho biểu mẫu này',
			'postal_field_label' => 'Trường mã bưu điện (7 số)', 'region_field_label' => 'Trường Tỉnh / Thành phố', 'locality_field_label' => 'Trường Quận / Huyện / Thị xã', 'street_field_label' => 'Trường Phường / Xã / Địa chỉ',
			'postal_help' => 'Hãy chọn các trường tương ứng trong biểu mẫu của bạn. Trường nào không dùng có thể để trống.',
			'general_title' => 'Cài đặt chung', 'general_description' => 'Thiết lập hành vi chung của biểu mẫu.',
			'validation_title' => 'Kiểm tra thời gian thực', 'validation_description' => 'Dùng cùng một quy tắc để phản hồi ngay trên trình duyệt và kiểm tra lại trên máy chủ.',
			'conditions_title' => 'Điều kiện trường nhập liệu', 'conditions_description' => 'Ẩn hoặc hiện trường dựa trên câu trả lời của người dùng.',
			'recipients_title' => 'Điều kiện người nhận', 'recipients_description' => 'Định tuyến email quản trị tới địa chỉ phù hợp. Thông tin người nhận không được công khai ra trình duyệt.',
			'uploads_title' => 'Tệp & Ảnh đính kèm', 'uploads_description' => 'Kiểm tra định dạng, dung lượng và kích thước tệp/ảnh trước khi đính kèm an toàn.',
			'diagnostics_title' => 'Chẩn đoán', 'diagnostics_description' => 'Kiểm tra các trường đã phát hiện và môi trường chạy.',
			'enable_form' => 'Bật tính năng mở rộng cho biểu mẫu này', 'enable_realtime' => 'Bật kiểm tra thời gian thực',
			'hide_error_empty' => 'Không hiện lỗi trước khi người dùng nhập', 'focus_first_error' => 'Đưa con trỏ tới lỗi đầu tiên khi gửi',
			'clear_hidden' => 'Xóa giá trị của trường bị ẩn', 'clear_hidden_help' => 'Khuyên dùng để tránh gửi nhầm; cũng được áp dụng phía máy chủ.',
			'delete_uninstall' => 'Xóa cấu hình khi gỡ plugin', 'validation_timing' => 'Thời điểm kiểm tra',
			'timing_blur_input' => 'Khi nhập và khi rời ô', 'timing_input' => 'Trong khi nhập', 'timing_blur' => 'Khi rời ô',
			'route_mode' => 'Chế độ quy tắc người nhận', 'route_first' => 'Chỉ quy tắc khớp đầu tiên', 'route_merge' => 'Gộp tất cả người nhận khớp',
			'add_validation' => 'Thêm quy tắc kiểm tra', 'add_display' => 'Thêm quy tắc hiển thị', 'add_recipient' => 'Thêm quy tắc người nhận',
			'add_upload' => 'Thêm cấu hình trường tệp / ảnh', 'addon' => 'Tiện ích mở rộng', 'active' => 'Đang hoạt động', 'detected_fields' => 'Số trường phát hiện',
			'browser' => 'Trình duyệt', 'type' => 'Loại', 'dirty' => 'Có thay đổi chưa được lưu',
			'empty_rules' => 'Chưa có quy tắc. Bạn có thể thêm bằng nút bên dưới.', 'saving' => 'Đang lưu…',
		);

		return 'vi' === $language ? $vi : $ja;
	}

	/** @return array<string,string> */
	public static function validation_types() {
		if ( 'vi' === self::get_language() ) {
			return array(
				'required' => 'Bắt buộc', 'email' => 'Địa chỉ email', 'tel_jp' => 'Số điện thoại Nhật Bản', 'postal_jp' => 'Mã bưu điện Nhật Bản',
				'hiragana' => 'Hiragana', 'katakana' => 'Katakana', 'url' => 'URL', 'numeric' => 'Số', 'min_length' => 'Số ký tự tối thiểu',
				'max_length' => 'Số ký tự tối đa', 'min' => 'Giá trị tối thiểu', 'max' => 'Giá trị tối đa', 'regex' => 'Biểu thức chính quy',
				'equals_field' => 'Khớp trường khác', 'different_field' => 'Khác trường khác',
			);
		}
		return array(
			'required' => '必須', 'email' => 'メールアドレス', 'tel_jp' => '日本の電話番号', 'postal_jp' => '日本の郵便番号',
			'hiragana' => 'ひらがな', 'katakana' => 'カタカナ', 'url' => 'URL', 'numeric' => '数値', 'min_length' => '最小文字数',
			'max_length' => '最大文字数', 'min' => '最小値', 'max' => '最大値', 'regex' => '正規表現', 'equals_field' => '他項目と一致', 'different_field' => '他項目と不一致',
		);
	}

	/** @return array<string,string> */
	public static function operators() {
		if ( 'vi' === self::get_language() ) {
			return array(
				'equals' => 'Bằng', 'not_equals' => 'Không bằng', 'contains' => 'Có chứa', 'not_contains' => 'Không chứa', 'in' => 'Khớp một giá trị',
				'not_in' => 'Không khớp giá trị nào', 'empty' => 'Đang trống', 'not_empty' => 'Không trống', 'greater' => 'Lớn hơn', 'greater_equal' => 'Lớn hơn hoặc bằng',
				'less' => 'Nhỏ hơn', 'less_equal' => 'Nhỏ hơn hoặc bằng', 'regex' => 'Khớp biểu thức chính quy', 'checked' => 'Đã chọn', 'unchecked' => 'Chưa chọn',
			);
		}
		return array(
			'equals' => '等しい', 'not_equals' => '等しくない', 'contains' => '含む', 'not_contains' => '含まない', 'in' => 'いずれかに一致',
			'not_in' => 'いずれにも一致しない', 'empty' => '空である', 'not_empty' => '空でない', 'greater' => 'より大きい', 'greater_equal' => '以上',
			'less' => 'より小さい', 'less_equal' => '以下', 'regex' => '正規表現に一致', 'checked' => '選択済み', 'unchecked' => '未選択',
		);
	}
}

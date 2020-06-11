<?php
const MESSAGES = [
    'doneDelete' => '削除しました。',
    'alreadyDelete' => 'すでに削除されたデータです。',
    'doneEdit' => '編集しました。',
    'doneRegister' => '新規登録しました。',
    'outbreakError' => 'エラーが発生しました。',
    'doneLogin' => 'ログインしました。',
    'loginError' => 'ユーザーネームかパスワードに誤りがあります。',
    'doneUserRegister' => '会員登録しました。',
    'logout' => 'ログアウトしました。'
];

const COMMON_ELEMENTS = [
    'phone_number' => '',
    'postal_code' => '',
    'prefectures_code' => '',
    'address' => '',
    'mail_address' => ''
];

const COMPANY_ELEMENTS = [
    'company_name' => '',
    'representative_name' => '',
    'image' => ''
];

const EMPLOYEE_ELEMENTS = [
    'employee_name' => '',
    'division_name' => ''
];

const COMMON_STRLEN_PARAMS = [
    'address' => 100,
    'mail_address' => 100
];

const COMPANY_STRLEN_PARAMS = [
    'company_name' => 50,
    'representative_name' => 20
];

const EMPLOYEE_STRLEN_PARAMS = [
    'employee_name' => 20,
    'division_name' => 20
];

const COMMON_BLANK_MESSAGES = [
    'phone_number' => '＊電話番号を記載してください',
    'postal_code' => '＊郵便番号を記載してください',
    'prefectures_code' => '＊都道府県を選択してください',
    'address' => '＊住所を記載してください',
    'mail_address' => '＊メールアドレスを記載してください'
];

const COMPANY_BLANK_MESSAGES = [
    'company_name' => '＊会社名を記載してください',
    'representative_name' => '＊代表者名を記載してください'
];

const EMPLOYEE_BLANK_MESSAGES = [
    'employee_name' => '＊社員名を記載してください',
    'division_name' => '＊部署名を記載してください'
];

const COMMON_WRONG_MESSAGES = [
    'phone_number' => '＊電話番号を全11桁正しく数字で記載してください',
    'postal_code' => '＊郵便番号を全7桁正しく数字で記載してください',
    'address' => '＊100文字以内で住所を記載してください',
    'mail_address' => '＊100文字以内で正しいメールアドレスを記載してください'
];

const COMPANY_WRONG_MESSAGES = [
    'company_name' => '＊50文字以内で会社名を記載してください',
    'representative_name' => '＊20文字以内で代表者名を記載してください',
    'image' => '＊画像を選択してください。'
];

const EMPLOYEE_WRONG_MESSAGES = [
    'employee_name' => '＊20文字以内で社員名を記載してください',
    'division_name' => '＊20文字以内で部署名を記載してください'
];

// 社員検索時に都道府県指定が無くともエラーが出ないように空を入れる
const PREF_CODES = ["" => "", "1" => "北海道", "2" => "青森県", "3" => "岩手県", "4" => "宮城県", "5" => "秋田県", "6" => "山形県", "7" => "福島県", "8" => "茨城県", "9" => "栃木県", "10" => "群馬県", "11" => "埼玉県", "12" => "千葉県", "13" => "東京都", "14" => "神奈川県", "15" => "新潟県", "16" => "富山県", "17" => "石川県", "18" => "福井県", "19" => "山梨県", "20" => "長野県", "21" => "岐阜県", "22" => "静岡県", "23" => "愛知県", "24" => "三重県", "25" => "滋賀県", "26" => "京都府", "27" => "大阪府", "28" => "兵庫県", "29" => "奈良県", "30" => "和歌山県", "31" => "鳥取県", "32" => "島根県", "33" => "岡山県", "34" => "広島県", "35" => "山口県", "36" => "徳島県", "37" => "香川県", "38" => "愛媛県", "39" => "高知県", "40" => "福岡県", "41" => "佐賀県", "42" => "長崎県", "43" => "熊本県", "44" => "大分県", "45" => "宮崎県", "46" => "鹿児島県", "47" => "沖縄県"];

const IMGS = [
    'gif' => 'image/gif',
    'jpg' => 'image/jpeg',
    'png' => 'image/png'
];

const SET_IMG = '＊もう一度選択してください。';

const USER_ELEMENTS =  ['user_name' => '', 'password' => ''];

const USER_STRLEN_PARAMS = ['user_name' => '50', 'password' => '100'];

const USER_BLANK_MESSAGES = [
    'user_name' => '＊ユーザーネームを記載してください',
    'password' => '＊パスワードを記載してください'
];

const USER_WRONG_MESSAGES = [
    'user_name' => '＊50文字以内でユーザーネームを記載してください',
    'password' => '＊8文字以上100文字以内でパスワードを記載してください'
];

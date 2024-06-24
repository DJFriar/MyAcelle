<?php return array (
  'accepted' => ':attributeを受け入れる必要があります。',
  'active_url' => ':attributeは有効なURLではありません。',
  'after' => ':attributeは、:dateの後の日付である必要があります。',
  'alpha' => ':attributeには文字のみを含めることができます。',
  'alpha_dash' => ':attributeには、文字、数字、およびダッシュのみを含めることができます。',
  'alpha_num' => ':attributeには、文字と数字のみを含めることができます。',
  'array' => ':attributeは配列である必要があります。',
  'attributes' => 
  array (
    'options' => 
    array (
      'limit_value' => 'リミット値',
      'limit_base' => 'リミット制限',
      'limit_unit' => 'リミット時間制御',
      'api_key' => 'API key',
      'api_secret_key' => 'API セキュリティー key',
      'username' => 'ユーザ名',
      'password' => 'パスワード',
      'vendor_id' => 'ベンダーID',
      'public_key' => 'public key',
      'vendor_auth_code' => 'vendor auth code',
      'merchant_key' => 'Merchant Key',
      'salt' => 'Salt',
      'payu_base_url' => 'PayU Base URL',
      'field' => 'リストフィールド',
      'days_of_week' => '曜日',
      'days_of_month' => '月日',
    ),
    'quota_value' => '送信制限',
    'quota_base' => '基本時間',
    'quota_unit' => '時間単位',
    'lists_segments' => 
    array (
      0 => 
      array (
        'mail_list_uid' => 'List',
      ),
      1 => 
      array (
        'mail_list_uid' => 'List',
      ),
      2 => 
      array (
        'mail_list_uid' => 'List',
      ),
      3 => 
      array (
        'mail_list_uid' => 'List',
      ),
      4 => 
      array (
        'mail_list_uid' => 'List',
      ),
      5 => 
      array (
        'mail_list_uid' => 'List',
      ),
      6 => 
      array (
        'mail_list_uid' => 'List',
      ),
      7 => 
      array (
        'mail_list_uid' => 'List',
      ),
      8 => 
      array (
        'mail_list_uid' => 'List',
      ),
      9 => 
      array (
        'mail_list_uid' => 'List',
      ),
    ),
    'plan' => 
    array (
      'general' => 
      array (
        'name' => 'name',
        'description' => 'description',
        'currency_id' => 'currency',
        'frequency_amount' => 'frequency amount',
        'frequency_unit' => 'frequency unit',
        'price' => 'price',
        'color' => 'color',
      ),
    ),
  ),
  'before' => ':attributeは、:dateより前の日付である必要があります。',
  'between' => 
  array (
    'numeric' => ':attributeは:minと:maxの間にある必要があります。',
    'file' => ':attributeは:minから:maxキロバイトの間でなければなりません。',
    'string' => ':attributeは:minから:max文字の間にある必要があります。',
    'array' => ':attributeには、:minと:maxの項目が必要です。',
  ),
  'boolean' => ':attributeフィールドはtrueまたはfalseである必要があります。',
  'confirmed' => ':attributeの確認が一致しません。',
  'custom' => 
  array (
    'miss_main_field_tag' => 
    array (
      'required' => 'EMAILフィールドタグがありません',
    ),
    'conflict_field_tags' => 
    array (
      'required' => 'フィールドタグを同じにすることはできません',
    ),
    'segment_conditions_empty' => 
    array (
      'required' => '条件リストを空にすることはできません',
    ),
    'mysql_connection' => 
    array (
      'required' => 'MySQLサーバーに接続できません',
    ),
    'database_not_empty' => 
    array (
      'required' => 'データベースは空ではありません',
    ),
    'promo_code_not_valid' => 
    array (
      'required' => 'プロモーションコードが無効です',
    ),
    'smtp_valid' => 
    array (
      'required' => 'SMTPサーバーに接続できません',
    ),
    'yaml_parse_error' => 
    array (
      'required' => 'yamlを解析できません。 構文を確認してください',
    ),
    'file_not_found' => 
    array (
      'required' => 'ファイルが見つかりません。',
    ),
    'not_zip_archive' => 
    array (
      'required' => 'このファイルはzipパッケージではありません。',
    ),
    'zip_archive_unvalid' => 
    array (
      'required' => 'パッケージを読み取ることができません。',
    ),
    'custom_criteria_empty' => 
    array (
      'required' => 'カスタム基準を空にすることはできません',
    ),
    'php_bin_path_invalid' => 
    array (
      'required' => '無効なPHP実行可能ファイル。 再度確認してください。',
    ),
    'can_not_empty_database' => 
    array (
      'required' => '特定のテーブルを削除できません。データベースを手動でクリーンアップして、再試行してください。',
    ),
    'recaptcha_invalid' => 
    array (
      'required' => 'Invalid reCAPTCHA check.',
    ),
    'payment_method_not_valid' => 
    array (
      'required' => 'お支払い方法の設定に問題がありました。 再度確認してください。',
    ),
    'email_already_subscribed' => 
    array (
      'required' => 'このメールはすでに登録されています。',
    ),
    'mail_list_uid' => 
    array (
      'required' => 'メールリストが必要です。',
    ),
    'contact' => 
    array (
      'zip' => 
      array (
        'required' => '郵便番号（Zip / Postal code）は必須項目です。',
      ),
    ),
  ),
  'date' => ':attributeは有効な日付ではありません。',
  'date_format' => ':attributeがフォーマット:formatと一致しません。',
  'different' => ':attributeと:otherは異なっている必要があります。',
  'digits' => ':attributeは:digitsdigitsである必要があります。',
  'digits_between' => ':attributeは:minと:maxの数字の間でなければなりません。',
  'distinct' => ':attributeフィールドの値が重複しています。',
  'email' => 'このフィールドには、有効なメールアドレスを入力する必要があります。',
  'exists' => '選択された:attributeは無効です。',
  'filled' => ':attributeは必須です。',
  'image' => ':attributeは画像である必要があります。',
  'in' => '選択された:attributeは無効です。',
  'in_array' => ':attributeフィールドは:otherには存在しません。',
  'integer' => ':attributeは整数でなければなりません。',
  'ip' => ':attributeは有効なIPアドレスである必要があります。',
  'json' => ':attributeは有効なJSON文字列である必要があります。',
  'license' => 'ライセンスが無効です。',
  'license_error' => ':error',
  'max' => 
  array (
    'numeric' => ':attributeは:maxより大きくすることはできません。',
    'file' => ':attributeは:maxキロバイトを超えることはできません。',
    'string' => ':attributeは:max文字より大きくすることはできません。',
    'array' => ':attributeには、:maxを超えるアイテムを含めることはできません。',
  ),
  'mimes' => ':attributeは、タイプ：:valuesのファイルである必要があります。',
  'min' => 
  array (
    'numeric' => ':attributeは少なくとも:minである必要があります。',
    'file' => ':attributeは少なくとも:minキロバイトでなければなりません。',
    'string' => ':attributeは少なくとも:min文字でなければなりません。',
    'array' => ':attributeには少なくとも:minアイテムが必要です。',
  ),
  'not_in' => '選択した:attributeが無効です。',
  'numeric' => ':attributeは数値でなければなりません。',
  'present' => ':attributeフィールドが存在する必要があります。',
  'regex' => ':attribute形式が無効です。',
  'required' => ':attributeフィールドは必須です。',
  'required_if' => ':otherが:valueの場合、:attributeフィールドは必須です。',
  'required_unless' => ':otherが:valuesにない限り、:attributeフィールドは必須です。',
  'required_with' => ':valuesが存在する場合、:attributeフィールドは必須です。',
  'required_with_all' => ':valuesが存在する場合、:attributeフィールドは必須です。',
  'required_without' => ':valuesが存在しない場合、:attributeフィールドは必須です。',
  'required_without_all' => ':valuesが存在しない場合、:attributeフィールドは必須です。',
  'same' => ':attributeと:otherは一致する必要があります。',
  'size' => 
  array (
    'numeric' => ':attributeは:sizeでなければなりません。',
    'file' => ':attributeは:sizeキロバイトでなければなりません。',
    'string' => ':attributeは:size文字でなければなりません。',
    'array' => ':attributeには:sizeアイテムが含まれている必要があります。',
  ),
  'string' => ':attributeは文字列である必要があります。',
  'substring' => ':tagタグが:attributeに見つかりませんでした。',
  'timezone' => ':attributeは有効なゾーンである必要があります。',
  'unique' => ':attributeフィールドはすでに使用されています。',
  'url' => ':attribute形式が無効です。',
) ?>
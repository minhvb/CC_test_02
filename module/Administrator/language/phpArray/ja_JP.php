<?php
return [
    'title_menu_system' => 'メニュー画面（管理用）',
    'title_menu_input' => 'メニュー画面（入力用）',
    'label_system_menu_policy' => '管理業務',
    'label_input_menu_policy' => '施策入力業務',
    'label_system_menu_homepage' => '閲覧業務',
    'label_input_menu_homepage' => '施策閲覧業務',
    'title_mainMenu' => '管理業務メイン画面',
    'MSG_CM_001_ServerError' => 'エラーが発生します。エラーが再発生する場合、管理者に連絡してください。',
    'MSG_UM_001_ServerError' => 'エラーが発生します。エラーが再発生する場合、管理者に連絡してください。',
    'MSG_UM_002_SuccessDeleteUser' => '[Value]のユーザーIDを正常に削除しました。',
    'MSG_UM_003_FailDeleteUser' => 'ユーザーを正常に削除しませんでした。',
    'MSG_UM_004_RequireField' => '[XXX]フィールドが必須です。',
    'MSG_UM_005_WrongPassFormat' => '英数混在の8文字以上で入力してください。',
    'MSG_UM_007_RequireFile' => 'アップロード対象ファイルを選択してください。',
    'MSG_UM_008_WrongCSVFile' => 'CSVファイルを選択してください。',
    'MSG_UM_009_WrongFileFormat' => 'アップロードしたファイルはフォーマットの通りに正しくないです。',
    'MSG_UM_010_WrongFileEmpty' => '取り込んだファイルにデータが1件もありません。',
    'MSG_UM_011_CannotDeleteUser' => 'ユーザーを削除することが出来ません。',
    'MSG_UM_012_CannotResetPassword' => 'パスワードをリセットすることが出来ません。',
    'MSG_UM_013_FailResetPassword' => 'パスワードを失敗にリセットしました。',
    'MSG_UM_014_SuccessResetPassword' => '[Value]のユーザーIDのパスワードを正常にリセットしました。',
    'MSG_UM_015_FileNotExist' => 'ファイルが存在しません。',
    'MSG_UM_016_FileSizeExceedingLimits' => 'ファイルの容量が100MBを超えました。',
    'MSG_UM_017_CanNotReadFile' => 'アップロードファイルを読み込むことが出来ません。',
    'MSG_UM_018_RequireField' => 'フィールドが必須です。',
    'MSG_UM_020_ValueNotExistInSystem' => '[Value]がシステムに存在しません。',
    'MSG_UM_021_RoleNotExistInSystem' => 'ロール[Value]がシステムに存在しません。',
    'MSG_UM_022_EmailWrongFormat' => 'メールが半角の256文字以下であり、メールのフォーマットが正しいこと。',
    'MSG_UM_023_UserCannotDelete' => '都職員、関係団体職員、金融機関行員、一般という4つユーザー種類のみ削除することが出来ます。',
    'MSG_UM_024_DepartmentNotExistInSystem' => '部が正しくないです。',
    'MSG_UM_025_DivisiontNotExistInSystem' => '課が正しくないです。',
    'MSG_UM_026_DupplicateUserInFile' => 'ユーザーID[Value]がファイルに存在しました。',
    'MSG_UM_027_UserIdExistInSystem' => 'ユーザーID[Value]がシステムに存在しました。',
    'MSG_UM_028_DeleteWrongFormat' => '値が「削除」又は「空白」となること。',
    'MSG_UM_029_EmailExistInSystem' => 'メール[Value]がシステムに存在しました。',
    'MSG_UM_030_UserIdNotExistInSystem' => 'ユーザーID[Value]及びロール[Role]がシステムに存在しません。',
    'MSG_UM_031_UserAdminRegex' => 'ユーザーID[Value]が英字3文字＋部(2桁)＋課(2桁)＋連番(2桁)となること。',
    'MSG_UM_032_UserViewRegex' => 'ユーザーID[Value]が英字3文字＋行員番号(7桁)となること。',
    'MSG_UM_033_UserEmailRegex' => 'ユーザーID[Value]がメールとなること。',
    'MSG_UM_034_DupplicateEmailInFile' => 'メール[Value]がファイルに存在しました。',
    'MSG_UM_035_NotSelectedFile' => 'アップロードするファイルを選択してください。',
    'MSG_UM_036_FileSelectedIsZeroByte' => '取り込んだファイルにデータが1件もありません。',
    'MSG_UM_037_MaxLineOfImport' => '一度にアップロードできるユーザー数は[Value]名までです。それ以上の場合は、1ファイルあたりのユーザー数が[Value]名以下になるよう分割してアップロードしてください。',
    'MSG_UM_038_EmailMustEqualID' => '[Value]というロールを持っているユーザーは、メールアドレスがIDと一致すること。',
    'MSG_UM_039_EmailNotEmpty' => '[Value]というロールを持っているユーザーは、メールを入力すること。',

    'MSG_MP_001_RequireField' => '[XXX]フィールドが必須です。',
    'MSG_MP_002_FailUpdateInfoUser' => '正常に更新しませんでした。',
    'MSG_MP_003_WrongPassFormat' => '最小の8桁でパスワードを入力してください。',
    'MSG_MP_004_ConfirmUpdateInfo' => '情報を更新しますか。',
    'MSG_MP_005_EmailWrong' => 'メールのフォーマットが正しくないです。',
    'MSG_MP_006_SecurityQuestionCannotEmpty' => '秘密質問を入力していません。',
    'MSG_MP_007_SecurityAnswerCannotEmpty' => '秘密回答を入力していません。',
    'MSG_MP_008_SuccessUpdateInfo' => ' 情報を正常に更新しました。',
    'MSG_MP_009_OldPasswordCannotEmpty' => '古いパスワードが空白にならないこと。',
    'MSG_MP_010_NewPasswordCannotEmpty' => '新しいパスワードが空白にならないこと。',
    'MSG_MP_011_NewPasswordRetypeCannotEmpty' => '再入力した新しいパスワードが空白にならないこと。',
    'MSG_MP_012_OldPasswordIncorrect' => '古いパスワードが正しくないです。',
    'MSG_MP_013_NewPasswordNotMatch' => '2つの新しいパスワードが同一となること。',
    'MSG_MP_014_PasswordCanNotInHistory' => '新しいパスワードは、最近の5回と重複されません。',
    'MSG_MP_015_SuccessChangePassword' => 'パスワードが正常に変更されました。',
    'MSG_MP_016_FailChangePassword' => 'パスワードが正常に変更されませんでした。',
    'MSG_MP_017_PasswordCanNotEqCurrentPassword' => '新しいパスワードが古いパスワードと重複されません。',
    'MSG_MP_018_SuccessSaveSettingMail' => 'メール設定を正常に保存しました。',
    'MSG_MP_019_FailSaveSettingMail' => 'メール設定を正常に保存しませんでした。',
    'MSG_MP_020_EmailExistInSystem'=>'既にシステムにメールが存在しています。',
    'MSG_MP_021_SecurityAnswerWrongFormat'=>'回答が全角文字となり、11文字より小さいであること。',
    'MSG_MP_022_CannotOpenSettingMail'=>'メールが存在していませんので、メールを設定することが出来ません。',
    'MSG_MP_023_CannotUpdateEmailToEmpty'=>'メールアドレスを入力してください。',
    'MSG_MP_024_SettingMailFirstTime'=>'初めてメールを設定する為、少なくとも1つ条件を選択してください。',
    'MSG_MP_025_ActiveEmailTitlePage'=>'メールアクティブ化用の画面',
    'MSG_MP_026_ActiveEmailMessage'=>'メールをアクティブ化する為にパスワードを入力してください。',
    'MSG_MP_027_PasswordNotEmpty'=>'パスワードを入力していません。',
    'MSG_MP_028_PasswordWasWrong'=>'パスワードが正しくないです。',
    'MSG_MP_029_EmailEmpty'=>'アクティブ化用のメールが存在しません。',
    'MSG_MP_030_EmailActived'=>'メールがアクティブ化されました。',
    'MSG_MP_031_EmailActivedSuccess'=>'メールが正常にアクティブ化されました。',
    'MSG_MP_032_CannotOpenSettingMailEmailNotActived'=>'メールがアクティブ化されていませんので、メールを設定することが出来ません。',
    'MSG_MP_033_EmailNotActivedCannotUpdateEmail'=>'メールがアクティブ化されていませんので、メールを更新することが出来ません。',
    'MSG_MP_034_ConfirmSettingMail'=>'現在登録されている施策の中で、条件に合致する施策は[Value]件あります。',
  
    'MSG_PO_000_System_Error' => 'エラーが発生します。エラーが再発生する場合、管理者に連絡してください。',
    'MSG_PO_001_Required_Field' => '必須入力項目です。',
    'MSG_PO_002_Error_Format_Date' => '日付のフォーマットが正しくないです。正しいフォーマットがYYYY/MM/DDとなること。',
    'MSG_PO_003_Error_File_Too_Large' => 'アップロードファイルが大きいすぎます。最大の容量が128MBとなります。',
    'MSG_PO_004_Error_File_Wrong_Format_PDF' => 'アップロードしたファイル形式が間違っています。ファイル形式がPDFファイルとなります。',
    'MSG_PO_005_Error_Publish_Date_Format' => '日付・時刻のフォーマットが正しくないです。正しいフォーマットがYYYY/MM/DD hh:mmとなります。',
    'MSG_PO_006_Error_Publish_Start_Greater_End' => '公開開始日が公開終了日より小さいであること。',
    'MSG_PO_007_Delete_Success' => '以下の施策を正常に削除しました。<br/>- %s',
    'MSG_PO_008_Error_Not_Exist_Policy_Can_Public' => '選択した施策が既に公開されました。',
    'MSG_PO_009_Publish_Policy_Success' => '以下の施策が正常に公開されました。<br/>- %s',
    'MSG_PO_010_Publish_Policy_Fail' => '以下の施策が公開されません。<br/>- %s',
    'MSG_PO_011_Error_Not_Exist_Policy_Can_Private' => '選択した施策が既に非公開されました。',
    'MSG_PO_012_Private_Policy_Success' => '以下の施策が正常に非公開されました。<br/>- %s',
    'MSG_PO_013_Private_Policy_Fail' => '以下の施策が非公開されません。<br/>- %s',
    'MSG_PO_014_Add_Policy_Success' => '施策を正常に新規作成しました。',
    'MSG_PO_015_Edit_Policy_Success' => '施策を正常に修正しました。',
    'MSG_PO_016_Error_Compare_StartDate_With_Input' => '募集開始日が募集終了日以下であり、及び募集打切日以下であること。',
    'MSG_PO_017_Error_Compare_EndDate_With_Input' => '募集終了日が公開開始日以上であり、及び公開終了日以下であること。',
    'MSG_PO_018_Error_Compare_Deadline_With_Input' => '募集打切日が公開開始日以上であり、及び公開終了日以下であること。',
    'MSG_PO_019_Error_Compare_UpdateDate_With_Input' => '公開日が公開開始日以上であり、及び公開終了日以下であること。',

    'MSG_PO_020_Error_Compare_StartDate_With_EndDate' => '開始日が終了日以下になります。',
    'MSG_PO_021_Error_Compare_StartDate_With_Deadline' => '開始日が締切日以下になります。',

    'MSG_PO_022_Error_Compare_StartDate_With_StartPublishDate' => '募集開始日が公開開始日以上であること。',
    'MSG_PO_023_Error_Compare_StartDate_With_EndPublishDate' => '募集開始日が公開終了日以下であること。',
    'MSG_PO_024_Error_Compare_StartDate_With_PublishDate' => '開始日が公開終了日以下であり、且つ公開開始日以上です。',

    'MSG_PO_025_Error_Compare_EndDate_With_StartPublishDate' => '募集終了日が公開開始日以上であること。',
    'MSG_PO_026_Error_Compare_EndDate_With_EndPublishDate' => '募集終了日が公開終了日以下であること。',
    'MSG_PO_027_Error_Compare_EndDate_With_PublishDate' => '終了日が公開終了日以下であり、且つ終了日が公開開始日以上です。',

    'MSG_PO_028_Error_Compare_Deadline_With_StartPublishDate' => '募集打切日が公開開始日以上であること。',
    'MSG_PO_029_Error_Compare_Deadline_With_EndPublishDate' => '募集打切日が公開終了日以下であること。',
    'MSG_PO_030_Error_Compare_Deadline_With_PublishDate' => '締め切り日が公開終了日以下であり、且つ締め切り日が公開開始日以上です。',

    'MSG_PO_031_Error_Compare_UpdateDate_With_StartPublishDate' => '更新日が公開開始日以上であること。',
    'MSG_PO_032_Error_Compare_UpdateDate_With_EndPublishDate' => '更新日が公開終了日以下であること。',
    'MSG_PO_033_Error_Compare_UpdateDate_With_PublishDate' => '更新日がが公開開始日以上であり、及び公開終了日以下であること。',

    'MSG_PO_034_Error_FullSize_And_Length' => '全角で%s文字以内で入力してください。',
    'MSG_PO_035_Error_File_Too_Small' => 'PDFファイルの容量が「0」より大きいであること。',

    'MSG_PO_036_Empty_Result' => 'レコードが存在しません。',
    'MSG_PO_037_Not_Exist_Record' => '施策がシステムに存在しません。',

    'MSG_PM_001_EmptyPolicyIds' => '選択項目にチェックを入れてください。',
    'MSG_PM_002_Title_Warning' => '注意',
    'MSG_PM_003_Content_Confirm_Warning' => '注目フラグすることに確認してください。',
    'MSG_PM_004_Title_Confirm_Save_Policy' => '施策の情報保存確認',
    'MSG_PM_005_Content_Confirm_Save_Policy' => 'この情報を保存したいですか。',

    'MSG_PM_006_Title_Private_Policy' => '施策非公開確認',
    'MSG_PM_007_Content_Private_Policy' => '施策を非公開しますか。',
    'MSG_PM_008_Title_Public_Policy' => '施策非公開確認',
    'MSG_PM_009_Content_Public_Policy' => '施策を公開しますか。',
    'MSG_PM_0010_Title_Delete_Policy' => '施策削除確認',
    'MSG_PM_0011_Content_Delete_Policy' => '施策を削除しますか。',
    'MSG_PM_0012_Title_Private_Policy_Succeed'=>'施策非公開完了',
    'MSG_PM_0013_Title_Public_Policy_Succeed'=>'施策公開完了',
    'MSG_PM_0014_Title_Delete_Policy_Succeed'=>'施策削除完了',
    'MSG_PM_0015_Title_Delete_Row'=>'データ削除確認',

    'MSG_NO_001_NoticeNormal' => 'お知らせ',
    'MSG_NO_002_SurveyQuestion' => 'アンケート',
    'MSG_NO_003_EditPageTitle' => 'お知らせの追加・修正',
];

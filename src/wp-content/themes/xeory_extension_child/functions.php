<?php
//定数定義
const UNPAID_MEMBER_LEVEL = 2;
const PAID_MEMBER_LEVEL = 3;
const TEST_CLIENT_IP = '00370';
const PRODUCT_CLIENT_IP = '95518';
const TELECOM_IP_FROM_TO = array('52.196.8.0', '54.65.177.67', '54.95.89.20', '54.238.8.174');


// 親テーマと子テーマのCSSを読み込み
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
function theme_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style',
    get_stylesheet_directory_uri() . '/style.css',
    array('parent-style')
    );
}

/**
 * 投稿のラベルを変更します。（投稿→加盟店舗）
 */
function custom_post_labels( $labels ) {
	$labels->name = '加盟店舗'; // 投稿
	$labels->singular_name = '加盟店舗'; // 投稿
	$labels->add_new = '新規追加'; // 新規追加
	$labels->add_new_item = '加盟店舗を追加'; // 新規投稿を追加
	$labels->edit_item = '店舗情報の編集'; // 投稿の編集
	$labels->new_item = '新規'; // 新規投稿
	$labels->view_item = '加盟店舗を表示'; // 投稿を表示
	$labels->search_items = '加盟店舗を検索'; // 投稿を検索
	$labels->not_found = '加盟店舗が見つかりませんでした。'; // 投稿が見つかりませんでした。
	$labels->not_found_in_trash = 'ゴミ箱内に加盟店舗が見つかりませんでした。'; // ゴミ箱内に投稿が見つかりませんでした。
	$labels->parent_item_colon = ''; // (なし)
	$labels->all_items = '加盟店舗一覧'; // 投稿一覧
	$labels->archives = '加盟店舗アーカイブ'; // 投稿アーカイブ
	$labels->insert_into_item = '加盟店舗ページに挿入'; // 投稿に挿入
	$labels->uploaded_to_this_item = 'この加盟店舗ページへのアップロード'; // この投稿へのアップロード
	$labels->featured_image = 'アイキャッチ画像'; // アイキャッチ画像
	$labels->set_featured_image = 'アイキャッチ画像を設定'; // アイキャッチ画像を設定
	$labels->remove_featured_image = 'アイキャッチ画像を削除'; // アイキャッチ画像を削除
	$labels->use_featured_image = 'アイキャッチ画像として使用'; // アイキャッチ画像として使用
	$labels->filter_items_list = '加盟店舗リストの絞り込み'; // 投稿リストの絞り込み
	$labels->items_list_navigation = '加盟店舗リストナビゲーション'; // 投稿リストナビゲーション
	$labels->items_list = '加盟店舗リスト'; // 投稿リスト
	$labels->menu_name = '加盟店舗'; // 投稿
	$labels->name_admin_bar = '加盟店舗'; // 投稿
	return $labels;
}
add_filter( 'post_type_labels_post', 'custom_post_labels' );

//ショートコードを使ったphpファイルの呼び出し方法
function my_php_Include($params = array()) {
extract(shortcode_atts(array('file' => 'default'), $params));
ob_start();
include(STYLESHEETPATH . "/$file.php");
return ob_get_clean();
}
add_shortcode('myphp', 'my_php_Include');


add_theme_support('post-thumbnails');


// メニューを非表示にする
function remove_menus () {
global $menu;
 if (!current_user_can('level_2')) { //level1以下のユーザーの場合メニューをunsetする
 remove_menu_page('wpcf7'); //Contact Form 7

 //unset($menu[2]); // ダッシュボード
 unset($menu[4]); // メニューの線1
 //unset($menu[5]); // 投稿
 unset($menu[10]); // メディア
 unset($menu[15]); // リンク
 unset($menu[20]); // ページ
 unset($menu[25]); // コメント
 unset($menu[59]); // メニューの線2
 unset($menu[60]); // テーマ
 unset($menu[65]); // プラグイン
 unset($menu[70]); // プロフィール
 unset($menu[75]); // ツール
 unset($menu[80]); // 設定
 unset($menu[90]); // メニューの線3
 unset($menu[26]);  // 利用者の声
 unset($menu[27]);  // コラム
 unset($menu[6]);  // CTA
}

}
add_action('admin_menu', 'remove_menus');

/* 店舗一覧ページに表示する抜粋文字数を設定する */
function new_excerpt_mblength($length) {
     return 200;
}
add_filter('excerpt_mblength', 'new_excerpt_mblength');

/* 末尾の[…]を消す */
function new_excerpt_more($more) {
	return '';
}
add_filter('excerpt_more', 'new_excerpt_more');

add_action('swpm_front_end_registration_complete_fb','after_registration');
function after_registration($data){
  if ($data['membership_level'] == UNPAID_MEMBER_LEVEL) {
  	$email = $data['email'];
  	$tel = $data['phone'];
  	$redirectUrl = site_url()."/register_done";
    $client_ip = (site_url() == 'http://www.c-lounge.club') ? PRODUCT_CLIENT_IP : TEST_CLIENT_IP;
  	$paymentUrl = "https://secure.telecomcredit.co.jp/inetcredit/adult/order.pl?clientip=".$client_ip."&rebill_param_id=30day3758yen&usrmail={$email}&usrtel={$tel}&redirect_back_url={$redirectUrl}";
  	header("Location: {$paymentUrl}");
    exit;
  }
}

add_shortcode('receive_telecom_result','receive_telecom_result');
function receive_telecom_result() {
	global $wpdb;
	//IPアドレスでテレコムからのアクセスであることを確認
	$is_telecom_access = _isTelecomIpAccessed();
	if ($is_telecom_access && isset($_GET['email']) && isset($_GET['rel']) && $_GET['rel'] == 'yes') {
		$email = $_GET['email'];
		$member_table = $wpdb->prefix . 'swpm_members_tbl';
		$wpdb->update($member_table, array('membership_level' => PAID_MEMBER_LEVEL), array('email' => $_GET['email']));
    // 件名
    $subject = "【カフェラウンジクラブ】登録完了";
    // 本文
    $message = "カフェラウンジクラブ事務局です。<br><br>会員登録が全て完了いたしました。<br><br>
    ログインページ（".site_url()."/clc/membership-login/membership-profile）より、設定したアカウントIDとパスワードでログインすることで、会員カードを表示することができます。店舗へ行った際に会員カードをお見せください。<br>今後ともカフェラウンジクラブをよろしくお願いいたします。<br><br><br>-------------<br>CLC（Cafe Lounge Club）事務局<br>運営チーム<br>
    <br>〒160−0022<br>
    <br>東京都新宿区新宿1-7-10-603<br>
    <br>TEL/FAX: 03-5366-6581<br>
    <br>MAIL:info@c-lounge.club<br>
    <br>営業時間:平日10:00～17:00<br>";
    // ヘッダー
    $headers = ['From: カフェラウンジクラブ <info@c-lounge.club>',
                         'Content-Type: text/html; charset=UTF-8',];

    wp_mail($email, $subject, $message, $headers);
    echo('決済認証成功');
	} else {
    echo('決済認証失敗');
  }
}

add_shortcode('receive_telecom_result_continue','receive_telecom_result_continue');
function receive_telecom_result_continue() {
	global $wpdb;
	//IPアドレスでテレコムからのアクセスであることを確認
	$is_telecom_access = _isTelecomIpAccessed();
	if ($is_telecom_access && isset($_GET['email']) && isset($_GET['rel']) && $_GET['rel'] == 'no') {
		$email = $_GET['email'];
		$member_table = $wpdb->prefix . 'swpm_members_tbl';
		$wpdb->update($member_table, array('membership_level' => UNPAID_MEMBER_LEVEL), array('email' => $_GET['email']));
    echo('継続決済失敗データを受信しました。');
	} else {
    echo('決済データを受信しました。');
  }
}

function _isTelecomIpAccessed() {
  $remoteIp = $_SERVER["REMOTE_ADDR"];
  return in_array($remoteIp, TELECOM_IP_FROM_TO);
}
?>

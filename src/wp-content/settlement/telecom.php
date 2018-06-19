<?php
/*
Version: 1.1.2
*/

/*  Copyright 2012 - 2014 Hiroaki Miyashita

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

class nsa_telecom {

	function nsa_telecom() {
		global $order_management;
		$order_management = new order_management();
	}
	
	function nsa_telecom_option() {
		$options = get_option('net_shop_admin');
?>
<tr><td>
<div class="postbox closed">
<div class="handlediv" title="<?php _e('Click to toggle', 'net-shop-admin'); ?>"><br /></div>
<h3><?php _e('Telecom Credit', 'net-shop-admin'); ?></h3>
<div class="inside">
<p><label><?php _e('Client IP', 'net-shop-admin'); ?>:<br /><input type="text" name="telecom[clientip]" value="<?php echo esc_attr($options['settlement_module_options']['telecom']['clientip']); ?>" class="regular-text" /></label></p>

<?php
		for($i=0;$i<count($options['settlement_module_options']['telecom']['purchase_product_id'])+1;$i++) :
?>
<p>
<label><?php _e('Rebill Parameter ID', 'net-shop-admin'); ?>:<input type="text" name="telecom[rebill_param_id][]" value="<?php echo esc_attr($options['settlement_module_options']['telecom']['rebill_param_id'][$i]); ?>" size="20" /></label>
<label><?php _e('Purchase Product ID', 'net-shop-admin'); ?>:<input type="text" name="telecom[purchase_product_id][]" value="<?php echo esc_attr($options['settlement_module_options']['telecom']['purchase_product_id'][$i]); ?>" size="5" /></label>
<label><?php _e('Cancel Product ID', 'net-shop-admin'); ?>:<input type="text" name="telecom[cancel_product_id][]" value="<?php echo esc_attr($options['settlement_module_options']['telecom']['cancel_product_id'][$i]); ?>" size="5" /></label>
<br />
<label><?php _e('Payment PHP Code', 'net-shop-admin'); ?>:<textarea name="telecom[payment_code][]" rows="2" cols="30"><?php echo esc_attr($options['settlement_module_options']['telecom']['payment_code'][$i]) ?></textarea></label>
<label><?php _e('Failed PHP Code', 'net-shop-admin'); ?>:<textarea name="telecom[failed_code][]" rows="2" cols="30"><?php echo esc_attr($options['settlement_module_options']['telecom']['failed_code'][$i]) ?></textarea></label>
</p>
<?php
		endfor;
?>
</div>
</div>
</td></tr>
<?php
	}
	
	function nsa_telecom_suboption($i) {
		$options = get_option('net_shop_admin');
?>
<p><label><?php _e('Payment Method', 'net-shop-admin'); ?>:<br /><select name="telecom_settlement[<?php echo $i; ?>]">
<option value=""></option>
<?php
		$telecom_settlement = array(
					'tsudo' => __('Tsudo Settlement', 'net-shop-admin'),
					'auth' => __('Auth Settlement', 'net-shop-admin'),
					//'cti' => __('CTI Settlement', 'net-shop-admin'),
					'netbank' => __('Net Bank Settlement', 'net-shop-admin'),
					'edy' => __('Edy Settlement', 'net-shop-admin')
					);
		
		foreach($telecom_settlement as $key => $val) :
?>
<option value="<?php echo $key; ?>" <?php selected($options['payment_options'][$i]['telecom_settlement'], $key); ?>><?php echo esc_attr($val); ?></option>
<?php
		endforeach;
?>
</select></label></p>
<?php
	}
	
	function nsa_telecom_submit() {
		global $net_shop_admin, $current_user, $order_management;
		$options = get_option('net_shop_admin');
		
		switch ( $options['payment_options'][$_SESSION['net-shop-admin']['payment_method']]['telecom_settlement'] ) :
			case 'auth' :
				$telecom_url = 'https://secure.telecomcredit.co.jp/inetcredit/secure/auth-order.pl';
				break;
			case 'cti' :
				$telecom_url = 'https://total.telecomcredit.co.jp/secure/securable.cgi';
				break;
			case 'netbank' :
				$telecom_url = 'https://secure.telecomcredit.co.jp/banktransfer/settle/transfer_request.pl';
				break;
			case 'edy' :
				$telecom_url = 'https://secure.telecomcredit.co.jp/payment/edy/order.pl';
				break;
			default:
				$telecom_url = 'https://secure.telecomcredit.co.jp/inetcredit/secure/order.pl';
				break;
		endswitch;
		
		foreach($_SESSION['net-shop-admin']['shopping_cart'] as $key => $val) :
			for($i=0;$i<count($options['settlement_module_options']['telecom']['purchase_product_id']);$i++) :
				if ( $options['settlement_module_options']['telecom']['purchase_product_id'][$i] == $val['product_data']['product_id'] || $options['settlement_module_options']['telecom']['cancel_product_id'][$i] == $val['product_data']['product_id'] ) :
					if ( $options['settlement_module_options']['telecom']['purchase_product_id'][$i] == $val['product_data']['product_id'] ) :
						$rebill_param_id = $options['settlement_module_options']['telecom']['rebill_param_id'][$i];
					elseif ( $options['settlement_module_options']['telecom']['cancel_product_id'][$i] == $val['product_data']['product_id'] ) :
						$product_id = $options['settlement_module_options']['telecom']['purchase_product_id'][$i];
						$taikai = 1;
					endif;
				endif;
			endfor;
		endforeach;
		
		if ( $taikai ) :
?>
<form action="" method="post" id="thank_you">
<p class="textRight"><input type="submit" name="wp-submit" value="<?php _e('Place Order &raquo;', 'net-shop-admin'); ?>" class="submit"<?php if( $options['global_settings']['disable_order'] && $current_user->wp_user_level < 10 ) echo ' disabled="disabled"'; ?> /></p>
<input type="hidden" name="action" value="settlement" />
<input type="hidden" name="withdrawal" value="1" />
<input type="hidden" name="product_id" value="<?php echo $product_id; ?>" />
</form>
<?php
		else :
		?>
<form action="<?php echo esc_attr($telecom_url); ?>" method="post" id="thank_you">
<p class="textRight"><input type="submit" name="wp-submit" id="wp-submit" value="<?php _e('Place Order &raquo;', 'net-shop-admin'); ?>" class="submit"<?php if( !empty($options['global_settings']['disable_order']) && $current_user->wp_user_level < 10 ) echo ' disabled="disabled"'; ?> /></p>
<input type="hidden" name="clientip" value="<?php echo esc_attr($options['settlement_module_options']['telecom']['clientip']); ?>" />
<?php
		if ( is_user_logged_in() ) :
?>
<input type="hidden" name="sendid" value="<?php echo substr($_SESSION['net-shop-admin']['order_contact_id'], 0, 16); ?>" />
<?php
		endif;
?>
<input type="hidden" name="money" value="<?php echo esc_attr($_SESSION['net-shop-admin']['total']); ?>" />
<input type="hidden" name="usrtel" value="<?php echo esc_attr(str_replace('-','',$_SESSION['net-shop-admin']['billing_information']['tel'])); ?>" />
<input type="hidden" name="usrmail" value="<?php echo esc_attr($_SESSION['net-shop-admin']['billing_information']['email']); ?>" />
<?php
		if ( $rebill_param_id ) :
?>
<input type="hidden" name="rebill_param_id" value="<?php echo esc_attr($rebill_param_id); ?>" />
<?php
		endif;
		
		if ( $options['payment_options'][$_SESSION['net-shop-admin']['payment_method']]['telecom_settlement'] == 'netbank' ) :
?>
<input type="hidden" name="redirect_back_url" value="<?php echo esc_attr($options['global_settings']['shopping_cart_url'].'?action=settlement&order_contact_id='.$_SESSION['net-shop-admin']['order_contact_id']); ?>" />
<?php
		else :
?>
<input type="hidden" name="redirect_url" value="<?php echo esc_attr($options['global_settings']['shopping_cart_url'].'?action=settlement&order_contact_id='.$_SESSION['net-shop-admin']['order_contact_id']); ?>" />
<?php
		endif;
?>
<input type="hidden" name="option" value="<?php echo $_SESSION['net-shop-admin']['order_contact_id']; ?>" />
<?php
		if ( function_exists('is_ktai') && is_ktai() ) :
?>
<input type="hidden" name="i" value="on" />
<?php
		endif;
?>
<?php /*
<input type="hidden" name="testmode" value="on" />
*/ ?>
</form>
<?php

			if ( method_exists($order_management, 'insert_order_unconfirmed') ) :
				$order_management->insert_order_unconfirmed(array('order_contact_id'=>$_SESSION['net-shop-admin']['order_contact_id'], 'user_id'=>$_SESSION['net-shop-admin']['user_id'], 'unconfirmed_module'=>'telecom', 'unconfirmed_value'=>maybe_serialize($_SESSION['net-shop-admin'])));
			else :
				$tmp_data = get_option('nsa_telecom');
				$_SESSION['net-shop-admin']['current_time'] = date_i18n('U');
				$tmp_data[$_SESSION['net-shop-admin']['order_contact_id']] = $_SESSION['net-shop-admin'];
				update_option('nsa_telecom', $tmp_data);
			endif;

		endif;
	}

	function nsa_telecom_withdrawal() {
		global $current_user;
		$options = get_option('net_shop_admin');

		$response = file_get_contents('https://secure.telecomcredit.co.jp/inetcredit/secure/member.pl?clientip='.$options['settlement_module_options']['telecom']['clientip'].'&member_id='.$current_user->{'telecom_'.$_POST['product_id']}.'&password=NA&mode=link');

		if ( $response == 'OK' ) :
			$_SESSION['net-shop-admin']['status'] = 1;
			delete_user_meta($_SESSION['net-shop-admin']['user_id'], 'telecom_'.$_POST['product_id'], $current_user->{'telecom_'.$_POST['product_id']});
		else :
			$_SESSION['net-shop-admin']['err_msg'] = __('Failed to process.', 'net-shop-admin');
			$_SESSION['net-shop-admin']['status'] = 0;
		endif;
		
		return;
	}
	
	function nsa_telecom_ipn() {
		global $order_management;
		$options = get_option('net_shop_admin');
		if ( !method_exists($order_management, 'select_order_unconfirmed') ) $tmp_data = get_option('nsa_telecom');

		echo 'SuccessOK';

		$ips = array('117.102.215.161','117.102.215.162','117.102.215.163','117.102.215.164','117.102.215.165','117.102.215.166','117.102.215.167','117.102.215.168','117.102.215.169','117.102.215.170','117.102.215.171','117.102.215.172','117.102.215.173','117.102.215.174','123.50.201.57','123.50.201.58','123.50.201.59','123.50.201.60','123.50.201.61','123.50.201.62','203.191.240.81','203.191.240.82','203.191.240.83','203.191.240.84','203.191.240.85','203.191.240.86','203.191.240.87','203.191.240.88','203.191.240.89','203.191.240.90','203.191.240.91','203.191.240.92','203.191.240.93','203.191.240.94','203.191.250.65','203.191.250.66','203.191.250.67','203.191.250.68','203.191.250.69','203.191.250.70','203.191.250.71','203.191.250.72','203.191.250.73','203.191.250.74','203.191.250.75','203.191.250.76','203.191.250.77','203.191.250.78','203.191.250.79','203.191.250.80','203.191.250.81','203.191.250.82','203.191.250.83','203.191.250.84','203.191.250.85','203.191.250.86','203.191.250.87','203.191.250.88','203.191.250.89','203.191.250.90','203.191.250.91','203.191.250.92','203.191.250.93','203.191.250.94');

		if ( (!empty($_GET['option']) || !empty($_GET['sendid'])) && in_array($_SERVER['REMOTE_ADDR'], $ips) ) :
			if ( method_exists($order_management, 'select_order_unconfirmed') ) :
				$_SESSION['net-shop-admin'] = $order_management->select_order_unconfirmed(array('order_contact_id'=>$_GET['option'], 'direct'=>1));
			else :
				$_SESSION['net-shop-admin'] = $tmp_data[$_GET['option']];
			endif;
			
			if ( !empty($_SESSION['net-shop-admin']) ) :
				if ( $_SESSION['net-shop-admin']['total'] != $_GET['money'] || $_GET['rel'] == 'no' ) :
					$_SESSION['net-shop-admin']['err_msg'] = __('Failed to process.', 'net-shop-admin');
					$_SESSION['net-shop-admin']['status'] = 0;
				else :
					$_SESSION['net-shop-admin']['status'] = 1;
					$_SESSION['net-shop-admin']['ipn'] = 1;
					if ( !empty($_GET['rebill_param_id'])  ) :
						foreach($_SESSION['net-shop-admin']['shopping_cart'] as $key => $val) :
							for($i=0;$i<count($options['settlement_module_options']['telecom']['purchase_product_id']);$i++) :
								if ( $options['settlement_module_options']['telecom']['rebill_param_id'][$i] == $_GET['rebill_param_id'] ) :
									$product_id = $options['settlement_module_options']['telecom']['purchase_product_id'][$i];
									break;
								endif;
							endfor;
						endforeach;
						add_user_meta($_SESSION['net-shop-admin']['user_id'], 'telecom_'.$product_id, $_GET['sendid']);
					endif;
				endif;
				if ( method_exists($order_management, 'delete_order_unconfirmed') ) :
					$order_management->delete_order_unconfirmed(array('order_contact_id'=>$_GET['option']));
				else :
					unset($tmp_data[$_GET['option']]);
					update_option('nsa_telecom', $tmp_data);
				endif;
			else :
				list($result, $supplement) = $order_management->select_order_management_data(array('q' => $_GET['sendid'], 't' => 'order_contact_id'));
				if ( $result[0] ) :
					for($i=0;$i<count($options['settlement_module_options']['telecom']['purchase_product_id']);$i++) :
						for($j=0;$j<count($result[0]['order_detail'][0]['order_product']);$j++) :
							if ( $options['settlement_module_options']['telecom']['purchase_product_id'][$i] == $result[0]['order_detail'][0]['order_product'][$j]['product_id'] ) :
								if ( $_GET['rel'] == 'yes' ) :
									$user_id = $result[0]['user_id'];
									eval($options['settlement_module_options']['telecom']['payment_code'][$i]);
									exit();
								elseif ( $_GET['rel'] == 'no' ) :
									$user_id = $result[0]['user_id'];
									eval($options['settlement_module_options']['telecom']['failed_code'][$i]);
									exit();
								endif;
							endif;
						endfor;
					endfor;
					exit();
				endif;
			endif;
		else :
			$_SESSION['net-shop-admin']['err_msg'] = __('Failed to process.', 'net-shop-admin');
			$_SESSION['net-shop-admin']['status'] = 0;
		endif;

		return;
	}
}
?>
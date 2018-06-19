<?php

/**
 * Description of class
 *
 * @author nur
 */
require_once(SWPM_FORM_BUILDER_PATH . 'classes/class.swpm-fb-form-custom.php');

class SwpmFbForm extends SwpmFbFormCustom {
   
    private $fatal;
    private $data;
    private $member_info;
    private $membership_level_info;
    private $required_fields_count;

    public function __construct() {
        $this->fatal = array();
        $this->data = array();
        $this->member_info = new stdClass();
        $this->required_fields_count = 0;
        parent::__construct();
    }

    public function init_by_id($form_id) {
        if (SwpmUtils::is_paid_registration()) {
            $this->member_info = SwpmUtils::get_paid_member_info();
            if (empty($this->member_info)) {
                $this->error[] = SwpmUtils::_('Error! Invalid Request. Could not find a match for the given security code and the user ID.');
            } else {
                $membership_level = $this->member_info->membership_level;
            }
        }

        if (empty($membership_level)) {
            $membership_level = SwpmUtils::get_free_level();
            if (empty($membership_level)) {
                $joinuspage_link = '<a href="' . SwpmSettings::get_instance()->get_value('join-us-page-url') . '">Join us</a>';
                $this->fatal[] = SwpmUtils::_('Free membership is disabled on this site. Please make a payment from the ' . $joinuspage_link . ' page to pay for a premium membership.');
                return;
            }
        }
        if (!empty($membership_level)) {
            $this->membership_level_info = SwpmPermission::get_instance($membership_level);
            $this->init($form_id);
        }
    }

    public function init_by_level_for_registration($membership_level) {

        //First check if paid level or free level registration
        if (SwpmUtils::is_paid_registration()) {
            //This is a paid membership registration. Lets retrieve the paid level from the user's profile.
            $this->member_info = SwpmUtils::get_paid_member_info();
            if (empty($this->member_info)) {
                $this->error[] = SwpmUtils::_('Error! Invalid Request. Could not find a match for the given security code and the user ID.');
            } else {
                $membership_level = $this->member_info->membership_level;
            }
        }      
        
        //Lets check and make sure the that the given membership level exists in the database. This check needs to happens AFTER the paid level data has been retrieved.
        if(!SwpmUtils::membership_level_id_exists($membership_level)){
            wp_die('Error! Invalid membership level ID! This membership level does not exist. You may have deleted this level. Check the membership level ID and correct it.');
        }        
        
        $this->membership_level_info = SwpmPermission::get_instance($membership_level);
        $level = $this->membership_level_info->get('id');

        $form_id = SwpmFbFormmeta::get_registration_form_id_by_level_or_default($level);
        if (empty($form_id)) {
            $this->fatal[] = SwpmUtils::_('Error! Form not found.');
            return;
        }

        $this->init($form_id);
    }

    public function get_level_info($key) {
        return $this->membership_level_info->get($key);
    }

    public function init_by_level_for_profile() {       
        $auth = BAuth::get_instance();
        if (!$auth->is_logged_in()) {
            $this->fatal[] = SwpmUtils::_('Please login to edit profile.');
        }

        $membership_level = $auth->get('membership_level');
        $this->membership_level_info = SwpmPermission::get_instance($membership_level);
        $form_id = SwpmFbFormmeta::get_profile_form_id_by_level_or_default($this->membership_level_info->get('id'));
        parent::init($auth->get('member_id')); // load custom fields when editing profile.
        if (empty($form_id)) {
            $this->fatal[] = SwpmUtils::_('Form Build Error! Form not found.');
            return;
        }
        $this->init($form_id);
        $this->member_info = $auth->userData;
    }

    public function init($form_id) {
        parent::load_form($form_id);
        return true;
    }

    public function is_valid() {
        if (!SwpmFbForm::is_form_submitted()) {
            return true;
        }
        if ($this->formmeta->type == SwpmFbForm::REGISTRATION && $this->required_fields_count != 3) {
            return false;
        }
        return empty($this->error);
    }

    public function get_field_value($field) { 
        if (isset($_POST['swpm-' . $field->id])) {
            return $_POST['swpm-' . $field->id];
        }
        $key = str_replace('-', '_', $field->key);
        if ($this->formmeta->type == self::REGISTRATION) {
            if ($key == 'membership_level') {
                return $this->membership_level_info->get('id');
            }
            if ($key == 'primary_address') {
                return array('address' => '',
                    'address-2' => '',
                    'city' => '',
                    'state' => '',
                    'zip' => '',
                    'country' => $field->default
                );
            }            
        } 
        if ($this->formmeta->type == self::PROFILE) {
            $id = $field->get_unique_value_id();
            if ($key == 'custom' && isset($this->custom_info[$id])) {
                if ($field->type == 'address') {
                    return json_decode($this->custom_info[$id]->value, true);
                }
                return $this->custom_info[$id]->value;
            }
        
            if ($key == 'primary_address') {
                $street = isset($this->member_info->address_street)? $this->member_info->address_street:"";
                $city = isset($this->member_info->address_city)? $this->member_info->address_city:"";
                $state = isset($this->member_info->address_state)? $this->member_info->address_state:"";
                $zip = isset($this->member_info->address_zipcode)? $this->member_info->address_zipcode:"";
                $country = isset($this->member_info->country)? $this->member_info->country:"";
                $address = explode(',', $street);
                return array('address' => $address[0],
                    'address-2' => isset($address[1]) ? $address[1] : '',
                    'city' => $city,
                    'state' => $state,
                    'zip' => $zip,
                    'country' => $country
                );
            }
        }
        if (isset($this->member_info->{$field->type})) {
            return $this->member_info->{$field->type};
        }
        if (isset($this->member_info->{$key})) {
            return $this->member_info->{$key};
        }        
        return '';
    }

    public function process() {
        if ($this->is_fatal()) {
            return; // if already got fatal error.
        }
        // mandatory fields
        foreach ($this->formmeta->fields as $field) {
            $key = str_replace('-', '_', $field->key);
            $type = str_replace('-', '_', $field->type);
            if (method_exists($this, $key)) {
                $this->$key($field);
            } else if (method_exists($this, $type)) {
                $this->$type($field);
            }
        }
        if ($this->spam_score > self::SPAM_SENSITIVITY) {
            $this->error["Spam Words"] = SwpmUtils::_(' Information You submitted contains too many spam word. Cannot continue.');
        }
        /* if ($this->is_valid()){
          $this->save();
          return true;
          }
          return false; */
    }

    private function password($meta) {
        if ($this->formmeta->type == self::REGISTRATION) {
            $this->required_fields_count += 1;
        }
        $value = filter_input(INPUT_POST, 'swpm-' . $meta->id);
        if (empty($value)) {
            if ($this->formmeta->type == self::PROFILE) {
                return;
            }
            $this->error[$meta->id] = SwpmUtils::_($meta->name . ' Field is required');
            return;
        }
        $value_re = filter_input(INPUT_POST, 'swpm-' . $meta->id . '_re');
        if ($value == $value_re) {
            include_once(ABSPATH . WPINC . '/class-phpass.php');
            $wp_hasher = new PasswordHash(8, TRUE);
            //$this->sanitized['plain_password'] = $password;
            $this->data['password'] = $wp_hasher->HashPassword(trim($value));
            $this->data['plain_password'] = $value;
            //$this->data['password'] = sanitize_text_field($value);
            return;
        }
        $this->error[$meta->name] = SwpmUtils::_($meta->name . SwpmUtils::_(' Password does not match'));
    }

    private function user_name($meta) {
        $value = filter_input(INPUT_POST, 'swpm-' . $meta->id);
        if ($this->formmeta->type == SwpmFbForm::REGISTRATION) {
            if (empty($value)) {
                $this->error[$meta->id] = SwpmUtils::_($meta->name . ' Field is required');
                return;
            }
            if (preg_match("/^[a-zA-Z0-9!@#$%&+\/=?^_`{|}~\.-]+$/", $value) === 0) {
                $this->error[$meta->id] = SwpmUtils::_($meta->name . SwpmUtils::_(' Field has invalid character'));
                return;
            }
            $this->required_fields_count += 1;
            global $wpdb;
            $query = $wpdb->prepare('SELECT 1 as yes FROM ' . $wpdb->prefix . 'swpm_members_tbl WHERE user_name=%s', $value);

            $exists = $wpdb->get_var($query);
            if (!empty($exists)) {
                $this->error[$meta->id] = $value . ' ' . SwpmUtils::_('Already taken.');
                return;
            }
            $this->data['user_name'] = sanitize_text_field($value);
            $this->spam_score += SwpmFbUtils::calculate_spam_score($value);
        }
    }

    private function first_name($meta) {
        $value = filter_input(INPUT_POST, 'swpm-' . $meta->id);
        if ($meta->required == 'yes' && empty($value)) {
            $this->error[$meta->id] = SwpmUtils::_($meta->name . ' Field is required');
            return;
        }
        $this->data['first_name'] = sanitize_text_field($value);
        $this->spam_score += SwpmFbUtils::calculate_spam_score($value);
    }

    private function last_name($meta) {
        $value = filter_input(INPUT_POST, 'swpm-' . $meta->id);
        if ($meta->required == 'yes' && empty($value)) {
            $this->error[$meta->id] = SwpmUtils::_($meta->name . ' Field is required');
            return;
        }
        $this->data['last_name'] = sanitize_text_field($value);
        $this->spam_score += SwpmFbUtils::calculate_spam_score($value);
    }

    private function gender($meta) {
        $value = filter_input(INPUT_POST, 'swpm-' . $meta->id);
        if ($meta->required == 'yes' && empty($value)) {
            $this->error[$meta->id] = SwpmUtils::_($meta->name . ' Field is required');
            return;
        }
        $this->data['gender'] = strtolower(wp_kses_data($value));
        $this->spam_score += SwpmFbUtils::calculate_spam_score($value);
    }

    private function title($meta) {
        $value = filter_input(INPUT_POST, 'swpm-' . $meta->id);
        if ($meta->required == 'yes' && empty($value)) {
            $this->error[$meta->id] = SwpmUtils::_($meta->name . ' Field is required');
            return;
        }
        $this->data['title'] = strtolower(wp_kses_data($value));
        $this->spam_score += SwpmFbUtils::calculate_spam_score($value);
    }

    private function primary_address($meta) {
        if (isset($_POST['swpm-' . $meta->id])) {
            $address = $_POST['swpm-' . $meta->id];
            $allowed_html = array('br' => array());
            $this->data['address_street'] = wp_kses($address['address'] . ', ' . $address['address-2'], $allowed_html);
            $this->data['address_city'] = wp_kses($address['city'], $allowed_html);
            $this->data['address_state'] = wp_kses($address['state'], $allowed_html);
            $this->data['address_zipcode'] = wp_kses($address['zip'], $allowed_html);
            $this->data['country'] = wp_kses($address['country'], $allowed_html);

            if (($meta->required == 'yes') && (empty($this->data['address_street']) || empty($this->data['address_city']) || empty($this->data['country']))) {
                $this->error[$meta->id] = SwpmUtils::_(' Address, City, Country  cannot be empty.');
            }
            //todo: spam score
        } else if ($meta->required == 'yes') {
            $this->error[$meta->id] = SwpmUtils::_($meta->name . ' Field is required');
        }
    }

    private function primary_phone($meta) {
        $value = filter_input(INPUT_POST, 'swpm-' . $meta->id);
        if ($meta->required == 'yes' && empty($value)) {
            $this->error[$meta->id] = SwpmUtils::_($meta->name . ' Field is required');
            return;
        }
        $pattern = '/^((\+)?[1-9]{1,2})?([-\s\.])?((\(\d{1,4}\))|\d{1,4})(([-\s\.])?[0-9]{1,12}){1,2}$/';
        if( !empty($value) && (strlen($value) <= 6 || !preg_match($pattern, $value))) {
            $this->error[$meta->id] = $meta->name . SwpmUtils::_(' not a valid phone number');
            return;
        }
        $this->data['phone'] = wp_kses_data($value);
        $this->spam_score += SwpmFbUtils::calculate_spam_score($value);
    }

    private function company_name($meta) {
        $value = filter_input(INPUT_POST, 'swpm-' . $meta->id);
        if ($meta->required == 'yes' && empty($value)) {
            $this->error[$meta->id] = SwpmUtils::_($meta->name . ' Field is required');
            return;
        }

        $this->data['company_name'] = wp_kses_data($value);
        $this->spam_score += SwpmFbUtils::calculate_spam_score($value);
    }
    
    private function primary_email($meta) {
        if ($this->formmeta->type == self::REGISTRATION) {
            $this->required_fields_count += 1;
        }
        $value = filter_input(INPUT_POST, 'swpm-' . $meta->id);
        if (empty($value)) {
            $this->error[$meta->id] = SwpmUtils::_($meta->name . ' Field is required');
            return;
        }
        if (!is_email($value)) {
            $this->error[$meta->id] = $value . ' ' . SwpmUtils::_('Invalid email.');
            return;
        }

        if ($this->formmeta->type == SwpmFbForm::PROFILE && $value == $this->member_info->email) {
            return; // same email address. doesn't require storing in db again.
        }
        global $wpdb;
        $query = $wpdb->prepare('SELECT email FROM ' . $wpdb->prefix . "swpm_members_tbl WHERE email=%s AND user_name != ''", $value);
        $exists = $wpdb->get_var($query);
        if (!empty($exists)) {
            $this->error[$meta->id] = $value . ' ' . SwpmUtils::_('Already taken.');
            return;
        }
        $this->data['email'] = sanitize_email($value);
        $this->spam_score += SwpmFbUtils::calculate_spam_score($value);
    }

    private function membership_level($meta) {
        if (!is_admin() && $this->formmeta->type == SwpmFbFormCustom::PROFILE) {
            return;
        }
        if (SwpmUtils::is_paid_registration()) {
            return;
        }
        $free = SwpmUtils::get_free_level();

        if (empty($free)) {
            return;
        }

        $this->data['membership_level'] = $free;
    }

    private function profile_image($meta) {
        $value = ( isset($_FILES['swpm-' . $meta->id]) ) ? $_FILES['swpm-' . $meta->id] : '';
        if ($meta->required == 'yes') {
            if ($this->formmeta->type == self::REGISTRATION && empty($value)) {
                $this->error[$meta->id] = SwpmUtils::_($meta->name . ' Field is required');
                return;
            }
            if ($this->formmeta->type == self::PROFILE && empty($value) && empty($this->member_info->profile_image)) {
                $this->error[$meta->id] = SwpmUtils::_($meta->name . ' Field is required');
                return;
            }
        }

        if (is_array($value) && $value['size'] > 0) {
            $status = SwpmFbUtils::handle_file_upload($value);
            if (isset($status['error'])) {
                $this->error[$meta->id] = $status['error'];
                return;
            }
            $this->data['profile_image'] = $status['attachment_id'];
            if ($this->formmeta->type == self::PROFILE) {
                //delete previously stored one.
                wp_delete_attachment($this->member_info->profile_image, true);
            }
        }
    }

    public function fatal($key = '', $value = '') {
        if (empty($key)) {
            return $this->fatal;
        }
        $this->fatal[$key] = $value;
    }

    public function is_fatal() {
        return !empty($this->fatal);
    }

    public function save() {
        global $wpdb;
        $account_status = BSettings::get_instance()->get_value('default-account-status', 'active');
        $auth = SwpmAuth::get_instance();
        if ($this->formmeta->type == self::REGISTRATION) {
            $plain_password = $this->data['plain_password'];
            unset($this->data['plain_password']);
            $this->data['account_state'] = $account_status;
            if (isset($this->member_info->member_id)) {
                $this->data['reg_code'] = '';
                $wpdb->update($wpdb->prefix . "swpm_members_tbl", $this->data, array('member_id' => $this->member_info->member_id));
                $user_id = $this->member_info->member_id;
            } else {
                $this->data['member_since'] = (date("Y-m-d"));
                $this->data['subscription_starts'] = date("Y-m-d");
                $this->data['last_accessed_from_ip'] = SwpmUtils::get_user_ip_address();
                $this->data['last_accessed'] = date("Y-m-d H:i:s");
                $wpdb->insert($wpdb->prefix . "swpm_members_tbl", $this->data);
                $user_id = $wpdb->insert_id;
            }
            $query = "SELECT role FROM " . $wpdb->prefix . "swpm_membership_tbl WHERE id = " . $this->get_level_info('id');
            $wp_user_info = array();
            $wp_user_info['user_nicename'] = implode('-', explode(' ', $this->data['user_name']));
            $wp_user_info['display_name'] = $this->data['user_name'];
            $wp_user_info['user_email'] = $this->data['email'];
            $wp_user_info['nickname'] = $this->data['user_name'];
            $wp_user_info['first_name'] = isset($this->data['first_name']) ? $this->data['first_name'] : "";
            $wp_user_info['last_name'] = isset($this->data['last_name']) ? $this->data['last_name'] : "";
            $wp_user_info['user_login'] = $this->data['user_name'];
            $wp_user_info['password'] = $plain_password;
            $wp_user_info['role'] = $wpdb->get_var($query);
            $wp_user_info['user_registered'] = date('Y-m-d H:i:s');
            SwpmUtils::create_wp_user($wp_user_info);
            
            //Save some additional values in the $data variable.
            $this->data['plain_password'] = $plain_password;
            $this->data['membership_level'] = $this->get_level_info('id');
            $this->data['member_id'] = $user_id;
            $this->send_reg_email();
            
            //Trigger the registration complete action hook
            do_action('swpm_front_end_registration_complete_fb', $this->data);
        } else if (!$auth->is_logged_in()) { // must be profile edit/must be logged in.
            return;
        }

        if ($this->formmeta->type == self::PROFILE) {
            $user_id = $auth->get('member_id');
            $plain_password = '';
            if (isset($this->data['plain_password'])) {
                $plain_password = $this->data['plain_password'];
                unset($this->data['plain_password']);
            }

            if (!empty($this->data)) {

                $wpdb->update($wpdb->prefix . "swpm_members_tbl", $this->data, array('member_id' => $user_id));
                $wp_data = $this->data;
                if (!empty($plain_password)) {
                    $wp_data['plain_password'] = $plain_password;
                }
                SwpmUtils::update_wp_user($auth->get('user_name'), $wp_data);

                $wp_user = get_user_by('login', $auth->get('user_name'));
                if ($wp_user) {
                    SwpmUtils::update_wp_user_Role($wp_user->ID, $auth->get('role'));
                }
                
                //Trigger the profile edited action hook
                do_action('swpm_front_end_profile_edited_fb', $this->data);                
            }
        }
        
        parent::init($user_id); // load custom fields.
        parent::save(); // save custom fields
    }

    protected function send_reg_email() {
        global $wpdb;
        if (empty($this->data)) {
            return false;
        }
        $member_info = $this->data;
        $settings = SwpmSettings::get_instance();
        $subject = empty($this->formmeta->notification_setting) ?
                $settings->get_value('reg-complete-mail-subject') : stripslashes($this->formmeta->notification_subject);

        $body = empty($this->formmeta->notification_setting) ?
                $settings->get_value('reg-complete-mail-body') : stripslashes(html_entity_decode(wp_kses_stripslashes(($this->formmeta->notification_message))));
        $from_address = empty($this->formmeta->notification_setting) ?
                $settings->get_value('email-from') : stripslashes($this->formmeta->notification_email_name);

        $login_link = $settings->get_value('login-page-url');
        $headers = 'From: ' . $from_address . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        if (!empty($this->formmeta->notification_setting)) {
            $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
        }
        $query = "SELECT alias FROM " . $wpdb->prefix . "swpm_membership_tbl WHERE id = " . $this->get_level_info('id');
        $member_info['membership_level_name'] = $wpdb->get_var($query);
        $member_info['password'] = $member_info['plain_password'];
        $member_info['login_link'] = $login_link;
        
        $values = array_values($member_info);
        $keys = array_map('swpm_enclose_var', array_keys($member_info));
        $body = str_replace($keys, $values, $body);
        
        if(method_exists("SwpmMiscUtils", "replace_dynamic_tags")){
            $member_id = $member_info['member_id'];
            $body = SwpmMiscUtils::replace_dynamic_tags($body, $member_id);//Do the standard merge var replacement.
        }
        
        //Add the raw custom fields data to the email (if the merge tag is present).
        $custom_fields_arr = $this->custom;
        $custom_field_values = array_values($custom_fields_arr);
        $custom_fields_string = print_r($custom_field_values, true);
        $body = str_replace('{raw_custom_fields}', $custom_fields_string, $body);
        
        //Send the member notification email.
        $email = sanitize_email($this->formmeta->type == self::REGISTRATION ? $this->data['email'] : $this->member_info->email);
        // if (!empty($this->formmeta->notification_setting)) {
        wp_mail(trim($email), $subject, $body, $headers);
        SwpmLog::log_simple_debug('Form builder addon - registration complete email sent to: '.$email.'. From Email Address value used: '.$from_address, true);
        // }

        if ($settings->get_value('enable-admin-notification-after-reg')) {
            $to_email_address = $settings->get_value('admin-notification-email');
            $admin_notification = empty($to_email_address) ? $from_address : $to_email_address;
            $notify_emails_array = explode(",", $admin_notification);
            
            $headers = 'From: ' . $from_address . "\r\n";
            $subject = "Notification of New Member Registration";
            
            $admin_notify_body = $settings->get_value('reg-complete-mail-body-admin');
            if(empty($admin_notify_body)){
                $admin_notify_body = "A new member has completed the registration.\n\n" .
                "Username: {user_name}\n" .
                "Email: {email}\n\n" .
                "Please login to the admin dashboard to view details of this user.\n\n" .
                "You can customize this email message from the Email Settings menu of the plugin.\n\n" .
                "Thank You";                        
            }
            $admin_notify_body = SwpmMiscUtils::replace_dynamic_tags($admin_notify_body, $member_id);//Do the standard merge var replacement.
            $admin_notify_body = str_replace('{raw_custom_fields}', $custom_fields_string, $admin_notify_body);//Do the raw custom fields merge var replacement.
            
            foreach ($notify_emails_array as $to_email){
                $to_email = trim($to_email);
                wp_mail($to_email, $subject, $admin_notify_body, $headers);
                if(method_exists("SwpmLog", "log_simple_debug")){
                    SwpmLog::log_simple_debug('Form builder addon - admin notification email sent to: '.$to_email, true);
                }
            }
        }
        return true;
    }

    public function validate_and_save() {
        $required = ( isset($_POST['_swpm-required-secret']) && $_POST['_swpm-required-secret'] == '0' ) ? false : true;
        $secret_field = ( isset($_POST['_swpm-secret']) ) ? esc_html($_POST['_swpm-secret']) : '';
        $honeypot = ( isset($_POST['swpm-spam']) ) ? esc_html($_POST['swpm-spam']) : '';
        $referrer = ( isset($_POST['_wp_http_referer']) ) ? esc_html($_POST['_wp_http_referer']) : false;
        $wp_get_referer = wp_get_referer();
        if (true == $required && !empty($secret_field)) {
            if (!empty($honeypot)) {
                $this->fatal[] = SwpmUtils::_('Security check: hidden spam field should be blank.');
            }
            if (!is_numeric($_POST[$secret_field]) || strlen($_POST[$secret_field]) !== 2) {
                $this->fatal[] = SwpmUtils::_('Security check: failed secret question. Please try again!');
            }
        }


        //Verify recaptcha if used
        //If captcha is present and validation failed, it returns an error string. If validation succeeds, it returns an empty string.
        $captcha_validation_output = apply_filters('swpm_validate_registration_form_submission', '');
        if (!empty($captcha_validation_output)) {
            $this->fatal('security', SwpmUtils::_('Security check: captcha validation failed.'));
            return;
        }

        // Tells us which form to get from the database
        $form_id = absint($_POST['form_id']);
        $this->init_by_id($form_id);
        if ($this->formmeta->type == SwpmFbForm::PROFILE) {
            $this->init_by_level_for_profile();
        }
        $skip_referrer_check = apply_filters('swpm_skip_referrer_check', false, $form_id);

        // Test if referral URL has been set
        if (!$referrer) {
            $this->fatal('security', SwpmUtils::_('Security check: referal URL does not appear to be set.'));
        }
        // Allow referrer check to be skipped
        if (!$skip_referrer_check) {
            // Test if the referral URL matches what sent from WordPress
            if ($wp_get_referer) {
                $this->fatal('security', SwpmUtils::_('Security check: referal does not match this site.'));
            }
        }

        // Test if it's a known SPAM bot
        if (SwpmFbUtils::isBot()) {
            $this->fatal('Spam Words', SwpmUtils::_('Security check: looks like you are a SPAM bot. If you think this is an error, please email the site owner.'));
            return;
        }

        if (!$this->is_fatal()) {
            $this->process();
            if ($this->is_valid()) {
                $this->save();
            }
        }
    }

    public static function is_form_submitted() {
        return isset($_POST['swpm-fb-submit']);
    }

}

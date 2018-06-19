<?php

/*
  Plugin Name: SWPM Show Member Info
  Description: Simple Membership extension for showing various member info on your site using shortcodes
  Plugin URI: https://simple-membership-plugin.com/
  Author: smp7, wp.insider
  Author URI: https://simple-membership-plugin.com/
  Version: 1.2
 */

//Slug - swpm_smi
//Direct access to this file is not permitted
if (!defined('ABSPATH')) {
    exit; //Exit if accessed directly
}

add_shortcode("swpm_show_member_info", "swpm_smi_show_member_info");

function swpm_smi_show_member_info($args) {
    extract(shortcode_atts(array(
        'column' => '',
        'member_id' => '',
                    ), $args));

    //Check column name
    if (empty($column)) {
        return '<div style="color: red;">Error! This shortcode requires a value for the "column" field</div>';
    }

    //Check member_id
    if (empty($member_id)) {
        //Show info of the currently logged in member. Lets get the currently logged-in member's ID.
        $member_id = SwpmMemberUtils::get_logged_in_members_id();
    } else {
        //Show info of the given member ID.
    }

    $column_value = swpm_smi_get_member_info_by_id($column, $member_id);
    return $column_value;
}

function swpm_smi_get_member_info_by_id($column, $member_id) {
    global $wpdb;
    $query = "SELECT * FROM " . $wpdb->prefix . "swpm_members_tbl WHERE member_id = %d";
    $userData = $wpdb->get_row($wpdb->prepare($query, $member_id));
    if (isset($userData->$column)) {
        return $userData->$column;
    }

    //There is not core field withthe given column name.
    //Lets check if form builder is being used then retrieve custom field data.
    $custom_field_value = swpm_smi_get_custom_field_info_by_id($column, $member_id);

    return $custom_field_value;
}

function swpm_smi_get_custom_field_info_by_id($column, $member_id) {
    if (!class_exists('Swpm_Form_Builder')) {
        //Not using the form builder. Nothing to retrieve.
        return '';
    }

    //Read the custom field value.
    global $wpdb;
    $sql = $wpdb->prepare("SELECT value FROM " . $wpdb->prefix . "swpm_form_builder_custom C
        INNER JOIN " . $wpdb->prefix . "swpm_form_builder_fields  F ON (C.field_id = F.field_id)
        WHERE C.user_id=%d AND F.Field_name=%s", $member_id, $column);

    $value = $wpdb->get_var($sql);
    return empty($value) ? "" : $value;
}

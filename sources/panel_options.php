<?php

/*
	Copyright (C) 2003-2004 UseBB Team
	http://www.usebb.net
	
	$Header$
	
	This file is part of UseBB.
	
	UseBB is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	UseBB is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with UseBB; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

//
// Die when called directly in browser
//
if ( !defined('INCLUDED') )
	exit();

//
// Set the page title
//
$template->set_page_title($lang['EditOptions']);

if ( !empty($_POST['submitted']) ) {
	
	//
	// Update the user's preferences
	//
	
	$_POST['language'] = ( !empty($_POST['language']) && in_array($_POST['language'], $functions->get_config('available_languages')) ) ? $_POST['language'] : $functions->get_config('language');
	$_POST['template'] = ( !empty($_POST['template']) && in_array($_POST['template'], $functions->get_config('available_templates')) ) ? $_POST['template'] : $functions->get_config('template');
	$_POST['email_show'] = ( !empty($_POST['email_show']) ) ? 1 : 0;
	$_POST['last_login_show'] = ( !empty($_POST['last_login_show']) ) ? 1 : 0;
	$_POST['date_format'] = ( !empty($_POST['date_format']) ) ? $_POST['date_format'] : $functions->get_config('date_format');
	$_POST['timezone'] = ( is_numeric($_POST['timezone']) && $functions->timezone_handler('check_existance', $_POST['timezone']) ) ? $_POST['timezone'] : $functions->get_config('timezone');
	$_POST['dst'] = ( !empty($_POST['dst']) ) ? 1 : 0;
	$_POST['quickreply'] = ( !empty($_POST['quickreply']) ) ? 1 : 0;
	$_POST['return_to_topic'] = ( !empty($_POST['return_to_topic']) ) ? 1 : 0;
	$_POST['target_blank'] = ( !empty($_POST['target_blank']) ) ? 1 : 0;
	
	if ( !($result = $db->query("UPDATE ".TABLE_PREFIX."members SET
		language        = '".$_POST['language']."',
		template        = '".$_POST['template']."',
		email_show      = ".$_POST['email_show'].",
		last_login_show = ".$_POST['last_login_show'].",
		date_format     = '".$_POST['date_format']."',
		timezone	= '".$_POST['timezone']."',
		dst		= ".$_POST['dst'].",
		enable_quickreply		= ".$_POST['quickreply'].",
		return_to_topic_after_posting		= ".$_POST['return_to_topic'].",
		target_blank		= ".$_POST['target_blank']."
	WHERE id = ".$session->sess_info['user_info']['id'])) )
		$functions->usebb_die('SQL', 'Unable to update user information!', __FILE__, __LINE__);
	
	$template->parse('msgbox', 'global', array(
		'box_title' => $lang['Note'],
		'content' => $lang['OptionsEdited']
	));
	
} else {
	
	if ( count($functions->get_config('available_languages')) < 2 ) {
		
		$single_language = $functions->get_config('available_languages');
		$language_input = $single_language[0];
		
	} else {
		
		$language_input = '<select name="language">';
		foreach ( $functions->get_config('available_languages') as $single_language ) {
			
			$selected = ( $functions->get_config('language') == $single_language ) ? ' selected="selected"' : '';
			$language_input .= '<option value="'.$single_language.'"'.$selected.'>'.$single_language.'</option>';
			
		}
		
		$language_input .= '</select>';
		
	}
	
	if ( count($functions->get_config('available_templates')) < 2 ) {
		
		$single_template = $functions->get_config('available_templates');
		$template_input = $single_template[0];
		
	} else {
		
		$template_input = '<select name="template">';
		foreach ( $functions->get_config('available_templates') as $single_template ) {
			
			$selected = ( $functions->get_config('template') == $single_template ) ? ' selected="selected"' : '';
			$template_input .= '<option value="'.$single_template.'"'.$selected.'>'.$single_template.'</option>';
			
		}
		$template_input .= '</select>';
		
	}
	
	$email_show_checked = ( $session->sess_info['user_info']['email_show'] ) ? ' checked="checked"' : '';
	$last_login_show_checked = ( $session->sess_info['user_info']['last_login_show'] ) ? ' checked="checked"' : '';

	$timezone_input = '<select name="timezone">';
	foreach ( $functions->timezone_handler('get_zones') as $key => $val ) {
		
		$selected = ( $functions->get_config('timezone') == $key ) ? ' selected="selected"' : '';
		$timezone_input .= '<option value="'.$key.'"'.$selected.'>'.$val.'</option>';
		
	}
	$timezone_input .= '</select>';

	$dst_checked = ( $functions->get_config('dst') ) ? ' checked="checked"' : '';
	$quickreply_checked = ( $session->sess_info['user_info']['enable_quickreply'] ) ? ' checked="checked"' : '';
	$return_to_topic_checked = ( $session->sess_info['user_info']['return_to_topic_after_posting'] ) ? ' checked="checked"' : '';
	$target_blank_checked = ( $session->sess_info['user_info']['target_blank'] ) ? ' checked="checked"' : '';
	
	$template->parse('edit_options', 'global', array(
		'form_begin'            => '<form action="'.$functions->make_url('panel.php', array('act' => 'editoptions')).'" method="post">',
		'edit_options'          => $lang['EditOptions'],
		'language'              => $lang['Language'],
		'language_input'        => $language_input,
		'template'              => $lang['Template'],
		'template_input'        => $template_input,
		'email_show'            => $lang['PublicEmail'],
		'email_show_input'      => '<input type="checkbox" name="email_show" id="email_show" value="1"'.$email_show_checked.' /><label for="email_show"> '.$lang['Yes'].'</label>',
		'last_login_show'       => $lang['PublicLastLogin'],
		'last_login_show_input' => '<input type="checkbox" name="last_login_show" id="last_login_show" value="1"'.$last_login_show_checked.' /><label for="last_login_show"> '.$lang['Yes'].'</label>',
		'date_format'           => $lang['DateFormat'],
		'date_format_input'     => '<input type="text" name="date_format" size="25" maxlength="255" value="'.$functions->get_config('date_format').'" />',
		'timezone'              => $lang['Timezone'],
		'timezone_input'	      => $timezone_input,
		'dst'		            	=> $lang['DST'],
		'dst_input'	         	=> '<input type="checkbox" name="dst" id="dst" value="1"'.$dst_checked.' /><label for="dst"> '.$lang['Enabled'].'</label>',
		'quickreply'	   		=> $lang['QuickReply'],
		'quickreply_input'		=> '<input type="checkbox" name="quickreply" id="quickreply" value="1"'.$quickreply_checked.' /><label for="quickreply"> '.$lang['Enabled'].'</label>',
		'return_to_topic'			=> $lang['ReturnToTopicAfterPosting'],
		'return_to_topic_input'	=> '<input type="checkbox" name="return_to_topic" id="return_to_topic" value="1"'.$return_to_topic_checked.' /><label for="return_to_topic"> '.$lang['Yes'].'</label>',
		'target_blank'	         => $lang['OpenLinksNewWindow'],
		'target_blank_input'    => '<input type="checkbox" name="target_blank" id="target_blank" value="1"'.$target_blank_checked.' /><label for="target_blank"> '.$lang['Yes'].'</label>',
		'submit_button'         => '<input type="submit" name="submit" value="'.$lang['EditOptions'].'" />',
		'reset_button'          => '<input type="reset" value="'.$lang['Reset'].'" />',
		'form_end'              => '<input type="hidden" name="submitted" value="true" /></form>'
	));
	
}

?>

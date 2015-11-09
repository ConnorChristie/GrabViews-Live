<?php
/*--------------------------------------------------*/
/* FILE GENERATED BY INVISION POWER BOARD 3         */
/* CACHE FILE: Skin set id: 2               */
/* CACHE FILE: Generated: Fri, 26 Jul 2013 21:54:30 GMT */
/* DO NOT EDIT DIRECTLY - THE CHANGES WILL NOT BE   */
/* WRITTEN TO THE DATABASE AUTOMATICALLY            */
/*--------------------------------------------------*/

class skin_messaging_2 extends skinMaster{

/**
* Construct
*/
function __construct( ipsRegistry $registry )
{
	parent::__construct( $registry );
	

$this->_funcHooks = array();
$this->_funcHooks['messengerDisabled'] = array('notByAdmin');
$this->_funcHooks['messengerTemplate'] = array('userIsStarter','lastReadTime','messageIsDeleted','systemMessage','topicUnavailable','userIsBanned','userIsActive','participants','allFolder','dirs','inlineError','hasParticipants','storageBar','myDirectories');
$this->_funcHooks['sendNewPersonalTopicForm'] = array('newTopicError','formReloadInvite','formReloadCopy','newTopicInvite');
$this->_funcHooks['sendReplyForm'] = array('replyForm','formErrors','formHeaderText','replyOptions');
$this->_funcHooks['showConversation'] = array('quickReply','canEdit','canDelete','replies','canReply','allAlone','canReply','allAlone2');
$this->_funcHooks['showConversationForArchive'] = array('replies');
$this->_funcHooks['showFolder'] = array('folderUnread','folderDeleted','folderDrafts','folderStarter','folderToMember','folderFixPlural','folderMultipleUsers','folderBannedIndicator','folderMessages','folderMessages');
$this->_funcHooks['showSearchResults'] = array('searchHasUnread','searchEndHasUnread','searchFixPlural','searchInvitedCount','searchToMember','messages','searchError','hasPagination','searchMessages');


}

/* -- messengerDisabled --*/
function messengerDisabled() {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_messaging', $this->_funcHooks['messengerDisabled'] ) )
{
$count_e55a12b03b01d80a49cfafe2b60bacc6 = is_array($this->functionData['messengerDisabled']) ? count($this->functionData['messengerDisabled']) : 0;
}
$IPBHTML .= "<h3>{$this->lang->words['pm_disabled_title']}</h3>
<div class='ipsPad'>
	{$this->lang->words['your_pm_is_disabled']}
	" . (($this->memberData['members_disable_pm'] != 2) ? ("
	<p class='ipsForm_center'>
		<br />
		<a href=\"" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "module=messaging&amp;section=view&amp;do=enableMessenger&amp;authKey={$this->member->form_hash}", "publicWithApp",'' ), "", "" ) . "\" class='ipsButton_secondary'>{$this->lang->words['pm_disabled_reactivate']}</a>
		<a href='" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "act=idx", "public",'' ), "true", "act=idx" ) . "' class='ipsButton_secondary'>{$this->lang->words['go_board_index']}</a>
		</p>
	") : ("")) . "
 </div>";
return $IPBHTML;
}

/* -- messengerTemplate --*/
function messengerTemplate($html, $jumpmenu, $dirData, $totalData=array(), $topicParticipants=array(), $inlineError='', $deletedTopic=0) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_messaging', $this->_funcHooks['messengerTemplate'] ) )
{
$count_18be451f85327cc4c204eda099e1dd1d = is_array($this->functionData['messengerTemplate']) ? count($this->functionData['messengerTemplate']) : 0;
$this->functionData['messengerTemplate'][$count_18be451f85327cc4c204eda099e1dd1d]['html'] = $html;
$this->functionData['messengerTemplate'][$count_18be451f85327cc4c204eda099e1dd1d]['jumpmenu'] = $jumpmenu;
$this->functionData['messengerTemplate'][$count_18be451f85327cc4c204eda099e1dd1d]['dirData'] = $dirData;
$this->functionData['messengerTemplate'][$count_18be451f85327cc4c204eda099e1dd1d]['totalData'] = $totalData;
$this->functionData['messengerTemplate'][$count_18be451f85327cc4c204eda099e1dd1d]['topicParticipants'] = $topicParticipants;
$this->functionData['messengerTemplate'][$count_18be451f85327cc4c204eda099e1dd1d]['inlineError'] = $inlineError;
$this->functionData['messengerTemplate'][$count_18be451f85327cc4c204eda099e1dd1d]['deletedTopic'] = $deletedTopic;
}
$IPBHTML .= "<div class='master_list'>
	<!--<h2>{$this->lang->words['m_messenger']}</h2>-->
	" . (($inlineError) ? ("
		<div class='message error'>
			{$inlineError}
		</div>
	") : ("")) . "
	{$html}
	
	" . ((is_array( $topicParticipants ) and count( $topicParticipants )) ? ("
		<h3>{$this->lang->words['participants']}</h3>
		".$this->__f__4b5b7d20a27b290442658c5ec6868bb2($html,$jumpmenu,$dirData,$totalData,$topicParticipants,$inlineError,$deletedTopic)."	") : ("")) . "				
	
	" . (($this->settings['_hideFolders'] != 1) ? ("<h3>{$this->lang->words['folders']} " . (($this->memberData['g_max_messages'] > 0) ? ("<span class='subtext'>{$totalData['full_percent']}% {$this->lang->words['full']}</span>") : ("")) . "</h3>
	
		" . ((count($dirData)) ? ("
			".$this->__f__83ada9e01f773d2d9addc0369f81b654($html,$jumpmenu,$dirData,$totalData,$topicParticipants,$inlineError,$deletedTopic)."		") : ("")) . "") : ("")) . "
</div>";
return $IPBHTML;
}


function __f__4b5b7d20a27b290442658c5ec6868bb2($html, $jumpmenu, $dirData, $totalData=array(), $topicParticipants=array(), $inlineError='', $deletedTopic=0)
{
	$_ips___x_retval = '';
	$__iteratorCount = 0;
	foreach( $topicParticipants as $memberID => $memberData )
	{
		
		$__iteratorCount++;
		$_ips___x_retval .= "
			<div class='row'>
				<div class='icon'>
					<a href='" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "showuser={$memberData['member_id']}", "public",'' ), "{$memberData['members_seo_name']}", "showuser" ) . "' title='{$this->lang->words['view_profile']}' ><img src='{$memberData['pp_mini_photo']}' style='width: 30px; height: 30px;' alt='{$this->lang->words['photo']}' class='photo' /></a>
				</div>
				" . (($memberData['map_user_active']) ? ("<a href='" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "showuser={$memberData['member_id']}", "public",'' ), "{$memberData['members_seo_name']}", "showuser" ) . "' title='{$this->lang->words['view_profile']}'><strong>{$memberData['members_display_name']}</strong></a>
					" . (($memberData['map_is_starter']) ? ("
						&nbsp;&middot;&nbsp;<span class='subtext'><strong>{$this->lang->words['col_starter']}</strong></span>
					") : ("")) . "
					<br />
					<span class='desc'>
						" . (($memberData['_topicDeleted']) ? ("
							<em>{$this->lang->words['topic_deleted']}</em>
						") : ("{$this->lang->words['last_read']}
							" . (($memberData['map_read_time']) ? ("
								" . IPSText::htmlspecialchars($this->registry->getClass('class_localization')->getDate($memberData['map_read_time'],"short", 0)) . "
							") : ("
								{$this->lang->words['not_yet_read']}
							")) . "")) . "							
					</span>") : ("" . (($memberData['map_user_banned']) ? ("
						<a href='" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "showuser={$memberData['member_id']}", "public",'' ), "{$memberData['members_seo_name']}", "showuser" ) . "' title='{$this->lang->words['view_profile']}'><strong>{$memberData['members_display_name']}</strong></a>
							&nbsp;&middot;&nbsp;<span class='subtext'><strong>{$this->lang->words['blocked']}</strong></span>
						<br />
						<span class='desc'>{$this->lang->words['user_is_blocked']}</span>
					") : ("<a href='" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "showuser={$memberData['member_id']}", "public",'' ), "{$memberData['members_seo_name']}", "showuser" ) . "' title='{$this->lang->words['view_profile']}'><strong>{$memberData['members_display_name']}</strong></a>
						<br />
						<span class='desc'>
							" . (($memberData['_topicDeleted']) ? ("
								{$this->lang->words['topic_deleted']}
							") : ("" . (($memberData['map_is_system']) ? ("
									{$this->lang->words['is_unable_part']}
								") : ("
									{$this->lang->words['has_left_convo']}
								")) . "")) . "
						</span>")) . "")) . "
			</div>
		
";
	}
	$_ips___x_retval .= '';
	unset( $__iteratorCount );
	return $_ips___x_retval;
}

function __f__83ada9e01f773d2d9addc0369f81b654($html, $jumpmenu, $dirData, $totalData=array(), $topicParticipants=array(), $inlineError='', $deletedTopic=0)
{
	$_ips___x_retval = '';
	$__iteratorCount = 0;
	foreach( $dirData as $id => $data )
	{
		
		$__iteratorCount++;
		$_ips___x_retval .= "
				<div class='row'>
					<div class='right_info'>
						" . (($id == 'all') ? ("
							" . intval($this->memberData['msg_count_total']) . "
						") : ("
							" . intval($data['count']) . "
						")) . "
					</div>
					<a href=\"" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "module=messaging&amp;section=view&amp;do=showFolder&amp;folderID={$id}", "publicWithApp",'' ), "", "" ) . "\" title=\"{$this->lang->words['go_to_folder']}\" rel=\"folder_name\">{$data['real']}</a>
				</div>
			
";
	}
	$_ips___x_retval .= '';
	unset( $__iteratorCount );
	return $_ips___x_retval;
}

/* -- PMQuickForm --*/
function PMQuickForm($toMemberData) {
$IPBHTML = "";
$IPBHTML .= "<!--no data in this master skin-->";
return $IPBHTML;
}

/* -- sendNewPersonalTopicForm --*/
function sendNewPersonalTopicForm($displayData) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_messaging', $this->_funcHooks['sendNewPersonalTopicForm'] ) )
{
$count_723f42cf2ac980b227a5df1b726433ac = is_array($this->functionData['sendNewPersonalTopicForm']) ? count($this->functionData['sendNewPersonalTopicForm']) : 0;
$this->functionData['sendNewPersonalTopicForm'][$count_723f42cf2ac980b227a5df1b726433ac]['displayData'] = $displayData;
}
$IPBHTML .= "" . (($this->settings['_hideFolders'] = 1) ? ("") : ("")) . "
<form id='msgForm' class='ipsForm_vertical' action=\"" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "module=messaging&amp;section=send&amp;do=send", "publicWithApp",'' ), "", "" ) . "\" method=\"post\" enctype='multipart/form-data'>
	" . (($displayData['errors']) ? ("
		<div class='message error'>
			<strong>{$this->lang->words['err_errors']}</strong><br />
			{$displayData['errors']}<br />
			{$this->lang->words['pme_none_sent']}
		</div>
	") : ("")) . "
	
	<h3>{$this->lang->words['pro_recips']}</h3>
	<div class='ipsField'>
		<label for='entered_name' class='ipsField_title'>{$this->lang->words['to_whom']}</label>
		<input type=\"text\" class='input_text' id='entered_name' name=\"entered_name\" size=\"30\" value=\"{$displayData['name']}\" tabindex=\"0\" />
	</div>
	" . ((intval($this->memberData['g_max_mass_pm'])) ? ("<div class='ipsField'>
			<label for='ipsField_title' class='ipsField_title'>{$this->lang->words['other_recipients']}</label>
			<input type='text' size=\"30\" class='input_text' name='inviteUsers' value='{$displayData['inviteUsers']}' id='more_members' tabindex='0' /><br />
			<span class='desc'>{$this->lang->words['youmay_add_to']} <strong>{$this->memberData['g_max_mass_pm']}</strong> {$this->lang->words['youmay_suffix']}</span>
		</div>
		
		<div class='ipsField'>
			<strong>{$this->lang->words['send_to_as']} </strong>
			<select name='sendType' id='send_type' tabindex='0'>
				<option value='invite'" . (($this->request['sendType']=='invite') ? (" selected='selected'") : ("")) . ">{$this->lang->words['send__invite']}</option>
				<option value='copy'" . (($this->request['sendType']=='copy') ? (" selected='selected'") : ("")) . ">{$this->lang->words['send__copy']}</option>
			</select><br />
			<span class='desc'>
				<strong>{$this->lang->words['send__invite']}</strong> {$this->lang->words['invite__desc']}<br />
				<strong>{$this->lang->words['send__copy']}</strong> {$this->lang->words['copy__desc']}
			</span>
		</div>") : ("")) . "
	
	<h3>{$this->lang->words['pro_message']}</h3>
	<div class='ipsField'>
		<label for='message_subject' class='ipsField_title'>{$this->lang->words['message_subject_send']}</label>
		<input type=\"text\" name=\"msg_title\" id='message_subject' class='input_text' size=\"30\" tabindex=\"0\" maxlength=\"40\" value=\"{$displayData['title']}\" />
	</div>
	<div class='ipsField'>
		{$displayData['editor']}
	</div>
	<input type='hidden' name='topicID' value=\"{$displayData['topicID']}\" />
	<input type='hidden' name=\"postKey\" value=\"{$displayData['postKey']}\" />
	<input type=\"hidden\" name=\"auth_key\" value=\"{$this->member->form_hash}\" />
	<div class='submit'>
		<input class='button' name=\"dosubmit\" type=\"submit\" value=\"{$this->lang->words['submit_send']}\" tabindex=\"0\" accesskey=\"s\" />
	</div>
		
</form>";
return $IPBHTML;
}

/* -- sendReplyForm --*/
function sendReplyForm($displayData) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_messaging', $this->_funcHooks['sendReplyForm'] ) )
{
$count_334d532824ba8419955b8d76d0f1b132 = is_array($this->functionData['sendReplyForm']) ? count($this->functionData['sendReplyForm']) : 0;
$this->functionData['sendReplyForm'][$count_334d532824ba8419955b8d76d0f1b132]['displayData'] = $displayData;
}
$IPBHTML .= "" . (($this->settings['_hideFolders'] = 1) ? ("") : ("")) . "
" . (($displayData['type'] == 'reply') ? ("
	<form id='msgForm' style='display:block' action=\"" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "module=messaging&amp;section=send&amp;do=sendReply", "publicWithApp",'' ), "", "" ) . "\" method=\"post\" name=\"REPLIER\">
") : ("
	<form id='msgForm' style='display:block' action=\"" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "module=messaging&amp;section=send&amp;do=sendEdit", "publicWithApp",'' ), "", "" ) . "\" method=\"post\" name=\"REPLIER\">
")) . "
<input type=\"hidden\" name=\"msgID\" value=\"{$displayData['msgID']}\" />
<input type='hidden' name='topicID' value=\"{$displayData['topicID']}\" />
<input type='hidden' name=\"postKey\" value=\"{$displayData['postKey']}\" />
<input type=\"hidden\" name=\"authKey\" value=\"{$this->member->form_hash}\" />
" . (($displayData['errors']) ? ("
	<div class='message error'>
		<strong>{$this->lang->words['err_errors']}</strong><br />
		{$displayData['errors']}<br />
		{$this->lang->words['pme_none_sent']}
	</div>
") : ("")) . "
" . (($displayData['type'] == 'reply') ? ("
	<h3>{$this->lang->words['compose_reply']}</h3>
") : ("
	<h3>{$this->lang->words['editing_message']}</h3>
")) . "
<div class='row'>
	{$displayData['editor']}
</div>
<div class='submit'>
	" . (($displayData['type'] == 'reply') ? ("
		<input class='button' type=\"submit\" value=\"{$this->lang->words['submit_send']}\" tabindex=\"0\" accesskey=\"s\" />
	") : ("
		<input class='button' type=\"submit\" value=\"{$this->lang->words['save_message_button']}\" tabindex=\"0\" accesskey=\"s\" />
	")) . "
</div>";
return $IPBHTML;
}

/* -- showConversation --*/
function showConversation($topic, $replies, $members, $jump="") {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_messaging', $this->_funcHooks['showConversation'] ) )
{
$count_77b65fc1dcdc003025167bb8128167b3 = is_array($this->functionData['showConversation']) ? count($this->functionData['showConversation']) : 0;
$this->functionData['showConversation'][$count_77b65fc1dcdc003025167bb8128167b3]['topic'] = $topic;
$this->functionData['showConversation'][$count_77b65fc1dcdc003025167bb8128167b3]['replies'] = $replies;
$this->functionData['showConversation'][$count_77b65fc1dcdc003025167bb8128167b3]['members'] = $members;
$this->functionData['showConversation'][$count_77b65fc1dcdc003025167bb8128167b3]['jump'] = $jump;
}
$IPBHTML .= "<div class='controls'>
	<div class='buttons'>
	" . ((empty( $topic['_everyoneElseHasLeft'] )) ? ("" . (($topic['_canReply']) ? ("
			<a class='button' href='" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "module=messaging&amp;section=send&amp;do=replyForm&amp;topicID={$topic['mt_id']}", "publicWithApp",'' ), "", "" ) . "'>" . $this->registry->getClass('output')->getReplacement("reply_icon") . " {$this->lang->words['add_reply']}</a>
		") : ("")) . "") : ("")) . "
		<!--<a class='button delete' href='" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "module=messaging&amp;section=view&amp;do=deleteConversation&amp;topicID={$topic['mt_id']}&amp;authKey={$this->member->form_hash}", "publicWithApp",'' ), "", "" ) . "'>{$this->lang->words['option__delete']}</a>-->
	</div>
	{$topic['_pages']}
</div>".$this->__f__fbc3f7736bace35fe471f27d58ff9f11($topic,$replies,$members,$jump)."<div class='controls'>
	<div class='buttons'>
	" . ((! empty( $topic['_everyoneElseHasLeft'] )) ? ("
		<p>{$this->lang->words['msg_all_alone_title']} - {$this->lang->words['msg_all_alone_desc']}</p>
	") : ("" . (($topic['_canReply']) ? ("
			<a class='button' href='" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "module=messaging&amp;section=send&amp;do=replyForm&amp;topicID={$topic['mt_id']}", "publicWithApp",'' ), "", "" ) . "'>" . $this->registry->getClass('output')->getReplacement("reply_icon") . " {$this->lang->words['add_reply']}</a>
		") : ("")) . "")) . "
	<!--<a class='button delete' href='" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "module=messaging&amp;section=view&amp;do=deleteConversation&amp;topicID={$topic['mt_id']}&amp;authKey={$this->member->form_hash}", "publicWithApp",'' ), "", "" ) . "'>{$this->lang->words['option__delete']}</a>-->
	</div>
	{$topic['_pages']}
</div>";
return $IPBHTML;
}


function __f__fbc3f7736bace35fe471f27d58ff9f11($topic, $replies, $members, $jump="")
{
	$_ips___x_retval = '';
	$__iteratorCount = 0;
	foreach( $replies as $msg_id => $msg )
	{
		
		$__iteratorCount++;
		$_ips___x_retval .= "
	<a id='msg{$msg['msg_id']}'></a>
	<div class='topic_reply'>
		<h2 class='secondary'>
			<img src='{$members[ $msg['msg_author_id'] ]['pp_thumb_photo']}' class='photo' />
			<a class=\"url fn\" href='" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "showuser={$members[ $msg['msg_author_id'] ]['member_id']}", "public",'' ), "{$members[ $msg['msg_author_id'] ]['members_seo_name']}", "showuser" ) . "'>{$members[ $msg['msg_author_id'] ]['members_display_name']}</a>
			<span class='subtext'>{$this->lang->words['pc_sent']} " . IPSText::htmlspecialchars($this->registry->getClass('class_localization')->getDate($msg['msg_date'],"long", 0)) . "</span>
		</h2>
		<div class='post line_spacing' id='msg_{$msg['msg_id']}'>
			{$msg['msg_post']}
			{$msg['attachmentHtml']}
		</div>
		<div class='post_controls' id='msg_{$msg['msg_id']}-controls'>
			" . (($topic['_canReply']) ? ("
				<a href=\"" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "module=messaging&amp;section=send&amp;do=replyForm&amp;topicID={$topic['mt_id']}&amp;msgID={$msg['msg_id']}", "publicWithApp",'' ), "", "" ) . "\" title=\"{$this->lang->words['tt_reply_to_post']}\">{$this->lang->words['pc_reply']}</a>
			") : ("")) . "
			" . (($msg['_canEdit'] === TRUE) ? ("
				<a href='" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "module=messaging&amp;section=send&amp;do=editMessage&amp;topicID={$topic['mt_id']}&amp;msgID={$msg['msg_id']}", "publicWithApp",'' ), "", "" ) . "' title='{$this->lang->words['edit_this_post']}'>{$this->lang->words['pc_edit']}</a>
			") : ("")) . "
			" . (($msg['_canDelete'] === TRUE && $msg['msg_is_first_post'] != 1) ? ("
				<a href='" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "module=messaging&amp;section=send&amp;do=deleteReply&amp;topicID={$topic['mt_id']}&amp;msgID={$msg['msg_id']}&amp;authKey={$this->member->form_hash}", "publicWithApp",'' ), "", "" ) . "' title='{$this->lang->words['delete_this_post']}' class='delete_post'>{$this->lang->words['pc_delete']}</a>
			") : ("")) . "
		</div>
	</div>

";
	}
	$_ips___x_retval .= '';
	unset( $__iteratorCount );
	return $_ips___x_retval;
}

/* -- showConversationForArchive --*/
function showConversationForArchive($topic, $replies, $members) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_messaging', $this->_funcHooks['showConversationForArchive'] ) )
{
$count_62c1b03f3713a2f5e848bbf987a88c05 = is_array($this->functionData['showConversationForArchive']) ? count($this->functionData['showConversationForArchive']) : 0;
$this->functionData['showConversationForArchive'][$count_62c1b03f3713a2f5e848bbf987a88c05]['topic'] = $topic;
$this->functionData['showConversationForArchive'][$count_62c1b03f3713a2f5e848bbf987a88c05]['replies'] = $replies;
$this->functionData['showConversationForArchive'][$count_62c1b03f3713a2f5e848bbf987a88c05]['members'] = $members;
}

foreach( $members as $id => $member )
{
	$mems[] = $member['members_display_name'];
}
$memberNames = implode( $mems, ', ' );
$IPBHTML .= "<html>
	<head>
		<meta http-equiv=\"content-type\" content=\"text/html; charset={$this->settings['gb_char_set']}\" />
		<title>{$topic['mt_title']}</title>
		<style type=\"text/css\">
			* {
				font-family: Georgia, \"Times New Roman\", serif;
			}
			
			html #content {
				font-size: 10pt;
			}
			
			ol,ul { list-style:none; }
			
			ul.pagination {
				margin-left: -35px;
			}
			
			ul.pagination a,
			ul.pagination li.active, 
			ul.pagination li.pagejump,
			ul.pagination li.total {
				text-decoration: none;
				padding: 1px 4px 1px 0px;
				display: block;
			}
			
			ul.pagination li {
				font-size: 0.9em;	
				margin: 0 2px 0 2px;
				float: left;
			}
			
				ul.pagination li.total,
				ul.pagination li.active {
					background: none;
					border: 0;
					margin-left: 0px;
				}
			
				ul.pagination li.active {
					color: #000;
					font-size: 1em;
					font-weight: bold;
				}
				
				ul.pagination li.pagejump {
					display: none;
				}
			
			#admin_bar,
			#header,
			#footer_utilities,
			#utility_links,
			.post_mod,
			.author_info,
			.rep_bar,
			.post_controls,
			.top,
			#content_jump,
			.topic_buttons,
			.topic_options,
			h1,
			.post_id,
			h3 img,
			.ip,
			hr,
			.moderation_bar,
			.topic_jump,
			.topic_share,
			#fast_reply,
			#reputation_filter,
			.statistics,
			.rating,
			.message,
			#debug_wrapper,
			fieldset,
			.signature {
				display: none;
			}
			
			#breadcrumb {
				display: block !important;
			}
				#breadcrumb li {
					float: left;
				}
			
			.topic, .hfeed {
				clear: both;
			}
			
			.post_block {
				margin-bottom: 10pt;
				border-top: 2pt solid gray;
				line-height: 60%; 
				padding-top: 10px;
			}
			
			.posted_info {
				color: gray !important;
				font-size: 8pt !important;
				text-decoration: none !important;
				padding-bottom: 3px;
				float: right;
				margin-top: -30px;
			}
			
			span.main_topic_title {
				font-size: 1.7em;
				padding-left: 2px;
			}
			
			.post_block h3 {
				display: inline !important;
				margin: 0px 0px 10px !important;
				padding: 0px !important;
				float: left;
			}
			
			.post_block h3 a {
				color: black !important;
				text-decoration: none !important;
				font-style: normal !important;
			}
			
				.post_block .post_body a:after {
				    content: \" (\" attr(href) \") \";
				}
			
			.post_body {
				line-height: 100%;
				margin-top: 15px;
				clear: both;
				display: block;
				padding: 10px;
				border-top: 1pt solid #d3d3d3;
			}
			
			h1, h2, h3 {
				font-weight: bold;
			}
			
			#copyright {
				text-align: center;
				color: gray;
				font-size: 9pt;
			}
			
			a img {
				border: 0px;
			}
			
			abbr.published {
				text-decoration: none !important;
				border: 0px;
			}
		</style>
	</head>
	<body>
		<h2 class='maintitle'>{$topic['mt_title']}</h2>
		<em>
			{$this->lang->words['email_participants']} {$memberNames}
		</em>
		<br />
		<br />
		".$this->__f__e1614f20921ed57113f79684e4fa8199($topic,$replies,$members)."	</body>
</html>";
return $IPBHTML;
}


function __f__e1614f20921ed57113f79684e4fa8199($topic, $replies, $members)
{
	$_ips___x_retval = '';
	$__iteratorCount = 0;
	foreach( $replies as $msg_id => $msg )
	{
		
		$__iteratorCount++;
		$_ips___x_retval .= "
			<div class='post_block first hentry'>
				<div class='post_wrap'>
					<h3>
						{$members[ $msg['msg_author_id'] ]['members_display_name']}
					</h3>
					<div class='post_body'>
						<p class='posted_info'>{$this->lang->words['pc_sent']} " . IPSText::htmlspecialchars($this->registry->getClass('class_localization')->getDate($msg['msg_date'],"long", 0)) . "</p>
						<div class='post entry-content'>
							{$msg['msg_post']}
						</div>
					</div>
					<ul class='post_controls'>
						<li>&nbsp;</li>
					</ul>
				</div>
			</div>
		
";
	}
	$_ips___x_retval .= '';
	unset( $__iteratorCount );
	return $_ips___x_retval;
}

/* -- showFolder --*/
function showFolder($messages, $dirname, $pages, $currentFolderID, $jumpFolderHTML) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_messaging', $this->_funcHooks['showFolder'] ) )
{
$count_4701909ca87722a60a11dcc577d3ef48 = is_array($this->functionData['showFolder']) ? count($this->functionData['showFolder']) : 0;
$this->functionData['showFolder'][$count_4701909ca87722a60a11dcc577d3ef48]['messages'] = $messages;
$this->functionData['showFolder'][$count_4701909ca87722a60a11dcc577d3ef48]['dirname'] = $dirname;
$this->functionData['showFolder'][$count_4701909ca87722a60a11dcc577d3ef48]['pages'] = $pages;
$this->functionData['showFolder'][$count_4701909ca87722a60a11dcc577d3ef48]['currentFolderID'] = $currentFolderID;
$this->functionData['showFolder'][$count_4701909ca87722a60a11dcc577d3ef48]['jumpFolderHTML'] = $jumpFolderHTML;
}

if( !$this->request['folderID'] )
	{
		$this->settings['_mobile_nav_home'] = true;
	}

if ( ! isset( $this->registry->templateStriping['msg_stripe'] ) ) {
$this->registry->templateStriping['msg_stripe'] = array( FALSE, "row1","row2");
}
$IPBHTML .= "<h2>{$dirname}</h2>
<div class='controls'><div class=\"buttons\">
	<a href='" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "module=messaging&amp;section=send&amp;do=form", "publicWithApp",'' ), "", "" ) . "' class='button' title='{$this->lang->words['go_to_compose']}'>{$this->lang->words['compose_new']}</a>
	{$pages}
</div>
</div>
" . ((count( $messages )) ? ("
		".$this->__f__0c4358ca55fcee88b41123185a1fc4ca($messages,$dirname,$pages,$currentFolderID,$jumpFolderHTML)."") : ("
	<div class='row no_messages'>
		{$this->lang->words['folder_no_messages_row']}
	</div>
")) . "
<!--<div class='controls'>
<div class=\"buttons\">
	<a href='" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "module=messaging&amp;section=send&amp;do=form", "publicWithApp",'' ), "", "" ) . "' class='button' title='{$this->lang->words['go_to_compose']}'>{$this->lang->words['compose_new']}</a>
	{$pages}
</div>
</div>-->";
return $IPBHTML;
}


function __f__0c4358ca55fcee88b41123185a1fc4ca($messages, $dirname, $pages, $currentFolderID, $jumpFolderHTML)
{
	$_ips___x_retval = '';
	$__iteratorCount = 0;
	foreach( $messages as $id => $msg )
	{
		
		$__iteratorCount++;
		$_ips___x_retval .= "
		<div class='row touch-row' id=\"row-{$id}\">
			<div class='icon'>
				" . (($msg['mt_is_deleted'] OR $msg['map_user_banned']) ? ("
					" . $this->registry->getClass('output')->getReplacement("msg_deleted") . "
				") : ("" . (($msg['map_has_unread'] == 1) ? ("
						" . $this->registry->getClass('output')->getReplacement("msg_icon_new") . "
					") : ("
						" . $this->registry->getClass('output')->getReplacement("msg_icon") . "
					")) . "")) . "
			</div>
			<div class='row_content'>
				" . (($currentFolderID == 'drafts') ? ("
					<strong><a href=\"" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "app=members&amp;module=messaging&amp;section=send&amp;do=form&amp;topicID={$msg['mt_id']}", "public",'' ), "", "" ) . "\" class=\"title\">{$msg['mt_title']}</a></strong>
				") : ("
					<strong><a href=\"" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "app=members&amp;module=messaging&amp;section=view&amp;do=findMessage&amp;topicID={$msg['mt_id']}&msgID=__firstUnread__", "public",'' ), "", "" ) . "\" class=\"title\">{$msg['mt_title']}</a></strong>
				")) . "
				<br />
				<span class='subtext'>
					{$this->lang->words['col_starter']}:
					" . (($msg['_starterMemberData']['members_display_name']) ? ("
						{$msg['_starterMemberData']['members_display_name']}
					") : ("
						<em>{$this->lang->words['deleted_user']}</em>
					")) . "
					{$this->lang->words['msg_sentto']}
					" . (($msg['_toMemberData']['members_display_name']) ? ("
						{$msg['_toMemberData']['members_display_name']}
					") : ("
						<em>{$this->lang->words['deleted_user']}</em>
					")) . "
					" . (($msg['_otherInviteeCount'] > 0) ? ("" . (($msg['_otherInviteeCount'] > 1) ? ("
							<span title='" . implode( ', ', $msg['_invitedMemberNames'] ) . "'>({$this->lang->words['pc_and']} {$msg['_otherInviteeCount']} {$this->lang->words['pc_others']})</span>
						") : ("
							<span title='" . implode( ', ', $msg['_invitedMemberNames'] ) . "'>({$this->lang->words['pc_and']} {$msg['_otherInviteeCount']} {$this->lang->words['pc_other']})</span>
						")) . "") : ("")) . "
					" . ((!$msg['map_user_banned']) ? ("
						&middot; " . intval( $msg['mt_replies'] ) . " {$this->lang->words['col_replies']}
					") : ("")) . "			
				</span>
			</div>
		</div>
	
";
	}
	$_ips___x_retval .= '';
	unset( $__iteratorCount );
	return $_ips___x_retval;
}

/* -- showSearchResults --*/
function showSearchResults($messages, $pages, $error) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_messaging', $this->_funcHooks['showSearchResults'] ) )
{
$count_705d29c3a527a36d52cb135cea8d9529 = is_array($this->functionData['showSearchResults']) ? count($this->functionData['showSearchResults']) : 0;
$this->functionData['showSearchResults'][$count_705d29c3a527a36d52cb135cea8d9529]['messages'] = $messages;
$this->functionData['showSearchResults'][$count_705d29c3a527a36d52cb135cea8d9529]['pages'] = $pages;
$this->functionData['showSearchResults'][$count_705d29c3a527a36d52cb135cea8d9529]['error'] = $error;
}

if ( ! isset( $this->registry->templateStriping['msg_stripe'] ) ) {
$this->registry->templateStriping['msg_stripe'] = array( FALSE, "row1","row2");
}
$IPBHTML .= "<script type='text/javascript'>
//<![CDATA[
	ipb.messenger.curFolder = 'in';
//]]>
</script>
" . (($error) ? ("
	<p class='message error'>
		{$error}
	</p>
	<br />
") : ("")) . "
" . (($pages) ? ("
	<div class='topic_controls'>
	{$pages}
	</div>
	<br />
") : ("")) . "
<h2 class='maintitle clear'>{$this->lang->words['your_search_results']}</h2>
<div id='message_list'>
		<table class='ipb_table' id='message_table'>
			<tr class='header'>
				<th scope='col' class='col_m_status'>&nbsp;</th>
				<th scope='col' class='col_m_subject'>{$this->lang->words['col_subject']}</th>
				<th scope='col' class='col_m_replies short'>{$this->lang->words['col_replies']}</th>
				<th scope='col' class='col_m_sender'>{$this->lang->words['col_recipient']}</th>
				<th scope='col' class='col_m_date'>{$this->lang->words['col_date']}</th>
			</tr>
		
			" . ((count( $messages )) ? ("
								".$this->__f__1d70fba90414b28762e7c1f88c99fd9e($messages,$pages,$error)."			") : ("
				<tr>
					<td colspan='5' class='no_messages row1'>
						{$this->lang->words['no_messages_row']}
					</td>
				</tr>
			")) . "
		</table>
	</form>
</div>";
return $IPBHTML;
}


function __f__1d70fba90414b28762e7c1f88c99fd9e($messages, $pages, $error)
{
	$_ips___x_retval = '';
	$__iteratorCount = 0;
	foreach( $messages as $id => $msg )
	{
		
		$__iteratorCount++;
		$_ips___x_retval .= "
					<tr id='{$msg['mt_id']}' class='" .  IPSLib::next( $this->registry->templateStriping["msg_stripe"] ) . "'>
						<td class='altrow short'>
						</td>
						<td>
							<span class='m_title'>
								" . (($msg['map_has_unread'] == 1) ? ("
									<strong>
								") : ("")) . "
								<a href=\"" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "app=members&amp;module=messaging&amp;section=view&amp;do=showConversation&amp;topicID={$msg['mt_id']}", "public",'' ), "", "" ) . "\">{$msg['mt_title']}</a>
								" . (($msg['map_has_unread'] == 1) ? ("
									</strong>
								") : ("")) . "
								<p>{$this->lang->words['label_pc']} {$msg['_folderName']}</p>
							</span>
						</td>
						<td>
							<span class='desc'>" . intval( $msg['mt_replies'] ) . "</span>
						</td>
						<td class='altrow'>
							" . (($msg['_toMemberData']['member_id']) ? ("<a href='" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "showuser={$msg['_toMemberData']['member_id']}", "public",'' ), "", "" ) . "'>{$msg['_toMemberData']['members_display_name']}</a>
								" . (($msg['mt_invited_count']) ? ("" . (($msg['mt_invited_count'] > 1) ? ("
										<p>({$this->lang->words['pc_and']} {$msg['mt_invited_count']} {$this->lang->words['pc_others']})</p>
									") : ("
										<p>({$this->lang->words['pc_and']} {$msg['mt_invited_count']} {$this->lang->words['pc_other']})</p>
									")) . "") : ("")) . "") : ("
								<span class='desc'>{$this->lang->words['deleted_user']}</span>
							")) . "
						</td>
						<td>
							" . IPSText::htmlspecialchars($this->registry->getClass('class_localization')->getDate($msg['mt_last_post_time'],"long", 0)) . "
						</td>
					</tr>
				
";
	}
	$_ips___x_retval .= '';
	unset( $__iteratorCount );
	return $_ips___x_retval;
}


}


/*--------------------------------------------------*/
/* END OF FILE                                      */
/*--------------------------------------------------*/

?>
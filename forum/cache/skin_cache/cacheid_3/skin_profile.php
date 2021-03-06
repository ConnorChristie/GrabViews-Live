<?php
/*--------------------------------------------------*/
/* FILE GENERATED BY INVISION POWER BOARD 3         */
/* CACHE FILE: Skin set id: 3               */
/* CACHE FILE: Generated: Fri, 26 Jul 2013 21:54:34 GMT */
/* DO NOT EDIT DIRECTLY - THE CHANGES WILL NOT BE   */
/* WRITTEN TO THE DATABASE AUTOMATICALLY            */
/*--------------------------------------------------*/

class skin_profile_3 extends skinMaster{

/**
* Construct
*/
function __construct( ipsRegistry $registry )
{
	parent::__construct( $registry );
	

$this->_funcHooks = array();
$this->_funcHooks['profileModern'] = array('tabs','pmlink');
$this->_funcHooks['statusReplies'] = array('canDelete');
$this->_funcHooks['statusUpdates'] = array('canDelete','outerLoop');
$this->_funcHooks['tabFriends'] = array('friends');
$this->_funcHooks['tabStatusUpdates'] = array('canCreate','leave_comment','hasUpdates');


}

/* -- acknowledgeWarning --*/
function acknowledgeWarning($warning) {
$IPBHTML = "";
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- addWarning --*/
function addWarning($member, $reasons, $errors, $editor) {
$IPBHTML = "";
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- customField__gender --*/
function customField__gender($f) {
$IPBHTML = "";
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- customField__generic --*/
function customField__generic($f) {
$IPBHTML = "";
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- customFieldGroup__contact --*/
function customFieldGroup__contact($f) {
$IPBHTML = "";
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- customizeProfile --*/
function customizeProfile($member) {
$IPBHTML = "";
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- dnameWrapper --*/
function dnameWrapper($member_name="",$records=array()) {
$IPBHTML = "";
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- explainPoints --*/
function explainPoints($reasons, $actions) {
$IPBHTML = "";
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- friendsList --*/
function friendsList($friends, $pages) {
$IPBHTML = "";
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- listWarnings --*/
function listWarnings($member, $warnings, $pagination, $reasons, $canWarn) {
$IPBHTML = "";
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- photoEditor --*/
function photoEditor($data, $member) {
$IPBHTML = "";
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- profileModern --*/
function profileModern($tabs=array(), $member=array(), $visitors=array(), $default_tab='status', $default_tab_content='', $friends=array(), $status=array(), $warns=array()) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_profile', $this->_funcHooks['profileModern'] ) )
{
$count_44e7fab34176c61d0077bfc4f0f1ba5f = is_array($this->functionData['profileModern']) ? count($this->functionData['profileModern']) : 0;
$this->functionData['profileModern'][$count_44e7fab34176c61d0077bfc4f0f1ba5f]['tabs'] = $tabs;
$this->functionData['profileModern'][$count_44e7fab34176c61d0077bfc4f0f1ba5f]['member'] = $member;
$this->functionData['profileModern'][$count_44e7fab34176c61d0077bfc4f0f1ba5f]['visitors'] = $visitors;
$this->functionData['profileModern'][$count_44e7fab34176c61d0077bfc4f0f1ba5f]['default_tab'] = $default_tab;
$this->functionData['profileModern'][$count_44e7fab34176c61d0077bfc4f0f1ba5f]['default_tab_content'] = $default_tab_content;
$this->functionData['profileModern'][$count_44e7fab34176c61d0077bfc4f0f1ba5f]['friends'] = $friends;
$this->functionData['profileModern'][$count_44e7fab34176c61d0077bfc4f0f1ba5f]['status'] = $status;
$this->functionData['profileModern'][$count_44e7fab34176c61d0077bfc4f0f1ba5f]['warns'] = $warns;
}
$IPBHTML .= "<template>profileView</template>
<profileData>
	<id>{$member['member_id']}</id>
	<name><![CDATA[{$member['members_display_name']}]]></name>
	<memberTitle><![CDATA[{$member['title']}]]></memberTitle>
	<reputation>{$member['pp_reputation_points']}</reputation>
	<postCount>{$member['posts']}</postCount>
	<avatar><![CDATA[{$member['pp_main_photo']}]]></avatar>	
</profileData>
<tab><![CDATA[{$default_tab}]]></tab>
" . (($default_tab == 'core:info') ? ("" . ((($member['member_id'] != $this->memberData['member_id']) AND $this->memberData['g_use_pm'] AND $this->memberData['members_disable_pm'] == 0 AND IPSLib::moduleIsEnabled( 'messaging', 'members' ) AND $member['members_disable_pm'] == 0) ? ("
<pmMeLink><![CDATA[" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "app=members&amp;module=messaging&amp;section=send&amp;do=form&amp;fromMemberID={$member['member_id']}", "public",'' ), "", "" ) . "]]></pmMeLink>
") : ("")) . "
<viewMyContent><![CDATA[" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "app=core&amp;module=search&amp;do=user_activity&amp;mid={$member['member_id']}", "public",'' ), "", "" ) . "]]></viewMyContent>
<profileTabs>
	".$this->__f__7c4bf5929998c6551cca2a71a3fb300f($tabs,$member,$visitors,$default_tab,$default_tab_content,$friends,$status,$warns)."</profileTabs>") : ("
	{$default_tab_content}
")) . "";
return $IPBHTML;
}


function __f__7c4bf5929998c6551cca2a71a3fb300f($tabs=array(), $member=array(), $visitors=array(), $default_tab='status', $default_tab_content='', $friends=array(), $status=array(), $warns=array())
{
	$_ips___x_retval = '';
	$__iteratorCount = 0;
	foreach( $tabs as $tab )
	{
		
		$__iteratorCount++;
		$_ips___x_retval .= "
		<profileTab>
			<name><![CDATA[{$tab['_lang']}]]></name>
			<url><![CDATA[" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "showuser={$member['member_id']}&amp;tab={$tab['plugin_key']}", "public",'' ), "{$member['members_seo_name']}", "showuser" ) . "]]></url>
		</profileTab>
	
";
	}
	$_ips___x_retval .= '';
	unset( $__iteratorCount );
	return $_ips___x_retval;
}

/* -- reputationPage --*/
function reputationPage($langBit, $currentApp='', $supportedApps=array(), $processedResults='') {
$IPBHTML = "";
$IPBHTML .= "<!--no data in this master skin-->";
return $IPBHTML;
}

/* -- showCard --*/
function showCard($member, $download=0) {
$IPBHTML = "";
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- statusReplies --*/
function statusReplies($replies=array(), $no_wrapper=false) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_profile', $this->_funcHooks['statusReplies'] ) )
{
$count_52b3cf6022630ce46226c9d221eada4a = is_array($this->functionData['statusReplies']) ? count($this->functionData['statusReplies']) : 0;
$this->functionData['statusReplies'][$count_52b3cf6022630ce46226c9d221eada4a]['replies'] = $replies;
$this->functionData['statusReplies'][$count_52b3cf6022630ce46226c9d221eada4a]['no_wrapper'] = $no_wrapper;
}
$IPBHTML .= "<commentReplies>
	".$this->__f__8a5666cd2d3eebe2945967c16cbce8d4($replies,$no_wrapper)."</commentReplies>";
return $IPBHTML;
}


function __f__8a5666cd2d3eebe2945967c16cbce8d4($replies=array(), $no_wrapper=false)
{
	$_ips___x_retval = '';
	$__iteratorCount = 0;
	foreach( $replies as $reply )
	{
		
		$__iteratorCount++;
		$_ips___x_retval .= "
		<commentReply>
			<author><![CDATA[{$reply['members_display_name']}]]></author>
			<avatar><![CDATA[{$reply['pp_main_photo']}]]></avatar>	
			<reply><![CDATA[{$reply['reply_content']}]]></reply>
			<date>{$reply['reply_date_formatted']}</date>
			<canDelete>" . (($reply['_canDelete']) ? ("1") : ("0")) . "</canDelete>
			<deleteURL><![CDATA[{$this->settings['base_url']}app=members&amp;module=profile&amp;section=status&amp;do=deleteReply&amp;status_id={$reply['reply_status_id']}&amp;reply_id={$reply['reply_id']}&amp;k={$this->member->form_hash}]]></deleteURL>
		</commentReply>
	
";
	}
	$_ips___x_retval .= '';
	unset( $__iteratorCount );
	return $_ips___x_retval;
}

/* -- statusUpdates --*/
function statusUpdates($updates=array(), $smallSpace=0, $latestOnly=0) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_profile', $this->_funcHooks['statusUpdates'] ) )
{
$count_9119e931725d7c5920caf33fce59db91 = is_array($this->functionData['statusUpdates']) ? count($this->functionData['statusUpdates']) : 0;
$this->functionData['statusUpdates'][$count_9119e931725d7c5920caf33fce59db91]['updates'] = $updates;
$this->functionData['statusUpdates'][$count_9119e931725d7c5920caf33fce59db91]['smallSpace'] = $smallSpace;
$this->functionData['statusUpdates'][$count_9119e931725d7c5920caf33fce59db91]['latestOnly'] = $latestOnly;
}
$IPBHTML .= "<profileComments>".$this->__f__345027a3132dae49f9a23f1d3f6a3bc7($updates,$smallSpace,$latestOnly)."</profileComments>";
return $IPBHTML;
}


function __f__345027a3132dae49f9a23f1d3f6a3bc7($updates=array(), $smallSpace=0, $latestOnly=0)
{
	$_ips___x_retval = '';
	$__iteratorCount = 0;
	foreach( $updates as $id => $status )
	{
		
		$__iteratorCount++;
		$_ips___x_retval .= "
		<profileComment>
			<author><![CDATA[{$status['members_display_name']}]]></author>
			<avatar><![CDATA[{$status['pp_main_photo']}]]></avatar>	
			<reply><![CDATA[{$status['status_content']}]]></reply>
			<date>{$status['status_date_formatted']}</date>
			<canDelete>" . (($status['_canDelete']) ? ("1") : ("0")) . "</canDelete>
			<deleteURL><![CDATA[{$this->settings['base_url']}app=members&amp;module=profile&amp;section=status&amp;do=deleteReply&amp;status_id={$status['status_status_id']}&amp;reply_id={$status['status_id']}&amp;k={$this->member->form_hash}]]></deleteURL>
			" . (($status['status_replies'] AND count( $status['replies'] )) ? ("
				" . ( method_exists( $this->registry->getClass('output')->getTemplate('profile'), 'statusReplies' ) ? $this->registry->getClass('output')->getTemplate('profile')->statusReplies($status['replies'], 1) : '' ) . "
			") : ("")) . "
			" . (($status['_userCanReply']) ? ("
					<replyURL><![CDATA[{$this->settings['base_url']}app=members&amp;module=profile&amp;section=status&amp;do=reply&amp;status_id={$status['status_id']}&amp;k={$this->member->form_hash}&amp;id={$this->memberData['member_id']}]]></replyURL>
			") : ("")) . "
		</profileComment>
	
";
	}
	$_ips___x_retval .= '';
	unset( $__iteratorCount );
	return $_ips___x_retval;
}

/* -- statusUpdatesPage --*/
function statusUpdatesPage($updates=array(), $pages='') {
$IPBHTML = "";
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- tabFriends --*/
function tabFriends($friends=array(), $member=array(), $pagination='') {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_profile', $this->_funcHooks['tabFriends'] ) )
{
$count_22a7c99a904ffcacee60e55b523646a0 = is_array($this->functionData['tabFriends']) ? count($this->functionData['tabFriends']) : 0;
$this->functionData['tabFriends'][$count_22a7c99a904ffcacee60e55b523646a0]['friends'] = $friends;
$this->functionData['tabFriends'][$count_22a7c99a904ffcacee60e55b523646a0]['member'] = $member;
$this->functionData['tabFriends'][$count_22a7c99a904ffcacee60e55b523646a0]['pagination'] = $pagination;
}
$IPBHTML .= "<pagination>{$pagination}</pagination>
<friends>
	".$this->__f__38802883b08aa3610db2d1c184c5570f($friends,$member,$pagination)."</friends>";
return $IPBHTML;
}


function __f__38802883b08aa3610db2d1c184c5570f($friends=array(), $member=array(), $pagination='')
{
	$_ips___x_retval = '';
	$__iteratorCount = 0;
	foreach( $friends as $friend )
	{
		
		$__iteratorCount++;
		$_ips___x_retval .= "
		<friend>
			<url><![CDATA[" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "showuser={$friend['member_id']}", "public",'' ), "{$friend['members_seo_name']}", "showuser" ) . "]]></url>
			<avatar><![CDATA[{$friend['pp_small_photo']}]]></avatar>
			<name><![CDATA[{$friend['members_display_name']}]]></name>
			<memberTitle><![CDATA[{$friend['member_title']}]]></memberTitle>
		</friend>
	
";
	}
	$_ips___x_retval .= '';
	unset( $__iteratorCount );
	return $_ips___x_retval;
}

/* -- tabNoContent --*/
function tabNoContent($langkey) {
$IPBHTML = "";
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- tabPosts --*/
function tabPosts($content) {
$IPBHTML = "";
$IPBHTML .= "<posts>
	{$content}
</posts>";
return $IPBHTML;
}

/* -- tabReputation --*/
function tabReputation($member, $currentApp='', $type='', $supportedApps=array(), $processedResults='', $pagination='') {
$IPBHTML = "";
$IPBHTML .= "<!--no data in this master skin-->";
return $IPBHTML;
}

/* -- tabReputation_calendar --*/
function tabReputation_calendar($results) {
$IPBHTML = "";
$IPBHTML .= "<!--no data in this master skin-->";
return $IPBHTML;
}

/* -- tabReputation_posts --*/
function tabReputation_posts($results) {
$IPBHTML = "";
$IPBHTML .= "<!--no data in this master skin-->";
return $IPBHTML;
}

/* -- tabSingleColumn --*/
function tabSingleColumn($row=array(), $read_more_link='', $url='', $title='') {
$IPBHTML = "";
$IPBHTML .= "<post>
<title><![CDATA[" . IPSText::truncate( $title, 90 ) . "]]></title>
<url><![CDATA[{$url}]]></url>
<text><![CDATA[{$row['post']}]]></text>
<date>" . IPSText::htmlspecialchars($this->registry->getClass('class_localization')->getDate($row['_raw_date'],"long", 0)) . "</date>
</post>";
return $IPBHTML;
}

/* -- tabStatusUpdates --*/
function tabStatusUpdates($updates=array(), $actions, $member=array()) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_profile', $this->_funcHooks['tabStatusUpdates'] ) )
{
$count_5a6e1ca628ca9ee85b421598b73bbe3b = is_array($this->functionData['tabStatusUpdates']) ? count($this->functionData['tabStatusUpdates']) : 0;
$this->functionData['tabStatusUpdates'][$count_5a6e1ca628ca9ee85b421598b73bbe3b]['updates'] = $updates;
$this->functionData['tabStatusUpdates'][$count_5a6e1ca628ca9ee85b421598b73bbe3b]['actions'] = $actions;
$this->functionData['tabStatusUpdates'][$count_5a6e1ca628ca9ee85b421598b73bbe3b]['member'] = $member;
}
$IPBHTML .= "" . (($this->memberData['member_id'] AND ( $this->memberData['member_id'] == $member['member_id'] ) AND $this->registry->getClass('memberStatus')->canCreate( $member )) ? ("
<newStatusURL><![CDATA[{$this->settings['base_url']}app=members&amp;module=profile&amp;section=status&amp;do=new&amp;k={$this->member->form_hash}&amp;id={$this->memberData['member_id']}&amp;forMemberId={$member['member_id']}]]>
</newStatusURL>
") : ("")) . "
" . (($this->memberData['member_id'] && $this->memberData['member_id'] != $member['member_id'] && $member['pp_setting_count_comments']) ? ("
<profileCommentURL>
<![CDATA[{$this->settings['base_url']}app=members&amp;module=profile&amp;section=status&amp;do=new&amp;k={$this->member->form_hash}&amp;id={$this->memberData['member_id']}&amp;forMemberId={$member['member_id']}]]>
</profileCommentURL>
") : ("")) . "

" . ((count( $updates )) ? ("
	" . ( method_exists( $this->registry->getClass('output')->getTemplate('profile'), 'statusUpdates' ) ? $this->registry->getClass('output')->getTemplate('profile')->statusUpdates($updates) : '' ) . "
") : ("
<commentReplies>
	<commentReply>
		<reply><![CDATA[{$this->lang->words['status_updates_none']}]]></reply>
	</commentReply>
</commentReplies>
")) . "";
return $IPBHTML;
}

/* -- tabTopics --*/
function tabTopics($content) {
$IPBHTML = "";
$IPBHTML .= "<posts>
		{$content}
<posts>";
return $IPBHTML;
}


}


/*--------------------------------------------------*/
/* END OF FILE                                      */
/*--------------------------------------------------*/

?>
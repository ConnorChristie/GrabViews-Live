<?php
/*--------------------------------------------------*/
/* FILE GENERATED BY INVISION POWER BOARD 3         */
/* CACHE FILE: Skin set id: 3               */
/* CACHE FILE: Generated: Fri, 26 Jul 2013 21:54:34 GMT */
/* DO NOT EDIT DIRECTLY - THE CHANGES WILL NOT BE   */
/* WRITTEN TO THE DATABASE AUTOMATICALLY            */
/*--------------------------------------------------*/

class skin_global_3 extends skinMaster{

/**
* Construct
*/
function __construct( ipsRegistry $registry )
{
	parent::__construct( $registry );
	

$this->_funcHooks = array();


}

/* -- defaultHeader --*/
function defaultHeader() {
$IPBHTML = "";
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- facebookShareButton --*/
function facebookShareButton($url, $title) {
$IPBHTML = "";
$IPBHTML .= "<!--no data in this master skin-->";
return $IPBHTML;
}

/* -- forum_jump --*/
function forum_jump($html) {
$IPBHTML = "";
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- globalTemplate --*/
function globalTemplate($html, $documentHeadItems, $css, $jsModules, $metaTags, array $header_items, $items=array(), $footer_items=array(), $stats=array()) {
$IPBHTML = "";

$uses_name		= false;
	$uses_email		= false;
	$_redirect		= '';
	
	foreach( $this->cache->getCache('login_methods') as $method )
	{
		if( $method['login_user_id'] == 'username' or $method['login_user_id'] == 'either' )
		{
			$uses_name	= true;
		}
		
		if( $method['login_user_id'] == 'email' or $method['login_user_id'] == 'either' )
		{
			$uses_email	= true;
		}
		
		if( $method['login_login_url'] )
		{
			$_redirect	= $method['login_login_url'];
		}
	}
	//These strings are hardcoded for a reason :)
	if( $uses_name AND $uses_email )
	{
		$this->lang->words['enter_name']	= "USERNAME OR EMAIL";
	}
	else if( $uses_email )
	{
		$this->lang->words['enter_name']	= "EMAIL";
	}
	else
	{
		$this->lang->words['enter_name']	= "USERNAME";
	}
$IPBHTML .= "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>
<ipb>
<title><![CDATA[{$header_items['title']}]]></title>
<boardURL><![CDATA[{$this->settings['board_url']}]]></boardURL>
<publicURL><![CDATA[{$this->settings['public_dir']}]]></publicURL>
<forumHome><![CDATA[" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "act=idx", "public",'' ), "", "" ) . "]]></forumHome>
<styleRevision><![CDATA[{$this->settings['style_last_updated']}]]></styleRevision>


<memberData>
	" . ((IPSMember::canReceiveMobileNotifications($this->memberData)) ? ("
	<push_enabled>1</push_enabled>
    ") : ("")) . "
	<member_id>{$this->memberData['member_id']}</member_id>
	<notificationCnt>{$this->memberData['notification_cnt']}</notificationCnt>
	<messageCnt>{$this->memberData['msg_count_new']}</messageCnt>
	<isSuperMod>{$this->memberData['g_is_supmod']}</isSuperMod>
	<isMod>{$this->memberData['is_mod']}</isMod>
	<isAdmin>{$this->memberData['g_access_cp']}</isAdmin>
	<membersDisplayName><![CDATA[{$this->memberData['members_display_name']}]]></membersDisplayName>
	<secureHash>{$this->member->form_hash}</secureHash>
	<sessionId>{$this->member->session_id}</sessionId>
	<avatarThumb><![CDATA[{$this->memberData['pp_thumb_photo']}]]></avatarThumb>
	<profileURL><![CDATA[" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "showuser={$this->memberData['member_id']}", "public",'' ), "{$this->memberData['members_seo_name']}", "showuser" ) . "]]></profileURL>
</memberData>
<admob>
   <adLocation>{$this->settings['admob_top']}|{$this->settings['admob_bottom']}</adLocation>
   <adCode>{$this->settings['admob_pub_id']}</adCode>
</admob><loginMethod>{$this->lang->words['enter_name']}</loginMethod>

{$html}
</ipb>";
return $IPBHTML;
}

/* -- googlePlusOneButton --*/
function googlePlusOneButton($url, $title) {
$IPBHTML = "";
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- include_highlighter --*/
function include_highlighter($load_when_needed=0) {
$IPBHTML = "";
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- include_lightbox --*/
function include_lightbox() {
$IPBHTML = "";
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- include_lightbox_real --*/
function include_lightbox_real() {
$IPBHTML = "";
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- includeCSS --*/
function includeCSS($css) {
$IPBHTML = "";
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- includeFeeds --*/
function includeFeeds($documentHeadItems) {
$IPBHTML = "";
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- includeJS --*/
function includeJS($jsModules) {
$IPBHTML = "";
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- includeMeta --*/
function includeMeta($metaTags) {
$IPBHTML = "";
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- includeRTL --*/
function includeRTL() {
$IPBHTML = "";
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- includeVars --*/
function includeVars($header_items=array()) {
$IPBHTML = "";
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- inlineLogin --*/
function inlineLogin() {
$IPBHTML = "";
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- liveEditJs --*/
function liveEditJs() {
$IPBHTML = "";
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- metaEditor --*/
function metaEditor($tags, $url) {
$IPBHTML = "";
$IPBHTML .= "<!--no data in this master skin-->";
return $IPBHTML;
}

/* -- nextPreviousTemplate --*/
function nextPreviousTemplate($data) {
$IPBHTML = "";
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- paginationTemplate --*/
function paginationTemplate($work, $data) {
$IPBHTML = "";
$IPBHTML .= "<paginationBase>" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "{$data['baseUrl']}&amp;{$data['startValueKey']}={$data['anchor']}", "{$data['base']}",'' ), "{$data['seoTitle']}", "{$data['seoTemplate']}" ) . "</paginationBase>
<itemsPerPage>{$data['itemsPerPage']}</itemsPerPage>
<totalPages>{$work['pages']}</totalPages>
<currentPage>{$work['current_page']}</currentPage>
<lastReadPage>" . intval( ( $work['pages'] - 1 ) * $data['itemsPerPage'] ) . "{$data['anchor']}</lastReadPage>";
return $IPBHTML;
}

/* -- quickSearch --*/
function quickSearch() {
$IPBHTML = "";
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- shareLinks --*/
function shareLinks($links, $title='', $url='', $cssClass='topic_share left') {
$IPBHTML = "";
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- signature_separator --*/
function signature_separator($sig="", $author_id=0, $can_ignore=true) {
$IPBHTML = "";
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- userHoverCard --*/
function userHoverCard($member=array()) {
$IPBHTML = "";
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- userInfoPane --*/
function userInfoPane($author, $contentid, $options) {
$IPBHTML = "";
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- userSmallPhoto --*/
function userSmallPhoto($member=array()) {
$IPBHTML = "";
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- warnDetails --*/
function warnDetails($warning, $canSeeModNote) {
$IPBHTML = "";
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}


}


/*--------------------------------------------------*/
/* END OF FILE                                      */
/*--------------------------------------------------*/

?>
<?php
/*--------------------------------------------------*/
/* FILE GENERATED BY INVISION POWER BOARD 3         */
/* CACHE FILE: Skin set id: 2               */
/* CACHE FILE: Generated: Fri, 26 Jul 2013 21:54:30 GMT */
/* DO NOT EDIT DIRECTLY - THE CHANGES WILL NOT BE   */
/* WRITTEN TO THE DATABASE AUTOMATICALLY            */
/*--------------------------------------------------*/

class skin_global_comments_2 extends skinMaster{

/**
* Construct
*/
function __construct( ipsRegistry $registry )
{
	parent::__construct( $registry );
	

$this->_funcHooks = array();
$this->_funcHooks['commentsList'] = array('comments','comments_top','allow_comments','comments_bottom');
$this->_funcHooks['form'] = array('comment_errors','isEditing','guest_captcha');


}

/* -- comment --*/
function comment($r, $parent, $settings) {
$IPBHTML = "";

// Adjust author name as needed
if( empty($r['author']['member_id']) && !empty($r['author']['comment_author_name']) )
{
	$r['author']['members_display_name'] = $r['author']['comment_author_name'];
}
$IPBHTML .= "<a id='comment_{$r['comment']['comment_id']}'></a>
<div class=\"row\" id=\"comment-{$r['comment']['comment_id']}\">
	<div class='icon'>
		<img src='{$r['author']['pp_thumb_photo']}' width='{$r['author']['pp_thumb_width']}' height='{$r['author']['pp_thumb_height']}' class='photo' />
	</div>
	<div class='rowContent'>
		<h4>" . ( method_exists( $this->registry->getClass('output')->getTemplate('global'), 'userHoverCard' ) ? $this->registry->getClass('output')->getTemplate('global')->userHoverCard($r['author']) : '' ) . ", <span class='desc'>" . IPSText::htmlspecialchars($this->registry->getClass('class_localization')->getDate($r['comment']['comment_date'],"short", 0)) . "</span></h4>
		{$r['comment']['comment_text']}
	</div>
</div>";
return $IPBHTML;
}

/* -- commentHidden --*/
function commentHidden($r, $parent, $settings) {
$IPBHTML = "";
$IPBHTML .= "<!--no data in this master skin-->";
return $IPBHTML;
}

/* -- commentsList --*/
function commentsList($comments, $settings, $pages, $parent, $preReply='') {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_global_comments', $this->_funcHooks['commentsList'] ) )
{
$count_9da908a9d99a5fa27ddfdc3063f969da = is_array($this->functionData['commentsList']) ? count($this->functionData['commentsList']) : 0;
$this->functionData['commentsList'][$count_9da908a9d99a5fa27ddfdc3063f969da]['comments'] = $comments;
$this->functionData['commentsList'][$count_9da908a9d99a5fa27ddfdc3063f969da]['settings'] = $settings;
$this->functionData['commentsList'][$count_9da908a9d99a5fa27ddfdc3063f969da]['pages'] = $pages;
$this->functionData['commentsList'][$count_9da908a9d99a5fa27ddfdc3063f969da]['parent'] = $parent;
$this->functionData['commentsList'][$count_9da908a9d99a5fa27ddfdc3063f969da]['preReply'] = $preReply;
}

$pluginEditorHook = IPSLib::loadLibrary( IPS_ROOT_PATH . 'sources/classes/editor/composite.php', 'classes_editor_composite' );
	$editor = new $pluginEditorHook();
$IPBHTML .= "<a id='commentsStart'></a>
" . (($pages) ? ("
	<div class='topic_controls'>
		{$pages}
	</div>
") : ("")) . "
<h3>{$this->lang->words['comments_header']}</h3>
<div class='comment_wrap' id='comment_wrap'>
	".$this->__f__31c7bcca0e3d53fc21b71c060f946d2a($comments,$settings,$pages,$parent,$preReply)."</div>
" . (($parent['_canComment']) ? ("
<h2>{$this->lang->words['mobile_add_comment']}</h2>
	<a id=\"fastreply\"></a>
	<div id='fast_reply'>
		<div>
			<form action=\"{$settings['formUrl']}\" method=\"post\">
				<input type=\"hidden\" name=\"auth_key\" value=\"{$this->member->form_hash}\" />
				<input type=\"hidden\" name=\"fromApp\" value=\"{$settings['fromApp']}\" />
				<input type=\"hidden\" name=\"app\" value=\"{$settings['formApp']}\" />
				<input type=\"hidden\" name=\"module\" value=\"{$settings['formModule']}\" />
				<input type=\"hidden\" name=\"section\" value=\"{$settings['formSection']}\" />
				<input type=\"hidden\" name=\"do\" value=\"add\" />
				<input type=\"hidden\" name=\"parentId\" value=\"{$parent['parent_id']}\" />
				<input type=\"hidden\" name=\"fast_reply_used\" value=\"1\" />
				<div id='commentReply'>
					" . $editor->show('Post', array( 'type' => 'mini', 'minimize' => 1, 'autoSaveKey' => $settings['autoSaveKey'], 'warnInfo' => 'fastReply', 'editorName' => 'commentFastReply' ), "$preReply")  . "
				</div>
				<div id='commentButtons'>
				<input type='submit' name=\"submit\" class='input_submit' id='commentPost' value='{$this->lang->words['comment_button_post']}' tabindex='2' accesskey='s' />
				</div>
			</form>
		</div>
	</div>
") : ("")) . "
" . (($pages) ? ("
	<div class='topic_controls'>
		{$pages}
	</div>
") : ("")) . "";
return $IPBHTML;
}


function __f__31c7bcca0e3d53fc21b71c060f946d2a($comments, $settings, $pages, $parent, $preReply='')
{
	$_ips___x_retval = '';
	$__iteratorCount = 0;
	foreach( $comments as $id => $r )
	{
		
		$__iteratorCount++;
		$_ips___x_retval .= "
		" . ( method_exists( $this->registry->getClass('output')->getTemplate('global_comments'), 'comment' ) ? $this->registry->getClass('output')->getTemplate('global_comments')->comment($r, $parent, $settings) : '' ) . "
	
";
	}
	$_ips___x_retval .= '';
	unset( $__iteratorCount );
	return $_ips___x_retval;
}

/* -- form --*/
function form($comment, $parent, $editor="", $settings, $errors="", $do='saveEdit') {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_global_comments', $this->_funcHooks['form'] ) )
{
$count_6423425611f5b67cac0a303e456cc3cc = is_array($this->functionData['form']) ? count($this->functionData['form']) : 0;
$this->functionData['form'][$count_6423425611f5b67cac0a303e456cc3cc]['comment'] = $comment;
$this->functionData['form'][$count_6423425611f5b67cac0a303e456cc3cc]['parent'] = $parent;
$this->functionData['form'][$count_6423425611f5b67cac0a303e456cc3cc]['editor'] = $editor;
$this->functionData['form'][$count_6423425611f5b67cac0a303e456cc3cc]['settings'] = $settings;
$this->functionData['form'][$count_6423425611f5b67cac0a303e456cc3cc]['errors'] = $errors;
$this->functionData['form'][$count_6423425611f5b67cac0a303e456cc3cc]['do'] = $do;
}
$IPBHTML .= "" . (($errors) ? ("
	<p class='message error'>{$errors}</p>
") : ("")) . "
<div class='post_form'>
	<form method=\"post\" action=\"{$settings['formAction']}\" name=\"REPLIER\">
		<input type=\"hidden\" name=\"auth_key\" value=\"{$this->member->form_hash}\" />
		<input type=\"hidden\" name=\"fromApp\" value=\"{$settings['fromApp']}\" />
		<input type=\"hidden\" name=\"app\" value=\"{$settings['formApp']}\" />
		<input type=\"hidden\" name=\"module\" value=\"{$settings['formModule']}\" />
		<input type=\"hidden\" name=\"section\" value=\"{$settings['formSection']}\" />	
		<input type=\"hidden\" name=\"do\" value=\"saveEdit\" />
		<input type=\"hidden\" name=\"parentId\" value=\"{$parent['parent_id']}\" />
		<input type=\"hidden\" name=\"comment_id\" value=\"{$comment['comment']['comment_id']}\" />
		<input type=\"hidden\" name=\"auth_key\" value=\"{$this->member->form_hash}\" />
		<input type=\"hidden\" name=\"modcp\" value=\"{$this->request['modcp']}\" />
		
		<h3 class='maintitle'>
		" . (($do == 'saveEdit') ? ("
			{$this->lang->words['edit_comment']} {$parent['parent_title']}
		") : ("")) . "
		</h3>
		<div class='generic_bar'></div>
		
		" . ((!$this->memberData['member_id'] AND $this->settings['guest_captcha'] AND $this->settings['bot_antispam_type'] != 'none') ? ("
			<fieldset>
				<ul>
					<li class='field'>
						<label for=''>{$this->lang->words['guest_captcha']}</label>
					</li>
				</ul>
			</fieldset>
		") : ("")) . "
		
		<fieldset>
			{$editor}
		</fieldset>
		<fieldset class='submit'>
			<input type=\"submit\" name=\"submit\" value=\"{$this->lang->words['comment_save']}\" tabindex=\"0\" class=\"input_submit\" accesskey=\"s\" /> {$this->lang->words['or']} <a href='" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "{$settings['baseUrl']}&amp;do=findComment&amp;comment_id={$comment['comment']['comment_id']}", "public",'' ), "", "" ) . "' class='cancel' title='{$this->lang->words['cancel']}'>{$this->lang->words['cancel']}</a>
		</fieldset>
	</form>
</div>";
return $IPBHTML;
}

/* -- getEditJs --*/
function getEditJs() {
$IPBHTML = "";
$IPBHTML .= "<!--no data in this master skin-->";
return $IPBHTML;
}


}


/*--------------------------------------------------*/
/* END OF FILE                                      */
/*--------------------------------------------------*/

?>
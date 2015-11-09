<?php
/**
 * <pre>
 * Invision Power Services
 * IP.Board v3.4.5
 * License Manager
 * Last Updated: $LastChangedDate: 2013-05-28 10:00:25 -0400 (Tue, 28 May 2013) $
 * </pre>
 *
 * @author 		$Author: AndyMillne $
 * @copyright	(c) 2001 - 2009 Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/company/standards.php#license
 * @package		IP.Board
 * @subpackage	Core
 * @link		http://www.invisionpower.com
 * @version		$Rev: 12273 $
 */

if ( ! defined( 'IN_ACP' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded 'admin.php'.";
	exit();
}


class admin_core_tools_licensekey extends ipsCommand
{
	/**
	 * Main entry point
	 *
	 * @param	object		ipsRegistry reference
	 * @return	@e void
	 */
	public function doExecute( ipsRegistry $registry )
	{
		/* Load lang and skin */
		$this->registry->class_localization->loadLanguageFile( array( 'admin_tools' ) );
		$this->html = $this->registry->output->loadTemplate( 'cp_skin_tools' );
		
		/* URLs */
		$this->form_code    = $this->html->form_code    = 'module=tools&amp;section=licensekey';
		$this->form_code_js = $this->html->form_code_js = 'module=tools&section=licensekey';
						
		/* What to do */
		switch( $this->request['do'] )
		{
			case 'activate':
				$this->activate();
				break;
				
			case 'remove':
				IPSLib::updateSettings( array( 'ipb_reg_number' => '' ) );
				$this->settings['ipb_reg_number'] = '';
				// Deliberately no break as we'll do onto recahce and then default action
				
			case 'refresh':
				$this->recache();
				// Deliberately no break as we'll go on to the default action
		
			default:
				if ( $this->settings['ipb_reg_number'] )
				{
					$this->overview();
				}
				else
				{
					$this->activateForm();
				}
		}
		
		/* Output */
		$this->registry->output->html_main .= $this->registry->output->global_template->global_frame_wrapper();
		$this->registry->output->sendOutput();
	}

	/**
	 * Show Activation Form
	 */
	protected function activateForm( $error='' )
	{
		$this->registry->output->html .= $this->html->activateForm( $error );
	}
	
	/**
	 * Activate
	 */
	protected function activate()
	{
		//WF.Null - Removed	
	}
	
	/**
	 * Overview
	 */
	protected function overview()
	{
		//WF.Null - Removed
	}
	
	/**
	 * Recache License Data
	 */
	public function recache()
	{
		//WF.Null - Removed
	}
}
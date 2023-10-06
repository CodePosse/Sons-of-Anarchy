<?php

class mailer {

	function __construct
	(	
	)
	{	$this->from_domain = $GLOBALS['page']->main_domain;
	
		foreach
			(	$GLOBALS['cfg']['mailer']	as	$mailer_prop_key	=>	$mailer_prop_value
			):
			$this->$mailer_prop_key = $mailer_prop_value;
		endforeach;
		reset
		(	$GLOBALS['cfg']['mailer']
		);
/*		
		$this->from_domain = str_replace
		(	'www.'
		,	''
		,	strtolower($_SERVER['HTTP_HOST'])
		);
		if	(	substr_count($this->from_domain,'.') > 1
			):
			$subs = explode
			(	'.'
			,	$this->from_domain
			);
			$new_from_domain = '';
			foreach (	$subs	as	$slice_count	=>	$domain_slice	
				):
				if	(	$slice_count > count($subs) - 3	
					):
					$new_from_domain .=	'.'
					.	$domain_slice
					;
				endif;
			endforeach;
			$this->from_domain = substr
			(	$new_from_domain
			,	1
			);
		endif;
*/
	}
	
	function send_mail
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'subject'
					,	'to'						=>	array
						(	'blurb'						=>	'array
(	array
	(	\'address\'		=>	"$address1"
	,	\'name\'		=>	"$name1"
	)
,	array
	(	\'address\'		=>	"$address2"
	,	\'name\'		=>	"$name2"
	)
,	...
)

$address is required in each $to sub-array.  $name is optional.'
						)
					,	'from'						=>	array
						(	'blurb'						=>	'array
(	\'address\'	=>	"$address"
,	\'name\'	=>	"$name"
)

$address is required in $from array.  $name is optional.'
						,	'default_value'				=>	array
							(	'address'	=>	'automailer@'.$this->from_domain
							,	'name'		=>	$GLOBALS['page']->site_title
							)
						)
					,	'reply_to'					=>	array
						(	'blurb'						=>	'Same array formation as $to argument.'
						,	'default_value'				=>	array()
						)
					,	'cc'						=>	array
						(	'blurb'						=>	'Same array formation as $to argument. $cc and $bcc addresses can only be used when $mailer != \'mail\'.'
						,	'default_value'				=>	array()
						)
					,	'bcc'						=>	array
						(	'blurb'						=>	'Same array formation as $to argument.$cc and $bcc addresses can only be used when $mailer != \'mail\'.'
						,	'default_value'				=>	array()
						)
					,	'watchdogs'					=>	array
						(	'blurb'						=>	'Same array formation as $to argument.  Separate copies of email will be sent to all watchdog addresses.  This can aid in debugging / monitoring in cases where $mailer = \'mail\'.'
						,	'default_value'				=>	$this->watchdogs
						)
					,	'host'						=>	array
						(	'default_value'				=>	$this->host
						)
					,	'mailer'					=>	array
						(	'default_value'				=>	$this->mailer
						)
					,	'html_format'				=>	array
						(	'possible_values'			=>	array
							(	0
							,	1
							,	''
							,	'Plain Text'
							,	'HTML'
							)
						,	'default_value'				=>	$this->html_format
						)
					,	'html_body'					=>	array
						(	'default_value'				=>	''
						)
					,	'plain_text_body'			=>	array
						(	'default_value'				=>	''
						)
					,	'files_to_attach'			=>	array
						(	'blurb'						=>	'Include complete paths and filenames as single strings in numerically keyed / unkeyed array.'
						,	'default_value'				=>	array()
						)
					)
				)
			)
		);
		
		if	(	!empty
				(	$html_body
				)
			||	!empty
				(	$text_body
				)
			):
			$html_format = 
			(	empty
				(	$html_format
				)
			||	$html_format == 'Plain Text'
			)
			?	0
			:	1
			;
			
			$mail = new php_mailer();
			$mail->From     = $from['address'];
			if	(	!empty
					(	$from['name']
					)
				):
				$mail->FromName = $from['name'];
			endif;
			$mail->Subject 	= $subject;
			$mail->Host     = $host;
			$mail->Mailer   = $mailer;
			
			if	(	!empty
					(	$html_body
					)
				):
				if	(	empty
						(	$plain_text_body
						)
					):
					$plain_text_body = strip_tags
					(	str_ireplace
						(	array
							(	'<BR>'
							,	'<BR/>'
							,	'<BR />'
							)
						,	"\n"
						,	nl2br
							(	$html_body
							)
						)
					)
					.	"\n\n"
					;
				endif;
				if	(	$html_format
					):
					$mail->Body		=	$html_body;
					$mail->AltBody	=	$plain_text_body;
				else:
					$mail->Body		=	$plain_text_body;
				endif;
			else:
				$mail->Body		=	$plain_text_body;
			endif;
			
			$addresses = array();
			foreach
				(	$to	as	&$to_whom	
				):
				if	(	empty
						(	$to_whom['name']
						)
					):
					$to_whom['name'] = '';
				endif;
				$mail->AddAddress
				(	$to_whom['address']
				,	$to_whom['name']
				);
				$addresses[] = $to_whom['address'];
			endforeach;
			reset
			(	$to
			);
			
			if	(	!empty
					(	$reply_to
					)
				):
				foreach
					(	$reply_to	as	&$reply_to_whom	
					):
					if	(	empty
							(	$reply_to_whom['name']
							)
						):
						$reply_to_whom['name'] = '';
					endif;
					$mail->AddReplyTo
					(	$reply_to_whom['address']
					,	$reply_to_whom['name']
					);
					$addresses[] = $reply_to_whom['address'];
				endforeach;
				reset
				(	$reply_to
				);
			endif;
			
			if	(	$host != 'mail'
				):
				if	(	!empty
						(	$cc
						)
					):
					foreach
						(	$cc	as	&$cc_whom	
						):
						if	(	empty
								(	$cc_whom['name']
								)
							):
							$cc_whom['name'] = '';
						endif;
						$mail->AddCC
						(	$cc_whom['address']
						,	$cc_whom['name']
						);
					$addresses[] = $cc_whom['address'];
					endforeach;
					reset
					(	$cc
					);
				endif;
				
				if	(	!empty
						(	$bcc
						)
					):
					foreach
						(	$bcc	as	&$bcc_whom	
						):
						if	(	empty
								(	$bcc_whom['name']
								)
							):
							$bcc_whom['name'] = '';
						endif;
						$mail->AddBCC
						(	$bcc_whom['address']
						,	$bcc_whom['name']
						);
					$addresses[] = $bcc_whom['address'];
					endforeach;
					reset
					(	$bcc
					);
				endif;
			endif;
			
			if	(	!empty
					(	$files_to_attach
					)
				):
				foreach
					(	$files_to_attach	as	$file_to_attach	
					):
					$mail->AddAttachment
					(	$file_to_attach
					);
				endforeach;
				reset
				(	$files_to_attach
				);
			endif;
			
			if	(	empty
					(	$watchdogs
					)
				&&	debug::get_mode()
				):
				$watchdogs = $this->watchdogs;
			endif;
			
			if	(	!empty
					(	$watchdogs
					)
				):
				$sent_to = "\n";
				foreach
					(	$addresses	as	$address	
					):
					$sent_to .= "\n".$address;
				endforeach;
				reset
				(	$addresses
				);
				
				$watchdog_args = $args;
				
				$watchdog_args['html_body']			=	$html_body
				.	nl2br
					(	$sent_to
					)
				;
				$watchdog_args['plain_text_body']	=	$plain_text_body
				.	$sent_to
				;
				
				$watchdog_args['watchdogs']			=	array();
				
				foreach
					(	$watchdogs	as	$watchdog	
					):
					if	(	!in_array
							(	$watchdog['address']
							,	$addresses
							)
						):
						if	(	empty
								(	$watchdog['name']
								)
							):
							$watchdog['name'] = '';
						endif;
						
						$watchdog_args['to'] = array
						(	$watchdog
						);
						
						$sent_to_watchdog = $this->send_mail
						(	$watchdog_args
						);
					endif;
				endforeach;
				reset
				(	$watchdogs
				);
			endif;
			
			return $mail->Send();
		else:
			return false;
		endif;
	}
	
	function send_mass_mail_static
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'message_table'
					,	'message_id'
					,	'recipients'			=>	array
						(	'default_value'				=>	array()
						)
					,	'recipient_label'		=>	array
						(	'default_value'				=>	'user'
						)
					,	'from'						=>	array
						(	'blurb'						=>	'array
(	\'address\'	=>	"$address"
,	\'name\'	=>	"$name"
)

$address is required in $from array.  $name is optional.'
						,	'default_value'				=>	array
							(	'address'	=>	'automailer@'
								.	$this->from_domain
							,	'name'		=>	$GLOBALS['page']->site_title
							)
						)
					,	'reply_to'					=>	array
						(	'blurb'						=>	'Same array formation as $to argument.'
						,	'default_value'				=>	array()
						)
					,	'cc'						=>	array
						(	'blurb'						=>	'Same array formation as $to argument. $cc and $bcc addresses can only be used when $mailer != \'mail\'.'
						,	'default_value'				=>	array()
						)
					,	'bcc'						=>	array
						(	'blurb'						=>	'Same array formation as $to argument.$cc and $bcc addresses can only be used when $mailer != \'mail\'.'
						,	'default_value'				=>	array()
						)
					,	'watchdogs'					=>	array
						(	'blurb'						=>	'Same array formation as $to argument.  Separate copies of email will be sent to all watchdog addresses.  This can aid in debugging / monitoring in cases where $mailer = \'mail\'.'
						,	'default_value'				=>	array()
						)
					,	'host'						=>	array
						(	'default_value'				=>	'mail'
						)
					,	'mailer'					=>	array
						(	'default_value'				=>	'smtp'
						)
					,	'files_to_attach'			=>	array
						(	'blurb'						=>	'Include complete paths and filenames as single strings in numerically keyed / unkeyed array.'
						,	'default_value'				=>	array()
						)
					,	'debuggery'					=>	array
						(	'possible_values'			=>	array
							(	0
							,	1
							)
						,	'default_value'				=>	0
						)
					)
				)
			)
		);
		
//$debuggery = 1;
		
		$elimit = "\n";
		
		// GET MESSAGE CONTENT
		$message = $GLOBALS['dbi']->get_result_array
		(	array
			(	'table'				=>	$message_table
			,	'equals'			=>	array
				(	'id'				=>	$message_id
				)
			,	'limit'				=>	1
			,	'pop_single_row'	=>	1
			)
		);
		
		$emailed_already = 
		$emailed_now = 
		$efailed = 
		array()
		;
		
		// GET EMAILS OF THOSE USERS PREVIOUSLY SENT SAME MESSAGE
		$GLOBALS['dbi']->tables['mass_mail_log']->initialize();
		$mail_logs = $GLOBALS['dbi']->get_owned_records
		(	array
			(	'owners'	=>	array
				(	$GLOBALS['dbi']->tables['mass_mail_log']->owners['associated_'.$message_table]	=>	array
					(	$message_id
					)
				)
			,	'full_records'	=>	1
			)
		);
		
if($debuggery):debug::expose($mail_logs);endif;
		
		foreach
			(	$mail_logs	as	$mail_log
			):
			$already = 
			(	empty
				(	$mail_log['emails_sent']
				)
			)
			?	array
				(
				)
			:	explode
				(	$elimit
				,	$mail_log['emails_sent']
				)
			;
			$emailed_already = array_unique
			(	array_merge
				(	$emailed_already
				,	$already
				)
			);
		endforeach;
		natcasesort($emailed_already);
		
if($debuggery):
debug::expose("EMAILED ALREADY: ".count($emailed_already));
debug::expose($emailed_already);
endif;
		
		$dupe_active_emails = 
		$invalid_emails = 
		array();
		
		// INSERT NEW MAIL LOG RECORD
		$log_id = $GLOBALS['dbi']->insert_row
		(	array
			(	'table'	=>	'mass_mail_log'
			,	'row'	=>	array
				(	'emails_sent'			=>	''
				,	'emails_failed'			=>	''
				,	'duped_active_emails'	=>	''
				,	'invalid_emails'		=>	''
				,	'inserted'				=>	'NOW()'
				)
			)
		);
		// OWN NEW MAIL LOG RECORD
		$own_id = $GLOBALS['dbi']->insert_row
		(	array
			(	'table'	=>	'owned'
			,	'row'	=>	array
				(	'ownership_id'	=>	$GLOBALS['dbi']->tables['mass_mail_log']->owners['associated_'.$message_table]
				,	'owner_id'		=>	$message_id
				,	'owned_id'		=>	$log_id
				,	'inserted'		=>	'NOW()'
				)
			)
		);
		
		
		// EMAIL NEWSLETTER TO SELECTED MEMBERS
if($debuggery):$rows = $already = $now = $failed = 0;endif;
		$sending = 
		$failing = 
		$duping = 
		$validating = 
		0
		;
		
		foreach (	$recipients	as	$recipient
			):
			set_time_limit(30);
			$email = strtolower
			(	$recipient['address']
			);
			$db_safe_email = addslashes
			(	$email
			);
			
			$sql = "	UPDATE	mass_mail_log
						SET		updated			=	NOW()	";
			
if($debuggery):
$rows++;
debug::expose('ATTEMPTING ROW #'.$rows.': '.$email);
endif;
			
			// VALIDATE EMAIL SYNTAX
			if	(	!validator::is_email_address
					(	$email
					)
				):
				
// if($debuggery):debug::expose(validator::return_errors());endif;
				
				$invalid_emails[] = $email;
				$sql .=
				(	$validating	
				)
				?	"	,	invalid_emails	=	CONCAT(invalid_emails,'$elimit','$db_safe_email')	"
				:	"	,	invalid_emails	=	'$db_safe_email'	"
				;
				$validating++;
				
if($debuggery):debug::expose('INVALID NOW #'.$validating.': '.$email);endif;
				
				validator::clear_errors();
			else:
				$allow_empty_sql_string = 1;
				if	(	in_array
						(	$email
						,	$emailed_now
						)
					&&	!in_array
						(	$email
						,	$dupe_active_emails
						)
					):
					$dupe_active_emails[] = $email;
					$sql .=
					(	$duping	
					)
					?	"	,	duped_active_emails	=	CONCAT(duped_active_emails,'$elimit','$db_safe_email')	"
					:	"	,	duped_active_emails	=	'$db_safe_email'	"
					;
					$duping++;
					$allow_empty_sql_string = 0;
				endif;
				if	(	!in_array
						(	$email
						,	$emailed_already
						)
					):
					
					$args = array
					(	'html_body'		=>	$message['body']
					,	'html_format'	=>	$recipient['html_format']
					);
					
					$args['to'] = array
					(	array
						(	'address'	=>	$email
						,	'name'		=>	$recipient['name']
						)
					);
					
if($debuggery):	
// THE FOLLOWING LINE TESTS THE LOGGING SYSTEM
$sent = rand(0,1);
else:
					$sent = $this->send_mail
					(	$args
					);
endif;
					
					if	(	$sent
						):
						
if($debuggery):
$now++;
debug::expose('EMAILED NOW #'.$now.': '.$email);
endif;
						
						$emailed_already[] = $email;
						$emailed_now[] = $email;
						$sql .= 
						(	$sending	
						)
						?	"	,	emails_sent	=	CONCAT(emails_sent,'$elimit','$db_safe_email')	"
						:	"	,	emails_sent	=	'$db_safe_email'	"
						;
						$sending++;
					else:
					
if($debuggery):
$failed++;
debug::expose('FAILED NOW #'.$failed.': '.$email);
endif;
						
						if	(	!in_array
								(	$email
								,	$efailed
								)
							):
							$efailed[] = $email;
							$sql .= 
							(	$failing	
							)
							?	"	,	emails_failed	=	CONCAT(emails_failed,'$elimit','$db_safe_email')	"
							:	"	,	emails_failed	=	'$db_safe_email'	"
							;
							$failing++;
						endif;
					endif;
				else:
					if	(	$allow_empty_sql_string
						):
						$sql = '';
					endif;
					
if($debuggery):
$already++;
debug::expose('EMAILED ALREADY #'.$already.': '.$email);
endif;
					
				endif;
			endif;
			if	(	strpos
					(	$sql
					,	','
					)
				):
				$sql .= "	WHERE	ID = $log_id	";
				
if($debuggery):debug::expose($sql);endif;
				
				$result = $GLOBALS['dbi']->get_result
				(	$sql
				);
			endif;
		endforeach;
		
		$mailed_count = count
		(	$emailed_now
		);
		$failed_count = count
		(	$efailed
		);
		$duped_count = count
		(	$dupe_active_emails
		);
		$invalid_count	= count
		(	$invalid_emails
		);
		
		// TIDY UP THE ALPHABETICAL ORDERING OF EMAIL LISTS, JUST IN CASE IT GOT SCREWED UP 
		
		extract
		(	$GLOBALS['dbi']->get_result_array
			(	array
				(	'table'	=>	'mass_mail_log'
				,	'equals'	=>	array
					(	'id'		=>	$log_id
					)
				,	'pop_single_row'	=>	1
				)
			)
		);
		
		// CHECK DB ENTRIES AGAINST REAL COUNTS
		$emails_sent = 
		(	empty
			(	$emails_sent
			)
		)
		?	array
			(	
			)
		:	explode
			(	$elimit
			,	$emails_sent
			)
		;
		sort
		(	$emails_sent
		);
		$emails_sent_count = count
		(	$emails_sent
		);
		if	(	$emails_sent_count	!=	$mailed_count
			):
			debug::expose('(count($emails_sent) != $mailed_count) // THERE WAS A PROBLEM HERE // WHAT TO DO ABOUT IT?');
			debug::expose('count($emails_sent) = '.$emails_sent_count);
			debug::expose('$mailed_count = '.$mailed_count);
			debug::expose('count(array_unique($emails_sent)) = '.count(array_unique($emails_sent)));
			debug::expose($emails_sent);
		endif;
		
		$emails_failed = 
		(	empty
			(	$emails_failed
			)
		)
		?	array
			(	
			)
		:	explode
			(	$elimit
			,	$emails_failed
			)
		;
		sort
		(	$emails_failed
		);
		$emails_failed_count = count
		(	$emails_failed
		);
		if	(	$emails_failed_count	!=	$failed_count
			):
			debug::expose('(count($emails_failed) != $failed_count) // THERE WAS A PROBLEM HERE // WHAT TO DO ABOUT IT?');
			debug::expose('count($emails_failed) = '.$emails_failed_count);
			debug::expose('$failed_count = '.$failed_count);
			debug::expose('count(array_unique($emails_failed)) = '.count(array_unique($emails_failed)));
			debug::expose($emails_failed);
		endif;
		
		$duped_active_emails = 
		(	empty
			(	$duped_active_emails
			)
		)
		?	array
			(	
			)
		:	explode
			(	$elimit
			,	$duped_active_emails
			)
		;
		sort
		(	$duped_active_emails
		);
		$duped_active_emails_count = count
		(	$duped_active_emails
		);
		if	(	$duped_active_emails_count	!=	$duped_count
			):
			debug::expose('(count($duped_active_emails) != $duped_count) // THERE WAS A PROBLEM HERE // WHAT TO DO ABOUT IT?');
			debug::expose('count($duped_active_emails) = '.$duped_active_emails_count);
			debug::expose('$duped_count = '.$duped_count);
			debug::expose('count(array_unique($duped_active_emails)) = '.count(array_unique($duped_active_emails)));
			debug::expose($duped_active_emails);
		endif;
		
		$invalid_emails = 
		(	empty
			(	$invalid_emails
			)
		)
		?	array
			(	
			)
		:	explode
			(	$elimit
			,	$invalid_emails
			)
		;
		sort
		(	$invalid_emails
		);
		$invalid_emails_count = count
		(	$invalid_emails
		);
		if	(	$invalid_emails_count	!=	$invalid_count
			):
			debug::expose('(count($invalid_emails) != $invalid_count) // THERE WAS A PROBLEM HERE // WHAT TO DO ABOUT IT?');
			debug::expose('count($invalid_emails) = '.$invalid_emails_count);
			debug::expose('$invalid_count = '.$invalid_count);
			debug::expose('count(array_unique($invalid_emails)) = '.count(array_unique($invalid_emails)));
			debug::expose($invalid_emails);
		endif;
		
		$message_label = strings::label
		(	$message_table
		.	' '
		.	$message_id
		);
		$recipients_label = strings::pluralize
		(	strtolower
			(	strings::label
				(	$recipient_label
				)
			)
		);
		$details = '<div class="mass_mail_report">';
		if	(	$mailed_count
			):
			$details .= $message_label
			.	' sent to '
			.	$mailed_count
			.	' '
			.	$recipients_label
			.	'<br /><ol class="mass_mail_report_list mass_mail_report_list_mailed">'
			;
			foreach
				(	$emails_sent as $email_now
				):
				$details .= '<li class="mass_mail_report_list_item">'
				.	$email_now
				.	'</li>'
				;
			endforeach;
			$details .= '</ol><br />';
		else:
			$details .= 'No '
			.	$recipients_label
			.	' were emailed at this time.  '
			;
			$details .= 
			(	$failed_count	
			)
			?	'All remaining unmailed '
				.	$recipients_label
				.	' have invalid email addresses.'
			:	'This '
				.	$message_label
				.	' has already been sent to all eligible '
				.	$recipients_label
				.	'.'
			;
		endif;
		
		if	(	$failed_count
			):
			$details .= 'Delivery failed for the following '
			.	$failed_count
			.	' email addresses:<br /><ol class="mass_mail_report_list mass_mail_report_list_failed">'
			;
			foreach
				(	$emails_failed	as	$efail	
				):
				$details .= '<li class="mass_mail_report_list_item">'
				.	$efail
				.	'</li>'
				;
			endforeach;
			$details .= '</ol><br />';
		endif;
		
		if	(	$duped_count
			):
			$details .= 'The emails belonging to the following '
			.	$duped_count
			.	' eligible '
			.	$recipients_label
			.	' are listed more than once in the database.  These addresses have only been sent one copy of the '
			.	$message_label
			.	', but it\'s still a good data management practice to delete duplicates.<br /><ol class="mass_mail_report_list mass_mail_report_list_duped">'
			;
			foreach
				(	$duped_active_emails	as	$edupe	
				):
				$details .= '<li class="mass_mail_report_list_item">'
				.	$edupe
				.	'</li>'
				;
			endforeach;
			$details .= '</ol><br />';
		endif;
		
		if	(	$invalid_count
			):
			$details .= 'The emails belonging to the following '
			.	$invalid_count
			.	' '
			.	$recipients_label
			.	' are invalid and cannot be mailed.<br /><ol class="mass_mail_report_list mass_mail_report_list_invalid">'
			;
			foreach
				(	$invalid_emails	as	$ebad	
				):
				$details .= '<li class="mass_mail_report_list_item">'
				.	$ebad
				.	'</li>'
				;
			endforeach;
			$details .= '</ol><br />';
		endif;
		$details .= '</div>';
		
		$GLOBALS['dbi']->get_result
		(	"	UPDATE	mass_mail_log
				SET		emails_sent_count			=	".($emails_sent_count+0)."
				,		emails_sent					=	'".addslashes(implode($elimit,$emails_sent))."'
				,		emails_failed_count			=	".($emails_failed_count+0)."
				,		emails_failed				= 	'".addslashes(implode($elimit,$efailed))."'
				,		duped_active_emails_count	=	".($duped_active_emails_count+0)."
				,		duped_active_emails			=	'".addslashes(implode($elimit,$duped_active_emails))."'
				,		invalid_emails_count		=	".($invalid_emails_count+0)."
				,		invalid_emails				=	'".addslashes(implode($elimit,$invalid_emails))."'
				WHERE	id							=	$log_id
			"
		);
		
		return $log_id;
	}
	
	/*
	function send_mass_mail_personalized
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'message_table'
					,	'message_id'
					,	'recipients_table'			=>	array
						(	'default_value'				=>	'user'
						)
					,	'recipients_email_field'	=>	array
						(	'default_value'				=>	'email'
						)
					,	'recipients_where'			=>	array
						(	'default_value'				=>	'id > 0'
						)
					,	'recipient_name_mask'		=>	array
						(	'default_value'				=>	'$name_first $name_last'
						)
					,	'recipient_field_defaults'	=>	array
						(	'blurb'						=>	'An array of values keyed to fields from the recipient table.

Before each copy of the message is sent, the function will attempt to replace any dollar signed variable references in the message bodies that match field names in the recipient table.  In this way, messages can be customized with recipient names and the like.

This argument is used to provide default values that may be inserted during the variable replacement if the corresponding field in the recipient row is empty.

For example: \'last_name\' => \'Site Subscriber\'.

The keys of this argument\'s array may also be used to determine the order in which the variables are replaced.  If keys matching table fields are present with empty strings supplied as values, the values from the recipient table row will be inserted instead.

If this argument is left empty, the variables will be replaced with values in REVERSE order of the corresponding field arrangement in the recipient table, to keep longer variable names from being partially replaced.  This is assuming that fields with longer names will occur later in the table.

For example: the \'email\' field appears close to the top of the table, while \'email_preference\' comes later.

If this argument is set, but contains fewer keys than are present in the table, the keys present in this argument will be replaced first, followed then by any remaining keys in REVERSE order of the table columns.'
						,	'default_value'				=>	array()
						)
					,	'from'						=>	array
						(	'blurb'						=>	'array
(	\'address\'	=>	"$address"
,	\'name\'	=>	"$name"
)

$address is required in $from array.  $name is optional.'
						,	'default_value'				=>	array
							(	'address'	=>	'automailer@'.$this->from_domain
							,	'name'		=>	$GLOBALS['page']->site_title
							)
						)
					,	'reply_to'					=>	array
						(	'blurb'						=>	'Same array formation as $to argument.'
						,	'default_value'				=>	array()
						)
					,	'cc'						=>	array
						(	'blurb'						=>	'Same array formation as $to argument. $cc and $bcc addresses can only be used when $mailer != \'mail\'.'
						,	'default_value'				=>	array()
						)
					,	'bcc'						=>	array
						(	'blurb'						=>	'Same array formation as $to argument.$cc and $bcc addresses can only be used when $mailer != \'mail\'.'
						,	'default_value'				=>	array()
						)
					,	'watchdogs'					=>	array
						(	'blurb'						=>	'Same array formation as $to argument.  Separate copies of email will be sent to all watchdog addresses.  This can aid in debugging / monitoring in cases where $mailer = \'mail\'.'
						,	'default_value'				=>	array()
						)
					,	'host'						=>	array
						(	'default_value'				=>	'mail'
						)
					,	'mailer'					=>	array
						(	'default_value'				=>	'smtp'
						)
					,	'files_to_attach'			=>	array
						(	'blurb'						=>	'Include complete paths and filenames as single strings in numerically keyed / unkeyed array.'
						,	'default_value'				=>	array()
						)
					)
				)
			)
		);
		
		// GET MESSAGE CONTENT
		$message = $GLOBALS['dbi']->get_result_array
		(	array
			(	'table'				=>	$message_table
			,	'equals'			=>	array
				(	'id'				=>	$message_id
				)
			,	'limit'				=>	1
			,	'pop_single_row'	=>	1
			)
		);
		
		$emailed_already = 
		$emailed_now = 
		$efailed = 
		array()
		;
		
		// GET EMAILS OF THOSE USERS PREVIOUSLY SENT SAME MESSAGE
		$sql = "	SELECT	*
					FROM	mass_mail_log
					WHERE	message_table	=	'$message_table'
					AND		message_id		=	'$message_id'
					ORDER					BY	inserted
					";
		$result = $GLOBALS['dbi']->get_result($sql);
		while (	$row = $result->fetch_array(MYSQLI_ASSOC)
			):
			extract($row);
			$already = 
			(	empty($emails_sent)
			)
			?	array()
			:	explode
				(	"\n"
				,	$emails_sent
				)
			;
			$emailed_already = array_merge
			(	$emailed_already
			,	$already
			);
		endwhile;
		//	debug::expose("EMAILED ALREADY: ".count($emailed_already));
		//	debug::expose($emailed_already);
		
		$dupe_active_emails = 
		$invalid_emails = 
		array();
		
		$log_id = $GLOBALS['dbi']->insert_row
		(	array
			(	'table'	=>	'mass_mail_log'
			,	'row'	=>	array
				(	'message_table'			=>	$message_table
				,	'message_id'			=>	$message_id
				,	'emails_sent'			=>	''
				,	'emails_failed'			=>	''
				,	'duped_active_emails'	=>	''
				,	'invalid_emails'		=>	''
				,	'inserted'				=>	'NOW()'
				)
			)
		);
		
		// EMAIL NEWSLETTER TO SELECTED MEMBERS
		//	$rows = $already = $now = $failed = 0;
		$sending = 
		$failing = 
		$duping = 
		$validating = 
		0
		;
		
		$recipients = $GLOBALS['dbi']->get_result_array
		(	array
			(	'table'		=>	$recipients_table
			,	'key_by'	=>	$recipients_email_field
			,	'where'		=>	$recipients_where
			,	'order_by'	=>	array
				(	$recipients_email_field	=>	''
				)
			)
		);
		
		unset
		(	$args['message_table']
		,	$args['message_id']
		,	$args['recipients_table']
		,	$args['recipients_email_field']
		,	$args['recipients_where']
		,	$args['recipient_field_defaults']
		,	$args['recipient_name_mask']
		);
		foreach (	$recipients	as	$recipient
			):
			set_time_limit(30);
			$email = strtolower
			(	$recipient[$recipients_email_field]
			);
			
			$sql = "	UPDATE	mass_mail_log
						SET		updated			=	NOW()	";
		//	$rows++;
		//	debug::expose('ATTEMPTING ROW #'.$rows.': '.$recipient['id'].': '.$recipient[$recipient_email_field]);
			
			// VALIDATE EMAIL SYNTAX
			if	(	!validator::is_email_address($email)
				):
				$invalid_emails[] = $email;
				$sql .=
				(	$validating	
				)
				?	"	,	invalid_emails	=	CONCAT(invalid_emails,'\n','$email')	"
				:	"	,	invalid_emails	=	'$email'	"
				;
				$validating++;
			else:
				$allow_empty_sql_string = 1;
				if	(	in_array
						(	$email
						,	$emailed_now
						)
					&&	!in_array
						(	$email
						,	$dupe_active_emails
						)
					):
					$dupe_active_emails[] = $email;
					$sql .=
					(	$duping	
					)
					?	"	,	duped_active_emails	=	CONCAT(duped_active_emails,'\n','$email')	"
					:	"	,	duped_active_emails	=	'$email'	"
					;
					$duping++;
					$allow_empty_sql_string = 0;
				endif;
				if	(	!in_array
						(	$email
						,	$emailed_already
						)
					):
					
					// REPLACE VARIABLE REFERENCES IN HTML AND TEXT BODIES WITH RECIPIENT-SPECIFIC FIELD INFO
					
					$bodies	= array
					(	'html_body'			=>	''
					,	'plain_text_body'	=>	''
					);
					foreach (	$bodies	as	&$body	
						):
						if	(	!empty($message[$body])
							):
							$body = $message[$body];
						endif;
					endforeach;
					reset($bodies);
					
					$recipient_fields_replaced = array();
					$recipient_display_name = $recipient_name_mask;
					if	(	!empty($recipient_field_defaults)
						):
						foreach (	$recipient_field_defaults	as	$recipient_field_name	=>	$recipient_field_default_value	
							):
							if	(	$recipient_field_default_value == ''
								):
								$recipient_field_default_value = $recipient[$recipient_field_name];
							endif;
							foreach	(	$bodies	as	&$body	
								):
								if	(	strstr
										(	$body
										,	'$'.$recipient_field_name
										)
									):
									$body = str_replace
									(	'$'.$recipient_field_name
									,	$recipient_field_default_value
									,	$body
									);
									$recipient_fields_replaced[] = $recipient_field_name;
								endif;
							endforeach;
							reset($bodies);
							if	(	strstr
									(	$recipient_display_name
									,	'$'.$recipient_field_name
									)
								):
								$recipient_display_name = str_replace
								(	'$'.$recipient_field_name
								,	$recipient_field_default_value
								,	$recipient_display_name
								);
							endif;
						endforeach;
						reset($recipient_field_defaults);
					endif;
					$recipient_sorted = arrays::sort_by_strlen
					(	array
						(	'array'			=>	$recipient
						,	'sort_by_key'	=>	1
						,	'reverse'		=>	1
						)
					);
					foreach	(	$recipient_sorted	as	$recipient_field_name	=>	$recipient_field_value	
						):
						if	(	!in_array
								(	$recipient_field_name
								,	$recipient_fields_replaced
								)
							):
							foreach	(	$bodies	as	&$body	
								):
								if	(	strstr
										(	$body
										,	'$'.$recipient_field_name
										)
									):
									$body = str_replace
									(	'$'.$recipient_field_name
									,	$recipient_field_value
									,	$body
									);
									$recipient_fields_replaced[] = $recipient_field_name;
								endif;
							endforeach;
							reset($bodies);
							if	(	strstr
									(	$recipient_display_name
									,	'$'.$recipient_field_name
									)
								):
								$recipient_display_name = str_replace
								(	'$'.$recipient_field_name
								,	$recipient_field_value
								,	$recipient_display_name
								);
							endif;
						endif;
					endforeach;
					reset($recipient_sorted);
					
					$args['to'] = array
					(	array
						(	'address'	=>	$email
						,	'name'		=>	$recipient_display_name
						)
					);
					if	(	$recipient['email_preference'] == 'HTML'
						):
						$args['html_body'] = $bodies['html_body'];
					endif;
					$args['plain_text_body'] = $bodies['plain_text_body'];
					
					// WORK OUT HOW REMAINING ARGUMENTS ARE ASSIGNED IF PASSED THRU send_mass_mail() AND/OR PRESENT IN message_table RECORD ROW
//					$other_message_specific_fields = array
//					(	'from'
//					,	'reply_to'
//					,	'cc'
//					,	'bcc'
//					,	'watchdogs'
//					,	'host'
//					,	'mailer'
//					,	'files_to_attach'
//					);
					
					$sent = $this->send_mail
					(	$args
					);
					
			//		THE FOLLOWING LINE TESTS THE LOGGING SYSTEM
			//		$sent = rand(0,1);
					
					if	(	$sent
						):
			//			$now++;
			//			debug::expose('EMAILED NOW #'.$now.': '.$email);
						$emailed_already[] = $email;
						$emailed_now[] = $email;
						$sql .= 
						(	$sending	
						)
						?	"	,	emails_sent	=	CONCAT(emails_sent,'\n','$email')	"
						:	"	,	emails_sent	=	'$email'	"
						;
						$sending++;
					else:
			//			$failed++;
			//			debug::expose('FAILED NOW #'.$failed.': '.$email);
						if	(	!in_array
								(	$email
								,	$efailed
								)
							):
							$efailed[] = $email;
							$sql .= 
							(	$failing	
							)
							?	"	,	emails_failed	=	CONCAT(emails_failed,'\n','$email')	"
							:	"	,	emails_failed	=	'$email'	"
							;
							$failing++;
						endif;
					endif;
				else:
					if	(	$allow_empty_sql_string
						):
						$sql = '';
					endif;
			//		$already++;
			//		debug::expose('EMAILED ALREADY #'.$already.': '.$email);
				endif;
			endif;
			if	(	strstr($sql,',')
				):
				$sql .= "	WHERE	ID = $log_id	";
				$result = $GLOBALS['dbi']->get_result($sql);
			endif;
		endforeach;
		
		$mailed_count = count($emailed_now);
		$failed_count = count($efailed);
		$duped_count = count($dupe_active_emails);
		$invalid_count	= count($invalid_emails);
		
		// TIDY UP THE ALPHABETICAL ORDERING OF EMAIL LISTS, JUST IN CASE IT GOT SCREWED UP 
		
		$sql = "	SELECT	*
					FROM 	mass_mail_log
					WHERE	id				=	$log_id
					";
		extract_result($sql);
		
		// CHECK DB ENTRIES AGAINST REAL COUNTS
		$emails_sent = 
		(	empty($emails_sent)	
		)
		?	array()
		:	explode("\n",$emails_sent)
		;
		sort($emails_sent);
		if	(	count($emails_sent)	!=	$mailed_count
			):
			debug::expose('(count($emails_sent) != $mailed_count) // THERE WAS A PROBLEM HERE // WHAT TO DO ABOUT IT?');
			debug::expose('count($emails_sent) = '.count($emails_sent));
			debug::expose('$mailed_count = '.$mailed_count);
			debug::expose('count(array_unique($emails_sent)) = '.count(array_unique($emails_sent)));
			debug::expose($emails_sent);
		endif;
		
		$emails_failed = 
		(	empty($emails_failed)	
		)
		?	array()
		:	explode("\n",$emails_failed)
		;
		sort($emails_failed);
		if	(	count($emails_failed)	!=	$failed_count
			):
			debug::expose('(count($emails_failed) != $failed_count) // THERE WAS A PROBLEM HERE // WHAT TO DO ABOUT IT?');
			debug::expose('count($emails_failed) = '.count($emails_failed));
			debug::expose('$failed_count = '.$failed_count);
			debug::expose('count(array_unique($emails_failed)) = '.count(array_unique($emails_failed)));
			debug::expose($emails_failed);
		endif;
		
		$duped_active_emails = 
		(	empty($duped_active_emails)
		)
		?	array()
		:	explode("\n",$duped_active_emails)
		;
		sort($duped_active_emails);
		if	(	count($duped_active_emails)	!=	$duped_count
			):
			debug::expose('(count($duped_active_emails) != $duped_count) // THERE WAS A PROBLEM HERE // WHAT TO DO ABOUT IT?');
			debug::expose('count($duped_active_emails) = '.count($duped_active_emails));
			debug::expose('$duped_count = '.$duped_count);
			debug::expose('count(array_unique($duped_active_emails)) = '.count(array_unique($duped_active_emails)));
			debug::expose($duped_active_emails);
		endif;
		
		$invalid_emails = 
		(	empty($invalid_emails)
		)
		?	array()
		:	explode("\n",$invalid_emails)
		;
		sort($invalid_emails);
		if	(	count($invalid_emails)	!=	$invalid_count
			):
			debug::expose('(count($invalid_emails) != $invalid_count) // THERE WAS A PROBLEM HERE // WHAT TO DO ABOUT IT?');
			debug::expose('count($invalid_emails) = '.count($invalid_emails));
			debug::expose('$invalid_count = '.$invalid_count);
			debug::expose('count(array_unique($invalid_emails)) = '.count(array_unique($invalid_emails)));
			debug::expose($invalid_emails);
		endif;
		
		$sql = "	UPDATE	mass_mail_log
					SET		emails_sent				=	'".implode("\n",$emails_sent)."'
					,		emails_failed			= 	'".implode("\n",$efailed)."'
					,		duped_active_emails		=	'".implode("\n",$duped_active_emails)."'
					,		invalid_emails			=	'".implode("\n",$invalid_emails)."'
					WHERE	id						=	$log_id
					";
		$result = get_result($sql);
		
		$message_label = strings::label($message_table);
		$recipients_label = strings::pluralize
		(	strtolower
			(	strings::label
				(	$recipients_table
				)
			)
		);
		$details = '<div class="mass_mail_report">';
		if	(	$mailed_count
			):
			$details .= $message_label
			.	' sent to '
			.	$mailed_count
			.	' '
			.	$recipients_label
			.	'<br /><ol class="mass_mail_report_list mass_mail_report_list_mailed">'
			;
			foreach ($emails_sent as $email_now):
				$details .= '<li class="mass_mail_report_list_item">'
				.	$email_now
				.	'</li>'
				;
			endforeach;
			$details .= '</ol><br />';
		else:
			$details .= 'No '
			.	$recipients_label
			.	' were emailed at this time.  '
			;
			$details .= 
			(	$failed_count	
			)
			?	'All remaining unmailed '
				.	$recipients_label
				.	' have invalid email addresses.'
			:	'This '
				.	$message_label
				.	' has already been sent to all eligible '
				.	$recipients_label
				.	'.'
			;
		endif;
		
		if	(	$failed_count
			):
			$details .= 'Delivery failed for the following '
			.	$failed_count
			.	' email addresses:<br /><ol class="mass_mail_report_list mass_mail_report_list_failed">'
			;
			foreach (	$emails_failed	as	$efail	
				):
				$details .= '<li class="mass_mail_report_list_item">'
				.	$efail
				.	'</li>'
				;
			endforeach;
			$details .= '</ol><br />';
		endif;
		
		if	(	$duped_count
			):
			$details .= 'The emails belonging to the following '
			.	$duped_count
			.	' eligible '
			.	$recipients_label
			.	' are listed more than once in the database.  These addresses have only been sent one copy of the '
			.	$message_label
			.	', but it\'s still a good data management practice to delete duplicates.<br /><ol class="mass_mail_report_list mass_mail_report_list_duped">'
			;
			foreach (	$duped_active_emails	as	$edupe	
				):
				$details .= '<li class="mass_mail_report_list_item">'
				.	$edupe
				.	'</li>'
				;
			endforeach;
			$details .= '</ol><br />';
		endif;
		
		if	(	$invalid_count
			):
			$details .= 'The emails belonging to the following '
			.	$invalid_count
			.	' '
			.	$recipients_label
			.	' are invalid and cannot be mailed.<br /><ol class="mass_mail_report_list mass_mail_report_list_invalid">'
			;
			foreach (	$invalid_emails	as	$ebad	
				):
				$details .= '<li class="mass_mail_report_list_item">'
				.	$ebad
				.	'</li>'
				;
			endforeach;
			$details .= '</ol><br />';
		endif;
		$details .= '</div>';
		
		return $details;
	}
	*/
}


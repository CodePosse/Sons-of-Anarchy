<?php

class user {

	function __construct()
	{	foreach
			(	$GLOBALS['cfg']['login']	as	$key => $value	
			):
			$this->$key = $value;
		endforeach;
		reset
		(	$GLOBALS['cfg']['login']
		);

		$this->table = &$GLOBALS['dbi']->tables['user'];
		$this->table->initialize();
		
		$this->cookie = $GLOBALS['cfg']['cookies']['login'];
		$this->cookie['name'] = 
		$GLOBALS['cfg']['cookies']['login']['name'] = 
		$GLOBALS['page']->cookies['login']['name'] = 
		strtoupper
		(	$GLOBALS['dbi']->name
			.	$this->table->name
		);

		$this->logged_in = 0;
		
		$this->did = 
		$this->dud = 
		''
		;
		
		$this->owns = 
		$this->owners = 
		$this->roles = 
		$this->permissions = 
		$this->visible_nodes = 
		array()
		;
		
		$this->browser = $this->analyze_client();
		$this->ip = $_SERVER['REMOTE_ADDR'];
				
		if	(	!empty
				(	$GLOBALS['page']->request['z']
				)
			&&	$GLOBALS['page']->request['z'] == 'out'
			):
			$this->login
			(	0
			);
		endif;
		
		$this->record = new record
		(	$this->table->name
		);
		
		if	(	$GLOBALS['cfg']['login']['required']
			):
			$this->login_verify();
		endif;
	}

	function analyze_client
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'user_agent_string'			=>	array
						(	'default_value'	=>	''
						)
					)
				)
			)
		);
		
		if	(	empty
				(	$user_agent_string
				)
			):
			if	(	empty
					(	$_SERVER['HTTP_USER_AGENT']
					)
				):
				return false;
			else:
				$user_agent_string = $_SERVER['HTTP_USER_AGENT'];
			endif;
		endif;
		
		$client_details = array
		(	'string'	=>	$user_agent_string
		);
		
		$check_fors = array
		(	'android'
		,	'firefox'
		,	'ipad'
		,	'iphone'
		,	'ipod'
		,	'kindle'
		,	'macintosh'
		,	'msie'
		,	'safari'
		,	'windows'
		,	'webkit'
		);
		
		foreach
			(	$check_fors	as	$check_for
			):
			$client_details[$check_for] = preg_match
			(	'/'
				.	$check_for
				.	'/i'
			,	$client_details['string']
			);
		endforeach;

		$client_details['ios'] = 
		(	$client_details['ipad']
		||	$client_details['iphone']
		||	$client_details['ipod']
		)
		?	1
		:	0
		;

		$client_details['mobile'] = 
		(	preg_match
			(	'/mobile/i'
			,	$client_details['string']
			)
		||	$client_details['ios']
		||	$client_details['android']
		)
		?	1
		:	0
		;
		
		if	(	$client_details['msie']
			):
			$client_details['msie'] = substr
			(	stristr
				(	$client_details['string']
				,	'msie'
				)
			,	5
			);
			$client_details['msie'] = substr
			(	$client_details['msie']
			,	0
			,	strpos
				(	$client_details['msie']
				,	'.'
				)
			);
		endif;
	
		return $client_details;
		
	}
	
	function get_permissions
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'user_roles'				=>	array
						(	'default_value'	=>	$this->roles
						)
					)
				)
			)
		);
		
		$GLOBALS['dbi']->tables['permission']->initialize();
		
		$permissions = $GLOBALS['dbi']->get_owned_records
		(	array
			(	'owners'		=>	array
				(	$GLOBALS['dbi']->tables['permission']->owners['user_role']	=>	$this->roles
				)
			,	'full_records'	=>	1
			)
		);
		
		foreach
			(	$permissions as &$permission	
			):
			foreach
				(	$permission	as	$permit_name => &$permit_value	
				):
				if	(	in_array
						(	$permit_name
						,	array
							(	'inserted'
							,	'updated'
							)
						)
					):
					unset
					(	$permission[$permit_name]
					);
				else:
					if	(	$permit_name != 'id'
						):
						if	(	empty
								(	$permit_value
								)
							):
							$permit_value = array();
						else:
							if	(	strpos
									(	$permit_value
									,	'$'
									)
								):
								$permit_value = explode
								(	"\n"
								,	$permit_value
								);
							else:
								$permit_value = preg_replace
								(	'/[^_A-Za-z0-9]+/'
								,	','
								,	$permit_value
								);
								$permit_value = explode
								(	','
								,	$permit_value
								);
							endif;
						endif;
					endif;
				endif;
			endforeach;
			reset
			(	$permission
			);
		endforeach;
		reset
		(	$permissions
		);
		
		$permissions_by_table = array_flip
		(	array_keys
			(	$GLOBALS['dbi']->tables
			)
		);
		foreach
			(	$permissions_by_table	as	&$table_permissions
			):
			$table_permissions = array();
		endforeach;
		reset
		(	$permissions_by_table
		);
		
		foreach
			(	$permissions as &$permission	
			):
			if	(	empty
					(	$permission['tables']
					)
				):
				foreach
					(	$permissions_by_table	as	&$table_permissions	
					):
					if	(	empty
							(	$table_permissions
							)
						):
						$table_permissions = $permission;
						$table_permissions['permissions_applied'] = array();
					else:
						foreach
							(	$permission	as	$permit_name	=>	$permit_value	
							):
							$table_permissions[$permit_name] = $permit_value;
						endforeach;
						reset
						(	$permission
						);
					endif;
					$table_permissions['permissions_applied'][] = $table_permissions['id'];
					$table_permissions['view_table_if'] = $table_permissions['view_tables_if'];
					unset
					(	$table_permissions['id']
					,	$table_permissions['tables']
					,	$table_permissions['view_tables_if']
					);
				endforeach;
				reset
				(	$permissions_by_table
				);
				unset
				(	$permission
				);
			endif;
		endforeach;
		reset
		(	$permissions
		);
		
		foreach
			(	$permissions as &$permission	
			):
			foreach
				(	$permission['tables']	as	$permit_table
				):
				foreach
					(	$permission	as	$permit_name	=>	$permit_value	
					):
					$permissions_by_table[$permit_table][$permit_name] = $permit_value;
				endforeach;
				reset
				(	$permission
				);
				$permissions_by_table[$permit_table]['permissions_applied'][] = $permissions_by_table[$permit_table]['id'];
				$permissions_by_table[$permit_table]['view_table_if'] = $permissions_by_table[$permit_table]['view_tables_if'];
				unset
				(	$permissions_by_table[$permit_table]['id']
				,	$permissions_by_table[$permit_table]['tables']
				,	$permissions_by_table[$permit_table]['view_tables_if']
				);
			endforeach;
		endforeach;
		
		return $permissions_by_table;
	}
	
	function get_roles()
	{	return
		(	empty
			(	$this->owners[$GLOBALS['dbi']->tables[$this->table->name]->owners['user_role']]
			)
		)
		?	array()
		:	$this->owners[$GLOBALS['dbi']->tables[$this->table->name]->owners['user_role']]
		;
	}
	
	function get_visible_nodes()
	{	$GLOBALS['dbi']->tables['node']->initialize();
		
		$get_array = array
		(	'table'		=>	'node'
		,	'equals'	=>	array
			(	'status'	=>	'Active'
			)
		);
		
		if	(	!empty
				(	$GLOBALS['dbi']->tables['node']->owners['user_role']
				)
			):
			$get_array['owners'] = array
			(	$GLOBALS['dbi']->tables['node']->owners['user_role']	=>	$this->roles
			);
		endif;
		
		return $GLOBALS['dbi']->get_result_array
		(	$get_array
		);
	}
	
	function is_admin
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'admin_role_id'				=>	array
						(	'default_value'				=>	1
						)
					)
				)
			)
		);
		
		return
		(	!empty
			(	$GLOBALS['user']->owners[$GLOBALS['dbi']->tables['user']->owners['user_role']][$admin_role_id]
			)
		);
		
	}
	
	function log_activity
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'action'
					,	'id'		=>	array
						(	'default_value'	=>	$this->creds['id']
						)
					,	'time'		=>	array
						(	'default_value'	=>	date
							(	'Y-m-d H:i:s'
							)
						)
					)
				)
			)
		);
		
		$inserted_log = $GLOBALS['dbi']->insert_row
		(	array
			(	'table'	=>	'user_log'
			,	'row'	=>	array
				(	'action'	=>	$action
				,	'inserted'	=>	$time
				)
			)
		);	
		
		return $GLOBALS['dbi']->insert_row
		(	array
			(	'table'	=>	'owned'
			,	'row'	=>	array
				(	'ownership_id'	=>	$GLOBALS['dbi']->tables['user']->owns['activity_log']
				,	'owner_id'		=>	$id
				,	'owned_id'		=>	$inserted_log
				)
			)
		);
	
	}
	
	function login
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'login'		=>	array
						(	'blurb'	=>	'True: Log user in.
False: Log user out.'
						,	'possible_values'	=>	array
							(	0
							,	1
							)
						,	'default_value'	=>	1
						)
					,	'redirect'	=>	array
						(	'blurb'		=>	'Target to redirect to after logging out.'
						,	'default_value'	=>	$_SERVER['SCRIPT_NAME']
						)
					)
				)
			)
		);

		if	(	$login
			):
			$log_time = date
			(	'Y-m-d H:i:s'
			);
			
			$result = $GLOBALS['dbi']->get_result
			(	array
				(	'sql'		=>	"
						UPDATE	`".$this->table->name."`
						SET		login_count		=	login_count + 1
						,		last_login		=	'$log_time'
						WHERE	id				=	".$this->creds['id']."
					"
				,	'tables'	=>	array
					(	$this->table->name
					)
				)
			);
			
			// LOG USER ACTIVITY
			$this->log_activity
			(	array
			 	(	'id'		=>	$this->creds['id']
				,	'action'	=>	'LOGGED IN from '
					.	$this->ip
				,	'time'		=>	$log_time
				)
			);

			setcookie
			(	$this->cookie['name']
			,	encryption::my_crypt
				(	array
					(	'data'	=>	$this->creds['id']
					,	'key'	=>	$GLOBALS['page']->crypt_key
					)
				)
			,	(	time()
				+	$this->cookie['expire_in_seconds']
				)
			,	'/'
			,	$GLOBALS['cfg']['cookie_domain']
			);

		else:
			$GLOBALS['page']->kill_cookies
			(	$redirect
			);
		endif;
		$GLOBALS['page']->redirect
		(	$redirect
		);
	}
	
	function login_verify
	(
	)
	{	// CHECK FOR LOGGED IN COOKIE
		if	(	empty
				(	$_COOKIE[$this->cookie['name']]
				)
			):
			
			//	IF NO USERS, GO DIRECTLY TO CREATE FIRST USER
			$any_users_present = $GLOBALS['dbi']->get_result_array
			(	array
				(	'table'				=>	'user'
				,	'count_only'		=>	1
				)
			);
			
			if	(	empty
					(	$GLOBALS['page']
					)
				):
				$GLOBALS['page'] = new page();
			endif;
			
			if	(	!$any_users_present
				):
				$GLOBALS['page']->redirect
				(	$_SERVER['SCRIPT_URI']
					.	'?z=user&id=0&waive=1'
				);
			else:
				unset
				(	$any_users_present
				);
			endif;
			
			//	IF NO COOKIE, DISPLAY LOGIN FORM
			$GLOBALS['page']->request['z'] = 'login';
			
			// CHECK FOR FORM SUBMITTED CREDENTIALS
			if	(	isset
					(	$GLOBALS['page']->request[$this->field]
					)
				):
				if	(	empty
						(	$GLOBALS['page']->request[$this->field]
						)
					):
					$this->dud = 'No '
					.	$GLOBALS['dbi']->tables[$this->table->name]->fields[$this->field]->title
					.	' submitted.'
					;
				else:
					// VALIDATE CREDENTIALS
					
					// GET MATCHING USER
					$this->creds = $GLOBALS['dbi']->get_result
					(	array
						(	'sql'			=>	"
								SELECT	*
								FROM	`".$this->table->name."`
								WHERE	".$this->field." = '".$GLOBALS['dbi']->real_escape_string($GLOBALS['page']->request[$this->field])."'
								AND		status			=	'Active'
								AND		(	active_from		IS	NULL
										OR	active_from		=	''
										OR	active_from		=	'0000-00-00 00:00:00'
										OR	active_from		<=	'".date('Y-m-d H:i:s')."'
										)
								AND		(	active_until	IS	NULL
										OR	active_until	=	''
										OR	active_until	=	'0000-00-00 00:00:00'
										OR	active_until	>	'".date('Y-m-d H:i:s')."'
										)
								ORDER					BY	inserted	DESC
								LIMIT	1
							"
						,	'tables'		=>	array
							(	$this->table->name
							)
						,	'return_array'	=>	1
						)
					);

					if	(	count
							(	$this->creds
							)	==	1
						):
						$this->creds = array_pop
						(	$this->creds
						);
						
						// DECRYPT PASSWORD
						$this->creds['password'] = encryption::my_crypt
						(	array
							(	'data'		=>	$this->creds['password']
							,	'key'		=>	$GLOBALS['page']->crypt_key
							,	'encrypt'	=>	0
							)
						);

						if	(	empty
								(	$GLOBALS['page']->request['forgotten']
								)
							):
							if	(	empty
									(	$GLOBALS['page']->request['password']
									)
								):
								$this->dud = 'No password submitted.';
							else:
								// VALIDATE PASSWORD
								if	(	$GLOBALS['page']->request['password'] == $this->creds['password']
									):

									// IF CREDENTIALS VALID, FIND EXISTING PLAYER PARTNER RECORD
									if	(	!empty
											(	$GLOBALS['page']->request['username']
											)
										):
										$player_partners = $GLOBALS['dbi']->get_result_array
										(	array
											(	'table'				=>	'player'
											,	'equals'			=>	array
												(	'status'			=>	'Active'
												)
											,	'where'				=>	"
													LCASE(summoner_name)	=	'".strtolower($GLOBALS['page']->request['username'])."'
												"
											)
										);
										$player_partner_count = count
										(	$player_partners
										);
										$redirector = explode
										(	'.'
										,	$GLOBALS['page']->request['redirect']
										);
										if	(	$player_partner_count
											):
											if	(	$player_partner_count	==	1
												):
												$player_partner = array_pop
												(	$player_partners
												);
											else:
												foreach
													(	$player_partners	as	$player_partner
													):
													if	(	$player_partner['region'] == $redirector[1]
														):
														break;
													endif;
													unset
													(	$player_partner
													);
												endforeach;
												if	(	empty
														(	$player_partner
														)
													):
													$player_partner = reset
													(	$player_partners
													);
												endif;
											endif;
										endif;
										if	(	!empty
												(	$player_partner['region']
												)
											&&	$player_partner['region'] != $redirector[1]
											):
											$redirector[1] = $player_partner['region'];
											$GLOBALS['page']->request['redirect'] = implode
											(	'.'
											,	$redirector
											);
										endif;
									endif;
									
									// SET COOKIE / REDIRECT
									$this->login
									(	array
										(	'redirect'	=>	$GLOBALS['page']->request['redirect']
										)
									);
								else:
									$this->dud = 'Incorrect password for '
									.	$this->field
									.	' "'
									.	$this->creds[$this->field]
									.	'".'
									;
								endif;
							endif;
						else:
							// FORGOTTEN PASSWORD
							if	(	$this->send_password()
								):
								$this->did = 'A password reminder has been sent to '
								.	$this->creds['email']
								.	'.'
								;
							else:
								$this->dud = 'Password reminder delivery failed for '
								.	$this->field
								.	' "'
								.	$this->creds[$this->field]
								.	'".  This may be a temporary problem.   Please try again in a few minutes.  If you continue to see this error message, contact '
								.	$GLOBALS['page']->site_title
								.	' customer service.'
								;
							endif;
						endif;
					else:
						$this->dud = 'No account exists for '
						.	$this->field
						.	' "'
						.	$GLOBALS['page']->request[$this->field]
						.	'".'
						;
					endif;
				endif;
			endif;
		else:

			$this->cookie['value'] = encryption::my_crypt
			(	array
				(	'data'	=>	$_COOKIE[$this->cookie['name']]
				,	'key'	=>	$GLOBALS['cfg']['crypt_key']
				,	'encrypt'	=>	0
				)
			);
			
			$this->record->act = 'hide';
			$this->record->initialize
			(	$this->cookie['value']
			);
			
			foreach
				(	$this->record->values	as	$key	=>	$val	
				):
				if	(	$key	!=	'password'
					):
					$this->$key = $val;
				endif;
			endforeach;
/*			
			if	(	strpos
					(	$this->email
					,	$GLOBALS['page']->main_domain
					)
					==	strpos
					(	$this->email
					,	'@'
					)	
					+	1
				):
				$this->owns = $this->record->owns;
			else:
*/				$this->owners = $this->record->owners;
				$this->owns = $this->record->owns;
				$this->roles = $this->get_roles();
				$this->permissions = $this->get_permissions();
//				$this->visible_nodes = $this->get_visible_nodes();
//			endif;
			
			$this->logged_in = 1;
		endif;
	}
	
	function send_password()
	{	$password_reminder = new mailer();
		return $password_reminder->send_mail
		(	array
			(	'subject'		=>	'Password Reminder for http://'
				.	$_SERVER['HTTP_HOST']
			,	'to'			=>	array
				(	array
					(	'address'		=>	$this->creds['email']
					,	'name'			=>	$this->creds['name_first']
						.	' '
						.	$this->creds['name_last']
					)
				)
			,	'html_format'	=>	$this->creds['email_preference']
			,	'html_body'		=>	'<p>Attention '
				.	$this->creds['name_first']
				.	' '
				.	$this->creds['name_last']
				.	',</p>

<p>To log in and access the secure features of our site, visit <a href="http://'
				.	$_SERVER['HTTP_HOST']
				.	'">http://'
				.	$_SERVER['HTTP_HOST']
				.	'</a> and enter the following information:</p>

<p><ul><li><strong>'
				.	$this->field
				.	':</strong> '
				.	$this->creds[$this->field]
				.	'</li>
<li>'
				.	'<strong>password:</strong> '
				.	$this->creds['password']
				.	'</li></ul></p>

<p> </p>'
			)
		);
	}
	
}

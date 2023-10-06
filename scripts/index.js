// GLOBAL VARIABLE / FUNCTION DECLARATIONS

// var
// functions
function getPlayerSplashes($select)
{	$.ajax(
	{	type: 'POST'
	,	url: $home_url+$interface
	,	data:
		{	z:			'entries_get_all'
	    }
	,	timeout: $waittime
	,	dataType: 'jsonp'
	,	success: function(data)
		{	if	(	typeof data.response.errors	!= 'undefined'
				)
			{	console.log(data.response.errors.length);	
				for	(	var key in data.response.errors
					)
				{	if 	(	data.response.errors.hasOwnProperty(key)
						)
					{	data.response.errors[key] = $db_errors[data.response.errors[key]];
					}
				}
				// show errors
			}
			else
			{	// process data.response.entries_list
			}
		}
	,	error: function(jqXHR,textStatus,errorThrown)
		{	console.log(errorThrown);
		}
	});	
}




	
// PROCESS ON WINDOW LOADED / DOM READY
$(function()
{	
	
	
});


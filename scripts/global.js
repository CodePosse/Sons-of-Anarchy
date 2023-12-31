// GLOBAL VARIABLE / FUNCTION DECLARATIONS

// var
var deeplink_id = '';
var deeplink_type = '';
;

// functions

var days_in_months = new Array;
days_in_months[1] = 31;
days_in_months[2] = 28;
days_in_months[3] = 31;
days_in_months[4] = 30;
days_in_months[5] = 31;
days_in_months[6] = 30;
days_in_months[7] = 31;
days_in_months[8] = 31;
days_in_months[9] = 30;
days_in_months[10] = 31;
days_in_months[11] = 30;
days_in_months[12] = 31;

function is_leap_year(year)
{	var leap = false;
	if(year%100 == 0) {
		if(year%400 == 0)
			leap = true;
	} else {
		if(year%4 == 0)
			leap = true;
	}
	return leap;
}

function correct_date($wha)
{	var chosen_month = Number($('#'+$wha+'_month').val());
	if	(	chosen_month	>	0
		)
	{	var days_in_month = days_in_months[chosen_month];
		if	(	is_leap_year(Number($('#'+$wha+'_year').val()))
			&&	chosen_month	==	2
			)
		{	days_in_month++;
		}
		if	(	Number($('#'+$wha+'_day').val())	>	days_in_month
			)
		{	$('#'+$wha+'_day').val(days_in_month);
		}
	}
	$('#'+$wha).val($('#'+$wha+'_year').val()+'-'+$('#'+$wha+'_month').val()+'-'+$('#'+$wha+'_day').val());
}

function generate_overlays(){
	// GLOBAL OVERLAY CREATOR
	$('*[rel]').not('link').overlay(	
	{	mask:
		{	color:			'#000'
//		,	effect:	'drop'
		,	zIndex:			8999
		,	closeOnClick: true
		,	closeOnEsc: true
		, 	fixed: true
		}
	,	onBeforeLoad:	function()
		{	$ovForm = $('#'+this.getTrigger().attr('data-form'));
			if	(	$ovForm.find('.captcha_container').length
				&&	typeof $ovForm.attr('data-captcha') == 'undefined'	
				)
			{	ajxCaptcha.refreshCaptcha();
			}			
		}
	,	onLoad:			function()
		{	//$olay = this.getOverlay();
			//select proper overlay
			$ovForm = $('#'+this.getTrigger().attr('data-form'));
			$ovForm.fadeIn('fast');
			if	(	typeof this.getTrigger().attr('data-next') !== 'undefined'
				)
			{	$ovForm.attr('data-next',this.getTrigger().attr('data-next'));
			}
			if	(	$ovForm.attr('id') == 'form_select_player_art'
				)
			{	selectPlayerSplash()
			}
			else
			{	$ovForm.find('*[tabindex="1"]').focus();
				//set up captcha if needed
				if	(	$ovForm.find('.captcha_container').length
					&&	typeof $ovForm.attr('data-captcha') == 'undefined'	
					)
				{	$ovForm.find('.refreshimage').on(
					{	click:	function()
						{	//console.log('#'+$ovForm.attr('id')+' .refreshimage');
							ajxCaptcha.refreshCaptcha();
						}
					});
					$ovForm.find('.captcha_text').on(
					{	keyup:	function()
						{	//console.log('#'+this.id+' .captcha_text');
							ajxCaptcha.validateCaptcha('#'+this.id);
						}
					});
					$ovForm.attr('data-captcha','initialized');
				}
			}
			//tracks overlays that appear
			$ovName = $ovForm.attr('id').replace('form_', '');
			track_event($ovName,'overlay');
		}
	,	onClose:			function()
		{	// RESET THE CAPTCHA
			$ovForm = $('#'+this.getTrigger().attr('data-form'));
			$ovForm.find('.refreshimage').click();
			// RESET THE OVERLAY
			$('#main_overlay form').hide();
			// TRIGGERS THE NEXT OVERLAY AFTER CLOSE
			if	(	typeof this.getTrigger().attr('data-next') !== 'undefined'
				)
			{	if	(	this.getTrigger().attr('data-next')	==	'form_create_tournament'
					&&	updateFormComplete					==	true
					)
				{	$('#nav_organize a').attr(
					{	'rel':			'#main_overlay'
					,	'data-form':	this.getTrigger().attr('data-next')
					,	'data-next':	''
					}).click();
				}
			}			
		}
	,	zIndex:			9000
	,	fixed:			false
	, 	api:			true
	,	closeOnClick: 	true
	});		
	
	//add close functionality to overlay 
	$('.overlay_close_btn, .overlay_cancel_btn').unbind().on('click', function() {
		// console.log($(this).parent().parent());
		$('.filter_check').prop('checked', false);
		$('.checkbox_sprite').css('background-position', '0 0');
		$('.match_error').hide();
//		$('.captcha_container').html('');
		$("a[rel]").not('link').each(function() {
			$(this).overlay().close();
		});
		// tabToCheckbox();
		// tabToSubmit();
	});	
	
}
	

// PROCESS ON WINDOW LOADED / DOCUMENT READY

$(function()
{	
	bind_things();
	if (deeplink_check()){
		deeplink_init();
	} else {
		gallery_init();
	}





$('#entry_form').validator(	{	lang: $loc_lang
	,	effect: 'wall'
	,	container: '#errorlay'
	,	errorInputEvent: null
	}
);	
$form_edit = $('#entry_form').data('validator');

$('#submit_btn').on(
{	click:	function(e)
	{	//console.log('$form_edit_contact_info.checkValidity() = |'+$form_edit_contact_info.checkValidity()+'|');
		e.preventDefault();
		//checks if default value is still present
		for (a = 0; a < $('.form_field').length; a++){
			var default_input = $('.form_field:eq('+a+')').val();
			var clean_input = default_input.replace($('.form_field:eq('+a+')').attr('data-val'), '');
			$('.form_field:eq('+a+')').val(clean_input);
		
		}
		if	(	$form_edit.checkValidity()
			)
		{	
			console.log('form good!');
			form_submit();				
		}
	}
,	touchstart: function(e)
	{	e.preventDefault();
		$(this).click();
	}	
});





var $nonos = [];
	$nonos[0] = /[^A-Za-z]+(@_$_$|0rgasm|a_$_$|abadesa|abo|abusador|abusar|acabar|accompagnatrici|aduana|adult chat|adult dvds|adult friend finder|adult movie|adult toy|adult toys|adultfriendfinder|afeminado|agarraderas|aguacates|agujero anal|agujero culo|agujero del culo|ahole|alargahuevo|alargahuevos|albondigas|alb—ndigas|alimentos|almeja|amandalist|amateur|amatoriale|amatoriali|amcik|ammucchiata|amp69|ampland|amplesso|anal|anale|analsex|andskota|animalsex|anita blond|anita dark|ano|anus|anustova|aparato|apedrear|apestoso|apuesta|apuestas|aracata|arcaton|archivioxxx|arrecho|arrombado|arschficken|arschlecker|arschloch|arse|arse hole|arsehole|asco|asesinar|asesinos|ash0le|ash0les|asholes|asian sex|askjolene|asquerosa|asqueroso|ass|ass hat|ass jabber|ass monkey|ass pirate|ass2ass|assbag|assbandit|assbanger|assbite|assclown|asscock|asscracker|asses|assface|assfuck|assfucker|assfucks|assgoblin|assh0le|assh0lez|asshat|asshead|asshole|assholes|assholz|asshopper|assjacker|asslick|assmonkey|assmunch|assmuncher|assnigger|asspirate|assrammer|assshit|assshole|asssucker|asswad|asswipe|asswipes|atacar|ataque|autoreggenti|avergallon|axe wound|axen|ayir|ayotes|azz|azzes|azzhole|azzholez|b!_+ch|b!tch|b_*tch|b[^o]tch|b00b|b00bs|b17ch|b1tch|babes|babosa|baboso,|bacalao|bacanal|bagascia|bagnarsi|baixaria|baldracca|ball bag|ballbusting|balls|bampot|band|bangbros|bangbus|bangers|barf|bassterds|bastard|bastarda|bastardo|bastards|bastardz|basterds|basterdz|basuco|basura|battere|battona|batty boy|bazookas|bdsm|beaner|beaner|beastia|beastiality|beatch|beaver|beavers|beef curtains|belino|bell end|belle fighe|berreadero|besamiculo|beschissen|besorgen|bestia|bestial|bestialidad|bestiality|bi-sexual|bi-sexuales|bi-sexuals|bi_+ch|bi7ch|biatch|bicha|bicho|biga|bigsister|biscate|bisex|bisexual|bisexuales|bisexualidad|bisexuality|bisexuals|bitch|bitchass|bitched|bitches|bitchin|bitching|bitchs|bitchtits|bitchy|bixa|bizatch|bl0wj0b|blackie|blackies|blasen|bloblos|blog|bloody|blow job|blow jobs|blowjob|blowjobs|blueballs|boba|bobalicon|bobalicona|bobo|bobos|bocchinara|bocchini|bocchino|bocke|bšckle|boff|boffing|boffs|bofilo|bog|bšg|bogan|bšhndle|boink|boinking|boinks|boiolas|bolas|bollera|bollern|bollo|bollock|bollocks|bollox|bolognaeros|boluda|boludo|bondage|boner|boners|bong|boob|boobies|boobs|boozer|boquete|bordel|bordello|boy2boy|boy4boy|boyera|boyforboy|boyo|boyonboy|boys2boys|boys4boys|boysforboys|boysonboys|boytoboy|branlette|braulho|braulio|breast|breasts|briana banks|brinca|bristols|bronha|brothel|brothels|brotherfucker|bruja|bucaiolo|buceta|budilo|bugger|buggery|bukkake|bullshit|bullshits|bullshitted|bullshitter|bullshitting|bum|bumblefuck|bumsen|bunda|bund‹o|bung|bunghole|bŸrschte|busengrapscher|busone|busty|butt|butt-pirate|buttfuck|buttfucked|buttfucker|buttfucking|buttfucks|butthead|butthole|buttlick|buttlicker)[^A-Za-z]+/i;
	
	$nonos[1] = /[^A-Za-z]+(buttlicking|buttlicks|buttmunch|buttweed|buttwipe|c0ck|c0cks|c0k|c.ck|cabao|caballo|cabasso|cabrito|cabron|cabr—n|cabrona|cabronaza|cabronazo|caca|cacca|cacete|cachapera|cagada|cagados|cagar|cagare|cagarse|cagasotto|cagate|cagna|cagon|calabazo|calienta|calientapolla|caliente|camel toe|cameltoe|cameltoes|camxcam|canalla|canallas|candice michelle|candyass|cani|can’s|capada|capado|capezzoli|carajo|caralho|caray|carpet muncher|carpetmuncha|carpetmuncher|casino|casquete|castrate|castrates|castration|cawk|cawks|cazzata|cazzi|cazzo|cazzone|cerda|cerdo|cfnm|chacon|chapero|chardo|chatte|checca|chiappe|chiavare|chiavata|chiavate|chicaconchica|chicaporchica|chichi|chick2chick|chick4chick|chickforchick|chickonchick|chicks2chicks|chicks4chicks|chicksforchicks|chicksonchicks|chickstochicks|chicktochick|chier|chik|chilito|chinaloa|chinc|chinga|chingado|chingalo|chingar|chingarles|chingate|chink|chinks|choad|choads|chocha|chochete|chocho|chochota|chode|chodelick|cholo|chora|chota|choto|chraa|christacrutchian|chuff nuts|chuj|chulo|chunga|chungo|chupa|chupaculo|chupadita|chupame un huevo|chupamiculo|chupapolla|chuperson|churra|chutarse|chute|cicciolina|cindy margolis|cipa|cipote|circuncido|circuncisi—n|cl1t|clacker|clara morgane|cliphunter|clistere|clit|clitface|clitfuck|clito|clitoride|clitoris|clits|clusterfuck|cnts|cntz|coca’na|cock|cock-head|cock-sucker|cockass|cockbite|cockburger|cockface|cockfucker|cockgobbler|cockhead|cockjockey|cockknoker|cockmaster|cockmongler|cockmongruel|cockmonkey|cockmuncher|cocknose|cocknugget|cockring|cocks|cockshit|cocksmith|cocksmoke|cocksmoker|cocksnifr|cocksuck|cocksucka|cocksucked|cocksucker|cocksucking|cocksucks|cockwaffle|coger|cogida|cogido|coglionazzo|coglione|coglioni|coito|cojonear|cojones|cojonudo|cojonudos|cokie|colgados|collant|comealmohada|comeco–o|comeco–os|comemierda|comepolla|comepollas|comestain|concha|conchudo|conchuda|condom|condoms|condon|cond—n|condones|connard|connasse|co–o|conpulantes|coochie|coochy|coon|coons|cooter|copulacion|copular|copulated|copulates|copulating|copulation|cork|cornhole|cornholes|cornudo|cornudos|cornuto|cosce|cotorrudo|cottorudo|couilles|crack-a-fat|cracker|cramouille|crap|crapfucking|crapola|crappacker|crappacking|crapped|crapper|crapping|crappy|craps|cretina|cretino|crisis|crissy moran|crotch|crotches|cu|cœ|cuernos|culattone|culero|culetto|culi|culi rotti|culo|culos|culote|cum|cumbubble|cumdumpster|cumguzzler|cumin|cumjockey|cums|cumshot|cumslut|cumstain|cumtart|cunilingus|cunnie|cunnilingus|cunt|cuntass|cuntface|cunthole|cuntrag|cunts|cuntslut|cuntsucker|cuntz|curry muncher|cus‹o|cusca|cut snake|cutre|cutreando|cutrear|cutrecillo|cvnt|d1ck|d4mn|dago|dagosex|dajmkryss|dammit|damn|damned|darkie)[^A-Za-z]+/i;
	
	$nonos[2] = /[^A-Za-z]+(darkies|darky|darlina|dating sex|daygo|de mierda|de quatro|debora caprioglio|defecaci—n|defecando|defecar|defecate|defecates|defecating|defecation|deggo|dego|demierda|dero|desbirgar|desiderya|desvirgar|dick|dick weed|dickbag|dickbeaters|dickbite|dickchode|dickface|dickfuck|dickfucker|dickhead|dickheads|dickhole|dickjuice|dickless|dicklick|dicklicka|dicklicker|dickmilk|dickmonger|dickring|dicks|dickslap|dicksuck|dicksucka|dicksucker|dicksuckers|dicksucking|dicksucks|dickwad|dickweasel|dickweed|dickwod|diddle|diddled|diddling|didle|dike|dikes|dild0|dild0s|dildo|dildo sex|dildoe|dildoes|dildos|dilld0|dilld0s|dingleberries|dingleberry|dink|dipshit|dipstick|dirsa|dita von teese|ditalini|ditalino|doggiestyle|dogshit|dojinshi|dominacion|domination|dominatricks|dominatrics|dominatrix|dong|donga|donger|doochbag|dookie|doosh|doppia penetrazione|dork|dorks|douche|douche fag|douchebag|douchebags|douchejob|douchejobs|douches|douchewaffle|doudounes|drŠgg|drague|drongo|dumass|dumbass|dumbfuck|dumbshit|dumshit|dung|dupa|durry|dush|dyke|dykes|dziwka|eiaculazione|ejackulate|ejaculate|ejaculated|ejaculates|ejaculation|ejakulate|ekrem|ekto|el ladies|elladies|encocado|encular|enculŽ|enculer|enema|enemas|enfoirŽ|enima|enimas|enkular|entrepierna|entrepiernas|erecci—n|erecion|erection|erositalia|erotic|erotica|eroticism|eroticismo|erotico|er—tico|erotismo|escort|escortforum|escroto|esibizionista|esibizioniste|esibizionisti|estupro|ewa sonnet|exclava|exibizioni|extreme sex|eyaculable|eyaculaci—n|eyaculaciones|eyacular|f u c k|f u c k e r|faccia di merda|facha|fachar|facial|faen|fag|fag1t|fagbag|faget|fagfucker|fagg1t|faggit|faggot|faggotcock|faggotlover|faggots|fagit|faglover|fags|fagtard|fagz|faig|faigs|fairies|fairy|fallo|fanculo|fanny|fanny farts|fart|farting|farts|fatass|fck|fcker|fckers|fcking|fcks|fcuk|feces|federica zarri|feg|felch|felcher|felches|felching|fellatio|fellch|feltch|feltcher|feltches|feltching|femdom|feral|fetiche|fetichista|feticismo|fetish|fetishes|fica|fiche|ficken|ficologo|figa|figa gratis|figa pelosa|figg|figgŠ|fighe|fighe pelose|fille|fingerpulla|finocchio|fio terra|fisting|fitt|fitta|fittafis|fittansikte|fittslickare|fittytuck|fittytucking|fladdermus|flamer|flŠnsost|flata|flikker|flip the bird|flipping the bird|flog|flogging|flogs|fluido|foda|follan|follando|follar|follarla|follarle|follarme|follen|footjob|foquin|foreskin|fornicate|fornicates|fornicating|fornication|forra|forro|fottere|fottersi|fottuto|fotze|foutre|franga|franger|freaking|free porn|free porno|free sex|fregna|friggin|frigging|frikking|frocio|froscio|frottage|fuck|f.ck|fuck-muppet|fucka|fuckas|fuckass|fuckbag|fuckboy|fuckbrain|fucked|fucker|fuckerino|fuckers|fuckersucker|fuckface|fuckfaces|fuckhead|fuckhole|fuckie|fuckin|f.ckin|f.cken|f.cking|fucking|fucknut|fucknutt|fucko|fuckoff|fucks|fuckstick|fucktard|fucktart|fuckup|fuckwad|fuckwit)[^A-Za-z]+/i;
	
	$nonos[3] = /[^A-Za-z]+(fuckwitt|fuder|fudge packer|fudgepacka|fudgepacker|fudgepacking|fudida|fudido|fugly|fuk|fuka|fukah|fuken|fuker|fukin|fukk|fukkah|fukken|fukker|fukkin|fukoff|fumetti erotici|fumetti porno|funkyass|fuq|fuqed|fuqing|fuqs|furry cup|futkretzn|fux0r|fvck|g00k|gang bang|gangbang|gangbanger|gangbangers|gangbanging|gangbangs|garchar|gash|gay|gayass|gaybob|gayboy|gaydar|gaydar.it|gaydo|gayfuck|gayfuckist|gaygirl|gaylord|gaylover|gays|gaytard|gaywad|gayz|genital|genitalia|genitals|gerbiling|gertrud|gessica massaro|giftbrun|gili|gilipolla|gilipollas|gincha|gincho|giochi erotici|giochi porno|giochi sexy|giornale|gipo|gipsy|girl2girl|girl4girl|girlforgirl|girlongirl|girls|girls2girls|girls4girls|girlsforgirls|girlsongirls|girlstogirls|girltogirl|gitana|glande|gnocca|gnocche|god-damned|goddammit|goddamn|goddamned|goddamnit|godemichet|gška|gola profonda|gooch|gook|gooks|gouine|granny sex|grifear|grifo|grilla|gringo|groe|grofaz|group sex|guarra|guarras|guarro|guarros|gŸebo|gŸevo|guevon|gŸevon|guevos|gŸevos|guido|guiena|gumersinda|gummer|guy2guy|guy4guy|guyforguy|guyonguy|guys2guys|guys4guys|guysforguys|guysonguys|guystoguys|guytoguy|gypo|gypsie|gypsy|h0|h00r|h0ar|h0r|h0re|h4x0r|hairpie|hairpies|hairy|hairy bush|handjob|handjobb|handjobs|handtralla|happysexo|hard|hard on|hardcore|hardon|hardons|heck|heeb|hell|hells|helvete|henger|hentai|hentay|hetero|hierba|hijo de puta|hijodeputa|hijoputa|hijos de puta|hijosdeputa|hitler|ho|hoar|hodgie|hoe|hoer|homo|homodumbshit|homos|homosexual|homosexuality|homosexuals|honkey|honkie|honkies|hooker|hookers|hoon|hoor|hoore|hooter|hooters|hore|horney|hornie|horny|horseshit|hotsexduo|huevo|huevon|huev—n|huevona|huevos|hui|hummer|hump|humping|humps|hure|hurensohn|hussy|hynda|ilona staller|imbecil|imbecille|imene|immagini porno|incappellata|incest|incesti|incesto|inculata|inculate|indigente|indio|ingoio|injun|inmigrante|interracial|interracial dating|invertido|italiandreams|jack off|jack-off|jackass|jacking|jackoff|jackshit|jap|japs|jenna jameson|jerk|jerk off|jerk-off|jerking|jerkoff|jessica rizzo|jewboy|jewed|jidder|jigaboo|jilipolla|jilipollas|jilipoya|jilipoyas|jincha|jincho|jisim|jism|jiss|jizm|jizz|jizzum|joden|joder|joderos|jodete|jodido|john holmes|jordan capri|josie maran|joto|jouir|jucka|jugs|jungle bunny|junglebunny|kabao|kabasso|kacken|kagar|kaka|kamasutra|kanker|kara monaco|karolcia|katie price|kawk|keeley hazell|kenga|kike|kikes|killer|kinky|kitzler|kkk|klitta|klootzak|knackered|knallen|knob|knob head|knobjockey|knobs|knobz|knockers|knšllare|knulla|knulle|konkelbŠr|kooch|koolie|koolielicker|koolies|kootch|kraut|kronjon|kuk|kukhuvud|kuksŒs|kuksuger|kunt|kunts|kuntz|kurac|kurva|kurve|kurwa|kush|kusi|kyke|kyla cole)[^A-Za-z]+/i;
	
	$nonos[4] = /[^A-Za-z]+(kyrpa|l3i_+ch|l3itch|labia|ladyvenere|lamadrequetepari—|lameass|lameculo|lameculos|langer|laostia|lap dance|latex|latin|latina hardcore|latino|latinos|laura angel|lebo|leccaculo|lerda|lerdo|lesbian|lesbian sex|lesbiana|lesbians|lesbica|lesbiche|lesbo|lesbos|letizia bruni|lez|lezzer|lezzian|lezzie|libido|lick|lina|lingerie|lipshits|lipshitz|llello|lolloscan|loshuevos|lovehole|luana|luana borgia|lubamba|lucifer|lucy pinder|lumaca|machunga|madama|madre|madrina|madrota|majja|maldecido|maleton|malet—n|malnacida|malnacido|malparida|malparido|maltratada|maltratado|maltratador|malva|mamada|mamadas|mamadita|mamhoon|mammaries|mamon|mam—n|mamona|mamonazo|mamones|man2man|man4man|manchas|manforman|manonman|mantoman|maquereau|marica|marico|maricon|maric—n|maricona|mariguana|marimacha|mariquinha|mariscala|masochism|masochist|masokist|masoquismo|massterbait|masstrbait|masstrbate|masterbaiter|masterbat|masterbat3|masterbate|masterbates|masterbating|masterbation|mastuerzo|masturba‹o|masturbate|masturbates|masturbating|masturbation|masturbator|masturbazione|matar|matthardcore|maurizia paradiso|maxim|mayate|mcfagget|mear|meatrack|mellons|mel—n|melona|mema|memo|men2men|men4men|mendruga|mendrugo|menformen|menonmen|mentomen|mequetrece|mequetreces|merda|merdata|merde|merdeux|merdoso|meter|mezza sega|mibun|mich|mick|mierda|mignotta|milena velba|milf|milo manara|mimi miyagi|minchia|minchione|minge|minger|misex|mistress|mlm|moana|moana pozzi|mofo|mongrel|monitor|monkeyass|monkleigh|monta|montare|moravia|morisqueta|moro|morrongo|morue|mota|motha fucker|motha fuker|motha fukkah|motha fukker|mothafucka|mothafuckin|motharfucka|motharfucker|motharfuka|motharfukka|mother fucker|mother fukah|mother fuker|mother fukkah|mother fukker|mother-fucker|motherfuck|motherfucka|motherfuckas|motherfucked|motherfucker|motherfuckers|motherfucking|motherfuka|motherukka|motivosa|motocicleta|motorizar|mouliewop|mudlover|mudpeople|mudpeoples|muerte|muertos|muff|muffdive|muffdiver|muffdiving|mugir|muie|mulkku|muncher|munging|murderer|murrda|mus|muschi|music|mussa|mut|mutandine|mutha fucker|mutha fukah|mutha fuker|mutha fukkah|mutha fukker|mutherfuka|mutta|n1gga|n1gger|n1gr|naked|nalga|nastt|natasha kiss|naturismo|naturist|nazi|nazis|necrophile|necrophiles|necrophilia|negro|negros|nepesaurio|nerchia|next door nikki|nicole lenz|nicole narain|nigaboo|nigga|niggah|niggahlover|niggalover|niggas|niggaz|nigger|niggerlover|niggers|niggerz|niglet|nigur|niiger|niigr|ninfeta|ninfo|nipple|nipples|nonce|nookie|norks|noune|nua|nuda|nude|nudes|nudez|nudi|nudismo|nudist|nudista|nudiste|nudisti|nudity|nudo|numpty|nut sack|nuts|nutsack|nutte|nymph|nympho|nymphomania|nymphomaniac|nymphomaniacs|nymphos|ochsen|ocker|oignon|orafis|oral|orgasim|orgasm|orgasmic|orgasmo|orgasmos|orgasms|orgasum|orge|orgi|orgia|orgiastic|orgie|orgies|orgy|oriface|orifice|orifiss|orospu|ostia|ostias|p0rn|p3n15|packi|packie|packy|pajero|paki|paki|pakistanien|pakie|paky|palle moscie|panooch|pansie|pansy|panties|panty)[^A-Za-z]+/i;
	
	$nonos[5] = /[^A-Za-z]+(pantyhose|paris hilton|paska|passera|passiva|patacca|patonza|pau|pavisosa|pavisoso|pavitonta|pavitonto|pecker|peckerhead|peckers|peckerwood|pecorina|pŽdale|pede|pŽdŽ|pederasta|pedo|pedofilia|pedophile|pedophiles|pedophilia|pedorro|peeenus|peeenusss|peenus|peepee|peepshow|peepshows|peinus|pelada|pelotuda|pelotudo|pen15|pen1s|penas|pendeja|pendejo|pene|penecino|penenillo|penes|penetra‹o|penetrare|penetrazione|penii|penis|pnis|penis-breath|penisbanger|penises|penisfucker|penispuf r|penthouse|penus|penuus|pepe|pepino|peppesex|perfect ten|perizoma|perra|perraca|perse|perversion|pervert|perverted|perverts|pflaume|phile|philes|philia|phonerotica|phuc|phuck|phucker|phuckers|phucking|phucks|phuk|phuker|phukers|phuking|phukker|phuks|phuq|phuqer|phuqers|phuqing|phuqs|pica|piccole trasgressioni|piccoletrasgressioni|pichunter|picka|pierdol|piglia in culo|pija|pijo|pijudo|pikey|pikie|piky|pilila|pillock|pillow biter|pillu|pimmel|pimp|pimpis|pimps|pin|pincharse|pinche|pinga|pinto|pippa|piranha|pirate|piriguete|pirla|piroca|pisello|piss|pissed|pissed off|pisser|pisses|pissflaps|pissing|pissy|pix|pizda|pkhs|playboy|playmate|plug|polac|polack|polak|polesmoker|polla|pollard—n|pollock|polvo|pompa|pompinara|pompinare|pompini|pompini gratis|pompino|poof|poofta|poofter|poofy|poohpusher|poon|poonani|poonanny|poonany|poontang|poontsee|poop|poopchute|poopshoot|poppe|porca|porch monkey|porche|porchmonkey|porco|porcone|pork sword|porking|porks|porn|porno|porn™|pornografia|pornogratis|pornoitalia|pornomotore|pornostar|pornostars|pornstar|pornstargals|poronga|porra|porro|porros|portugal|pot|pothead|pouffiasse|pousse-crotte|pr0n|pr1c|pr1ck|pr1k|pregnant|prendi cazzi|preservativo|presevativos|preteen|prick|prick pube|prive|priv|proibid‹o|prostitui‹o|prostituta|prostitute|pu_$_$y|pube|pubes|pubic|pud|pudd|puderdose|pudern|puds|pugnetta|puke|pula|pule|punanni|punanny|pu–etazo|pu–etazos|punheta|punta|puntang|puntotrans|pusse|pussee|pussi|pussies|pussy|pussylicka|pussylicker|pussylicking|pussysuck|pussysucka|pussysucker|pussysucking|puta|putain|putamadre|putang|putaria|putaso|putazo|pute|puteiro|puterio|puter’o|putero|puto|putogay|puton|put—n|puttana|puttane|puuke|puuker|qahbeh|quaglia|quebracho|quebrachon|queef|queer|queerbait|queerhole|queers|queerz|quenga|querfotze|quesejodan|quetejodan|quilombo|quim|qweers|qweerz|qweir|rabbit vibrator|rabo|racconti erotici|racconti erotici gay|racconti gay|racconti hard|racconti incesto|racconti incestuosi|racconti milu|racconti porno|racial|racism|racismo|racist|racista|racists|ragazze in vendita|ragazze nude|raghead|rahowa|raja|rajas|rŠpa|rape|raping|rapist|raspone|rautenberg|recktum|rectal|rectum|redskin|reggicalze|renob|resbalon|retard|retozona|ricchione|rimjob|rimjobs|rivista|rocco siffredi|roger|rom|root-rat|rootrat|rooty|rossella brescia nuda|ršv|ršvhŒl|rubbers|rug muncher|rul|rulacho|ruletera|rump|rumpranger|rumprider|rumps|rumpshaker|rumpshakers|rumpshakerz|runka|ruski|s.o.b.|sacanagem|sadism|sadist|sado|sadomaso|sadomasochism|sadomasoquismo|salido|salope|samantha fox|sand nigger|sandnigger|sapphic|sappho|sapphos|sarasa|satan|savanna|savanna samson|sbocchinare|sborra|sborrata|sborrate|sborrone|sbrodolata|scallywag|scambio coppie|scank|scatological|schaffer|schŠllŠ|scheiss|scheisse)[^A-Za-z]+/i;
	
	$nonos[6] = /[^A-Za-z]+(schei§en|schlampe|schlong|schlonging|schlongs|schmegma|schmuck|schnŠgg|schtup|schtupping|schtups|schwuchtel|schwul|schyss|schyssdrŠgg|schysse|scopare|scopata|scopate|scrag|screw|screwed|screwing|screws|scrote|scrotum|secchio|seckel|sega|seghe|seich|selen|selene|semen|semŽn|seni|seno|senza palle|sepo|serin|server|sesso|sessogratis|sex|sexe|sexed|sexi|sexking|sexkitten|sexmachine|sexo|sexocean|sexqueen|sextoon|sexual|sexuality|sexy|sexybitch|sexybitches|sexycoppie|sh!_+|sh!t|sh1t|sh[^ou]t|sh1ter|sh1ts|sh1tter|sh1tz|shagger|sharmuta|sharmute|sheepshagger|shemale|shemale fucking|shemale hardcore|shemale movie|shemales|shi_+|shipal|shirt lifter|shit|shitass|shitbag|shitbagger|shitbrains|shitbreath|shitcan|shitcanned|shitcunt|shitdick|shited|shitface|shitfaced|shitfit|shithead|shitheads|shithole|shithouse|shiting|shitlist|shitpacka|shitpacker|shitpackers|shitpacking|shits|shitspitter|shitstain|shitt|shitted|shitter|shittiest|shitting|shitts|shitty|shity|shitz|shiz|shiznit|shonky|showgirl|shtup|shyt|shyte|shytty|shyty|siffredi|silvia saint|siti porno|sixty-nine|sixtynine|sixtynining|skanck|skank|skankee|skankey|skanks|skanky|skeet|skitpackare|skrew|skrewing|skrews|skribz|skullfuck|skurwysyn|slag|slanteyes|slanthead|slatcha|slattern|slave|slaves|sleazydream|slopehead|slopeheads|slot|slut|slutbag|sluts|slutty|slutz|slyna|smeg|smoko|smut|snatch|snatches|snopp|snoppfŠlla|sob|soccia|socmel|soddom|sodom|sodomist|sodomists|sodomita|sodomizar|sodomize|sodomized|sodomizing|sodomy|somaro|son-of-a-bitch|sonia eyes|sonnofabitch|sonnovabitch|sonnuvabitch|sonofa|sonofabitch|soplanucas|soplapolla|soplapollas|soplapoya|soplapoyas|sorca|sorete|spagnola|spank|spanked|spanking|spanks|spŠnnbšg|spastic|spearchucker|spearchuckers|sperm|sperma|spermicidal|spermjuice|spermshack|sphencter|sphincter|spic|spick|spicks|spics|spierdalaj|spik|spiks|splooge|spompinare|spoof|spooff|spooge|spook|sprutluder|spunk|squirt|squirting|sticchio|stiffie|stiffy|stjŠrthŒl|stocking|stocking tease|stoned|stoner|storie erotiche|stštarršv|stricher|strip poker|stronza|stronzata|stronzo|strumpa|stud|studs|stupid|stupido|stupro|subba|submissive|submissives|subnormal|subnormales|succhia|succhiacazzi|suck|sucka|suckas|suckass|suckaz|sucked|sucker|suckers|sucking|sucks|sudaca|sudacas|sudamericano|suicide girls|suka|supertangas|surfjas|suruba|swing|swinger|swingers|swp|sylvia saint|tackern|taconera|taint|tajarse|tana|tanates|tante|tapette|tarada|tarado|tarbaby|tard)[^A-Za-z]+/i;
	
	$nonos[7] = /[^A-Za-z]+(taruga|tarugo|teen|teen sex|teen2teen|teen4teen|teenforteen|teenonteen|teens|teens2teens|teens4teens|teensforteens|teensonteens|teenstoteens|teentoteen|teets|teez|tera patrick|testa di cazzo|testa di minchia|testes|testical|testicals|testicle|testicles|testicoli|test’culo|testiculos|teta|tetas|tette|tette dure|tette enormi|tette grosse|tettedure|tettine|tettona|tettone|tgp|thehun|thong|thumbzilla|thundercunt|tiava|tinkle|tinto brass|tiran|tirana|tirania|tirano|tirao|tirar|tirarme|tirarse|tirartela|tit|tit-wank|titfuck|tits|titt|titten|titties|titts|titty|tittyfuck|titwank|toba|tokjucka|tonta|tonto|topa|topless|torpe|torp—n|torrjucka|tortillera|toss pot|tosser|trabuco|tramp|tramps|trannie|tranny|trans|trans escort|trans grande fratello|transessuali|transex|transexual|transsexual|transsexuals|transvestite|transvestites|trasero|traseros|travestiti|travesty|trepar|tringle|tringler|trique|troia|troie|trolo|trola|trombare|trombare la gnocca|trombata|tulle|tuputamadre|turd|turlute|tuttouomini|tw@t|twat|twatlips|twats|twatwaffle|twink|uao|unclefucker|underwear|uomini nudi|uomo nudo|upskirt|urinate|urinated|urinates|urinating|urination|urinieren|va j j|va1jina|vaca|vacca|vadia|vaffanculo|vag|vag1na|vagiina|vagina|vaginal|vaginas|vagine|vaina|vaj1na|vajayjay|vajina|valentine demy|vangare|vecchie troie|venere bianca|verga|vergallito|vergallo|vergas|veronica zemanova|veuve|vibrador|vibrator|vibratore|vibratori|videoporno|violencia|violncia|violenta|violento|virgin|virginity|virgins|vittu|vixena|vjayjay|všgeln|vomitar|voyeur|voyeurism|voyeurs|voyeurweb|vulgar|vullva|vulva|w00se|w0p|wang|wank|wanker|wankjob|wcotc|wetback|wetbacks|wetfart|wh00r|wh0re|whacka|whacker|whip|whipped|whipping|whips|whitepower|whoar|whore|whorebag|whored|whoreface|whorehouse|whores|whoring|wichsen|wichser|wixen|wog|woggy|woman2woman|woman4woman|womanforwoman|womanonwoman|womantowoman|women2women|women4women|womenforwomen|womenonwomen|womentowomen|wop|worldsex|wpww|wuss|x-rated|xinga|xingale|xingar|xixi|xnxx|xoxete|xoxo|xoxota|xrated|xstream|xupapollas|xute|xx|xxx|yaoi|yed|yerba|yid|yobbo|zabourah|zemanova|zipperhead|zipperheads|zoccola|zoccole|zoofilia|zoofilico|zoosex|zopenca|zopenco|zorete|zorra|zorrero|zorrita|zorr—n|zurramato|zurrar)[^A-Za-z]+/i;
	
	// BEGIN form validation GLOBAL INIT
	$TLDsRe = /_.(AC|AD|AE|AERO|AF|AG|AI|AL|AM|AN|AO|AQ|AR|ARPA|AS|ASIA|AT|AU|AW|AX|AZ|BA|BB|BD|BE|BF|BG|BH|BI|BIZ|BJ|BM|BN|BO|BR|BS|BT|BV|BW|BY|BZ|CA|CAT|CC|CD|CF|CG|CH|CI|CK|CL|CM|CN|CO|COM|COOP|CR|CU|CV|CX|CY|CZ|DE|DJ|DK|DM|DO|DZ|EC|EDU|EE|EG|ER|ES|ET|EU|FI|FJ|FK|FM|FO|FR|GA|GB|GD|GE|GF|GG|GH|GI|GL|GM|GN|GOV|GP|GQ|GR|GS|GT|GU|GW|GY|HK|HM|HN|HR|HT|HU|ID|IE|IL|IM|IN|INFO|INT|IO|IQ|IR|IS|IT|JE|JM|JO|JOBS|JP|KE|KG|KH|KI|KM|KN|KP|KR|KW|KY|KZ|LA|LB|LC|LI|LK|LR|LS|LT|LU|LV|LY|MA|MC|MD|ME|MG|MH|MIL|MK|ML|MM|MN|MO|MOBI|MP|MQ|MR|MS|MT|MU|MUSEUM|MV|MW|MX|MY|MZ|NA|NAME|NC|NE|NET|NF|NG|NI|NL|NO|NP|NR|NU|NZ|OM|ORG|PA|PE|PF|PG|PH|PK|PL|PM|PN|PR|PRO|PS|PT|PW|PY|QA|RE|RO|RS|RU|RW|SA|SB|SC|SD|SE|SG|SH|SI|SJ|SK|SL|SM|SN|SO|SR|ST|SU|SV|SY|SZ|TC|TD|TEL|TF|TG|TH|TJ|TK|TL|TM|TN|TO|TP|TR|TRAVEL|TT|TV|TW|TZ|UA|UG|UK|US|UY|UZ|VA|VC|VE|VG|VI|VN|VU|WF|WS|XN_-_-0ZWM56D|XN_-_-11B5BS3A9AJ6G|XN_-_-3E0B707E|XN_-_-45BRJ9C|XN_-_-80AKHBYKNJ4F|XN_-_-9T4B11YI5A|XN_-_-CLCHC0EA0B2G2A9GCD|XN_-_-DEBA0AD|XN_-_-FIQS8S|XN_-_-FIQZ9S|XN_-_-FPCRJ9C3D|XN_-_-FZC2C9E2C|XN_-_-G6W251D|XN_-_-GECRJ9C|XN_-_-H2BRJ9C|XN_-_-HGBK6AJ7F53BBA|XN_-_-HLCJ6AYA9ESC7A|XN_-_-J6W193G|XN_-_-JXALPDLP|XN_-_-KGBECHTV|XN_-_-KPRW13D|XN_-_-KPRY57D|XN_-_-MGBAAM7A8H|XN_-_-MGBAYH7GPA|XN_-_-MGBBH1A71E|XN_-_-MGBERP4A5D4AR|XN_-_-O3CW4H|XN_-_-OGBPF8FL|XN_-_-P1AI|XN_-_-PGBS0DH|XN_-_-S9BRJ9C|XN_-_-WGBH1C|XN_-_-WGBL6A|XN_-_-XKC2AL3HYE2A|XN_-_-XKC2DL3A5EE0H|XN_-_-YFRO4I67O|XN_-_-YGBI2AMMX|XN_-_-ZCKZAH|YE|YT|ZA|ZM|ZW)$/i;
		
	$.tools.validator.fn(':email', function(input) {
		var valid	=	false
		,	val		=	input.val()
		;
		if	(	val		//	if value is present
			)
		{	var emailRe = /^([_da-z__._-_+]+)@([_da-z_._-]+)_.([_da-z_-_.]{2,})$/i;
			if	(	emailRe.test(val)	//	if value matches regex
				//	1. An email address can contain only one at-symbol [@]
				//	5. An email address cannot contain control characters or DEL
				//	6. Characters ()<>,;:_/"''[] or spaces are not permitted in an email address
				)
			{	emailRe = /(@|_.)_./i;
				if	(	!emailRe.test(val)	//	if value does not match regex
					//	2. An email address cannot have a dot immediately after the at-symbol [@.]
					//	3. An email address cannot have two dots in a row [..]
					)
				{	emailRe = /^(@|_.)/i;
					if	(	!emailRe.test(val)	//	if value does not match regex
						//	4. An email address cannot begin with the dot [.] or at-symbol [@]
						)
					{	emailRe = /^(abuse|administrator|hostmaster|info|jobs|postmaster|root|support|webmaster)@/i;
						if	(	!emailRe.test(val)	//	if value does not match regex
							//	8. An email address cannot have names likes root, abuse, info, webmaster, etc.
							)
						{	emailRe = /_.(AERO|ASIA|BIZ|CAT|COM|COOP|EDU|GOV|INFO|INT|JOBS|MIL|MOBI|MUSEUM|NAME|NET|ORG|PRO|TEL|TRAVEL)$/i;
							if	(	emailRe.test(val)	//	if value matches regex
								//	7. An email address must have a valid top level domain (i.e. com, org, net, edu, mil)
								//	(Test most common TLDs first to avoid executing the following heavy test unless absolutely necessary)
								)
							{	valid = true;
							}
							else
							{	//	//	if value matches regex
								//	The system should be accepting all of the top level domains listed on this site:
								//	http://data.iana.org/TLD/tlds-alpha-by-domain.txt
								valid = $TLDsRe.test(val);
							}
						}
					}
				}
			}
		}
		return valid; 
	});
	
	$.tools.validator.addEffect('wall'
	,	function(errors, event)
		{	$('body,html,document').scrollTop(0);
			var $errorlay = $(this.getConf().container);
			$errorlay.find('p').remove();
			$.each(errors, function(index, error) {
				if	(	error.input.attr('type') != 'hidden'
//					&&	error.input.is(':visible')
					)
				{	$xclass = '';
					iPos = error.input.offset();
					switch(error.input.attr('id'))
					{
/*					
						case 'terms':
							iPos = $('#opt_in_img').offset();
							iPos.top += 4;
							iPos.left += 40;
							$errorframe = '<p id="error_'+error.input.attr('id')+'" class="error_frame optoer"><span class="error">' +error.messages[0]+ '</span></p> ';
							break;	
						case 'dob':
							iPos = $('#dob_month').offset();
							$errorframe = '<p id="error_'+error.input.attr('id')+'" class="error_frame long"><span class="error">' +error.messages[0]+ '</span></p> ';
							break;
*/
						default:
							$errorframe = '<p id="error_'+error.input.attr('id')+'" data-field="'+error.input.attr('id')+'" class="error_frame'+$xclass+'"><span class="error">' +error.messages[0]+ '</span></p> ';
					}
					tPos = iPos.top;
					lPos = iPos.left;
					$errorlay.append($errorframe);
					$('#error_'+error.input.attr('id')).css(
						{	'top'		:	tPos + 'px'
						,	'left'		:	lPos + 'px'
						}
					);
				}
			});
			
			$errorlay.fadeIn('fast',function()
			{	$('.captcha').attr('src', $home_url+'images/captcha.php?cx=' + Math.random());
			    $('.captcha_text').val('');
				$('.incorrect').html('');				
			});
		}
	,	function(inputs)
		{	
		}
	);

	$.tools.validator.fn('[data-placeholder]', function(input) {
		var	pl = input.attr('data-placeholder');
		return input.val() == pl ? false : true;
	});
		
	$.tools.validator.fn('[data-equals]', function(input) {
		var	name = input.attr('data-equals')
		,	field = this.getInputs().filter('[name=' + name + ']'); 
		return input.val() == field.val() ? true : [name.replace(/_/,' ')]; 
	});
	
	$.tools.validator.fn('[data-digits]', $js_errors.data_digits, function(input) {
		var	minlength = input.attr('data-digits');
		var error_spec = minlength;
		var maxlength = input.attr('maxlength');
		var regex_spec = minlength;
		if	(	maxlength
			)
		{	if	(	maxlength	>	minlength
				)
			{	error_spec = (minlength == 1)?' '+$js_errors.up_to+' '+maxlength:$js_errors.between+' '+minlength+' '+$js_errors.and+' '+maxlength;
				regex_spec = minlength+','+maxlength;
			}
		}
		var digitsRe = new RegExp('^[0-9]{'+regex_spec+'}$');
		return digitsRe.test(input.val()) ? true : [error_spec];
	});	

	$.tools.validator.fn('[data-code]', $js_errors.data_code, function(input) {
		var	code_type = input.attr('data-code');
		switch(code_type)
		{	case 'phone':
				codeRe = /^_(?(_d{3})_)?[-. ]?(_d{3})[-. ]?(_d{4})$/;
				error_spec = $js_errors.valid_phone;
				break;
			default: // case 'zip':
				codeRe = /^_d{5}([_-]_d{4})?$/;	
				error_spec = $js_errors.valid_zip;
		}
		return codeRe.test(input.val()) ? true : [error_spec];
	});	
	
	$.tools.validator.fn('[data-password]', $js_errors.data_code, function(input) {
		var valid	=	[$js_errors.valid_pass]
		,	val		=	input.val()
		;
		if	(	val		//	if value is present
			)
		{	var passRe = /.{6,}/i;
			if	(	passRe.test(val)	//	if value matches regex
				//	Passwords must be at least 6 characters long
				)
			{	passRe = /[0-9]+/i;
				if	(	passRe.test(val)	//	if value matches regex
					//	Passwords must contain at least 1 number
					)
				{	passRe = /[a-z]+/i;
					valid = passRe.test(val)	//	if value matches regex
					//	Passwords must contain at least 1 letter
				}
			}
		}
		return valid; 
	});
	
	$.tools.validator.fn('[data-match]', function(input) {
		var valid	=	false
		,	val		=	input.val()
		,	match	=	new RegExp(input.attr('data-match')) // include case-insensitive regex in main body, no modifiers in this function
		;
		if	(	val		//	if value is present
			)
		{	valid = match.test(val);	//	if value matches regex
		}
		return valid; 
	});
	
	$.tools.validator.fn('[data-nonocheck]', function(input) {
		var valid	=	[$js_errors.bad_words]
		,	$val	=	input.val()
		;
		if	(	$val		//	if value is present
			)
		{	for (	var	$no	in	$nonos
				)
			{	if	(	$nonos[$no].test(' '+$val+' ')
					)
			 	{	input.val('');
			 		return valid;
				}
			}	//	if value matches regex
		}
		return true; 
	});
	
	$(':date').dateinput(
	{	lang: $loc_lang
	,	format: 'yyyy-mm-dd' // "mmmm d, yyyy"
	,	selectors: true
	,	max: '2011-12-31'
	,	yearRange: [-111,1]
	});
	
	$('.dater').live(
	{	change:	function()
		{	correct_date($(this).attr('data-date'));
		}
	});

	$('#errorlay').on(
	{	click:	function(e)
		{	e.preventDefault();
			$(this).fadeOut('fast',function()
			{	$('#'+$(this).find('.error_frame:first').attr('data-field')).focus();				
			});
		}
	,	touchstart: function(e)
		{	e.preventDefault();
			$(this).click();
		}	
	});
	
	// END form validation GLOBAL INIT

	
	///GENERRATE OVERLAYS
//	generate_overlays();
		

});

//SECTION INITIALIZATION FUNCTIONS
function bind_things(){
	$('#goto_gallery').off().on({
		mouseup: function(){
			gallery_init()
		}
	});
	/*$('#goto_form_ov').off().on({
		mouseup: function(){
			form_ov_init()
		}
	});*/
	$('.share_site').off().on({
		mouseup: function(){
			share_site($(this).attr('id'));
		}
	});
}

function gallery_init(){
	$('#gallery_wrapper').animate({'margin-top':'0px'}, 300);
		}


function deeplink_init(){
	deeplink_call();
}

function form_ov_init(){
	console.log('intro_ov');
	//$('#gallery_wrapper').animate({'margin-top':'395px'}, 300);	
	//$('#gallery').hide();
	$('#intro').show().animate({'margin-top':'100px','opacity':'1'}, 300);
	if($('#intro').hasClass('deeplink')) {
		if($('#form_ov_5').css('display') == 'block') {
			$('#form_ov_5').fadeOut();
		}
	}
	$('#icon-x').off().on({
		click: function(){
			$('#gallery_wrapper').animate({'margin-top':'0px'}, 300);
			$('#intro').animate({'margin-top':'0px','opacity':'0'}, 300, function(){$('#intro').hide()});
		}
	});
	$('#goto_form').off().on({
		mouseup: function(){
			form_init();
			
		}
	});
}

function form_init(){
		$('#intro').animate({'margin-top':'0px','opacity':'0'}, 300, function(){$('#intro').hide()});
		$('#gallery_wrapper').animate({'margin-top':'0px'}, 300,  function(){
			$('#gallery_wrapper').hide();
			$('#gallery').hide();
		
	
	console.log('form_ov');
	$('#form_wrapper').animate({'margin-top':'30px','opacity':'1','display':'block','height':'toggle'}, 300);
	$('input, textarea').off().on({
		mouseup: function(){
			if ($(this).val() == $(this).attr('data-val')) {
				$(this).val('');
			}
		},
		blur: function(){
			if ($(this).val() == '') {
				$(this).val($(this).attr('data-val'));
			};
		}
		});
	});
}

function form_closer() {
	$('#gallery_wrapper').css('margin-top', '0px');
	$('#gallery').show();
	$('#gallery_wrapper').fadeIn();
	$('#intro').css('margin-top', '100px');
	$('#intro').css('opacity', '1');
	$('#form_wrapper').hide();
}
$(".close_grandma").click(function() {
	$(this).parent().parent().animate({
		'opacity':'0','display':'none','height':'toggle',
	}, 500, function() {
		//$("#form_wrapper").css("display", "none");
		if($('#intro').hasClass('deeplink')) {
			//$('#form_wrapper, #form_ov_5').css('display', 'block');
			$('#form_ov_5').fadeIn();
		} else {
			$("#gallery_wrapper").css("display", "block");
		}
	});
});
function showView() {
	 $("#form_ov_1").css("display","none").css("opacity","0");
	 $('#intro').animate({'margin-top':'0px','opacity':'0','display':'none','height':'toggle'}, 300);
	 $("#form_wrapper , #form_ov_5").animate({'opacity':'1','display':'block','height':'toggle',}, 500, function() {
    $("#form_wrapper, #form_ov_5").css("display","block").css("opacity","1");
    
  });
}
function confirm_init(title, user_name, thumbnail, story, yt_code) {

	if ($('#form_ov_1').is(":visible") || $('#gallery_wrapper').is(":visible") ){

		$('#form_ov_1').animate({'margin-top':'0px','opacity':'0'}, 300, function(){
			$('#form_ov_1').hide();
			$('#form_ov_2 .title').html(title);
			$('#form_ov_2 .user_name').html(user_name);
			$('#form_ov_2 .story').html(story);
			/*
			//use this for deeplinked overlay
			if(yt_code){
				//$('#column3').html('<iframe id="story_video_large" width="640" height="390" src="//www.youtube.com/embed/'+yt_code+'" frameborder="0" allowfullscreen></iframe>');
				$('#column3').html('<div id="story_photo_small"><img src="'+thumbnail+'" alt="'+title+'" /></div>');
			} else {
				//$('#column3').html('<div id="story_photo_large"><img src="'+thumbnail+'" alt="'+title+'" /></div>');
			}
			*/
			$('#confirm_image').html('<img src="'+thumbnail+'" alt="'+title+'"  width="380" />');
		
		});
	}
		$('#form_ov_2').show().animate({'margin-top':'0px','opacity':'1'}, 300);
		
		$('#goto_success').off().on({
			mouseup: function(){
				form_confirm(title, user_name, thumbnail, story, yt_code);
			}
		});
		console.log('confirm_init');

}

function form_submit(){
	//first,we check to see if there is a value for the youtube video or photo
	var has_photo = false;
	var has_youtube = false
	var photo_url = '';
	var title = $('#form_title').val();
	var story = $('#form_story').val();
	var user_name = $('#form_name').val();
	var youtube_link = $('#form_youtube_link').val();
	if($('#uplaoded image').attr('src')){
		has_photo = true;
		console.log('has photo');
	}
	if($('#form_youtube_link').val()){
		has_youtube = true;
		console.log('has youtube');
	}
	if(has_youtube){
		//looks if the content is valid
		var jsonvars = {}
		jsonvars.z  = 'video_verify';
		jsonvars.user_name = user_name
		jsonvars.video_link =  youtube_link;
		//console.log(jsonvars);
		$.ajax({
				type: 'POST',
		  		url: 'interface.php',
		  		data:jsonvars,
		  		dataType: 'json',
				success: function(data) {	
					console.log('video checked out');
					console.log(data.response);
					var thumbnail = data.response.video.thumbnail_url;
					var yt_code = data.response.video.video_code;
					confirm_init(title, user_name, thumbnail, story, yt_code);
					},
				error: function() {
					console.log('Unable to confrim youtube.');
				}
		});	

		
		
	} else if(has_photo){
		$.ajax({
		    url: photo_url,
		    type:'HEAD',
		    success: function()
		    {
		        //file exists
		        console.log('video checked out');
		        var thumbnail = photo_url;
				confirm_init(title, user_name, thumbnail, story, yt_code);
		    },
		    error: function()
		    {
		        //file does notexists
		        console.log('Unable to confrim image.');
		    }
		});
	}
	
}

function form_confirm(title, user_name, thumbnail, story, yt_code){
	console.log('inseting code to db - '+yt_code)
	if (typeof yt_code !== 'undefined'){
		var jsonvars = {}
		jsonvars.z  = 'video_insert';
		jsonvars.user_name = user_name;
		jsonvars.video_title = title;
		jsonvars.video_code = yt_code;
		jsonvars.thumbnail_url = thumbnail;
		jsonvars.description = story;
		//console.log(jsonvars);
		$.ajax({
				type: 'POST',
		  		url: 'interface.php',
		  		data:jsonvars,
		  		dataType: 'json',
				success: function(data) {	
					console.log('video submitted');
					success_init();
					},
				error: function() {
					console.log('Unable to submit youtube video to db.');
				}
		});	
	}
	$('#form_ov_4').show().animate({'margin-top':'0px','opacity':'1','display':'block'}, 300);
}

function success_init(){
	if ($('#form_ov_2').is(":visible") || $('#gallery_wrapper').is(":visible") ){
		$('#form_ov_2').animate({'margin-top':'0px','opacity':'0'}, 300, function(){
			$('#form_ov_2').hide();
			$('#form_ov_3 .user_name').html(user_name);
		});
	}
	console.log('show success overlay');
}


//MISC FUNCTIONS
function deeplink_check() {
	var number_ex = /^\d+$/;
	var page_id = window.location.href.split('=').pop();
	console.log(page_id);
	result = number_ex.test(page_id)
	deeplink_id = page_id;
	if (window.location.href.indexOf("video") >= 0) {
		deeplink_type = 'video';
	} else {
		deeplink_type = 'photo';
	}
	console.log(result)
	return result;
}
function deeplink_call() {
		console.log('deeplink_call');
		var jsonvars = {}
		jsonvars.z  = 'story_get';
		jsonvars.id = deeplink_id;
		jsonvars.type =  deeplink_type;
		//console.log(jsonvars);
		$('#intro, #gallery_wrapper, #form_ov_1')
			.css('display', 'none')
			.css('opacity', '0');
		$('#intro')
			.css('margin-top', '0px');
		$.ajax({
				type: 'POST',
		  		url: 'interface.php',
		  		data:jsonvars,
		  		dataType: 'json',
				success: function(data) {	
					console.log(data.response);
					// form_ov_5 window to show
					var viewId = (data.response.id);
					var viewTitle = (data.response.video_title);
					var viewDesc = (data.response.description);
					var viewUser = (data.response.u);
					//var viewImage = (data.response.image);
					var vidThumb = (data.response.video_thumbnail);
					var vidCode = (data.response.video_code);
					$('#form_ov_5 .title').html(viewTitle);
					$('#form_ov_5 .description').html(viewDesc);
					$('#form_ov_5 .user_name').html("- " + viewUser);
					//$('#form_ov_5 .view_image').html(viewImage);
					$('#form_ov_5 .vid_thumb').html(vidThumb);
					$('#form_ov_5 .vid_code').html('<iframe id="story_video_large" width="640" height="390" src="//www.youtube.com/embed/'+vidCode+'" frameborder="0" allowfullscreen></iframe>');
					//$('#intro, #gallery_wrapper').hide();
					//showView();
						
					//$('#form_wrapper, #form_ov_5').fadeIn();
					
					$("#form_wrapper, #form_ov_5").animate({
						'opacity':'1',
						//'display':'block',
						//'height':'toggle',
					}, 500,
						function() {
							$("#form_wrapper, #form_ov_5").css("display","block").css("opacity","1");
					});
				},
				error: function() {
					console.log('Unable to get video id.');
				}
		});	
}

function share_site_fb() {
	window.open(
      'https://www.facebook.com/sharer/sharer.php?u='+encodeURIComponent(location.href), 
      'facebook-share-dialog', 
      'width=626,height=436'); 
    return false;
	
}
function share_site_tw() {
	window.open(
      'https://twitter.com/share', 
      'twitter-share-dialog', 
      'width=626,height=460'); 
    return false;
	
}

function share_site_tum() {
	window.open(
      'http://www.tumblr.com/share/link?url='+encodeURIComponent(location.href)+'&name=Sons of Anarchy Clubhouse Stories&description=Everyones got one of THOSE stories. That story you dont tell your mother. The clubhouse story. Upload your Sons Of Anarchy - inspired clubhouse story. We wont tell your mother', 
      'tumblr-share-dialog', 
      'width=626,height=450'); 
    return false;
	
}
function share_site_pin() {
	window.open(
      'http://pinterest.com/pin/create/button/?url='+encodeURIComponent(location.href)+'&media='+encodeURIComponent(location.href)+'/images/logo.png'+'&description=Everyones got one of THOSE stories. That story you dont tell your mother. The clubhouse story. Upload your Sons Of Anarchy - inspired clubhouse story. We wont tell your mother', 
      'pinterest-share-dialog', 
      'width=626,height=436'); 
    return false;
	
}
// show and hide flow
function showSubmit(){
$("#form_ov_5, #form_ov_4, #form_ov_3, #form_ov_2, #intro, #gallery").hide("linear");
$("#form_wrapper").css("display","block");
$("#form_wrapper").css("opacity","1");
$("#form_ov_1").css("display", "block");
$("#form_ov_1").animate({"opacity": "1","display": "block"}, 300);
}
function showOV1(){
$("#intro, #gallery, #form_ov_5, #form_ov_4, #form_ov_3, #form_ov_2").hide("linear");
$("#form_wrapper").css("display","block");
$("#form_wrapper").css("opacity","1");
$("#form_ov_1").css("display", "block");
$("#form_ov_1").animate({"top": "40px","opacity": "1","display": "block"}, 300);
}
function showOV2(){
$("#form_ov_1").hide("linear");
$("#form_ov_2").animate({"display": "block","top": "40px","opacity": "1"}, 300);
$("#form_ov_2").css("display", "block");
}
function editVideo(){
$("#form_ov_2").hide("linear");
$("#form_ov_1").animate({"top": "40px","opacity": "1","display": "block"}, 300);
$("#form_ov_1").css("display", "block");
}
function showOV4(){
$("#form_ov_2").hide("linear");
$("#form_ov_4").animate({"top": "40px","opacity":"1","display": "block"}, 300);
$("#form_ov_4").css("display", "block");
}
function showGall(){
$("#intro, #form_ov_5, #form_ov_4, #form_ov_3, #form_ov_2, #form_ov_1, #form_wrapper").hide("linear");
$("#gallery, #gallery_wrapper").animate({"top": "40px","opacity":"1","display": "block"}, 300);
$("#gallery, #gallery_wrapper").css("display", "block");
}
function closeOV1(){
$("#gallery").show("linear");
$("#form_ov_1").hide("linear");
$("#form_wrapper").css("display","none");
$("#form_wrapper").css("opacity","0");
}
function closeOV2(){
$("#gallery").show("linear");
$("#form_ov_2").hide("linear");
$("#form_wrapper").css("display","none");
$("#form_wrapper").css("opacity","0");
}
function closeOV4(){
$("#form_ov_4").hide("linear");
$("#gallery").show("linear");
$("#form_wrapper").css("display","none");
$("#form_wrapper").css("opacity","0");
}
function closeOV5(){
$("#form_ov_5").hide("linear");
$("#gallery, #gallery_wrapper").show("linear");
$("#form_wrapper").css("display","none");
$("#form_wrapper").css("opacity","0");
}
$('input, textarea').on('click focusin', function() {
    this.value = '';
});
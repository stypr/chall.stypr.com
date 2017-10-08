/* 
	jquery is enough for the only javascript framework..
	This is the remastered code of my old code(or commits) :p
	You may make pull requests for any fixes/improvements.
*/

/* Global variables */
var IRC_CHANNEL = "stypr"; // freenode
var CURRENT_PAGE = null;
var CURRENT_LANG = null;
var CURRENT_USER = null;
var IS_AUTH = false;

/* Helper functions */
var add_data = function(t, d){ $(t).append(d); }
var new_data = function(t, d){ if(!d)d=''; $(t).html(d); }
var check_string = function(str, min, max){
	if(!min) min=5; if(!max) max=30;
	var _regexp = '^[a-zA-Z0-9-_!@$.%^&*()가-힣]{'+min+','+max+'}$';
	var _check = new RegExp(_regexp).test(str);
	return _check;
}

/* Action functions */
function act_chall_auth(){
	_input = {'flag': $("#flag").val() }
	$("#output-message").removeClass("flash-error");
	$("#output-message").addClass("flash-info");
	$("#output-message").addClass("flash");
	$("#output-message").html(output_intl("chall-auth-check"));
	var check_flag = new RegExp("^[a-zA-Z0-9-_:+!@#$.%^&*(){}:\/.\ <>가-힣]{0,100}$").test(_input['flag']);
	if(!check_flag){
		$("#output-message").addClass("flash-error");
		$("#output-message").html(output_intl("chall-auth-invalid"));
		return false;
	}
	$.post("?controller=challenge&action=auth", _input, function(d){
		switch(d){
			case 'success':
				main();
				break;
			case 'already-solved':
				$("#output-message").addClass("flash-error");
				$("#output-message").html(output_intl("chall-auth-already-solved"));
				break;
			case 'nope':
				$("#output-message").addClass("flash-error");
				$("#output-message").html(output_intl("chall-auth-wrong"));
				break;
		}
	});
	return false;
}
function act_user_recover(n){
	$("#output-message").removeClass("flash-error");
	$("#output-message").addClass("flash-info");
	$("#output-message").addClass("flash");
	$("#output-message").html(output_intl("find-send-loading"));
	_input = {'recovery_code': n, 'password': $("#password").val() }
	if(!check_string(_input['password'], 4, 100)){
		$('#output-message').html(output_intl('reg-deny-pass')+'<br>' +
			'<pre>RegExp: ^[a-zA-Z0-9-_!@$.%^&*()가-힣]{4, 100}$</pre>');
		return false;
	}
	$.post("?controller=user&action=recover", _input, function(d){
		if(d == true){
			window.location.hash = '/user/login';
		}else{
			$("#output-message").addClass("flash-error");
			$("#output-message").html(output_intl("find-new-fail"))
		}
	});
	return false;
}
function act_user_find(){
	_input = {'username': $("#username").val() }
	$("#output-message").removeClass("flash-error");
	$("#output-message").addClass("flash-info");
	$("#output-message").addClass("flash");
	$("#output-message").html(output_intl("find-send-loading"));
	$.post("?controller=user&action=find", _input, function(d){
		switch(d){
			case "done":
				$("#output-message").html(output_intl("find-send-done"));
				break;
			case "exceed":
				$("#output-message").addClass("flash-error");
				$("#output-message").html(output_intl("find-send-exceed"));
				break;
			case "fail":
				$("#output-message").addClass("flash-error");
				$("#output-message").html(output_intl("find-send-fail"));
				break;
			case "nope":
				$("#output-message").addClass("flash-error");
				$("#output-message").html(output_intl("find-send-nope"));
				break;
			default: // unknown
				$("#output-message").addClass("flash-error");
				$("#output-message").html(output_intl("find-send-fail"));
				break;
		}
	});
	return false;
}
function act_user_auth(){
	// user auth event
	$("#output-message").removeClass("flash-error");
	$("#output-message").addClass("flash-info");
	$("#output-message").addClass("flash");
	$("#output-message").html(output_intl("auth-loading"));
	_input = {'nickname': $("#nickname").val(), 'password': $("#password").val()};
	$.post("?controller=user&action=login", _input, function(d){
		if(d == true){
			if($("#remember-nick").prop('checked')){
				localStorage.setItem('current_nick', _input['nickname']);
			}else{
				localStorage.setItem('current_nick', null);
			}
			IS_AUTH = true;
			window.location.hash = '#/';
			main();
		}else{
			$("#output-message").addClass("flash-error");
			$("#output-message").html(output_intl("auth-wrong"));
		}
	});
	return false;
}
function act_user_edit(){
	// user auth event
	$("#output-message").removeClass("flash-error");
	$("#output-message").addClass("flash-info");
	$("#output-message").addClass("flash");
	$("#output-message").html(output_intl("auth-loading"));
	_input = {'password': $("#password").val(), 'comment': $("#comment").val()};
	if(_input['password']){
		if(!check_string(_input['password'], 4, 100)){
			$('#output-message').html(output_intl('reg-deny-pass')+'<br>' +
				'<pre>RegExp: ^[a-zA-Z0-9-_!@$.%^&*()가-힣]{4, 100}$</pre>');
			return false;
		}
		if(!(new RegExp("^[a-zA-Z0-9-_:+!@#$.%^&*(){}:\/.\ <>가-힣]{0,50}$").test(_input['comment']))){
			$('#output-message').html(output_intl('reg-deny-comment')+'<br>' +
				'<pre>RegExp: ^[a-zA-Z0-9-_:+!@#$.%^&*(){}:\/.\ <>가-힣]{0,50}$</pre>');
			return false;
		}
	}
	$.post("?controller=user&action=edit", _input, function(d){
		if(d == true){
			if($("#remember-nick").prop('checked')){
				localStorage.setItem('current_nick', _input['nickname']);
			}else{
				localStorage.setItem('current_nick', null);
			}
			IS_AUTH = true;
			$("#output-message").addClass("flash-info");
			$("#output-message").html(output_intl("edit-success"));
		}else{
			$("#output-message").addClass("flash-error");
			$("#output-message").html(output_intl("edit-fail"));
		}
	});
	return false;
}
function act_user_register(){
	// user register event
	$("#output-message").removeClass("flash-error");
	$("#output-message").addClass("flash-info");
	$("#output-message").addClass("flash");
	$("#output-message").html(output_intl("auth-loading"));
	_input = {'username': $("#username").val(), 'nickname': $("#nickname").val(), 'password': $("#password").val()};

	if(!check_string(_input['username'], 5, 100)){ 
		$("#output-message").addClass("flash-error");
		$('#output-message').html(output_intl('reg-deny-user')+'<br>' +
			'<pre>RegExp: ^[a-zA-Z0-9-_!@$.%^&*()가-힣]{8, 100}$</pre>');
		return false;
	}
	if(!check_string(_input['nickname'], 3, 20)){
		$('#output-message').html(output_intl('reg-deny-nick')+'<br>' +
			'<pre>RegExp: ^[a-zA-Z0-9-_!@$.%^&*()가-힣]{3, 20}$</pre>');
		return false;
	}
	if(!check_string(_input['password'], 4, 100)){
		$('#output-message').html(output_intl('reg-deny-pass')+'<br>' +
			'<pre>RegExp: ^[a-zA-Z0-9-_!@$.%^&*()가-힣]{4, 100}$</pre>');
		return false;
	}
	$.post("?controller=user&action=register", _input, function(d){
		switch(d){
			case "duplicate_nick":
				$('#output-message').html(output_intl('reg-deny-dup-nick'));
				return false;
			case "duplicate_mail":
				$('#output-message').html(output_intl('reg-deny-dup-mail'));
				return false;
			case "email_format":
				$('#output-message').html(output_intl('reg-deny-format-mail'));
				return false;
			case "size": // error by length
				$('#output-message').html(output_intl('reg-deny-size'));
				return false;
			case "true":
				// get back to login on successful.
				window.location.hash = '#/user/login';
				main();
				return false;
			default:
				$('#output-message').html(output_intl('reg-deny-unknown'));
				return false;
		}
	});
	return false;
}


/* Content functions */
var load_chat = function(){
	if(!IS_AUTH){ set_error(403); return; }
	new_data('#content', '<iframe src="//kiwiirc.com/client/irc.freenode.net/?nick='+CURRENT_USER['nick']+'&theme=cli#'+
		IRC_CHANNEL +'" style="border:0; width:100%; height:450px;"></iframe>' +
	'<center><h4>' + output_intl('chat-rule') + '</h4></center>');
};
var load_chall = function(p){
	if(!IS_AUTH){ set_error(403); return; }
	new_data('#content', '<div class="row column centered">'+
		'<div id="output-message"></div><form onsubmit="return act_chall_auth()">'+
		'<div class="input-group columns">'+
		'<div class="two-thirds p-2 column">'+
		'<input class="form-control" placeholder="Answer format: flag{ ... }" autocomplete="off" id="flag" name="flag" style="width:100%; font-family:monospace;"></div>'+
		'<div class="one-third p-2 column"><span class="input-group-button">'+
		'<button class="btn btn-primary one-third" style="width:100%;" type="submit">'+
		'<span class="octicon octicon-key"> '+output_intl('auth')+'</span>'+
		'</button></div>'+
		'</div><hr style="border:0;">');
	$.get("?controller=challenge&action=list", function(d){
		// ascending order
		d=d.sort(function(a,b){
			if(a.challenge_score == b.challenge_score) return 0;
			return a.challenge_score > b.challenge_score ? 1 : -1;
		});
		/*
		// filter unsolved TBD
		d=d.filter(function(a){
			return a.challenge_solved === false;
		}); */
		for(var i=0;i<d.length;i++){
			if(d[i]['challenge_solved'] == false){
				add_data("#content", '<div class="Box mb-3"><div class="Box-header pt-2 pb-2 Box-header--blue">' +
					'<h3 class="Box-title"><span class="octicon octicon-bug">&nbsp;</span>'+ d[i]['challenge_name'] +
					' <span class="right">' + d[i]['challenge_score']+ output_intl('pt') + '</span></h3></div>'+
					'<div class="Box-body">' + d[i]['challenge_desc']+
					'</form></div></div>');
			}else{
				add_data("#content", '<div class="Box mb-3"><div class="Box-header pt-2 pb-2 Box-header--green">' +
					'<h3 class="Box-title"><span class="octicon octicon-shield">&nbsp;</span>'+ d[i]['challenge_name'] +
					' <span class="right">' + d[i]['challenge_score']+ output_intl('pt') + '</span></h3></div>'+
					'<div class="Box-body">' + d[i]['challenge_desc']+
					'</form></div></div>');
			}
		}
	});
};
var load_profile = function(p){
	$.get("?controller=status&action=profile&nickname=" + p, function(d){
		_solve = [];
		_break = [];
		// TBD: need to optimize, badge
		if(!d){ set_error(404); return; }
		if(d['solved']){
			// parse solved
			for(var i=0;i<d['solved'].length;i++){
				if(d['solved'][i]['chall_break']){
					_break.push({'challenge_name': d['solved'][i]['chall_name'],
					'solve_date': d['solved'][i]['chall_solve_date'],
					'solve_score': d['solved'][i]['chall_score'],
					'break_rank': d['solved'][i]['chall_break']['break_rank'],
					});
				}else{
					_solve.push({'challenge_name': d['solved'][i]['chall_name'],
					'solve_date': d['solved'][i]['chall_solve_date'],
					'solve_score': d['solved'][i]['chall_score'],
					});
				}
			}
		}
		_break_out = "";
		_break_html = "";
		if(_break.length){
			for(var i=0;i<_break.length;i++){
				_break_out += '<div class="Box-header pt-2 pb-2">'+
					'<span class="octicon octicon-flame" style=\'letter-spacing:-.5px;\'><sup>#'+_break[i]['break_rank']+'</sup>&nbsp;</span>'+
					_break[i]['challenge_name']+' (' + _break[i]['solve_score'] + output_intl('pt') + ')'+
					'<span class="right">'+_break[i]['solve_date']+'</span>'+
					'</div>';
			}
			_break_html = '<h3>'+output_intl('profile-break')+'</h3>'+
				'<div class="Box Box-default">'+
				_break_out+
				'</div><br>';
		}
		_solve_out = "";
		_solve_html = "";
		if(_solve.length){
			for(var i=0;i<_solve.length;i++){
				_solve_out += '<div class="Box-header pt-2 pb-2">'+
					'<span class="octicon octicon-check" style=\'letter-spacing:-.5px;\'>&nbsp;</span>'+
					_solve[i]['challenge_name']+' (' + _solve[i]['solve_score'] + output_intl('pt') + ')'+
					'<span class="right">'+_solve[i]['solve_date']+'</span>'+
					'</div>';
			}
			_solve_html = '<h3>'+output_intl('profile-clear')+'</h3>'+
			'<div class="Box Box-default">'+
			_solve_out+
			'</div><br>';
		}
		if(!(_solve_out||_break_out)){
			_solve_html = '<div class="blankslate blankstate-spacious">'+
				'<span class="mega-octicon octicon-thumbsdown blankslate-icon"></span>'+
				'<h3>'+output_intl('profile-no-solve-head')+'</h3>'+
				'<p>'+output_intl('profile-no-solve-body')+'</p>'+
				'</div>';
		}
		new_data("#content", '<div class="columns">'+
			'<div class="four-fifths column">'+
			'<h1 style="line-height:0.9;">'+d['nick']+'</h1>' +
			'<hr style="margin:5pt;border:0;">'+
			'<p style="line-height:2.0;">#'+d['rank']+output_intl('profile-score-prefix')+
			+d['score']+output_intl('pt')+output_intl('profile-score-suffix')+'.</p>'+
			''+
			_break_html+
			_solve_html+
			'</div>'+
			''+
			'<div class="one-fifth column"><center>'+
			'<img class="avatar" src="'+d['profile_picture']+'" width=100%>'+
			'<font size=2><span class="octicon octicon-lock" style="margin-top:5pt;"></span>'+d['username']+'<br>'+
			'Since '+d['join_date']+'.</font><br><br>'+
			'<code style="letter-spacing:-1px; white-space: pre-wrap;">'+d['comment']+'</pre></center>'+
			'</div>');
	});
}
var load_user = function(p, n){
	if(!n) n='';
	switch(p){
		case "find":
			if(IS_AUTH){ set_error(418); break; }
			if(n){
				if(!(new RegExp("^[a-zA-Z0-9-_]{0,50}$").test(n))){
					window.location.hash = '#/user/find';
					return false;
				}
				new_data("#content", '<div class="row column centered">'+
					'<div id="output-message" class="mb-2" ></div>'+
					'<form class="auth-form-body" onsubmit="return act_user_recover(\''+n+'\');">'+
					'<label for="password">'+output_intl('find-new-pw')+'</label>'+
					'<input class="form-control input-block disabled" tabindex=1 name="password" id="password" type="password">'+
					'<button class="btn btn-block btn-primary" tabindex=4 id="edit_button" type="submit">'+output_intl('find-new-submit')+'</button>'+
					'</form>');
			}else{
			new_data("#content", '<div class="row column centered">'+
				'<div id="output-message" class="mb-2" ></div>'+
				'<form class="auth-form-body" onsubmit="return act_user_find();">'+
				'<label for="username">'+output_intl('reg-input-email')+'</label>'+
				'<input class="form-control input-block disabled" tabindex=1 name="username" id="username" type="email">'+
				'<p class="note">'+output_intl('find-send-tip')+'</p>'+
				'<button class="btn btn-block btn-primary" tabindex=4 id="edit_button" type="submit">'+output_intl('find-send-submit')+'</button>'+
				'</form>');
			}
			break;
		case "edit":
			if(!IS_AUTH){ set_error(403); break; }
			if(!CURRENT_USER['comment']) CURRENT_USER['comment'] = '';
			new_data("#content", '<div class="row column centered">'+
				'<div id="output-message" class="mb-2" ></div>'+
				'<form class="auth-form-body" onsubmit="return act_user_edit();">'+
				'<label for="username">'+output_intl('reg-input-email')+'</label>'+
				'<input class="form-control input-block disabled" tabindex=1 name="username" id="username" type="text" disabled value='+CURRENT_USER['username']+'>'+
				'<label for="nickname">'+output_intl('auth-nick')+'</label>'+
				'<input class="form-control input-block disabled" tabindex=1 name="nickname" id="nickname" type="text" disabled value='+CURRENT_USER['nick']+'>'+
				'<label for="password">'+output_intl('edit-new-pass')+'</label>'+
				'<input class="form-control input-block" tabindex=2 id="password" name="password" placeholder="Password" type="password">'+
				'<p class="note">'+output_intl('edit-password-tip')+'</p>'+
				'<label for="password">'+output_intl('comment')+'</label>'+
				'<input class="form-control input-block" tabindex=3 id="comment" name="comment" value="'+CURRENT_USER['comment']+'" placeholder="'+output_intl('edit-comment-tip')+'">'+
				'<button class="btn btn-block btn-primary" tabindex=4 id="edit_button" type="submit">'+output_intl('edit-submit')+'</button>'+
				'</form>');
			break;
		case "login":
			if(IS_AUTH){ set_error(418); break; }
			new_data("#content", '<div class="row column centered">'+
				'<form class="auth-form-body" onsubmit="return act_user_auth();">'+
				'<div id="output-message" class="mb-2" ></div>'+
				'<label for="nickname">'+output_intl('auth-nick')+'</label>'+
				'<input class="form-control input-block" tabindex=1 name="nickname" id="nickname" type="text" placeholder="stypr, neko, superuser, ...">'+
				'<label for="password">'+output_intl('auth-pass')+' <a href="#/user/find" class=right>'+output_intl('auth-forgot')+'</a></label>'+
				'<input class="form-control input-block" tabindex=2 id="password" name="password" placeholder="Password" type="password">'+
				'<input class="form-checkbox" id="remember-nick" type="checkbox"> '+output_intl('auth-remember')+
				'<button class="btn btn-block btn-primary" tabindex=3 id="signin_button" type="submit">'+output_intl('auth-login')+'</button>'+
				'</form><br><p class="new-comer">'+output_intl('auth-reg-new')+
				' <a href="#/user/register" data-ga-click="Sign in, switch to sign up">'+output_intl('auth-reg-create')+'</a>.</p>');
				_nick = localStorage.getItem('current_nick');
				if(_nick && _nick != 'null'){
					$("#remember-nick").prop('checked', true);
					$("#nickname").val(_nick);
				}
			break;
		case "logout":
			if(!IS_AUTH){ set_error(403); break; }
			window.location.hash = '#/';
			$.get("?controller=user&action=logout", function(d){
				IS_AUTH = false;
				window.location.hash = '#/user/login';
				main();
			});
			break;
		case "register":
			new_data("#content", '<div class="columns">'+
				'<div class="two-thirds column">'+
				'<h2 class="setup-form-title mb-3">'+output_intl('reg-head')+'</h2>'+
				'<form onsubmit="return act_user_register();">'+
				'<dl class="form-group"><dt class="input-label">'+
				'<label autocapitalize="off" autofocus="autofocus" for="username" for="username">'+output_intl('reg-input-email')+'</label>'+
				'</dt><dd>'+
				'<input autocapitalize="off" autofocus="autofocus" class="form-control" id="username" name="username" size="30" type="email" />'+
				'<p class="note">'+output_intl('reg-info-email')+'</p>'+
				'</dd></dl>'+
				'<dl class="form-group"><dt class="input-label">'+
				'<label autocapitalize="off" for="nickname">'+output_intl('nickname')+'</label>'+
				'</dt><dd>'+
				'<input autocapitalize="off" class="form-control" name="nickname" size="30" type="text" id="nickname">'+
				'<p class="note">'+output_intl('reg-info-nickname')+'</p>'+
				'</dd></dl>'+
				'<dl class="form-group"><dt class="input-label">'+
				'<label autocapitalize="off" for="password">'+output_intl('auth-pass')+'</label>'+
				'</dt><dd>'+
				'<input autocapitalize="off" class="form-control" name="password" size="30" type="password" id="password">'+
				'<p class="note">'+output_intl('reg-info-password')+'</p>'+
				'</dd></dl>'+
				'<div id="output-message" class="mb-2" ></div>'+
				'<input type="submit" class="btn btn-primary" id="signup_button" value="'+output_intl('reg-submit')+'">'+
				'</form></div>'+
				'<div class="one-third column"><h2>'+output_intl('reg-note')+'</h2><br>'+
				'<li>'+output_intl('reg-note-1')+'</li><br>' +
				'<li>'+output_intl('reg-note-2')+'</li><br>' +
				'<li>'+output_intl('reg-note-3')+'</li><br>' +
				'</div></div>');
			break;
		case "find":
		default:
			set_error(404);
			break;
	}
};

var load_intro = function(){
	// TBD: I need to translate the content.. lolz
	new_data("#content", output_intl('INTRO'));
};
var load_status = function(p){
	// add tab
	new_data("#content", "<div class='tabnav'><nav class='tabnav-tabs' id='content-tabs'></nav></div>" + 
		"<div id='output-layer'></div>");
	new_data("#content-tabs", '<a href="#/status/player" class="tabnav-tab" sub-id="player">' + output_intl('stat-player') + '</a>' +
		'<a href="#/status/chall" class="tabnav-tab" sub-id="chall">' + output_intl('stat-chall') + '</a>' +
		'<a href="#/status/auth" class="tabnav-tab" sub-id="auth">' + output_intl('stat-auth') + '</a>' +
		'<a href="#/status/fame" class="tabnav-tab" sub-id="fame">' + output_intl('stat-fame') + '</a>' +
		'</nav>');
	// auto-select tab
	if(!p) p = 'player';
	$(".tabnav-tab[sub-id='"+p+"']").addClass("selected");

	// content by the tab
	switch(p){
		case 'fame':
			// TBD: probably will develop on freetime..	
			new_data("#output-layer");
			add_data("#output-layer", '<table class="data-table" id="pwner"></table>' +
			'<h3>Please refer to <a href="https://github.com/stypr/chall.stypr.com#for-pwners-who-seek-to-report-vuln">https://github.com/stypr/chall.stypr.com#for-pwners-who-seek-to-report-vuln</a>');
			
			break;
		case 'auth':
			$.get('?controller=status&action=auth', function(d){
				new_data("#output-layer");
				add_data("#output-layer", '<table class="data-table table-hover" id="scoreboard" style="font-size:10pt;">' +
					'<thead><tr>'+
					'<th align=center>#</th><th align=center>'+output_intl('nickname')+'</th>' +
					'<th align=center>'+output_intl('chall')+'</th>' +
					'<th align=center>'+output_intl('chall-solve-date')+'</th>'+
					'</tr></thead><tbody id="log-list"></tbody></table>');
				for(var i=0;i<d.length;i++){
					_log = d[i];
					add_data("#log-list", '<tr class="info" style="cursor:pointer;" onclick="location.replace(\'#/profile/'+_log['nick']+'\')">' +
						'<td>'+_log['no']+'</td><td>'+_log['nick'] + '</td>' +
						'<td>'+_log['chall']+'</td>' + 
						'<td>'+_log['date']+'</td>' +
						'</tr>');
				}
			});
			break;
		case 'chall':
			$.get('?controller=status&action=challenge', function(d){
				new_data("#output-layer");
				for(var i=0;i<d.length;i++){
					_top = d[i]['break'];
					_top_break = '';
					_css_break = '';
					try{
						for(var j=0;j<_top.length;j++){
							_top_break += '<tr class="info" style="cursor:pointer;" onclick="location.replace(\'#/profile/'+_top[j]['user']+'\')">' +
							'<td>#'+(_top[j]['rank'])+'</td><td>'+_top[j]['user']+'</td><td>'+_top[j]['date']+'</td></tr>';
						}
					}catch(e){ }
					if(!_top_break){
						_css_break = 'Box-header--red';
						_top_break = '<tr><td colspan=4><h2 align=center>PWN ME IF YOU CAN</h2></td></tr>';
					}
					add_data("#output-layer", '<div class="Box mb-3"><div class="Box-header '+_css_break+' pt-2 pb-2">' +
						'<h3 class="Box-title">'+ d[i]['name'] +' <span class="right">' + d[i]['score']+output_intl('pt')+'</span></h3></div>'+
						'<div class="Box-body"><table class="data-table mt-0" id="break-info">' +
						'<th>'+output_intl('chall-by')+'</th><td>' + d[i]['author']+'</td>'+
						'<th>'+output_intl('chall-solver')+'</th><td>' + d[i]['solver']+ ' ' +
						''+output_intl('chall-player-count')+'</td><th>'+output_intl('last_solved')+'</th> '+
						'<td>' + d[i]['last-solved'] + '</td></tr></table>' +
						'<table class="data-table table-hover mt-2" id="break-stat"><tr><td width=8>&nbsp;<font color=red>'+
						'<span class="octicon octicon-flame"></span></font></td>'+
						'<td>'+output_intl('nickname')+'</td><td>'+output_intl('chall-solve-date')+'</td></tr>' +
						_top_break +
						'</td></tr></table>');
				}
				
			});
			break;
		case 'player':
		default:
			$.get('?controller=status&action=scoreboard', function(d){
				new_data("#output-layer", '<table class="data-table table-hover" id="scoreboard" style="font-size:10pt;">' +
					'<thead><tr>'+
					'<th align=center></th><th align=center>'+output_intl('nickname')+'</th>' +
					'<th align=center>'+output_intl('score')+'</th>' +
					'<th align=center>&nbsp;<font color=red><span class="octicon octicon-flame"></span></font></th>' +
					'<th align=center>'+output_intl('comment')+'</th>'+
					'<th align=center>'+output_intl('last_solved')+'</th>'+
					'</tr></thead><tbody id="ranker-list"></tbody></table>');
				ranker = false; // check if the user is ranker.
				for(var i=0;i<d['ranker'].length;i++){
					_ranker = d['ranker'][i];
					_rank = (i)<3 && "&#9813;" || i+1;
					_ranker['comment'] = _ranker['comment'] != null ? _ranker['comment'] : '';
					if((CURRENT_USER['nick'] == _ranker['nickname'])){
						_rank = '&#9733;';
						ranker = true;
					}
					add_data("#scoreboard", '<tr class="info" style="cursor:pointer;" onclick="location.replace(\'#/profile/'+_ranker['nickname']+'\')">' +
						'<td>'+_rank+'</td><td>'+_ranker['nickname'] + '</td>' +
						'<td>'+_ranker['score']+'</td>' + 
						'<td>'+_ranker['break_count']+'</td>' +
						'<td>'+_ranker['comment']+'</td><td>'+_ranker['last_solved']+'</td></tr>');
				}
				if(ranker == false && IS_AUTH == true){
					add_data("#scoreboard", '<tr class="info" style="cursor:pointer;" onclick="location.replace(\'#/profile/'+CURRENT_USER['nick']+'\')">' +
						'<td>'+CURRENT_USER['rank']+'</td><td>'+CURRENT_USER['nick']+'</td>' +
						'<td>'+CURRENT_USER['score']+'</td>' + 
						'<td></td>' +
						'<td>'+CURRENT_USER['comment']+'</td><td>'+CURRENT_USER['last_solved']+'</td></tr>');
				}
				add_data("#output-layer", "<h4 align=center>"+d['total']+output_intl("player-total-msg")+"</h4>");
			});
			break;
	}
}

/* Basic functions for init/route */
var set_error = function(t){
	// this does not follow the HTTP standard, please don't judge me.
	switch(t){
		case 418: // wtf?
			new_data("#content", "<div class='flash flash-warning'><h5>"+output_intl("error-wtf")+"</h5></div><br>" +
				"<img src='./static/image/418.png' width=100%>");
			break;
		case 403: // unauthorized
			new_data("#content", "<div class='flash flash-error'><h5>"+output_intl("error-auth")+"</h5></div><br>" +
				"<img src='./static/image/404.jpg' width=100%>");
			break;
		case 404: // not found
		default:
			new_data("#content", "<div class='flash flash-error'><h4>"+output_intl("error-nope")+"</h4>"+output_intl("error-nope-info")+"</div><br>" +
				"<img src='./static/image/403.png' width=100%>");
			break;
	}	
}
var set_auth = function(){
	$.get('?controller=user&action=check', function(d){
		res = $.parseJSON(d);
		if(res != IS_AUTH){
			IS_AUTH = res;
		}
		set_layout();
	});
};
var set_language = function(){
	_local = localStorage.getItem('current_lang');
	if(CURRENT_LANG == null){
		if(_local == 'null' || !_local){
			CURRENT_LANG = 'en';
			localStorage.setItem('current_lang', CURRENT_LANG);	
		}else{
			CURRENT_LANG = _local;
		}
	}else{
		localStorage.setItem('current_lang', CURRENT_LANG);
	}
	$("*:not(.octicon)").css("font-family", output_intl('FONT'));
	$("#language-select").val(CURRENT_LANG);
	// add events..
	$("#language-select").unbind('change');
	$("#language-select").change(function(){
		CURRENT_LANG = $("#language-select").val();
		$("*:not(.octicon)").css("font-family", output_intl('FONT'));
		main();
	});	
};
var set_layout = function(){
	new_data("#sidebar");
	// sidebar
	add_data("#sidebar", "<ul class='filter-list' id='sidebar-menu'></ul>");
	$.get('?controller=status&action=profile', function(d){
		CURRENT_USER = d;
		if(IS_AUTH){
			_sub = 'edit';
			add_data("#sidebar-menu", "<li page-id='" + _sub + "'><a href='#/user/edit' class='filter-item'>"+
				"<table class='profile'><tr><td rowspan=2><img src="+d['profile_picture']+" width=40 class='profile-image'>&nbsp;</td>"+
				"<td class='profile-nickname'>"+d['nick']+"</td></tr><tr><td class='profile-score'>"+d['score']+output_intl('pt')+"</td></tr></table>"+
				"</a></li>");
			_sub = 'logout';
			add_data("#sidebar-menu", "<li page-id='" + _sub + "'><a href='#/user/logout' class='filter-item'></a></li>");
			add_data("#sidebar-menu>li[page-id='"+_sub+"']>a", output_intl(_sub) +
				"<span class='octicon octicon-sign-out right'></span>");
		}else{
			_sub = 'login';
			add_data("#sidebar-menu", "<li page-id='" + _sub + "'><a href='#/user/login' class='filter-item'></a></li>");
			add_data("#sidebar-menu>li[page-id='"+_sub+"']>a",  output_intl(_sub) +
				"<span class='octicon octicon-sign-in right'></span>");
		}
		add_data("#sidebar-menu", "<hr>");
		_sub = 'intro';
		add_data("#sidebar-menu", "<li page-id='" + _sub + "'><a href='#/' class='filter-item'></a></li>");
		add_data("#sidebar-menu>li[page-id='"+_sub+"']>a",  output_intl(_sub) +
			"<span class='octicon octicon-home right'></span>");
		_sub = 'status';
		add_data("#sidebar-menu", "<li page-id='" + _sub + "'><a href='#/status' class='filter-item'></a></li>");
		add_data("#sidebar-menu>li[page-id='"+_sub+"']>a",  output_intl(_sub) +
			"<span class='octicon octicon-graph right'></span>");
		if(IS_AUTH){
			_sub = 'chall';
			add_data("#sidebar-menu", "<li page-id='" + _sub + "'><a href='#/chall' class='filter-item'></a></li>");
			add_data("#sidebar-menu>li[page-id='"+_sub+"']>a",  output_intl(_sub) +
				"<span class='octicon octicon-bug right'></span>");
			_sub = 'chat';
			add_data("#sidebar-menu", "<li page-id='" + _sub + "'><a href='#/chat' class='filter-item'></a></li>");
			add_data("#sidebar-menu>li[page-id='"+_sub+"']>a",  output_intl(_sub) +
				"<span class='octicon octicon-comment-discussion right'></span>");
		}
		// set route after load of sidebar
		set_route();
	});
	// adding click events //
	$("#sidebar-menu > li > a").unbind("click");
	$("#sidebar-menu > li > a").click(function(){
		$("#sidebar-menu .selected").removeClass("selected");
		$(this).siblings().removeClass("selected");
		$(this).addClass("selected"); // children(':first').
	});

};
var set_route = function(){
	_url = (typeof CURRENT_PAGE === "string" && CURRENT_PAGE !== "") && CURRENT_PAGE.split('/') || ["", ""];
	switch(_url[1]){
		case 'user':
			_d = IS_AUTH && 'logout' || 'login';
			if(_url[2] == 'edit'){ _d = 'edit'; }
			$("#sidebar-menu>li[page-id='"+_d+"']>a").addClass("selected");
			load_user(_url[2], _url[3]);
			break;
		case 'status':
			$("#sidebar-menu>li[page-id='status']>a").addClass("selected");
			load_status(_url[2]);
			break;
		case 'chat':
			$("#sidebar-menu>li[page-id='chat']>a").addClass("selected");
			load_chat();
			break;
		case 'chall':
			$("#sidebar-menu>li[page-id='chall']>a").addClass("selected");
			load_chall(_url[2]);
			break;
		case 'profile':
			$("#sidebar-menu>li[page-id='status']>a").addClass("selected");
			load_profile(_url[2]);
			break;
		case '':
			$("#sidebar-menu>li[page-id='intro']>a").addClass("selected");
			load_intro();
			break;
		default:
			set_error(404);
			//console.log(_url);
	}
};
/* Init function */
function main(){
	CURRENT_PAGE = location.hash.slice(1) || '/';
	set_language();
	// order of execution: set_auth() -> set_layout() -> set_route() 
	set_auth();
	// hash_change handler
	$(window).unbind('hashchange');
	$(window).on('hashchange',function(){ 
		CURRENT_PAGE = location.hash.slice(1);
		$("#sidebar-menu .selected").removeClass("selected");
		$(this).siblings().removeClass("selected");
		set_route();
	});
}
$(document).ready(main);

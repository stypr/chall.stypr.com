<!doctype html>
	<head>
	<meta charset="utf-8">
		<title>Stereotyped Challenges</title>
		<meta name="viewport" content="initial-scale=1, user-scalable=0">
		<!--<link href="//fonts.googleapis.com/css?family=Raleway:400,300,600" rel="stylesheet" type="text/css">-->
		<link href="//cdnjs.cloudflare.com/ajax/libs/octicons/4.4.0/font/octicons.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="//unpkg.com/primer-css@9.4.0/build/build.css">
		<style>
			body {background: #333;}
			#container {background:#fff;}
			.right { float: right; }
			.selector-language { margin-bottom: 30px; }
			#break-stat td, #scoreboard td { padding:5px; text-align:center; border-right:0; border-left:0; }
			#break-info td {text-align:center; border-right:0; font-size:13pt; font-weight:bold;}
			#break-info th {border:0; }
			#break-stat { margin:0; }
			#scoreboard th {text-align:center; border-right:0; font-size:13pt; }
			#scoreboard {border-right:0; border-left:0; border-top: 0;}
			.new-comer{padding:15px 20px;text-align:center;border:1px solid #d8dee2;border-radius:5px}
		.auth-form{width:400px;margin:60px auto}.auth-form .password-note{margin:15px 0;text-align:center}.auth-form-header{position:relative;padding:10px 20px;margin:0;color:#fff;text-shadow:0 -1px 0 rgba(0,0,0,0.3);background-color:#829aa8;border:1px solid #768995;border-radius:3px 3px 0 0}.auth-form-header h1{font-size:16px}.auth-form-header h1 a{color:#fff}.auth-form-header .octicon{position:absolute;top:10px;right:20px;color:rgba(0,0,0,0.4);text-shadow:0 1px 0 rgba(255,255,255,0.1)}.auth-form-message{max-height:180px;padding:10px 20px;margin-bottom:20px;overflow-y:scroll;border:1px solid #d8dee2;border-radius:3px}.auth-form-body{padding:20px;font-size:14px;background-color:#fff;border:1px solid #d8dee2;border-radius:3px;}.auth-form-body .input-block{margin-top:5px;margin-bottom:15px}.auth-form-body p{margin:0 0 10px}.two-factor-help{position:relative;padding:10px 10px 10px 36px;margin:60px 0 auto auto;border:1px solid #eaeaea;border-radius:3px}.two-factor-help h4{margin-top:0;margin-bottom:5px}.two-factor-help .octicon-device-mobile{position:absolute;top:10px;left:10px}.two-factor-help .octicon-key{position:absolute;left:10px}.two-factor-help .btn-sm{float:right}.two-factor-help ul{list-style-type:none}.u2f-send-code-spinner{position:relative;bottom:2px;display:none;vertical-align:bottom}.loading .u2f-send-code-spinner{display:inline}.u2f-login-spinner{position:relative;top:2px}.u2f-auth-header{padding-bottom:10px;margin-bottom:20px;border-bottom:1px solid #eaeaea}.u2f-auth-form-body{padding:30px 30px 20px;text-align:center}	.table>thead>tr>td.info,.table>tbody>tr>td.info,.table>tfoot>tr>td.info,.table>thead>tr>th.info,.table>tbody>tr>th.info,.table>tfoot>tr>th.info,.table>thead>tr.info>td,.table>tbody>tr.info>td,.table>tfoot>tr.info>td,.table>thead>tr.info>th,.table>tbody>tr.info>th,.table>tfoot>tr.info>th{background-color:#d9edf7}.table-hover>tbody>tr>td.info:hover,.table-hover>tbody>tr>th.info:hover,.table-hover>tbody>tr.info:hover>td,.table-hover>tbody>tr:hover>.info,.table-hover>tbody>tr.info:hover>th{background-color:#c4e3f3}
			.table-hover>tbody>tr:hover{background-color:#f5f5f5}
		</style>
	</head>
	<body>
		<div id="container" class="container-lg clearfix px-3 pt-3 pb-4 mt-4 mb-4">
			<div id="language" class="col-12 selector-language"><div class="right"><select id="language-select"><option>en</option><option>ko</option></select></div></div>
			<div id="sidebar" class="col-3 float-left pr-3"></div>
			<div id="content" class="col-9 float-left pl-2"></div>
		</div>
	</div>
	<!-- Loader TBD -->
	<script src="//unpkg.com/jquery@3.2.1/dist/jquery.js"></script>
	<script>
		/* jquery is enough for the javascript framework..
		This is the remastered code of old code :p
		You may make pull requests for any fixes/improvements.
		*/
		/* Global variables */
		var CURRENT_PAGE = null;
		var CURRENT_LANG = null;
		var IS_AUTH = false;

		/* Helper functions */
		var add_data = function(t, d){ $(t).append(d); }
		var new_data = function(t, d){ if(!d)d=''; $(t).html(d); }
		var output_intl = function(s){
			// I'm actually considering about adding japanese and chinese too..
			langmap = {
				
				'logout': {'en': 'Sign Out', 'ko': '로그아웃'},
				'login': {'en': 'Sign In', 'ko': '로그인'},
				'home': {'en': 'Home', 'ko': '메인'},
				'chall': {'en': 'Challenge', 'ko': '문제'},
				'chat': {'en': 'Chat', 'ko': '채팅방'},
				'status': {'en': 'Status', 'ko': '현황판'},

				'nickname': {'en': 'Nickname', 'ko': '닉네임'},
				'score': {'en': 'Score', 'ko': '점수'},
				'comment': {'en': 'Comment', 'ko': '정보'}, 
				'last_solved': {'en': 'Last Solved', 'ko': '최근 풀이시간'},

				'stat-player': {'en': 'Scoreboard', 'ko': '순위'},
				'stat-chall': {'en': 'Chall Info', 'ko': '문제 정보'},
				'stat-auth': {'en': 'Solve Log', 'ko': '인증 로그'},
				'stat-fame': {'en': 'Hall of Fame', 'ko': '명예의 전당'},

				'chall-solver': {'en': 'Solvers', 'ko': '풀은 인원'},
				'chall-player-count': {'en': 'players', 'ko': '명'},
				'chall-solve-date': {'en': 'Solved at', 'ko': '풀은 시간'},

				'auth-nick': {'en': 'Nickname', 'ko': '닉네임'},
				'auth-pass': {'en': 'Password', 'ko': '비밀번호'},
				'auth-forgot': {'en': 'Forgot password?', 'ko': '비밀번호를 잊으셨나요?'},
				'auth-reg-new': {'en': 'New Here?', 'ko': '처음 방문하셨나요?'},
				'auth-reg-create': {'en': 'Create an account', 'ko': '계정을 생성하세요'},
				'auth-loading': {'en': 'Checking...', 'ko': '확인 중...'},
				'auth-wrong': {'en': 'Incorrect Credentials.', 'ko': '계정정보가 일치하지 않습니다.'},

				'error-nope': {'en': 'Nope!', 'ko': '응 아니야~'},
				'error-nope-info': {'en': 'The page you are looking for is not found. Better check elsewhere :p', 
								'ko': '접속하신 페이지를 찾을 수 없습니다. 다른 곳을 확인해보세요 :p'},
				'error-auth': {'en': 'You need to sign in to view this page.', 'ko': '이 페이지를 보시려면 로그인 하셔야 합니다.'},
				'error-wtf': {'en': 'You\'re already signed in.', 'ko': '이미 로그인 하신 상태입니다.'},

			}
			return langmap[s][CURRENT_LANG];
		}

		/* Action functions */
		function act_user_auth(){
			// user auth event
			$("#output-message").removeClass("flash-error");
			$("#output-message").addClass("flash-info");
			$("#output-message").addClass("flash");
			$("#output-message").html(output_intl("auth-loading"));
			_input = {'nickname': $("#nickname").val(), 'password': $("#password").val()};
			$.post("?controller=user&action=login", _input, function(d){
				if(d == true){
					main();
					window.location.hash = '#/';
				}else{
					$("#output-message").addClass("flash");
					$("#output-message").addClass("flash-error");
					$("#output-message").html(output_intl("auth-wrong"));
				}
			});

			return false;
		}

		/* Content functions */
		var load_user = function(p){
			switch(p){
				case "login":
					if(IS_AUTH){ set_error(418); break; }
					new_data("#content", '<div class="row column centered one-half auth-form">'+
						'<div id="output-message" class="mb-2"></div><form class="auth-form-body" onsubmit="return act_user_auth();">'+
						'<label for="nickname">'+output_intl('auth-nick')+'</label>'+
						'<input class="form-control input-block" tabindex=1 name="nickname" id="nickname" type="text" placeholder="stypr, neko, superuser, ...">'+
						'<label for="password">'+output_intl('auth-pass')+' <a href="/#user/find" class=right>'+output_intl('auth-forgot')+'</a></label>'+
						'<input class="form-control input-block" tabindex=2 id="password" name="password" placeholder="Password" type="password">'+
						'<button class="btn btn-block btn-primary" tabindex=3 id="signin_button" type="submit">Sign in</button>'+
						'</form><br><p class="new-comer">'+output_intl('auth-reg-new')+
						' <a href="#/user/register" data-ga-click="Sign in, switch to sign up">'+output_intl('auth-reg-create')+'</a>.</p>');
					break;
				case "logout":
					if(!IS_AUTH){ set_error(403); break; }
					$.get("?controller=user&action=logout", function(d){
						main();
						window.location.hash = '#/';
					});
				case "register":
					break;
				case "find":
				default:
					set_error(404);
					break;
			}
		};

		var load_main = function(){
			// TBD: I need to translate the content.. lolz
			new_data("#content", "<h1>Stereotyped Challenges</h1><h2>Redefine your web hacking techniques today!</h2><br><br>" +
				"This website provides advanced web-based hacking challenges, which would require you to think and solve logically. Please try other wargame communities if you find difficulty in solving challenges.<br><br>"+
				"The rules of this website are simple — Make sure that other users can enjoy the wargame. DO NOT bruteforce challenges and DO NOT post your solutions on the internet. Solving challenges would become worthless if solutions are posted everywhere."+
				"Sharing a small bit of hints for challenges would be the most appropriate to help others.");
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
					add_data("#output-layer", '<table class="data-table" id="pwner"></table>');
					
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
							add_data("#log-list", '<tr onclick="location.replace(\'#/profile/'+_log['nick']+'\')">' +
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
							try{
								for(var j=0;j<_top.length;j++){
									_top_break += '<tr><td>#'+(_top[j]['rank'])+'</td><td>'+_top[j]['user']+'</td><td>'+_top[j]['date']+'</td></tr>';
								}
							}catch(e){ }
							if(!_top_break){
								_top_break = '<tr><td colspan=4><h2 align=center>...</h2></td></tr>';
							}
							add_data("#output-layer", '<div class="Box mb-3"><div class="Box-header pt-2 pb-2">' +
								'<h3 class="Box-title">'+ d[i]['name'] +' <span class="right">' + d[i]['score']+ 'pt</span></h3></div>'+
								'<div class="Box-body"><table class="data-table mt-0" id="break-info" style="font-size:12pt;">' +
								'<th>'+output_intl('chall-solver')+'</th><td>' + d[i]['solver']+ ' ' +
								''+output_intl('chall-player-count')+'</td><th>'+output_intl('last_solved')+'</th> '+
								'<td>' + d[i]['last-solved'] + '</td></tr></table>' +
								'<table class="data-table mt-2" id="break-stat"><tr><td width=8>&nbsp;<font color=red>'+
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
						for(var i=0;i<d.length;i++){
							_ranker = d[i];
							_rank = (i)<3 && "&#9813;" || i+1;
							add_data("#scoreboard", '<tr class="info" style="cursor:pointer;" onclick="location.replace(\'#/profile/'+_ranker['nickname']+'\')">' +
								'<td>'+_rank+'</td><td>'+_ranker['nickname'] + '</td>' +
								'<td>'+_ranker['score']+'</td>' + 
								'<td>'+_ranker['break_count']+'</td>' +
								'<td>'+_ranker['comment']+'</td><td>'+_ranker['last_solved']+'</td></tr>');
						}
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
						"<img src='./static/image/error.jpg' width=100%>");
					break;
				case 403: // unauthorized
					new_data("#content", "<div class='flash flash-error'><h5>"+output_intl("error-auth")+"</h5></div><br>" +
						"<img src='./static/image/error.jpg' width=100%>");
					break;
				case 404: // not found
				default:
					new_data("#content", "<div class='flash flash-error'><h4>"+output_intl("error-nope")+"</h4>"+output_intl("error-nope-info")+"</div><br>" +
						"<img src='./static/image/error.jpg' width=100%>");
					break;
			}	
		}
		var set_auth = function(){
			$.ajax({
				url: '?controller=user&action=check',
				success: function(d){
					res = $.parseJSON(d);
					if(res != IS_AUTH){
						IS_AUTH = res;
					}
				},
				async: false,
			});
		};
		var set_language = function(){
			_local = localStorage.getItem('current_lang');
			if(CURRENT_LANG == null){
				if(_local == 'null' || !_local){
					console.log('hits here');
					CURRENT_LANG = 'en';
					localStorage.setItem('current_lang', CURRENT_LANG);	
				}else{
					CURRENT_LANG = _local;
				}
			}else{
				localStorage.setItem('current_lang', CURRENT_LANG);
			}
			$("#language-select").val(CURRENT_LANG);
			// add events..
			$("#language-select").unbind('change');
			$("#language-select").change(function(){
				CURRENT_LANG = $("#language-select").val();
				main();
			});	
		};
		var set_layout = function(){
			// sidebar first //
			new_data("#sidebar");
			add_data("#sidebar", "<ul class='filter-list' id='sidebar-menu'></ul>");
	
			if(IS_AUTH){
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
			_sub = 'home';
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
				case 'chall':
					$("#sidebar-menu>li[page-id='"+_url[1]+"']>a").addClass("selected");
					break;
				case 'user':
					_d = IS_AUTH && 'logout' || 'login';
					$("#sidebar-menu>li[page-id='"+_d+"']>a").addClass("selected");
					load_user(_url[2]);
					break;
				case 'status':
					$("#sidebar-menu>li[page-id='status']>a").addClass("selected");
					load_status(_url[2]);
					break;
				case 'chat':
					$("#sidebar-menu>li[page-id='chat']>a").addClass("selected");
					break;
				case 'chall':
					$("#sidebar-menu>li[page-id='chall']>a").addClass("selected");
					break;
				case 'profile':
					$("#sidebar-menu>li[page-id='status']>a").addClass("selected");
					break;
				case '':
					$("#sidebar-menu>li[page-id='home']>a").addClass("selected");
					load_main();
					break;
				default:
					set_error(404);
					console.log(_url);
			}
		};

		/* Init function */
		function main(){
			set_auth();
			CURRENT_PAGE = location.hash.slice(1) || '/';
			// initialize on first load.
			set_language();
			set_layout();
			set_route();
			// hash_change handler
			$(window).on('hashchange',function(){ 
				CURRENT_PAGE = location.hash.slice(1);
				$("#sidebar-menu .selected").removeClass("selected");
				$(this).siblings().removeClass("selected");
				set_route();
			});
		}
		$(document).ready(main);
	</script>
	</body>
</html>
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
			#container { background:#fff; }
			.right { float: right; }
			.selector-language { margin-bottom: 40px; }
			.profile td{ margin:0; padding:0; line-height:80%; }
			.profile-nickname { font-size: 13pt; }
			.profile-score { font-size: 8pt; }
			.profile-panel * { text-align:center; margin:auto; float:middle; }
			.profile-image { border:2px solid #ccc; border-radius:2px; margin-top:5px; margin-right:10px; width:40px; height:40px; background-color:#fff; }
			.footer { background-color:#eee; padding:5px; text-align:center; }
			#break-stat td, #scoreboard td { padding:5px; text-align:center; border-right:0; border-left:0; }
			#break-info * { padding:20px 5px; }
			#break-info td { text-align:left; border-right:0; font-size:12pt; font-weight:bold;}
			#break-info th {border:0; font-size:11pt; width:120px; text-align:right; }
			@media (max-width: 1024px) {
				#sidebar { position:absolute; top:0; height: 62px; background-color:#eee; width:100%; left:0}
				#sidebar > ul { display:inline; }
				#sidebar * { border-radius:0; }
				#sidebar > ul > li { display:inline-block; float:right; padding-top: 13px; }
				#sidebar > ul > li:first-child { float:left;  padding:0;}
				#content { width:100%; }
				#sidebar hr { display:none; }
				#language { margin-top: 30px;}
			}

			#break-stat { margin:0; }
			#scoreboard th {text-align:center; border-right:0; font-size:12pt; }
			#scoreboard {border-right:0; border-left:0; border-top: 0;}

			.counter { float:right; border:1px solid #ccc;  padding:1px 5px; border-radius:5px; background-color: #eee; margin-left: 3px; }
			.new-comer{padding:15px 20px;text-align:center;border:1px solid #d8dee2;border-radius:5px}.auth-form{width:400px;margin:60px auto}.auth-form .password-note{margin:15px 0;text-align:center}.auth-form-header{position:relative;padding:10px 20px;margin:0;color:#fff;text-shadow:0 -1px 0 rgba(0,0,0,0.3);background-color:#829aa8;border:1px solid #768995;border-radius:3px 3px 0 0}.auth-form-header h1{font-size:16px}.auth-form-header h1 a{color:#fff}.auth-form-header .octicon{position:absolute;top:10px;right:20px;color:rgba(0,0,0,0.4);text-shadow:0 1px 0 rgba(255,255,255,0.1)}.auth-form-message{max-height:180px;padding:10px 20px;margin-bottom:20px;overflow-y:scroll;border:1px solid #d8dee2;border-radius:3px}.auth-form-body{padding:20px;font-size:14px;background-color:#fff;border:1px solid #d8dee2;border-radius:3px;}.auth-form-body .input-block{margin-top:5px;margin-bottom:15px}.auth-form-body p{margin:0 0 10px}.two-factor-help{position:relative;padding:10px 10px 10px 36px;margin:60px 0 auto auto;border:1px solid #eaeaea;border-radius:3px}.two-factor-help h4{margin-top:0;margin-bottom:5px}.two-factor-help .octicon-device-mobile{position:absolute;top:10px;left:10px}.two-factor-help .octicon-key{position:absolute;left:10px}.two-factor-help .btn-sm{float:right}.two-factor-help ul{list-style-type:none}.u2f-send-code-spinner{position:relative;bottom:2px;display:none;vertical-align:bottom}.loading .u2f-send-code-spinner{display:inline}.u2f-login-spinner{position:relative;top:2px}.u2f-auth-header{padding-bottom:10px;margin-bottom:20px;border-bottom:1px solid #eaeaea}.u2f-auth-form-body{padding:30px 30px 20px;text-align:center}	.table>thead>tr>td.info,.table>tbody>tr>td.info,.table>tfoot>tr>td.info,.table>thead>tr>th.info,.table>tbody>tr>th.info,.table>tfoot>tr>th.info,.table>thead>tr.info>td,.table>tbody>tr.info>td,.table>tfoot>tr.info>td,.table>thead>tr.info>th,.table>tbody>tr.info>th,.table>tfoot>tr.info>th{background-color:#d9edf7}.table-hover>tbody>tr>td.info:hover,.table-hover>tbody>tr>th.info:hover,.table-hover>tbody>tr.info:hover>td,.table-hover>tbody>tr:hover>.info,.table-hover>tbody>tr.info:hover>th{background-color:#c4e3f3}.table-hover>tbody>tr:hover{background-color:#f5f5f5}
		</style>
	</head>
	<body>
		<div id="container" class="container-xl clearfix px-3 pt-3 pb-4 mt-4">
			<div id="language" class="col-12 selector-language">
			<div class="right">
					<span class="octicon octicon-globe"></span>&nbsp;
					<select id="language-select">
						<option>en</option>
						<option>ko</option>
					</select>
				</div>
			</div>
			<div id="sidebar" class="col-3 float-left pr-3"></div>
			<div id="content" class="col-9 float-left pl-2"></div>
			
		</div>
		<div class="footer container-xl mb-4 ">footer &hearts;</div>
	</div>
	<!-- Loader TBD -->
	<script src="//unpkg.com/jquery@3.2.1/dist/jquery.js"></script>
	<script>
		/* 
			jquery is enough for the only javascript framework..
			This is the remastered code of my old code(or commits) :p
			You may make pull requests for any fixes/improvements.
		*/

		/* Global variables */
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
		var output_intl = function(s){
			// I'm actually considering about adding japanese and chinese too..
			langmap = {
				'logout': {'en': 'Sign Out', 'ko': '로그아웃'},
				'login': {'en': 'Sign In', 'ko': '로그인'},
				'intro': {'en': 'Welcome', 'ko': '환영'},
				'chall': {'en': 'Challenge', 'ko': '문제'},
				'chat': {'en': 'Chat', 'ko': '채팅방'},
				'status': {'en': 'Scoreboard', 'ko': '랭킹'},

				'nickname': {'en': 'Nickname', 'ko': '닉네임'},
				'score': {'en': 'Score', 'ko': '점수'},
				'pt': {'en': 'pt', 'ko': '점'},
				'comment': {'en': 'Comment', 'ko': '소개'}, 
				'last_solved': {'en': 'Last Solved', 'ko': '최근 풀이시간'},

				'stat-player': {'en': 'Scoreboard', 'ko': '순위'},
				'stat-chall': {'en': 'Chall Info', 'ko': '문제 정보'},
				'stat-auth': {'en': 'Solve Log', 'ko': '인증 로그'},
				'stat-fame': {'en': 'Hall of Fame', 'ko': '명예의 전당'},

				'chall-by': {'en': 'Author', 'ko': '제작자'},
				'chall-solver': {'en': 'Solvers', 'ko': '풀은 인원'},
				'chall-player-count': {'en': 'players', 'ko': '명'},
				'chall-solve-date': {'en': 'Solved at', 'ko': '풀은 시간'},

				'chall-auth-check': {'en': 'Checking the flag..', 'ko': '플래그 확인 중 입니다.'},
				'chall-auth-invalid': {'en': 'The format for the flag is invalid. Please try again.', 'ko': '플래그 형식이 비정상적입니다. 다시 입력해주세요'},
				'chall-auth-wrong': {'en': 'Incorrect flag.', 'ko': '불일치한 플래그입니다.'},

				'auth': {'en': 'auth', 'ko': '인증'},
				'auth-nick': {'en': 'Nickname', 'ko': '닉네임'},
				'auth-pass': {'en': 'Password', 'ko': '비밀번호'},
				'auth-remember': {'en': 'Remember nickname', 'ko': '닉네임 기억하기'},
				'auth-forgot': {'en': 'Forgot password?', 'ko': '비밀번호를 잊으셨나요?'},
				'auth-reg-new': {'en': 'New Here?', 'ko': '처음 방문하셨나요?'},
				'auth-reg-create': {'en': 'Create an account', 'ko': '계정을 생성하세요'},
				'auth-loading': {'en': 'Checking...', 'ko': '확인 중...'},
				'auth-wrong': {'en': 'Incorrect Credentials.', 'ko': '계정정보가 일치하지 않습니다.'},
				'auth-login': {'en': 'Sign In', 'ko': '로그인'},

				'profile-score-prefix': {'en': ' with a total of ', 'ko': '등. '},
				'profile-score-suffix': {'en': '', 'ko': ' 보유 중'},
				'profile-clear': {'en': 'Cleared', 'ko': '푼 문제'},
				'profile-break': {'en': 'Breakthrough', 'ko': '먼저 푼 문제'},
				'profile-no-solve-head': {'en': 'No Information', 'ko': '정보 없음'},
				'profile-no-solve-body': {'en': 'This user did not solve any challenges yet.',
					'ko': '이 사용자는 아직 한 문제도 풀지 못하였습니다.'},

				'edit-new-pass': {'en': 'New Password', 'ko': '새 비밀번호'},
				'edit-password-tip': {'en': 'You do not need to write anything unless if you wish to change it.',
					'ko': '비밀번호 변경을 하고싶지 않은 경우 작성하실 필요가 없습니다.'},
				'edit-comment-tip': {'en': 'Introduce a little bit about yourself',
					'ko': '자신에 대해 간단히 소개해주세요.'},
				'edit-submit': {'en': 'Change Info', 'ko': '변경'},
				'edit-success': {'en': 'Change successful.', 'ko': '변경이 완료되었습니다.'},
				'edit-fail': {'en': 'Change failed. Try again later.', 'ko': '변경에 실패하였습니다. 나중에 다시 시도하세요.'},

				'reg-head': {'en': 'Create your wargame account', 'ko': '새 워게임 계정을 생성하세요'},
				'reg-input-email': {'en': 'Email Address', 'ko': '이메일 주소'}, 
				'reg-info-email': {'en': 'You may want to link your email on wechall. We promise not to share your email to anyone.',
					'ko': 'WeChall에 랭킹 등록시 필요합니다. 이 정보는 다른 이에게 제공하지 않습니다.'},
				'reg-info-nickname': {'en': 'This is the idetifier of your account. You need this to log into your account.',
					'ko': '생성하시는 계정의 아이디입니다. 로그인 하실 때 필요합니다.'},
				'reg-info-password': {'en': 'Please try to use a secure password, even if we hash passwords with salts.',
					'ko': '자체적으로 데이터를 암호화하지만, 가급적 안전한 비밀번호를 사용해주세요.'},
				'reg-submit': {'en': 'Create an account', 'ko': '계정 생성하기'},
				'reg-note': {'en': 'Quick Note', 'ko': '참고사항'},
				'reg-note-1': {'en': 'Please contact directly to the administrator for any account related assisstance. If you want to change your password, We will provide you a hash generator and will change the password by hands.',
					'ko': '계정 관련 문의는 관리자에게 직접 문의해주시면 됩니다. 비밀번호 변경에 어려움이 있는 경우, 관리자가 비밀번호를 암호화 해줄 수 있는 페이지를 통해 수동으로 변경해드립니다.'},
				'reg-note-2': {'en': 'Please DO NOT flood or DDoS any challenges for a long period of time. Play nice and be generous to others. Otherwise you will be banned from this website forever.',
					'ko': '오랜 시간동안 문제 서비스에 플로딩 혹은 DDoS를 가하지 마시기 바랍니다. 착하고 자비로운 사람이 됩시다. 이 규칙을 어길시 워게임에서 영구 밴처리 됩니다.'},
				'reg-note-3': {'en': 'The service encrypts password with salts on it. But still, make sure to use secure passwords for your wargame credential. Here, a secure password should be made with the exception of personal passwords and frequently-used passwords.',
					'ko': '비밀번호는 salt가 추가된 상태로 암호화됩니다. 그렇다 하여도, 워게임 계정은 비교적 안전한 비밀번호를 사용해주세요. 여기서 안전함이란 자주쓰는 비밀번호 혹은 개인적인 비밀번호를 사용하라는 의미가 아닙니다.'},
				'reg-deny-nick': {'en':'You cannot use this nickname.', 'ko':'입력하신 닉네임은 사용하실 수 없습니다.'},
				'reg-deny-user': {'en':'You cannot use this email address.', 'ko':'입력하신 이메일은 사용하실 수 없습니다.'},
				'reg-deny-pass': {'en':'You cannot use this password.', 'ko':'입력하신 비밀번호는 사용하실 수 없습니다.'},
				'reg-deny-comment': {'en': 'You cannot use this content.', 'ko': '입력하신 내용은 사용하실 수 없습니다.'},
				'reg-deny-dup-nick': {'en': 'The nickname is already registered',
					'ko': '입력하신 닉네임은 이미 가입되어 있습니다'},
				'reg-deny-dup-mail': {'en': 'The mail address is already registered.',
					'ko': '입력하신 메일 주소는 이미 가입되어 있습니다.'},
				'reg-deny-format-mail': {'en': 'Invalid format for an e-mail address.',  'ko': '이메일 주소가 잘못되었습니다.'},
				'reg-deny-size': {'en': 'Impossible!', 'ko': '불가능 ㄹㅇ루다가'},
				'reg-deny-unknown': {'en': 'An unexpected error has been occured. Please contact administrator for more information',
					'ko': '예상치 못한 오류가 발생하였습니다. 자세한 정보는 관리자에게 문의하시기 바랍니다.'},

				'error-nope': {'en': 'Nope!', 'ko': '응 아니야~'},
				'error-nope-info': {'en': 'The page you are looking for is not found. Better check elsewhere :p', 
								'ko': '접속하신 페이지를 찾을 수 없습니다. 다른 곳을 확인해보세요 :p'},
				'error-auth': {'en': 'You need to sign in to view this page.', 'ko': '이 페이지를 보시려면 로그인 하셔야 합니다.'},
				'error-wtf': {'en': 'You\'re already signed in.', 'ko': '이미 로그인 하신 상태입니다.'},
			}
			return langmap[s][CURRENT_LANG];
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
				$("#output-message").addClass("flash");
				$("#output-message").addClass("flash-error");
				$("#output-message").html(output_intl("chall-auth-invalid"));
                return false;
            }
			$.post("?controller=challenge&action=auth", _input, function(d){
				if(d == true){
					main();
				}else{
					$("#output-message").addClass("flash");
					$("#output-message").addClass("flash-error");
					$("#output-message").html(output_intl("chall-auth-wrong"));
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
					$("#output-message").addClass("flash");
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
					$("#output-message").addClass("flash");
					$("#output-message").addClass("flash-info");
					$("#output-message").html(output_intl("edit-success"));
				}else{
					$("#output-message").addClass("flash");
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

		var load_chall = function(p){
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
				// filter unsolved
				d=d.filter(function(a){
					return a.challenge_solved === false;
				}); */
				console.log(d);
				for(var i=0;i<d.length;i++){
					add_data("#content", '<div class="Box mb-3"><div class="Box-header pt-2 pb-2 Box-header--blue">' +
						'<h3 class="Box-title"><span class="octicon octicon-bug">&nbsp;</span>'+ d[i]['challenge_name'] +' <span class="right">' + d[i]['challenge_score']+ 'pt</span></h3></div>'+
						'<div class="Box-body">' + d[i]['challenge_desc']+
						'</form></div></div>');
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
		var load_user = function(p){
			switch(p){
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
						'<label autocapitalize="off" autofocus="autofocus" for="username">'+output_intl('reg-input-email')+'</label>'+
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
							try{
								for(var j=0;j<_top.length;j++){
									_top_break += '<tr class="info" style="cursor:pointer;" onclick="location.replace(\'#/profile/'+_top[j]['user']+'\')">' +
									'<td>#'+(_top[j]['rank'])+'</td><td>'+_top[j]['user']+'</td><td>'+_top[j]['date']+'</td></tr>';
								}
							}catch(e){ }
							if(!_top_break){
								_top_break = '<tr><td colspan=4><h2 align=center>...</h2></td></tr>';
							}
							add_data("#output-layer", '<div class="Box mb-3"><div class="Box-header pt-2 pb-2">' +
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
			console.log('wtf..?');
			_url = (typeof CURRENT_PAGE === "string" && CURRENT_PAGE !== "") && CURRENT_PAGE.split('/') || ["", ""];
			switch(_url[1]){
				case 'user':
					_d = IS_AUTH && 'logout' || 'login';
					if(_url[2] == 'edit'){ _d = 'edit'; }
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
					console.log(_url);
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
	</script>
	</body>
</html>>
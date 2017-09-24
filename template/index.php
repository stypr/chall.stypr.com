<!doctype html>
	<head>
	<meta charset="utf-8">
		<title>Stereotyped Challenges</title>
		<meta name="viewport" content="initial-scale=1, user-scalable=0">
		<link href="//fonts.googleapis.com/css?family=Raleway:400,300,600" rel="stylesheet" type="text/css">
		<link href="//cdnjs.cloudflare.com/ajax/libs/octicons/4.4.0/font/octicons.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="//unpkg.com/primer-core@6.3.1/build/build.css">
		<style>
			.right { float: right; }
			.selector-language { margin-bottom: 15px; }
		</style>
	</head>
	<body>
		<div id="container" class="container-lg clearfix px-3 mt-4">
			<div class="col-12 selector-language">
				&nbsp;
				<div class="right">
					<select id="language">
						<option>en</option>
						<option>ko</option>
					</select>
				</div>
			</div>
			<div id="sidebar" class="col-3 float-left pr-3"></div>
			<div id="content" class="col-9 float-left pl-2" page-id="default"></div>
		</div>
	</div>
	<!-- Loader TBD -->
	<script src="//unpkg.com/jquery@3.2.1/dist/jquery.js"></script>
	<script>
		/* I'm writing all these on my freetime at the military base.. in the middle of the night!
		Sorry guys, I'm not going to refactor this unless bugs are found. 
		If you can refactor/beautify the code, please provide me a pull request.
		*/
		var IS_AUTH = -1;
		var IS_INIT = true;
		var LANG = null;

		/* helper functions */
		var expand = function(t, d){ $(t).html($(t).html() + d); }
		var reload = function(t, d){ $(t).html(d); }
		var output = function(s){
			// I'm actually considering about adding japanese and chinese too
			langmap = {
				'logout': {'en': 'logout', 'ko': '로그아웃'},
				'login': {'en': 'Sign In', 'ko': '로그인'},
				'home': {'en': 'Home', 'ko': '메인'},
				'chall': {'en': 'Challenge', 'ko': '문제'},
				'chat': {'en': 'Chat', 'ko': '채팅방'},
				'status': {'en': 'Status', 'ko': '현황판'},
				'stat-player': {'en': 'Scoreboard', 'ko': '순위'},
				'stat-chall': {'en': 'Chall Info', 'ko': '문제 정보'},
				'stat-auth': {'en': 'Solve Log', 'ko': '인증 로그'},
				'stat-pwner': {'en': 'Hall of Fame', 'ko': '명예의 전당'},
				
			}
			return langmap[s][LANG];
		}
		var check_auth = function(){
			$.get('?controller=user&action=check', function(d){
				res = $.parseJSON(d);
				if(res != IS_AUTH){
					res = IS_AUTH;
					refresh_page();
				}
			});
		}

		
		/* subroutines of pages*/
		function get_status(type){
			switch(type){
				case 'scoreboard':
				
					break;
			}
			//$.get('?controller=');
		}
		/* contents per page */
		function load_page(page_id){
			$('#content').attr('page-id', page_id);
			switch(page_id){
				case "status":
					reload("#content", '<div class="clearfix"><nav class="tabnav-tabs" style="border-bottom:0;">');
					expand("#content", '<a href="#" class="tabnav-tab selected" sub-id="player">' + output('stat-player') + '</a>');
					expand("#content", '<a href="#" class="tabnav-tab" sub-id="chall">' + output('stat-chall') + '</a>');
					expand("#content", '<a href="#" class="tabnav-tab" sub-id="auth">' + output('stat-auth') + '</a>');
					expand("#content", '<a href="#" class="tabnav-tab" sub-id="pwner">' + output('stat-pwner') + '</a>');
					expand("#content", '</nav></div></div><hr style="margin:0;">');
					
					get_status();
					break;
				default:
					reload("#content", "empty page");
					break;
			}
		}

		/* basic features to make the layout running.. */
		function refresh_language(){
			lang_local = localStorage.getItem('lang');
			if(LANG == null){
				if(lang_local == 'null' || !lang_local){
					console.log('hits here');
					LANG = 'en';
					localStorage.setItem('lang', LANG);	
				}else{
					LANG = lang_local;
				}
			}else{
				localStorage.setItem('lang', LANG);
			}
			$("#language").val(LANG);
			// add events..
			$("#language").unbind('change');
			$("#language").change(function(){
				LANG = $("#language").val();
				refresh_page();
			});
		}
		function refresh_sidebar(){
			current_page = $('#content').attr('page-id');
			// expand menu list
			reload("#sidebar", '');
			expand("#sidebar" , '<ul class="filter-list" id="menu"></ul>');
			var _menu_sub = '<li><a href="#" class="filter-item" page-id="__PAGE__">__NAME__<span class="octicon octicon-light-bulb right"></span></a>';
			if(IS_AUTH == true){
				expand("#menu", _menu_sub.replace('__NAME__', output('logout')).replace('__PAGE__', 'logout'));
			}else{
				expand("#menu", _menu_sub.replace('__NAME__',  output('login')).replace('__PAGE__', 'login'));
			}

			expand("#menu", '<hr>');
			expand("#menu", _menu_sub.replace('__NAME__',  output('home')).replace('__PAGE__', 'default'));
			expand("#menu", _menu_sub.replace('__NAME__',  output('status')).replace('__PAGE__', 'status'));
			if(IS_AUTH == true){
				expand("#menu", _menu_sub.replace('__NAME__',  output('chall')).replace('__PAGE__', 'chall'));
				expand("#menu", _menu_sub.replace('__NAME__',  output('chat')).replace('__PAGE__', 'chat'));
			}

			$("#menu > li>a[page-id='"+current_page+"']").addClass("selected");
			// on menu click
			$("#language").unbind('click');
			$("#menu > li").click(function(){
				$("#menu .selected").removeClass("selected");
				$(this).siblings().removeClass("selected");
				$(this).children(':first').addClass("selected");
				
				load_page($(this).children(':first').attr("page-id"));
			});
		}
		function refresh_content(){
			current_page = $('#content').attr('page-id');
			load_page(current_page);
		}
		function refresh_page(){
			// refresh order: language -> sidebar -> content
			refresh_language();
			refresh_sidebar();
			refresh_content();
		}

		/* on initial load.. */
		$(document).ready(function(){
			check_auth();
			IS_INIT = false;
		});
	</script>
	</body>
</html>
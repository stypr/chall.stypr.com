var output = function(s) {
    // I'm actually considering about adding japanese and chinese too..
    langmap = {

        // language specific monospace fonts
        'FONT': {
            'en': 'Inconsolata',
            'ko': 'Nanum Gothic Coding'
        },

        // intro page, html.
        'INTRO': {
            'en': '<h1>Stereotyped Challenges</h1>' +
                '<h2>Upgrade your web hacking techniques today!</h2><hr>' +
                '<p class="lead">Hackers tend to have stereotypes about <A href="https://en.wikipedia.org/wiki/Wargame_(hacking)">wargames</a>; Many consider wargames as basic learning materials for new-comers of cyber security.<br><br>' +
                'Well, it\'s not completely true. I personally think wargames are supposed to be and meant to be very challenging, regardless of how they\'re good at it.' +
                '<br>So, here it is. This wargame is a little bit different to break your typical stereotypes.<br><br>' +
                'This wargame is intended for offensive security experts who are willing to overcome situations of getting stuck in breaking into web services or web-based solutions.<br><br>' +
                'At the end of the day, you will be able to learn and utilize a lot of advanced web-based exploitation techniques and improve your offensive skills.<br><br>' +
                'The rules of this wargame is very straightforward. Make sure that other users can enjoy the wargame. DO NOT bruteforce challenges. This wargame is non-commercial and I pay server expenses from my own pocket.<br><br>Please DO NOT post your solutions on the internet. Solving challenges would become worthless if solutions are posted. This is because people improve their skills a lot more once they try out challenges by their own.<br><br>Advertise this wargame if it helped you to break into more stuff. ' +
                'Please contact me if you would like to release your challenges here. Make sure it\'s difficult enough!<br><br>' +
                '<hr><center>Recommended wargames<br><a href="http://chall.tasteless.eu">Tasteless</a>(<a href="#/profile/nurfed">nurfed</a>) &middot; <a href="http://suninatas.com">Suninatas</a> &middot; <a href="http://wargame.kr/">wargame.kr</a> &middot; <a href="http://webhacking.kr">webhacking.kr</a> &middot; <a href="http://wechall.com">WeChall</a> &middot; <a href="https://los.eagle-jump.org/">Lord of SQLi</a>(<a href="#/profile/rubiya">rubiya</a>) &middot; <a href="http://pwnable.kr">pwnable.kr</a>(<a href="#/profile/daehee">daehee</a>) &middot; <a href="http://canhack.me/">CanHackMe</a>(<a href="#/profile/Safflower">Safflower</a>)',
            'ko': '<h1>Stereotyped Challenges</h1>' +
                '<h2>오늘 당신의 웹 해킹 능력을 업그레이드하세요!</h2><hr>' +
                '<p class="lead">수 많은 해커들은 <a href="https://en.wikipedia.org/wiki/Wargame_(hacking)">워게임</a>에 관한 고정관념을 가지고 있습니다. 대부분 워게임을 사이버 보안에 입문하는 자들을 위한 기본적인 교육 도구라고 생각합니다.<br><br>' +
                '하지만 그 주장은 조금 잘못됬다고 생각합니다. 개인적인 생각으로 워게임은 실력의 차를 떠나 푸는 사람 입장에서 어려워야한다고 생각합니다.' +
                '<br>그리하여 이 워게임이 탄생하였습니다. 이 워게임은 일반적인 고정관념을 깨기 위해 만들어진 조금 다른 워게임입니다.<br><br>' +
                '이 워게임은 공격형 해킹을 전문적으로 하는 해커들이, 웹 서비스나 웹 기반 솔루션를 뚫는데 있어서 어려울 수 있는 상황을 미리 극복 해낼 수 있도록 도우기 위해 만들어졌습니다.<br><br>' +
                '문제를 다 푸실때 쯤이면 심도있는 웹 기반 공격 기술들에 대해 배우실 수 있으며, 공격형 해킹 능력이 향상 될 것 입니다.<br><br>' +
                '이 워게임의 규칙은 매우 간단합니다. 다른 이들이 이 워게임을 즐길 수 있도록 배려해주시기 바랍니다. 서비스에 과도한 브루트포싱은 자제 부탁드립니다. 서버는 제 사비로 운영되고 있습니다.<br><br>' +
                '그리고 제발 답공유를 하지마시기 바랍니다. 만약 답이나 풀이가 인터넷에 돌아다닌다면 처음부터 문제를 푸는 의미가 없어집니다. 문제를 푸는 과정에서도 해커는 발전하고 실력을 쌓기 때문입니다. 만약 이 워게임을 통해 해킹을 하기 쉬워졌다면 홍보해주시면 감사하겠습니다.<br><br>' +
                '제 워게임에 문제를 제출하고 싶으신 의향이 있으신 분은 저에게 연락을 주시면 직접 검토 후 올리겠습니다. 꼭 어려운 문제여야만 합니다!<br>' +
                '<hr><center>추천하는 워게임들<br><a href="http://chall.tasteless.eu">Tasteless</a>(<a href="#/profile/nurfed">nurfed</a>) &middot; <a href="http://suninatas.com">Suninatas</a> &middot; <a href="http://wargame.kr/">wargame.kr</a> &middot; <a href="http://webhacking.kr">webhacking.kr</a> &middot; <a href="http://wechall.com">WeChall</a> &middot; <a href="https://los.eagle-jump.org/">Lord of SQLi</a>(<a href="#/profile/rubiya">rubiya</a>) &middot; <a href="http://pwnable.kr">pwnable.kr</a>(<a href="#/profile/daehee">daehee</a>) &middot; <a href="http://canhack.me/">CanHackMe</a>(<a href="#/profile/Safflower">Safflower</a>)</p>'
        },

		// message for default layout
        'logout': {
            'en': 'Sign Out',
            'ko': '로그아웃'
        },
        'login': {
            'en': 'Sign In',
            'ko': '로그인'
        },
        'intro': {
            'en': 'Welcome',
            'ko': '환영'
        },
        'chall': {
            'en': 'Challenge',
            'ko': '문제'
        },
        'chat': {
            'en': 'Chat',
            'ko': '채팅방'
        },
        'status': {
            'en': 'Scoreboard',
            'ko': '랭킹'
        },
		'netstat': {
			'en': 'Network Status',
			'ko': '네트워크 현황',
		},
        'nickname': {
            'en': 'Nickname',
            'ko': '닉네임'
        },
        'score': {
            'en': 'Score',
            'ko': '점수'
        },
        'pt': {
            'en': 'pt',
            'ko': '점'
        },
        'comment': {
            'en': 'Comment',
            'ko': '소개'
        },
        'last_solved': {
            'en': 'Last Solved',
            'ko': '최근 풀이시간'
        },

        'stat-player': {
            'en': 'Scoreboard',
            'ko': '순위'
        },
        'stat-chall': {
            'en': 'Chall Info',
            'ko': '문제 정보'
        },
        'stat-auth': {
            'en': 'Solve Log',
            'ko': '인증 로그'
        },
        'stat-fame': {
            'en': 'Hall of Fame',
            'ko': '명예의 전당'
        },

        'player-total-msg': {
            'en': ' players are currently registered.',
            'ko': '명의 플레이어가 현재 가입되어 있습니다.'
        },

        'chall-by': {
            'en': 'Author',
            'ko': '제작자'
        },
        'chall-solver': {
            'en': 'Solvers',
            'ko': '풀은 인원'
        },
        'chall-player-count': {
            'en': 'players',
            'ko': '명'
        },
        'chall-solve-date': {
            'en': 'Solved at',
            'ko': '풀은 시간'
        },

        'chall-auth-check': {
            'en': 'Checking the flag..',
            'ko': '플래그 확인 중 입니다.'
        },
        'chall-auth-invalid': {
            'en': 'The format for the flag is invalid. Please try again.',
            'ko': '플래그 형식이 비정상적입니다. 다시 입력해주세요'
        },
        'chall-auth-wrong': {
            'en': 'Incorrect flag.',
            'ko': '불일치한 플래그입니다.'
        },
        'chall-auth-already-solved': {
            'en': 'You already solved this challenge',
            'ko': '이미 이 문제를 푸셨습니다!'
        },

        'auth': {
            'en': 'auth',
            'ko': '인증'
        },
        'auth-nick': {
            'en': 'Nickname',
            'ko': '닉네임'
        },
        'auth-pass': {
            'en': 'Password',
            'ko': '비밀번호'
        },
        'auth-remember': {
            'en': 'Remember nickname',
            'ko': '닉네임 기억하기'
        },
        'auth-forgot': {
            'en': 'Forgot password?',
            'ko': '비밀번호를 잊으셨나요?'
        },
        'auth-reg-new': {
            'en': 'New Here?',
            'ko': '처음 방문하셨나요?'
        },
        'auth-reg-create': {
            'en': 'Create an account',
            'ko': '계정을 생성하세요'
        },
        'auth-loading': {
            'en': 'Checking...',
            'ko': '확인 중...'
        },
        'auth-wrong': {
            'en': 'Incorrect Credentials.',
            'ko': '계정정보가 일치하지 않습니다.'
        },
        'auth-login': {
            'en': 'Sign In',
            'ko': '로그인'
        },

        'profile-score-prefix': {
            'en': ' with a total of ',
            'ko': '등. '
        },
        'profile-score-suffix': {
            'en': '',
            'ko': ' 보유 중'
        },
        'profile-clear': {
            'en': 'Cleared',
            'ko': '푼 문제'
        },
        'profile-break': {
            'en': 'Breakthrough',
            'ko': '먼저 푼 문제'
        },
        'profile-no-solve-head': {
            'en': 'No Information',
            'ko': '정보 없음'
        },
        'profile-no-solve-body': {
            'en': 'This user did not solve any challenges yet.',
            'ko': '이 사용자는 아직 한 문제도 풀지 못하였습니다.'
        },

        'chat-rule': {
            'en': 'You may query(DM) stypr on #stypr to ask challenge-related questions. You are allowed to mail me on demand.',
            'ko': '문제에 관련한 문의사항은 #stypr 채널에 있는 stypr에게 query(DM) 요청 하시면 됩니다. 필요한 경우 메일을 보내주시면 됩니다.'
        },

        'edit-new-pass': {
            'en': 'New Password',
            'ko': '새 비밀번호'
        },
        'edit-password-tip': {
            'en': 'You do not need to write anything unless if you wish to change it.',
            'ko': '비밀번호 변경을 하고싶지 않은 경우 작성하실 필요가 없습니다.'
        },
        'edit-comment-tip': {
            'en': 'Introduce a little bit about yourself',
            'ko': '자신에 대해 간단히 소개해주세요.'
        },
        'edit-submit': {
            'en': 'Change Info',
            'ko': '변경'
        },
        'edit-success': {
            'en': 'Change successful.',
            'ko': '변경이 완료되었습니다.'
        },
        'edit-fail': {
            'en': 'Change failed. Try again later.',
            'ko': '변경에 실패하였습니다. 나중에 다시 시도하세요.'
        },

        'find-send-loading': {
            'en': 'Sending your request...',
            'ko': '확인 중 입니다...'
        },
        'find-send-tip': {
            'en': 'You need to confirm your mail to continue the process.',
            'ko': '귀하의 이메일로 비밀번호 복구 관련 메일이 전송됩니다.'
        },
        'find-send-submit': {
            'en': 'Send a request',
            'ko': '요청 보내기'
        },
        'find-send-done': {
            'en': 'Please check your inbox to continue the process.',
            'ko': '귀하 메일 계정으로 확인 메일을 보냈습니다.'
        },
        'find-send-exceed': {
            'en': 'Your limit has been exceeded. Please contact administrator for more information.',
            'ko': '요청 제한이 초과되었습니다. 자세한 내용은 관리자에게 문의하시기 바랍니다.'
        },
        'find-send-fail': {
            'en': 'There was an error during your request. Please try again later.',
            'ko': '요청하신 내용을 실행하는 도중 에러가 발생하였습니다. 잠시 후 다시 시도해주세요.'
        },
        'find-send-nope': {
            'en': 'The mail you requested does not exist.',
            'ko': '요청하신 이메일 주소는 존재하지 않습니다.'
        },
        'find-new-pw': {
            'en': 'New Password',
            'ko': '새 비밀번호'
        },
        'find-new-submit': {
            'en': 'Change the password',
            'ko': '비밀번호 변경하기'
        },
        'find-new-fail': {
            'en': 'Change failed. Please contact administrator for more information.',
            'ko': '변경에 실패하였습니다. 자세한 내용은 관리자에게 문의하시기 바랍니다.'
        },

        'reg-head': {
            'en': 'Create your wargame account',
            'ko': '새 워게임 계정을 생성하세요'
        },
        'reg-input-email': {
            'en': 'Email Address',
            'ko': '이메일 주소'
        },
        'reg-info-email': {
            'en': 'You may want to link your email on wechall. We promise not to share your email to anyone.',
            'ko': 'WeChall에 랭킹 등록시 필요합니다. 이 정보는 다른 이에게 제공하지 않습니다.'
        },
        'reg-info-nickname': {
            'en': 'This is the idetifier of your account. You need this to log into your account.',
            'ko': '생성하시는 계정의 아이디입니다. 로그인 하실 때 필요합니다.'
        },
        'reg-info-password': {
            'en': 'Please try to use a secure password, even if we hash passwords with salts.',
            'ko': '자체적으로 데이터를 암호화하지만, 가급적 안전한 비밀번호를 사용해주세요.'
        },
        'reg-submit': {
            'en': 'Create an account',
            'ko': '계정 생성하기'
        },
        'reg-note': {
            'en': 'Quick Note',
            'ko': '참고사항'
        },
        'reg-note-1': {
            'en': 'Please contact directly to the administrator for any account related assisstance. If you want to change your password, We will provide you a hash generator and will change the password by hands.',
            'ko': '계정 관련 문의는 관리자에게 직접 문의해주시면 됩니다. 비밀번호 변경에 어려움이 있는 경우, 관리자가 비밀번호를 암호화 해줄 수 있는 페이지를 통해 수동으로 변경해드립니다.'
        },
        'reg-note-2': {
            'en': 'Please DO NOT flood or DDoS any challenges for a long period of time. Play nice and be generous to others. Otherwise you will be banned from this website forever.',
            'ko': '오랜 시간동안 문제 서비스에 플로딩 혹은 DDoS를 가하지 마시기 바랍니다. 착하고 자비로운 사람이 됩시다. 이 규칙을 어길시 워게임에서 영구 밴처리 됩니다.'
        },
        'reg-note-3': {
            'en': 'The service encrypts password with salts on it. But still, make sure to use secure passwords for your wargame credential. Here, a secure password should be made with the exception of personal passwords and frequently-used passwords.',
            'ko': '비밀번호는 salt가 추가된 상태로 암호화됩니다. 그렇다 하여도, 워게임 계정은 비교적 안전한 비밀번호를 사용해주세요. 여기서 안전함이란 자주쓰는 비밀번호 혹은 개인적인 비밀번호를 사용하라는 의미가 아닙니다.'
        },
        'reg-deny-nick': {
            'en': 'You cannot use this nickname.',
            'ko': '입력하신 닉네임은 사용하실 수 없습니다.'
        },
        'reg-deny-user': {
            'en': 'You cannot use this email address.',
            'ko': '입력하신 이메일은 사용하실 수 없습니다.'
        },
        'reg-deny-pass': {
            'en': 'You cannot use this password.',
            'ko': '입력하신 비밀번호는 사용하실 수 없습니다.'
        },
        'reg-deny-comment': {
            'en': 'You cannot use this content.',
            'ko': '입력하신 내용은 사용하실 수 없습니다.'
        },
        'reg-deny-dup-nick': {
            'en': 'The nickname is already registered',
            'ko': '입력하신 닉네임은 이미 가입되어 있습니다'
        },
        'reg-deny-dup-mail': {
            'en': 'The mail address is already registered.',
            'ko': '입력하신 메일 주소는 이미 가입되어 있습니다.'
        },
        'reg-deny-format-mail': {
            'en': 'Invalid format for an e-mail address.',
            'ko': '이메일 주소가 잘못되었습니다.'
        },
        'reg-deny-size': {
            'en': 'Impossible!',
            'ko': '불가능 ㄹㅇ루다가'
        },
        'reg-deny-unknown': {
            'en': 'An unexpected error has been occured. Please contact administrator for more information.',
            'ko': '예상치 못한 오류가 발생하였습니다. 자세한 정보는 관리자에게 문의하시기 바랍니다.'
        },

       'error-nope': {
            'en': 'Nope!',
            'ko': '응 아니야~'
        },
        'error-nope-info': {
            'en': 'The page you are looking for is not found. Better check elsewhere :p',
            'ko': '접속하신 페이지를 찾을 수 없습니다. 다른 곳을 확인해보세요 :p'
        },
        'error-auth': {
            'en': 'You need to sign in to view this page.',
            'ko': '이 페이지를 보시려면 로그인 하셔야 합니다.'
        },
        'error-wtf': {
            'en': 'You\'re already signed in.',
            'ko': '이미 로그인 하신 상태입니다.'
        },
    }
    return langmap[s][CURRENT_LANG];
}

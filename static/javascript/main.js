/* static/javascript/main.js
jquery is <3 for now, but I should make my own framework next time.. */
// Global Variables //
var IRC_CHANNEL = "stypr";
var CURRENT_PAGE = null;
var CURRENT_LANG = null;
var CURRENT_USER = null;
var IS_AUTH = false;
var CHECK_REGEX = "^[a-zA-Z0-9-_!@$.%^&*()가-힣]";

// Global Function //

var set_html = function (t, d, n) {
    if (!d) d = '';
    if (n) {
        $(t).html(d);
    } else {
        $(t).append(d);
    }
}

var check_string = function (str, min, max) {
    if (!min) min = 5;
    if (!max) max = 30;
    var _regexp = CHECK_REGEX + '{' + min + ',' + max + '}$';
    var _check = new RegExp(_regexp).test(str);
    return _check;
}

var set_error = function (t) {
    // this does not follow the HTTP standard, please don't judge me.
    switch (t) {
    case 418: // already-authenticated
        set_html("#content",
            "<div class='flash flash-warning'><h5>" + output("error-wtf") + "</h5></div><br>" +
            "<img src='./static/image/418.png' width=100%>", true);
        break;
    case 403: // unauthorized
        set_html("#content",
            "<div class='flash flash-error'><h5>" + output("error-auth") + "</h5></div><br>" +
            "<img src='./static/image/404.jpg' width=100%>", true);
        break;
    case 404: // not found
    default:
        set_html("#content",
            "<div class='flash flash-error'><h4>" + output("error-nope") + "</h4>" +
            output("error-nope-info") + "</div><br>" +
            "<img src='./static/image/403.png' width=100%>", true);
        break;
    }
    toggle_load();
}

function redirect(url) {
    toggle_load(false);
    $(window).unbind('hashchange');
    window.location.hash = '#' + url;
    load_profile();
}

// Action //

function act_chall_auth() {
    _input = {
        'flag': $("#flag").val()
    }
    $("#output-message").removeClass("flash-error");
    $("#output-message").addClass("flash-info");
    $("#output-message").addClass("flash");
    $("#output-message").html(output("chall-auth-check"));
    var check_flag = new RegExp("^[a-zA-Z0-9-_:+!@#$.%^&*(){}:\/.\ <>가-힣]{0,100}$").test(_input['flag']);
    // Validation Check
    if (!check_flag) {
        $("#output-message").addClass("flash-error");
        $("#output-message").html(output("chall-auth-invalid"));
        return false;
    }
    $.post("/challenge/auth", _input, function (d) {
        switch (d) {
        case 'success':
            redirect('/chall');
            break;
        case 'already-solved':
            $("#output-message").addClass("flash-error");
            $("#output-message").html(output("chall-auth-already-solved"));
            break;
        case 'nope':
        default:
            $("#output-message").addClass("flash-error");
            $("#output-message").html(output("chall-auth-wrong"));
            break;
        }
    });
    return false;
}

function act_user_recover(recovery_code) {
    $("#output-message").removeClass("flash-error");
    $("#output-message").addClass("flash-info");
    $("#output-message").addClass("flash");
    $("#output-message").html(output("find-send-loading"));

    _input = {
        'recovery_code': recovery_code,
        'password': $("#password").val()
    }

    if (!check_string(_input['password'], 4, 100)) {
        $('#output-message').html(output('reg-deny-pass') + '<br>' +
            '<pre>RegExp: ' + CHECK_REGEX + '{4, 100}$</pre>');
        return false;
    }
    $.post("/user/recover", _input, function (d) {
        if (d == true) {
            redirect('/user/login');
        } else {
            $("#output-message").addClass("flash-error");
            $("#output-message").html(output("find-new-fail"));
        }
    });
    return false;
}

function act_user_find() {
    _input = {
        'username': $("#username").val()
    }

    $("#output-message").removeClass("flash-error");
    $("#output-message").addClass("flash-info");
    $("#output-message").addClass("flash");
    $("#output-message").html(output("find-send-loading"));

    $.post("/user/find", _input, function (d) {
        switch (d) {
        case "done":
            $("#output-message").html(output("find-send-done"));
            break;
        case "exceed":
            $("#output-message").addClass("flash-error");
            $("#output-message").html(output("find-send-exceed"));
            break;
        case "fail":
            $("#output-message").addClass("flash-error");
            $("#output-message").html(output("find-send-fail"));
            break;
        case "nope":
            $("#output-message").addClass("flash-error");
            $("#output-message").html(output("find-send-nope"));
            break;
        default: // unknown
            $("#output-message").addClass("flash-error");
            $("#output-message").html(output("find-send-fail"));
            break;
        }
    });
    return false;
}

function act_user_login() {
    $("#output-message").removeClass("flash-error");
    $("#output-message").addClass("flash-info");
    $("#output-message").addClass("flash");
    $("#output-message").html(output("auth-loading"));

    _input = {
        'nickname': $("#nickname").val(),
        'password': $("#password").val()
    };
    $.post("/user/login", _input, function (d) {
        if (d == true) {
            if ($("#remember-nick").prop('checked')) {
                localStorage.setItem('current_nick', _input['nickname']);
            } else {
                localStorage.setItem('current_nick', null);
            }

            redirect('/');
        } else {
            $("#output-message").addClass("flash-error");
            $("#output-message").html(output("auth-wrong"));
        }
    });
    return false;
}

function act_user_register() {
    // user register event
    $("#output-message").removeClass("flash-error");
    $("#output-message").addClass("flash-info");
    $("#output-message").addClass("flash");
    $("#output-message").html(output("auth-loading"));
    _input = {
        'username': $("#username").val(),
        'nickname': $("#nickname").val(),
        'password': $("#password").val()
    };

    if (!check_string(_input['username'], 5, 100)) {
        $("#output-message").addClass("flash-error");
        $('#output-message').html(output('reg-deny-user') + '<br>' +
            '<pre>RegExp: ' + CHECK_REGEX + '{8, 100}$</pre>');
        return false;
    }
    if (!check_string(_input['nickname'], 3, 20)) {
        $('#output-message').html(output('reg-deny-nick') + '<br>' +
            '<pre>RegExp: ' + CHECK_REGEX + '{3, 20}$</pre>');
        return false;
    }
    if (!check_string(_input['password'], 4, 100)) {
        $('#output-message').html(output('reg-deny-pass') + '<br>' +
            '<pre>RegExp: ' + CHECK_REGEX + '{4, 100}$</pre>');
        return false;
    }
    $.post("/user/register", _input, function (d) {
        switch (d) {
        case "duplicate_nick":
            $('#output-message').html(output('reg-deny-dup-nick'));
            return false;
        case "duplicate_mail":
            $('#output-message').html(output('reg-deny-dup-mail'));
            return false;
        case "email_format":
            $('#output-message').html(output('reg-deny-format-mail'));
            return false;
        case "size": // error by length
            $('#output-message').html(output('reg-deny-size'));
            return false;
        case "true":
            // get back to login on successful.
            redirect('/user/login');
            return false;
        default:
            $('#output-message').html(output('reg-deny-unknown'));
            return false;
        }
    });
    return false;
}

function act_user_edit() {
    // user auth event
    $("#output-message").removeClass("flash-error");
    $("#output-message").addClass("flash-info");
    $("#output-message").addClass("flash");
    $("#output-message").html(output("auth-loading"));
    _input = {
        'password': $("#password").val(),
        'comment': $("#comment").val()
    };
    if (_input['password']) {
        if (!check_string(_input['password'], 4, 100)) {
            $('#output-message').html(output('reg-deny-pass') + '<br>' +
                '<pre>RegExp: ' + CHECK_REGEX + '{4, 100}$</pre>');
            return false;
        }
<<<<<<< HEAD
        if (!(new RegExp("^[a-zA-Z0-9-_:+!@#$.%^&*(){}:\/.\ <>가-힣]{0,100}$").test(_input['comment']))) {
=======
		pass_check = "^[a-zA-Z0-9-_:+!@#$.%^&*(){}:\/.\ <>가-힣]{0,100}$";
        if (!(new RegExp(pass_check).test(_input['comment']))) {
>>>>>>> 52d3c88 (Hotfix: syntax error caused by text-wrapping)
            $('#output-message').html(output('reg-deny-comment') + '<br>' +
                '<pre>RegExp: ' + CHECK_REGEX + '{0,50}$</pre>');
            return false;
        }
    }
    $.post("/user/edit", _input, function (d) {
        if (d == true) {
            redirect('/user/edit');
        } else {
            $("#output-message").addClass("flash-error");
            $("#output-message").html(output("edit-fail"));
        }
    });
    return false;
}



// View //

function view_intro() {
    set_html("#content", output("INTRO"), true);
    toggle_load();
}

function view_status(path) {
    page_type = path[1];
    if (!page_type) page_type = 'player';
    // #content -> #content-tabs, #output-layer
    set_html("#content",
        "<div class='tabnav'><nav class='tabnav-tabs' id='content-tabs'></nav></div>" +
        "<div id='output-layer'></div>", true);

    // #content-tabs -> 4 * <a>
    set_html("#content-tabs",
        '<a href="#/status/player" class="tabnav-tab" sub-id="player">' + output('stat-player') + '</a>' +
        '<a href="#/status/chall" class="tabnav-tab" sub-id="chall">' + output('stat-chall') + '</a>' +
        '<a href="#/status/auth" class="tabnav-tab" sub-id="auth">' + output('stat-auth') + '</a>' +
        '<a href="#/status/fame" class="tabnav-tab" sub-id="fame">' + output('stat-fame') + '</a>');
    $(".tabnav-tab[sub-id='" + page_type + "']").addClass("selected");

    switch (page_type) {

    case "fame":
        set_html("#output-layer",
            "Click <a href='https://github.com/stypr/chall.stypr.com#vulnerability-reports'>here</a>" +
            " for the detailed information.", true);
        toggle_load();
        break;

    case "auth":
        $.get('/status/auth', function (d) {
            // #output-layer -> table -> tbody #log-list
            set_html("#output-layer",
                '<table class="data-table table-hover" id="scoreboard">' +
                '<thead><tr>' +
                '<th align=center>#</th><th align=center>' + output('nickname') + '</th>' +
                '<th align=center>' + output('chall') + '</th>' +
                '<th align=center>' + output('chall-solve-date') + '</th>' +
                '</tr></thead><tbody id="log-list"></tbody></table', true);
            for (i = 0; i < d.length; i++) {
                auth_log = d[i];
                // #loglist -> tr -> td
                set_html("#log-list",
                    '<tr class="info" ' +
                    'onclick="location.replace(\'#/profile/' + auth_log['nick'] + '\')">' +
                    '<td>' + auth_log['no'] + '</td>' +
                    '<td>' + auth_log['nick'] + '</td>' +
                    '<td>' + auth_log['chall'] + '</td>' +
                    '<td>' + auth_log['date'] + '</td>' +
                    '</tr>');
            }
            toggle_load();
        });
        break;

    case "chall":
        $.get('/status/challenge', function (d) {
            for (i = 0; i < d.length; i++) {
                curr_chall = d[i];
                curr_chall_top = curr_chall['break'];

                // Get top3 breakthru
                top_info = '';
                try {
                    for (j = 0; j < curr_chall_top.length; j++) {
                        top_info += '<tr class="info" onclick="location.replace(\'#/profile/' + curr_chall_top[j]['user'] + '\')">' +
                            '<td>#' + (curr_chall_top[j]['rank']) + '</td>' +
                            '<td>' + curr_chall_top[j]['user'] + '</td>' +
                            '<td>' + curr_chall_top[j]['date'] + '</td></tr>';
                    }
                } catch (e) {}

                // if solved by none, mark the box
                top_color = '';
                if (!top_info) {
                    top_color = 'Box-header--red'; // additional class
                    top_info = '<tr><td colspan=4><h2 align=center>PWN ME IF YOU CAN</h2></td></tr>';
                }

                // set html
                set_html("#output-layer",
                    '<div class="Box mb-3"><div class="Box-header ' + top_color + ' pt-2 pb-2">' +
                    '<h3 class="Box-title">' + curr_chall['name'] + ' <span class="right">' + curr_chall['score'] + output('pt') + '</span></h3></div>' +
                    '<div class="Box-body">' +

                    '<table class="data-table mt-0" id="break-info">' +
                    '<th>' + output('chall-by') + '</th><td>' + curr_chall['author'] + '</td>' +
                    '<th>' + output('chall-solver') + '</th><td>' + curr_chall['solver'] + ' ' +
                    output('chall-player-count') + '</td><th>' + output('last_solved') + '</th> ' +
                    '<td>' + curr_chall['last-solved'] + '</td></tr></table>' +
                    // breakthrough output
                    '<table class="data-table table-hover mt-2" id="break-stat"><tr>' +
                    '<td width=8>&nbsp;<font color=red><span class="octicon octicon-flame"></span></font></td>' +
                    '<td>' + output('nickname') + '</td><td>' + output('chall-solve-date') + '</td></tr>' +
                    top_info +
                    '</table>');
            }
            toggle_load();
        });
        break;

    case "player":
    case "":
        $.get('/status/scoreboard', function (d) {
            set_html("#output-layer",
                '<table class="data-table table-hover" id="scoreboard">' +
                '<thead><tr>' +
                '<th align=center></th><th align=center>' + output('nickname') + '</th>' +
                '<th align=center>' + output('score') + '</th>' +
                '<th align=center>&nbsp;<font color=red><span class="octicon octicon-flame"></span></font></th>' +
                '<th align=center>' + output('comment') + '</th>' +
                '<th align=center>' + output('last_solved') + '</th>' +
                '</tr></thead><tbody id="ranker-list"></tbody></table>', true);

            is_ranker = false; // check if the current user is ranker
            ranker = d['ranker'];
            for (i = 0; i < ranker.length; i++) {
                _player = ranker[i];
                // Prettify comments
                _player['comment'] = _player['comment'] ? _player['comment'] : '';
                if (_player['comment'].length > 30) {
                    _player['comment'] = _player['comment'].substring(0, 30) + " ...";
                }
                // Top 3 players should get the crown rank
                _rank = i < 3 && "&#9813;" || i + 1;
                // Give star rank if current user in ranker
                try {
                    if (CURRENT_USER['nick'] == _player['nickname']) {
                        _rank = '&#9733;';
                        is_ranker = true;
                    }
                } catch (e) {}
                set_html("#scoreboard",
                    '<tr class="info" onclick="location.replace(\'#/profile/' + _player['nickname'] + '\')">' +
                    '<td>' + _rank + '</td><td>' + _player['nickname'] + '</td>' +
                    '<td>' + _player['score'] + '</td>' +
                    '<td>' + _player['break_count'] + '</td>' +
                    '<td>' + _player['comment'] + '</td><td>' + _player['last_solved'] + '</td></tr>');
            }

            // List current user if not listed
            if (is_ranker == false && IS_AUTH == true) {
                set_html("#scoreboard",
                    '<tr class="info" onclick="location.replace(\'#/profile/' + CURRENT_USER['nick'] + '\')">' +
                    '<td>' + CURRENT_USER['rank'] + '</td><td>' + CURRENT_USER['nick'] + '</td>' +
                    '<td>' + CURRENT_USER['score'] + '</td>' +
                    '<td>?</td>' +
                    '<td>' + CURRENT_USER['comment'] + '</td><td>' + CURRENT_USER['last_solved'] + '</td></tr>');
            }
            set_html("#output-layer", "<br><h4 align=center>" + d['total'] + output("player-total-msg") + "</h4>");
            toggle_load();
        });
        break;

    default:
        set_error(404);
    }
}

function view_user(path) {
    page_type = path[1];
    if (!page_type) page_type = '';

    switch (page_type) {

    case "find":
        if (IS_AUTH) {
            set_error(418);
            break;
        }
        // if second parameter (recovery code) is returned
        if (path[2]) {
            // check its validity
            if (!(new RegExp("^[a-zA-Z0-9-_]{30,50}$").test(path[2]))) {
                window.location.hash = '#/user/find';
                return false;
            }
            // ask for the new password
            set_html("#content",
                '<div class="row column centered">' +
                '<div id="output-message" class="mb-2"></div>' +
                '<form class="auth-form-body" onsubmit="return act_user_recover(\'' + path[2] + '\');">' +
                '<label for="password">' + output('find-new-pw') + '</label>' +
                '<input class="form-control input-block disabled" tabindex=1 name="password" id="password" type="password">' +
                '<button class="btn btn-block btn-primary" tabindex=4 id="edit_button" type="submit">' + output('find-new-submit') + '</button>' +
                '</form>', true);

        } else {
            set_html("#content",
                '<div class="row column centered">' +
                '<div id="output-message" class="mb-2" ></div>' +
                '<form class="auth-form-body" onsubmit="return act_user_find();">' +
                '<label for="username">' + output('reg-input-email') + '</label>' +
                '<input class="form-control input-block disabled" tabindex=1 name="username" id="username" type="email">' +
                '<p class="note">' + output('find-send-tip') + '</p>' +
                '<button class="btn btn-block btn-primary" tabindex=4 id="edit_button" type="submit">' + output('find-send-submit') + '</button>' +
                '</form>', true);
        }
        toggle_load();
        break;

    case "edit":
        if (!IS_AUTH) {
            set_error(403);
            break;
        }
        set_html("#content",
            '<div class="row column centered">' +
            '<div id="output-message" class="mb-2" ></div>' +
            '<form class="auth-form-body" onsubmit="return act_user_edit();">' +
            '<label for="username">' + output('reg-input-email') + '</label>' +
            '<input class="form-control input-block disabled" tabindex=1 name="username" id="username" type="text" disabled value=' + CURRENT_USER['username'] + '>' +
            '<label for="nickname">' + output('auth-nick') + '</label>' +
            '<input class="form-control input-block disabled" tabindex=1 name="nickname" id="nickname" type="text" disabled value=' + CURRENT_USER['nick'] + '>' +
            '<label for="password">' + output('edit-new-pass') + '</label>' +
            '<input class="form-control input-block" tabindex=2 id="password" name="password" placeholder="Password" type="password">' +
            '<p class="note">' + output('edit-password-tip') + '</p>' +
            '<label for="password">' + output('comment') + '</label>' +
            '<input class="form-control input-block" tabindex=3 id="comment" name="comment" value="' + CURRENT_USER['comment'] + '" placeholder="' + output('edit-comment-tip') + '">' +
            '<button class="btn btn-block btn-primary" tabindex=4 id="edit_button" type="submit">' + output('edit-submit') + '</button>' +
            '</form></div>', true);
        toggle_load();
        break;

    case "register":
        if (IS_AUTH) {
            set_error(418);
            break;
        }
        set_html("#content",
            '<div class="columns">' +
            '<div class="two-thirds column">' +
            '<h2 class="setup-form-title mb-3">' + output('reg-head') + '</h2>' +
            '<form onsubmit="return act_user_register();">' +
            '<dl class="form-group"><dt class="input-label">' +
            '<label autocapitalize="off" autofocus="autofocus" for="username" for="username">' + output('reg-input-email') + '</label>' +
            '</dt><dd>' +
            '<input autocapitalize="off" autofocus="autofocus" class="form-control" id="username" name="username" size="30" type="email" />' +
            '<p class="note">' + output('reg-info-email') + '</p>' +
            '</dd></dl>' +
            '<dl class="form-group"><dt class="input-label">' +
            '<label autocapitalize="off" for="nickname">' + output('nickname') + '</label>' +
            '</dt><dd>' +
            '<input autocapitalize="off" class="form-control" name="nickname" size="30" type="text" id="nickname">' +
            '<p class="note">' + output('reg-info-nickname') + '</p>' +
            '</dd></dl>' +
            '<dl class="form-group"><dt class="input-label">' +
            '<label autocapitalize="off" for="password">' + output('auth-pass') + '</label>' +
            '</dt><dd>' +
            '<input autocapitalize="off" class="form-control" name="password" size="30" type="password" id="password">' +
            '<p class="note">' + output('reg-info-password') + '</p>' +
            '</dd></dl>' +
            '<div id="output-message" class="mb-2" ></div>' +
            '<input type="submit" class="btn btn-primary" id="signup_button" value="' + output('reg-submit') + '">' +
            '</form></div>' +
            '<div class="one-third column"><h2>' + output('reg-note') + '</h2><br>' +
            '<li>' + output('reg-note-1') + '</li><br>' +
            '<li>' + output('reg-note-2') + '</li><br>' +
            '<li>' + output('reg-note-3') + '</li><br>' +
            '</div></div>', true);
        toggle_load(false);
        break;

    case "logout":
        if (!IS_AUTH) {
            set_error(403);
            break;
        }
        $.get("/user/logout", function (d) {
            redirect('/user/login');
        });
        break;

    case "login":
        if (IS_AUTH) {
            set_error(418);
            break;
        }
        set_html("#content",
            '<div class="row column centered">' +
            '<form class="auth-form-body" onsubmit="return act_user_login();">' +
            '<div id="output-message" class="mb-2" ></div>' +
            '<label for="nickname">' + output('auth-nick') + '</label>' +
            '<input class="form-control input-block" tabindex=1 name="nickname" id="nickname" type="text" placeholder="stypr, neko, superuser, ...">' +
            '<label for="password">' + output('auth-pass') + ' <a href="#/user/find" class=right>' + output('auth-forgot') + '</a></label>' +
            '<input class="form-control input-block" tabindex=2 id="password" name="password" placeholder="Password" type="password">' +
            '<input class="form-checkbox" id="remember-nick" type="checkbox"> ' + output('auth-remember') +
            '<button class="btn btn-block btn-primary" tabindex=3 id="signin_button" type="submit">' + output('auth-login') + '</button>' +
            '</form>' +
            '<br><p class="new-comer">' + output('auth-reg-new') + '&nbsp;' +
            '<a href="#/user/register" data-ga-click="Sign in, switch to sign up">' + output('auth-reg-create') + '</a>.' +
            '</p></div>', true);
        // get nickname from browser
        local_nick = localStorage.getItem('current_nick');
        if (local_nick && local_nick != 'null') {
            $("#remember-nick").prop('checked', true);
            $("#nickname").val(local_nick);
        }
        toggle_load();
        break;

    default:
        set_error(404);
    }
}

function view_profile(path) {
    nickname = decodeURIComponent(path[1]);
    if (check_string(nickname, 3, 20) == false) {
        set_error(404);
        return;
    }
    $.get("/status/profile?nickname=" + nickname, function (d) {
        chall_solve = [];
        chall_break = [];
        if (!d) {
            set_error(404);
            return;
        }

        // Parsed solved challenges
        if (d['solved']) {
            // wow, i'm using this syntax for the first time!
            // (thanks @vbalien for the insightful code)
            for (let solved of d['solved']) {
                if (solved['chall_break']) {
                    chall_break.push({
                        'challenge_name': solved['chall_name'],
                        'solve_date': solved['chall_solve_date'],
                        'solve_score': solved['chall_score'],
                        'break_rank': solved['chall_break']['break_rank'],
                    });
                } else {
                    chall_solve.push({
                        'challenge_name': solved['chall_name'],
                        'solve_date': solved['chall_solve_date'],
                        'solve_score': solved['chall_score'],
                    });
                }
            }
        }

        // Prettify information (solved)
        chall_break_out = "";
        if (chall_break.length) {
            chall_break_out = '<h3>' + output('profile-break') + '</h3>' +
                '<div class="Box Box-default">';
            for (let chall of chall_break) {
                chall_break_out += '<div class="Box-header pt-2 pb-2">' +
                    '<span class="octicon octicon-flame short-space"><sup>#' + chall['break_rank'] + '</sup>&nbsp;</span>' +
                    chall['challenge_name'] + ' (' + chall['solve_score'] + output('pt') + ')' +
                    '<span class="right">' + chall['solve_date'] + '</span>' +
                    '</div>';
            }
            chall_break_out += '</div><br>';
        }

        chall_solve_out = "";
        if (chall_solve.length) {
            chall_solve_out = '<h3>' + output('profile-clear') + '</h3>' +
                '<div class="Box Box-default">';
            for (let chall of chall_solve) {
                chall_solve_out += '<div class="Box-header pt-2 pb-2">' +
                    '<span class="octicon octicon-check short-space">&nbsp;</span>' +
                    chall['challenge_name'] + ' (' + chall['solve_score'] + output('pt') + ')' +
                    '<span class="right">' + chall['solve_date'] + '</span>' +
                    '</div>';
            }
            chall_solve_out += '</div><br>';
        }
        if (!(chall_solve_out || chall_break_out)) {
            chall_solve_out = '<div class="blankslate blankstate-spacious">' +
                '<span class="mega-octicon octicon-thumbsdown blankslate-icon"></span>' +
                '<h3>' + output('profile-no-solve-head') + '</h3>' +
                '<p>' + output('profile-no-solve-body') + '</p>' +
                '</div>';
        }
        // if comment is null, don't show it
        if (!d['comment'] || d['comment'] == undefined) d['comment'] = '';

        set_html("#content", '<div class="columns">' +
            // left side
            '<div class="four-fifths column">' +
            '<h1 class="short-line" id="profile-nickname">' + d['nick'] + '</h1>' +
            '<code class="wrap-code short-space">' + d['comment'] + '</code>' +
            '<hr style="margin:5pt;border:0;">' +
            '<p class="long-line">#' + d['rank'] + output('profile-score-prefix') +
            +d['score'] + output('pt') + output('profile-score-suffix') + '.</p>' +
            chall_break_out +
            chall_solve_out +
            '</div>' +
            // right side
            '<div class="one-fifth column"><center>' +
            '<img class="avatar" src="' + d['profile_picture'] + '" width=100%>' +
            '<font size=2><span class="octicon octicon-lock" style="margin-top:5pt;"></span>' + d['username'] + '<br>' +
            'Since ' + d['join_date'] + '.</font><br><br></center>' +
            '</div>', true);

        // Get badges and add it on the result
        $.get("/badge/get?nickname=" + d['nick'], function (x) {
            if (typeof x === "object") {
                for (let badge of x) {
                    set_html("#profile-nickname",
                        '&thinsp;' +
                        '<label class="Label Label--' + badge['type'] + '">' +
                        badge['name'] +
                        '</label>'
                    );
                }
            }
            toggle_load();
        });
    });
}

function view_chat() {
    if (!IS_AUTH) {
        set_error(403);
        return;
    }
    set_html('#content',
        '<iframe src="//kiwiirc.com/client/irc.freenode.net/?nick=' +
        CURRENT_USER['nick'] + '&theme=cli#' +
        IRC_CHANNEL + '" style="border:0; width:100%; height:450px;">' +
        '</iframe>' +
        '<center><h4>' + output('chat-rule') + '</h4></center>', true);
    toggle_load();
}

function view_chall(path) {
    // Filtering not implemented yet. //
    // chall/(filter_type)/(filter_string)
    if (!IS_AUTH) {
        set_error(403);
        return;
    }
    set_html("#content",
        '<div class="row column centered">' +
        '<div id="output-message"></div>' +
        '<form onsubmit="return act_chall_auth()">' +
        '<div class="input-group columns">' +
        '<div class="two-thirds p-2 column">' +
        '<input class="form-control monospace full-width" placeholder="flag{ ... }"' +
        'autocomplete="off" id="flag" name="flag">' +
        '</div>' +
        '<div class="one-third p-2 column"><span class="input-group-button">' +
        '<button class="btn btn-primary one-third full-width" type="submit">' +
        '<span class="octicon octicon-key"> ' + output('auth') + '</span>' +
        '</button></div>' +
        '</div><hr style="border:0;">', true);
    $.get("/challenge/list", function (d) {
        // Sort by challenge_score, ascending order.
        challenges = d.sort(function (a, b) {
            if (a.challenge_score == b.challenge_score) return 0;
            return a.challenge_score > b.challenge_score ? 1 : -1;
        });
        user_solved = [];
        for (let solved of CURRENT_USER['solved']) {
            user_solved.push(Object.values(solved)[0]);
        }
        for (let challenge of challenges) {
            // Change background/icon for solved ones
            if (user_solved.includes(challenge['challenge_name'])) {
                chall_color = "green";
                chall_icon = "shield";
            } else {
                chall_color = "blue";
                chall_icon = "bug";
            }
            set_html("#content",
                '<div class="Box mb-3"><div class="Box-header pt-2 pb-2 Box-header--' + chall_color + '">' +
                '<h3 class="Box-title"><span class="octicon octicon-bug">&nbsp;</span>' + challenge['challenge_name'] +
                ' <span class="right">' + challenge['challenge_score'] + output('pt') + '</span></h3></div>' +
                '<div class="Box-body">' + challenge['challenge_desc'] +
                '</form></div></div>');
        }
        toggle_load();
    });
}

// Loader //

function load_language() {

    // Set default language (en) on first access
    data_local = localStorage.getItem('current_language');
    if (CURRENT_LANG == null) {
        if (!data_local || data_local == 'null') {
            data_local = 'en';
            localStorage.setItem('current_language', data_local);
        }
        CURRENT_LANG = data_local;
    } else {
        localStorage.setItem('current_language', CURRENT_LANG);
    }

    // Set suitable font for all languages
    $("*:not(.octicon)").css("font-family", output('FONT'));
    $("#language-select").val(CURRENT_LANG);

    // Flush and Add events
    $("#language-select").unbind('change');
    $("#language-select").change(function () {
        // The script needs to be restarted upon the change of language
        CURRENT_LANG = $("#language-select").val();
        $("*:not(.octicon)").css("font-family", output('FONT'));
        init_load();
    });
}

function load_profile() {
    toggle_load();
    $.get('/status/profile', function (d) {
        if (d == false) {
            IS_AUTH = false;
            CURRENT_USER = null;
        } else {
            IS_AUTH = true;
            CURRENT_USER = d;
            if (!CURRENT_USER['comment']) CURRENT_USER['comment'] = '';
        }
        load_layout();
    });
}

function load_layout() {
    // Add Sidebar, then load content
    set_html("#sidebar", "<ul class='filter-list' id='sidebar-menu'></ul>", true);
    if (IS_AUTH) {

        _subhead = 'edit';
        set_html("#sidebar-menu",
            "<li page-id='" + _subhead + "'><a href='#/user/edit' class='filter-item'>" +
            "<table class='profile'><tr><td rowspan=2>" +
            "<img src=" + CURRENT_USER['profile_picture'] + " width=40 class='profile-image'>" +
            "&nbsp;</td><td class='profile-nickname'>" + CURRENT_USER['nick'] + "</td></tr>" +
            "<tr><td class='profile-score'>" + CURRENT_USER['score'] + output('pt') + "</td>" +
            "</tr></table></a></li>");

        _subhead = 'logout';
        set_html("#sidebar-menu",
            "<li page-id='" + _subhead + "'>" +
            "<a href='#/user/logout' class='filter-item'></a></li>");
        set_html("#sidebar-menu>li[page-id='" + _subhead + "']>a",
            output(_subhead) +
            "<span class='octicon octicon-sign-out right'></span>");

    } else {

        _subhead = 'login';
        set_html("#sidebar-menu",
            "<li page-id='" + _subhead + "'>" +
            "<a href='#/user/login' class='filter-item'></a></li>");
        set_html("#sidebar-menu>li[page-id='" + _subhead + "']>a",
            output(_subhead) +
            "<span class='octicon octicon-sign-in right'></span>");

    }

    set_html("#sidebar-menu", "<hr>");

    _subhead = 'intro';
    set_html("#sidebar-menu",
        "<li page-id='" + _subhead + "'>" +
        "<a href='#/intro' class='filter-item'></a></li>");
    set_html("#sidebar-menu>li[page-id='" + _subhead + "']>a",
        output(_subhead) +
        "<span class='octicon octicon-home right'></span>");

    _subhead = 'status';
    set_html("#sidebar-menu",
        "<li page-id='" + _subhead + "'>" +
        "<a href='#/status' class='filter-item'></a></li>");
    set_html("#sidebar-menu>li[page-id='" + _subhead + "']>a",
        output(_subhead) +
        "<span class='octicon octicon-graph right'></span>");

    if (IS_AUTH) {
        _subhead = 'chall';
        set_html("#sidebar-menu",
            "<li page-id='" + _subhead + "'>" +
            "<a href='#/chall' class='filter-item'></a></li>");
        set_html("#sidebar-menu>li[page-id='" + _subhead + "']>a",
            output(_subhead) +
            "<span class='octicon octicon-bug right'></span>");

        _subhead = 'chat';
        set_html("#sidebar-menu",
            "<li page-id='" + _subhead + "'>" +
            "<a href='#/chat' class='filter-item'></a></li>");
        set_html("#sidebar-menu>li[page-id='" + _subhead + "']>a",
            output(_subhead) +
            "<span class='octicon octicon-comment-discussion right'></span>");

        /*
        		_subhead = 'board';
        		set_html("#sidebar-menu",
        			"<li page-id='" + _subhead + "'>" +
        			"<a href='#/board' class='filter-item'></a></li>");
        		set_html("#sidebar-menu>li[page-id='"+_subhead+"']>a",
        			output(_subhead) +
        			"<span class='octicon octicon-home right'></span>");
        */
    }
	_subhead = 'netstat';
	set_html("#sidebar-menu",
		"<li page-id='" + _subhead + "'>" +
		"<a href='//eagle-jump.org' target='_blank' class='filter-item'></a></li>");
	set_html("#sidebar-menu>li[page-id='" + _subhead + "']>a",
		output(_subhead) +
		"<span class='octicon octicon-megaphone right'></span>");

    // Add click events for sidebar / hashchange
    $("#sidebar-menu > li > a").unbind("click");
    $("#sidebar-menu > li > a").click(function () {
        $("#sidebar-menu .selected").removeClass("selected");
        $(this).siblings().removeClass("selected");
        $(this).addClass("selected"); // children(':first')
    });
    $(window).unbind('hashchange');
    $(window).on('hashchange', function () {
        CURRENT_PAGE = location.hash.slice(1);
        $("#sidebar-menu .selected").removeClass("selected");
        $(this).siblings().removeClass("selected");
        toggle_load();
        load_content();
    });

    // Load contents
    CURRENT_PAGE = location.hash.slice(1);
    load_content();
}

function load_content() {
    // Parse the current URL and return the path
    path = [''];
    if (typeof CURRENT_PAGE === "string" && CURRENT_PAGE !== "") {
        path = CURRENT_PAGE.split('/');
        path.shift();
    }
    // Load content based on the path
    switch (path[0]) {
    case 'user':
        selected_menu = IS_AUTH && 'logout' || 'login';
        if (path[1] == 'edit') selected_menu = 'edit';
        $("#sidebar-menu>li[page-id='" + selected_menu + "']>a").addClass("selected");
        view_user(path);
        break;
    case 'status':
        $("#sidebar-menu>li[page-id='status']>a").addClass("selected");
        view_status(path);
        break;
    case 'chat':
        $("#sidebar-menu>li[page-id='chat']>a").addClass("selected");
        view_chat();
        break;
    case 'chall':
        $("#sidebar-menu>li[page-id='chall']>a").addClass("selected");
        view_chall(path);
        break;
    case 'profile':
        $("#sidebar-menu>li[page-id='status']>a").addClass("selected");
        view_profile(path);
        break;
    case 'intro':
    case '':
        $("#sidebar-menu>li[page-id='intro']>a").addClass("selected");
        view_intro();
        break;
    default:
        set_error(404);
        //console.log(_url);
    }
}


// Loader //

function toggle_load() {
    load_style = $('.loader').css('display');
    // for toggle
    // >> toggle_load(false); // hides load forcefully.
    if (arguments.length == 1) {
        if (arguments[0] == false) {
            $('.loader').css('display', 'none');
        } else {
            $('.loader').css('display', 'block');
        }
    } else {
        if (load_style == "block") {
            $('.loader').css('display', 'none');
        } else {
            $('.loader').css('display', 'block');
        }

    }
}

function init_load() {
    load_language();
    load_profile();
}

// Main //
function main() {
    init_load();
}

$(document).ready(main);

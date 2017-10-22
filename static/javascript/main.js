/* static/javascript/main.js
jquery is <3 for now, but I should make my own framework next time.. */
// Global Variables //
var IRC_CHANNEL = "stypr";
var CURRENT_PAGE = null;
var CURRENT_LANG = null;
var CURRENT_USER = null;
var IS_AUTH = false;

// Global Function //

var set_html = function(t, d, n) {
    if (!d) d = '';
    if (n) {
        $(t).html(d);
    } else {
        $(t).append(d);
    }
}

var check_string = function(str, min, max) {
    if (!min) min = 5;
    if (!max) max = 30;
    var _regexp = '^[a-zA-Z0-9-_!@$.%^&*()가-힣]{' + min + ',' + max + '}$';
    var _check = new RegExp(_regexp).test(str);
    return _check;
}


// View //

function view_intro() {
    set_html("#content", output("INTRO"), true);
}

function view_status() {
    page_type = arguments[0][1];
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
            break;

        case "auth":
            $.get('/status/auth', function(d) {
                // #output-layer -> table -> tbody #log-list
                set_html("#output-layer",
                    '<table class="data-table table-hover" id="scoreboard" style="font-size:10pt;">' +
                    '<thead><tr>' +
                    '<th align=center>#</th><th align=center>' + output('nickname') + '</th>' +
                    '<th align=center>' + output('chall') + '</th>' +
                    '<th align=center>' + output('chall-solve-date') + '</th>' +
                    '</tr></thead><tbody id="log-list"></tbody></table', true);
                for (i = 0; i < d.length; i++) {
                    auth_log = d[i];
                    // #loglist -> tr -> td
                    set_html("#log-list",
                        '<tr class="info" style="cursor:pointer;"' +
                        'onclick="location.replace(\'#/profile/' + auth_log['nick'] + '\')">' +
                        '<td>' + auth_log['no'] + '</td>' +
                        '<td>' + auth_log['nick'] + '</td>' +
                        '<td>' + auth_log['chall'] + '</td>' +
                        '<td>' + auth_log['date'] + '</td>' +
                        '</tr>');
                }
            });
            break;

        case "chall":
            $.get('/status/challenge', function(d) {
                for (i = 0; i < d.length; i++) {
                    curr_chall = d[i];
                    curr_chall_top = curr_chall['break'];

                    // Get top3 breakthru
                    top_info = '';
                    try {
                        for (j = 0; j < curr_chall_top.length; j++) {
                            top_info += '<tr class="info" style="cursor:pointer" onclick="location.replace(\'#/profile/' + curr_chall_top[j]['user'] + '\')">' +
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
            });
            break;

        case "player":
        default:
            $.get('/status/scoreboard', function(d) {
                set_html("#output-layer",
                    '<table class="data-table table-hover" id="scoreboard" style="font-size:10pt;">' +
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
                            ranker = true;
                        }
                    } catch (e) {}
                    set_html("#scoreboard",
                        '<tr class="info" style="cursor:pointer;" onclick="location.replace(\'#/profile/' + _player['nickname'] + '\')">' +
                        '<td>' + _rank + '</td><td>' + _player['nickname'] + '</td>' +
                        '<td>' + _player['score'] + '</td>' +
                        '<td>' + _player['break_count'] + '</td>' +
                        '<td>' + _player['comment'] + '</td><td>' + _player['last_solved'] + '</td></tr>');
                }

                // List current user if not listed
                if (ranker == false && IS_AUTH == true) {
                    set_html("#scoreboard",
                        '<tr class="info" style="cursor:pointer;" onclick="location.replace(\'#/profile/' + CURRENT_USER['nick'] + '\')">' +
                        '<td>' + CURRENT_USER['rank'] + '</td><td>' + CURRENT_USER['nick'] + '</td>' +
                        '<td>' + CURRENT_USER['score'] + '</td>' +
                        '<td>?</td>' +
                        '<td>' + CURRENT_USER['comment'] + '</td><td>' + CURRENT_USER['last_solved'] + '</td></tr>');
                }
                set_html("#output-layer", "<br><h4 align=center>" + d['total'] + output("player-total-msg") + "</h4>");
            });
    }
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
    $("#language-select").change(function() {
        // The script needs to be restarted upon the change of language
        CURRENT_LANG = $("#language-select").val();
        $("*:not(.octicon").css("font-family", output('FONT'));
        init_load();
    });
}

function load_profile() {
    $.get('/status/profile', function(d) {
        if (d == false) {
            IS_AUTH = false;
            CURRENT_USER = null;
        } else {
            IS_AUTH = true;
            CURRENT_USER = d;
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
            "<li page-id=" + _subhead + "'><a href='#/user/edit' class='filter-item'>" +
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

    // Add click events for sidebar / hashchange
    $("#sidebar-menu > li > a").unbind("click");
    $("#sidebar-menu > li > a").click(function() {
        $("#sidebar-menu .selected").removeClass("selected");
        $(this).siblings().removeClass("selected");
        $(this).addClass("selected"); // children(':first')
    });
    $(window).unbind('hashchange');
    $(window).on('hashchange', function() {
        CURRENT_PAGE = location.hash.slice(1);
        $("#sidebar-menu .selected").removeClass("selected");
        $(this).siblings().removeClass("selected");
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
function init_load() {
    load_language();
    load_profile();
}

// Main //
function main() {
    init_load();
}

$(document).ready(main);
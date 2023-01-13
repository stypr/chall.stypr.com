<?php

//define("__FLAG__", "flag{d1897bc269683381496093bb180ff10ad92bfa11}");
define("__FLAG__", "3xZG{dgznclXAwFVCUHPMWFViZBDSsltxKdrvFBVYHSr1}");

function stypr_crypt($_0)
{
    $_1 = $_2 = $_3 = array(); 
    $_4 =array('?','(','@', ';', '$','#',  ']',  '&', '*'); 
    $_4 = array_merge($_4,range('a','z'),range('A','Z'),range('0','9')); 
    $_4 = array_merge($_4,array('!', ')', '_', '+', '|', '%', '/', '[', '.', ' ')); 
    for($_5=9,$_6=0;$_6 < $_5;$_6++)
    { 
        for($_7=0;$_7<$_5;$_7++)
        {
            $_2[$_6][$_7] = $_4[$_6*$_5+$_7]; 
            $_3[$_6][$_7] = str_rot13($_4[($_5*$_5-1)-($_6*$_5+$_7)]);
        } 
    } 
    unset($_4); 
    $_8 = floor(strlen($_0)/2)*2; 
    $_9 = ($_8 == strlen($_0))? '': $_0[strlen($_0)-1]; 
    $_10 = array(); 
    for($_11 = 0;$_11 < $_8;$_11+=2)
    {
        $_12 = $_13 = strval($_0[$_11]); 
        $_14 = $_15 = strval($_0[$_11+1]); 
        $_16 = $_17 = array(); 
        for($_6=0; $_6 < $_5;$_6++)
        {
            for($_7=0;$_7<$_5;$_7++)
            { 
                if($_12 === strval($_3[$_6][$_7]))
                { 
                    $_16 = array($_6, $_7);
                } 

                if($_14 === strval($_2[$_6][$_7]))
                { 
                    $_17 = array($_6, $_7);
                } 

                if(!isset($_9) && ($_9 === strval($_3[$_6][$_7])))
                {
                    $_10 = array($_6, $_7);
                } 
            } 
        } 

        if(sizeof($_16) && sizeof($_17))
        {
            $_13 = $_2[$_16[0]][$_17[1]]; 
            $_15 = $_3[$_17[0]][$_16[1]];
        } 

        $_1[] = $_13.$_15;
    } 

    if(!isset($_9) && sizeof($_10))
    {
        $_1[] = $_2[$_10[1]][$_10[0]];
    } 

    return implode('',$_1); 
}

if(isset($_GET['flag']) && is_string($_GET['flag'])){
	if(stypr_crypt(stypr_crypt(stypr_crypt($_GET['flag']))) === __FLAG__){
		print("<center>Correct!</center>");
	}else{
		print("<center>Incorrect..</center>");
	}
}

?><!DOCTYPE html>
<html lang="en">
  <head>
          <meta charset="utf-8">
          <meta http-equiv="X-UA-Compatible" content="IE=edge">
          <meta name="viewport" content="width=device-width, initial-scale=1">
          <title>Derick's analysis on the next level</title>
          <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
          <style>
                        html {
                          position: relative;
                          min-height: 100%;
                        }
                        body {
                          margin-bottom: 60px;
                        }
                        .footer {
                          position: absolute;
                          bottom: 0;
                          width: 100%;
                          height: 60px;
                          background-color: #f5f5f5;
                        }
                        .container {
                          width: auto;
                          max-width: 840px;
                          padding: 0 15px;
                        }
                        .container .text-muted {
                          margin: 20px 0;
                        }
			.page-header {
			  margin-top: 50pt;
			}
          </style>
  </head>
  <body>
    <div class="container">
      <div class="page-header">
        <h1>Brainstorm your PHP reverse engineering!</h1>
      </div>
      <p class="lead">Top engineering universities in Korea have already solved this challenge. Can you?</p>
          <p>
<pre>
(null)

line     #* E I O op                           fetch          ext  return  operands
-------------------------------------------------------------------------------------
   3     0  E >   SEND_VAL                                                 '__FLAG__'
         1        SEND_VAL                                                 '3xZG%7BdgznclXAwFVCUHPMWFViZBDSsltxKdrvFBVYHSr1%7D'
         2        DO_FCALL                                      2          'define'
   4     3        NOP                                                      
   6     4        FETCH_CONSTANT                                   ~1      '__FLAG__'
         5        FETCH_R                      global              $2      '_GET'
         6        FETCH_DIM_R                                      $3      $2, 'flag'
         7        SEND_VAR                                                 $3
         8        DO_FCALL                                      1  $4      'stypr_crypt'
         9        SEND_VAR_NO_REF                               6          $4
        10        DO_FCALL                                      1  $5      'stypr_crypt'
        11        SEND_VAR_NO_REF                               6          $5
        12        DO_FCALL                                      1  $6      'stypr_crypt'
        13        IS_IDENTICAL                                     ~7      ~1, $6
        14      > JMPZ                                                     ~7, ->17
   7    15    >   ECHO                                                     'Correct!'
   8    16      > JMP                                                      ->18
   9    17    >   ECHO                                                     'Incorrect..'
  15    18    > > RETURN                                                   1
</pre>

<pre>
stypr_crypt()

line     #* E I O op                           fetch          ext  return  operands
-------------------------------------------------------------------------------------
   4     0  E >   RECV                                             !0      
         1        INIT_ARRAY                                       ~0      
         2        ASSIGN                                           $1      !3, ~0
         3        ASSIGN                                           $2      !2, $1
         4        ASSIGN                                                   !1, $2
         5        INIT_ARRAY                                       ~4      '%3F'
         6        ADD_ARRAY_ELEMENT                                ~4      '%28'
         7        ADD_ARRAY_ELEMENT                                ~4      '%40'
         8        ADD_ARRAY_ELEMENT                                ~4      '%3B'
         9        ADD_ARRAY_ELEMENT                                ~4      '%24'
        10        ADD_ARRAY_ELEMENT                                ~4      '%23'
        11        ADD_ARRAY_ELEMENT                                ~4      '%5D'
        12        ADD_ARRAY_ELEMENT                                ~4      '%26'
        13        ADD_ARRAY_ELEMENT                                ~4      '%2A'
        14        ASSIGN                                                   !4, ~4
        15        SEND_VAR                                                 !4
        16        SEND_VAL                                                 'a'
        17        SEND_VAL                                                 'z'
        18        DO_FCALL                                      2  $6      'range'
        19        SEND_VAR_NO_REF                               6          $6
        20        SEND_VAL                                                 'A'
        21        SEND_VAL                                                 'Z'
        22        DO_FCALL                                      2  $7      'range'
        23        SEND_VAR_NO_REF                               6          $7
        24        SEND_VAL                                                 0
        25        SEND_VAL                                                 9
        26        DO_FCALL                                      2  $8      'range'
        27        SEND_VAR_NO_REF                               6          $8
        28        DO_FCALL                                      4  $9      'array_merge'
        29        ASSIGN                                                   !4, $9
        30        SEND_VAR                                                 !4
        31        INIT_ARRAY                                       ~11     '%21'
        32        ADD_ARRAY_ELEMENT                                ~11     '%29'
        33        ADD_ARRAY_ELEMENT                                ~11     '_'
        34        ADD_ARRAY_ELEMENT                                ~11     '%2B'
        35        ADD_ARRAY_ELEMENT                                ~11     '%7C'
        36        ADD_ARRAY_ELEMENT                                ~11     '%25'
        37        ADD_ARRAY_ELEMENT                                ~11     '%2F'
        38        ADD_ARRAY_ELEMENT                                ~11     '%5B'
        39        ADD_ARRAY_ELEMENT                                ~11     '.'
        40        ADD_ARRAY_ELEMENT                                ~11     '+'
        41        SEND_VAL                                                 ~11
        42        DO_FCALL                                      2  $12     'array_merge'
        43        ASSIGN                                                   !4, $12
        44        ASSIGN                                                   !5, 9
        45        ASSIGN                                                   !6, 0
        46    >   IS_SMALLER                                       ~16     !6, !5
        47      > JMPZNZ                                       51          ~16, ->76
        48    >   POST_INC                                         ~17     !6
        49        FREE                                                     ~17
        50      > JMP                                                      ->46
        51    >   ASSIGN                                                   !7, 0
        52    >   IS_SMALLER                                       ~19     !7, !5
        53      > JMPZNZ                                       57          ~19, ->75
        54    >   POST_INC                                         ~20     !7
        55        FREE                                                     ~20
        56      > JMP                                                      ->52
        57    >   MUL                                              ~23     !6, !5
        58        ADD                                              ~24     ~23, !7
        59        FETCH_DIM_R                                      $25     !4, ~24
        60        FETCH_DIM_W                                      $21     !2, !6
        61        ASSIGN_DIM                                               $21, !7
        62        OP_DATA                                                  $25, $26
        63        MUL                                              ~29     !5, !5
        64        SUB                                              ~30     ~29, 1
        65        MUL                                              ~31     !6, !5
        66        ADD                                              ~32     ~31, !7
        67        SUB                                              ~33     ~30, ~32
        68        FETCH_DIM_R                                      $34     !4, ~33
        69        SEND_VAR                                                 $34
        70        DO_FCALL                                      1  $35     'str_rot13'
        71        FETCH_DIM_W                                      $27     !3, !6
        72        ASSIGN_DIM                                               $27, !7
        73        OP_DATA                                                  $35, $36
        74      > JMP                                                      ->54
        75    > > JMP                                                      ->48
        76    >   UNSET_VAR                                                !4
        77        SEND_VAR                                                 !0
        78        DO_FCALL                                      1  $37     'strlen'
        79        DIV                                              ~38     $37, 2
        80        SEND_VAL                                                 ~38
        81        DO_FCALL                                      1  $39     'floor'
        82        MUL                                              ~40     $39, 2
        83        ASSIGN                                                   !8, ~40
        84        SEND_VAR                                                 !0
        85        DO_FCALL                                      1  $42     'strlen'
        86        IS_EQUAL                                         ~43     !8, $42
        87      > JMPZ                                                     ~43, ->90
        88    >   QM_ASSIGN_VAR                                    $44     ''
        89      > JMP                                                      ->95
        90    >   SEND_VAR                                                 !0
        91        DO_FCALL                                      1  $45     'strlen'
        92        SUB                                              ~46     $45, 1
        93        FETCH_DIM_R                                      $47     !0, ~46
        94        QM_ASSIGN_VAR                                    $44     $47
        95    >   ASSIGN                                                   !9, $44
        96        INIT_ARRAY                                       ~49     
        97        ASSIGN                                                   !10, ~49
        98        ASSIGN                                                   !11, 0
        99    >   IS_SMALLER                                       ~52     !11, !8
       100      > JMPZNZ                                      103          ~52, ->187
       101    >   ASSIGN_ADD                                    0          !11, 2
       102      > JMP                                                      ->99
       103    >   FETCH_DIM_R                                      $54     !0, !11
       104        SEND_VAR                                                 $54
       105        DO_FCALL                                      1  $55     'strval'
       106        ASSIGN                                           $56     !13, $55
       107        ASSIGN                                                   !12, $56
       108        ADD                                              ~58     !11, 1
       109        FETCH_DIM_R                                      $59     !0, ~58
       110        SEND_VAR                                                 $59
       111        DO_FCALL                                      1  $60     'strval'
       112        ASSIGN                                           $61     !15, $60
       113        ASSIGN                                                   !14, $61
       114        INIT_ARRAY                                       ~63     
       115        ASSIGN                                           $64     !17, ~63
       116        ASSIGN                                                   !16, $64
       117        ASSIGN                                                   !6, 0
       118    >   IS_SMALLER                                       ~67     !6, !5
       119      > JMPZNZ                                      123          ~67, ->165
       120    >   POST_INC                                         ~68     !6
       121        FREE                                                     ~68
       122      > JMP                                                      ->118
       123    >   ASSIGN                                                   !7, 0
       124    >   IS_SMALLER                                       ~70     !7, !5
       125      > JMPZNZ                                      129          ~70, ->164
       126    >   POST_INC                                         ~71     !7
       127        FREE                                                     ~71
       128      > JMP                                                      ->124
       129    >   FETCH_DIM_R                                      $72     !3, !6
       130        FETCH_DIM_R                                      $73     $72, !7
       131        SEND_VAR                                                 $73
       132        DO_FCALL                                      1  $74     'strval'
       133        IS_IDENTICAL                                     ~75     !12, $74
       134      > JMPZ                                                     ~75, ->139
       135    >   INIT_ARRAY                                       ~76     !6
       136        ADD_ARRAY_ELEMENT                                ~76     !7
       137        ASSIGN                                                   !16, ~76
       138      > JMP                                                      ->139
       139    >   FETCH_DIM_R                                      $78     !2, !6
       140        FETCH_DIM_R                                      $79     $78, !7
       141        SEND_VAR                                                 $79
       142        DO_FCALL                                      1  $80     'strval'
       143        IS_IDENTICAL                                     ~81     !14, $80
       144      > JMPZ                                                     ~81, ->149
       145    >   INIT_ARRAY                                       ~82     !6
       146        ADD_ARRAY_ELEMENT                                ~82     !7
       147        ASSIGN                                                   !17, ~82
       148      > JMP                                                      ->149
       149    >   ISSET_ISEMPTY_VAR                           293601280  ~84     !9
       150        BOOL_NOT                                         ~85     ~84
       151      > JMPZ_EX                                          ~85     ~85, ->158
       152    >   FETCH_DIM_R                                      $86     !3, !6
       153        FETCH_DIM_R                                      $87     $86, !7
       154        SEND_VAR                                                 $87
       155        DO_FCALL                                      1  $88     'strval'
       156        IS_IDENTICAL                                     ~89     !9, $88
       157        BOOL                                             ~85     ~89
       158    > > JMPZ                                                     ~85, ->163
       159    >   INIT_ARRAY                                       ~90     !6
       160        ADD_ARRAY_ELEMENT                                ~90     !7
       161        ASSIGN                                                   !10, ~90
       162      > JMP                                                      ->163
       163    > > JMP                                                      ->126
       164    > > JMP                                                      ->120
       165    >   SEND_VAR                                                 !16
       166        DO_FCALL                                      1  $92     'sizeof'
       167      > JMPZ_EX                                          ~93     $92, ->171
       168    >   SEND_VAR                                                 !17
       169        DO_FCALL                                      1  $94     'sizeof'
       170        BOOL                                             ~93     $94
       171    > > JMPZ                                                     ~93, ->183
       172    >   FETCH_DIM_R                                      $95     !16, 0
       173        FETCH_DIM_R                                      $97     !17, 1
       174        FETCH_DIM_R                                      $96     !2, $95
       175        FETCH_DIM_R                                      $98     $96, $97
       176        ASSIGN                                                   !13, $98
       177        FETCH_DIM_R                                      $100    !17, 0
       178        FETCH_DIM_R                                      $102    !16, 1
       179        FETCH_DIM_R                                      $101    !3, $100
       180        FETCH_DIM_R                                      $103    $101, $102
       181        ASSIGN                                                   !15, $103
       182      > JMP                                                      ->183
       183    >   CONCAT                                           ~106    !13, !15
       184        ASSIGN_DIM                                               !1
       185        OP_DATA                                                  ~106, $107
       186      > JMP                                                      ->101
       187    >   ISSET_ISEMPTY_VAR                           293601280  ~108    !9
       188        BOOL_NOT                                         ~109    ~108
       189      > JMPZ_EX                                          ~109    ~109, ->193
       190    >   SEND_VAR                                                 !10
       191        DO_FCALL                                      1  $110    'sizeof'
       192        BOOL                                             ~109    $110
       193    > > JMPZ                                                     ~109, ->201
       194    >   FETCH_DIM_R                                      $112    !10, 1
       195        FETCH_DIM_R                                      $114    !10, 0
       196        FETCH_DIM_R                                      $113    !2, $112
       197        FETCH_DIM_R                                      $115    $113, $114
       198        ASSIGN_DIM                                               !1
       199        OP_DATA                                                  $115, $116
       200      > JMP                                                      ->201
       201    >   SEND_VAL                                                 ''
       202        SEND_VAR                                                 !1
       203        DO_FCALL                                      2  $117    'implode'
       204      > RETURN                                                   $117
       205*     > RETURN                                                   null
</pre>
          </p>
    </div>

    <div class="footer">
      <div class="container">
        <p class="text-muted">Copyleft &copy; 2015 stypr.</p>
      </div>
    </div>

  </body>
</html>

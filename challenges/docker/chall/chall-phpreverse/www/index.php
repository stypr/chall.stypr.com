<?php
        error_reporting(0);
        define("__FLAG__", "flag{ef967d23b0c293406767b5d466978283}");

        function Decrypt($str){
                $strings = strtolower(strrev($str));
                $MyKey = hexdec(substr($strings, 0, 4)) ^ hexdec("BFF") ^ hexdec("D77D");
                $Temps = substr($strings, 4);
                unset($tmp);
                for ($i = 0; $i < strlen($Temps); $i+=6)
                {
                        ++$n;
                        $cal = ($n * $n) ^ hexdec("6E");
                        $tmp = $tmp . chr(octdec(hexdec(substr($Temps, $i, 6))) ^ ($MyKey ^ hexdec("AFE43") ^ hexdec("399AA3") ^ $cal));
                }
                return $tmp;
        }
        if(Decrypt($_REQUEST['pass']) == "pwning_the_phpreverse!"){
                die(__FLAG__);
        }
?>
<!DOCTYPE html>
<html lang="en">
  <head>
          <meta charset="utf-8">
          <meta http-equiv="X-UA-Compatible" content="IE=edge">
          <meta name="viewport" content="width=device-width, initial-scale=1">
          <title>Derick's Bet</title>
          <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
          <link href="sticky-footer.css" rel="stylesheet">
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
          </style>
  </head>
  <body>
    <div class="container">
      <div class="page-header">
        <h1>Important!</h1>
      </div>
      <p class="lead">Hi, My name is Derick and I develop PHP debugging softwares. I am willing to pay you 500 pounds if you decode the code and authenticate successfully. Good Luck!</p>
          <p>
<pre>
line     # *  op                           fetch          ext  return  operands
---------------------------------------------------------------------------------
   2     0  >   NOP
  17     1      SEND_VAL                                                 '__FLAG__'
         2      SEND_VAL                                                 'flag{hidden_flag}'
         3      DO_FCALL                                      2          'define'
  18     4      FETCH_R                      global              $1      '_REQUEST'
         5      FETCH_DIM_R                                      $2      $1, 'pass'
         6      SEND_VAR                                                 $2
         7      DO_FCALL                                      1  $3      'decrypt'
         8      IS_EQUAL                                         ~4      $3, 'pwning_the_phpreverse%21'
         9    > JMPZ                                                     ~4, ->13
  19    10  >   FETCH_CONSTANT                                   ~5      '__FLAG__'
        11    > EXIT                                                     ~5
  20    12*     JMP                                                      ->13
  22    13  > > RETURN                                                   1


line     # *  op                           fetch          ext  return  operands
---------------------------------------------------------------------------------
   2     0  >   RECV                                             !0
   4     1      SEND_VAR                                                 !0
         2      DO_FCALL                                      1  $0      'strrev'
         3      SEND_VAR_NO_REF                               6          $0
         4      DO_FCALL                                      1  $1      'strtolower'
         5      ASSIGN                                                   !1, $1
   5     6      SEND_VAR                                                 !1
         7      SEND_VAL                                                 0
         8      SEND_VAL                                                 4
         9      DO_FCALL                                      3  $3      'substr'
        10      SEND_VAR_NO_REF                               6          $3
        11      DO_FCALL                                      1  $4      'hexdec'
        12      SEND_VAL                                                 'BFF'
        13      DO_FCALL                                      1  $5      'hexdec'
        14      BW_XOR                                           ~6      $4, $5
        15      SEND_VAL                                                 'D77D'
        16      DO_FCALL                                      1  $7      'hexdec'
        17      BW_XOR                                           ~8      ~6, $7
        18      ASSIGN                                                   !2, ~8
   6    19      SEND_VAR                                                 !1
        20      SEND_VAL                                                 4
        21      DO_FCALL                                      2  $10     'substr'
        22      ASSIGN                                                   !3, $10
   7    23      UNSET_VAR                                                !4
   8    24      ASSIGN                                                   !5, 0
        25  >   SEND_VAR                                                 !3
        26      DO_FCALL                                      1  $13     'strlen'
        27      IS_SMALLER                                       ~14     !5, $13
        28    > JMPZNZ                                       1F          ~14, ->58
        29  >   ASSIGN_ADD                                    0          !5, 6
        30    > JMP                                                      ->25
  10    31  >   PRE_INC                                                  !6
  11    32      MUL                                              ~17     !6, !6
        33      SEND_VAL                                                 '6E'
        34      DO_FCALL                                      1  $18     'hexdec'
        35      BW_XOR                                           ~19     ~17, $18
        36      ASSIGN                                                   !7, ~19
  12    37      SEND_VAR                                                 !3
        38      SEND_VAR                                                 !5
        39      SEND_VAL                                                 6
        40      DO_FCALL                                      3  $21     'substr'
        41      SEND_VAR_NO_REF                               6          $21
        42      DO_FCALL                                      1  $22     'hexdec'
        43      SEND_VAR_NO_REF                               6          $22
        44      DO_FCALL                                      1  $23     'octdec'
        45      SEND_VAL                                                 'AFE43'
        46      DO_FCALL                                      1  $24     'hexdec'
        47      BW_XOR                                           ~25     !2, $24
        48      SEND_VAL                                                 '399AA3'
        49      DO_FCALL                                      1  $26     'hexdec'
        50      BW_XOR                                           ~27     ~25, $26
        51      BW_XOR                                           ~28     ~27, !7
        52      BW_XOR                                           ~29     $23, ~28
        53      SEND_VAL                                                 ~29
        54      DO_FCALL                                      1  $30     'chr'
        55      CONCAT                                           ~31     !4, $30
        56      ASSIGN                                                   !4, ~31
  13    57    > JMP                                                      ->29
  14    58  > > RETURN                                                   !4
  15    59*   > RETURN                                                   null
</pre>
          </p>
    </div>

    <div class="footer">
      <div class="container">
        <p class="text-muted">Copyleft &copy; 2014 stypr.</p>
      </div>
    </div>

  </body>
</html>


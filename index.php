<?php
// * Copyright 2012-2013 Melvin Carvalho and other contributors; Licensed LGPGv3

// Init
require_once('init.php');
header("Access-Control-Allow-Origin: *");

$destination = isset($_REQUEST['destination']) ? $_REQUEST['destination'] : 'http://melvincarvalho.com/#me';
$referrer    = isset($_REQUEST['referrer'])    ? $_REQUEST['referrer']    : 'http://taskify.org/';
$currency    = isset($_REQUEST['currency'])    ? $_REQUEST['currency']    : 'https://taskify.org/points#';
$amount      = isset($_REQUEST['amount'])      ? $_REQUEST['amount']      : '25';
$source      = isset($_REQUEST['source'])      ? $_REQUEST['source']      : 'https://taskify.org/me#';

// @TODO validation

// insert into DB
if ($amount > 0) {
  $r = Database::getInstance()->insert("insert into webcredits values ( 'https://d.taskify.org/c/transfer/', NULL, 'http://purl.org/commerce#Transfer', '$source', $amount, '$currency','$destination', NULL, NULL, '$referrer')");
}

// return total for day
$r = Database::getInstance()->select("select sum(amount) total from webcredits where destination = '$destination' and DATE(created) = DATE(NOW()) and currency = '$currency'");

if (isset($_REQUEST['destination'])) {
  echo $r[0]['total'] ;
  exit;
} else {

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
  <head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="http://twitter.github.com/bootstrap/assets/css/bootstrap.css" rel="stylesheet">
  <link href="http://twitter.github.com/bootstrap/assets/css/bootstrap-responsive.css" rel="stylesheet">
  <link href="http://code.jquery.com/ui/1.9.0/themes/base/jquery-ui.css" rel="stylesheet">
  <link href="libnotify.css" rel="stylesheet">
  <link href="todo.css" rel="stylesheet">
  <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
  <script src="http://code.jquery.com/jquery-1.8.2.js" type="text/javascript"></script>
  <script src="http://code.jquery.com/ui/1.9.0/jquery-ui.js" type="text/javascript"></script>
  <script src="http://twitter.github.com/bootstrap/assets/js/bootstrap.min.js" type="text/javascript"></script>

  <title>Dot Chart</title>
  <script src="raphael.js" type="text/javascript" charset="utf-8"></script>
  <script src="dots.js" type="text/javascript" charset="utf-8"></script>
  <link rel="stylesheet" href="demo.css" type="text/css" media="screen">
  <link rel="stylesheet" href="demo-print.css" type="text/css" media="print">
  <style type="text/css" media="screen">
    body {
      margin: 0;
    }
    #chart {
      color: #333;
      left: 50%;
      margin: -150px 0 0 -400px;
      position: absolute;
      top: 50%;
      width: 300px;
      width: 800px;
    }
  </style>
  </head>

  <body>
  <div class="navbar-wrapper">
  <!-- Wrap the .navbar in .container to center it within the absolutely positioned parent. -->
    <div class="container">

      <div class="navbar navbar-inverse">

        <div class="navbar-inner">
          <a style="font-family: Arial; font-style:italic; color:#0088CC" class="brand" href="/">Taski<b>f</b>y <sup>&alpha;</sup></a> &nbsp;
          <ul class="nav">
          </ul>
          <ul class="nav pull-right">
            <li class="dropdown">
            </li>

          </ul>
          <form class="form-inline pull-left" id="newtagform">
          </form>
          <div id="tags" class="btn-group pull-left">

          </div>
        </div>
      </div>

      <div>
        <form>
        <table method="POST">
          <tr><td>Source: </td><td><input name="source"/></td></tr>
          <tr><td>Amount: </td><td><input name="amount"/></td></tr>
          <tr><td>Currency: </td><td><input name="currency"/></td</tr>
          <tr><td>Destination: </td><td><input name="destination"/></td></tr>
          <tr><td>Comment: </td><td><input name="comment"/></td></tr>
        </table>
        <input type="submit" />
        </form>
      </div>

    </div>
  </div>

  </body>
</html>
<?php } ?>

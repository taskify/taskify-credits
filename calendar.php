<?php

// * Copyright 2012 Melvin Carvalho and other contributors; Licensed MIT

require_once('init.php');
header("Access-Control-Allow-Origin: *");


$referrer = $_REQUEST['referrer'];

$currency = $currency ? $currency : 'https://w3id.org/cc#bit';
$source = $source ? $source : 'https://taskify.org/me#';
$amount = $amount ? $amount : 25;
$destination = $destination ? $destination : 'https://melvincarvalho.com/#me';
$wallet1 = 'https://melvincarvalho.com/wallet/taskify.ttl#this';
$wallet2 = 'https://melvincarvalho.com/wallet/small.ttl#this';

if (!$date) {
  $sql = "select sum(amount) total, HOUR(timestamp) hour, DAYOFWEEK(timestamp) day from Credit where destination = '$destination' and currency = '$currency' and wallet in ('$wallet1', '$wallet2')  and DATE_SUB(NOW(),INTERVAL 167 HOUR) <= timestamp group by hour, day order by hour desc";
} else {
  $sql = "select sum(amount) total, HOUR(timestamp) hour, DAYOFWEEK(timestamp) day from Credit where destination = '$destination' and currency = '$currency' and wallet in ('$wallet1', '$wallet2')  and DATE_SUB(STR_TO_DATE('$date', '%Y%m%d'),INTERVAL 167 HOUR) <= timestamp and STR_TO_DATE('$date', '%Y%m%d') >= timestamp group by hour, day order by hour desc";
}


error_log($sql);

$st = $db->query($sql);
$r = $st->fetchAll(PDO::FETCH_ASSOC);


$tot = 0;
for ($i = 0; $i<count($r); $i++) {
  $o = $r[$i];
  //echo "$o[source] issued $o[amount] $o[currency] to $o[destination] at $o[created]<br/>";
  //print_r($o);
  //echo "<br/>";
  $arr[$o['day']][$o['hour']] = $o['total'];
  $tot += intval($o['amount']);
}

$sql = "select sum(amount) total from webcredits where destination = '$destination' and currency = '$currency' and DATE(NOW()) = DATE(created)";

//$st = $db->query($sql);
//$today = $st->fetchAll(PDO::FETCH_ASSOC);

//$today = Database::getInstance()->select();
//$today = $today[0]['total'] / 10.0;
$today = 0;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
  <head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
  <script src="http://code.jquery.com/jquery-1.8.2.js" type="text/javascript"></script>
  <script src="http://code.jquery.com/ui/1.9.0/jquery-ui.js" type="text/javascript"></script>
  <script src="http://twitter.github.com/bootstrap/assets/js/bootstrap.min.js" type="text/javascript"></script>

  <title>Dot Chart</title>

  </head>

    <body>
  <div class="navbar-wrapper">
  <!-- Wrap the .navbar in .container to center it within the absolutely positioned parent. -->
    <div class="container">

      <div class="navbar navbar-inverse">

        <div class="navbar-inner">
          <a style="font-family: Arial; font-style:italic; color:#0088CC" class="brand" href="/">Taski<b>f</b>y <sup>&alpha;</sup></a> &nbsp;
          <ul class="nav">
            <li id="about"><a href="/">Back</a></li>
          </ul>
          <ul class="nav pull-right">
            <li class="dropdown">
              <a id="user" target="_blank" href="#">Burndown Chart For: <?php echo $destination ?></b></a>
            </li>

          </ul>
          <form class="form-inline pull-left" id="newtagform">
<!--
          <div class="btn-group input-append">
            <input id="newtag" class="input-medium" size="32" type="text"><button class="btn btn-primary btn-mini"><i class="icon-plus-sign icon-white"></i><i class="icon-tag icon-white"></i></button>
          </div>
-->
          </form>
          <div id="tags" class="btn-group pull-left">

          </div>
<!--
          <div class="btn-group pull-right">
            <button type="button" id="ui-focus" class="btn btn-success">Focus</button>
          </div>
-->
        </div>
      </div>

    </div>
  </div>

        <table>
            <tfoot>
                <tr>
                    <td>&nbsp;</td>
                    <th>12am</th>
                    <th>1</th>
                    <th>2</th>
                    <th>3</th>
                    <th>4</th>
                    <th>5</th>
                    <th>6</th>
                    <th>7</th>
                    <th>8</th>
                    <th>9</th>
                    <th>10</th>
                    <th>11</th>
                    <th>12pm</th>
                    <th>1</th>
                    <th>2</th>
                    <th>3</th>
                    <th>4</th>
                    <th>5</th>
                    <th>6</th>
                    <th>7</th>
                    <th>8</th>
                    <th>9</th>
                    <th>10</th>
                    <th>11</th>
                </tr>
            </tfoot>
            <tbody>
                <tr>

<?php
$week = 0;
$days = array(0, 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
for ($day = 1; $day<8; $day++) {
  echo '</tr><tr>';
  $tot = 0;
  $str = '';
  for ($i=0; $i<24; $i++) {
    $str .= "<td><a target='_blank' href='pie.php?hour=$i'>" . $arr[$day][$i] / 10 . "</a></td>";
    $tot += $arr[$day][$i] / 10;
  }
  echo '<th title="'.$tot.'" scope="row">'.$days[$day].'</th>';
  echo $str;
  $week += $tot;
}


?>

                </tr>
            </tbody>
        </table>
        <div id="chart"></div>
        <p id="copy">Credits received by day and hour. Weekly : <?php echo $week  . ' Daily: ' . $today ?></p>

<script>
var dots = $('a').splice(3)
var d = new Date()
var day = d.getUTCDay()
var hour = d.getUTCHours()
var now = day * 24 + hour
var today = dots[now]
for (var i=0; i<168; i++) {
  var date = d.toISOString().substring(0,10)
  var index = ( 168 + now - i ) % 168
  var h = (index) % 24
  if (h === 0) {
    d.setDate(d.getDate() - 1)
  }
  console.log('h', h, 'date', date)
  var el = dots[index]
  el.href = 'pie.php?date=' + date + '&hour=' + h
  console.log(el)
}
</script>

    </body>
</html>

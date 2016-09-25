<!DOCTYPE html>
<html class="full" lang="en">
<head>
    <title>Alerts</title>
    <script src="https://code.jquery.com/jquery-3.1.1.js" integrity="sha256-16cdPddA6VdVInumRGo6IbivbERE8p7CQR3HzTBuELA=" crossorigin="anonymous"></script>
    <script src="jquery.csv.js"></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="./assets/js/bootstrap.min.js" defer></script>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Wesley Cheung">
    
    <!-- Bootstrap Core CSS -->
    <link href="./assets/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Core CSS -->
    <link href="./assets/css/font-awesome.min.css" rel="stylesheet" />
</head>
<body>
<div>

        
        

</div>
<br>
<p style="font-size: 200%;margin-left: 175px">Currently Tracking:</p>

<?php
    
    include("config.php");
    if(!isset($_POST['username'],$_POST['password'],$_POST['threshold'])) {
        echo("<center>Error: not logged in / fields invalid</center>");
        die();
    }
    $username = mysqli_real_escape_string($db,$_POST['username']);
    $password = mysqli_real_escape_string($db,$_POST['password']);
    
    //get and salt password
    $query = "select salt from user where username ='".$username."';";
    if(!($result = mysqli_query($db, $query)))
    {
        die();
    }
    $row = mysqli_fetch_array($result);
    $salt = $row['salt'];
    $hashedPass = hash("sha256", $salt.$password);
    
    //query for user identification
    $query = "select uid from user where username ='".$username."' and password='".$hashedPass."';";
    if(!($result = mysqli_query($db, $query)))
    {
            echo "Error";
    }
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    $count = mysqli_num_rows($result);
    // If result matched $myusername and $mypassword, table row must be 1 row
    if($count != 1) {
        echo "Not logged in";
        die();
    }
    $uid = $row['uid'];
    
    //get phone number
    $query = "select pnum from user where username ='".$username."';";
    if(!($result = mysqli_query($db, $query)))
    {
        die();
    }
    $row = mysqli_fetch_array($result);
    $phone = $row['pnum'];
    
    // get stocks owned
    $query = "select sid from owned where uid ='".$uid."';";
    if(!($result = mysqli_query($db, $query)))
    {
        printf("Error");
    }
    $stocks = "";
    if($row = mysqli_fetch_assoc( $result )){
        $stocks = "'".$row['sid']."'";
    }
    
    while ( $row = mysqli_fetch_assoc( $result ) )
    {
        $stocks = $stocks.",'".$row['sid']."'";
    }
    
    // get threshold check
    $threshhold = mysqli_real_escape_string($db,$_POST['threshold']);
    if(is_int((int)$threshhold)){
        $threshhold = (int)$threshhold;
    } else {
        $threshhold = 100;
    }
    
    // check if current threshold exists and alert if above
    $sendAlert = false;
    if(isset($_POST['currThreshold'])) {
        
        $currThreshold = mysqli_real_escape_string($db,$_POST['currThreshold']);
        
        if(is_int((int)$currThreshold)){
            
            if($threshhold < (int) $currThreshold ) {
            
                $sendAlert = true;
            
            }
            
        }
    }
        
    
    
    // close connection
    $db->close();
?>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/r/bs-3.3.5/jq-2.1.4,dt-1.10.8/datatables.min.css">
<script src="https://cdn.datatables.net/r/bs-3.3.5/jqc-1.11.3,dt-1.10.8/datatables.min.js"></script>
<script src="https://code.highcharts.com/stock/highstock.js"></script>
<script src="https://code.highcharts.com/stock/modules/exporting.js"></script>
<script src="https://www.blackrock.com/tools/api/js/hackathon"></script>
<style>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://test3.blackrock.com/tools/resources/css/api/main.css">
    <link rel="stylesheet" href="https://test3.blackrock.com/tools/resources/css/api/tables.css">
    <link rel="stylesheet" href="https://test3.blackrock.com/tools/resources/css/api/theme.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
    <script src="https://test3.blackrock.com/tools/resources/js/api/bootstrap.js"></script>
    <script src="https://www.blackrock.com/tools/resources/js/utils/StringUtil.js"></script>
    <script src="https://www.blackrock.com/tools/api/js/hackathon"></script>
table {
    font-family: arial, sans-serif;
    border-collapse: collapse;
    width: 100%;
}
td, th {
    border: 1px solid #dddddd;
    text-align: left;
    padding: 8px;
}
tr:nth-child(even) {
    background-color: #dddddd;
}
</style>

<div class="container">
  <h3 style="text-align:center;">
  Holdings
  </h3>
  <table id="holdings" class="table table-striped table-bordered" cellspacing="0" width="100%"></table>
</div>
<h4 style="text-align:center;">
  Performance ($10,000 Investment)
  </h4>
<div id="returns" style="height: 400px; min-width: 310px; min-height:400px; display:block;"></div>
<div class="container">
  <h4 style="text-align:center;">
  Risk
  </h4>
  <table>
    <tr>
        <th>Total Risk: </th>
        <th id="totalRisk">0</th>
    </tr>
</table>
</div>


<script>
var totalRisk;
$(function() {
  var Aladdin = new blk.API();
  var tickers = [<?php echo $stocks; ?>];
  var pos = "";
  var weight = 100/tickers.length;
  for (var i = 0; i < tickers.length; i++) {
        pos += tickers[i] + "~" + weight + "|";
    }
    pos = pos.substring(0,pos.length-1);
  Aladdin.portfolioAnalysis({
    calculateExposures: "true",
    calculatePerformance: "true",
    calculateRisk: "true",
    calculateExpectedReturns: "true",
    positions: pos,
    useCache: "true"
  }, function(data) {
    var portfolio = data.resultMap.PORTFOLIOS[0].portfolios[0];
    $('#holdings').DataTable({
      data: portfolio.holdings.map(function(holding) {
        return [holding.ticker, holding.description, holding.assetType, holding.pbRatio, holding.peRatio, holding.weight]
      }),
      columns: [{
        title: 'Ticker'
      }, {
        title: 'Description'
      }, {
        title: 'Asset Type'
      }, {
        title: "P/B Ratio"
    }, {
        title: "P/E Ratio"
    }, {
        title: 'Weight'
      }],
      order: [
        [0, 'desc']
      ]
    });
    $('#returns').highcharts('StockChart', {
      rangeSelector: {
        selected: 5
      },
      series: [{
        name: 'Portfolio',
        data: portfolio.returns.performanceChart.map(function(point) {
          return [point[0], point[1] * 10000]
        }),
        tooltip: {
          valueDecimals: 2
        }
      }]
    });
    $("#totalRisk").text(portfolio.riskData.totalRisk);
    totalRisk = portfolio.riskData.totalRisk;
  });
});
</script>


<form id="myform" name="thresholdForm" action="./update.php" method="post">
    <input type="hidden" name="currThreshold" value="">
    <input type="hidden" name="username" value=<?php echo $username?>>
    <input type="hidden" name="password" value=<?php echo $password?>>
    <input type="hidden" name="threshold" value=<?php echo $threshhold?>>
</form>


<?php
    
    
    // Required if your envrionment does not handle autoloading
    require __DIR__ . '/twilio-php-master/Twilio/autoload.php';
    // Use the REST API Client to make requests to the Twilio REST API
    use Twilio\Rest\Client;
    if($sendAlert === true){
    // Your Account SID and Auth Token from twilio.com/console
    $sid = 'AC587db681d006e09ff7583b7d0f548ef5';
    $token = '38adf22b4c2c4c2aea3f520b6355a219';
    $client = new Client($sid, $token);
    // Use the client to do fun stuff like send text messages!
    $client->messages->create(
    // the number you'd like to send the message to
        '+1'.$phone,
        array(
            // A Twilio phone number you purchased at twilio.com/console
            'from' => '+16786078046 ',
            // the body of the text message you'd like to send
            'body' => 'Your securities risk threshold for your portfolio ('.$stocks.') has been reached! Consider visiting BlackRock to secure your future!'
        )
    );
    }
?> 


<script>
setTimeout(function () { document.thresholdForm.currThreshold.value = totalRisk; document.getElementById("myform").submit(); }, 20*1000);
</script>

</html>
<?php
    
    include("config.php");
        
    if(!isset($_POST['username'],$_POST['password'],$_POST['stocks'],$_POST['threshold'])) {
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
    
    //get phone number
    $query = "select pnum from user where username ='".$username."';";
    if(!($result = mysqli_query($db, $query)))
    {
        die();
    }
    $row = mysqli_fetch_array($result);
    $phone = $row['pnum'];
    
    // get stocks of csv
    $stocks = mysqli_real_escape_string($db,$_POST['stocks']);
    
    // get threshold check
    $threshhold = mysqli_real_escape_string($db,$_POST['threshold']);
    
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
<p style="font-size: 200%"><center>Currently Tracking:</center></p>



<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/r/bs-3.3.5/jq-2.1.4,dt-1.10.8/datatables.min.css">
<script src="https://cdn.datatables.net/r/bs-3.3.5/jqc-1.11.3,dt-1.10.8/datatables.min.js"></script>
<script src="https://code.highcharts.com/stock/highstock.js"></script>
<script src="https://code.highcharts.com/stock/modules/exporting.js"></script>
<script src="https://www.blackrock.com/tools/api/js/hackathon"></script>
<style>
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
  <center>
  <input type="text" name="removeStock" value="" form="form" />
  <input type="submit" value="Remove Stock Ticker" form="form" />
  </center>
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
    var totalRisk = portfolio.riskData.totalRisk;
  });
});
</script>
<?php
// Required if your envrionment does not handle autoloading
require __DIR__ . '/twilio-php-master/Twilio/autoload.php';
// Use the REST API Client to make requests to the Twilio REST API
use Twilio\Rest\Client;
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
        'from' => '+16786078046',
        // the body of the text message you'd like to send
        'body' => 'Your securities have reached your risk threshhold!'
    )
);
?>
<script>
setTimeout(function () { window.location.reload(); }, 5*60*1000);
// just show current time stamp to see time of last refresh.
document.write(new Date());
</script>
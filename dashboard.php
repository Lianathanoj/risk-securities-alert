<!DOCTYPE html>
<html class="full" lang="en">
<head>
    <title>Dashboard</title>
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

<h1 style="font-size: 400%"><center>Dashboard</center></h1>
<hr>
<div>
    
    <?php
        include("config.php");
        
        if(!isset($_POST['username'],$_POST['password'])) {
            echo("<center>Error: not logged in</center>");
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
            echo "<center>Not logged in/invalid login</cemter>";
            die();
        }
        echo "<h2><center>Welcome ". $username."!</center></h2>";
        
        //check if stock as been added
        $uid = $row['uid'];
        
        if(isset($_POST['addStock']) && $_POST['addStock'] != ""){
            $addStock = mysqli_real_escape_string($db,$_POST['addStock']);
            $query = "INSERT INTO owned
                        VALUES ('$uid', '$addStock');";
            if(!($result = mysqli_query($db, $query)))
            {
                echo("<center>Error: Stock already under portfolio</center>");
            } else {
                echo("<center>Stock ".$_POST['addStock']." successfully added! </center>");
            }
            
        }
        
        //check if stock needs to be removed
        if(isset($_POST['removeStock']) && $_POST['removeStock'] != ""){
            $removeStock = mysqli_real_escape_string($db,$_POST['removeStock']);
            $query = "DELETE FROM owned
                        where uid='$uid' and sid='$removeStock';";
            if(!($result = mysqli_query($db, $query)))
            {
                echo("<center>Error: Invalid Stock</center>");
            } else {
                echo("<center>Stock ".$_POST['removeStock']." successfully removed! </center>");
            }
            
        }

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
        
        
        // close connection
        $db->close();
        ?>
        
        

</div>
<p style="font-size: 200%;margin-left: 175px">Currently owned securities:</p>



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

<br>
<hr>
<br>
<center>
 <form action="./update.php" method="post">
        <input type="hidden" name="username" value=<?php echo $username?>>
        <input type="hidden" name="password" value=<?php echo $password?>>
        <input type="text" name="threshold" placeholder="Threshold Risk" required>
        <input type="submit" value="Get Automated Alerts">
</form>
</center>

<br>
<hr>
<br>

<div>
    <p style="font-size: 200%;margin-left: 175px">Add Assets:</p>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://test3.blackrock.com/tools/resources/css/api/main.css">
    <link rel="stylesheet" href="https://test3.blackrock.com/tools/resources/css/api/tables.css">
    <link rel="stylesheet" href="https://test3.blackrock.com/tools/resources/css/api/theme.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
    <script src="https://test3.blackrock.com/tools/resources/js/api/bootstrap.js"></script>
    <script src="https://www.blackrock.com/tools/resources/js/utils/StringUtil.js"></script>
    <script src="https://www.blackrock.com/tools/api/js/hackathon"></script>

    <div class="container">
        <div class="input-group">
            <input id="search" type="search" class="form-control" placeholder="Search..." value="Blackrock">
        <span class="input-group-btn">
            <button class="btn btn-primary" type="button">Search</button>
          </span>
        </div>
        <input id="rows" type="number" class="form-control" placeholder="Max Results (optional)">
        <form id="form" name="myform" action="./dashboard.php" method="post">
            <input type="hidden" name="addStock" value="" >
            <input type="hidden" name="username" value="<?php echo $username ?>" >
            <input type="hidden" name="password" value="<?php echo $password ?>" >
            
            <input id="submit" type="submit" value="Add Top Stock">
            
        </form>
        <table id="displayTable" class="table table-striped table-bordered" cellspacing="0" width="100%"></table>
    </div>

    <script>
        // var tickers = []; // new Set();
        $(function() {
            var Aladdin = new blk.API({});
            var $table = $('#displayTable');
            var $rows = $('#rows');
            var $search = $('#search');
            var table;
            function submit(query, rows, skipColumns, columnOrder) {
                if (table) {
                    table.destroy();
                    table = null;
                }
                $table.html('<p>Searching...</p>');
                Aladdin.searchSecurities({
                    query: query,
                    rows: rows || 100,
                    useDefaultDocumentType: true,
                    useCache: true
                }, function(data) {
                    var securities = data.resultMap.SEARCH_RESULTS[0].resultList;
                    var skipCols = skipColumns || [];
                    var startingColumns = columnOrder || [];
                    var columns = [];
                    /*securities.forEach(function(sec) {
                     for (var field in sec) {
                     if ((typeof sec[field] === 'string' || typeof sec[field] === 'number') && startingColumns.includes(field) && !columns.includes(field)) {
                     columns.push(field);
                     }
                     }
                     });
                     columns = startingColumns.concat(columns.sort());*/
                    columns = startingColumns;
                    var tableData = securities.map(function(sec) {
                        return columns.map(function(col) {
                            return col == 'score' ? sec[col].toFixed(3) : sec[col] || '-';
                        });
                    });
                    $("#submit").click(function() {
                        var columnsTicker = ["ticker"];
                        var tableDataTicker = securities.map(function(sec) {
                            return columnsTicker.map(function(col) {
                                return col == 'score' ? sec[col].toFixed(3) : sec[col] || '-';
                            });
                        });
                        if (tableDataTicker.length != 0) {
                            // tickers.add(tableDataTicker[0][0]);
                            document.myform.addStock.value = tableDataTicker[0][0];
                            /*/!*<form id="tickerpost" action="dashboard.php" method="post" value>
                             </form>*!/*/
                        }
                        console.log(tableDataTicker[0][0]);
                    });
                    if (table) {
                        table.destroy();
                    }
                    $table.empty();
                    table = $table.DataTable({
                        data: tableData,
                        columns: columns.map(function(col) {
                            return {
                                title: StringUtil.camelToHuman(col) + '\n(' + col + ')'
                            }
                        }),
                        order: [
                            [0, 'desc']
                        ]
                    });
                });
            }
            var skipCols = ['@type', 'asOfDate', 'aladdinTicker', 'assetClass', 'countryCode', 'duration', 'effectiveDuration', 'expense', 'funFamilyNow', 'bcusip', 'bloombergId', 'bloombergTicker', 'country', 'fundFamilyName', 'fundId', 'gics1Sector', 'gics2IndustryGroup', 'gics3Industry', 'giccs4SubIndustry', 'gicsCode', 'inceptionDate', 'incomeYield', 'legalType', 'mappedDescription', 'mappedTicker', 'mappedType', 'marketCode', 'maturity', 'modelDuration', 'modifiedDuration', 'morningstarCategory', 'morningstarFundID', 'morningstarSecID', 'oad', 'pbRatio', 'peRatio', 'portfolioId', 'portfolioName', 'pricingCusip', 'prospectusNetExpenseRatio', 'returnOnAssets', 'returnOnEquity', 'riskCusip', 'secYield', 'secYieldEndDate', 'securityId', 'securityIdType', 'securityName', 'sedol', 'shareClassType', 'stdPerfAsOfDate', 'std'];
            var colOrder = ['score', 'description', 'ticker', 'assetType', 'availability', 'country', 'isin',];
            var searchTimeout;
            function delayedSearch(delay) {
                window.clearTimeout(searchTimeout);
                searchTimeout = window.setTimeout(submit.bind(this, $search.val(), $rows.val(), skipCols, colOrder), delay);
            }
            $search.on('input', delayedSearch.bind($search[0], 250));
            $('#submit').click(delayedSearch.bind($search[0], 0));
            submit($search.val(), $rows.val(), skipCols, colOrder);
        });
    </script>
    <!--
    <form>
        Search for a stock:<br>
        <input id="addstock" type="text" name="addstock" required>
        <input id="searchstock" type="button" value="Submit">
    </form>
    <p id="ifexist"> </p>
    <br>
    <form>
        <button id="add" name="add" type="button" disabled="disabled" onclick="addstock.php">Add</button>
    </form>
    <script>
    $(document).ready(function(){
        var searched = "";
        var stocks = [];
        $("#searchstock").click(function(){
            searched = $("#addstock").val();
            stocks = $.csv.toArray("topstocks.csv");
            if ($.inArray(searched, stocks) == -1) {
                $("#ifexist").text("This stock does not exist.");
            } else {
                $("#ifexist").text("Add " + searched + "?");
            }
        });
    });
    </script>
-->


</div>


</body>
</html>
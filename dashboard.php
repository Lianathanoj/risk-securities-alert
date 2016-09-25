<!DOCTYPE html>
<html class="full" lang="en">
<head>
    <title>Dashboard</title>
    <script src="https://code.jquery.com/jquery-3.1.1.js" integrity="sha256-16cdPddA6VdVInumRGo6IbivbERE8p7CQR3HzTBuELA=" crossorigin="anonymous"></script>
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
<div>
    <p style="font-size: 200%;margin-left: 175px">Currently Tracking:</p><br>

    <?php

    include("config.php");

    $username = mysqli_real_escape_string($db,$_POST['username']);
    $password = mysqli_real_escape_string($db,$_POST['password']);

    $query = "select salt from user where username ='".$username."';";

    if(!($result = mysqli_query($db, $query)))
    {
        die();
    }
    $row = mysqli_fetch_array($result);
    $salt = $row['salt'];


    $hashedPass = hash("sha256", $salt.$password);
    $query = "select uid from user where username ='".$username."' and password='".$hashedPass."';";

    if(!($result = mysqli_query($db, $query)))
    {
        echo "     Your registration FAILED\n";
        echo "Error: " . $query . "<br>" . $db->error;

    }
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

    $count = mysqli_num_rows($result);

    // If result matched $myusername and $mypassword, table row must be 1 row

    if($count != 1) {
        echo "Not logged in";
        die();
    }

    echo "<h2><center>Hello ". $username."</center></h2>";



    ?>

</div>

<div>
    <p style="font-size: 200%;margin-left: 175px">Add Assets:</p>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://test3.blackrock.com/tools/resources/css/api/main.css">
    <link rel="stylesheet" href="https://test3.blackrock.com/tools/resources/css/api/tables.css">
    <link rel="stylesheet" href="https://test3.blackrock.com/tools/resources/css/api/theme.css">
    <script src="https://code.jquery.com/jquery-2.2.3.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
    <script src="https://test3.blackrock.com/tools/resources/js/api/bootstrap.js"></script>
    <script src="https://www.blackrock.com/tools/resources/js/utils/StringUtil.js"></script>
    <script src="https://www.blackrock.com/tools/api/js/hackathon"></script>

    <div class="container">
        <div class="input-group">
            <input id="search" type="search" class="form-control" placeholder="Search..." value="Blackrock">
        <span class="input-group-btn">
            <button id="submit" class="btn btn-primary" type="button">Search</button>
          </span>
        </div>
        <input id="rows" type="number" class="form-control" placeholder="Max Results (optional)">
        <form action="dashboard.php" method="post">
            <input id="add" type="button" value="Add">
        </form>
        <table id="displayTable" class="table table-striped table-bordered" cellspacing="0" width="100%"></table>
    </div>

    <script>
        // var tickers = []; // new Set();
        function php_post() {

        }


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
                    $("#add").click(function() {
                        var columnsTicker = ["ticker"];
                        var tableDataTicker = securities.map(function(sec) {
                            return columnsTicker.map(function(col) {
                                return col == 'score' ? sec[col].toFixed(3) : sec[col] || '-';
                            });
                        });
                        if (tickers.length != 0) {
                            // tickers.add(tableDataTicker[0][0]);
                            document.myform.extra.value = tableDataTicker[0][0];

                            /*/!*<form id="tickerpost" action="dashboard.php" method="post" value>
                             </form>*!/*/
                        }
                        console.log(tickers);
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

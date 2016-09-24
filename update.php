<?php
    $servername = "<server ip>";
    $username = "<username";
    $password = "<password>";
    $dbname = "<databaseName>";
    
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    //instantiate phone number array
    $phoneNumbers = array();
    
    // get falling stocks array
    $fallingStocks;
    
    
    // query table here
    foreach ($fallingStocks as $stock){
        $sql ="select phoneNumber 
                from users 
                where uid in (select uid 
                                from owned 
                                where sid == ".$stock.")";
        if(!($result = mysqli_query($conn, $query)))
        {      
    
           printf("Error: %s\n", mysqli_error($conn));
           exit(1);
           
        } else {
            
            while ( $row = mysqli_fetch_assoc( $result ) )
            {

               foreach ($row as $key => $value)
               {
                     array_push($phoneNumbers, $value);
               }

            }
            
            
            
        }    
    }
    
    
    // sms alert all users with risk lower than wanted
    foreach ($phoneNumbers as $number){
        
        //alert user
        
        
    }
    
    // close connection
    $conn->close();
    
?>
<script>

var http = require("http");

http.createServer(function (request, response) {
   // Send the HTTP header 
   // HTTP Status: 200 : OK
   // Content Type: text/plain
   response.writeHead(200, {'Content-Type': 'text/plain'});
   response.end('Check your phone');
}).listen(8081);
console.log('Server running at http://127.0.0.1:8081/');

var twilio = require('twilio');
var client = twilio('AC587db681d006e09ff7583b7d0f548ef5', '38adf22b4c2c4c2aea3f520b6355a219');
 
// Sends the text message.

client.sendMessage({
  to: '6789107883', // My phone number
  from: '6786078046',
  body: 'Hello from Twilio!'
});


setTimeout(function () { window.location.reload(); }, 5*60*1000);
// just show current time stamp to see time of last refresh.
document.write(new Date());
</script>
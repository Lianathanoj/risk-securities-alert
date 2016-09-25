<?php

    include("config.php");
    
    
    //instantiate phone number array
    $phoneNumbers = array();
    
    
    // query table here
    foreach ($fallingStocks as $stock){
        $sql ="select pnum 
                from users 
                where uid in (select uid 
                                from owned 
                                where sid == ".$stock.")";
        if(!($result = mysqli_query($db, $query)))
        {      

           printf("Error: %s\n", mysqli_error($db));
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
    
    //compare risk and add if below threshhold
    
    
    // sms alert all users with risk lower than wanted
    foreach ($phoneNumbers as $number){
        
        
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
            '+1'.$number,
            array(
                // A Twilio phone number you purchased at twilio.com/console
                'from' => '+16786078046 ',
                // the body of the text message you'd like to send
                'body' => 'fk u jonathan!'
            )
        );
        
    }
    
    // close dbection
    $db->close();

?>

<?php

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
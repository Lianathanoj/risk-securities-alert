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
        '+16789107883',
        array(
            // A Twilio phone number you purchased at twilio.com/console
            'from' => '+16786078046 ',
            // the body of the text message you'd like to send
            'body' => 'fk u jonathan!'
        )
    );
    ?> 
<script>
setTimeout(function () { window.location.reload(); }, 5*60*1000);
// just show current time stamp to see time of last refresh.
document.write(new Date());
</script>
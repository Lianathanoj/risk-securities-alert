/*var server = app.listen(8081, function () {
 var host = server.address().address
 var port = server.address().port

 console.log("Example app listening at http://%s:%s", host, port)
 })*/


var mysql = require('mysql');

// create the connection to database
var connection = mysql.createConnection({host:'localhost', user: 'root', database: 'test', password: 'pass'});

/*// connection.connect();
 connection.connect(function(err, rows, fields) {
 if (err) {
 console.error('error connecting: ' + err.stack);
 return;
 }

 console.log('connected as id ' + connection.threadId);
 console.log(rows);
 // console.log(fields);

 });*/
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

connection.query("SELECT * FROM USER", function (err, rows, fields) {
    if (rows[0].username == 'mary') {
        client.sendMessage({
            to: '6789107883', // My phone number
            from: '6786078046',
            body: 'Hello from Twilio!'
        });
    }
});

connection.end();


// Sends the text message.



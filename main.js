// get the client
var mysql = require('mysql');

// create the connection to database
var connection = mysql.createConnection({host:'localhost', user: 'root', database: 'test', password: 'secret'});

// connection.connect();
connection.connect(function(err, rows, fields) {
    if (err) {
        console.error('error connecting: ' + err.stack);
        return;
    }

    console.log('connected as id ' + connection.threadId);
    console.log(rows);
    // console.log(fields);

});
/*console.log(connection);
// simple query
connection.query("SELECT * FROM `testdata`", function (err, rows, fields) {
  if (err) throw err;
  console.log(rows); // results contains rows returned by server
  console.log(fields); // fields contains extra meta data about results, if available
});*/

connection.end();
// with placeholder
// connection.query('SELECT * FROM `table` WHERE `name` = ? AND `age` > ?', ['Page', 45], function (err, results) {
  // console.log(results);
// });

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



var express = require('express');
var app = express();
var port = process.env.PORT || 3030;

var bodyParser = require('body-parser');
app.use(bodyParser.json());// support json encoded bodies
app.use(bodyParser.urlencoded({ extended: true }));//  support encoded bodies
var needle = require('needle');


var cron = require('node-cron');

cron.schedule('* * * * *', () => {
    console.log('in cron');
    var mysql = require('mysql');

    var con = mysql.createConnection({
        host: "localhost",
        user: "root",
        password: "2bQ3MsDDTJ",
        database: "rossoner_taxi"
    });
    var now = Math.floor(Date.now() / 1000)
    var ONE_HOUR = 60 * 60;
    var sql = 'update orders set deleted = 1 WHERE created_at >= now() + INTERVAL 1 HOUR;';
    con.query(sql, function (err, result) {
        console.log('deleted');
        if (err) throw err;
    });


});



console.log('listening ' + port);
app.listen(port);




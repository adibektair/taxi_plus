var express = require('express');
var app = express();
var port = process.env.PORT || 443;

var bodyParser = require('body-parser');
app.use(bodyParser.json());// support json encoded bodies
// app.use(bodyParser.json({ type: 'application/*+json' }))
app.use(bodyParser.urlencoded({ extended: true }));//  support encoded bodies
var needle = require('needle');

var firebase = require("firebase");
var config = {
    apiKey: "AIzaSyCyJY0Wu3OYyRFx2LHLXfk6_3xQ3lLZ7eo",
    authDomain: "taxiplus-bf8af.firebaseapp.com",
    databaseURL: "https://taxiplus-bf8af.firebaseio.com/",
    storageBucket: "gs://taxiplus-bf8af.appspot.com",
};
firebase.initializeApp(config);
var database = firebase.database();

var axios = require('axios');

var Fingerprint = require('express-fingerprint');
app.use(Fingerprint({
    parameters:[
        Fingerprint.useragent,
        Fingerprint.acceptHeaders,
        Fingerprint.geoip
    ]
}));


app.get('/chat', (req, res) => {
    res.setHeader('Access-Control-Allow-Origin', 'http://194.87.146.89');
    res.setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, PATCH, DELETE');
    res.setHeader('Access-Control-Allow-Headers', 'X-Requested-With,content-type');
    res.setHeader('Access-Control-Allow-Credentials', true);
    var messages = [];
    var client_phone = req.query.client;
    var driver_phone = req.query.driver;
    var x = driver_phone + ''  + client_phone;
    var y = x.replace(/\s/g, '');

    console.log('Message/' + y +'/chat');

    // 7123555555577476313354
    // firebase.database().ref('Message/7702864644477001235513/chat').once('value').then(function(snapshot) {
    firebase.database().ref('Message/' + y +'/chat').once('value').then(function(snapshot) {
        snapshot.forEach(function(childSnapshot) {
            var childData = childSnapshot.val();
            messages.push(childData);
        });
        res.send(messages);
    });
});


app.post('/send-message', (req, res) => {
    res.setHeader('Access-Control-Allow-Origin', 'http://194.87.146.89');
    res.setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, PATCH, DELETE');
    // res.setHeader('Access-Control-Allow-Headers', 'X-Requested-With,content-type');
    res.setHeader('Access-Control-Allow-Credentials', true);
    var messages = [];
    var client_phone = req.body.client;
    var driver_phone = req.body.driver;
    var message = req.body.message;


    firebase.database().ref('Message/' + driver_phone + client_phone +'/chat').once('value').then(function(snapshot) {
        var time = Date.now();
        var dateString = '"' + time + '"';
        console.log(dateString);
        if(snapshot.ref.repo.dataUpdateCount == 0 ){
            console.log('is null');
            var message_data = {
                from: client_phone,
                message: message,
                time: dateString
            };
            console.log(message_data);
            var newPostKey = firebase.database().ref().child('chat').push().key;
            console.log('key '  + newPostKey);
            var obj = {
                [newPostKey]: message_data,
            };

            firebase.database().ref('Message/' + driver_phone + client_phone).set({
                chat: obj,
                sender: client_phone,
                id : driver_phone + '' + client_phone
            }, function(error) {
                if (error) {
                    res.send('error');
                } else {
                    res.send('success');
                }
            });
        }
        else{

            var time = Date.now();
            var dateString = '"' + time + '"';
            var message_data = {
                from: client_phone,
                message: message,
                time: time.toString()
            };
            var newPostKey = firebase.database().ref().child('chat').push().key;
            console.log('key '  + newPostKey);
            var obj = {
                [newPostKey]: message_data,
            };
            var rootRef = firebase.database().ref();
            var storesRef = rootRef.child('Message/' + driver_phone + client_phone + '/chat');
            var newStoreRef = storesRef.push();
            newStoreRef.set(message_data, function(error) {
                if (error) {
                    res.send('error');
                } else {
                    res.send('success');
                }
            });


            // firebase.database().ref('Message/' + driver_phone + client_phone + '/chat').set(obj, function(error) {
            //     if (error) {
            //         res.send(error)
            //     } else {
            //         res.send('success');
            //     }
            // });
        }

    });
});

app.get('/', (req, res) => {
    // console.log(req.fingerprint.components.useragent.os.family);
    var mysql = require('mysql');

    var con = mysql.createConnection({
        host: "127.0.0.1",
        user: "root",
        password: "2bQ3MsDDTJ",
        database: "rossoner_taxi"
    });

    var id = req.query.auth_key;
    var sql = 'SELECT id FROM users WHERE id = ' + mysql.escape(id);
    con.query(sql, function (err, result) {

        var ip = req.ip;// + req.fingerprint.components.useragent.os.family + req.fingerprint.components.useragent.os.major + req.fingerprint.components.useragent.os.minor;
        console.log('KEY: ' + ip);

        if (err) throw err;
        var sql1 = "INSERT INTO referals (user_id, ip) VALUES ?";
        var values = [
            [result[0].id, ip]
        ];
        con.query(sql1, [values], function (err, result) {
            if (err) throw err;
            console.log("1 record inserted");
        });
    });

   // res.send(id);
   //  console.log(req.fingerprint.components.useragent);
   //  console.log(req.fingerprint.components.acceptHeaders);
   //  console.log(req.ip)
  //  var id = req.query.auth_key;
    if(req.fingerprint.components.useragent.os.family == "iOS"){
        res.redirect('https://itunes.apple.com/kz/app/taxi/id1447483851?mt=8');
    }else{
        res.redirect('https://play.google.com/store/apps/details?id=kz.taxiplus.ysmaiylbokeikhan.taxiplus');
    }

});




app.post('/verify-code', (req, res) => {
    // console.log(req.fingerprint.components.useragent.os.family);
    var mysql = require('mysql');

    var con = mysql.createConnection({
        host: "127.0.0.1",
        user: "root",
        password: "2bQ3MsDDTJ",
        database: "rossoner_taxi"
    });
    var phone = req.body.phone;
    var code = req.body.code;
    var ip = req.ip; // + req.fingerprint.components.useragent.os.family + req.fingerprint.components.useragent.os.major + req.fingerprint.components.useragent.os.minor;
    console.log('KEY: ' + ip);
    var sql = "SELECT * FROM referals WHERE ip = \'" + ip + "\'" ;
    con.query(sql, function (err, result) {
        if (err) throw err;
        var userId = 0;
        console.log(result);
        if(result != null){
            if(result.length > 0){
                userId = result[result.length - 1].user_id;
            }
        }
        console.log('\n' + phone + ' ' + code + ' user id ' + userId);
        var data = {
            "phone" : phone,
            "code": code,
            "user_id": userId
        }
        const config = {
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }

        axios.post('http://194.87.146.89/profile/account/verify-code/', data, config)
            .then(function (response) {
                res.send(response.data)

                console.log(response.data);
            })
            .catch(function (error) {
                var err = {"state" : "error"};
                res.send(err)

                console.log('Error \n' + error);
            });

    });

    // var id = req.query.auth_key;
    // var sql = 'SELECT id FROM users WHERE token = ' + mysql.escape(id);
    // con.query(sql, function (err, result) {
    //     var ip = req.ip + req.fingerprint.components.useragent.os.family + req.fingerprint.components.useragent.os.major + req.fingerprint.components.useragent.os.minor;
    //     if (err) throw err;
    //     var sql1 = "INSERT INTO referals (user_id, ip) VALUES ?";
    //     var values = [
    //         [result[0].id, ip]
    //     ];
    //     con.query(sql1, [values], function (err, result) {
    //         if (err) throw err;
    //         console.log("1 record inserted");
    //     });
    // });
    //
    // res.redirect('https://play.google.com/store/apps');
});


var bodyParser = require('body-parser');
app.use(bodyParser.json());// support json encoded bodies
app.use(bodyParser.urlencoded({ extended: true }));//  support encoded bodies
var needle = require('needle');


var cron = require('node-cron');
var mysql = require('mysql');

var con = mysql.createConnection({
    host: "127.0.0.1",
    user: "root",
    password: "2bQ3MsDDTJ",
    database: "rossoner_taxi"
});

cron.schedule('* * * * *', () => {
    console.log('in cron');

    axios.get('http://194.87.146.89/profile/account/autodelete/')
    axios.get('https://dissertation.rossonero.kz/api/deside')


});



console.log('listening ' + port);
app.listen(port);




// var http = require('http');
//
// http.createServer(function (req, res) {
//     res.writeHead(200, {'Content-Type': 'text/plain'});
//     res.end('Hello World!');
// }).listen(3000);



// var express = require('express');
// var app = express();
// var port = process.env.PORT || 8080;
//
// var bodyParser = require('body-parser');
// app.use(bodyParser.json());// support json encoded bodies
// app.use(bodyParser.urlencoded({ extended: true }));//  support encoded bodies
// var needle = require('needle');
// /*
// app.get('/api/superman', function(req, res) {
//     //var ip = req.connection.remoteAddress;
//     res.send('aaa');
//     console.log('aaa');
//
// });
// */
//   app.post('/notes', (req, res) => {
//     res.send('Hello')
//   });
//
//
// console.log('listening ' + port);
// app.listen(port);
//
//
//
// //var app = require('express')();
// //var http = require('http').Server(app);
// //var io = require('socket.io')(http);
// //
// //
// //
// //
// //var MySQLEvents = require('mysql-events');
// //var dsn = {
// //  host:     '127.0.0.1',
// //  user:     'root',
// //  password: 'root',
// //  database: 'taxi_plus'
// //};
// //
// //var mysqlEventWatcher = MySQLEvents(dsn);
// //
// //var watcher =mysqlEventWatcher.add(
// //  'taxi_plus',
// //  function (oldRow, newRow, event) {
// //     //row inserted
// //    if (oldRow === null) {
// //
// //    }
// //
// //     //row deleted
// //    if (newRow === null) {
// //      //delete code goes here
// //    }
// //
// //     //row updated
// //    if (oldRow !== null && newRow !== null) {
// //      //update code goes here
// //    }
// //
// //    //detailed event information
// //      console.log(newRow);
// //    console.log(event)
// //  }
// //
// //);
// //
// //
// //
// //
// //
// //
// //
// app.get('/', function(req, res){
//   res.sendFile(__dirname + '/index.html');
// });
// //
// //http.listen(3000, function(){
// //  console.log('listening on *:3000');
// //});
// //
// //
// //io.on('connection', function(socket){
// //    console.log('a user connected');
// //    socket.on('disconnect', function(){
// //    console.log('user disconnected');
// //  });
// //});
// //io.on('connection', function(socket){
// //  socket.on('chat message', function(msg){
// //    console.log('message: ' + msg);
// //  });
// //});
// //io.on('connection', function(socket){
// //  socket.on('chat message', function(msg){
// //    io.emit('chat message', msg);
// //  });
// //});

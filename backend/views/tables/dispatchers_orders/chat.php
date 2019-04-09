<?php
?>
<!-- ENGINE -->
<script type="text/javascript" src="/profile/files/js/plugins/notifications/sweet_alert.min.js"></script>
<script type="text/javascript" src="/profile/files/js/pages/form_layouts.js"></script>


<?=$this->render("/layouts/header/_header", array("model" => $model))?>

<div class="content">
    <form id = "form">
        <div class="panel panel-flat">
            <div class="panel-body">
                <div class="col-md-12" id="chat">

                </div>
                <div class="col-md-11">
                    <input type="text" id="message" class="form-control" placeholder="Ваше сообщение">
                </div>
                <div class="col-md-1">
                    <button class="btn btn-success" onclick="sendMessage()" type="button">Отправить</button>
                </div>
            </div>
    </form>
</div>
<script>

    function sendMessage() {
        var message = document.getElementById('message').value;
        var input = document.getElementById('message');
        input.value = "";
        var data = {
            client: <?=$me?>,
            driver: <?=$driver?>,
            message: message
        };
        console.log(data);

        $.post("http://185.236.130.126:443/send-message", //Required URL of the page on server
            data,function(response,status){ // Required Callback Function
            });

    }
    var appended = [];
    $(document).ready(function() {
        appended = [];
        console.log('onload');
        timedText();
        // myTimeout1();
    });

    function timedText() {
        console.log('function');
        setInterval(myTimeout1, 1000)
    }

    function myTimeout1() {
        var refresh = false;
        $.ajax({
            dataType: "json",
            type: "GET",
            url: "http://194.87.146.89:443/chat?client=<?=$me?>&driver=<?=$driver?>",
            // url: "http://194.87.146.89:443/chat?client=77476313354&driver=71235555555",
            cache: true,
            contentType: false,
            crossDomain: true,
            processData: false,
            success: function (data) {
                console.log(data.length);
                var div = document.getElementById('chat');
                var add = document.createElement('div');
                add.classList.toggle('col-md-12');
                var append = false;
                for(var i = 0; i<data.length; i++){
                    var messageDiv = document.createElement('div');
                    var p = document.createElement('h6');

                    var text = document.createElement('label');
                    text.classList.toggle('text-semibold');
                    console.log('driver ' + <?=$driver?>);
                    if(data[i].from === "71235555555"){
                        p.innerText = "<?=$driver?>:";
                        messageDiv.classList.toggle('text-left');
                    }else{
                        p.innerText = "You:";
                        messageDiv.classList.toggle('text-right');
                    }
                    text.innerText = data[i].message;
                    if(appended.includes(data[i].time)){

                    }else{
                        messageDiv.appendChild(p);
                        messageDiv.appendChild(text);
                        add.appendChild(messageDiv);
                        appended.push(data[i].time);
                        refresh = true;
                    }


                }
                if(refresh){
                    div.appendChild(add);
                }



            },
        }).fail(function (xhr) {
            console.log('err ' + xhr.responseText);
        });
    }



</script>
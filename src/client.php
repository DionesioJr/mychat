<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <title>Client | </title>
</head>

<body>

    <div style="background: mintcream;">

        <p>https://github.com/nielsbaloe/webrtc-php/blob/master/index.html</p>
        <video width="320" height="240" ></video>
        <audio width="320" height="240" controls ></audio>
        <!-- <video  width="320" height="240" controls autoplay></video> -->

        <script>
            navigator.getMedia = (navigator.getUserMedia ||
                navigator.webkitGetUserMedia ||
                navigator.mozGetUserMedia ||
                navigator.msGetUserMedia);


            navigator.getMedia({
                    video: true,
                    audio: false
                },
                function(stream) {
                    var video = document.querySelector('video');
                    video.srcObject = stream;

                    var audio = document.querySelector('audio');
                    audio.srcObject = stream;
                    // video.src = vendorUrl.createObjectURL(stream);
                    // video.src = localMediaStream;
                    video.play();

                    // video.onloadedmetadata = function(e) {
                    //     // Faz algo com o vídeo aqui.
                    //      console.log(e);
                    // };

                },
                function(error) {
                    console.log(error);
                }
            );
        </script>

    </div>

    <div style="margin-top: 15px;">

        <span>Informe seu nome: </span><input type="text" name="name" id="name" value="Dionésio Guerra">
        <br>
        <span>Canal 1 </span><button onclick="subscribe('canal1')">Entrar</button>
        <br>
        <span>Canal 2 </span><button onclick="subscribe('canal2')">Entrar</button>
        <br><br>
        <textarea name="chat" id="chat" cols="30" rows="10"></textarea>
        <br>
        <input type="text" name="mesage" id="mesage">
        <input type="button" value="Enviar" id="send" name="send"">

    </div>

</body>

<script>

        var conn = new WebSocket('ws://localhost:8080')
        conn.onopen = function(e) {
            console.log(" Connection established!"); }; conn.onmessage=function(e) { console.log(e.data); data=JSON.parse(e.data); if(data.message){ $('#chat').append(data.message+'&#10;'); } }; $("#send").click(function(){ let mesage=document.getElementById('mesage'); sendMessage(mesage.value); }); function subscribe(channel) { conn.send(JSON.stringify({command: "subscribe" , channel: channel})); } function sendMessage(msg) { conn.send(JSON.stringify({command: "message" , message: msg})); } </script>

</html>
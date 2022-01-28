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
    <div>

        <span>Informe seu nome: </span><input type="text" name="name" id="name" value="Dionésio Guerra">
        <br><br>
        <textarea name="chat" id="chat" cols="30" rows="10"></textarea>
        <br>
        <input type="text" name="mesage" id="mesage">
        <input type="button" value="Enviar" id="send" name="send"">

    </div>

</body>

<script>
    $(document).ready(function() {

        var conn = new WebSocket('ws://localhost:8080')
        conn.onopen = function(e) {
            console.log("Connection established!");
        };

        conn.onmessage = function(e) {
            console.log(e.data);
            $('#chat').append(e.data+'&#10;');
        };

        $("#send").click(function(){
            let mesage = document.getElementById('mesage');
            conn.send(mesage.value);
        })

    })


</script>

</html>
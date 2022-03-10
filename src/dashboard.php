<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>


    <title>Dashboard | </title>
</head>

<body>
    <div>

        Total de usuarios connectados: <span id="total_users">0</span>

    </div>

</body>

<script>
    var conn = new WebSocket('ws://localhost:8080')
    conn.onopen = function(e) {
        console.log("Connection established!");
    };

    conn.onmessage = function(e) {
        console.log(e.data);
        setter(e.data);
    };


    function setter(data) {

        data = JSON.parse(data);

        if (!data.settings) {
            return false;
        }

        if (data.settings.total_users > 0) {
            data.settings.total_users = data.settings.total_users - 1;

        }

        $("#total_users").html(data.settings.total_users);
    }
</script>

</html>
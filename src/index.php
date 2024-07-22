<html>
    <head>
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title></title>
        <link rel="stylesheet" href="/style.css">
        <script src="/script.js"></script>
    </head>
    <body>
        <h1>AnonBBS</h1>
        <p>AnonBBS is privacy and security focused BBS. <a href="https://github.com/mgmgprndev/anonbbs">GitHub</a></p>

        <p>Make new Chat Room:</p>
        <button onclick="createRoom(this);">New Room</button>

        <br>
        <p>Generated Public Keys and Room Key (Please Save!)</p>

        <input id="rmk" type="text" placeholder="ROOM KEY" style="width: 100%;" readonly>
        <div style="display: flex; flex-direction: row; width: 100%; min-height: 200px;">
            <textarea id="pbk" style="width: 50%; min-height: 200px; height: 100%; resize: vertical;" readonly placeholder="PUBLIC KEY"></textarea>
            <textarea id="prk" style="width: 50%; min-height: 200px; height: 100%; resize: vertical;" readonly placeholder="PRIVATE KEY"></textarea>
        </div>

        <br><br><br>

        <p>Or Join To Existing Chat Room:</p>
        <input type="text" id="roomkeyjoin"  placeholder="ROOM KEY">
        <button onclick="joinroom(this)">Join</button>
    </body>
</html>
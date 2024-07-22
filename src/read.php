<?php

$roomkey = isset($_GET["roomkey"]) ? $_GET["roomkey"] : "";

if($roomkey == ""){
    echo "???????";
    exit;
}

require $_SERVER['DOCUMENT_ROOT'] . '/util.php';

$tr = ThreadTable::where("roomkey", $roomkey)->first();
if(!$tr){
    echo "???????";
    exit;
}

?>
<html>
    <head>
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title></title>
        <link rel="stylesheet" href="/style.css">
        <script src="/script.js"></script>
    </head>
    <body>
        <p>Input your Public key and Private Key</p>
        <input id="rmk" type="text" placeholder="ROOM KEY" style="width: 500px;" value="<?php echo $roomkey; ?>" readonly>

        <div style="display: flex; flex-direction: row; width: 500px; min-height: 200px;">
            <textarea id="pbk" style="width: 50%; min-height: 200px; height: 100%; resize: vertical;" placeholder="PUBLIC KEY"></textarea>
            <textarea id="prk" style="width: 50%; min-height: 200px; height: 100%; resize: vertical;" placeholder="PRIVATE KEY"></textarea>
        </div>

        <br>
        
        <button style="width: fit-content;" onclick='reload();'>Fetch/Reload Comments</button>
        
        <br>

        <textarea id="chattxt" style="width: 500px; height: 500px;" placeholder="Chat"></textarea>

        <!-- <div style="display: flex; flex-direction: row; gap:1px; width:500px;">
            <p style="width: 25%;">ページ</p>

            <button style="width: 25%;">PREV</button>

            <input id="page" type="number" style="width: 25%;"/>

            <button style="width: 25%;">NEXT</button>

        </div> -->

        <input    oninput="updateMsgToSend()" id="nkn" type="text" style="width: 500px;" placeholder="NAME">
        <textarea oninput="updateMsgToSend()" id="msg" style="width: 500px; height: 150px;" placeholder="MESSAGE"></textarea>

        <textarea id="msgtosend" style="width: 500px; height: 150px; display: none;" placeholder="MESSAGE"></textarea>

        <script>
            function formatUTCDate() {
                const options = { 
                    weekday: 'short', 
                    month: 'short', 
                    day: 'numeric', 
                    year: 'numeric', 
                    hour: 'numeric', 
                    minute: 'numeric', 
                    hour12: true, 
                    timeZone: 'UTC' 
                };
                const now = new Date();
                const formattedDate = now.toLocaleDateString('en-US', options);
                return formattedDate.replace(/, /g, ' ').replace(',', '');
            }           

            function updateMsgToSend(){
                var str = "[" + document.getElementById('nkn').value + "]-[" + formatUTCDate() + "]\n";
                var msg = document.getElementById('msg').value.split("\n");
                msg.forEach(i => {
                    str += "* " + i + "\n";
                });
                document.getElementById('msgtosend').value = str; 
                return str;
            }

            async function send(i){
                var arrEn = await encrypt( updateMsgToSend(), document.getElementById('pbk').value );

                fetch('/api/new-comment.php', {
                    method: 'POST',
                    headers: {
                      'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                      roomkey: "<?php echo $roomkey; ?>",
                      text: arrayBufferToBase64( arrEn )
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if(data.status=="success"){
                        reload();
                    }else {
                        alert("Failed To Submit");
                    }
                })
                .catch(error => console.error('Error:', error));
            }

            async function reload(){
                fetch('/api/get-comment.php', {
                    method: 'POST',
                    headers: {
                      'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                      roomkey: "<?php echo $roomkey; ?>"
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if(data.status=="success"){
                        document.getElementById('chattxt').value = "";
                        data.comments.forEach(i => {
                            console.log(i.text);
                            addComment(i.text);
                        });
                    }else {
                        alert("Failed To Fetch");
                    }
                })
                .catch(error => console.error('Error:', error));
            }

            async function addComment( i ){
                var b6 = base64ToArrayBuffer( i );
                var arrDe = await decrypt( b6, document.getElementById('prk').value );
                document.getElementById('chattxt').value += arrDe + "\n";
            }
        </script>

        <button style="width: 500px;" onclick="send(this)">Post Comment (And Reload)</button>
    </body>
</html>
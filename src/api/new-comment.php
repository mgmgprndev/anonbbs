<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    require $_SERVER['DOCUMENT_ROOT'] . '/util.php';

    $roomkey = isset($data["roomkey"]) ? $data["roomkey"] : "";
    $text = isset($data["text"]) ? $data["text"] : "";

    if( $roomkey == "" || $text == "" ){
        echo json_encode([
            'status' => 'error'
        ]);
        exit;
    }

    $thread = ThreadTable::where("roomkey", $roomkey)->first();
    if(!$thread){
        echo json_encode([
            'status' => 'error'
        ]);
        exit;
    }

    $comment = new CommentTable();
    $comment->threadid = $thread->id;
    $comment->text = $data["text"];
    $comment->save();

    echo json_encode([
        'status' => 'success'
    ]);
} else {
    echo json_encode(['status' => 'error']);
}


?>
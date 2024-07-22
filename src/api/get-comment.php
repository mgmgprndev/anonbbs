<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    require $_SERVER['DOCUMENT_ROOT'] . '/util.php';

    $roomkey = isset($data["roomkey"]) ? $data["roomkey"] : "";

    if( $roomkey == ""){
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

    $comments = CommentTable::where('threadid', $thread->id)->get();
    $data = [
        'status' => 'success',
        'comments' => []
    ];

    foreach ($comments as $comment){
        $data["comments"][] = [
            'text' => $comment->text
        ];
    }

    echo json_encode($data);
} else {
    echo json_encode(['status' => 'error']);
}


?>
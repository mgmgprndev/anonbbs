<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    require $_SERVER['DOCUMENT_ROOT'] . '/util.php';

    $roomkey = isset($data["roomkey"]) ? $data["roomkey"] : "";

    if( $roomkey == "" ){
        echo json_encode([
            'status' => 'error'
        ]);
        exit;
    }

    $thread = new ThreadTable();
    $thread->roomkey = $roomkey;
    $thread->save();

    echo json_encode([
        'status' => 'success'
    ]);
} else {
    echo json_encode(['status' => 'error']);
}


?>
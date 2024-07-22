<?php
require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Eloquent\Model;



$capsule = new Capsule;


$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => '127.0.0.1',
    'database'  => 'anonbbs',
    'username'  => 'anonbbs',
    'password'  => 'anonbbs',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);


$capsule->setAsGlobal();
$capsule->bootEloquent();


class ThreadTable extends Model
{
    protected $table = 'threads';
}

class CommentTable extends Model
{
    protected $table = 'comments';
}

function secure_hash($str){
    $algo = ["ripemd320", "whirlpool", "snefru", "gost", "sha384", "sha256", "sha512"];
    $str = base64_encode(rawurlencode($str));
    foreach ($algo as $a){
        $str .= hash($a, base64_encode(rawurlencode($str)));
        $str = hash("sha256", base64_encode(rawurlencode($str)));
    }
    return hash("sha256", base64_encode(rawurlencode($str)));
}
<?php
$uploaddir = dirname(dirname( dirname(__FILE__) )).'/uploads/';
$file = $uploaddir . basename($_FILES['uploadfile']['name']);

if (move_uploaded_file($_FILES['uploadfile']['tmp_name'], $file)) {
    echo "success";
} else {
    echo "error";
    var_dump($uploaddir);
    var_dump($_FILES['tmp_name']);
}
?>
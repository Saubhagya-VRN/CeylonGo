<?php
function view($name, $data = []) {
    extract($data);
    require "../views/$name.php";
}
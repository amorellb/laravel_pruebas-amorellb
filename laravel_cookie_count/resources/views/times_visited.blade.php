<?php
    $userName = $name ?? 'Bernat';
if (isset($_COOKIE['cookie_counter'])) {
    setcookie('cookie_counter', ++$_COOKIE['cookie_counter']);
    echo "Hello, $userName. You have visited our website " . $_COOKIE['cookie_counter'] . " times";
} else {
    setcookie('cookie_counter', 1, time() + 1200);
    echo "Hello, $userName. You have visited our website 1 times";
}

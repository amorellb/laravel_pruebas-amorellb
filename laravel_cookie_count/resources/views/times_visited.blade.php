<?php
if (isset($_COOKIE['cookie_counter'])) {
    setcookie('cookie_counter', ++$_COOKIE['cookie_counter']);
    echo "You have visited our website " . $_COOKIE['cookie_counter'] . " times";
} else {
    setcookie('cookie_counter', 1, time() + 1200);
    echo "You have visited our website " . $_COOKIE['cookie_counter'] . " times";
}

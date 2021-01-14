<?php


function notify_admin($message, $only_prod = true) {
    if (config('app.env') != 'production' && $only_prod) {
        return 0;
    }

    $url = "https://echo.gufoe.it/PnwDXj1PCx?".http_build_query([
        'msg' => $message,
    ]);
    $bytes = @file_get_contents($url) ? 'ok' : 'fail';

    $url = "https://echo.gufoe.it/FvG3w7IE8T?".http_build_query([
        'msg' => $message,
    ]);
    $bytes = @file_get_contents($url) ? 'ok' : 'fail';

    return $bytes;
}

<?php

function array_to_obj(array $array = []) {
    return json_decode(json_encode($array));
}

function valid_sql_date($date) {
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

function app_asset_path($path) {
    return url('/app-assets/'.$path);
}

function asset_path($path) {
    return url('/assets/'.$path);
}

function random_alpha_string($length = 10) {
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function random_alpha_num_string($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function response_success(array $data = []) {
    $response = array_merge(['success' => true], $data);
    return response()->json($response, 200);
}

function response_failure(array $data = []) {
    $response = array_merge(['success' => false], $data);
    return response()->json($response, 200);
}

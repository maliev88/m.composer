<?php

$url = 'http://192.168.229.197:9000/b2b-test/test1.jpg';
$filePath = 'C:/Users/komarevtsev_am/Work/Projects/ngp/Packages/Marvel.Infrastructure/Marvel.Infrastructure.FileStorage.DemoApp/Programming.jpg';

$headers = [
    'Content-Type: image/jpeg',
    'X-Amz-Content-Sha256: ••••••', // замените на фактическое значение
    'X-Amz-Date: ••••••', // замените на фактическое значение
    'Authorization: ••••••', // замените на фактическое значение
];

// Читаем содержимое файла
$fileData = file_get_contents($filePath);
if ($fileData === false) {
    die("Не удалось прочитать файл");
}

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POSTFIELDS, $fileData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$err = curl_error($ch);

curl_close($ch);

if ($err) {
    echo "cURL Error: " . $err;
} else {
    echo "Ответ сервера: " . $response;
}

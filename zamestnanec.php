<?php
require_once "includes/db_inc.php";
$employee_id = (int) ($_GET['employee_id'] ?? 0);

$id = filter_input(INPUT_GET,
    'employee_id',
    FILTER_VALIDATE_INT,
    ["options" => ["min_range"=> 1]]
);

if ($id === null || $id === false) {
    http_response_code(400);
    $status = "bad_request";
} else {
    $stmt = $pdo->prepare("SELECT employee.surname, employee.name, employee.job, employee.room,employee.wage, employee.employee_id, room.room_id, room.phone, room.name as roomName  From employee  INNER JOIN room ON employee.room=room.room_id WHERE employee_id=:employee_id");
    $stmt -> execute(['employee_id' => $employee_id]);
    $stmt->execute(['employee_id' => $id]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        $status = "not_found";
    } else {
        $row = $stmt->fetch();
        $status = "ok";


    }
}
?>
<!DOCTYPE html>

<html>
<head>
    <meta charset="UTF-8">
    <!-- Bootstrap-->
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <title>
        <?php
        if($status == "ok"){
            echo("{$row -> surname} {$row -> name}");
        }
        else{
            $httpCode = http_response_code();
            echo("Chybový kód: {$httpCode}");
        }
        ?>
    </title>
</head>
<body class="container">

<?php

switch ($status) {
    case "bad_request":
        echo "<h1>Error 400: Bad request</h1>";
        break;
    case "not_found":
        echo "<h1>Error 404: Not found</h1>";
        break;

}
$prvniZeJmena = "";
$prvniZeJmena .= mb_substr($row->name, 0, 1);
if ($status == 'ok'){
    echo "<h1> Karta osoby: $row->surname $prvniZeJmena. </h1>";
    echo "<dl  class='dl-horizontal'>";
    echo "<dt>Jméno</dt><dd>".htmlspecialchars($row->name)."</dd>";
    echo "<dt>Přijímení</dt>";
    echo "<dd>". htmlspecialchars($row ->surname) ."</dd>";
    echo "<dt>Pozice</dt>";
    echo "<dd>". htmlspecialchars($row -> job) ."</dd>";
    echo "<dt>Mzda</dt>";
    echo "<dd>". htmlspecialchars(number_format($row -> wage,2)) ."</dd>";
    echo "<dt>Místnost</dt>";
    echo "<dd>". htmlspecialchars($row -> roomName) ."</dd>";
    $stmt = $pdo ->prepare("SELECT room.name, room.room_id FROM `key` klic JOIN room ON klic.room = room.room_id WHERE klic.employee =:employeeId ORDER BY room.name");
    $stmt->execute(['employeeId' => $id]);
    if($stmt ->fetch() === 0){
        echo("<dt>Klíče</dt><dd>—</dd>");
    }
    else{
        echo("<dt>Klíče</dt>");
        while($room = $stmt->fetch()){

            echo("<dd><a href='room.php?roomId={$room->room_id}'>{$room->name}</a></dd>");
        }
    }

    echo "</dl>";
    echo "<a href='zamestnanci.php'><span class='glyphicon glyphicon-arrow-left' aria-hidden='true'></span> Zpět na seznam zaměstnanců<a/>";
}




unset($stmt);
?>
</body>
</html>

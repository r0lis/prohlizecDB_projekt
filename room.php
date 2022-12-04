<?php
    require_once "includes/db_inc.php";
    $roomId = (int) ($_GET['room_id'] ?? 0);

$id = filter_input(INPUT_GET,
    'roomId',
    FILTER_VALIDATE_INT,
    ["options" => ["min_range"=> 1]]
);


if ($id === null || $id === false) {
    http_response_code(400);
    $status = "bad_request";
} else {

    $stmt = $pdo->prepare("SELECT * FROM room WHERE room_id=:roomId ");
    $stmt -> execute(['roomId' => $roomId]);

    $stmt->execute(['roomId' => $id]);
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
            echo("Místnost č. {$row -> no}");
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

if ($status == 'ok'){
    echo "<h1>Místnost č. $row->no </h1>";
    echo "<dl>";
    echo "<dt>Číslo</dt>";
    echo "<dd>" .htmlspecialchars($row->no)."</dd>";
    echo "<dt>Název</dt>";
    echo "<dd>". htmlspecialchars($row ->name) ."</dd>";
    echo "<dt>Telefon</dt>";
    echo "<dd>". htmlspecialchars($row -> phone) ."</dd>";
    $stmt = $pdo->prepare("SELECT employee.name, employee.surname, employee.wage, employee.employee_id FROM employee INNER JOIN room ON room.room_id =:roomId AND room.room_id = employee.room ");
    $stmt->execute(['roomId' => $id]);
    if ($stmt->rowCount() === 0) {
        echo("<dt>Zaměstnanci</dt><dd>—</dd><dt>Průměrná mzda</dt><dd>—</dd>");

    } else {
        $employeeCount = $stmt->rowCount();
        $wageSum = 0;
        $averageWage = 0;
        echo("<dt>Lidé</dt>");
        while($row = $stmt->fetch()){
            $firstLetterOfName = mb_substr($row -> name,0,1);
            echo("<dd><a href='zamestnanec.php?employee_id={$row->employee_id}'>{$row->surname} {$firstLetterOfName}.</a></dd>");
            $wageSum += $row -> wage;
        }
        $averageWage = $wageSum / $employeeCount;
        $averageWage = number_format($averageWage,2);
        echo("<dt>Průměrná mzda</dt><dd>{$averageWage}</dd>");
    }
    $stmt = $pdo->prepare("SELECT employee.name, employee.surname, employee.employee_id FROM `key` klice JOIN employee ON klice.employee = employee.employee_id WHERE klice.room =:roomId ORDER BY employee.surname; ");
    $stmt->execute(['roomId' => $id]);
    if($stmt -> rowCount() === 0){
        echo("<dt>Klíče</dt><dd>—</dd>");
    }
    else{
        echo("<dt>Klíče</dt>");
        while($row = $stmt->fetch()){
            $firstLetterOfName = mb_substr($row -> name,0,1);
            echo("<dd><a href='zamestnanec.php?employee_id={$row->employee_id}'>{$row->surname} {$firstLetterOfName}.</a></dd>");
        }
    }
    echo "</dl>";
    echo "<a href='rooms.php'><span class='glyphicon glyphicon-arrow-left' aria-hidden='true'></span> Zpět na seznam místností<a/>";


}




unset($stmt);
?>
</body>
</html>

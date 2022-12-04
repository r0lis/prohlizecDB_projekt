<?php
$columns = array('surname','roomName','phone','job');
$column = isset($_GET['column']) && in_array($_GET['column'], $columns) ? $_GET['column'] : $columns[0];
$sort_order = isset($_GET['order']) && strtolower($_GET['order']) == 'desc' ? 'DESC' : 'ASC';
?>

<html>
<head>
    <meta charset="UTF-8">
    <!-- Bootstrap-->
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <title>Seznam Zaměstnanců</title>
</head>
<body class="container">
<?php

require_once "includes/db_inc.php";

$stmt = $pdo->query('SELECT employee.surname, employee.name, employee.job, employee.room, employee.employee_id, room.room_id, room.phone, room.name as roomName FROM employee INNER JOIN room ON employee.room=room.room_id ORDER BY ' . $column . ' ' . $sort_order );

if ($stmt->rowCount() == 0) {
    echo "Záznam neobsahuje žádná data";
} else {
    $up_or_down = str_replace(array('ASC','DESC'), array('up','down'), $sort_order);
    $asc_or_desc = $sort_order == 'ASC' ? 'desc' : 'asc';
    $add_class = ' class="highlight"';
    echo "<h1>Seznam Zaměstnanců</h1>";
    echo "<table class='table table-striped'>";
    echo "<tr>";
    echo "<th>Jméno <a href='zamestnanci.php?column=name&order=$asc_or_desc' class='sorted'><span class='glyphicon glyphicon-arrow-down $column == 'name' ? '-' . $up_or_down : '' aria-hidden='true'><a href='zamestnanci.php?column=name&order=$asc_or_desc' class='sorted'><span class='glyphicon glyphicon-arrow-up $column == 'name' ? '-' . $up_or_down : '' aria-hidden='true'></th><th>Místnost <a href='zamestnanci.php?column=roomName&order=$asc_or_desc' class='sorted'><span class='glyphicon glyphicon-arrow-down $column == 'roomName' ? '-' . $up_or_down : '' aria-hidden='true'><a href='zamestnanci.php?column=roomName&order=$asc_or_desc' class='sorted'><span class='glyphicon glyphicon-arrow-up $column == 'roomName' ? '-' . $up_or_down : '' aria-hidden='true'> </th><th>Telefon <a href='zamestnanci.php?column=phone&order=$asc_or_desc' class='sorted'><span class='glyphicon glyphicon-arrow-down $column == 'phone' ? '-' . $up_or_down : '' aria-hidden='true'><a href='zamestnanci.php?column=phone&order=$asc_or_desc' class='sorted'><span class='glyphicon glyphicon-arrow-up $column == 'phone' ? '-' . $up_or_down : '' aria-hidden='true'> </th><th>Pozice <a href='zamestnanci.php?column=job&order=$asc_or_desc' class='sorted'><span class='glyphicon glyphicon-arrow-down $column == 'job' ? '-' . $up_or_down : '' aria-hidden='true'><a href='zamestnanci.php?column=job&order=$asc_or_desc' class='sorted'><span class='glyphicon glyphicon-arrow-up $column == 'job' ? '-' . $up_or_down : '' aria-hidden='true'> </th>";
    echo "</tr>";
    while ($row = $stmt->fetch()) {
        echo "<tr>";
        echo "<td><a href='zamestnanec.php?employee_id={$row->employee_id}'>{$row->surname} {$row->name}</td><td>{$row->roomName}</td><td>{$row->phone}</td><td>{$row->job}</td>";
        echo "</tr>";
    }
    echo "</table>";
}
unset($stmt);
?>
</body>
</html>


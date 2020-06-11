<?php
// /tips/func.php
function sql($dataBase, $sql, $letters, $numbers)
{
    $objects = $dataBase->prepare($sql);

    foreach ($letters as $key => $value) {
        $objects->bindValue($key, $value, PDO::PARAM_STR);
    }

    foreach ($numbers as $key => $value) {
        $objects->bindValue($key, $value, PDO::PARAM_INT);
    }

    $result = $objects->execute();

    return [$objects, $result];
}


// /employees/index.php
$sql = 'SELECT * FROM employees WHERE company_id=:id AND deleted IS NULL';
$numbers = [':id' => $companyId];
list($employees, $result) = sql($db, $sql, $letters, $numbers);

// /employees/edit.php
$sql = 'UPDATE employees SET employee_name=:employee_name, division_name=:division_name,phone_number=:phone_number, postal_code=:postal_code, prefectures_code=:prefectures_code, address=:address, mail_address=:mail_address, modified=now() WHERE id=:id';

$letters = [':employee_name' => $_POST['employee_name'], ':division_name' => $_POST['division_name'], ':phone_number' => $phone_number, ':postal_code' => $postal_code, ':address' => $_POST['address'], ':mail_address' => $mail_address];

$numbers = [':prefectures_code' => $_POST['prefectures_code'], ':id' => $oldEmployee['id']];

list($objects, $result) = sql($db, $sql, $letters, $numbers);

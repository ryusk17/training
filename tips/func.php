<?php
function h($value = '')
{
    return htmlspecialchars($value, ENT_QUOTES);
}

function isBlank($submitData, $error)
{
    foreach (array_keys($submitData) as $key) {
        if ($submitData[$key] === '') {
            $error[$key] = 'blank';
        }
    }
    return $error;
}

function validStrlen($submitData, $strlenArray, $error)
{
    foreach ($strlenArray as $key => $len) {
        if (mb_strlen($submitData[$key]) > $len) {
            $error[$key] = 'wrong';
        }
    }
    return $error;
}

function convertNumber($number)
{
    $search = array("-", "ー");
    $result = str_replace($search, "", $number);
    return mb_convert_kana($result, 'n');
}

function validNumber($number, $length)
{
    if (is_string($number) && preg_match("/\A\d{{$length}}\z/", $number)) {
        return true;
    }
    return false;
}

function validMail($mail)
{
    if (filter_var($mail, FILTER_VALIDATE_EMAIL, FILTER_FLAG_EMAIL_UNICODE)) {
        return true;
    }
    return false;
}

function errorMessage($errors, $errorMessages, $blankMessages, $wrongMessages)
{
    foreach ($errors as $key => $value) {
        if ($value === 'blank') {
            $errorMessages[$key] =  $blankMessages[$key];
        }
        if ($value === 'wrong') {
            $errorMessages[$key] =  $wrongMessages[$key];
        }
    }
    return $errorMessages;
}

function updateValue($objects, $submitData)
{
    foreach (array_keys($objects) as $key) {
        // null 合体演算子を用いることで、company_id 等の値(送信されない値)が null にならない
        $objects[$key] = $submitData[$key] ?? $objects[$key];
    }
    return $objects;
}

function isDeleted($dataBase, $table, $id)
{
    $sql = 'SELECT COUNT(id) as cnt FROM ' . $table . ' WHERE id=:id AND deleted IS NOT NULL';
    $del = $dataBase->prepare($sql);
    $del->bindValue(':id', $id, PDO::PARAM_INT);
    $result = $del->execute();
    if ($result) {
        $data = $del->fetch();
        if ($data['cnt'] > 0) {
            return true;
        }
        return false;
    }
    return 'error';
}

function sortArray($array, $keyName)
{
    // 一つの値を配列内のすべての要素と比べ、一番大きかった場合に元の配列から取り除く
    //　上記を元の配列の要素がなくなるまで繰り返す
    $newArray = [];
    $i = 0;
    while (count($array) > 0) {
        foreach ($array as $key => $valueA) {
            $j = 0;
            foreach ($array as $valueB) {
                if ($valueA[$keyName] < $valueB[$keyName]) {
                    ++$j;
                }
            }
            if ($j === 0) {
                $newArray[$i] = $valueA;
                unset($array[$key]);
                ++$i;
            }
        }
    }
    return $newArray;
}

function convertKeywords($keywords)
{
    $search = array(" ", "　");
    $result = str_replace($search, ",", $keywords);
    return explode(',', $result);
}

function companySql($keywords)
{
    // SQL 文に半角を忘れずに
    // 変数埋め込む際は "" で囲む
    $sql = 'SELECT c.*, COUNT(e.company_id) - COUNT(e.deleted) AS cnt FROM companies c LEFT JOIN employees e ON c.id = e.company_id WHERE c.deleted IS NULL';
    for ($i = 0; $i < count($keywords); $i++) {
        $sql .= " AND (c.company_name LIKE :free_word{$i} OR c.representative_name LIKE :free_word{$i} OR c.phone_number LIKE :free_word{$i} OR c.postal_code LIKE :free_word{$i} OR c.address LIKE :free_word{$i} OR c.mail_address LIKE :free_word{$i})";
    }
    $sql .= ' GROUP BY c.id, c.company_name, c.representative_name, c.phone_number, c.address, c.mail_address';
    return $sql;
}

function employeeSql($employeeName, $divisionName, $prefecturesCode)
{
    $sql = 'SELECT * FROM employees WHERE company_id=:id AND deleted IS NULL';
    if (!empty($employeeName)) {
        $sql .= ' AND employee_name LIKE :employee_name';
    }
    if (!empty($divisionName)) {
        $sql .= ' AND division_name=:division_name';
    }
    if (!empty($prefecturesCode)) {
        $sql .= ' AND prefectures_code=:prefectures_code';
    }
    return $sql;
}

function bindEmployeeSql($employees, $employeeName, $divisionName, $prefecturesCode)
{
    if (!empty($employeeName)) {
        $employees->bindValue(':employee_name', '%' . addcslashes($employeeName, '\_%') . '%', PDO::PARAM_STR);
    }
    if (!empty($divisionName)) {
        $employees->bindValue(':division_name', $divisionName, PDO::PARAM_STR);
    }
    if (!empty($prefecturesCode)) {
        $employees->bindValue(':prefectures_code', $prefecturesCode, PDO::PARAM_INT);
    }
    return $employees;
}

<?php
// array_multisort( ソートの軸となる配列, 並び順, ソートするデータの型, ソートしたい配列 ) : bool
// array_column( 多次元配列, 値を返したいカラム ) : array
// array_multisort(array_column($employees, 'id'), SORT_DESC, SORT_NUMERIC, $employees);

// 違う方法・配列の順番を逆にする(id がもともと昇順のため)
// $employees = array_reverse($employees);

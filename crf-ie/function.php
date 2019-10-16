<?php
function ds($s) {
	// fungsi buat print di layar
	echo "<pre>";
	print_r($s);
	echo "</pre>";
}
function fitur($lambda,$fk){
	$hasil = exp($lambda*$fk);
	return $hasil;
}
function bulat($nilai){
	$nilai = round($nilai,6);
	return $nilai;
}

function hr() {
	echo "<hr>";
}

function recursive_array_search($needle, $haystack) {
    foreach($haystack as $key=>$value) {
        $current_key=$key;
        if($needle===$value OR (is_array($value) && recursive_array_search($needle,$value) !== false)) {
            return $current_key;
        }
    }
    return false;
}
?>

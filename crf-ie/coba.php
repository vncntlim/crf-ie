<?php
	//buka file csv, pisahkan token dengan kelas
	$row = 0;
    if (($handle = fopen("".__DIR__."/data_testing.csv", "r")) !== FALSE) {
    	while (($data = fgetcsv($handle, 9999, ",", '"')) !== FALSE) {
            $row++;
	        $tokens[] = $data[0];
	        $kelass[] = $data[1];
        }
        fclose($handle);
    }

    $temp_array_hasil = array();
	$hasil_praproses = array();
	for ($i=0; $i < sizeof($tokens); $i++) { 
		$array_kata_per_kelas = preg_split ('/(\s*,*\s*)* +(\s*,*\s*)*/', trim($tokens[$i]));
		$kls = $kelass[$i];
		$hasil_praproses['kata_per_kelas'][$kls][] = $array_kata_per_kelas;
		for ($j=0; $j < sizeof($array_kata_per_kelas); $j++) { 
			$vtoken = $array_kata_per_kelas[$j];
			$vkelas = $kls;
		}
	}

	echo "<pre>";
	var_dump($hasil_praproses);
	echo "</pre>";
?>
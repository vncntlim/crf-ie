<?php
	ini_set('error_reporting', E_ALL & E_NOTICE & E_WARNING);
	ini_set('memory_limit','4000M');
	ini_set('max_execution_time', 72000);

	//baca file csv
	if (($handle = fopen("".__DIR__."/data_training.csv", "r")) !== FALSE) {
    	$row = 0;
    	while (($data = fgetcsv($handle, 9999, ",", '"')) !== FALSE) {
	        if($no_dokumen_sebelum != $data[2]){
	        	$row = 0;
	        }
	        $tokens[$data[2]][$row]['baris'] = $data[0];
	        $tokens[$data[2]][$row]['label'] = $data[1];
	        $no_dokumen_sebelum = $data[2];
	        $row++;
        }
        fclose($handle);
    }

    // inisialisasi array_tagset dengan nilai 0
	$array_tagset = array(
		array ("i" => "0", "tag" => "Kepala Surat", "nilai" => "0" ),
		array ("i" => "1", "tag" => "Jenis Surat", "nilai" => "0" ),
		array ("i" => "2", "tag" => "Nomor Surat", "nilai" => "0" ),
		array ("i" => "3", "tag" => "Tentang", "nilai" => "0" ),
		array ("i" => "4", "tag" => "Jabatan yang Menetapkan", "nilai" => "0" ),
		array ("i" => "5", "tag" => "Menimbang", "nilai" => "0" ),
		array ("i" => "6", "tag" => "Mengingat", "nilai" => "0" ),
		array ("i" => "7", "tag" => "Memutuskan", "nilai" => "0" ),
		array ("i" => "8", "tag" => "Nama yang Dituju", "nilai" => "0" ),
		array ("i" => "9", "tag" => "Nomor Induk yang Dituju", "nilai" => "0" ),
		array ("i" => "10", "tag" => "Tempat Ditetapkan", "nilai" => "0" ),
		array ("i" => "11", "tag" => "Tanggal Ditetapkan", "nilai" => "0" ),
		array ("i" => "12", "tag" => "Organisasi", "nilai" => "0" ),
		array ("i" => "13", "tag" => "Nama yang Menetapkan", "nilai" => "0" ),
		array ("i" => "14", "tag" => "Tembusan", "nilai" => "0" )
	);

	//inisialisasi array
	$kamus = array();
	$temp_array_hasil = array();
	$hasil_praproses = array();

	for ($i=0; $i < sizeof($tokens); $i++) {
		//untuk setiap dokumen
		for ($j=0; $j < sizeof($tokens[$i]); $j++) {
			//untuk setiap baris
			//filter simbol
			$tokens[$i][$j]['baris'] = preg_replace('[^- \/,.&%()a-zA-Z0-9]', '', $tokens[$i][$j]['baris']);
			//tokenisasi kata
			$array_kata_per_kelas = preg_split('/(\s*,*\s*)* +(\s*,*\s*)*/', trim($tokens[$i][$j]['baris']));
			$kls = $tokens[$i][$j]['label'];
			$hasil_praproses['kata_per_kelas'][$kls][] = $array_kata_per_kelas; 
			for ($k=0; $k < sizeof($array_kata_per_kelas); $k++) { 
				# code...
				$hasil_praproses[$j][$k]['kata'] = $array_kata_per_kelas[$k];
				$hasil_praproses[$j][$k]['kelas'] = $tokens[$i][$j]['label'];
				$hasil_praproses[$j][$k]['fitur'] = $array_tagset;
				
				//fitur node
				$hasil_praproses[$j][$k]['fitur'][$kls]['nilai'] = "1";
			}

			//masuk array temp_array_hasil
			if (preg_match("#[0-9]#", $array_kata_per_kelas[0])) {
				//jika token pertama mengandung angka
				$temp_array_hasil[$i][$j]['kata'] = $array_kata_per_kelas[1];
			} else {
				//jika tidak mengandung angka
				$temp_array_hasil[$i][$j]['kata'] = $array_kata_per_kelas[0];
			}

			//masuk kamus
			if (!in_array($temp_array_hasil[$i][$j]['kata'], $kamus[$kls])) {
				# code...
				$kamus[$kls][] = $temp_array_hasil[$i][$j]['kata'];
			}
			
			$temp_array_hasil[$i][$j]['kelas'] = $kls;
			$temp_array_hasil[$i][$j]['fitur'] = $array_tagset;
			
			//fitur node
			$temp_array_hasil[$i][$j]['fitur'][$kls]['nilai'] = "1";
			
			//fitur edge
			if(isset($tokens[$i][$j+1])){
				//jika ada kelas selanjutnya
				if (($tokens[$i][$j]['label'] == "14") and ($tokens[$i][$j+1]['label'] == "0")) {
					# code...
					//jika akhir baris pada sebuah dokumen
				} else {
					$tag_setelahnya = $tokens[$i][$j+1]['label'];
				
					$temp_array_hasil[$i][$j]['fitur_transisi'] = array();
					$counter_fitur_transisi = 0;
					$temp_fitur = $temp_array_hasil[$i][$j]['fitur'];
					
					foreach ($temp_fitur as $key_fitur => $value_fitur) {
						
						foreach ($array_tagset as $key_tagset => $value_tagset) {
			
							$temp_array_hasil[$i][$j]['fitur_transisi'][$counter_fitur_transisi]['tag_sebelumnya'] = $value_fitur['tag'];
							
							$temp_array_hasil[$i][$j]['fitur_transisi'][$counter_fitur_transisi]['tag_setelahnya'] = $value_tagset['tag'];
							
							if (($value_tagset['i'] == $tag_setelahnya)&&($value_fitur['nilai'] == 1)) {
								$temp_array_hasil[$i][$j]['fitur_transisi'][$counter_fitur_transisi]['nilai'] = "1";
							} else {
								$temp_array_hasil[$i][$j]['fitur_transisi'][$counter_fitur_transisi]['nilai'] = "0";
							}
							
							$counter_fitur_transisi++;
						}
					}	
				}
			}
		}
	}

	//inisialisasi array
	$lambda_fitur_node = array();
	$lambda_fitur_edge = array();
	$status_n = array();
	$status_e = array();
	$status_node = 0;
	$status_edge = 0;

	// INISIALISASI LAMBDA
	for ($i = 0; $i < 15; $i++) {
		$lambda_fitur_node[$i] = 0;
		$status[$i] = 0;
	}

	for ($i = 0; $i < 225; $i++) {
		$lambda_fitur_edge[$i] = 0;
		$status_e[$i] = 0;
	}

	// training
	$SDEV = 2;
	$LRATE = 0.01;
	
	for($counter_train = 0; $counter_train < 1; $counter_train++){
		if(($status_node != 15) or ($status_edge != 225)){
			for ($i=0; $i < sizeof($temp_array_hasil); $i++){
				//mencari node dan edge potensial
				for ($j=0; $j < sizeof($temp_array_hasil[$i]); $j++) {
					//inisialisasi array
					$temp_array_hasil[$i][$j]['node_potensial'] = array();
					$temp_array_hasil[$i][$j]['edge_potensial'] = array();
					
					for ($k=0; $k < 15; $k++) {
						$node_potensial = $lambda_fitur_node[$k] * $temp_array_hasil[$i][$j]['fitur'][$k]['nilai'];
						$temp_array_hasil[$i][$j]['node_potensial'][$k] = exp($node_potensial); 	
					}

					for ($k=0; $k < 225; $k++) {
						if (isset($temp_array_hasil[$i][$j]['fitur_transisi'])) {
							$edge_potensial = $lambda_fitur_edge[$k] * $temp_array_hasil[$i][$j]['fitur_transisi'][$k]['nilai'];
							$temp_array_hasil[$i][$j]['edge_potensial'][$k] = exp($edge_potensial);
						}
					}
				}

	 			//mencari forward pass
	 			for ($j=0; $j < sizeof($temp_array_hasil[$i]); $j++) {
					$temp_array_hasil[$i][$j]['forward_var'] = array();
					if($j == 0){
						for ($k=0; $k < 15; $k++) { 
							$temp_array_hasil[$i][$j]['forward_var'][$k] = (1/15);
							$forward_var_sebelumnya = $temp_array_hasil[$i][$j]['forward_var'];
						}
					} else {
	 					$jumlah_forward_var = 0;
	 					for($k = 0; $k < 15; $k++){
							$jumlah_temp_forward_var = 0;
	 						$counter_edge = $k;
							for ($f=0; $f < sizeof($forward_var_sebelumnya); $f++) { 
	 							$a = $forward_var_sebelumnya[$f];
	 							$b = $temp_array_hasil[$i][$j]['node_potensial'][$k];
	 							$c = $temp_array_hasil[$i][$j-1]['edge_potensial'][$counter_edge];

	 							$temp_forward_var = $a * $b * $c;
	 							$jumlah_temp_forward_var += $temp_forward_var;
	 							$counter_edge += 15;
	 						}
	 						$jumlah_forward_var += $jumlah_temp_forward_var;
	 						$temp_array_hasil[$i][$j]['forward_var'][$k] = $jumlah_temp_forward_var;
	 					}
	 					$kt = (1/$jumlah_forward_var);
	 					for ($m=0; $m < sizeof($temp_array_hasil[$i][$j]['forward_var']); $m++) { 
	 						# code...
	 						$temp_array_hasil[$i][$j]['forward_var'][$m] = $kt * $temp_array_hasil[$i][$j]['forward_var'][$m];
	 					}
	 					$forward_var_sebelumnya = $temp_array_hasil[$i][$j]['forward_var'];
	 				}
	 			}

	 			//mencari backward pass
	 			for ($j=(sizeof($temp_array_hasil[$i])-1); $j >= 0; $j--) { 
	 					$temp_array_hasil[$i][$j]['backward_var'] = array();
	 					if($j == (sizeof($temp_array_hasil[$i])-1)){
	 						for ($k=0; $k < 15; $k++) { 
	 							$temp_array_hasil[$i][$j]['backward_var'][$k] = (1/15);
	 						}
	 						$backward_var_sebelumnya = $temp_array_hasil[$i][$j]['backward_var'];
	 					} else {
	 						$jumlah_backward_var = 0;
	 						$counter_edge = 0;
	 						for ($k=0; $k < 15; $k++) { 
	 							$jumlah_temp_backward_var = 0;
	 							for ($f=0; $f < sizeof($backward_var_sebelumnya); $f++) { 
	 								# code...
	 								$a = $backward_var_sebelumnya[$f];
	 								$b = $temp_array_hasil[$i][$j+1]['node_potensial'][$f];
	 								$c = $temp_array_hasil[$i][$j]['edge_potensial'][$counter_edge];
	 								$temp_bacward_var = $a * $b * $c;
	 								$jumlah_temp_backward_var += $temp_bacward_var;
	 								$counter_edge++;
	 							}
	 							$jumlah_backward_var += $jumlah_temp_backward_var;
	 							$temp_array_hasil[$i][$j]['backward_var'][$k] = $jumlah_temp_backward_var;
	 						}
	 						$kt = (1/$jumlah_backward_var);
	 						for ($m=0; $m < sizeof($temp_array_hasil[$i][$j]['backward_var']); $m++) { 
	 							# code...
	 							$temp_array_hasil[$i][$j]['backward_var'][$m] = $kt * $temp_array_hasil[$i][$j]['backward_var'][$m];
	 						}

	 						$backward_var_sebelumnya = $temp_array_hasil[$i][$j]['backward_var'];
	 					}
	 			}

	 			//mencari probabilitas node
	 			for ($j=0; $j < sizeof($temp_array_hasil[$i]); $j++) { 
 					# code...
 					$temp_array_hasil[$i][$j]['probabilitas_node'] = array();
 					$total_probabilitas = 0;
 					for ($k=0; $k < sizeof($temp_array_hasil[$i][$j]['forward_var']); $k++) { 
 						# code...
 						$fv = $temp_array_hasil[$i][$j]['forward_var'][$k];
 						$bv = $temp_array_hasil[$i][$j]['backward_var'][$k];
 						$np = $temp_array_hasil[$i][$j]['node_potensial'][$k];

 						$pn = $fv * $bv * $np;
 						$temp_array_hasil[$i][$j]['probabilitas_node'][$k] = $pn;
 						$total_probabilitas += $pn;
 					}

 					for ($k=0; $k < sizeof($temp_array_hasil[$i][$j]['probabilitas_node']); $k++) { 
 						# code...
 						$pn_asli = 0;
 						$wt = 1/$total_probabilitas;
 						$pn_asli = $wt * $temp_array_hasil[$i][$j]['probabilitas_node'][$k];
 						$temp_array_hasil[$i][$j]['probabilitas_node'][$k] = $pn_asli;
 					}
	 			}

	 			//mencari probabilitas edge
 				for ($j=0; $j < sizeof($temp_array_hasil[$i]); $j++) { 
 					# code...
 					if (isset($temp_array_hasil[$i][$j+1])) {
 						$temp_kata_setelahnya = $temp_array_hasil[$i][$j+1];
						$cep = 0;
 						$temp_array_hasil[$i][$j]['probabilitas_edge'] = array();
 						$total_probabilitas = 0;
 						for ($k=0; $k < sizeof($temp_array_hasil[$i][$j]['forward_var']); $k++) { 
 							# code...
 							$fv = $temp_array_hasil[$i][$j]['forward_var'][$k];
 							$np = $temp_array_hasil[$i][$j]['node_potensial'][$k];
 							for ($m=0; $m < sizeof($temp_kata_setelahnya['node_potensial']); $m++) { 
 								# code...
 								$bvs = $temp_kata_setelahnya['backward_var'][$m];
 								$nps = $temp_kata_setelahnya['node_potensial'][$m];
 								$ep = $temp_array_hasil[$i][$j]['edge_potensial'][$cep];
 								$pe = $fv * $np * $nps * $bvs * $ep;
 								$total_probabilitas += $pe;
 								$temp_array_hasil[$i][$j]['probabilitas_edge'][$cep] = $pe;
 								$cep++;
 							}
 						}

 						for ($k=0; $k < sizeof($temp_array_hasil[$i][$j]['probabilitas_edge']); $k++) { 
 							# code...
 							$pe_asli = 0;
 							$yt = 1/$total_probabilitas;
 							$pe_asli = $yt * $temp_array_hasil[$i][$j]['probabilitas_edge'][$k];
 							$temp_array_hasil[$i][$j]['probabilitas_edge'][$k] = $pe_asli;
 						}
 					} else if (isset($temp_array_hasil[$i+1][0])) {
 						# code...
 						$temp_kata_setelahnya = $temp_array_hasil[$i+1][0];
 						$cep = 0;
 						$temp_array_hasil[$i][$j]['probabilitas_edge'] = array();
 						$total_probabilitas = 0;
 						for ($k=0; $k < sizeof($temp_array_hasil[$i][$j]['forward_var']); $k++) { 
 							# code...
 							$fv = $temp_array_hasil[$i][$j]['forward_var'][$k];
 							$np = $temp_array_hasil[$i][$j]['node_potensial'][$k];

 							for ($m=0; $m < sizeof($temp_kata_setelahnya['node_potensial']); $m++) { 
 								# code...
 								$bvs = $temp_kata_setelahnya['backward_var'][$m];
 								$nps = $temp_kata_setelahnya['node_potensial'][$m];
 								$ep = $temp_array_hasil[$i][$j]['edge_potensial'][$cep];

 								$pe = $fv * $np * $nps * $bvs * $ep;
 								$total_probabilitas += $pe;
 								$temp_array_hasil[$i][$j]['probabilitas_edge'][$cep] = $pe;
 								$cep++;
 							}
 						}

 						for ($k=0; $k < sizeof($temp_array_hasil[$i][$j]['probabilitas_edge']); $k++) { 
 							# code...
 							$pe_asli = 0;
 							$yt = 1/$total_probabilitas;
 							$pe_asli = $yt * $temp_array_hasil[$i][$j]['probabilitas_edge'][$k];
 							$temp_array_hasil[$i][$j]['probabilitas_edge'][$k] = $pe_asli;
 						}
 					}
 				}
 			}

			// mencari gradient node
			$total_g_node = array();
			for ($a=0; $a < 15; $a++) { 
				$temp_total_g = 0;
				if ($status_n[$a] == 0) {
					for ($i=0; $i < sizeof($temp_array_hasil); $i++) { 
						for ($j=0; $j < sizeof($temp_array_hasil[$i]); $j++) { 
							$total_node = 0;
							for ($m=0; $m < sizeof($temp_array_hasil[$i][$j]['probabilitas_node']); $m++) { 
								$temp_total_node = 0;
								$pn = $temp_array_hasil[$i][$j]['probabilitas_node'][$m];
								$nf = $temp_array_hasil[$i][$j]['fitur'][$m]['nilai'];
								$temp_total_node = ($pn * $nf);

								$total_node += $temp_total_node;
							}
							$temp_g = $temp_array_hasil[$i][$j]['fitur'][$a]['nilai'] - $total_node;
							$temp_total_g += $temp_g;
						}
					}
					if ($counter_train == 0) {
						# code...
						$total_g_node[$a] = $temp_total_g - ($lambda_fitur_node[$a] / (pow($SDEV, 2)));
						$lambda_fitur_node[$a] = $lambda_fitur_node[$a] + ($LRATE * $total_g_node[$a]);
					} else {
						$total_g_node[$a] = $temp_total_g - ($lambda_fitur_node[$a] / (pow($SDEV, 2)));
						$temp_lambda_node = $lambda_fitur_node[$a] + ($LRATE * $total_g_node[$a]);
						if ($temp_lambda_node > $lambda_fitur_node[$a]) {
							# code...
							$lambda_fitur_node[$a] = $temp_lambda_node;
						} else {
							$status_n[$a] = 1;
							$lambda_fitur_node[$a] = $temp_lambda_node;
						}
					}
					$array_parameter['gradien_fitur_node'][$a] = $total_g_node[$a];
					$array_parameter['lambda_fitur_node'][$a] = $lambda_fitur_node[$a];
				}
			}
			$status_node = 0;
			for ($i=0; $i < 15; $i++) { 
				$status_node += $status_n[$i];
			}

			// mencari gradient edge
			$total_g_edge = array();
			for ($a=0; $a < 225; $a++) { 
				$temp_total_g = 0;
				if ($status_e[$a] == 0) {
					for ($i=0; $i < sizeof($temp_array_hasil); $i++) { 
						for ($j=0; $j < sizeof($temp_array_hasil[$i]) - 1; $j++) { 
							$total_edge = 0;
							for ($m=0; $m < sizeof($temp_array_hasil[$i][$j]['probabilitas_edge']); $m++) { 
								$temp_total_edge = 0;
								$pe = $temp_array_hasil[$i][$j]['probabilitas_edge'][$m];
								$nf = $temp_array_hasil[$i][$j]['fitur_transisi'][$m]['nilai'];
								$temp_total_edge = ($pn * $nf);

								$total_edge += $temp_total_edge;
							}
							$temp_g = $temp_array_hasil[$i][$j]['fitur_transisi'][$a]['nilai'] - $total_edge;
							$temp_total_g += $temp_g;
						}
					}
					if ($counter_train == 0) {
						# code...
						$total_g_edge[$a] = $temp_total_g - ($lambda_fitur_edge[$a] / (pow($SDEV, 2)));
						$lambda_fitur_edge[$a] = $lambda_fitur_edge[$a] + ($LRATE * $total_g_edge[$a]);
					} else {
						$total_g_edge[$a] = $temp_total_g - ($lambda_fitur_edge[$a] / (pow($SDEV, 2)));
						$temp_lambda_edge = $lambda_fitur_edge[$a] + ($LRATE * $total_g_edge[$a]);
						if ($temp_lambda_edge < $lambda_fitur_edge[$a]) {
							# code...
							$status_e[$a] = 1;
							$lambda_fitur_edge[$a] = $temp_lambda_edge;
						} else {
							$lambda_fitur_edge[$a] = $temp_lambda_edge;
						}
					}
					$array_parameter['gradien_fitur_edge'][$a] = $total_g_edge[$a];
					$array_parameter['lambda_fitur_edge'][$a] = $lambda_fitur_edge[$a];
				}
			}
			$status_edge = 0;
			for ($i=0; $i < 225; $i++) { 
				$status_edge += $status_e[$i];
			}
		}
	}

	function utf8ize( $mixed ) {
	    if (is_array($mixed)) {
	        foreach ($mixed as $key => $value) {
	            $mixed[$key] = utf8ize($value);
	        }
	    } elseif (is_string($mixed)) {
	        return mb_convert_encoding($mixed, "UTF-8", "UTF-8");
	    }
	    return $mixed;
	}
	
	$json = json_encode($array_parameter);
	file_put_contents("parameter.txt", $json);

	$json = json_encode(utf8ize($kamus));
	file_put_contents("kamus.txt", $json);
	// die("json_encode fail: " . json_last_error_msg());

	$json = json_encode(utf8ize($temp_array_hasil));
	file_put_contents("Hasil.txt", $json);

	$json = json_encode(utf8ize($hasil_praproses));
	file_put_contents("Hasil_praproses_data_training.txt", $json);

	$json = json_encode($lambda_fitur_node);
	file_put_contents("lambda_fitur_node.txt", $json);

	$json = json_encode($lambda_fitur_edge);
	file_put_contents("lambda_fitur_edge.txt", $json);

	echo "<script>alert('Proses pelatihan berhasil!');window.location.href='hasil_training.php';</script>";

?>
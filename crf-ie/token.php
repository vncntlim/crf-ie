<?php
	//tokenizer
	require_once __DIR__ . '/vendor/autoload.php';
	require_once __DIR__ . '/function.php';
	$tokenizerFactory  = new \Sastrawi\Tokenizer\TokenizerFactory();
	$tokenizer = $tokenizerFactory->createDefaultTokenizer();

	error_reporting(0);
	ini_set('memory_limit','4000M');
	ini_set('max_execution_time', 72000);

	//buka file csv, pisahkan token dengan kelas
	$row = 0;
    if (($handle = fopen("".__DIR__."/data_training.csv", "r")) !== FALSE) {
    	while (($data = fgetcsv($handle, 9999, ",", '"')) !== FALSE) {
            $row++;
	        $tokens[] = $data[0];
	        $kelass[] = $data[1];
        }
        fclose($handle);
    }

    // inisialisasi array_tagset dengan nilai 0
	$array_tagset = array(
		array ("i" => "0", "tag" => "js", "nilai" => "0" ), //jenis surat
		array ("i" => "1", "tag" => "ns", "nilai" => "0" ), //nomor surat
		array ("i" => "2", "tag" => "tt", "nilai" => "0" ), //tentang
		array ("i" => "3", "tag" => "yms", "nilai" => "0" ), //yang menetapkan surat
		array ("i" => "4", "tag" => "mg", "nilai" => "0" ), //menimbang
		array ("i" => "5", "tag" => "mt", "nilai" => "0" ), //mengingat
		array ("i" => "6", "tag" => "ms", "nilai" => "0" ), //memutuskan
		array ("i" => "7", "tag" => "nyd", "nilai" => "0" ), //nama yang dituju
		array ("i" => "8", "tag" => "tp", "nilai" => "0" ), //tempat
		array ("i" => "9", "tag" => "tg", "nilai" => "0" ), //tanggal ditetapkan
		array ("i" => "10", "tag" => "org", "nilai" => "0" ), //organisasi
		array ("i" => "11", "tag" => "nym", "nilai" => "0" ), //nama yang menetapkan
		array ("i" => "12", "tag" => "tmb", "nilai" => "0" ) //tembusan
	);

	//tokenizing kata
	$temp_array_hasil = array();
	$hasil_praproses = array();
	for ($i=0; $i < sizeof($tokens); $i++) { 
		$array_kata_per_kelas = $tokenizer->tokenize($tokens[$i]);
		$kls = $kelass[$i];
		$hasil_praproses['kata_per_kelas'][$kls][] = $array_kata_per_kelas;
		for ($j=0; $j < sizeof($array_kata_per_kelas); $j++) { 
			$vtoken = $array_kata_per_kelas[$j];
			$vkelas = $kls;

			//masuk array prarposes
			$hasil_praproses['tokentag'][$i][$j]['kata'] = $vtoken;
			$hasil_praproses['tokentag'][$i][$j]['kelas'] = $vkelas;
			//masuk array temp_array_hasil
			$temp_array_hasil[$i][$j]['kata'] = $vtoken;
			$temp_array_hasil[$i][$j]['kelas'] = $vkelas;
			$temp_array_hasil[$i][$j]['fitur'] = $array_tagset;

			$temp_array_hasil[$i][$j]['fitur'][$vkelas]['nilai'] = "1";

			if(isset($kelass[$i+1])){
				//jika ada kelas selanjutnya
				if(isset($array_kata_per_kelas[$j+1])){
					$kelas_setelahnya = $temp_array_hasil[$i][$j+1]['kelas'];
					$counter_kelas = 0;
					$temp_array_hasil[$i][$j]['fitur_transisi'] = array();
					$temp_fitur = $temp_array_hasil[$i][$j]['fitur'];

					foreach ($temp_fitur as $key_fitur => $value_fitur) {
						foreach ($array_tagset as $key_tagset => $value_tagset) {
							$ind = $counter_kelas * 13 + $value_tagset['i'];
							$temp_array_hasil[$i][$j]['fitur_transisi'][$ind]['tag_sebelumnya'] = $value_fitur['tag'];

							$temp_array_hasil[$i][$j]['fitur_transisi'][$ind]['tag_setelahnya'] = $value_tagset['tag'];
							if(($value_fitur['nilai'] == 1) && ($value_tagset['i'] == $kelas_setelahnya)){
								$temp_array_hasil[$i][$j]['fitur_transisi'][$ind]['nilai'] = "1";
							} else {
								$temp_array_hasil[$i][$j]['fitur_transisi'][$ind]['nilai'] = "0";
							}
						}
						$counter_kelas++;
					}	
				} else {
					$kelas_setelahnya = $temp_array_hasil[$i+1][1]['kelas'];
					$counter_kelas = 0;
					$temp_array_hasil[$i][$j]['fitur_transisi'] = array();
					$temp_fitur = $temp_array_hasil[$i][$j]['fitur'];

					foreach ($temp_fitur as $key_fitur => $value_fitur) {
						foreach ($array_tagset as $key_tagset => $value_tagset) {
							$ind = $counter_kelas * 13 + $value_tagset['i'];
							$temp_array_hasil[$i][$j]['fitur_transisi'][$ind]['tag_sebelumnya'] = $value_fitur['tag'];

							$temp_array_hasil[$i][$j]['fitur_transisi'][$ind]['tag_setelahnya'] = $value_tagset['tag'];
							if(($value_fitur['nilai'] == 1) && ($value_tagset['i'] == $kelas_setelahnya)){
								$temp_array_hasil[$i][$j]['fitur_transisi'][$ind]['nilai'] = "1";
							} else {
								$temp_array_hasil[$i][$j]['fitur_transisi'][$ind]['nilai'] = "0";
							}
						}
						$counter_kelas++;
					}
				}
			} else {
				//jika tidak
				if (isset($array_kata_per_kelas[$j+1])) {
					$kelas_setelahnya = $temp_array_hasil[$i][$j+1]['kelas'];
					$counter_kelas = 0;
					$temp_array_hasil[$i][$j]['fitur_transisi'] = array();
					$temp_fitur = $temp_array_hasil[$i][$j]['fitur'];

					foreach ($temp_fitur as $key_fitur => $value_fitur) {
						foreach ($array_tagset as $key_tagset => $value_tagset) {
							$ind = $counter_kelas * 13 + $value_tagset['i'];
							$temp_array_hasil[$i][$j]['fitur_transisi'][$ind]['tag_sebelumnya'] = $value_fitur['tag'];

							$temp_array_hasil[$i][$j]['fitur_transisi'][$ind]['tag_setelahnya'] = $value_tagset['tag'];
							if(($value_fitur['nilai'] == 1) && ($value_tagset['i'] == $kelas_setelahnya)){
								$temp_array_hasil[$i][$j]['fitur_transisi'][$ind]['nilai'] = "1";
							} else {
								$temp_array_hasil[$i][$j]['fitur_transisi'][$ind]['nilai'] = "0";
							}
						}
						$counter_kelas++;
					}
				}
			}
		}
	}

	$lambda_fitur_node = array();
	$lambda_fitur_edge = array();
	$status_n = array();
	$status_e = array();
	$status_node = 0;
	$status_edge = 0;

	// inisialisasi lambda
	for ($i=0; $i < 13; $i++) { 
		$lambda_fitur_node[$i] = 0;
		$status_n[$i] = 0;
	}
	
	for ($i=0; $i < 169; $i++) { 
		$lambda_fitur_edge[$i] = 0;
		$status_e[$i] = 0;
	}

	// training
	$SDEV = 2;
	$LRATE = 0.000001;
	
	// for($counter_train = 0; $counter_train < 20; $counter_train++){
	// 		for ($i=0; $i < sizeof($temp_array_hasil); $i++){
	// 			$temp_array_hasil[$i] = $temp_array_hasil[$i];
	// 			//mencari node dan edge potensial
	// 			for ($j=0; $j < sizeof($temp_array_hasil[$i]); $j++) {
	// 				$temp_array_hasil[$i][$j] = $temp_array_hasil[$i][$j]; 
	// 				$temp_array_hasil[$i][$j]['node_potensial'] = array();
	// 				$temp_array_hasil[$i][$j]['edge_potensial'] = array();

	// 				for ($k=0; $k < 13; $k++) { 
	// 					$node_potensial = exp(($lambda_fitur_node[$k] * $temp_array_hasil[$i][$j]['fitur'][$k]['nilai']));
	// 					$temp_array_hasil[$i][$j]['node_potensial'][$k] = $node_potensial;
	// 				}

	// 				for ($k=0; $k < 169; $k++) { 
	// 					if (isset($temp_array_hasil[$i][$j]['fitur_transisi'])) {
	// 						$edge_potensial = exp(($lambda_fitur_edge[$k] * $temp_array_hasil[$i][$j]['fitur_transisi'][$k]['nilai']));
	// 						$temp_array_hasil[$i][$j]['edge_potensial'][$k] = $edge_potensial;
	// 					}
	// 				}
	// 	 		}

	//  			//mencari forward pass
	//  			for ($j=0; $j < sizeof($temp_array_hasil[$i]); $j++) {
	//  					$temp_array_hasil[$i][$j] = $temp_array_hasil[$i][$j];  
	//  					$temp_array_hasil[$i][$j]['forward_var'] = array();
	//  					if($j == 0){
	//  						for ($k=0; $k < 13; $k++) { 
	//  							$temp_array_hasil[$i][$j]['forward_var'][$k] = (1/13);
	//  							$forward_var_sebelumnya = $temp_array_hasil[$i][$j]['forward_var'];
	//  						}
	//  					} else {
	//  						$jumlah_forward_var = 0;

	//  						for($k = 0; $k < 13; $k++){
	//  							$jumlah_temp_forward_var = 0;
	//  							$counter_edge = $k;
	//  							for ($f=0; $f < sizeof($forward_var_sebelumnya); $f++) { 
	//  								$a = $forward_var_sebelumnya[$f];
	//  								$b = $temp_array_hasil[$i][$j]['node_potensial'][$k];
	//  								$c = $temp_array_hasil[$i][$j-1]['edge_potensial'][$counter_edge];

	//  								$temp_forward_var = $a * $b * $c;
	//  								$jumlah_temp_forward_var += $temp_forward_var;
	//  								$counter_edge += 13;
	//  							}
	//  							$jumlah_forward_var += $jumlah_temp_forward_var;
	//  							$temp_array_hasil[$i][$j]['forward_var'][$k] = $jumlah_temp_forward_var;
	//  						}
	//  						$kt = (1/$jumlah_forward_var);

	//  						for ($m=0; $m < sizeof($temp_array_hasil[$i][$j]['forward_var']); $m++) { 
	//  							# code...
	//  							$temp_array_hasil[$i][$j]['forward_var'][$m] = $kt * $temp_array_hasil[$i][$j]['forward_var'][$m];
	//  						}
	//  						$forward_var_sebelumnya = $temp_array_hasil[$i][$j]['forward_var'];
	//  					}
	//  			}

	//  			//mencari backward pass
	//  			for ($j=(sizeof($temp_array_hasil[$i])-1); $j >= 0; $j--) { 
	//  					$temp_array_hasil[$i][$j]['backward_var'] = array();
	//  					if($j == (sizeof($temp_array_hasil[$i])-1)){
	//  						for ($k=0; $k < 13; $k++) { 
	//  							$temp_array_hasil[$i][$j]['backward_var'][$k] = (1/13);
	//  						}
	//  						$backward_var_sebelumnya = $temp_array_hasil[$i][$j]['backward_var'];
	//  					} else {
	//  						$jumlah_backward_var = 0;
	//  						$counter_edge = 0;
	//  						for ($k=0; $k < 13; $k++) { 
	//  							$jumlah_temp_backward_var = 0;
	//  							for ($f=0; $f < sizeof($backward_var_sebelumnya); $f++) { 
	//  								# code...
	//  								$a = $backward_var_sebelumnya[$f];
	//  								$b = $temp_array_hasil[$i][$j+1]['node_potensial'][$f];
	//  								$c = $temp_array_hasil[$i][$j]['edge_potensial'][$counter_edge];
	//  								$temp_bacward_var = $a * $b * $c;
	//  								$jumlah_temp_backward_var += $temp_bacward_var;
	//  								$counter_edge++;
	//  							}
	//  							$jumlah_backward_var += $jumlah_temp_backward_var;
	//  							$temp_array_hasil[$i][$j]['backward_var'][$k] = $jumlah_temp_backward_var;
	//  						}
	//  						$kt = (1/$jumlah_backward_var);
	//  						for ($m=0; $m < sizeof($temp_array_hasil[$i][$j]['backward_var']); $m++) { 
	//  							# code...
	//  							$temp_array_hasil[$i][$j]['backward_var'][$m] = $kt * $temp_array_hasil[$i][$j]['backward_var'][$m];
	//  						}

	//  						$backward_var_sebelumnya = $temp_array_hasil[$i][$j]['backward_var'];
	//  					}
	//  			}

	//  			//mencari probabilitas node
	//  			for ($j=0; $j < sizeof($temp_array_hasil[$i]); $j++) { 
	//  					# code...
	//  					$temp_array_hasil[$i][$j]['probabilitas_node'] = array();
	//  					$total_probabilitas = 0;
	//  					for ($k=0; $k < sizeof($temp_array_hasil[$i][$j]['forward_var']); $k++) { 
	//  						# code...
	//  						$fv = $temp_array_hasil[$i][$j]['forward_var'][$k];
	//  						$bv = $temp_array_hasil[$i][$j]['backward_var'][$k];
	//  						$np = $temp_array_hasil[$i][$j]['node_potensial'][$k];

	//  						$pn = $fv * $bv * $np;
	//  						$temp_array_hasil[$i][$j]['probabilitas_node'][$k] = $pn;
	//  						$total_probabilitas += $pn;
	//  					}

	//  					for ($k=0; $k < sizeof($temp_array_hasil[$i][$j]['probabilitas_node']); $k++) { 
	//  						# code...
	//  						$pn_asli = 0;
	//  						$wt = 1/$total_probabilitas;
	//  						$pn_asli = $wt * $temp_array_hasil[$i][$j]['probabilitas_node'][$k];
	//  						$temp_array_hasil[$i][$j]['probabilitas_node'][$k] = $pn_asli;
	//  					}
	//  			}

	//  			//mencari probabilitas edge
	//  			for ($j=0; $j < sizeof($temp_array_hasil[$i]); $j++) { 
	//  					# code...
	//  					if (isset($temp_array_hasil[$i][$j+1])) {
	//  						$temp_kata_setelahnya = $temp_array_hasil[$i][$j+1];
	//  						$cep = 0;
	//  						$temp_array_hasil[$i][$j]['probabilitas_edge'] = array();
	//  						$total_probabilitas = 0;
	//  						for ($k=0; $k < sizeof($temp_array_hasil[$i][$j]['forward_var']); $k++) { 
	//  							# code...
	//  							$fv = $temp_array_hasil[$i][$j]['forward_var'][$k];
	//  							$np = $temp_array_hasil[$i][$j]['node_potensial'][$k];

	//  							for ($m=0; $m < sizeof($temp_kata_setelahnya['node_potensial']); $m++) { 
	//  								# code...
	//  								$bvs = $temp_kata_setelahnya['backward_var'][$m];
	//  								$nps = $temp_kata_setelahnya['node_potensial'][$m];
	//  								$ep = $temp_array_hasil[$i][$j]['edge_potensial'][$cep];

	//  								$pe = $fv * $np * $nps * $bvs * $ep;
	//  								$total_probabilitas += $pe;
	//  								$temp_array_hasil[$i][$j]['probabilitas_edge'][$cep] = $pe;
	//  								$cep++;
	//  							}
	//  						}

	//  						for ($k=0; $k < sizeof($temp_array_hasil[$i][$j]['probabilitas_edge']); $k++) { 
	//  							# code...
	//  							$pe_asli = 0;
	//  							$yt = 1/$total_probabilitas;
	//  							$pe_asli = $temp_array_hasil[$i][$j]['probabilitas_edge'][$k];
	//  							$temp_array_hasil[$i][$j]['probabilitas_edge'][$k] = $pe_asli;
	//  						}
	//  					}
	//  			}
	//  		}

	// 		// mencari gradient node
	// 		$total_g_node = array();
	// 		for ($a=0; $a < 13; $a++) { 
	// 			$temp_total_g = 0;
	// 			if ($status_n[$a] == 0) {
	// 				for ($i=0; $i < sizeof($temp_array_hasil); $i++) { 
	// 					for ($j=0; $j < sizeof($temp_array_hasil[$i]); $j++) { 
	// 						$total_node = 0;
	// 						for ($m=0; $m < sizeof($temp_array_hasil[$i][$j]['probabilitas_node']); $m++) { 
	// 							$temp_total_node = 0;
	// 							$pn = $$temp_array_hasil[$i][$j]['probabilitas_node'][$m];
	// 							$nf = $temp_array_hasil[$i][$j]['fitur'][$m]['nilai'];
	// 							$temp_total_node = ($pn * $nf);

	// 							$total_node += $temp_total_node;
	// 						}
	// 						$temp_g = $temp_array_hasil[$i][$j]['fitur'][$a]['nilai'] - $total_node;
	// 						$temp_total_g = $temp_g;
	// 					}
	// 				}
	// 				if ($counter_train == 0) {
	// 					# code...
	// 					$total_g_node[$a] = $temp_total_g - ($lambda_fitur_node[$a] / (pow($SDEV, 2)));
	// 					$lambda_fitur_node[$a] = $lambda_fitur_node[$a] + ($LRATE + $total_g_node[$a]);
	// 				} else {
	// 					$total_g_node[$a] = $temp_total_g - ($lambda_fitur_node[$a] / (pow($SDEV, 2)));
	// 					$temp_lambda_node = $lambda_fitur_node[$a] + ($LRATE * $total_g_node[$a]);
	// 					if ($temp_lambda_node > $lambda_fitur_node[$a]) {
	// 						# code...
	// 						$lambda_fitur_node[$a] = $temp_lambda_node;
	// 					} else {
	// 						$status_n = 1;
	// 						$lambda_fitur_node[$a] = $temp_lambda_node;
	// 					}
	// 				}
	// 				$array_parameter['gradien_fitur_node'][$a] = $total_g_node[$a];
	// 				$array_parameter['lambda_fitur_node'][$a] = $lambda_fitur_node[$a];
	// 			}
	// 		}
	// 		$status_node = 0;
	// 		for ($i=0; $i < 13; $i++) { 
	// 			$status_node = $status_n[$i];
	// 		}

	// 		// mencari gradient edge
	// 		$total_g_edge = array();
	// 		for ($a=0; $a < 169; $a++) { 
	// 			$temp_total_g = 0;
	// 			if ($status_e[$a] == 0) {
	// 				for ($i=0; $i < sizeof($temp_array_hasil); $i++) { 
	// 					for ($j=0; $j < sizeof($temp_array_hasil[$i])-1; $j++) { 
	// 						$total_edge = 0;
	// 						for ($m=0; $m < sizeof($temp_array_hasil[$i][$j]['probabilitas_edge']); $m++) { 
	// 							$temp_total_edge = 0;
	// 							$pe = $temp_array_hasil[$i][$j]['probabilitas_edge'][$m];
	// 							$nf = $temp_array_hasil[$i][$j]['fitur_transisi'][$m]['nilai'];
	// 							$temp_total_edge = ($pe * $nf);

	// 							$total_edge += $temp_total_edge;
	// 						}
	// 						$temp_g = $temp_array_hasil[$i][$j]['fitur_transisi'][$a]['nilai'] - $total_edge;
	// 						$temp_total_g += $temp_g;
	// 					}
	// 				}
	// 				if($counter_train == 0){
	// 					$total_g_edge[$a] = $temp_total_g - ($lambda_fitur_edge[$a] / (pow($SDEV, 2)));
	// 					$lambda_fitur_edge[$a] = $lambda_fitur_edge[$a] + ($LRATE * $total_g_edge[$a]);
	// 				} else {
	// 					$total_g_edge[$a] = $temp_total_g - ($lambda_fitur_edge[$a] / (pow($SDEV, 2)));
	// 					$temp_total_edge = $lambda_fitur_edge[$a] + ($LRATE * $total_g_edge[$a]);
	// 					if ($temp_lambda_edge < $lambda_fitur_edge[$a]) {
	// 						$status_e[$a] = 1;
	// 						$lambda_fitur_edge[$a] = $temp_lambda_edge;
	// 					} else {
	// 						$lambda_fitur_edge[$a] = $temp_lambda_edge;
	// 					}
	// 				}
	// 				$array_parameter['gradien_fitur_edge'][$a] = $total_g_edge[$a];
	// 				$array_parameter['lambda_fitur_edge'][$a] = $lambda_fitur_edge[$a];
	// 			}
	// 		}
	// 		$status_edge = 0;
	// 		for ($a=0; $a < 169; $a++) { 
	// 			$status_edge += $status_e[$a];
	// 		}
	// }
	
	// echo "DONE<br><hr>";
	// $json = json_encode($array_parameter);
	// file_put_contents("parameter.txt", $json);

	// $json = json_encode($temp_array_hasil);
	// file_put_contents("Hasil.txt", $json);

	// $json = json_encode($hasil_praproses);
	// file_put_contents("Hasil_praproses_data_training.txt", $json);

	// $json = json_encode($lambda_fitur_node);
	// file_put_contents("lambda_fitur_node.txt", $json);

	// $json = json_encode($lambda_fitur_edge);
	// file_put_contents("lambda_fitur_edge.txt", $json);

	echo "<pre>";
	var_dump($temp_array_hasil);
	echo "<pre>";		

?>

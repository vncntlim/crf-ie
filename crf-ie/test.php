<?php
	ini_set('error_reporting', E_ALL & E_NOTICE & E_WARNING);
	ini_set('memory_limit','4000M');
	ini_set('max_execution_time', 72000);

	$hasil_praproses = array();

	//load data latih
	$kamus = array();
	$string_array_kamus = file_get_contents("kamus.txt");
	$kamus = json_decode($string_array_kamus, true);

	//load akurasi
	$akurasi = array();
	if (file_exists("akurasi.txt")) {
		# code...
		$string_array_akurasi = file_get_contents("akurasi.txt");
		$akurasi = json_decode($string_array_akurasi, true);
	} else {
	    for ($i=0; $i < 15; $i++) { 
			# code...
			for ($j=0; $j < 15; $j++) { 
				# code...
				$akurasi[$i.".".$j] = 0;
			}
		}
	}

    //load data uji
    $row = 0;

	if (($handle = fopen("".__DIR__."/data_testing.csv", "r")) !== FALSE) {
    	while (($data = fgetcsv($handle, 9999, ",", '"')) !== FALSE) {
            $row++;
	        $tokens[] = $data[0];
	        $kelass[] = $data[1];
        }
        fclose($handle);
    }

    //inisialisasi confusion matrix
    $cm = array();
    for ($i=0; $i < 15; $i++) { 
		# code...
		for ($j=0; $j < 15; $j++) { 
			# code...
			$cm[$i.".".$j] = 0;
		}
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

	//tokenizing kata
	$temp_array_hasil = array();
	$hasil_praproses = array();
	for ($i=0; $i < sizeof($tokens); $i++) { 
		$array_kata_per_kelas = preg_split ('/(\s*,*\s*)* +(\s*,*\s*)*/', trim($tokens[$i]));
		$kls = $kelass[$i];
		$hasil_praproses['kata_per_kelas'][$kls][] = $array_kata_per_kelas;
		$awal_angka = 0;
			for ($j=0; $j < sizeof($array_kata_per_kelas); $j++) { 
				$vtoken = $array_kata_per_kelas[$j];
				$vtoken = preg_replace('[^- \/,.&%()a-zA-Z0-9]', '', $vtoken);
				$vkelas = $kls;
				if ($vtoken != "") {
					if (($j == 0) and (preg_match("#[0-9]#", $vtoken))) {
						# code...
						$awal_angka = 1;
					} else {
						if ($awal_angka == 1) {
							# code...
							//masuk array prarposes
							$hasil_praproses['tokentag'][$i][$j-1]['kata'] = $vtoken;
							//masuk array temp_array_hasil
							$temp_array_hasil[$i][$j-1]['kata'] = $vtoken;
							$temp_array_hasil[$i][$j-1]['tag data'] = $vkelas;
							$temp_array_hasil[$i][$j-1]['fitur'] = $array_tagset;
						} else {
							//masuk array prarposes
							$hasil_praproses['tokentag'][$i][$j]['kata'] = $vtoken;
							//masuk array temp_array_hasil
							$temp_array_hasil[$i][$j]['kata'] = $vtoken;
							$temp_array_hasil[$i][$j]['tag data'] = $vkelas;
							$temp_array_hasil[$i][$j]['fitur'] = $array_tagset;	
						}	
					}
				} else {
					
				}
			}
	}

	//ekstraksi fitur node
	for ($i=0; $i < sizeof($temp_array_hasil); $i++) { 
		# code...
		for ($j=0; $j < sizeof($temp_array_hasil[$i]); $j++) { 
			# code...
			$token_cari = $temp_array_hasil[$i][0]['kata'];
			for ($l=15; $l >= 0; $l--) { 
				# code...
				if (in_array($token_cari, $kamus[$l])) {
					# code...
					$temp_array_hasil[$i][$j]['fitur'][$l]['nilai'] = 1;
				}
			}
		}
	}

	//ekstraksi fitur edge
	for ($i=0; $i < sizeof($temp_array_hasil); $i++) { 
		# code...
		for ($j=0; $j < sizeof($temp_array_hasil[$i]); $j++) { 
			# code...
			if (isset($temp_array_hasil[$i+1])) {
				# code...
				if (isset($temp_array_hasil[$i][$j+1])) {
					# code...
					$counter_kelas = 0;
					$temp_array_hasil[$i][$j]['fitur_transisi'] = array();
					$temp_fitur = $temp_array_hasil[$i][$j]['fitur'];

					foreach ($temp_fitur as $key_fitur => $value_fitur) {
						# code...
						foreach ($array_tagset as $key_tagset => $value_tagset) {
							# code...
							$ind = $counter_kelas * 15 + $value_tagset['i'];
							$temp_array_hasil[$i][$j]['fitur_transisi'][$ind]['tag_sebelumnya'] = $value_fitur['tag'];
							$temp_array_hasil[$i][$j]['fitur_transisi'][$ind]['tag_setelahnya'] = $value_tagset['tag'];
							if (($value_fitur['nilai'] == 1) && ($value_tagset['i'] == $value_fitur['i'])) {
								# code...
								$temp_array_hasil[$i][$j]['fitur_transisi'][$ind]['nilai'] = "1";
							} else {
								$temp_array_hasil[$i][$j]['fitur_transisi'][$ind]['nilai'] = "0";
							}
						}
						$counter_kelas++;
					}
				} else {
					$kelas_setelahnya = $temp_array_hasil
					[$i+1];
					$counter_kelas = 0;
					$temp_array_hasil[$i][$j]['fitur_transisi'] = array();
					$temp_fitur = $temp_array_hasil[$i][$j]['fitur'];

					foreach ($temp_fitur as $key_fitur => $value_fitur) {
						# code...
						foreach ($array_tagset as $key_tagset => $value_tagset) {
							# code...
							$ind = $counter_kelas * 15 + $value_tagset['i'];
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
				if (isset($temp_array_hasil[$i][$j+1])) {
					$counter_kelas = 0;
					$temp_array_hasil[$i][$j]['fitur_transisi'] = array();
					$temp_fitur = $temp_array_hasil[$i][$j]['fitur'];

					foreach ($temp_fitur as $key_fitur => $value_fitur) {
						foreach ($array_tagset as $key_tagset => $value_tagset) {
							$ind = $counter_kelas * 15 + $value_tagset['i'];
							$temp_array_hasil[$i][$j]['fitur_transisi'][$ind]['tag_sebelumnya'] = $value_fitur['tag'];

							$temp_array_hasil[$i][$j]['fitur_transisi'][$ind]['tag_setelahnya'] = $value_tagset['tag'];
							if(($value_fitur['nilai'] == 1) && ($value_tagset['i'] == $value_fitur['i'])){
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

	//load parameter
	$lambda_fitur_node = array();
	$lambda_fitur_edge = array();

	$string_lambda_fitur_node = file_get_contents("lambda_fitur_node.txt");
	$string_lambda_fitur_edge = file_get_contents("lambda_fitur_edge.txt");

	$lambda_fitur_node = json_decode($string_lambda_fitur_node, true);
	$lambda_fitur_edge = json_decode($string_lambda_fitur_node, true);

	for ($i=0; $i < sizeof($temp_array_hasil); $i++) { 
		# code...
		$temp_array_hasil[$i] = $temp_array_hasil[$i];

		//hitung node dan edge potensial
		for ($j=0; $j < sizeof($temp_array_hasil[$i]); $j++) { 
			# code...
			$temp_array_hasil[$i][$j] = $temp_array_hasil[$i][$j];
			$temp_array_hasil[$i][$j]['node_potensial'] = array();
			$temp_array_hasil[$i][$j]['edge_potensial'] = array();

			for ($k=0; $k < 15; $k++) { 
				$node_potensial = exp(($lambda_fitur_node[$k] * $temp_array_hasil[$i][$j]['fitur'][$k]['nilai']));
				$temp_array_hasil[$i][$j]['node_potensial'][$k] = $node_potensial;
			}

			for ($k=0; $k < 225; $k++) { 
				$edge_potensial = exp(($lambda_fitur_edge[$k] * $temp_array_hasil[$i][$j]['fitur_transisi'][$k]['nilai']));
				$temp_array_hasil[$i][$j]['edge_potensial'][$k] = $edge_potensial;
			}
		}

		//menghitung max forward pass
		for ($j=0; $j < sizeof($temp_array_hasil[$i]); $j++) { 
			$temp_array_hasil[$i][$j] = $temp_array_hasil[$i][$j];
			$temp_array_hasil[$i][$j]['forward_var'] = array();

			if ($j == 0) {
				# code...
				for ($k=0; $k < 15; $k++) { 
					# code...
					$temp_array_hasil[$i][$j]['forward_var'][$k] = (1/15);
				}

				$forward_var_sebelumnya = $temp_array_hasil[$i][$j]['forward_var'];

				$maxs = array_keys($temp_array_hasil[$i][$j]['forward_var'], max($temp_array_hasil[$i][$j]['forward_var']));

				for ($o=0; $o < sizeof($maxs); $o++) { 
					# code...
					$temp_array_hasil[$i][$j]['max_forward_var'][$o]['index'] = $maxs[$o];
					$temp_array_hasil[$i][$j]['max_forward_var'][$o]['nilai'] = $temp_array_hasil[$i][$j]['forward_var'][$maxs[$o]];
				}
			} else {
				$jumlah_forward_var = 0;

				for ($k=0; $k < 15; $k++) { 
					# code...
					$jumlah_temp_forward_var = 0;

					$maxs = array_keys($forward_var_sebelumnya, max($forward_var_sebelumnya));
					for ($o=0; $o < sizeof($maxs); $o++) { 
						# code...
						$index_max_sebelumnya = $temp_array_hasil[$i][$j-1]['max_forward_var'][$o]['index'];
						$nilai_max_sebelumnya = $temp_array_hasil[$i][$j-1]['max_forward_var'][$o]['nilai'];
						$counter_edge = $index_max_sebelumnya * 15;

						$a = $nilai_max_sebelumnya;
						$b = $temp_array_hasil[$i][$j]['node_potensial'][$k];
						$c = $temp_array_hasil[$i][$j-1]['edge_potensial'][$counter_edge];

						$temp_forward_var = $a * $b * $c;
						$jumlah_temp_forward_var += $temp_forward_var;
					}

					$jumlah_forward_var += $jumlah_temp_forward_var;
					$temp_array_hasil[$i][$j]['forward_var'][$k] = $jumlah_forward_var;
				}

				$kt = (1/$jumlah_forward_var);

				for ($m=0; $m < sizeof($temp_array_hasil[$i][$j]['forward_var']); $m++) { 
					# code...
					$temp_array_hasil[$i][$j]['forward_var'][$m] = $kt * $temp_array_hasil[$i][$j]['forward_var'][$m];
				}

				$maxs = array_keys($temp_array_hasil[$i][$j]['forward_var'], max($temp_array_hasil[$i][$j]['forward_var']));
				for ($o=0; $o < sizeof($maxs); $o++) { 
					# code...
					$temp_array_hasil[$i][$j]['max_forward_var'][$o]['index'] = $maxs[$o];
					$temp_array_hasil[$i][$j]['max_forward_var'][$o]['nilai'] = $temp_array_hasil[$i][$j]['forward_var'][$maxs[$o]];
				}
				$forward_var_sebelumnya = $temp_array_hasil[$i][$j]['forward_var'];
			}
		}

		//backtracking
		for ($j=(sizeof($temp_array_hasil[$i])-1); $j >= 0; $j--) { 
			# code...
			$temp_array_hasil[$i][$j]['prediksi'] = array();
			$temp_array_hasil[$i][$j]['backtracking'] = array();

			for ($k=0; $k < 15; $k++) { 
				# code...
				$a = $temp_array_hasil[$i][$j]['forward_var'][$k];
				$b = $temp_array_hasil[$i][$j]['node_potensial'][$k];
				$temp_array_hasil[$i][$j]['backtracking'][$k] = $a * $b;
			}

			$maxs = array_keys($temp_array_hasil[$i][$j]['backtracking'], max($temp_array_hasil[$i][$j]['backtracking']));

			for ($o=0; $o < sizeof($maxs); $o++) { 
				# code...
				$temp_array_hasil[$i][$j]['prediksi'][$o]['index'] = $maxs[$o];
				$temp_array_hasil[$i][$j]['prediksi'][$o]['nilai'] = $temp_array_hasil[$i][$j]['backtracking'][$maxs[$o]];
				$temp_array_hasil[$i][$j]['prediksi'][$o]['tag'] = $array_tagset[$maxs[$o]]['tag'];
			}
		}

		//prediksi per baris
		for ($j=0; $j < sizeof($temp_array_hasil[$i]); $j++) { 
			# code...
			if ($j == 0) {
				# code...
				$prediksi_baris = $temp_array_hasil[$i][$j]['prediksi'][0]['index'];
				$prediksi_tag = $temp_array_hasil[$i][$j]['prediksi'][0]['tag'];
			} else {
				$prediksi_baris = $temp_array_hasil[$i][0]['prediksi'][0]['index'];
				$prediksi_tag = $temp_array_hasil[$i][0]['prediksi'][0]['tag'];		
			}
			$temp_array_hasil[$i][$j]['prediksi_label']['label'] = $prediksi_baris;
			$temp_array_hasil[$i][$j]['prediksi_label']['tag'] = $prediksi_tag;

		}
	}

	//confusion matrix
	for ($i=0; $i < sizeof($temp_array_hasil); $i++) { 
		# code...
		for ($j=0; $j < sizeof($temp_array_hasil[$i]); $j++) { 
			# code...
			$actual = $temp_array_hasil[$i][$j]['tag data'];
			$prediksi = $temp_array_hasil[$i][$j]['prediksi_label']['label'];

			$cm[$actual.".".$prediksi] += 1;
		}
	}

	for ($i=0; $i < 15; $i++) { 
		# code...
		for ($j=0; $j < 15; $j++) { 
			# code...
			$akurasi[$i.".".$j] = $akurasi[$i.".".$j] + $cm[$i.".".$j];
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

	$json = json_encode($hasil_praproses);
	file_put_contents("Hasil_praproses_data_testing.txt", $json);

	$json = json_encode(utf8ize($temp_array_hasil));
	file_put_contents("Hasil_pengujian.txt", $json);

	$json = json_encode($akurasi);
	file_put_contents("akurasi.txt", $json);

	echo "<script>alert('Proses pengujian berhasil!');window.location.href='hasil_testing.php';</script>";
?>
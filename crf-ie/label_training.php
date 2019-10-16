<?php
	$array_tagset = array(
		array ("i" => "0", "tag" => "Kepala Surat", "nilai" => "0" ), //jenis surat
		array ("i" => "1", "tag" => "Jenis Surat", "nilai" => "0" ), //jenis surat
		array ("i" => "2", "tag" => "Nomor Surat", "nilai" => "0" ), //nomor surat
		array ("i" => "3", "tag" => "Tentang", "nilai" => "0" ), //tentang
		array ("i" => "4", "tag" => "Jabatan yang Menetapkan Surat", "nilai" => "0" ), //yang menetapkan surat
		array ("i" => "5", "tag" => "Menimbang", "nilai" => "0" ), //menimbang
		array ("i" => "6", "tag" => "Mengingat", "nilai" => "0" ), //mengingat
		array ("i" => "7", "tag" => "Putusan", "nilai" => "0" ), //putusan
		array ("i" => "8", "tag" => "Nama yang Diputuskan", "nilai" => "0" ), //nama yang dituju
		array ("i" => "9", "tag" => "Nomor Induk yang Diputuskan", "nilai" => "0" ), //ni yang dituju
		array ("i" => "10", "tag" => "Isi Putusan", "nilai" => "0" ), //isi putusan
		array ("i" => "11", "tag" => "Tempat Diputuskan", "nilai" => "0" ), //tempat
		array ("i" => "12", "tag" => "Tanggal Diputuskan", "nilai" => "0" ), //tanggal ditetapkan
		array ("i" => "13", "tag" => "Organisasi", "nilai" => "0" ), //organisasi
		array ("i" => "14", "tag" => "Nama yang Menetapkan", "nilai" => "0" ), //nama yang menetapkan
		array ("i" => "15", "tag" => "Tembusan", "nilai" => "0" ) //nama yang menetapkan
	);

	//load baris data
	$tokens = array();
	$string_array = file_get_contents("baris_training.txt");
	$tokens = json_decode($string_array, true);
?>
<html>
	<head>
		<!-- bootstrap -->
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
		<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
		<!-- title -->
		<title>Pelatihan - CRF-Ekstraksi Informasi</title>
	</head>
	<body>
		<!-- navbar -->
		<nav class="navbar navbar-dark bg-dark">
		  <a class="navbar-brand" href="index.php">CRF-Ekstraksi Informasi</a>
		  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
		    <span class="navbar-toggler-icon"></span>
		  </button>

		  <div class="collapse navbar-collapse" id="navbarSupportedContent">
		    <ul class="navbar-nav mr-auto">
		      <li class="nav-item">
		        <a class="nav-link" href="index.php">Halaman Utama</a>
		      </li>
		      <li class="nav-item active dropdown">
		        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		          Pelatihan
		        </a>
		        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
		          <a class="dropdown-item" href="training.php">Unggah Data</a>
		          <a class="dropdown-item" href="hasil_training.php">Hasil</a>
		        </div>
		      </li>
		      <li class="nav-item dropdown">
		        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		          Testing
		        </a>
		        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
		          <a class="dropdown-item" href="testing.php">Unggah Data</a>
		          <a class="dropdown-item" href="hasil_testing.php">Hasil</a>
		        </div>
		      </li>
		    </ul>
		  </div>
		</nav>
		<!-- container -->
		<div class="container">
			<h1>Labeling</h1>
			<hr>
			<form action="" enctype="multipart/form-data" method="post">
			<?php
				for ($i=0; $i < sizeof($tokens['baris']); $i++) { 
					# code...
					//input label
			?>	
				<div class="form-row">
				<div class="form-group col-md-8">
					<input class="form-control" type="text" value="<?php echo $tokens['baris'][$i] ?>">
				</div>
				<div class="form-group col-md-4">
				    <select class="form-control" name="label<?php echo $i; ?>">
			<?php
				for ($j=0; $j < sizeof($array_tagset); $j++) { 
					# code...
			?>
					<option value="<?php echo $array_tagset[$j]['i']; ?>"><?php echo $array_tagset[$j]['tag']; ?></option>
			<?php
				}
			?>   
			    	</select>
			    </div>
			  	</div>
			<?php
				}
			?>
			<button type="submit" name="proses2" class="btn btn-primary">Submit</button>	
			</form>		
		</div>
	</body>
</html>
<?php
	if (isset($_POST['proses2'])) {
		# code...
		for ($i=0; $i < sizeof($tokens['baris']); $i++) { 
			# code...
			$label = $_POST['label'.$i];
			$tokens['label'][] = $label;
		}

		$json = json_encode($tokens);
		file_put_contents("label_baris_training.txt", $json);

		echo "<script>alert('Proses labeling berhasil!');window.location.href='train.php';</script>";
	}
?>
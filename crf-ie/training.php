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
		<div class="jumbotron">
		  <h1 class="display-4">Pelatihan!</h1>
		  <p class="lead">Anda dapat mengunggah file CSV untuk data latih pada formulir dibawah ini!</p>
		  <hr class="my-4">
		  <!-- form upload -->
		  <form method="post" action="" enctype="multipart/form-data">
			<div class="form-group">
			  <label for="exampleFormControlFile1">Unggah data latih.</label>
			  <input type="file" class="form-control-file" name="fileToUpload" id="fileToUpload" accept=".csv" required="required">
			</div>
			<button type="submit" class="btn btn-primary" name="proses">Latih!</button>
		  </form>
		</div>
	</body>
</html>
<?php
	if (isset($_POST['proses'])) {
		if ($_FILES["fileToUpload"]["size"] > 100000000) {
			echo "<script>alert('Ukuran file terlalu besar, silakan coba lagi!');</script>";
		} else {
			$namafileasal = basename($_FILES["fileToUpload"]["name"]);
			$namafiletujuan = "data_training.csv";
			$url = 'http://localhost/crf-ie/';

			if(move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $namafiletujuan)){
				echo "<script>alert('Proses unggah data latih berhasil!');window.location.href='train.php';</script>";
			} else {
				echo "<script>alert('Proses unggah data latih gagal, silakan coba lagi!');</script>";
			}
		}
		
	}
?>
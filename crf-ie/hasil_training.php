<?php
	require_once __DIR__ . '/function.php';
	ini_set('memory_limit','4000M');
	ini_set('max_execution_time', 72000);
	error_reporting(0);

	$temp_array_hasil = array(); 

	$string_array_kalimat = file_get_contents("Hasil.txt");

	$temp_array_hasil = json_decode($string_array_kalimat, true);

	$string_array_parameter = file_get_contents("parameter.txt");

	$array_parameter = json_decode($string_array_parameter, true);

	$banyak_node = 15;
	$banyak_edge = 225;
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
		<div class="container">
					<div class="row">
						<div class="col-lg-12 col-md-12">
							<div class="panel panel-default">
								<div class="panel-heading">Ekstraksi Fitur Node</div>
								<div class="panel-body">
									<div style="overflow-y: scroll; height: 250px;">
										<div class="table-responsive">
											<table class="table table-striped table-bordered table-hover">
												<thead>
													<tr>
														<th class="text-center">Kata</th>
														<?php
														for ($node=1; $node < $banyak_node+1; $node++) { 
														?>
															<th class="text-center"><?php echo "f".$node; ?></th>
														<?php
														}
														?>
													</tr>
												</thead>
												<tbody>	
													<?php
														for ($i=0; $i < 1; $i++) { 
															
															for ($j=0; $j < sizeof($temp_array_hasil[$i]); $j++) { 
																echo"<tr>";
																echo "<td>".$temp_array_hasil[$i][$j]['kata']."</td>";
																for ($k=0; $k < $banyak_node; $k++) { 
																	echo "<td>".$temp_array_hasil[$i][$j]['fitur'][$k]['nilai']."</td>";
																}
																echo"</tr>";
															}
														}
													?>																
												</tbody>
											</table>
										</div>
									</div>		
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12 col-md-12">
							<div class="panel panel-default">
								<div class="panel-heading">Ekstraksi Fitur Edge</div>
								<div class="panel-body">
									<div style="overflow-y: scroll; height: 250px;">
										<div class="table-responsive">
											<table class="table table-striped table-bordered table-hover">
												<thead>
													<tr>
														<th class="text-center">Kata</th>
														<?php
														for ($edge=1; $edge < $banyak_edge+1; $edge++) { 
														?>
															<th class="text-center"><?php echo "f".$edge; ?></th>
														<?php
														}
														?>
													</tr>
												</thead>
												<tbody>	
													<?php
														for ($i=0; $i < 1; $i++) { 
															
															for ($j=0; $j < sizeof($temp_array_hasil[$i]); $j++) { 
																echo"<tr>";
																echo "<td>".$temp_array_hasil[$i][$j]['kata']."</td>";
																for ($k=0; $k < $banyak_edge; $k++) { 
																	echo "<td>".$temp_array_hasil[$i][$j]['fitur_transisi'][$k]['nilai']."</td>";
																}
																echo"</tr>";
															}
														}
													?>																
												</tbody>
											</table>
										</div>
									</div>		
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12 col-md-12">
							<div class="panel panel-default">
								<div class="panel-heading">Node Potensial</div>
								<div class="panel-body">
									<div style="overflow-y: scroll; height: 250px;">
										<div class="table-responsive">
											<table class="table table-striped table-bordered table-hover">
												<thead>
													<tr>
														<th class="text-center">Kata</th>
														<?php
														for ($node=1; $node < $banyak_node+1; $node++) { 
														?>
															<th class="text-center"><?php echo "f".$node; ?></th>
														<?php
														}
														?>
													</tr>
												</thead>
												<tbody>	
													<?php
														for ($i=0; $i < 1; $i++) { 
															
															for ($j=0; $j < sizeof($temp_array_hasil[$i]); $j++) { 
																echo"<tr>";
																echo "<td>".$temp_array_hasil[$i][$j]['kata']."</td>";
																for ($k=0; $k < $banyak_node; $k++) { 
																	echo "<td>".$temp_array_hasil[$i][$j]['node_potensial'][$k]."</td>";
																}
																echo"</tr>";
															}
														}
													?>																
												</tbody>
											</table>
										</div>
									</div>		
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12 col-md-12">
							<div class="panel panel-default">
								<div class="panel-heading">Edge Potensial</div>
								<div class="panel-body">
									<div style="overflow-y: scroll; height: 250px;">
										<div class="table-responsive">
											<table class="table table-striped table-bordered table-hover">
												<thead>
													<tr>
														<th class="text-center">Kata</th>
														<?php
														for ($edge=1; $edge < $banyak_edge+1; $edge++) { 
														?>
															<th class="text-center"><?php echo "f".$edge; ?></th>
														<?php
														}
														?>
													</tr>
												</thead>
												<tbody>	
													<?php
														for ($i=0; $i < 1; $i++) { 
															
															for ($j=0; $j < sizeof($temp_array_hasil[$i]); $j++) { 
																echo"<tr>";
																echo "<td>".$temp_array_hasil[$i][$j]['kata']."</td>";
																for ($k=0; $k < $banyak_edge; $k++) { 
																	echo "<td>".$temp_array_hasil[$i][$j]['edge_potensial'][$k]."</td>";
																}
																echo"</tr>";
															}
														}
													?>																
												</tbody>
											</table>
										</div>
									</div>		
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12 col-md-12">
							<div class="panel panel-default">
								<div class="panel-heading">Forward Pass</div>
								<div class="panel-body">
									<div style="overflow-y: scroll; height: 250px;">
										<div class="table-responsive">
											<table class="table table-striped table-bordered table-hover">
												<thead>
													<tr>
														<th class="text-center">Kata</th>
														<?php
														for ($node=1; $node < $banyak_node+1; $node++) { 
														?>
															<th class="text-center"><?php echo "f".$node; ?></th>
														<?php
														}
														?>
													</tr>
												</thead>
												<tbody>	
													<?php
														for ($i=0; $i < 1; $i++) { 
															
															for ($j=0; $j < sizeof($temp_array_hasil[$i]); $j++) { 
																echo"<tr>";
																echo "<td>".$temp_array_hasil[$i][$j]['kata']."</td>";
																for ($k=0; $k < $banyak_node; $k++) { 
																	echo "<td>".$temp_array_hasil[$i][$j]['forward_var'][$k]."</td>";
																}
																echo"</tr>";
															}
														}
													?>																
												</tbody>
											</table>
										</div>
									</div>		
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12 col-md-12">
							<div class="panel panel-default">
								<div class="panel-heading">Backward Pass</div>
								<div class="panel-body">
									<div style="overflow-y: scroll; height: 250px;">
										<div class="table-responsive">
											<table class="table table-striped table-bordered table-hover">
												<thead>
													<tr>
														<th class="text-center">Kata</th>
														<?php
														for ($node=1; $node < $banyak_node+1; $node++) { 
														?>
															<th class="text-center"><?php echo "f".$node; ?></th>
														<?php
														}
														?>
													</tr>
												</thead>
												<tbody>	
													<?php
														for ($i=0; $i < 1; $i++) { 
															
															for ($j=0; $j < sizeof($temp_array_hasil[$i]); $j++) { 
																echo"<tr>";
																echo "<td>".$temp_array_hasil[$i][$j]['kata']."</td>";
																for ($k=0; $k < $banyak_node; $k++) { 
																	echo "<td>".$temp_array_hasil[$i][$j]['backward_var'][$k]."</td>";
																}
																echo"</tr>";
															}
														}
													?>																
												</tbody>
											</table>
										</div>
									</div>		
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12 col-md-12">
							<div class="panel panel-default">
								<div class="panel-heading">Probabilitas Node</div>
								<div class="panel-body">
									<div style="overflow-y: scroll; height: 250px;">
										<div class="table-responsive">
											<table class="table table-striped table-bordered table-hover">
												<thead>
													<tr>
														<th class="text-center">Kata</th>
														<?php
														for ($node=1; $node < $banyak_node+1; $node++) { 
														?>
															<th class="text-center"><?php echo "f".$node; ?></th>
														<?php
														}
														?>
													</tr>
												</thead>
												<tbody>	
													<?php
														for ($i=0; $i < 1; $i++) { 
															
															for ($j=0; $j < sizeof($temp_array_hasil[$i]); $j++) { 
																echo"<tr>";
																echo "<td>".$temp_array_hasil[$i][$j]['kata']."</td>";
																for ($k=0; $k < $banyak_node; $k++) { 
																	echo "<td>".$temp_array_hasil[$i][$j]['probabilitas_node'][$k]."</td>";
																}
																echo"</tr>";
															}
														}
													?>																
												</tbody>
											</table>
										</div>
									</div>		
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12 col-md-12">
							<div class="panel panel-default">
								<div class="panel-heading">Probabilitas Edge</div>
								<div class="panel-body">
									<div style="overflow-y: scroll; height: 250px;">
										<div class="table-responsive">
											<table class="table table-striped table-bordered table-hover">
												<thead>
													<tr>
														<th class="text-center">Kata</th>
														<?php
														for ($edge=1; $edge < $banyak_edge+1; $edge++) { 
														?>
															<th class="text-center"><?php echo "f".$edge; ?></th>
														<?php
														}
														?>
													</tr>
												</thead>
												<tbody>	
													<?php
														for ($i=0; $i < 1; $i++) { 
															
															for ($j=0; $j < sizeof($temp_array_hasil[$i]); $j++) { 
																echo"<tr>";
																echo "<td>".$temp_array_hasil[$i][$j]['kata']."</td>";
																for ($k=0; $k < $banyak_edge; $k++) { 
																	echo "<td>".$temp_array_hasil[$i][$j]['probabilitas_edge'][$k]."</td>";
																}
																echo"</tr>";
															}
														}
													?>																
												</tbody>
											</table>
										</div>
									</div>		
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12 col-md-12">
							<div class="panel panel-default">
								<div class="panel-heading">Gradien dan Parameter Fitur Node</div>
								<div class="panel-body">
									<div style="overflow-y: scroll; height: 250px;">
										<div class="table-responsive">
											<table class="table table-striped table-bordered table-hover">
												<thead>
													<tr>
														<th><b> Nomor Fitur<b></td>
														<th class="text-center"><b> Nilai Gradien <b></td>
														<th class = "text-center"><b> Nilai Parameter<b></td>
													</tr>
												</thead>
												<tbody>	
													<?php
													for ($i = 0; $i < $banyak_node; $i++) {
														?>
														<tr>
															<td class="angka" align="center"><?php echo $i+1?></td>
															<td class ="angka"><?php echo bulat($array_parameter['gradien_fitur_node'][$i]); ?></td>
															<td class ="angka"><?php echo bulat($array_parameter['lambda_fitur_node'][$i]); ?></td>
														</tr>
														<?php
													}
													?>																
												</tbody>
											</table>
										</div>
									</div>		
								</div>
							</div>
						</div>
						<div class="col-lg-12 col-md-12">
							<div class="panel panel-default">
								<div class="panel-heading">Gradien dan Parameter Fitur Edge</div>
								<div class="panel-body">
									<div style="overflow-y: scroll; height: 250px;">
										<div class="table-responsive">
											<table class="table table-striped table-bordered table-hover">
												<thead>
													<tr>
														<th><b> Nomor Fitur<b></td>
														<th class="text-center"><b> Nilai Gradien <b></td>
														<th class = "text-center"><b> Nilai Parameter<b></td>
													</tr>
												</thead>
												<tbody>	
													<?php
													for ($i = 0; $i < $banyak_edge; $i++) {
														?>
														<tr>
															<td class="angka" align="center"><?php echo $i+1?></td>
															<td class ="angka"><?php echo bulat($array_parameter['gradien_fitur_edge'][$i]); ?></td>
															<td class ="angka"><?php echo bulat($array_parameter['lambda_fitur_edge'][$i]); ?></td>
														</tr>
														<?php
													}
													?>																
												</tbody>
											</table>
										</div>
									</div>		
								</div>
							</div>
						</div>
					</div>
			<!-- /.row -->
		</div>
		<!-- /#page-wrapper -->
	</body>
</html>
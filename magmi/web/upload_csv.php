<?php 
if($_SERVER["REQUEST_METHOD"] == "POST") {
// 	$pathMagento = "/home/zerma/magentostore/var/import/";
	$pathMagento = "/wwwroot/dev.obravip.com/var/import/";
	
	$csv_new_name = "csv_".date("D_m_Y_H_i_s").".csv";
	if(move_uploaded_file($_FILES['file_csv']['tmp_name'], $pathMagento.$csv_new_name)) {
		header("Location: magmi.php");
	}
	else {
		$error = "Ocorreu um erro ao tentar enviar o arquivo.";
	}
}

?>
<?php require("head.php"); require("header.php"); ?>
<style type="text/css">
	div {
		float: left;
		margin: 10px;
}
</style>
</head>
<body>
	<div class="container_12" >
		<div class="mgupload_info" style="margin-top:5px; background-color: red; <?php print(isset($error) ? 'display: block;':'display: none;'); ?>">
			<?php print(isset($error) ? $error : ""); ?>
		</div>
		<div class="grid_12 subtitle"><span>Importar arquivo CSV</span></div>
		<div class="mgupload_info" style="margin-top:5px">
			<form action="" method="post" enctype="multipart/form-data">
				<label>Arquivo CSV</label>
				<input type="file" name="file_csv">
				<input type="submit" value="Enviar">
			</form>
		</div>
	</div>
</body>
</html>
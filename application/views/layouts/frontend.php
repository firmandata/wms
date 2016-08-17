<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8"/>
		<title><?php echo $title; ?></title>
		
		<link type="text/css" rel="stylesheet" media="screen" href="<?php echo base_url('css/frontend/style.css');?>"/>
		
		<!-- Javascript @ jQuery -->
		<script type="text/javascript" src="<?php echo base_url('js/jquery/jquery-2.1.1.min.js');?>"></script> <!-- Core -->
	</head>
	<body>
<?php echo $login; ?>
<?php echo $content; ?>
	</body>
</html>
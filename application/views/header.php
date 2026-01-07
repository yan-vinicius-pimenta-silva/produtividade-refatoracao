<!DOCTYPE html>
<html lang="pt-BR">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=Edge">
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<meta name="robots" content="noindex">
	<title>Painel | PRODUTIVIDADE - PMA</title>
	<!-- Favicon-->
	<link rel="icon" href="<?= base_url() ?>/images/favicon.gif" type="image/x-icon">

	<!-- Google Fonts -->
	<link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">

	<!-- Bootstrap Core Css -->
	<link href="<?= base_url() ?>plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">

	<!-- Waves Effect Css -->
	<link href="<?= base_url() ?>plugins/node-waves/waves.css" rel="stylesheet" />

	<!-- Animation Css -->
	<link href="<?= base_url() ?>plugins/animate-css/animate.css" rel="stylesheet" />

	<!-- Bootstrap Spinner Css -->
	<link href="<?= base_url() ?>plugins/jquery-spinner/css/bootstrap-spinner.css" rel="stylesheet">

	<!-- Morris Chart Css-->
	<link href="<?= base_url() ?>plugins/morrisjs/morris.css" rel="stylesheet" />

	<!-- JQuery DataTable Css -->
	<link href="<?= base_url() ?>plugins/jquery-datatable/skin/bootstrap/css/dataTables.bootstrap.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="<?= base_url() ?>plugins/table/responsive.dataTables.min.css">

	<!-- Bootstrap Select Css  -->
	<link href="<?= base_url() ?>plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" />

	<!-- Bootstrap Select2  -->
	<!-- <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" /> -->

	<!-- Multi Select Css -->
	<link href="<?= base_url() ?>plugins/multi-select/css/multi-select.css" rel="stylesheet">

	<!-- <link rel="stylesheet" href="js/custom/datatables.min.css"> -->
	<link href="<?= base_url() ?>css/custom/jquery-confirm.css" rel="stylesheet" type="text/css" />
	<link href="<?= base_url() ?>css/custom/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />

	<!-- Custom Css -->
	<link href="<?= base_url() ?>css/style.css" rel="stylesheet">

	<!-- AdminBSB Themes. You can choose a theme from css/themes instead of get all themes -->
	<link href="<?= base_url() ?>css/themes/all-themes.css" rel="stylesheet" />

	<!-- datetimepicker -->
	<link href="<?= base_url() ?>plugins/eonasdan-bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" />

	<style>
		.ui-autocomplete {
			position: absolute;
			z-index: 1000;
			cursor: default;
			padding: 0;
			margin-top: 2px;
			list-style: none;
			background-color: #ffffff;
			border: 1px solid #ccc;
			-webkit-border-radius: 5px;
			-moz-border-radius: 5px;
			border-radius: 5px;
			-webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
			-moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
			box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
		}

		.ui-autocomplete>li {
			padding: 3px 20px;
		}

		.ui-autocomplete>li.ui-state-focus {
			background-color: #DDD;
		}

		.ui-helper-hidden-accessible {
			display: none;
		}

		/* bootstrap-seletc fix */
		.dropdown-menu.open {
			overflow: inherit !important;
		}
	</style>

	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-105726941-23"></script>
	<script>
		window.dataLayer = window.dataLayer || [];

		function gtag() {
			dataLayer.push(arguments);
		}
		gtag('js', new Date());

		gtag('config', 'UA-105726941-23');
	</script>


</head>

<body class="theme-teal">
	<div class="page-loader-wrapper">
		<div class="loader">
			<div class="preloader">
				<div class="spinner-layer pl-red">
					<div class="circle-clipper left">
						<div class="circle"></div>
					</div>
					<div class="circle-clipper right">
						<div class="circle"></div>
					</div>
				</div>
			</div>
			<p>Por favor, aguarde...</p>
		</div>
	</div>
	<div class="overlay"></div>

	<nav class="navbar">
		<div class="container-fluid">
			<div class="navbar-header">
				<a href="javascript:void(0);" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse" aria-expanded="false"></a>
				<a href="javascript:void(0);" class="bars"></a>
				<a class="navbar-brand" href="<?= base_url() ?>home"><?= $this->session->userdata['logged_in']['nome_empresa'] ?></a>
			</div>
			<div class="collapse navbar-collapse" id="navbar-collapse">
				<ul class="nav navbar-nav navbar-right">
					<li>
						<a href="<?= base_url() ?>login/logout" class="js-search" data-close="true">
							<i class="material-icons">input</i> <span style="position:relative;top:-6px">SAIR</span>
						</a>
					</li>
				</ul>
			</div>
		</div>
	</nav>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>PRODUTIVIDIDADE - PMA</title>
    <!-- Favicon-->
    <link rel="icon" href="<?=base_url()?>favicon.gif" type="image/x-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">

    <!-- Bootstrap Core Css -->
    <link href="<?=base_url()?>plugins/bootstrap/css/bootstrap.css" rel="stylesheet">

    <!-- Waves Effect Css -->
    <link href="<?=base_url()?>plugins/node-waves/waves.css" rel="stylesheet" />

    <!-- Animation Css -->
    <link href="<?=base_url()?>plugins/animate-css/animate.css" rel="stylesheet" />

    <!-- Custom Css -->
    <link href="<?=base_url()?>css/style.css" rel="stylesheet">
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=false&key=AIzaSyDNc3Xy5T_QmFqHuNHXlWSjm0zFu_Sk6PM"></script>

</head>
<style>
    #map-canvas {
      height: 100%;
      position: absolute; 
      top: 0; 
      bottom: -200px; 
      left: 0; 
      right: 0; 
      z-index: 0;
    }
    p {
        margin:inherit;
    }
</style>
<body class="login-page" style="background-color:#838888;background-size:100%">
    <div id='map-canvas'></div>
    <div class="login-box">
    <div class="logo" style="position: relative;">
            <a href="javascript:void(0);">Prefeitura de <b>Araras</b></a>
            <small>Departamento de Tecnologia da Informação</small>
        </div>
        <div class="card">
            <div class="body">
                <form id="login" method="POST" action="<?=base_url()?>login/receiveLogin">
                    <div class="msg">Logue para iniciar sessão</div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">person</i>
                        </span>
                        <div class="form-line">
                            <input type="text" class="form-control" name="username" placeholder="Usuário" required autofocus>
                        </div>
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">lock</i>
                        </span>
                        <div class="form-line">
                            <input type="password" class="form-control" name="password" placeholder="Senha" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <button class="btn btn-block bg-green waves-effect" type="submit">ENTRAR</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script type="text/javascript" src="<?=base_url()?>js/login.min.js"></script>

    <?php
      if($this->session->flashdata('result'))
        echo '<script>showNotification("bg-red", "'.trim($this->session->flashdata('result')).'", "bottom", "center", "", "");</script>';
    ?>

    <?php
        if($this->session->flashdata('result_ok'))
            echo '<script>showNotification("bg-green", "'.trim($this->session->flashdata('result_ok')).'", "bottom", "center", "", "");</script>';
    ?>

</body>

</html>
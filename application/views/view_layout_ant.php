<?php
    $Admin = true;
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8" />
 <title>Erp Cubifact</title>
 <meta name="viewport" content="width=device-width, initial-scale=1">
  <!--<lin--k rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">-->
 
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">

    <link href="<?= base_url("assets/plugins/font-awesome/css/font-awesome.css") ?>" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="<?= base_url("assets/js/funciones.js") ?>"></script>
     
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <!--<scrrript src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>-->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

 <style type="text/css">
    body{
        /*background-color: rgb(250,250,200);  amarillo perla */
        color: rgb(0,50,150);
    }
    .mariposa{
        float: left; margin: 0px 0px 0px 1px; padding: 3px 2px; width: 100px; height: 40px;
        text-align: center; border-style: none; border-color: red; border-width: 1px; font-family: 'Arial'; font-size: 11px;
    }

 </style>
</head>
<body class="">

    <div class="container-fluid">

        <div class="row" style="border-style:solid; border-color:gray; border-radius: 10px; background-color: rgb(220,220,220); height: 950px;">

            <div id="menuzote" class="col-xs-12 col-sm-1 col-md-1 col-lg-1" style="border-style:none; border-color: gray; margin-left: 0px; height: 794px; border-radius: 7px; padding-right:0px; padding-left:0px;">
                <nav class="el_menu">
                    <?= $this->fm->menu_principal2($Admin, "", ""); ?>
                </nav>
            </div>

            <div class="col-xs-12 col-sm-11 col-md-11 col-lg-11" style="border-style:none; border-color: gray; margin-left:0px">
                <?php if(isset($msg)){ ?>
    			<div class="row">
    				<div class="col-xs-12 col-sm-12" style="margin-top:7px;">
    					<div class="alert alert-<?= isset($rpta_msg) ? $rpta_msg : "success" ?>"> <?= $msg ?> </div>
    				</div>
    			</div>
    			<?php } ?>
    			<div class="row" style="display:flex; margin-left: 10px; background-color:linear-gradient(to right, rgb(70,70,73) , rgb(200,150,0));">
                    <div class="col-xs-10 col-sm-9" style="padding: 5px 0px 5px 15px; height:70px; background-image: linear-gradient(to right, rgb(70,70,73) , rgb(200,150,0));">
                        <span><a href="#" id="acople" onclick="acople_menu()">Ocultar men&uacute;</a></span><br>
                        <h2 style="margin-left:10px; margin-top:5px">
                        <?php 
                            if(isset($page_title)){
                                echo $page_title;
                            }else{
                                echo "";
                            }
                        ?>
                        </h2>
                    </div>
                    <div class="col-xs-2 col-sm-3" style="margin:auto; padding: 5px 0px 5px 15px; background-color:rgb(190,190,190); height:70px;">
                        <span style="text-align: right;">
                        <span style="font-weight:bold;font-style: italic;">Fecha: </span><?php echo date("Y-m-d H:i:s"); ?><br>
                        <span style="font-weight:bold;font-style: italic;">Usuario: </span><?php echo $_SESSION["usuario"]; ?>
                        &nbsp;&nbsp;&nbsp; <?= ini_get('session.gc_maxlifetime') . " " . ini_get('session.cookie_lifetime') ?><br>
                        <span style="font-weight:bold;font-style: italic;">Tienda: </span><?php echo $_SESSION["nombre_tienda"]; ?>
                        </span>
                    </div>

                </div>

                   
            </div>
            
        </div>

        <?= $contents ?>   

        <div class="row">
            <div class="col-xs-12 col-sm-12" style="text-align: center;">
                <span style="text-align: center;">JFK System</span>
            </div>
        </div>

    </div>

    <script type="text/javascript">
        function acople_menu(){
            var estado = document.getElementById('menuzote').style.display
            if(estado == 'none' && estado != 'block'){
                document.getElementById('menuzote').style.display = 'block';
                document.getElementById('acople').innerHTML = "Ocultar men&uacute;"
            }else{
                document.getElementById('menuzote').style.display = 'none';
                document.getElementById('acople').innerHTML = "Mostrar men&uacute;"
            }
        }
    </script>
</body>
</html>
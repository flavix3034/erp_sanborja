<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
	if(!isset($desde)){ $desde = "null"; }
	if(!isset($hasta)){ $hasta = "null"; }
	if(!isset($store_id)){ $store_id = $_SESSION["store_id"];}
?>
<style type="text/css">
	.filitas{
		margin-top: 10px;
	}
</style>
<script type="text/javascript">
	var store_id = <?= $store_id ?>;
	function activo1(){
		let desde = document.getElementById("desde").value
		let hasta = document.getElementById("hasta").value

		if(desde.length == 0){
			desde = 'null'
		}

		if(hasta.length == 0){
			hasta = 'null'
		}

		document.getElementById('refresco').innerHTML = '<a href="<?= base_url() ?>gastos/index/' + store_id + '/' + desde + '/' + hasta + '" id="enlace_grilla_gastos">Ejecutar</a>'
		document.getElementById('preparo').style.display = "none"
		document.getElementById('enlace_grilla_gastos').click()
	}
</script>
<section class="content">

	<div class="row" style="display:flex;margin-top: 15px; margin-bottom: 5px;">
		<div class="col-sm-4 col-md-3">
			<div class="form-group">
				<label for="">Desde:</label>
				<input type="date" name="desde" id="desde" class="form-control" value="<?= ($desde != 'null' ? $desde : '') ?>">
			</div>
		</div>

		<div class="col-sm-4 col-md-3">
			<div class="form-group">
				<label for="">Hasta:</label>
				<input type="date" name="hasta" id="hasta" class="form-control" value="<?= ($hasta != 'null' ? $hasta : '') ?>">
			</div>
		</div>

		<div id="preparo" class="col-sm-2 col-md-1" style="margin: auto;">
			<br><a href="#" onclick="activo1()" class="btn btn-primary"><b>Consultar</b></a>
		</div>

		<div id="refresco" class="col-sm-1"></div>

		<div class="col-sm-2" style="margin: auto;">
			<br><a href="<?= base_url('gastos/add') ?>" class="btn btn-success"><i class="fa fa-plus"></i> Nuevo Gasto</a>
		</div>

		<div class="col-sm-2" style="margin: auto;">
			<br><a href="<?= base_url('gastos/categorias') ?>" class="btn btn-default"><i class="fa fa-tags"></i> Categorias</a>
		</div>
	</div>

	<div class="row" id="grilla">
		<div class="col-12">
		<table id="example" class="display" style="width:99%; font-size: 12px; margin-bottom: 20px;" data-page-length='12'>
			<thead>
				<tr>
					<th>Id</th>
					<th>Fecha</th>
					<th>TipoDoc</th>
					<th>NroDoc</th>
					<th>Proveedor</th>
					<th>Conceptos</th>
					<th>Total</th>
					<th>Estado Pago</th>
					<th>Comp.</th>
					<th>Acciones</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
				</tr>
			</tfoot>
		</table>
		</div>
	</div>

	<!--*** FORMULARIO MODAL POPUP BOOTSTRAP ****-->
	<span id="btn_ver" data-toggle="modal" data-target="#myModal"></span>

	<!-- Modal -->
	<div class="modal fade" id="myModal" role="dialog">
		<div class="modal-dialog modal-lg">
		  <div class="modal-content">
			<div class="modal-header">
			  <h4 class="modal-title" id="titulo_modal_1">Gasto</h4>
			  <button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body" id="body_modal_1">
			</div>
			<div class="modal-footer">
			  <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
			</div>
		  </div>
		</div>
	</div>

</section>

<script type="text/javascript">

	$(document).ready(function() {
		$('#example').DataTable({
			dom: 'Bfrtip',
			buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
			"ajax": "<?= base_url("gastos/get_gastos/{$store_id}/{$desde}/{$hasta}") ?>",
			"columnDefs":[
				{ className: "text-center", "targets": [7, 8, 9] }
			]
		});
	});

	function editar(id){
		window.location.href = '<?= base_url("gastos/add/") ?>' + id
	}

	function ver(id){
		$.ajax({
			data    :{id:id},
			type    :"get",
			url     :"<?= base_url("gastos/ver") ?>",
			success :function(res){
				document.getElementById("titulo_modal_1").innerHTML = "Gasto Id:" + id
				document.getElementById("body_modal_1").innerHTML = res
				document.getElementById("btn_ver").click()
			}
		})
	}

	function eliminar(id){
		if (confirm("Desea eliminar este gasto?")){
			$.ajax({
				data    :{id:id},
				type    :"get",
				url     :"<?= base_url("gastos/eliminar") ?>",
				success :function(res){
					var obj = JSON.parse(res)
					if (obj.rpta_msg == "success"){
						alert("Se elimino correctamente el Gasto.")
						location.reload()
					}else{
						alert(obj.message)
					}
				}
			})
		}
	}
</script>

<div class="row filitas">
	<div class="col-sm-12">
		<h3><?= $page_title ?></h3>
	</div>
</div>

<!-- Filtros -->
<div class="row filitas">
	<div class="col-sm-12">
		<div class="panel panel-default">
			<div class="panel-body">
				<form id="filterForm">
					<div class="row">
						<div class="col-sm-3">
							<label>Estado:</label>
							<?php 
							echo form_dropdown('estado', $estados, $estado, 'class="form-control" id="estadoFilter"');
							?>
						</div>
						<div class="col-sm-3">
							<label>Técnico:</label>
							<?php 
							$ar_tecnicos = array('0' => '-- Todos --');
							foreach($tecnicos as $tec) {
								$ar_tecnicos[$tec->id] = $tec->nombre;
							}
							echo form_dropdown('tecnico', $ar_tecnicos, $tecnico, 'class="form-control" id="tecnicoFilter"');
							?>
						</div>
						<div class="col-sm-3">
							<label>&nbsp;</label><br>
							<button type="button" class="btn btn-primary" onclick="applyFilters()">
								<i class="glyphicon glyphicon-search"></i> Aplicar Filtros
							</button>
							<button type="button" class="btn btn-default" onclick="clearFilters()">
								<i class="glyphicon glyphicon-refresh"></i> Limpiar
							</button>
						</div>
						<div class="col-sm-3">
							<label>&nbsp;</label><br>
							<a href="<?= base_url('servicios/add') ?>" class="btn btn-success">
								<i class="glyphicon glyphicon-plus"></i> Nuevo Servicio
							</a>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- Tabla de Servicios -->
<div class="row filitas">
	<div class="col-sm-12">
		<div class="panel panel-default">
			<div class="panel-body">
				<div class="table-responsive">
					<table id="servicios_table" class="table table-striped table-bordered" style="width:100%">
						<thead>
							<tr>
								<th>Código</th>
								<th>Cliente</th>
								<th>Teléfono</th>
								<th>Equipo</th>
								<th>Estado</th>
								<th>Prioridad</th>
								<th>Fecha</th>
								<th>Técnico</th>
								<th>Acciones</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Scripts -->
<script>
$(document).ready(function() {
    var tabla = $('#servicios_table').DataTable({
        "ajax": {
            "url": "<?= base_url('servicios/getServicios') ?>",
            "type": "post",
            "data": function(d) {
                d.estado = $('#estadoFilter').val();
                d.tecnico = $('#tecnicoFilter').val();
            },
            "dataSrc": ""
        },
        "columns": [
            { "data": "codigo" },
            { "data": "cliente_nombre" },
            { "data": "cliente_telefono" },
            { "data": "equipo_descripcion" },
            { 
                "data": "estado",
                "render": function(data, type, row) {
                    var color = '';
                    switch(data) {
                        case 'RECIBIDO': color = 'info'; break;
                        case 'EN DIAGNOSTICO': color = 'warning'; break;
                        case 'EN REPARACION': color = 'primary'; break;
                        case 'ESPERA REPUESTOS': color = 'warning'; break;
                        case 'REPARADO': color = 'success'; break;
                        case 'ENTREGADO': color = 'success'; break;
                        case 'CANCELADO': color = 'danger'; break;
                    }
                    return '<span class="label label-' + color + '">' + data + '</span>';
                }
            },
            { 
                "data": "prioridad",
                "render": function(data, type, row) {
                    var color = '';
                    switch(data) {
                        case 'BAJA': color = 'default'; break;
                        case 'NORMAL': color = 'info'; break;
                        case 'ALTA': color = 'warning'; break;
                        case 'URGENTE': color = 'danger'; break;
                    }
                    return '<span class="label label-' + color + '">' + data + '</span>';
                }
            },
            { "data": "fecha_recepcion" },
            { "data": "tecnico_nombre" },
            { 
                "data": "acciones",
                "orderable": false,
                "searchable": false
            }
        ],
        "order": [[ 0, "desc" ]],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json"
        },
        "dom": 'Bfrtip',
        "buttons": [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
    
    // Filter functions
    window.applyFilters = function() {
        tabla.ajax.reload();
    };
    
    window.clearFilters = function() {
        $('#estadoFilter').val('0');
        $('#tecnicoFilter').val('0');
        tabla.ajax.reload();
    };
    
    // Auto-reload when filters change
    $('#estadoFilter, #tecnicoFilter').change(function() {
        tabla.ajax.reload();
    });
});

function editar(id) {
    window.location.href = '<?= base_url('servicios/add') ?>/' + id;
}

function anular(id) {
    if(confirm('¿Está seguro de anular este servicio?')) {
        $.post('<?= base_url('servicios/anular') ?>', {id: id}, function(response) {
            if(response.rpta == 'success') {
                alert('Servicio anulado correctamente');
                $('#servicios_table').DataTable().ajax.reload();
            } else {
                alert('Error: ' + response.msg);
            }
        }, 'json');
    }
}

function ver(id) {
    window.location.href = '<?= base_url('servicios/view') ?>/' + id;
}
</script>

<style>
.label {
    font-size: 11px;
    padding: 3px 6px;
}
</style>

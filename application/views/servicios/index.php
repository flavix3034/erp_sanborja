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
				<div class="row">
					<div class="col-sm-3">
						<label>Estado:</label>
						<select id="filtro_estado" class="form-control">
							<option value="0">-- Todos --</option>
							<?php foreach($estados as $key => $value): ?>
								<?php if($key != ''): ?>
								<option value="<?= $key ?>"><?= $value ?></option>
								<?php endif; ?>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="col-sm-3">
						<label>T&eacute;cnico:</label>
						<select id="filtro_tecnico" class="form-control">
							<option value="0">-- Todos --</option>
							<?php foreach($tecnicos as $tec): ?>
								<option value="<?= $tec->id ?>"><?= $tec->nombre ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="col-sm-2">
						<label>&nbsp;</label><br>
						<button type="button" class="btn btn-info" onclick="aplicarFiltros()">
							<i class="glyphicon glyphicon-filter"></i> Aplicar
						</button>
					</div>
					<div class="col-sm-2">
						<label>&nbsp;</label><br>
						<button type="button" class="btn btn-default" onclick="limpiarFiltros()">
							<i class="glyphicon glyphicon-refresh"></i> Limpiar
						</button>
					</div>
					<div class="col-sm-2">
						<label>&nbsp;</label><br>
						<a href="<?= base_url('servicios/add') ?>" class="btn btn-primary">
							<i class="glyphicon glyphicon-plus"></i> Nuevo
						</a>
					</div>
				</div>
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
								<th>C&oacute;digo</th>
								<th>Cliente</th>
								<th>Tel&eacute;fono</th>
								<th>Tipo</th>
								<th>Marca</th>
								<th>Modelo</th>
								<th>Estado</th>
								<th>Prioridad</th>
								<th>Fecha Ingreso</th>
								<th>Fecha Est. Rep.</th>
								<th>Costo Final</th>
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
var tabla;

$(document).ready(function() {
    tabla = $('#servicios_table').DataTable({
        "ajax": {
            "url": "<?= base_url('servicios/getServicios') ?>",
            "type": "post",
            "data": function(d) {
                return {
                    estado: $('#filtro_estado').val(),
                    tecnico: $('#filtro_tecnico').val()
                };
            },
            "dataSrc": "data"
        },
        "columns": [
            { "data": "codigo" },
            { "data": "cliente_nombre" },
            { "data": "cliente_telefono" },
            { "data": "equipo_tipo" },
            { "data": "marca" },
            { "data": "modelo" },
            {
                "data": "estado",
                "render": function(data, type, row) {
                    var cls = '';
                    switch(data) {
                        case 'RECIBIDO':         cls = 'badge-recibido'; break;
                        case 'EN DIAGNOSTICO':   cls = 'badge-diagnostico'; break;
                        case 'EN REPARACION':    cls = 'badge-reparacion'; break;
                        case 'ESPERA REPUESTOS': cls = 'badge-espera'; break;
                        case 'REPARADO':         cls = 'badge-reparado'; break;
                        case 'ENTREGADO':        cls = 'badge-entregado'; break;
                        case 'CANCELADO':        cls = 'badge-cancelado'; break;
                    }
                    return '<span class="estado-badge ' + cls + '">' + data + '</span>';
                }
            },
            {
                "data": "prioridad",
                "render": function(data, type, row) {
                    var icon = '';
                    var cls = '';
                    switch(data) {
                        case 'BAJA':    cls = 'label label-default'; break;
                        case 'NORMAL':  cls = 'label label-info'; break;
                        case 'ALTA':    cls = 'label label-warning'; icon = ' <i class="fa fa-exclamation-triangle text-danger"></i>'; break;
                        case 'URGENTE': cls = 'label label-danger'; icon = ' <i class="fa fa-exclamation-triangle"></i>'; break;
                    }
                    return '<span class="' + cls + '">' + data + '</span>' + icon;
                }
            },
            { "data": "fecha_ingreso" },
            { "data": "fecha_estimada_reparacion" },
            {
                "data": "costo_final",
                "render": function(data, type, row) {
                    var num = parseFloat(data) || 0;
                    return 'S/. ' + num.toFixed(2);
                },
                "className": "text-right"
            },
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
        ],
        "initComplete": function(settings, json) {
            if (json && json.error) {
                console.error('Error en DataTable:', json.message);
            }
        }
    });
});

function aplicarFiltros() {
    tabla.ajax.reload();
}

function limpiarFiltros() {
    $('#filtro_estado').val('0');
    $('#filtro_tecnico').val('0');
    tabla.ajax.reload();
}

function editar(id) {
    window.location.href = '<?= base_url('servicios/add') ?>/' + id;
}

function anular(id) {
    if(confirm('¿Está seguro de anular este servicio?')) {
        $.post('<?= base_url('servicios/anular') ?>', {id: id}, function(response) {
            if(response.rpta == 'success') {
                alert('Servicio anulado correctamente');
                tabla.ajax.reload();
            } else {
                alert('Error: ' + response.msg);
            }
        }, 'json');
    }
}

function ver(id) {
    window.location.href = '<?= base_url('servicios/view') ?>/' + id;
}

function print_etiqueta(id) {
    window.open('<?= base_url('servicios/print_etiqueta') ?>/' + id, 'etiqueta', 'width=500,height=300');
}
</script>

<style>
/* Tabla: nowrap para evitar quiebre de columnas */
#servicios_table th,
#servicios_table td {
    white-space: nowrap;
    vertical-align: middle;
    font-size: 12px;
}

#servicios_table thead th {
    background-color: #f7f7f7;
    font-weight: bold;
    font-size: 12px;
}

/* Badges de estado */
.estado-badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 3px;
    color: #fff;
    font-size: 11px;
    font-weight: bold;
    letter-spacing: 0.3px;
}

.badge-recibido {
    background-color: #5bc0de; /* info - azul claro */
}

.badge-diagnostico {
    background-color: #f0ad4e; /* warning - amarillo */
}

.badge-reparacion {
    background-color: #337ab7; /* primary - azul */
}

.badge-espera {
    background-color: #777777; /* secondary - gris */
}

.badge-reparado {
    background-color: #5cb85c; /* success - verde */
}

.badge-entregado {
    background-color: #333333; /* dark - negro/gris oscuro */
}

.badge-cancelado {
    background-color: #d9534f; /* danger - rojo */
}

/* Prioridad labels */
.label {
    font-size: 11px;
    padding: 3px 6px;
}
</style>

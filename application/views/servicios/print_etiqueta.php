<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Etiqueta <?= $servicio->codigo ?></title>
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
<style>
	@page {
		size: 50mm 25mm;
		margin: 0;
	}

	* { margin: 0; padding: 0; box-sizing: border-box; }

	body {
		font-family: Arial, Helvetica, sans-serif;
		background: #f0f0f0;
		display: flex;
		flex-direction: column;
		align-items: center;
		padding-top: 20px;
	}

	.no-print { margin-bottom: 12px; }
	.no-print button {
		padding: 6px 18px;
		font-size: 13px;
		cursor: pointer;
		border: 1px solid #ccc;
		border-radius: 4px;
		background: #fff;
		margin: 0 4px;
	}
	.no-print button.btn-print {
		background: #337ab7;
		color: #fff;
		border-color: #337ab7;
	}

	.etiqueta {
		width: 50mm;
		height: 25mm;
		background: #fff;
		border: 1px dashed #ccc;
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;
		padding: 1mm 2mm;
		overflow: hidden;
	}

	.etiqueta svg {
		max-width: 46mm;
		height: 10mm;
	}

	.etiqueta .codigo {
		font-size: 9px;
		font-weight: bold;
		letter-spacing: 1px;
		margin-top: 0.5mm;
	}

	.etiqueta .info {
		font-size: 7px;
		text-align: center;
		line-height: 1.3;
		margin-top: 0.5mm;
		max-width: 46mm;
		overflow: hidden;
		white-space: nowrap;
		text-overflow: ellipsis;
	}

	@media print {
		body {
			background: none;
			padding: 0;
			display: block;
		}
		.no-print { display: none !important; }
		.etiqueta {
			border: none;
			width: 50mm;
			height: 25mm;
		}
	}
</style>
</head>
<body>

<div class="no-print">
	<button class="btn-print" onclick="window.print()"><b>&#128424;</b> Imprimir</button>
	<button onclick="window.close()">Cerrar</button>
</div>

<div class="etiqueta">
	<svg id="barcode"></svg>
	<div class="codigo"><?= $servicio->codigo ?></div>
	<div class="info"><?= mb_strtoupper($servicio->cliente_nombre) ?> | <?= $servicio->equipo_tipo ?> <?= $servicio->marca ?></div>
	<div class="info">Ingreso: <?= date('d/m/Y', strtotime($servicio->fecha_recepcion)) ?></div>
</div>

<script>
JsBarcode("#barcode", "<?= $servicio->codigo ?>", {
	format: "CODE128",
	width: 1.5,
	height: 30,
	displayValue: false,
	margin: 0
});
</script>

</body>
</html>

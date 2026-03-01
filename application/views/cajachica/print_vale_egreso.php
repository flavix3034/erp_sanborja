<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Vale de Egreso Interno</title>
<style>
	@page { margin: 0; size: 80mm auto; }
	* { margin: 0; padding: 0; box-sizing: border-box; }
	body {
		font-family: 'Courier New', monospace;
		font-size: 12px;
		width: 80mm;
		margin: 0 auto;
		padding: 4mm;
		color: #000;
	}
	.center { text-align: center; }
	.bold { font-weight: bold; }
	.sep {
		border-top: 1px dashed #000;
		margin: 4px 0;
	}
	.title {
		font-size: 14px;
		font-weight: bold;
		margin: 4px 0;
	}
	.nro {
		font-size: 13px;
		font-weight: bold;
	}
	.row {
		display: flex;
		justify-content: space-between;
		margin: 2px 0;
	}
	.row .label { font-weight: bold; min-width: 80px; }
	.monto-box {
		text-align: center;
		font-size: 16px;
		font-weight: bold;
		margin: 8px 0;
		padding: 6px;
		border: 1px solid #000;
	}
	.firma {
		margin-top: 30px;
		text-align: center;
	}
	.firma-line {
		border-top: 1px solid #000;
		width: 60%;
		margin: 0 auto 2px;
	}
	.firma-label {
		font-size: 11px;
	}
	.no-print { margin-top: 10px; text-align: center; }
	@media print {
		.no-print { display: none; }
		body { padding: 2mm; }
	}
</style>
</head>
<body>

<div class="center">
	<div class="bold" style="font-size:13px;"><?= isset($store->nombre_empresa) ? $store->nombre_empresa : 'EMPRESA' ?></div>
	<div>RUC: <?= isset($store->ruc) ? $store->ruc : '' ?></div>
	<div style="font-size:11px;"><?= isset($store->address1) ? $store->address1 : '' ?></div>
</div>

<div class="sep"></div>

<div class="center">
	<div class="title">VALE DE EGRESO INTERNO</div>
	<div class="nro">Nro: VE-<?= $periodo->id ?>-<?= str_pad($gasto->numero_vale_egreso, 4, '0', STR_PAD_LEFT) ?></div>
</div>

<div class="sep"></div>

<div class="row">
	<span class="label">Fecha:</span>
	<span><?= date('d/m/Y H:i', strtotime($gasto->fecha_gasto)) ?></span>
</div>
<div class="row">
	<span class="label">Beneficiario:</span>
	<span><?= htmlspecialchars($gasto->beneficiario) ?></span>
</div>
<div class="row">
	<span class="label">Categoria:</span>
	<span><?= isset($categoria->nombre) ? htmlspecialchars($categoria->nombre) : '' ?></span>
</div>
<div class="row">
	<span class="label">Descripcion:</span>
</div>
<div style="margin-left:4px;font-size:11px;"><?= htmlspecialchars($gasto->descripcion) ?></div>

<div class="monto-box">
	MONTO: S/. <?= number_format($gasto->monto, 2) ?>
</div>

<div class="sep"></div>

<div style="font-size:10px;margin:4px 0;">
	Este vale reemplaza al comprobante de pago para fines de control interno de Caja Chica.
</div>

<div class="sep"></div>

<div class="firma">
	<div class="firma-line"></div>
	<div class="firma-label">Autorizado por</div>
	<div class="firma-label"><?= isset($usuario->username) ? $usuario->username : '' ?></div>
	<div class="firma-label">Responsable de Caja</div>
</div>

<div class="no-print">
	<button onclick="window.print();" style="padding:8px 20px;font-size:14px;cursor:pointer;">Imprimir</button>
	<button onclick="window.close();" style="padding:8px 20px;font-size:14px;cursor:pointer;margin-left:5px;">Cerrar</button>
</div>

<script>
window.onload = function() {
	setTimeout(function(){ window.print(); }, 300);
};
</script>
</body>
</html>

<?php
	$this->db->select("*");
	$query = $this->db->get('tec_users');
	foreach($query->result() as $r){
	}
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>JFK SYSTEM - Iniciar Sesi&oacute;n</title>
	<script src="<?= base_url("assets/plugins/jQuery/jQuery-2.1.4.min.js") ?>"></script>
	<script src="<?= base_url("assets/bootstrap/js/bootstrap.js") ?>"></script>
	<link rel="stylesheet" type="text/css" href="<?= base_url("assets/bootstrap/css/bootstrap.css") ?>">
	<style>
		* { margin: 0; padding: 0; box-sizing: border-box; }
		html, body { height: 100%; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }

		.login-container {
			display: flex;
			height: 100vh;
			width: 100%;
		}

		/* --- Lado izquierdo: formulario --- */
		.login-left {
			flex: 1;
			display: flex;
			flex-direction: column;
			justify-content: center;
			align-items: center;
			padding: 40px;
			background: #fff;
		}

		.login-form-wrapper {
			width: 100%;
			max-width: 420px;
		}

		.login-logo-wrapper {
			width: 90px;
			height: 90px;
			margin-bottom: 30px;
			background: linear-gradient(135deg, #1a1a2e 0%, #2c5364 100%);
			border-radius: 18px;
			display: flex;
			align-items: center;
			justify-content: center;
			box-shadow: 0 4px 12px rgba(26, 26, 46, 0.25);
		}

		.login-logo-wrapper img {
			height: 58px;
			object-fit: contain;
		}

		.login-form-wrapper h1 {
			font-size: 28px;
			font-weight: 700;
			color: #1a1a2e;
			margin-bottom: 6px;
		}

		.login-form-wrapper .subtitle {
			color: #888;
			font-size: 14px;
			margin-bottom: 30px;
		}

		.form-group-custom {
			margin-bottom: 20px;
		}

		.form-group-custom label {
			display: block;
			font-size: 13px;
			font-weight: 600;
			color: #333;
			margin-bottom: 6px;
		}

		.input-wrapper {
			position: relative;
		}

		.input-wrapper input {
			width: 100%;
			padding: 12px 40px 12px 14px;
			border: 1px solid #ddd;
			border-radius: 10px;
			font-size: 14px;
			outline: none;
			transition: border-color 0.2s;
			background: #fafafa;
		}

		.input-wrapper input:focus {
			border-color: #4CAF50;
			background: #fff;
		}

		.input-wrapper input::placeholder {
			color: #bbb;
		}

		.toggle-password {
			position: absolute;
			right: 12px;
			top: 50%;
			transform: translateY(-50%);
			cursor: pointer;
			color: #aaa;
			font-size: 16px;
			background: none;
			border: none;
			padding: 0;
			line-height: 1;
		}

		.toggle-password:hover { color: #666; }

		.checkbox-wrapper {
			display: flex;
			align-items: center;
			margin-bottom: 24px;
			gap: 8px;
		}

		.checkbox-wrapper input[type="checkbox"] {
			width: 18px;
			height: 18px;
			accent-color: #4CAF50;
			cursor: pointer;
		}

		.checkbox-wrapper label {
			font-size: 13px;
			color: #555;
			cursor: pointer;
			margin: 0;
		}

		.btn-login {
			width: 100%;
			padding: 13px;
			background: #4CAF50;
			color: #fff;
			border: none;
			border-radius: 10px;
			font-size: 15px;
			font-weight: 600;
			cursor: pointer;
			transition: background 0.2s;
			letter-spacing: 0.3px;
		}

		.btn-login:hover { background: #43a047; }
		.btn-login:active { background: #388e3c; }

		.forgot-link {
			display: block;
			text-align: center;
			margin-top: 18px;
			color: #888;
			font-size: 13px;
			text-decoration: none;
		}

		.forgot-link:hover { color: #555; text-decoration: underline; }

		.error-message {
			margin-top: 16px;
			text-align: center;
			color: #e53935;
			font-size: 13px;
		}

		/* --- Lado derecho: panel de avisos --- */
		.login-right {
			flex: 1;
			display: flex;
			align-items: center;
			justify-content: center;
			padding: 40px;
			background: linear-gradient(135deg, #0f2027 0%, #203a43 40%, #2c5364 100%);
			position: relative;
			overflow: hidden;
		}

		/* Esferas decorativas con CSS */
		.login-right::before {
			content: '';
			position: absolute;
			width: 300px;
			height: 300px;
			background: radial-gradient(circle, rgba(212,175,55,0.3) 0%, transparent 70%);
			border-radius: 50%;
			top: -50px;
			right: -50px;
		}

		.login-right::after {
			content: '';
			position: absolute;
			width: 200px;
			height: 200px;
			background: radial-gradient(circle, rgba(100,180,220,0.25) 0%, transparent 70%);
			border-radius: 50%;
			bottom: 60px;
			left: -30px;
		}

		.avisos-card {
			background: rgba(255, 255, 255, 0.08);
			backdrop-filter: blur(10px);
			-webkit-backdrop-filter: blur(10px);
			border: 1px solid rgba(255, 255, 255, 0.12);
			border-radius: 16px;
			padding: 40px 36px;
			max-width: 440px;
			width: 100%;
			text-align: center;
			position: relative;
			z-index: 1;
		}

		.avisos-card .system-name {
			font-size: 14px;
			letter-spacing: 4px;
			color: rgba(255, 255, 255, 0.9);
			text-transform: uppercase;
			margin-bottom: 8px;
			font-weight: 600;
		}

		.avisos-card .divider {
			width: 50px;
			height: 2px;
			background: rgba(255, 255, 255, 0.4);
			margin: 12px auto 24px;
		}

		.avisos-card .avisos-title {
			font-size: 26px;
			font-weight: 700;
			color: #fff;
			line-height: 1.3;
			text-transform: uppercase;
			letter-spacing: 1px;
		}

		.avisos-card .avisos-text {
			margin-top: 20px;
			font-size: 14px;
			color: rgba(255, 255, 255, 0.7);
			line-height: 1.6;
		}

		/* Responsive */
		@media (max-width: 768px) {
			.login-container { flex-direction: column; }
			.login-right { display: none; }
			.login-left { padding: 30px 20px; }
		}
	</style>
</head>
<body>

<div class="login-container">

	<!-- Lado izquierdo: Formulario -->
	<div class="login-left">
		<div class="login-form-wrapper">

			<div class="login-logo-wrapper">
				<img src="<?= base_url("imagenes/logosbl.svg") ?>" alt="Logo">
			</div>

			<h1>Iniciar sesi&oacute;n</h1>
			<p class="subtitle">&iexcl;Bienvenido! Por favor ingresa tus datos</p>

			<form id="form_login" name="form_login" method="post" action="<?= base_url("welcome/inicia_sesion") ?>">

				<div class="form-group-custom">
					<label for="usuario">Usuario</label>
					<div class="input-wrapper">
						<input type="text" name="usuario" id="usuario" placeholder="Ingresa tu usuario" value="" autocomplete="username">
					</div>
				</div>

				<div class="form-group-custom">
					<label for="pass">Contrase&ntilde;a</label>
					<div class="input-wrapper">
						<input type="password" name="pass" id="pass" placeholder="Ingresa tu contrase&ntilde;a" value="" autocomplete="current-password">
						<button type="button" class="toggle-password" id="togglePass" title="Mostrar/ocultar contrase&ntilde;a">&#128065;</button>
					</div>
				</div>

				<div class="checkbox-wrapper">
					<input type="checkbox" id="showPass">
					<label for="showPass">Mostrar contrase&ntilde;a</label>
				</div>

				<button type="submit" class="btn-login">Iniciar sesi&oacute;n</button>

			</form>

			<a href="#" class="forgot-link" onclick="return false;">&iquest;Olvidaste tu contrase&ntilde;a?</a>

			<div class="error-message">
				<?php
				if(isset($message)){
					if(strlen($message)>0){
						echo $message;
					}
				}
				?>
			</div>

		</div>
	</div>

	<!-- Lado derecho: Panel de avisos -->
	<div class="login-right">
		<div class="avisos-card">
			<div class="system-name">JFK System</div>
			<div class="divider"></div>
			<div class="avisos-title">Espacio para avisos de noticias sobre el sistema</div>
			<p class="avisos-text">Aqu&iacute; se mostrar&aacute;n comunicados importantes, actualizaciones y novedades del sistema.</p>
		</div>
	</div>

</div>

<script>
	// Toggle password con el ícono de ojo
	$('#togglePass').on('click', function() {
		var input = $('#pass');
		var isPassword = input.attr('type') === 'password';
		input.attr('type', isPassword ? 'text' : 'password');
		$('#showPass').prop('checked', isPassword);
	});

	// Toggle password con el checkbox
	$('#showPass').on('change', function() {
		$('#pass').attr('type', this.checked ? 'text' : 'password');
	});
</script>

</body>
</html>

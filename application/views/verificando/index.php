    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>

	<div class="row" style="margin-left:15px">

		<div class="col-sm-3">
			Id:<input type="text" name="txt_id" id="txt_id" class="form-control">
		</div>

		<div class="col-sm-3">
			<br>
			<button type="button" class="form-control" onclick="analizar()">Analizar</button>
		</div>

		<div class="col-sm-3">
			<br>
			<button type="button" class="form-control" onclick="ver_rpta_sunat()">ver Rpta Sunat</button>
		</div>

		
	</div>

	<div class="row">
		<div class="col-sm-12" id="rpta">
		</div>
	</div>

	<div class="row">
		<div class="col-sm-12" id="rpta2">
			
		</div>
	</div>

<script type="text/javascript">
	function analizar(){
		$.ajax({
			data 	: { id : document.getElementById('txt_id').value},
			url 	: '<?= base_url("verificando/analizar") ?>',
			type 	: 'get',
			success : function(res){
				document.getElementById("rpta").innerHTML = res

				setTimeout("document.getElementById('btn_1').click()",1)
				setTimeout("document.getElementById('btn_2').click()",1400)
				setTimeout("document.getElementById('btn_3').click()",2800)
				setTimeout("document.getElementById('btn_4').click()",4200)
			}
		})
	}

	function ver_rpta_sunat(){
		$.ajax({
			data 	: { id : document.getElementById('txt_id').value},
			url 	: '<?= base_url("verificando/ver_rpta_sunat") ?>',
			type 	: 'get',
			success : function(res){
				document.getElementById("rpta2").innerHTML = res
			}
		})
	}

	function ejecutar(cad){
		$.ajax({
			data: 		{ dato1 : criptar(cad)},
			url: 		'<?= base_url("verificando/ejecuta_consulta") ?>',
			type: 		'get',
			success: function(res){
				console.log(res)
			}
		})
	}

	function criptar(cQuery){ // Ver 1.0
		var cDato = ""
		cDato = cQuery.replace(/select/g,"wMundano")
		cDato = cDato.replace('*',"wrisco")
		cDato = cDato.replace('*',"wrisco")
		cDato = cDato.replace('*',"wrisco")
		cDato = cDato.replace(/from/g,"wdesde")
		cDato = cDato.replace(/inner/g,"_ier__")
		cDato = cDato.replace(/left/g,"_lef__")
		cDato = cDato.replace(/where/g,"_donde__")
		cDato = cDato.replace(/-/g,"_-__")
		cDato = cDato.replace(/%/g,"Mundiali")
		cDato = cDato.replace(/like/g,"megusta_")
		cDato = cDato.replace(/group/g,"j_eb_epu")

		return cDato
	}

    function envio_sunat(){
        var cId = document.getElementById("txt_id").value
        document.getElementById("popper").innerHTML = "<a id=\"link_bip\" href=\"<?=base_url("sales/enviar_individual/") ?>" + cId + "\" target=\"_blank\">Bip "+cId+"</a>"
        document.getElementById("link_bip").click()
    }

</script>
<?php
	// id, username, email, active, first_name, last_name, group_id, store_id,
	$id = "";
	$username 	= set_value("username");
	$email 		= set_value("email");
	$active 	= set_value("active");
	$first_name = set_value("first_name");
	$last_name 	= set_value("last_name");
	$group_id 	= set_value("group_id");
	$store_id 	= set_value("store_id");
	$password 	= set_value("password");
	$active 	= set_value("active");
	
	if(isset($query_p1)){
		foreach($query_p1->result() as $r){
			$id 			= $r->id; 
			$username 		= $r->username;
			$email 			= $r->email;
			$active 		= $r->active;
			$first_name 	= $r->first_name;
			$last_name 		= $r->last_name;
			$group_id 		= $r->group_id;
			$store_id 		= $r->store_id;
			$password 		= $r->password;
			$active 		= $r->active;
			//die("8 misiles...".$active);
		}
	}
?>
<style type="text/css">
	.ventas{
		border-style:none; border-width: 1px; border-color:rgb(170,170,170);
	}
	.filitas{
		margin-top: 15px;
	}	
</style>

<?= form_open(base_url("usuarios/save"), 'method="post" name="form1" id="form1"'); ?>

	<div class="row filitas">
	
		<div class="col-sm-5 col-md-4 col-lg-3 ventas">
			<label>User:</label>
			<?= form_input('username', $username, 'class="form-control" id="username" required'); ?>
			<input type="hidden" name="id" value="<?= $id ?>">
			<input type="hidden" name="active" value="<?= $active ?>">
		</div>

		<div class="col-sm-5 col-md-4 col-lg-3 ventas" style="margin-left:10px;">
			<label>Nombre:</label>
			<?= form_input('first_name', $first_name, 'class="form-control tip" id="first_name" required'); ?>
		</div>

		<div class="col-sm-5 col-md-4 col-lg-3 ventas" style="margin-left:10px;">
			<label>Apellidos:</label>
			<?= form_input('last_name', $last_name, 'class="form-control tip" id="last_name" required'); ?>
		</div>

	</div>

	<div class="row filitas">
	
		<div class="col-sm-5 col-lg-3 ventas">
			<label>Correo</label>
			<?= form_input('email', $email, 'class="form-control" id="email"'); ?>
		</div>

		<div class="col-sm-4 col-lg-3 ventas">
			<label>Grupo:</label>
			<?php
				$result = $this->db->select("id, grupo")->from("tec_grupo_usuarios")->where("activo","1")->get()->result_array();
				$ar = $this->fm->conver_dropdown($result, "id", "grupo"); 
				echo form_dropdown('group_id', $ar, $group_id,'class="form-control tip" id="group_id" required="required"');
			?>
		</div>

		<div class="col-sm-5 col-md-4 col-lg-3 ventas">
			<label>Tienda:</label>
			<?php
				$result = $this->db->select("id, name")->from("tec_stores")->get()->result_array();
				$ar = $this->fm->conver_dropdown($result, "id", "name"); 
				echo form_dropdown('store_id', $ar, $store_id,'class="form-control tip" id="store_id" required="required"');
			?>
		</div>
		
	</div>

	<div class="row filitas">
	
		<div class="col-sm-5 col-lg-3 ventas">
			<label>Password</label>
			<?= form_input('password', $password, 'class="form-control" id="password"'); ?>
		</div>

		<div class="col-sm-5 col-lg-3 ventas">
			<label>Estado</label>
			<?php
				$ar = array("1"=>"Activo", "0"=>"Inactivo");
				echo form_dropdown('active', $ar, $active,'class="form-control tip" id="active" required="required"');
			?>
		</div>

	</div>

	<div class="row filitas">
		<div class="col-xs-5 col-sm-4 col-md-3 col-lg-2 ventas">
			<br>
			<button type="button" class="btn btn-primary" onclick="validar()">Guardar</button>
		</div>
		<div class="col-xs-5 col-sm-7 col-md-8 col-lg-9 ventas">
		</div>
		<div class="col-xs-2 col-sm-1 col-md-1 col-lg-1 ventas">
			<br>
			<button type="submit" name="submit1" id="submit1">.</button>
		</div>
	</div>

<?= form_close() ?>

<script type="text/javascript">
	ar_items = new Array()

	function empty(data){
	    if(typeof(data) == 'number' || typeof(data) == 'boolean')
	    { 
	      return false; 
	    }
	    if(typeof(data) == 'undefined' || data === null)
	    {
	      return true; 
	    }
	    if(typeof(data.length) != 'undefined')
	    {
	      return data.length == 0;
	    }
	    var count = 0;
	    for(var i in data)
	    {
	      if(data.hasOwnProperty(i))
	      {
	        count ++;
	      }
	    }
	    return count == 0;
	}

	function validar(){
		//document.form1.submit()
		document.getElementById("submit1").click()
	}

	function mensaje(cad){
		alert(cad)
	}
	
	function llenar(){
	}
</script>
<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
    if($modo == 'insert'){
        $id         = ""; 
        $code       = "";
        $name       = "";
        $category_id = "";
        $unidad     = "";
        $alert_cantidad = "";
        $price      = "";
        $imagen     = "";
        $marca      = "";
        $precio_x_mayor = "";
        $prod_serv  = "";
    }else{
        $code       = $row->code;
        $name       = $row->name;
        $category_id = $row->category_id;
        $unidad     = $row->unidad;
        $alert_cantidad = $row->alert_cantidad;
        $price      = $row->price;
        $imagen     = $row->imagen;
        $marca      = $row->marca;
        $precio_x_mayor = $row->precio_x_mayor;
        $prod_serv  = $row->prod_serv;
    }
?>
<style type="text/css">
    .ventas{
        border-style:none; border-width: 1px; border-color:rgb(170,170,170);
    }
    .filitas{
        margin-top: 15px;
    }
    .mayusculas {
        text-transform: uppercase;
    }   
</style>

    <div class="row filitas">
        <div class="col-sm-12 ventas">
            <a href="<?= base_url() ?>products/importacion"><span style="font-weight:bold;color:blue">Importar Productos....</span></a><hr>
        </div>
    </div>


<?= form_open_multipart(base_url("products/save"), 'method="post" name="form1" id="form1"'); ?>

    <div class="row filitas"> 
    
        <div class="col-sm-4 col-lg-1 ventas">
            <label>Id:</label>
            <?= form_input('id', $id, 'class="form-control tip" id="code" readonly'); ?>
        </div>
        
        <div class="col-sm-4 col-lg-2 ventas">
            <label>Code:</label>
            <?= form_input('code', $code, 'class="form-control tip mayusculas" id="code"'); ?>
        </div>

        <div class="col-sm-4 col-lg-3 ventas" style="margin-left:10px;">
            <label>Descripcion Comercial:</label>
            <?= form_input('name', $name, 'class="form-control tip mayusculas" id="name" required"'); ?>
        </div>

        <div class="col-sm-4 col-lg-2 ventas">
            <label>Marca (Opcional):</label>
            <?= form_input('marca', $marca, 'class="form-control tip mayusculas" id="marca"'); ?>
        </div>


    </div>
    <div class="row filitas"> 

        <div class="col-sm-4 col-lg-3 ventas">
            <label>Categoria:</label>
            <?php 
                $result = $this->db->query("select id,name from tec_categories where activo='1' order by name")->result_array();
                $ar     = $this->fm->conver_dropdown($result,"id","name");
                echo form_dropdown('category_id',$ar,$category_id,'class="form-control tip" id="category_id" required="required"');
            ?>

        </div>

        <!-- unidad, alert_cantidad, price, imagen -->
        <div class="col-sm-4 col-lg-2 ventas">
            <label>Unidad:</label>
            <?php 
                $result = $this->db->query("select id, descrip from tec_unidades order by id")->result_array();
                $ar     = $this->fm->conver_dropdown($result,"id","descrip");
                echo form_dropdown('unidad',$ar,$unidad,'class="form-control tip" id="unidad" required="required"');
            ?>
        </div>

        <div class="col-sm-4 col-lg-2 ventas">
            <label>Alerta cantidad:</label>
            <?= form_input('alert_cantidad', $alert_cantidad, 'class="form-control tip" id="alert_cantidad" required'); ?>
        </div>

        <div class="col-sm-4 col-lg-2 ventas">
            <label>Precio:</label>
            <?= form_input('price', $price, 'class="form-control tip" id="price" required'); ?>
        </div>

        <div class="col-sm-4 col-lg-2 ventas">
            <label>Precio x Mayor:</label>
            <?= form_input('precio_x_mayor', $precio_x_mayor, 'class="form-control tip" id="precio_x_mayor" required'); ?>
        </div>

    </div>
    
    <div class="row filitas">
        <!--<div class="col-sm-4 col-lg-2 ventas">
            <label>Producto:</label>
            <?php 
                $ar     = array(""=>"--ELIGIR--","P"=>"PRODUCTO","S"=>"SERVICIO");
                echo form_dropdown('prod_serv', $ar, $prod_serv, 'class="form-control tip" id="prod_serv" required="required"');
            ?>
        </div>-->

        <div class="col-sm-5 col-lg-4 ventas">
            <label>imagen:</label>
            <input type="text" name='imagen' value="<?= $imagen ?>" class="form-control tip" id="imagen" readonly>
        </div>
        <div class="col-sm-7 col-lg-5 ventas">
            <label>Subir</label>
            <input type="file" name='archivo' class="form-control tip" id="archivo">
            <input type="hidden" name="modo" id="modo" value="<?= $modo ?>">
        </div>

    </div>

    <!-- ============ SECCION DE VARIANTES ============ -->
    <div class="row filitas">
        <div class="col-sm-12 ventas">
            <div style="border:1px solid #ddd;border-radius:6px;padding:12px 15px;background:#fafbfc;">
                <label style="cursor:pointer;">
                    <input type="checkbox" id="chk_variantes" onchange="toggleVariantes()" <?= (isset($tiene_variantes) && $tiene_variantes) ? 'checked' : '' ?>>
                    <strong style="font-size:14px;"> Este producto tiene variantes</strong>
                    <span style="color:#888;font-size:12px;">(Color, Capacidad, Calidad, etc.)</span>
                </label>

                <div id="panel_variantes" style="display:none;margin-top:12px;">
                    <!-- Selectores de atributos (hasta 3) -->
                    <div class="row">
                        <div class="col-sm-4">
                            <div style="border:1px solid #e0e0e0;border-radius:6px;padding:10px;background:#fff;">
                                <label style="font-weight:700;font-size:12px;color:#4e73df;">
                                    <i class="fa fa-tag"></i> Atributo 1
                                </label>
                                <select id="sel_attr_1" class="form-control input-sm" onchange="cargarValoresSlot(1)" style="margin-bottom:8px;">
                                    <option value="">-- No usar --</option>
                                </select>
                                <div id="valores_slot_1" style="display:flex;flex-wrap:wrap;gap:4px;"></div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div style="border:1px solid #e0e0e0;border-radius:6px;padding:10px;background:#fff;">
                                <label style="font-weight:700;font-size:12px;color:#6c757d;">
                                    <i class="fa fa-tag"></i> Atributo 2 <span style="font-weight:400;color:#aaa;">(opcional)</span>
                                </label>
                                <select id="sel_attr_2" class="form-control input-sm" onchange="cargarValoresSlot(2)" style="margin-bottom:8px;">
                                    <option value="">-- No usar --</option>
                                </select>
                                <div id="valores_slot_2" style="display:flex;flex-wrap:wrap;gap:4px;"></div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div style="border:1px solid #e0e0e0;border-radius:6px;padding:10px;background:#fff;">
                                <label style="font-weight:700;font-size:12px;color:#6c757d;">
                                    <i class="fa fa-tag"></i> Atributo 3 <span style="font-weight:400;color:#aaa;">(opcional)</span>
                                </label>
                                <select id="sel_attr_3" class="form-control input-sm" onchange="cargarValoresSlot(3)" style="margin-bottom:8px;">
                                    <option value="">-- No usar --</option>
                                </select>
                                <div id="valores_slot_3" style="display:flex;flex-wrap:wrap;gap:4px;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-top:8px;">
                        <div class="col-sm-12 text-right">
                            <button type="button" class="btn btn-success" onclick="generarCombinaciones()">
                                <i class="fa fa-cogs"></i> Generar Combinaciones
                            </button>
                        </div>
                    </div>

                    <!-- Resumen de combinaciones -->
                    <div id="resumen_combinaciones" style="margin-top:8px;"></div>

                    <!-- Tabla de variantes generadas -->
                    <div style="margin-top:12px;overflow-x:auto;">
                        <table class="table table-bordered table-condensed" style="font-size:12px;display:none;" id="tabla_variantes">
                            <thead style="background:#e9ecef;">
                                <tr>
                                    <th style="width:25%">Combinacion</th>
                                    <th style="width:15%">SKU</th>
                                    <th style="width:15%">Cod. Barras</th>
                                    <th style="width:12%">Precio</th>
                                    <th style="width:12%">Precio Mayor</th>
                                    <th style="width:8%">Activo</th>
                                    <th style="width:5%"></th>
                                </tr>
                            </thead>
                            <tbody id="tbody_variantes"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ============ FIN SECCION DE VARIANTES ============ -->

    <div class="row filitas">
        <div class="col-sm-4 col-lg-2 ventas">
            <br>
            <button type="button" class="btn btn-primary btn-lg" onclick="validar()">Guardar</button>
        </div>
    </div>

    <!-- Campos hidden para variantes -->
    <input type="hidden" name="tiene_variantes" id="tiene_variantes" value="0">
    <input type="hidden" name="variantes_json" id="variantes_json" value="">

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
        var name        = document.getElementById("name").value
        var category_id = document.getElementById("category_id").value
        var unidad      = document.getElementById("unidad").value
        var alert_cantidad = document.getElementById("alert_cantidad").value
        if (name.length == 0){
            alert("Debe ingresar correctamente el nombre")
            return false
        }
        if(category_id.length == 0){
            alert("Debe ingresar correctamente la Categoria")
            return false
        }
        if(unidad.length == 0){
            alert("Debe ingresar correctamente la Unidad")
            return false
        }
        if(alert_cantidad.length == 0){
            alert("Debe ingresar correctamente alerta de cantidad")
            return false
        }

        /*var archivo = document.getElementById("archivo").value
        if(archivo.length > 0){
            var nPos = archivo.indexOf("\\",4)
            document.getElementById("imagen").value = archivo.substr(nPos+1,40)
        }*/

        document.form1.submit()
    }

    function mensaje(cad){
        alert(cad)
    }
    
    function llenar(){
    }

    // ========================
    // VARIANTES (hasta 2 atributos)
    // ========================
    var atributosData = [];

    function toggleVariantes() {
        var chk = document.getElementById('chk_variantes');
        var panel = document.getElementById('panel_variantes');
        var codeField = document.querySelectorAll('input[name="code"]')[0];
        document.getElementById('tiene_variantes').value = chk.checked ? '1' : '0';
        panel.style.display = chk.checked ? 'block' : 'none';
        if (chk.checked) {
            codeField.readOnly = true;
            codeField.value = '';
            codeField.style.backgroundColor = '#eee';
            if (atributosData.length === 0) {
                cargarAtributos();
            }
        } else {
            codeField.readOnly = false;
            codeField.style.backgroundColor = '';
        }
    }

    function cargarAtributos() {
        $.getJSON('<?= base_url("atributos/get_atributos_json") ?>', function(data) {
            atributosData = data;
            var opts = '<option value="">-- No usar --</option>';
            for (var i = 0; i < data.length; i++) {
                opts += '<option value="' + data[i].id + '">' + data[i].nombre + '</option>';
            }
            document.getElementById('sel_attr_1').innerHTML = opts;
            document.getElementById('sel_attr_2').innerHTML = opts;
            document.getElementById('sel_attr_3').innerHTML = opts;
        });
    }

    function cargarValoresSlot(slot) {
        var sel = document.getElementById('sel_attr_' + slot);
        var container = document.getElementById('valores_slot_' + slot);
        var attrId = sel.value;

        if (!attrId) { container.innerHTML = ''; return; }

        // Evitar que multiples slots tengan el mismo atributo
        var allSlots = [1, 2, 3];
        for (var s = 0; s < allSlots.length; s++) {
            if (allSlots[s] === slot) continue;
            var otroVal = document.getElementById('sel_attr_' + allSlots[s]).value;
            if (attrId === otroVal) {
                alert('Este atributo ya esta seleccionado en otro slot.');
                sel.value = '';
                container.innerHTML = '';
                return;
            }
        }

        var attr = null;
        for (var i = 0; i < atributosData.length; i++) {
            if (atributosData[i].id == attrId) { attr = atributosData[i]; break; }
        }
        if (!attr) return;

        var html = '';
        for (var j = 0; j < attr.valores.length; j++) {
            var v = attr.valores[j];
            html += '<label style="margin:0;font-weight:normal;cursor:pointer;background:#f0f0f0;padding:3px 10px;border-radius:15px;font-size:12px;border:1px solid #ddd;">'
                + '<input type="checkbox" class="chk_slot_' + slot + '" data-attr-id="' + attrId + '" '
                + 'data-attr-nombre="' + attr.nombre + '" data-val-id="' + v.id + '" data-val-nombre="' + v.valor + '"> '
                + v.valor + '</label>';
        }
        container.innerHTML = html;
    }

    function generarCombinaciones() {
        // Recoger seleccion de los 3 slots
        var slots = [1, 2, 3];
        var arrays = [];

        for (var s = 0; s < slots.length; s++) {
            var checks = document.querySelectorAll('.chk_slot_' + slots[s] + ':checked');
            if (checks.length === 0) continue;

            var items = [];
            for (var i = 0; i < checks.length; i++) {
                var c = checks[i];
                items.push({
                    atributo_id: c.getAttribute('data-attr-id'),
                    atributo_nombre: c.getAttribute('data-attr-nombre'),
                    valor_id: c.getAttribute('data-val-id'),
                    valor: c.getAttribute('data-val-nombre')
                });
            }
            arrays.push(items);
        }

        if (arrays.length === 0) {
            alert('Debe seleccionar al menos un valor de un atributo.');
            return;
        }

        var combinaciones = cartesian(arrays);

        // Recopilar variantes existentes del tbody (las que tienen var_id)
        var tbody = document.getElementById('tbody_variantes');
        var filasExistentes = {};
        var rows = tbody.querySelectorAll('tr');
        for (var r = 0; r < rows.length; r++) {
            var hiddenId = rows[r].querySelector('input[name="var_id[]"]');
            var hiddenAttrs = rows[r].querySelector('input[name="var_attrs[]"]');
            if (hiddenId && hiddenAttrs) {
                var attrs = JSON.parse(hiddenAttrs.value);
                var key = attrs.map(function(a) { return a.atributo_id + ':' + a.valor_id; }).sort().join('|');
                filasExistentes[key] = rows[r].outerHTML;
            }
        }

        // Generar filas: preservar existentes, agregar solo las nuevas
        var codePadre = document.getElementById('code') ? document.getElementById('code').value : '';
        tbody.innerHTML = '';
        var totalFilas = 0;

        for (var i = 0; i < combinaciones.length; i++) {
            var combo = combinaciones[i];
            var attrKey = combo.map(function(c) { return c.atributo_id + ':' + c.valor_id; }).sort().join('|');

            if (filasExistentes[attrKey]) {
                // Variante ya existe: preservar fila original con su var_id, sku, barcode, precios, stock
                tbody.innerHTML += filasExistentes[attrKey];
            } else {
                // Variante nueva: crear fila sin var_id
                var comboLabel = combo.map(function(c) { return c.valor; }).join(' / ');
                var skuSuffix = combo.map(function(c) { return c.valor.replace(/\s+/g, '').substring(0, 6).toUpperCase(); }).join('-');
                var sku = codePadre ? codePadre + '-' + skuSuffix : skuSuffix;
                var attrJson = JSON.stringify(combo.map(function(c) { return { atributo_id: c.atributo_id, valor_id: c.valor_id }; }));

                tbody.innerHTML += '<tr>'
                    + '<td>' + comboLabel + '<input type="hidden" name="var_attrs[]" value=\'' + attrJson + '\'></td>'
                    + '<td><input type="text" name="var_sku[]" value="' + sku + '" class="form-control input-sm" style="font-size:11px;"></td>'
                    + '<td><input type="text" name="var_barcode[]" value="" class="form-control input-sm" style="font-size:11px;" placeholder="Opcional"></td>'
                    + '<td><input type="text" name="var_price[]" value="" class="form-control input-sm" style="font-size:11px;" placeholder="Heredar"></td>'
                    + '<td><input type="text" name="var_pmayor[]" value="" class="form-control input-sm" style="font-size:11px;" placeholder="Heredar"></td>'
                    + '<td class="text-center"><input type="checkbox" name="var_activo[]" value="' + i + '" checked></td>'
                    + '<td class="text-center"><a href="#" onclick="$(this).closest(\'tr\').remove();return false;" style="color:#dc3545;"><i class="fa fa-times"></i></a></td>'
                    + '</tr>';
            }
            totalFilas++;
        }

        // Resumen: mostrar cuántas existentes y cuántas nuevas
        var nExistentes = Object.keys(filasExistentes).length;
        var nNuevas = totalFilas - Object.keys(filasExistentes).filter(function(k) {
            // contar solo las existentes que siguen en las combinaciones
            var found = false;
            for (var j = 0; j < combinaciones.length; j++) {
                var ck = combinaciones[j].map(function(c) { return c.atributo_id + ':' + c.valor_id; }).sort().join('|');
                if (ck === k) { found = true; break; }
            }
            return found;
        }).length;
        var resHtml = '<span style="font-size:12px;color:#495057;"><strong>' + totalFilas + '</strong> variante(s)';
        if (nNuevas > 0 && nNuevas < totalFilas) {
            resHtml += ' — <span style="color:#28a745;">' + nNuevas + ' nueva(s)</span>, <span style="color:#17a2b8;">' + (totalFilas - nNuevas) + ' existente(s) preservada(s)</span>';
        }
        resHtml += '</span>';
        document.getElementById('resumen_combinaciones').innerHTML = resHtml;

        document.getElementById('tabla_variantes').style.display = totalFilas > 0 ? 'table' : 'none';
    }

    function cartesian(arrays) {
        if (arrays.length === 0) return [[]];
        var result = [[]];
        for (var i = 0; i < arrays.length; i++) {
            var temp = [];
            for (var j = 0; j < result.length; j++) {
                for (var k = 0; k < arrays[i].length; k++) {
                    temp.push(result[j].concat([arrays[i][k]]));
                }
            }
            result = temp;
        }
        return result;
    }

    // Inicializar si viene en modo edicion con variantes
    <?php if (isset($tiene_variantes) && $tiene_variantes): ?>
    $(document).ready(function() {
        document.getElementById('chk_variantes').checked = true;
        toggleVariantes();
        $.getJSON('<?= base_url("products/get_variantes_producto/" . (isset($id) ? $id : 0)) ?>', function(data) {
            if (data.length > 0) {
                var tbody = document.getElementById('tbody_variantes');
                tbody.innerHTML = '';
                for (var i = 0; i < data.length; i++) {
                    var v = data[i];
                    tbody.innerHTML += '<tr>'
                        + '<td>' + (v.combinacion || '-') + '<input type="hidden" name="var_id[]" value="' + v.id + '">'
                        + '<input type="hidden" name="var_attrs[]" value=\'' + JSON.stringify(v.atributos || []) + '\'></td>'
                        + '<td><input type="text" name="var_sku[]" value="' + (v.sku || '') + '" class="form-control input-sm" style="font-size:11px;"></td>'
                        + '<td><input type="text" name="var_barcode[]" value="' + (v.barcode || '') + '" class="form-control input-sm" style="font-size:11px;"></td>'
                        + '<td><input type="text" name="var_price[]" value="' + (v.price || '') + '" class="form-control input-sm" style="font-size:11px;" placeholder="Heredar"></td>'
                        + '<td><input type="text" name="var_pmayor[]" value="' + (v.precio_x_mayor || '') + '" class="form-control input-sm" style="font-size:11px;" placeholder="Heredar"></td>'
                        + '<td class="text-center"><input type="checkbox" name="var_activo[]" value="' + i + '" ' + (v.activo == '1' ? 'checked' : '') + '></td>'
                        + '<td class="text-center"><a href="#" onclick="$(this).closest(\'tr\').remove();return false;" style="color:#dc3545;"><i class="fa fa-times"></i></a></td>'
                        + '</tr>';
                }
                document.getElementById('tabla_variantes').style.display = 'table';
            }
        });
    });
    <?php endif; ?>
</script>
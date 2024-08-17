var ar_real = [];

function visualizar_impresionx(){
    var nro_compra = document.getElementById("nro_compra").value

    // Colocandolo en el array global
    //ar_real.push(nro_compra)

    document.getElementById("nro_compra").value = ""

    xp = new XMLHttpRequest();
    xp.open("POST", base_url + "products/incluir_nro_compra");
    xp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xp.send("nro_compra="+nro_compra);

    xp.onload = function(){
        
        var obj = JSON.parse(this.responseText);
        //console.log(obj)
        var x = 0
        var cad = "<table class=\"table\">"
        
        cad += "<tr>" + celdah("Item") + celdah("compra_id")  + celdah("Codigo") + celdah("Producto") + celdah("Cantidad")  + celdah(".") + "</tr>"

        for(casco in obj){
            //console.log( casco )
            x = x + 1
            cad += "<tr>"
            cad += celda( x + ")<input type=\"hidden\" name=\"product_id[]\" value=\"" + obj[casco]["product_id"] + "\">" )
            cad += celda_edita( obj[casco]["compra_id"], "compra_id[]", "width:55px;" )
            cad += celda( obj[casco]["code"] )
            cad += celda( obj[casco]["nombre_producto"] )
            let nombre_celda = "cantidad[]"
            cad += celda_edita( obj[casco]["cantidad"], nombre_celda, "width:55px;")
            cad += celda( "<span class=\"glyphicon glyphicon-remove iconos\" onclick=\"eliminar_item(" + obj[casco]["id"] + ")\"></span>" )
            cad += "</tr>"
        }
        cad += "</table>"

        document.getElementById("taxi").innerHTML = cad

        /*var cad = "<table>"
        for(casco in obj){
            lim_reg = obj[casco].length
            lim_campos = obj[casco][0].length
            for(var i=0; i<lim_reg; i++){
                cad += "<tr>"
                for(var j=0; j<lim_campos-1; j++){
                    cad += celda(obj[casco][i][j])
                }
                cad += "</tr>"
            }
        }
        cad += "</table>"*/
    }
}

function celda(cad){
    return "<td>" + cad + "</td>" 
}

function celdah(cad){
    return "<th>" + cad + "</th>" 
}

function celda_edita(cad, nombre, estilo=""){
    return "<td><input type=\"text\" name=\"" + nombre + "\" id=\"" + nombre + "\" class=\"control-form\" value=\"" + cad + "\" style=\"" + estilo + "\"></td>" 
}

/*
function guardar(){
    
    // acopio de datos en array
    let cantidad = document.getElementById("cantidad[]").value

    xp = new XMLHttpRequest();
    xp.open("POST", base_url + "products/save_impresionx");
    xp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xp.send("cantido="+cantidad);

    xp.onload = function(){
        alert(this.responseText);
        document.getElementById("taxi").innerHTML = cad
    }

}
*/
function resetear_impresionx(){
    xp = new XMLHttpRequest();
    xp.open("POST", base_url + "products/reset_impresionx");
    xp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xp.send();

    xp.onload = function(){
        alert(this.responseText);
        document.getElementById("taxi").innerHTML = ""
    }
}

function eliminar_item(id){
    if(confirm("Seguro desea eliminar?")){
        xp = new XMLHttpRequest();
        xp.open("POST", base_url + "products/elimina_item_impresionx");
        xp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xp.send("id="+id);

        xp.onload = function(){
            if (this.responseText == "OK"){
                window.location.reload()
            }else{
                alert(this.responseText)
            }
        }
    }
}

function confirmacion(){
    if (confirm("Asegúrese de haber guardado primero, desea continuar?")){
        document.getElementById("form_compra2").submit()
    }
}

visualizar_impresionx();

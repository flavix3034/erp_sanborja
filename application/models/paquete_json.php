<?php

//$ruta = "https://api.nubefact.com/api/v1/98121ccf-5d02-4f08-80d0-f4322ac22e27";
$ruta = "https://api.nubefact.com/api/v1/ee047059-16bb-4595-adb6-fc5e559ee23f";

//TOKEN para enviar documentos
//$token = "f8011ff38c484ec4ac05a21d011d2b4b1ee2d9dca8164213a25252c32124987d";
$token = "1ebd60ff9fcb4411b26b9cc192bb480156e131c38fcf4a18b7b3636ac1d4fdec";

/*
#########################################################
#### PASO 2: GENERAR EL ARCHIVO PARA ENVIAR A NUBEFACT ####
+++++++++++++++++++++++++++++++++++++++++++++++++++++++
# - MANUAL para archivo JSON en el link: https://goo.gl/WHMmSb
# - MANUAL para archivo TXT en el link: https://goo.gl/Lz7hAq
+++++++++++++++++++++++++++++++++++++++++++++++++++++++
 */

$ar_obj = array(
    "operacion"                         => "generar_comprobante",
    "tipo_de_comprobante"               => $tipo, /*"1",*/
    "serie"                             => $serie, /*"FFF1",*/
    "numero"                            => $numero, /*"1",*/
    "sunat_transaction"                 => "1",
    "cliente_tipo_de_documento"         => $cliente_tipo_de_documento,
    "cliente_numero_de_documento"       => $cliente_numero_de_documento,
    "cliente_denominacion"              => $cliente_denominacion,
    "cliente_direccion"                 => $cliente_direccion,
    "cliente_email"                     => "",
    "cliente_email_1"                   => "",
    "cliente_email_2"                   => "",
    "fecha_de_emision"                  => $fecha_de_emision,
    "fecha_de_vencimiento"              => $fecha_de_vencimiento, 
    "moneda"                            => $moneda,
    "tipo_de_cambio"                    => "",
    "porcentaje_de_igv"                 => $porcentaje_de_igv,
    "descuento_global"                  => "",
    "descuento_global"                  => "",
    "total_descuento"                   => $total_descuento,
    "total_anticipo"                    => "",
    "total_gravada"                     => $total_gravada,
    "total_inafecta"                    => "",
    "total_exonerada"                   => "",
    "total_igv"                         => $total_igv,
    "total_gratuita"                    => "",
    "total_otros_cargos"                => "",
    "total"                             => $total,
    "percepcion_tipo"                   => "",
    "percepcion_base_imponible"         => "",
    "total_percepcion"                  => "",
    "total_incluido_percepcion"         => "",
    "detraccion"                        => "false",
    "observaciones"                     => "",
    "documento_que_se_modifica_tipo"    => $documento_que_se_modifica_tipo,
    "documento_que_se_modifica_serie"   => $documento_que_se_modifica_serie,
    "documento_que_se_modifica_numero"  => $documento_que_se_modifica_numero,
    "tipo_de_nota_de_credito"           => $tipo_de_nota_de_credito,
    "tipo_de_nota_de_debito"            => $tipo_de_nota_de_debito,
    "enviar_automaticamente_a_la_sunat" => "true",
    "enviar_automaticamente_al_cliente" => "false",
    "codigo_unico"                      => "",
    "condiciones_de_pago"               => "",
    "medio_de_pago"                     => "",
    "placa_vehiculo"                    => "",
    "orden_compra_servicio"             => "",
    "tabla_personalizada_codigo"        => "",
    "formato_de_pdf"                    => "",
    "items" => $items_
);

$this->el_json = print_r($ar_obj, true) . "\n\n";  // Para colocarlo en un archivo de texto
$data_json = json_encode($ar_obj);

/*
#########################################################
#### PASO 3: ENVIAR EL ARCHIVO A NUBEFACT ####
+++++++++++++++++++++++++++++++++++++++++++++++++++++++
# SI ESTÁS TRABAJANDO CON ARCHIVO JSON
# - Debes enviar en el HEADER de tu solicitud la siguiente lo siguiente:
# Authorization = Token token="8d19d8c7c1f6402687720eab85cd57a54f5a7a3fa163476bbcf381ee2b5e0c69"
# Content-Type = application/json
# - Adjuntar en el CUERPO o BODY el archivo JSON o TXT
# SI ESTÁS TRABAJANDO CON ARCHIVO TXT
# - Debes enviar en el HEADER de tu solicitud la siguiente lo siguiente:
# Authorization = Token token="8d19d8c7c1f6402687720eab85cd57a54f5a7a3fa163476bbcf381ee2b5e0c69"
# Content-Type = text/plain
# - Adjuntar en el CUERPO o BODY el archivo JSON o TXT
+++++++++++++++++++++++++++++++++++++++++++++++++++++++
*/

/*
//Invocamos el servicio de NUBEFACT
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $ruta);
curl_setopt(
    $ch, CURLOPT_HTTPHEADER, array(
    'Authorization: Token token="'.$token.'"',
    'Content-Type: application/json',
    )
);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$respuesta  = curl_exec($ch);
curl_close($ch);
*/

/*
 #########################################################
#### PASO 4: LEER RESPUESTA DE NUBEFACT ####
+++++++++++++++++++++++++++++++++++++++++++++++++++++++
# Recibirás una respuesta de NUBEFACT inmediatamente lo cual se debe leer, verificando que no haya errores.
# Debes guardar en la base de datos la respuesta que te devolveremos.
# Escríbenos a soporte@nubefact.com o llámanos al teléfono: 01 468 3535 (opción 2) o celular (WhatsApp) 955 598762
# Puedes imprimir el PDF que nosotros generamos como también generar tu propia representación impresa previa coordinación con nosotros.
# La impresión del documento seguirá haciéndose desde tu sistema. Enviaremos el documento por email a tu cliente si así lo indicas en el archivo JSON o TXT.
+++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 */


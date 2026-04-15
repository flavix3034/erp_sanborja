<?php $es_nuevo = empty($rol); ?>

<div class="row">
    <div class="col-md-11">

        <?= form_open(base_url('roles/save')) ?>
        <input type="hidden" name="grupo_id" value="<?= $es_nuevo ? 0 : $rol['id'] ?>">

        <div class="form-group">
            <label for="nombre">Nombre del Rol <span style="color:red">*</span></label>
            <input type="text" name="nombre" id="nombre" class="form-control"
                style="max-width: 350px;"
                placeholder="Ej: Cajero, Vendedor, Supervisor..."
                value="<?= $es_nuevo ? '' : htmlspecialchars($rol['grupo']) ?>" required>
        </div>

        <hr>
        <h4>Permisos por Módulo</h4>
        <p class="text-muted" style="font-size:13px;">
            Expande cada módulo para elegir qué sub-opciones tendrá disponibles este rol.
            Al activar el encabezado del módulo se marcan todos sus ítems de una vez.
        </p>

        <div style="margin-bottom:14px;">
            <button type="button" class="btn btn-xs btn-default" onclick="toggleTodos(true)">Seleccionar todo</button>
            <button type="button" class="btn btn-xs btn-default" onclick="toggleTodos(false)">Deseleccionar todo</button>
        </div>

        <?php
        $seccion_actual = '';
        foreach ($modulos as $padre):
            // Separador de sección
            if ($padre['seccion'] !== $seccion_actual):
                $seccion_actual = $padre['seccion'];
        ?>
        <h5 style="margin-top:22px;margin-bottom:6px;text-transform:uppercase;color:#555;font-size:11px;letter-spacing:1px;border-bottom:1px solid #ddd;padding-bottom:4px;">
            <?= htmlspecialchars($seccion_actual) ?>
        </h5>
        <?php endif; ?>

        <div class="panel panel-default" style="margin-bottom:8px;">
            <div class="panel-heading" style="padding:8px 14px;cursor:pointer;background:#f7f7f7;"
                 onclick="toggleModulo(<?= $padre['id'] ?>)">
                <label style="margin:0;cursor:pointer;font-weight:600;">
                    <input type="checkbox"
                        id="padre_<?= $padre['id'] ?>"
                        style="margin-right:7px;cursor:pointer;"
                        onclick="event.stopPropagation(); toggleHijos(<?= $padre['id'] ?>, this.checked)">
                    <i class='bx bx-chevron-right' id="icono_<?= $padre['id'] ?>"></i>
                    <?= htmlspecialchars($padre['nombre']) ?>
                    <span class="badge" id="badge_<?= $padre['id'] ?>" style="margin-left:8px;background:#aaa;font-size:10px;">0</span>
                </label>
            </div>
            <div class="panel-body" id="hijos_<?= $padre['id'] ?>" style="display:none;padding:10px 18px;">
                <?php if (empty($padre['hijos'])): ?>
                    <em class="text-muted" style="font-size:12px;">Sin submódulos configurados</em>
                <?php else: ?>
                <div class="row">
                    <?php foreach ($padre['hijos'] as $hijo):
                        $checked = in_array($hijo['id'], $permisos_actuales);
                    ?>
                    <div class="col-sm-4 col-md-3" style="margin-bottom:8px;">
                        <label style="font-weight:normal;cursor:pointer;font-size:13px;">
                            <input type="checkbox"
                                name="modulos[]"
                                value="<?= $hijo['id'] ?>"
                                class="hijo-<?= $padre['id'] ?>"
                                onchange="actualizarPadre(<?= $padre['id'] ?>)"
                                <?= $checked ? 'checked' : '' ?>>
                            <span style="margin-left:4px;"><?= htmlspecialchars($hijo['nombre']) ?></span>
                        </label>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>

        <hr>
        <button type="submit" class="btn btn-success">
            <i class="glyphicon glyphicon-floppy-disk"></i>
            <?= $es_nuevo ? 'Crear Rol' : 'Guardar Cambios' ?>
        </button>
        <a href="<?= base_url('roles/index') ?>" class="btn btn-default">Cancelar</a>
        <?= form_close() ?>
    </div>
</div>

<script>
// Abre/cierra el panel de hijos
function toggleModulo(padreId) {
    var panel = document.getElementById('hijos_' + padreId);
    var icono = document.getElementById('icono_' + padreId);
    if (panel.style.display === 'none') {
        panel.style.display = 'block';
        icono.className = 'bx bx-chevron-down';
    } else {
        panel.style.display = 'none';
        icono.className = 'bx bx-chevron-right';
    }
}

// Marca/desmarca todos los hijos de un módulo
function toggleHijos(padreId, estado) {
    document.querySelectorAll('.hijo-' + padreId).forEach(function(cb) {
        cb.checked = estado;
    });
    actualizarPadre(padreId);
    // Si se activa, abrir el panel
    if (estado) {
        var panel = document.getElementById('hijos_' + padreId);
        var icono = document.getElementById('icono_' + padreId);
        panel.style.display = 'block';
        icono.className = 'bx bx-chevron-down';
    }
}

// Actualiza el estado del checkbox padre y el badge contador
function actualizarPadre(padreId) {
    var hijos    = document.querySelectorAll('.hijo-' + padreId);
    var marcados = document.querySelectorAll('.hijo-' + padreId + ':checked');
    var padre    = document.getElementById('padre_' + padreId);
    var badge    = document.getElementById('badge_' + padreId);

    badge.textContent = marcados.length + '/' + hijos.length;
    badge.style.background = marcados.length > 0 ? '#5cb85c' : '#aaa';

    if (marcados.length === 0) {
        padre.indeterminate = false;
        padre.checked = false;
    } else if (marcados.length === hijos.length) {
        padre.indeterminate = false;
        padre.checked = true;
    } else {
        padre.indeterminate = true;
    }
}

// Seleccionar / deseleccionar todo
function toggleTodos(estado) {
    document.querySelectorAll('input[name="modulos[]"]').forEach(function(cb) {
        cb.checked = estado;
    });
    // Actualizar todos los padres
    document.querySelectorAll('[id^="padre_"]').forEach(function(chk) {
        var padreId = chk.id.replace('padre_', '');
        actualizarPadre(padreId);
        if (estado) {
            var panel = document.getElementById('hijos_' + padreId);
            var icono = document.getElementById('icono_' + padreId);
            panel.style.display = 'block';
            icono.className = 'bx bx-chevron-down';
        }
    });
}

// Inicializar badges y estado de padres al cargar
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[id^="padre_"]').forEach(function(chk) {
        var padreId = chk.id.replace('padre_', '');
        actualizarPadre(padreId);
        // Si tiene hijos marcados, abrir el panel automáticamente
        var marcados = document.querySelectorAll('.hijo-' + padreId + ':checked');
        if (marcados.length > 0) {
            document.getElementById('hijos_' + padreId).style.display = 'block';
            document.getElementById('icono_' + padreId).className = 'bx bx-chevron-down';
        }
    });
});
</script>

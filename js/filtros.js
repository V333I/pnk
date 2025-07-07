document.addEventListener('DOMContentLoaded', function() {
    console.log('Iniciando carga de filtros...');
    // Cargar regiones al iniciar
    cargarRegiones();

    // Event listeners para los cambios en los selectores
    document.getElementById('region').addEventListener('change', function() {
        const regionId = this.value;
        console.log('Región seleccionada:', regionId);
        if (regionId) {
            cargarProvincias(regionId);
        } else {
            document.getElementById('provincia').innerHTML = '<option value="">Seleccione provincia</option>';
            document.getElementById('comuna').innerHTML = '<option value="">Seleccione comuna</option>';
            document.getElementById('sector').innerHTML = '<option value="">Seleccione sector</option>';
        }
    });

    document.getElementById('provincia').addEventListener('change', function() {
        const provinciaId = this.value;
        console.log('Provincia seleccionada:', provinciaId);
        if (provinciaId) {
            cargarComunas(provinciaId);
        } else {
            document.getElementById('comuna').innerHTML = '<option value="">Seleccione comuna</option>';
            document.getElementById('sector').innerHTML = '<option value="">Seleccione sector</option>';
        }
    });

    document.getElementById('comuna').addEventListener('change', function() {
        const comunaId = this.value;
        if (comunaId) {
            cargarSectores(comunaId);
        } else {
            document.getElementById('sector').innerHTML = '<option value="">Seleccione sector</option>';
        }
    });

    // Event listener para todos los filtros
    const filtros = ['tipo_propiedad', 'region', 'provincia', 'comuna', 'sector'];
    filtros.forEach(filtro => {
        document.getElementById(filtro).addEventListener('change', actualizarResultados);
    });
});

function cargarRegiones() {
    console.log('Cargando regiones...');
    fetch('obtener_ubicaciones.php?tipo=regiones')
        .then(response => {
            console.log('Respuesta recibida:', response);
            return response.json();
        })
        .then(data => {
            console.log('Datos de regiones:', data);
            if (data.success) {
                const select = document.getElementById('region');
                select.innerHTML = '<option value="">Seleccione región</option>';
                data.data.forEach(region => {
                    const option = document.createElement('option');
                    option.value = region.idregion;
                    option.textContent = region.nombre_region;
                    select.appendChild(option);
                });
            } else {
                console.error('Error al cargar regiones:', data.error);
            }
        })
        .catch(error => {
            console.error('Error en la petición de regiones:', error);
        });
}

function cargarProvincias(regionId) {
    console.log('Cargando provincias para región:', regionId);
    fetch(`obtener_ubicaciones.php?tipo=provincias&id=${regionId}`)
        .then(response => response.json())
        .then(data => {
            console.log('Datos de provincias:', data);
            if (data.success) {
                const select = document.getElementById('provincia');
                select.innerHTML = '<option value="">Seleccione provincia</option>';
                data.data.forEach(provincia => {
                    const option = document.createElement('option');
                    option.value = provincia.idprovincias;
                    option.textContent = provincia.nombre_provincia;
                    select.appendChild(option);
                });
                document.getElementById('comuna').innerHTML = '<option value="">Seleccione comuna</option>';
                document.getElementById('sector').innerHTML = '<option value="">Seleccione sector</option>';
            } else {
                console.error('Error al cargar provincias:', data.error);
            }
        })
        .catch(error => {
            console.error('Error en la petición de provincias:', error);
        });
}

function cargarComunas(provinciaId) {
    console.log('Cargando comunas para provincia:', provinciaId);
    fetch(`obtener_ubicaciones.php?tipo=comunas&id=${provinciaId}`)
        .then(response => response.json())
        .then(data => {
            console.log('Datos de comunas:', data);
            if (data.success) {
                const select = document.getElementById('comuna');
                select.innerHTML = '<option value="">Seleccione comuna</option>';
                data.data.forEach(comuna => {
                    const option = document.createElement('option');
                    option.value = comuna.idcomunas;
                    option.textContent = comuna.nombre_comuna;
                    select.appendChild(option);
                });
                document.getElementById('sector').innerHTML = '<option value="">Seleccione sector</option>';
            } else {
                console.error('Error al cargar comunas:', data.error);
            }
        })
        .catch(error => {
            console.error('Error en la petición de comunas:', error);
        });
}

function cargarSectores(comunaId) {
    console.log('Cargando sectores para comuna:', comunaId);
    fetch(`obtener_ubicaciones.php?tipo=sectores&id=${comunaId}`)
        .then(response => response.json())
        .then(data => {
            console.log('Datos de sectores:', data);
            if (data.success) {
                const select = document.getElementById('sector');
                select.innerHTML = '<option value="">Seleccione sector</option>';
                data.data.forEach(sector => {
                    const option = document.createElement('option');
                    option.value = sector.idsectores;
                    option.textContent = sector.nombre_sector;
                    select.appendChild(option);
                });
            } else {
                console.error('Error al cargar sectores:', data.error);
            }
        })
        .catch(error => {
            console.error('Error en la petición de sectores:', error);
        });
}

function actualizarResultados() {
    var tipo = $("#tipo_propiedad").val();
    var region = $("#region").val();
    var provincia = $("#provincia").val();
    var comuna = $("#comuna").val();
    var sector = $("#sector").val();

    $.ajax({
        url: 'obtener_propiedades.php',
        type: 'POST',
        data: {
            tipo: tipo,
            region: region,
            provincia: provincia,
            comuna: comuna,
            sector: sector
        },
        success: function(response) {
            $(".galeria").html(response);
        },
        error: function() {
            $(".galeria").html('<div class="alert alert-danger">Error al buscar propiedades.</div>');
        }
    });
} 
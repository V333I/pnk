// Función para abrir el modal de edición
function abrirModalEditarGestor(id, rut, nombre, fecha, correo, sexo, telefono, estado) {
    try {
        document.getElementById('id_gestor').value = id;
        document.getElementById('rut_gestor').value = rut;
        document.getElementById('nombre_gestor').value = nombre;
        document.getElementById('fecha_nacimiento_gestor').value = fecha;
        document.getElementById('correo_gestor').value = correo;
        document.getElementById('sexo_gestor').value = sexo;
        document.getElementById('telefono_gestor').value = telefono;
        document.getElementById('estado_gestor').value = estado;
        
        // Establecer la fecha máxima como hoy
        const fechaInput = document.getElementById('fecha_nacimiento_gestor');
        const hoy = new Date();
        const formatoFecha = hoy.toISOString().split('T')[0];
        fechaInput.max = formatoFecha;
        
        var modal = new bootstrap.Modal(document.getElementById('modalEditarGestor'));
        modal.show();
    } catch (error) {
        console.error('Error al abrir modal:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error al abrir el formulario de edición'
        });
    }
}

// Función para validar RUT chileno
function validarRut(rut) {
    if (!/^[0-9]+-[0-9kK]{1}$/.test(rut)) return false;
    var tmp = rut.split('-');
    var digv = tmp[1].toLowerCase();
    var rut = tmp[0];
    if (digv == 'k') digv = 'k';
    return (dv(rut) == digv);
}

function dv(T) {
    var M = 0, S = 1;
    for (; T; T = Math.floor(T/10))
        S = (S + T % 10 * (9 - M++ % 6)) % 11;
    return S ? S - 1 : 'k';
}

// Función para validar teléfono
function validarTelefono(telefono) {
    const regex = /^\+569\d{8}$/;
    return regex.test(telefono);
}

// Función para validar email
function validarEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

// Función para validar fecha de nacimiento
function validarFechaNacimiento(fecha) {
    const fechaSeleccionada = new Date(fecha);
    const hoy = new Date();
    return fechaSeleccionada <= hoy;
}

// Función para actualizar gestor
function actualizarGestor(event) {
    event.preventDefault();
    
    const formData = new FormData(document.getElementById('formEditarGestor'));
    const fechaNacimiento = document.getElementById('fecha_nacimiento_gestor').value;
    const rut = document.getElementById('rut_gestor').value;
    const correo = document.getElementById('correo_gestor').value;
    const telefono = document.getElementById('telefono_gestor').value;

    // Validar RUT
    if (!validarRut(rut)) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'El RUT ingresado no es válido'
        });
        return;
    }

    // Validar fecha de nacimiento
    if (!validarFechaNacimiento(fechaNacimiento)) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'La fecha de nacimiento no puede ser una fecha futura'
        });
        return;
    }

    // Validar email
    if (!validarEmail(correo)) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'El correo electrónico no es válido'
        });
        return;
    }

    // Validar teléfono
    if (!validarTelefono(telefono)) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'El teléfono debe tener el formato +569XXXXXXXX'
        });
        return;
    }
    
    fetch('actualizar_gestor.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor');
        }
        return response.json();
    })
    .then(data => {
        if (data.status === 'success') {
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: data.message,
                showConfirmButton: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.reload();
                }
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Error al actualizar el gestor'
            });
        }
    })
    .catch(error => {
        console.error('Error en la solicitud:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Hubo un error al procesar la solicitud'
        });
    });
}

// Función para eliminar gestor
function eliminarGestor(id) {
    if (!id) {
        console.error('ID no proporcionado');
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'ID de gestor no válido'
        });
        return;
    }

    Swal.fire({
        title: '¿Estás seguro?',
        text: "Esta acción no se puede deshacer",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append('id', id);
            
            fetch('eliminar_gestor.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Eliminado!',
                        text: data.message,
                        showConfirmButton: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.reload();
                        }
                    });
                } else {
                    throw new Error(data.message || 'Error al eliminar el gestor');
                }
            })
            .catch(error => {
                console.error('Error en la solicitud:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'Hubo un error al eliminar el gestor'
                });
            });
        }
    });
} 
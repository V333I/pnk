// Función para abrir el modal de edición
function abrirModalEditarPropietario(id, rut, nombre, fecha, correo, sexo, telefono, propiedad) {
    try {
        if (!document.getElementById('edit_id')) console.error('Falta el campo edit_id');
        if (!document.getElementById('edit_rut')) console.error('Falta el campo edit_rut');
        if (!document.getElementById('edit_nombre')) console.error('Falta el campo edit_nombre');
        if (!document.getElementById('edit_fecha')) console.error('Falta el campo edit_fecha');
        if (!document.getElementById('edit_correo')) console.error('Falta el campo edit_correo');
        if (!document.getElementById('edit_sexo')) console.error('Falta el campo edit_sexo');
        if (!document.getElementById('edit_telefono')) console.error('Falta el campo edit_telefono');

        document.getElementById('edit_id').value = id;
        document.getElementById('edit_rut').value = rut;
        document.getElementById('edit_nombre').value = nombre;
        document.getElementById('edit_fecha').value = fecha;
        document.getElementById('edit_correo').value = correo;
        document.getElementById('edit_sexo').value = sexo;
        document.getElementById('edit_telefono').value = telefono;
        // Si tienes un campo para propiedad, agrégalo aquí si existe en el HTML
        var modal = new bootstrap.Modal(document.getElementById('modalEditarPropietario'));
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

// Función para actualizar propietario
function actualizarPropietario(event) {
    event.preventDefault();
    
    const formData = new FormData(document.getElementById('formEditarPropietario'));
    const fechaNacimiento = document.getElementById('edit_fecha').value;

    // Validar fecha de nacimiento
    if (!validarFechaNacimiento(fechaNacimiento)) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'La fecha de nacimiento no puede ser una fecha futura'
        });
        return;
    }
    
    // Mostrar los datos que se van a enviar
    console.log('Datos a enviar:');
    for (let pair of formData.entries()) {
        console.log(pair[0] + ': ' + pair[1]);
    }
    
    fetch('actualizar_propietario.php', {
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
        console.log('Respuesta del servidor:', data);
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
                text: data.message || 'Error al actualizar el propietario'
            });
        }
    })
    .catch(error => {
        console.error('Error en la solicitud:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Hubo un error al procesar la solicitud. Por favor, intente nuevamente.'
        });
    });
}

// Función para eliminar propietario
function eliminarPropietario(id) {
    if (!id) {
        console.error('ID no proporcionado');
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'ID de propietario no válido'
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
            
            console.log('Intentando eliminar propietario con ID:', id);
            
            fetch('eliminar_propietario.php', {
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
                console.log('Respuesta del servidor:', data);
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
                    throw new Error(data.message || 'Error al eliminar el propietario');
                }
            })
            .catch(error => {
                console.error('Error en la solicitud:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'Hubo un error al eliminar el propietario'
                });
            });
        }
    });
} 
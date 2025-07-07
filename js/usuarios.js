function abrirModalEditar(id, rut, nombres, ap_paterno, ap_materno, usuario, estado) {
    document.getElementById('editId').value = id;
    document.getElementById('editRut').value = rut;
    document.getElementById('editNombres').value = nombres;
    document.getElementById('editApPaterno').value = ap_paterno;
    document.getElementById('editApMaterno').value = ap_materno;
    document.getElementById('editUsuario').value = usuario;
    document.getElementById('editEstado').value = estado;
    
    // Mostrar el modal
    var modal = new bootstrap.Modal(document.getElementById('modalEditar'));
    modal.show();
}

function actualizarUsuario(event) {
    event.preventDefault();
    
    const formData = new FormData(document.getElementById('formEditar'));
    
    fetch('actualizar_usuario.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.status === 'success') {
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: 'Usuario actualizado correctamente',
                showConfirmButton: true
            }).then((result) => {
                if (result.isConfirmed) {
                    location.reload();
                }
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message
            });
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Hubo un error al procesar la solicitud'
        });
    });
}

function eliminarUsuario(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Esta acción no se puede revertir",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#AC1754',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('eliminar_usuario.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'id=' + id
            })
            .then(response => response.json())
            .then(data => {
                if(data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Eliminado!',
                        text: 'El usuario ha sido eliminado.',
                        showConfirmButton: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message
                    });
                }
            });
        }
    });
} 
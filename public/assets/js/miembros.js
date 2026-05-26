$(document).ready(function () {
    // Enviar formulario de agregar/editar miembro
    $('#addMemberForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: 'insertar_miembro.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    showSuccessMessage('Miembro agregado exitosamente');
                    $('#addMemberModal').modal('hide');
                    location.reload(); // Recargar la página o actualizar la tabla
                } else {
                    showErrorMessage('Error: ' + response.error);
                }
            },
            error: function (xhr, status, error) {
                showErrorMessage('Ha ocurrido un error: ' + error);
            }
        });
    });

    // Inicializar el modal y el formulario de miembro
    var memberModal = new bootstrap.Modal(document.getElementById('memberModal'));
    var memberForm = document.getElementById('memberForm');

    // Botón para agregar nuevo miembro
    document.querySelector('[data-bs-target="#memberModal"]').addEventListener('click', function () {
        resetForm(memberForm);
        document.getElementById('action').value = 'add';
        document.getElementById('memberModalLabel').textContent = 'Nuevo Miembro';
    });

    // Botones de edición de miembros
    document.querySelectorAll('.edit-btn').forEach(function (button) {
        button.addEventListener('click', function () {
            var miembro_id = this.getAttribute('data-id');
            editMember(miembro_id);
        });
    });

    // Botones de eliminación de miembros
    document.querySelectorAll('.delete-btn').forEach(function (button) {
        button.addEventListener('click', function () {
            var miembro_id = this.getAttribute('data-id');
            if (confirm('¿Estás seguro de que quieres eliminar este miembro?')) {
                window.location.href = '?delete=' + miembro_id;
            }
        });
    });

    // Función para mostrar un mensaje de éxito
    function showSuccessMessage(message) {
        alert(message); // Puedes mejorarlo con una notificación visual
    }

    // Función para mostrar un mensaje de error
    function showErrorMessage(message) {
        alert(message); // Puedes mejorarlo con una notificación visual
    }

    // Función para restablecer el formulario
    function resetForm(form) {
        form.reset();
        document.querySelectorAll('input, select').forEach(function (element) {
            element.disabled = false;
        });
    }

    // Función para editar un miembro
    function editMember(miembro_id) {
        document.getElementById('action').value = 'edit';
        document.getElementById('miembro_id').value = miembro_id;
        document.getElementById('memberModalLabel').textContent = 'Editar Miembro';

        // Hacer una petición AJAX para obtener los datos del miembro
        fetch('?edit=' + miembro_id)
            .then(response => response.json())
            .then(data => {
                populateForm(data);
                memberModal.show(); // Mostrar el modal
            })
            .catch(error => showErrorMessage('Error al obtener los datos: ' + error));
    }

    // Función para llenar el formulario con los datos del miembro
    function populateForm(data) {
        Object.keys(data).forEach(key => {
            let field = document.getElementById(key);
            if (field) {
                field.value = data[key];

                // Deshabilitar ciertos campos
                if (['estado_civil', 'sexo', 'nivel_estudio', 'cargo', 'estado'].includes(key)) {
                    field.disabled = true;
                } else if (key === 'familia') {
                    field.disabled = true;
                    document.getElementById('familia').innerHTML = data.familia_nombre;
                } else if (key === 'fecha_nacimiento') {
                    field.disabled = true;
                    field.valueAsDate = new Date(data[key]);
                } else if (['tel_celular', 'tel_fijo'].includes(key)) {
                    field.disabled = true;
                    field.value = data[key];
                }
            }
        });
    }
});

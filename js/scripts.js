// Scripts del proyecto PetsBook

// Ejecutar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('PetsBook cargado correctamente');
    
    // Auto-cerrar alertas después de 5 segundos
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.remove();
            }, 500);
        }, 5000);
    });
    
    // Validación de formularios
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            // Validar campos de contraseña si existen
            const password = form.querySelector('[name="password"]');
            const passwordConfirm = form.querySelector('[name="password_confirm"]');
            
            if (password && passwordConfirm) {
                if (password.value !== passwordConfirm.value) {
                    e.preventDefault();
                    alert('Las contraseñas no coinciden');
                    return false;
                }
            }
        });
    });
    
    // Confirmación mejorada para enlaces de borrar
    const deleteLinks = document.querySelectorAll('.btn-borrar');
    deleteLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const confirmMessage = this.getAttribute('data-confirm') || '¿Estás seguro de eliminar este elemento?';
            if (!confirm(confirmMessage)) {
                e.preventDefault();
                return false;
            }
        });
    });
    
    // Validación de fecha de cita (no permitir fechas pasadas)
    const fechaCitaInputs = document.querySelectorAll('input[name="fecha_cita"]');
    fechaCitaInputs.forEach(input => {
        const today = new Date().toISOString().split('T')[0];
        input.setAttribute('min', today);
    });
    
    // Previsualizar imagen si se proporciona URL
    const imagenInput = document.querySelector('input[name="imagen"]');
    if (imagenInput) {
        imagenInput.addEventListener('blur', function() {
            const url = this.value;
            if (url) {
                const preview = document.createElement('img');
                preview.src = url;
                preview.style.maxWidth = '200px';
                preview.style.marginTop = '10px';
                preview.onerror = function() {
                    this.remove();
                };
                
                // Remover preview anterior si existe
                const oldPreview = this.parentElement.querySelector('img');
                if (oldPreview) oldPreview.remove();
                
                this.parentElement.appendChild(preview);
            }
        });
    }
});

// Función para mostrar notificación temporal
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type}`;
    notification.textContent = message;
    notification.style.position = 'fixed';
    notification.style.top = '20px';
    notification.style.right = '20px';
    notification.style.zIndex = '10000';
    notification.style.maxWidth = '400px';
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.transition = 'opacity 0.5s';
        notification.style.opacity = '0';
        setTimeout(() => {
            notification.remove();
        }, 500);
    }, 3000);
}

// Función para validar email
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Función para validar teléfono
function validatePhone(phone) {
    const re = /^[0-9\s\-\+\(\)]+$/;
    return re.test(phone);
}

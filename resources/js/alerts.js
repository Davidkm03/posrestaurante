/**
 * Sistema de Alertas Modales Personalizadas
 * Reemplaza los alerts nativos del navegador con modales elegantes
 */

// Configuración de colores por tipo
const alertColors = {
    success: { bg: 'bg-green-100', icon: 'text-green-600', btn: 'bg-green-600 hover:bg-green-700', border: 'border-green-200' },
    error: { bg: 'bg-red-100', icon: 'text-red-600', btn: 'bg-red-600 hover:bg-red-700', border: 'border-red-200' },
    warning: { bg: 'bg-yellow-100', icon: 'text-yellow-600', btn: 'bg-yellow-600 hover:bg-yellow-700', border: 'border-yellow-200' },
    info: { bg: 'bg-blue-100', icon: 'text-blue-600', btn: 'bg-blue-600 hover:bg-blue-700', border: 'border-blue-200' },
    danger: { bg: 'bg-red-100', icon: 'text-red-600', btn: 'bg-red-600 hover:bg-red-700', border: 'border-red-200' }
};

// Iconos SVG por tipo
const alertIcons = {
    success: `<svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>`,
    error: `<svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>`,
    warning: `<svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>`,
    info: `<svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>`,
    danger: `<svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>`
};

/**
 * Muestra una alerta modal
 * @param {string} type - Tipo: 'success', 'error', 'warning', 'info', 'danger'
 * @param {string} title - Título del modal
 * @param {string} message - Mensaje del modal
 * @param {function|null} onConfirm - Callback al confirmar
 * @param {boolean} showCancel - Mostrar botón cancelar
 * @returns {Promise} - Resuelve true si confirma, false si cancela
 */
window.showAlert = function(type, title, message, onConfirm = null, showCancel = false) {
    return new Promise((resolve) => {
        const color = alertColors[type] || alertColors.info;
        const icon = alertIcons[type] || alertIcons.info;
        
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 z-[9999] flex items-center justify-center p-4';
        modal.id = 'alertModal_' + Date.now();
        
        modal.innerHTML = `
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity alert-backdrop" onclick="this.parentElement.remove()"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all animate-modal-in">
                <div class="p-6 text-center">
                    <div class="mx-auto w-16 h-16 ${color.bg} rounded-full flex items-center justify-center mb-4 ${color.icon}">
                        ${icon}
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">${title}</h3>
                    <p class="text-gray-600 mb-6">${message}</p>
                    <div class="flex gap-3 justify-center">
                        ${showCancel ? `<button class="alert-cancel-btn px-6 py-2.5 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 font-medium transition-colors">Cancelar</button>` : ''}
                        <button class="alert-confirm-btn px-6 py-2.5 ${color.btn} text-white rounded-xl font-medium transition-colors shadow-lg">
                            ${showCancel ? 'Confirmar' : 'Aceptar'}
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        // Event listeners
        const confirmBtn = modal.querySelector('.alert-confirm-btn');
        const cancelBtn = modal.querySelector('.alert-cancel-btn');
        const backdrop = modal.querySelector('.alert-backdrop');
        
        const closeModal = (result) => {
            modal.querySelector('.relative').classList.add('animate-modal-out');
            setTimeout(() => {
                modal.remove();
                resolve(result);
                if (result && onConfirm) onConfirm();
            }, 150);
        };
        
        confirmBtn.onclick = () => closeModal(true);
        if (cancelBtn) cancelBtn.onclick = () => closeModal(false);
        backdrop.onclick = () => closeModal(false);
        
        // Focus en el botón de confirmar
        setTimeout(() => confirmBtn.focus(), 100);
        
        // Cerrar con Escape
        const escHandler = (e) => {
            if (e.key === 'Escape') {
                closeModal(false);
                document.removeEventListener('keydown', escHandler);
            }
        };
        document.addEventListener('keydown', escHandler);
    });
};

/**
 * Muestra una confirmación de acción peligrosa
 * @param {string} title - Título
 * @param {string} message - Mensaje descriptivo
 * @param {string|null} warningText - Texto de advertencia adicional
 * @param {function} onConfirm - Callback al confirmar
 * @returns {Promise}
 */
window.showDangerConfirm = function(title, message, warningText, onConfirm) {
    return new Promise((resolve) => {
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 z-[9999] flex items-center justify-center p-4';
        modal.id = 'dangerModal_' + Date.now();
        
        modal.innerHTML = `
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity alert-backdrop"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-lg w-full transform transition-all animate-modal-in">
                <div class="p-6">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-gray-900">${title}</h3>
                            <p class="mt-2 text-gray-600">${message}</p>
                            ${warningText ? `
                            <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-xl">
                                <div class="flex items-center gap-2 text-red-800">
                                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                    <span class="text-sm font-medium">${warningText}</span>
                                </div>
                            </div>
                            ` : ''}
                        </div>
                    </div>
                    <div class="mt-6 flex gap-3 justify-end">
                        <button class="alert-cancel-btn px-5 py-2.5 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 font-medium transition-colors">
                            Cancelar
                        </button>
                        <button class="alert-confirm-btn px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl font-medium transition-colors shadow-lg flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Sí, continuar
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        const confirmBtn = modal.querySelector('.alert-confirm-btn');
        const cancelBtn = modal.querySelector('.alert-cancel-btn');
        const backdrop = modal.querySelector('.alert-backdrop');
        
        const closeModal = (result) => {
            modal.querySelector('.relative').classList.add('animate-modal-out');
            setTimeout(() => {
                modal.remove();
                resolve(result);
                if (result && onConfirm) onConfirm();
            }, 150);
        };
        
        confirmBtn.onclick = () => closeModal(true);
        cancelBtn.onclick = () => closeModal(false);
        backdrop.onclick = () => closeModal(false);
        
        // Cerrar con Escape
        const escHandler = (e) => {
            if (e.key === 'Escape') {
                closeModal(false);
                document.removeEventListener('keydown', escHandler);
            }
        };
        document.addEventListener('keydown', escHandler);
    });
};

/**
 * Muestra un loader/spinner de carga
 * @param {string} message - Mensaje a mostrar
 */
window.showLoader = function(message = 'Procesando...') {
    // Remover loader existente si hay uno
    hideLoader();
    
    const loader = document.createElement('div');
    loader.id = 'globalLoader';
    loader.className = 'fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm';
    loader.innerHTML = `
        <div class="bg-white rounded-2xl shadow-2xl p-8 text-center animate-modal-in">
            <div class="relative w-16 h-16 mx-auto mb-4">
                <div class="absolute inset-0 border-4 border-blue-200 rounded-full"></div>
                <div class="absolute inset-0 border-4 border-blue-600 rounded-full border-t-transparent animate-spin"></div>
            </div>
            <p class="text-gray-700 font-medium" id="loaderMessage">${message}</p>
        </div>
    `;
    document.body.appendChild(loader);
};

/**
 * Oculta el loader
 */
window.hideLoader = function() {
    const loader = document.getElementById('globalLoader');
    if (loader) {
        loader.querySelector('.bg-white').classList.add('animate-modal-out');
        setTimeout(() => loader.remove(), 150);
    }
};

/**
 * Actualiza el mensaje del loader
 * @param {string} message - Nuevo mensaje
 */
window.updateLoader = function(message) {
    const msgEl = document.getElementById('loaderMessage');
    if (msgEl) msgEl.textContent = message;
};

/**
 * Muestra una notificación toast (pequeña notificación en esquina)
 * @param {string} type - 'success', 'error', 'warning', 'info'
 * @param {string} message - Mensaje
 * @param {number} duration - Duración en ms (default: 3000)
 */
window.showToast = function(type, message, duration = 3000) {
    const color = alertColors[type] || alertColors.info;
    const icon = alertIcons[type] || alertIcons.info;
    
    // Contenedor de toasts
    let container = document.getElementById('toastContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toastContainer';
        container.className = 'fixed top-4 right-4 z-[9999] flex flex-col gap-2';
        document.body.appendChild(container);
    }
    
    const toast = document.createElement('div');
    toast.className = `flex items-center gap-3 px-4 py-3 bg-white rounded-xl shadow-lg border ${color.border} animate-slide-in-right`;
    toast.innerHTML = `
        <div class="flex-shrink-0 w-8 h-8 ${color.bg} rounded-full flex items-center justify-center ${color.icon}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                ${type === 'success' ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>' : ''}
                ${type === 'error' ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>' : ''}
                ${type === 'warning' ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"></path>' : ''}
                ${type === 'info' ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01"></path>' : ''}
            </svg>
        </div>
        <p class="text-gray-700 font-medium text-sm">${message}</p>
        <button class="ml-2 text-gray-400 hover:text-gray-600" onclick="this.parentElement.remove()">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    `;
    
    container.appendChild(toast);
    
    // Auto-remover
    setTimeout(() => {
        toast.classList.add('animate-slide-out-right');
        setTimeout(() => toast.remove(), 300);
    }, duration);
};

/**
 * Muestra un input prompt
 * @param {string} title - Título
 * @param {string} message - Mensaje
 * @param {string} placeholder - Placeholder del input
 * @param {string} defaultValue - Valor por defecto
 * @returns {Promise<string|null>} - Valor ingresado o null si cancela
 */
window.showPrompt = function(title, message, placeholder = '', defaultValue = '') {
    return new Promise((resolve) => {
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 z-[9999] flex items-center justify-center p-4';
        
        modal.innerHTML = `
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm alert-backdrop"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all animate-modal-in">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">${title}</h3>
                    <p class="text-gray-600 mb-4">${message}</p>
                    <input type="text" class="prompt-input w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" placeholder="${placeholder}" value="${defaultValue}">
                    <div class="mt-6 flex gap-3 justify-end">
                        <button class="alert-cancel-btn px-5 py-2.5 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 font-medium transition-colors">
                            Cancelar
                        </button>
                        <button class="alert-confirm-btn px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-medium transition-colors shadow-lg">
                            Aceptar
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        const input = modal.querySelector('.prompt-input');
        const confirmBtn = modal.querySelector('.alert-confirm-btn');
        const cancelBtn = modal.querySelector('.alert-cancel-btn');
        const backdrop = modal.querySelector('.alert-backdrop');
        
        const closeModal = (value) => {
            modal.querySelector('.relative').classList.add('animate-modal-out');
            setTimeout(() => {
                modal.remove();
                resolve(value);
            }, 150);
        };
        
        confirmBtn.onclick = () => closeModal(input.value);
        cancelBtn.onclick = () => closeModal(null);
        backdrop.onclick = () => closeModal(null);
        input.onkeydown = (e) => {
            if (e.key === 'Enter') closeModal(input.value);
            if (e.key === 'Escape') closeModal(null);
        };
        
        setTimeout(() => input.focus(), 100);
    });
};

// Agregar estilos de animación si no existen
if (!document.getElementById('alertAnimationStyles')) {
    const style = document.createElement('style');
    style.id = 'alertAnimationStyles';
    style.textContent = `
        @keyframes modal-in {
            from { opacity: 0; transform: scale(0.95) translateY(-10px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }
        @keyframes modal-out {
            from { opacity: 1; transform: scale(1) translateY(0); }
            to { opacity: 0; transform: scale(0.95) translateY(-10px); }
        }
        @keyframes slide-in-right {
            from { opacity: 0; transform: translateX(100%); }
            to { opacity: 1; transform: translateX(0); }
        }
        @keyframes slide-out-right {
            from { opacity: 1; transform: translateX(0); }
            to { opacity: 0; transform: translateX(100%); }
        }
        .animate-modal-in { animation: modal-in 0.2s ease-out forwards; }
        .animate-modal-out { animation: modal-out 0.15s ease-in forwards; }
        .animate-slide-in-right { animation: slide-in-right 0.3s ease-out forwards; }
        .animate-slide-out-right { animation: slide-out-right 0.3s ease-in forwards; }
    `;
    document.head.appendChild(style);
}

console.log('✅ Sistema de alertas cargado correctamente');

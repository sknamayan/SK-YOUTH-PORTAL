import './bootstrap';
import flatpickr from 'flatpickr';
import "flatpickr/dist/flatpickr.min.css";
window.flatpickr = flatpickr;

// Alpine is automatically loaded and started by Livewire 3.
// Manual initialization here causes conflict.
// import Alpine from 'alpinejs';
// window.Alpine = Alpine;
// Alpine.start();

// Double Submission Prevention (Frontend protection)
document.addEventListener('submit', function (e) {
    const form = e.target;
    
    // Ignore if the form is already submitting to prevent stack overflow/duplicate event loops
    if (form.dataset.submitting === 'true') {
        e.preventDefault();
        return;
    }
    
    // Check HTML5 validation first so missing required fields don't lock the form
    if (form.checkValidity && !form.checkValidity()) {
        return;
    }
    
    const submitButtons = form.querySelectorAll('button[type="submit"], input[type="submit"]');
    if (submitButtons.length > 0) {
        form.dataset.submitting = 'true';
        
        submitButtons.forEach(button => {
            // Save original button content
            button.dataset.originalContent = button.innerHTML || button.value;
            
            // Disable button and update UI styles
            button.disabled = true;
            button.classList.add('opacity-75', 'cursor-not-allowed');
            
            // Premium SVG spinner loader
            const spinner = `
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-current inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            `;
            
            if (button.tagName.toLowerCase() === 'button') {
                button.innerHTML = spinner + 'Processing...';
            } else {
                button.value = 'Processing...';
            }
        });
    }
});

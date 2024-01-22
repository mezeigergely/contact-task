import './bootstrap.js';
import './styles/app.scss';

document.addEventListener('DOMContentLoaded', function () {
    var successMessage = document.getElementById('success-message');
    var closeSuccess = document.getElementById('close-success');

    function hideSuccessMessage() {
        successMessage.style.display = 'none';
    }

    if (successMessage) {
        setTimeout(hideSuccessMessage, 5000);

        if (closeSuccess) {
            closeSuccess.addEventListener('click', hideSuccessMessage);
        }
    }
});

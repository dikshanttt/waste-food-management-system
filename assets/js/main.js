/* Show or hide the password when the button is clicked. */
var passwordButtons = document.querySelectorAll('[data-password-toggle]');

passwordButtons.forEach(function (button) {
    button.addEventListener('click', function () {
        var passwordBox = document.getElementById(button.dataset.passwordToggle);

        if (passwordBox.type === 'password') {
            passwordBox.type = 'text';
            button.textContent = 'Hide';
        } else {
            passwordBox.type = 'password';
            button.textContent = 'Show';
        }
    });
});
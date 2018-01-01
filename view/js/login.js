/**
 * Created by KlymenokAlexey on 17.10.17.
 */
function resetPassword() {
    event.preventDefault();
    var xhr = new XMLHttpRequest();
    var formData = new FormData(document.forms.forgot_form);
    xhr.responseType = 'text';
    xhr.onload =function () {
        if (xhr.responseText !== "success") {
            document.getElementsByClassName("error_login_message")[2].textContent = xhr.responseText;
        } else {
            document.getElementById('forgot_form').style.display = 'none';
            document.getElementsByClassName("password_reset")[0].style.display = 'block';
        }
    };
    xhr.open('POST', '/camagru/user/resetPassword');
    xhr.send(formData);
}

function createAccount() {
    event.preventDefault();
    var xhr = new XMLHttpRequest();
    var formData = new FormData(document.forms.create_user_form);
    xhr.responseType = 'text';
    xhr.onload =function () {
        if (xhr.responseText !== "success") {
            document.getElementsByClassName("error_login_message")[1].textContent = xhr.responseText;
        } else {
            document.getElementById('create_user_form').style.display = 'none';
            document.getElementsByClassName("account_created")[0].style.display = 'block';
        }
    };
    xhr.open('POST', '/camagru/user/create');
    xhr.send(formData);
}

function logIn() {
    event.preventDefault();
    var xhr = new XMLHttpRequest();
    var formData = new FormData(document.forms.login_form);
    xhr.open('POST', '/camagru/user/login?action=ajax');
    xhr.send(formData);
    xhr.onload =function () {
        if (!('success' in JSON.parse(xhr.responseText))) {
            document.querySelector(".error_login_message").textContent = JSON.parse(xhr.responseText)['error'];
        } else {
            location.reload();
        }
    };

}

function showCreateForm() {
    var createForm = document.getElementById('create_user_form');
    var loginForm = document.getElementById('login_form');
    loginForm.style.display = 'none';
    createForm.style.display = 'block';
}

function showForgotForm() {
    var forgotForm = document.getElementById('forgot_form');
    var loginForm = document.getElementById('login_form');
    loginForm.style.display = 'none';
    forgotForm.style.display = 'block';
}

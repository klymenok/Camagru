function editUserInformation() {
    if (document.querySelector('.password_change').style.display === 'block') {
        showChangePassword();
    }
    unblockEditionForm();
}

function changePassword() {
    if (document.querySelectorAll('.main_user_info')[2].style.display === 'block') {
        unblockEditionForm();
    }
    showChangePassword();
}

function showChangePassword() {
    var inputs = document.querySelectorAll('.password_change');
    var display = (inputs[0].style.display === 'block') ? 'none' : 'block';
    for (var i = 0; i < inputs.length; i++) {
        inputs[i].style.display = display;
    }
}

function unblockEditionForm() {
    var inputs = document.querySelectorAll('.main_user_info');
    var display = (inputs[0].disabled === true) ? false : true;
    for (var i = 0; i < inputs.length; i++) {
        inputs[i].disabled = display;
        inputs[i].classList.contains('input-active') ? inputs[i].classList.remove('input-active') : inputs[i].classList.add('input-active');
    }
    display === true ? inputs[2].style.display = 'none' : inputs[2].style.display = 'block';
}

window.onload = function () {
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            if ('success' in JSON.parse(xhr.response)) {
                var data = JSON.parse(xhr.response)['success'][0];
                document.querySelectorAll('.main_user_info')[0].value = data['login'];
                document.querySelectorAll('.main_user_info')[1].value = data['email'];
            }
        }
    };
    xhr.open('POST', 'user/getEmail?action=ajax');
    xhr.send();
};

function changeImformation() {
    event.preventDefault();
    var xhr = new XMLHttpRequest();
    var type = (document.querySelector('.password_change').style.display === 'block') ? 'password' : 'data';
    var formData = new FormData(document.forms.user_form);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            var response = JSON.parse(xhr.response);
            if ('success' in response) {
                document.querySelector(".error_login_message").textContent = response['success'];
                for (var i = 0; i < document.querySelectorAll('.password_change').length - 1; i++) {
                    document.querySelectorAll('.password_change')[i].value = "";
                }
            } else if ('error' in response) {
                document.querySelector(".error_login_message").textContent = response['error'];
            } else {
                document.querySelector(".error_login_message").textContent = 'error';
            }
        }
    };
    xhr.open('POST', 'user/changeInformation?action=ajax&type=' + type);
    xhr.send(formData);
}
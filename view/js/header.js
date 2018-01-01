function drop() {
    var icon = document.querySelector('.icons_container').querySelectorAll('.icon');
    for (var i = 0; i < icon.length; i++) {
        icon[i].classList.contains('active') ? icon[i].classList.remove('active') : icon[i].classList.add('active');
    }
}

document.querySelector('.main_menu').addEventListener('click', function () {

    var xhr = new XMLHttpRequest();
    var $this = this;
    xhr.open('POST', 'user/isLogged');
    xhr.send();
    xhr.onload = function () {
        if (xhr.response === 'true') {
            if ($this.classList.contains('open')) {
                $this.classList.remove('open');
            } else {
                $this.classList.add('open');
            }
            drop();
        } else {
            location.href = 'login';
        }
    };
});

document.querySelector('.icon-logout').addEventListener('click', function () {
    location.href = 'user/logout';
});

document.querySelector('.icon-photo').addEventListener('click', function () {
    location.href = 'gallery';

});

document.querySelector('.icon-user-1').addEventListener('click', function () {
    location.href = 'user';

});

document.querySelector('.icon-camera').addEventListener('click', function () {
    location.href = 'camera';

});


document.addEventListener('DOMContentLoaded', function () {
    var xhr = new XMLHttpRequest();
    document.querySelector('.main_menu').dataset.username = "";
    xhr.onload = function () {
        if ('success' in JSON.parse(xhr.response)) {
            document.querySelector('.main_menu').dataset.username = JSON.parse(xhr.response)['success'];
        }
    };
    xhr.open('POST', '/camagru/user/getUser?action=ajax');
    xhr.send();
});
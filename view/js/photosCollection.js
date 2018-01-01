function getContent() {
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            useTemplate(xhr.response);
        }
    };
    xhr.open('GET', '/camagru/view/templates/photo_container.html');
    xhr.send();
}

window.onload = function get() {
    getContent();
};

function useTemplate(response) {
    var xhr = new XMLHttpRequest();
    var pag = document.querySelector(".pagination");
    var pagData = pag.dataset;
    var user = document.querySelector('.main_menu').dataset.username;
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            var data = JSON.parse(xhr.response);
            var div = document.createElement('div');
            div.innerHTML = response;
            var container = document.getElementsByClassName('collection_container')[0];
            for (var i = 0; i < data.length; i++) {
                for (var n = 0; n < div.getElementsByClassName('likes_count').length; n++) {
                    div.getElementsByClassName('likes_count')[n].textContent = data[i]['likes'];
                }
                for (var m = 0; m < div.getElementsByClassName('comments_count').length; m++) {
                    div.getElementsByClassName('comments_count')[m].textContent = data[i]['comments_count'];
                }

                data[i]['liked'] === '1' ? div.querySelector('.icon-heart').style.color = 'red' : div.querySelector('.icon-heart').style.color = 'black';
                data[i]['login'] === user ? div.querySelector('.icon-trash').style.visibility = 'visible' : div.querySelector('.icon-trash').style.visibility = 'hidden';
                div.querySelector('img').src = '/camagru/' + data[i]['photo'];
                div.querySelector('.username').textContent = data[i]['login'];
                container.innerHTML += div.innerHTML;
                pagData.total = data[i].total;
            }
            var current = parseInt(pagData.current);
            var total = parseInt(pagData.total);
            current += data.length;
            pag.setAttribute("data-current", current);
            if (current < total) {
                pag.style.display = "inline-block";
            } else {
                pag.style.display = "none";
            }
        }
    };
    xhr.open('GET', '/camagru/gallery/getData?action=ajax&items=' + pagData.current);
    xhr.send();
}

function deletePhoto(elem) {
    var result = confirm('Are you sure?');
    if (result) {
        var xhr = new XMLHttpRequest();
        var data = new FormData();
        var photo = elem.parentNode.parentNode.querySelector('img');
        data.append('photo', photo.src);
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                if ('success' in JSON.parse(xhr.response)) {
                    var parent = photo.parentNode.parentNode;
                    parent.removeChild(photo.parentNode);
                }
            }
        };
        xhr.open('POST', '/camagru/gallery/delete?action=ajax');
        xhr.send(data);
    }
}

function openPhoto(image) {
    var filename = image.src.split('/');
    document.location.href='/camagru/gallery?photo=' + filename[filename.length - 1];
}

window.onscroll = function() {
    var body = document.body;
    var elem = document.documentElement;
    var scroll = document.documentElement.scrollTop;
    var height = Math.max( body.scrollHeight, body.offsetHeight, elem.clientHeight, elem.scrollHeight, elem.offsetHeight );
    if (scroll >= (height - document.documentElement.clientHeight)) {
        getContent();
    }
};

function like(elem) {
    if (document.querySelector('.main_menu').dataset.username === "") {
        alert("To set \"like\" you must login first");
    } else {
        var photo = elem.parentNode.parentNode.querySelector('img').src;
        if (elem.style.color === 'red') {
            elem.style.color = 'black';
            elem.parentNode.querySelector('.likes_count').textContent = parseInt(elem.parentNode.querySelector('.likes_count').textContent) - 1;
        } else {
            elem.parentNode.querySelector('.likes_count').textContent = parseInt(elem.parentNode.querySelector('.likes_count').textContent) + 1;
            elem.style.color = 'red';
        }
        var xhr = new XMLHttpRequest();
        var data = new FormData();
        data.append('photo', photo);
        xhr.open('POST', '/camagru/gallery/like?action=ajax');
        xhr.send(data);
    }
}
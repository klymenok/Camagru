window.onload = function () {
    var xhr = new XMLHttpRequest();
    var data = new FormData();
    data.append('photo', window.location.href.split('=')[1]);
    xhr.open('POST', '/camagru/gallery/getData?action=ajax&type=single');
    xhr.send(data);
    var user = document.querySelector('.main_menu').dataset.username;
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            var div = document.querySelector('.main_view');
            var data = JSON.parse(xhr.response)[0];

            div.querySelector('.likes_count').textContent = data['likes'];
            div.querySelector('.comments_count').textContent = data['comments_count'];
            data['liked'] === '1' ? div.querySelector('.icon-heart').style.color = 'red' : div.querySelector('.icon-heart').style.color = 'black';
            data['login'] === user ? div.querySelector('.icon-trash').style.visibility = 'visible' : div.querySelector('.icon-trash').style.visibility = 'hidden';
            div.querySelector('img').src = data['photo'];
            div.querySelector('.username').textContent = data['login'];

        }
    };
    getComments(window.location.href.split('=')[1]);
};

function getComments(imageName) {
    var xhr = new XMLHttpRequest();
    var data = new FormData();

    data.append('photo', imageName);

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            var comments = JSON.parse(xhr.response);
            var container = document.querySelector('.comments-container');
            for (var i = 0; i < comments.length; i++) {
                var singleComment = document.createElement('div');
                var commentTitle = document.createElement('span');
                var commentBody = document.createElement('span');

                commentTitle.className = 'comment_title';
                commentTitle.textContent = comments[i]['login'] + " ";
                commentBody.className = 'comment_body';
                commentBody.textContent = comments[i]['comment'];
                singleComment.className = 'single_comment';
                singleComment.appendChild(commentTitle);
                singleComment.appendChild(commentBody);
                container.appendChild(singleComment);
            }
        }
    };
    xhr.open('POST', '/camagru/gallery/getComments?action=ajax');
    xhr.send(data);
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

function sendComment() {
    event.preventDefault();
    if (document.querySelector('.main_menu').dataset.username === "") {
        alert("To add comment you must login first");
    } else {
        var data = new FormData();
        var xhr = new XMLHttpRequest();

        if (document.querySelector('.comment-text').value === "") {
            document.querySelector('.comment-text').placeholder = "Please, write something";
        } else {
            data.append('comment', document.querySelector('.comment-text').value);
            data.append('photo', document.querySelector('.main_view').querySelector('img').src);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4) {
                    if ('success' in JSON.parse(xhr.response)) {
                        var name = JSON.parse(xhr.response)['success'];
                        var container = document.querySelector('.comments-container');
                        var singleComment = document.createElement('div');
                        var commentTitle = document.createElement('span');
                        var commentBody = document.createElement('span');

                        commentTitle.className = 'comment_title';
                        commentTitle.textContent = name + " ";
                        commentBody.className = 'comment_body';
                        commentBody.textContent = document.querySelector('.comment-text').value;
                        singleComment.className = 'single_comment';
                        singleComment.appendChild(commentTitle);
                        singleComment.appendChild(commentBody);
                        container.appendChild(singleComment);
                        document.querySelector('.comment-text').value = "";
                    } else {
                        console.log('error');//TODO add error handler
                    }
                }
            }
        }
    };
    xhr.open('POST', '/camagru/gallery/addComment?action=ajax');
    xhr.send(data);

}

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
function uploadImage() {
    event.preventDefault();
    var xhr = new XMLHttpRequest();
    var formData = new FormData(document.forms.upload_form);
    xhr.onload = function () {
        if ('success' in JSON.parse(xhr.response)) {
            var image = document.getElementById('upload');
            var container = document.querySelector('.photo_upload_container');
            image.src = "/camagru/view/images/tmp/" + JSON.parse(xhr.response)['success'];
            container.style.opacity = 1;
        } else if ('error' in JSON.parse(xhr.response)){
            alert(JSON.parse(xhr.response)['error']);
        } else {
            alert('error');
        }
    };
    xhr.open('POST', '/camagru/camera/upload?action=ajax');
    xhr.send(formData);
}

function deleteStickers() {
    var parent = document.querySelector('.sticker_container').parentNode;
    while (parent.querySelector('.sticker_container')) {
        parent.removeChild(parent.querySelector('.sticker_container'));
    }
}

function uploadSticker() {
    event.preventDefault();
    var xhr = new XMLHttpRequest();
    var formData = new FormData(document.forms.add_sticker);
    xhr.onload = function () {
        if ('success' in JSON.parse(xhr.response)) {
            deleteStickers();
            getStickers();
        } else if ('error' in JSON.parse(xhr.response)){
            alert(JSON.parse(xhr.response)['error']);
        } else {
            alert('error');
        }
    };
    xhr.open('POST', '/camagru/camera/uploadSticker?action=ajax');
    xhr.send(formData);
}

function photo() {
    var video = document.getElementById('video');

    if(navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
        navigator.mediaDevices.getUserMedia({ video: true }).then(function(stream) {
            video.src = window.URL.createObjectURL(stream);
            video.play();
        });
    } else if(navigator.getUserMedia) {
        navigator.getUserMedia({ video: true }, function(stream) {
            video.src = stream;
            video.play();
        }, errBack);
    } else if(navigator.webkitGetUserMedia) {
        navigator.webkitGetUserMedia({ video: true }, function(stream){
            video.src = window.webkitURL.createObjectURL(stream);
            video.play();
        }, errBack);
    } else if(navigator.mozGetUserMedia) {
        navigator.mozGetUserMedia({ video: true }, function(stream){
            video.src = window.URL.createObjectURL(stream);
            video.play();
        }, errBack);
    }
}

document.getElementById("save_uploaded_photo_button").addEventListener("click", function() {
    var template = document.getElementsByClassName('preview_template')[0];
    var container = document.getElementsByClassName('preview_side')[0];
    var newNode  = document.createElement('div');
    newNode.className = 'preview_template';
    newNode.innerHTML = template.innerHTML;
    newNode.addEventListener('click', listener, false);
    container.appendChild(newNode);

    var childCount = container.childElementCount - 1;
    document.getElementsByClassName('preview_template')[childCount].style.display = 'inline-block';
    document.querySelectorAll('.preview_template')[childCount].style.height = parseInt(document.querySelectorAll('.preview_template')[childCount].offsetWidth / 4 * 3) + 'px';
    addOkIconListener(document.querySelectorAll('.preview_template')[childCount].querySelector('.icon-ok-circled'));
    var preview = document.getElementsByClassName('preview')[0];
    preview.width = preview.clientWidth;
    preview.height = preview.clientHeight;
    var previewContext = preview.getContext('2d');
    var stickers = document.getElementsByClassName('str');
    var containers = document.getElementsByClassName('canvas_container');
    var img = document.getElementById('upload');
    previewContext.drawImage(img, 0, 0, preview.width, preview.height);
    for (var i = 0; i < stickers.length; i++) {
        previewContext.drawImage(stickers[i], parseInt(containers[i].style.left), parseInt(containers[i].style.top), parseInt(containers[i].style.width), parseInt(containers[i].style.height));
    }

    createTmpFile(preview, childCount);
    var canvas = document.getElementsByClassName('canvas')[childCount];
    var context = canvas.getContext('2d');
    context.drawImage(preview, 0, 0, canvas.width, canvas.height);
    document.getElementById("cancel_upload_button").click();
    if (document.querySelector('.icon-cancel').classList.contains('toolbar_active')) {
        document.querySelector('.icon-cancel').classList.remove('toolbar_active');
        document.querySelector('.icon-floppy').classList.remove('toolbar_active');
        document.querySelector('.icon-attach').classList.remove('toolbar_active');
    }

});

document.getElementById("take_photo_button").addEventListener("click", function() {
    var template = document.getElementsByClassName('preview_template')[0];
    var container = document.getElementsByClassName('preview_side')[0];
    var newNode  = document.createElement('div');

    newNode.className = 'preview_template';
    newNode.innerHTML = template.innerHTML;
    newNode.addEventListener('click', listener, false);
    container.appendChild(newNode);
    var childCount = container.childElementCount - 1;
    document.querySelectorAll('.preview_template')[childCount].style.display = 'inline-block';
    document.querySelectorAll('.preview_template')[childCount].style.height = parseInt(document.querySelectorAll('.preview_template')[childCount].offsetWidth / 4 * 3) + 'px';
    addOkIconListener(document.querySelectorAll('.preview_template')[childCount].querySelector('.icon-ok-circled'));
    var preview = document.getElementsByClassName('preview')[0];

    preview.width = preview.clientWidth;
    preview.height = preview.clientHeight;

    var previewContext = preview.getContext('2d');
    var stickers = document.getElementsByClassName('str');
    var containers = document.getElementsByClassName('canvas_container');
    previewContext.drawImage(video, 0, 0, preview.width, preview.height);
    for (var i = 0; i < stickers.length; i++) {
        previewContext.drawImage(stickers[i], parseInt(containers[i].style.left), parseInt(containers[i].style.top), parseInt(containers[i].style.width), parseInt(containers[i].style.height));
    }

    createTmpFile(preview, childCount);
    var canvas = document.getElementsByClassName('canvas')[childCount];
    var context = canvas.getContext('2d');

    context.drawImage(preview, 0, 0, canvas.width, canvas.height);
});

document.getElementById("clear_button").addEventListener("click", function() {
    var stickers = document.getElementsByClassName('canvas_container');
    var parent = document.getElementsByClassName('stickers')[0];
    var length = stickers.length;

    for (var i = length; i > 0; i--) {
        parent.removeChild(stickers[i - 1]);
    }
});

document.getElementById("cancel_upload_button").addEventListener("click", function() {
    document.querySelector('#photo_upload').value = "";
    var container = document.getElementsByClassName('photo_upload_container')[0];
    document.querySelector('#clear_button').click();
    container.style.opacity = 0;
    if (document.querySelector('.icon-cancel').classList.contains('toolbar_active')) {
        document.querySelector('.icon-cancel').classList.remove('toolbar_active');
        document.querySelector('.icon-floppy').classList.remove('toolbar_active');
        document.querySelector('.icon-attach').classList.remove('toolbar_active');
    }
});

function addOkIconListener(icon) {
    icon.addEventListener("click", function() {
        var postData = document.querySelector('.selected');
        if (postData) {
            var xhr = new XMLHttpRequest();
            var data = new FormData();

            data.append('photo_name', postData.dataset.id);
            xhr.open('POST', '/camagru/camera/makePost?action=ajax');
            xhr.send(data);
            xhr.onload = function () {
                if ('success' in JSON.parse(xhr.response)) {
                    clearTmp();
                    window.location.href = '/camagru/gallery';
                } else if ('error' in JSON.parse(xhr.response)){
                    alert(JSON.parse(xhr.response)['error']);
                } else {
                    alert('error');
                }
            };
        } else {
            alert('no photo selected');
        }
    });
}


var listener = function() {
    var collection = document.querySelectorAll('.preview_template');
    for (var i = 0; i < collection.length; i++) {
        collection[i].className = 'preview_template';
        if (collection[i].querySelector('.post_button').classList.contains('active_button')) {
            collection[i].querySelector('.post_button').classList.remove('active_button');
            collection[i].querySelector('.icon').classList.remove('active_icon');
        }
    }
    this.className = 'preview_template selected';
    if (!this.querySelector('.post_button').classList.contains('active_button')) {
        this.querySelector('.post_button').classList.add('active_button');
        this.querySelector('.icon').classList.add('active_icon');
    }
};

function createTmpFile(canvas, childCount) {
    var img = canvas.toDataURL("image/png", 1.0);
    var form = new FormData;
    var xhr = new XMLHttpRequest();

    form.append('img', img);
    xhr.open('POST', '/camagru/camera/createTmp?action=ajax');
    xhr.send(form);
    xhr.onload = function () {
        if ('success' in JSON.parse(xhr.response)) {
            document.getElementsByClassName('preview_template')[childCount].dataset.id = JSON.parse(xhr.response)['success'];
        }
    };
}

function addEventsListeners() {
    var stickers = document.getElementsByClassName('stickers_img');
    for (var i = 0; i < stickers.length; i++) {
        stickers[i].addEventListener('click', copy, false);
    }
}

function copy() {
    var canvasContainer = document.createElement('div');
    var newCanvas = document.createElement('canvas');
    var stickers = document.getElementsByClassName('stickers');

    newCanvas.className = 'str';
    canvasContainer.appendChild(newCanvas);
    canvasContainer.className = 'canvas_container';
    canvasContainer.style.top = "0px";
    canvasContainer.style.left = "0px";
    canvasContainer.style.width = "128px";
    canvasContainer.style.height = "128px";
    stickers[0].appendChild(canvasContainer);

    var canvasContainers = document.getElementsByClassName('canvas_container');
    var canvas = document.getElementsByClassName('str')[canvasContainers.length - 1];
    var context = canvas.getContext('2d');
    var width = canvas.width;
    var height = canvas.height;
    context.drawImage(this, 0, 0, width, height);
    dragSticker(canvasContainers[canvasContainers.length - 1]);
}



function dragSticker(sticker) {
    var pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
    sticker.onmousedown = dragMouseDown;
    sticker.onclick = function () {
        var stickers = document.querySelectorAll('.canvas_container');

        for (var i = 0; i < stickers.length; i++) {
            if (stickers[i].classList.contains('sizable')) {
                stickers[i].classList.remove('sizable');
            }
        }
        this.classList.add('sizable');
    };
    function dragMouseDown(e) {
        if (e.button === 0) {
            e = e || window.event;
            pos3 = e.clientX;
            pos4 = e.clientY;
            document.onmouseup = closeDragElement;
            document.onmousemove = elementDrag;
        }
    }

    function elementDrag(e) {
        if (e.button === 0) {
            e = e || window.event;
            pos1 = pos3 - e.clientX;
            pos2 = pos4 - e.clientY;
            pos3 = e.clientX;
            pos4 = e.clientY;
            sticker.style.top = (sticker.offsetTop - pos2) + "px";
            sticker.style.left = (sticker.offsetLeft - pos1) + "px";
        }
    }

    function closeDragElement() {
        document.onmouseup = null;
        document.onmousemove = null;
    }
}

function clearTmp() {
    var xhr = new XMLHttpRequest();
    var data = new FormData;
    data.append('remove', 'true');
    xhr.open('POST', '/camagru/camera/clearTmp?action=ajax');
    xhr.send(data);
}

function getStickers() {
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            var data = JSON.parse(xhr.response);
            var stickers = document.getElementsByClassName('stickers_side')[0];
            document.getElementsByClassName('circle')[0].style.display = 'none';
            for (var i = 0; i < data.length; i++) {
                var container = document.createElement('div');
                var sticker = document.createElement('img');

                container.className = 'sticker_container';
                sticker.className = 'stickers_img';
                sticker.src = '/camagru/' + data[i];
                container.appendChild(sticker);
                stickers.appendChild(container);

            }
            addEventsListeners();
        }
    };
    xhr.open('GET', '/camagru/camera/getStickers?action=ajax');
    xhr.send();
}

window.onload = function () {
    getStickers();
    photo();
};

document.addEventListener('DOMContentLoaded', function () {
    var toolbarHeight = document.querySelector('.camera_toolbar').offsetHeight;
    document.querySelector('.main_page_top').style.maxHeight = document.querySelector('#video').offsetWidth / 1.33 + toolbarHeight + 40  + 'px';
    document.querySelector('.preview_template').style.height = document.querySelector('.preview_template').style.width / 4 * 3 + 'px';
});

document.querySelector('.icon-attach').addEventListener('click', function () {
    document.querySelector('#photo_upload').click();
});

document.querySelector('.icon-plus').addEventListener('click', function () {
    document.querySelector('#sticker_upload').click();
});

document.querySelector("#photo_upload").onchange = function() {
    document.querySelector("#submit").click();
    if (!document.querySelector('.icon-cancel').classList.contains('toolbar_active')) {
        document.querySelector('.icon-cancel').classList.add('toolbar_active');
        document.querySelector('.icon-floppy').classList.add('toolbar_active');
        document.querySelector('.icon-attach').classList.add('toolbar_active');
    }
};

document.querySelector("#sticker_upload").onchange = function() {
    document.querySelector("#submit_sticker").click();
};

function preventDefault(e) {
    e = e || window.event;
    if (e.preventDefault)
        e.preventDefault();
    e.returnValue = false;
}

function enableScroll() {
    window.onwheel = null;
}

function disableScroll() {
    window.onwheel = preventDefault;
}

document.addEventListener('mousedown', function (button) {
    if (button.button === 1) {
        !(window.onwheel) ? disableScroll() : enableScroll();
    }
});

document.addEventListener('wheel', function (cursor) {
    var sticker = document.querySelector('.sizable');
    if (sticker) {
        var minY = sticker.getBoundingClientRect().top;
        var maxY = sticker.getBoundingClientRect().top + sticker.getBoundingClientRect().height;
        var minX = sticker.getBoundingClientRect().left;
        var maxX = sticker.getBoundingClientRect().left + sticker.getBoundingClientRect().width;

        if (cursor.clientX >= minX &&
            cursor.clientX <= maxX &&
            cursor.clientY >= minY &&
            cursor.clientY <= maxY) {
            if (cursor.deltaY > 0) {
                sticker.style.top = parseInt(sticker.style.top) - 1  + 'px';
                sticker.style.left = parseInt(sticker.style.left) - 1 + 'px';
                sticker.style.width = parseInt(sticker.style.width) + 2 + 'px';
                sticker.style.height = parseInt(sticker.style.height) + 2 + 'px';
            } else {
                sticker.style.top = parseInt(sticker.style.top) + 1  + 'px';
                sticker.style.left = parseInt(sticker.style.left) + 1 + 'px';
                sticker.style.width = parseInt(sticker.style.width) - 2 + 'px';
                sticker.style.height = parseInt(sticker.style.height) - 2 + 'px';
            }
        }
    }
});





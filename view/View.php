<?php

namespace view;

class View
{
    public function render($content, $data = array())
    {
        if (is_array($data)) {
            extract($data);
        }
        include_once RT . 'view/templates/header.html';
        include_once RT . 'view/templates/' . $content . '.html';
        include_once RT . 'view/templates/footer.html';
    }

    public function renderStartPage()
    {
        include_once RT . 'view/templates/header.html';
        include_once RT . 'view/templates/photos_collection.html';
        include_once RT . 'view/templates/footer.html';

    }
}
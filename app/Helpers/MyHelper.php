<?php

if (!function_exists('convert_price')) {
    function convert_price(string $price = '')
    {
        return str_replace('.', '', $price);
    }
}
if (!function_exists('convert_array')) {
    function convert_array($system = null, $keyword = '', $value = '')
    {
        $temp = [];
        if (is_array($system)) {
            foreach ($system as $key => $val) {
                $temp[$val[$keyword]] = $val[$value];
            }
        }
        if (is_object($system)) {
            foreach ($system as $key => $val) {
                $temp[$val->{$keyword}] = $val->{$value};
            }
        }
        return $temp;
    }
}

if (!function_exists('renderSystemInput')) {
    function renderSystemInput(string $name = '', $systems = null)
    {
        return '<input type="text" name="config[' . $name . ']" value="' . old($name, (($systems[$name]) ?? '')) . '" class="form-control" placeholder="" autocomplete="off" />';
    }
}
if (!function_exists('renderSystemImages')) {
    function renderSystemImages(string $name = '', $systems = null)
    {
        return '<input type="text" name="config[' . $name . ']" value="' . old($name, (($systems[$name]) ?? '')) . '" class="form-control upload-image" placeholder="" autocomplete="off" />';
    }
}
if (!function_exists('renderSystemTextarea')) {
    function renderSystemTextarea(string $name = '', $systems = null)
    {
        return '<textarea class="form-control system-textarea" name="config[' . $name . ']" >' . old($name, (($systems[$name]) ?? '')) . '</textarea>';
    }
}
if (!function_exists('renderSystemEditor')) {
    function renderSystemEditor(string $name = '', $systems = null)
    {
        return '<textarea id="' . $name . '" class="form-control system-textarea ck-editor" name="config[' . $name . ']" >' . old($name, (($systems[$name]) ?? '')) . '</textarea>';
    }
}
if (!function_exists('renderSystemLink')) {
    function renderSystemLink(array $item = [], $systems = null)
    {
        return (isset($item['link'])) ? '<a target="' . $item['link']['target'] . '" class="system-link" href="' . $item['link']['href'] . '">' . $item['link']['text'] . '</a>' : '';
    }
}
if (!function_exists('renderSystemTitle')) {
    function renderSystemTitle(array $item = [], $systems = null)
    {
        return (isset($item['title'])) ? '<span  class="system-link text-danger" >' . $item['title'] . '</span>' : '';
    }
}
if (!function_exists('renderSystemSelect')) {
    function renderSystemSelect(array $item = [], string $name = '', $systems = null)
    {
        $html = '<select name="config[' . $name . ']" class="form-control">';
        foreach ($item['option'] as $key => $val) {
            $html .= '<option ' . ((isset($systems[$name]) && $key == $systems[$name])  ? 'selected' : '') . ' value="' . $key . '">' . $val . '</option>';
        }
        $html .= '</select>';
        return $html;
    }
}
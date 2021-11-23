<?php

function getFilenameFromDisposition($value) {
    $value = trim($value);

    if (strpos($value, ';') === false) {
        return null;
    }

    list($type, $attr_parts) = explode(';', $value, 2);

    $attr_parts = explode(';', $attr_parts);
    $attributes = array();

    foreach ($attr_parts as $part) {
        if (strpos($part, '=') === false) {
            continue;
        }

        list($key, $value) = explode('=', $part, 2);

        $attributes[trim($key)] = trim($value);
    }

    $attrNames = ['filename*' => true, 'filename' => false];
    $filename = null;
    $isUtf8 = false;
    foreach ($attrNames as $attrName => $utf8) {
        if (!empty($attributes[$attrName])) {
            $filename = trim($attributes[$attrName]);
            $isUtf8 = $utf8;
            break;
        }
    }
    if ($filename === null) {
        return null;
    }

    if ($isUtf8 && strpos($filename, "utf-8''") === 0 && $filename = substr($filename, strlen("utf-8''"))) {
        return rawurldecode($filename);
    }
    if (substr($filename, 0, 1) === '"' && substr($filename, -1, 1) === '"') {
        $filename = substr($filename, 1, -1);
    }

    return $filename;
}
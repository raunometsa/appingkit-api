<?php

function color($name)
{
    $hash = md5($name);

    $color1 = hexdec(substr($hash, 8, 2));
    $color2 = hexdec(substr($hash, 4, 2));
    $color3 = hexdec(substr($hash, 0, 2));

    if ($color1 < 128) {
        $color1 += 128;
    }
    if ($color2 < 128) {
        $color2 += 128;
    }
    if ($color3 < 128) {
        $color3 += 128;
    }

    return '#' . dechex($color1) . dechex($color2) . dechex($color3);
}

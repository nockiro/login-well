<?php
/**
* Builds a file path with the appropriate directory separator.
* @param string $segments,... unlimited number of path segments
* @return string Path
*/
function str_insert($str, $search, $insert) {
    $index = strpos($str, $search);
    if($index === false) {
        return $str;
    }
    return substr_replace($str, $search.$insert, $index, strlen($search));
}

function startsWith($strProve, $ProveFor) {
	return 0 === strpos($strProve, $ProveFor);
}

function file_build_path(...$segments) {
    return join(DIRECTORY_SEPARATOR, $segments);
}
function esc_url($url)
{
    
    if ('' == $url) {
        return $url;
    }
    
    $url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\\x80-\\xff]|i', '', $url);
    
    $strip = array(
        '%0d',
        '%0a',
        '%0D',
        '%0A'
    );
    $url   = (string) $url;
    
    $count = 1;
    while ($count) {
        $url = str_replace($strip, '', $url, $count);
    }
    
    $url = str_replace(';//', '://', $url);
    
    $url = htmlentities($url);
    
    $url = str_replace('&amp;', '&#038;', $url);
    $url = str_replace("'", '&#039;', $url);
    
    if ($url[0] !== '/') {
        // Wir wollen nur relative Links von $_SERVER['PHP_SELF']
        return '';
    } else {
        return $url;
    }
}
?>
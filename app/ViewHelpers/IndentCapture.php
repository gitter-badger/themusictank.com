<?php

namespace App\ViewHelpers;

class IndentCapture {

    public static function on($prefix = '')
    {
        ob_start();
        echo $prefix . "\n";
    }

    public static function off($suffix = '')
    {
        $content = ob_get_contents();
        ob_end_clean();

        // If no content is printed, don't even print the wrapper.
        if (!strlen(trim($content))) {
            return "";
        }

        // Thanks https://github.com/fitztrev/laravel-html-minify/blob/master/src/Fitztrev/LaravelHtmlMinify/LaravelHtmlMinifyCompiler.php
        $replace = [
            '/<!--[^\[](.*?)[^\]]-->/s' => '',
            "/<\?php/"                  => '<?php ',
            "/\n([\S])/"                => ' $1',
            "/\r/"                      => '',
            "/\n/"                      => '',
            "/\t/"                      => ' ',
            "/ +/"                      => ' ',
        ];

        return preg_replace(
            array_keys($replace), array_values($replace), $content . "\n" . $suffix
        );
    }

}

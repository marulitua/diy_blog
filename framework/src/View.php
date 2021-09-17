<?php

namespace Framework;

/**
 * Class View
 * @author Erwin Pakpahan <erwinmaruli@live.com>
 */
class View
{
    /**
     * Set data from controller: $view->data['variable'] = 'value';
     * @var array
     */
    public $data = [];

    /**
     * @param string Path to template file.
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    function render(string $template): string
    {
        if (!is_file($template)) {
            throw new \RuntimeException('Template not found: ' . $template);
        }

        // define a closure with a scope for the variable extraction
        $result = function(string $file, array $data = array()) : string {
            ob_start();
            extract($data, EXTR_SKIP);
            if (file_exists($file)) {
                include $file;
            } else {
                ob_end_clean();
                throw new \Exception("Cannot found $file template file");
            }
            return ob_get_clean();
        };

        // call the closure
        return $result($template, $this->data);
    }
}


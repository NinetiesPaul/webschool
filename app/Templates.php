<?php

namespace App;

class Templates
{
    public function __construct($path, $args = null, $nivel = '')
    {
        $args['LINKS'] = Util::generateLinks($nivel, ($args['ON_PROFILE'] ? true : false));

        $template = $this->getTemplate($path);
        echo $this->parseTemplate($template, $args);
    }

    protected function getTemplate($template, $folder = "web/")
    {
        $arqTemp = $folder.$template; // criando var com caminho do arquivo
        $content = '';

        if (is_file($arqTemp)) { // verificando se o arq existe
            $content = file_get_contents($arqTemp);
        } // retornando conteúdo do arquivo

        return $content;
    }
    
    protected function parseTemplate($template, $array)
    {
        foreach ($array as $a => $b) {// recebemos um array com as tags
            if (strpos($a, 'list')) {
                $template = str_replace('{'.$a.'}', json_encode($b), $template);
            } else {
                $template = str_replace('{'.$a.'}', $b, $template);
            }
        }

        return $template; // retorno o html com conteúdo final
    }
}

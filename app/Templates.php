<?php

namespace App;

class Templates
{
    public function getTemplate($template, $folder = "web/")
    {
        $arqTemp = $folder.$template; // criando var com caminho do arquivo
        $content = '';

        if (is_file($arqTemp)) { // verificando se o arq existe
            $content = file_get_contents($arqTemp);
        } // retornando conteúdo do arquivo

        return $content;
    }
    
    public function parseTemplate($template, $array)
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

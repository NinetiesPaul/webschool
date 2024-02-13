<?php

namespace App;

class Templates
{
    public function __construct($path, $args = null, $nivel = '')
    {
        $args['CSS'] = $this->generateCss();
        $args['JS'] = $this->generateJs();
        $args['LINKS'] = $this->generateLinks($nivel, ($args['ON_PROFILE'] ? true : false));

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

    protected function generateLinks($nivel = '', $perfil = false)
    {
        session_start();

        $tipo = $_SESSION['tipo'];
        if (!$tipo) {
            return '';
        }

        $path = ($perfil) ? $tipo . "/" . $nivel : $nivel;

        switch ($tipo) {
            case Enum::TIPO_ADMIN:
                return "
                    <a class='btn btn-light btn-block' href='" . $this->createUrl('admin_turmas') . "'>Turmas</a>
                    <a class='btn btn-light btn-block' href='" . $this->createUrl('admin_disciplinas') . "'>Disciplinas</a>
                    <a class='btn btn-light btn-block' href='" . $this->createUrl('admin_professores') . "'>Professores</a>
                    <a class='btn btn-light btn-block' href='" . $this->createUrl('admin_alunos') . "'>Alunos</a>
                    <a class='btn btn-light btn-block' href='" . $this->createUrl('admin_responsaveis') . "'>Responsáveis</a>
                ";
                break;
            case Enum::TIPO_RESPONSAVEL:
                return "
                    <a class='btn btn-light btn btn-block' href='" . $this->createUrl('responsavel_alunos') . "'>Alunos</a>
                ";
                break;
            case Enum::TIPO_PROFESSOR:
                return "
                    <a class='btn btn-light btn btn-block' href='".$path."turmas'>Turmas</a>
                ";
                break;
            case Enum::TIPO_ALUNO:
                return "
                    <a class='btn btn-light btn btn-block' href='" . $this->createUrl('aluno_turmas') . "'>Turmas</a>
                ";
                break;
                
        }
    }

    protected function generateCss()
    {
        $rootUrl = substr(url(), 0, strlen(url()) - 1);
        $rootUrl = count(explode("/", $rootUrl)) - 2;

        $backwards = '';
        for ($i = 1; $i <= $rootUrl; $backwards .= "../", $i++);

        $cssFiles = ['css', 'glyphicons', 'navbar'];
        $cssPaths = '';
        foreach ($cssFiles as $cssFile)
        {
            $cssPaths .= "<link href='" . $backwards . "includes/css/$cssFile.css' rel='stylesheet'>";
        }
        return $cssPaths;
    }

    protected function generateJs()
    {
        $rootUrl = substr(url(), 0, strlen(url()) - 1);
        $rootUrl = count(explode("/", $rootUrl)) - 2;

        $backwards = '';
        for ($i = 1; $i <= $rootUrl; $backwards .= "../", $i++);

        $jsFiles = ['jquery', 'height'];
        $jsPaths = '';
        foreach ($jsFiles as $cssFile)
        {
            $jsPaths .= "<script src='" . $backwards . "includes/js/$cssFile.js'></script>";
        }
        return $jsPaths;
    }

    protected function createUrl($url)
    {
        return substr(url($url), 0, strlen($url) + 1);
    }
}

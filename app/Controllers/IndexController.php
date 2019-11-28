<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controllers;

/**
 * Description of IndexController
 *
 * @author Paul Richard
 */
class IndexController {
    //put your code here
    
    public function __construct() {}
    
    public function index()
    {
        $templateFinal 	= $this->getTemplate('index.html');
        
        echo $templateFinal;
    }
    
    public function list()
    {
        $arrTags = array(
            'titulo'=>'Dicas de PHP',
            'artigo_titulo'=>'Templates em PHP - Dicas de PHP',
            'artigo_conteudo'=>'Demonstração de como criar um sistema simples de templates com PHP',
            'rodape'=>'© Dicas de PHP - Todos direitos reservados 2012 ',
            'item_list'=> [
                1 => 'item 1',
                2 => 'item 2'
            ]
        );
        
        $template = self::getTemplate('listing.html');
        $templateFinal 	= self::parseTemplate( $template, $arrTags );
        
        echo $templateFinal;
    }
    
    public function form()
    {
        $arrTags = array(
            'titulo'=>'Dicas de PHP',
            'artigo_titulo'=>'Templates em PHP - Dicas de PHP',
            'artigo_conteudo'=>'Demonstração de como criar um sistema simples de templates com PHP',
            'rodape'=>'© Dicas de PHP - Todos direitos reservados 2012 '
        );
        
        $template = self::getTemplate('form.html');
        $templateFinal 	= self::parseTemplate( $template, $arrTags );
        
        echo $templateFinal;
    }
    
    public function fromForm()
    {
        $arrTags = array(
            'titulo'=>'Dicas de PHP',
            'artigo_titulo'=>'Templates em PHP - Dicas de PHP',
            'artigo_conteudo'=>'Demonstração de como criar um sistema simples de templates com PHP',
            'rodape'=>'© Dicas de PHP - Todos direitos reservados 2012 ',
            'post_data' => json_encode($_POST)
        );
        
        $template = self::getTemplate('fromform.html');
        $templateFinal 	= self::parseTemplate( $template, $arrTags );
        
        echo $templateFinal;
    }
    
    private function getTemplate( $template, $folder = "web/" ) 
    {
        $arqTemp = $folder.$template; // criando var com caminho do arquivo
        $content = '';

        if ( is_file( $arqTemp ) ) // verificando se o arq existe
            $content = file_get_contents( $arqTemp ); // retornando conteúdo do arquivo

        return $content;
    }
    
    private function parseTemplate( $template, $array ) 
    {
        foreach ($array as $a => $b) {// recebemos um array com as tags 
            if (strpos ($a, 'list')) {
                $template = str_replace( '{'.$a.'}', json_encode($b), $template );
            } else {
                $template = str_replace( '{'.$a.'}', $b, $template );
            }
        }

        return $template; // retorno o html com conteúdo final
    }
}

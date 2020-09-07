<?php

use Phinx\Migration\AbstractMigration;

class Estados extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    addCustomColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Any other destructive changes will result in an error when trying to
     * rollback the migration.
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $estados = [
            [ 'nome' => 'Acre', 'sigla' => 'AC' ],
            [ 'nome' => 'Alagoas', 'sigla' => 'AL' ],
            [ 'nome' => 'Amapa', 'sigla' => 'AP' ],
            [ 'nome' => 'Amazonas', 'sigla' => 'AM' ],
            [ 'nome' => 'Bahia', 'sigla' => 'BA' ],
            [ 'nome' => 'Ceara', 'sigla' => 'CE' ],
            [ 'nome' => 'Distrito Federal', 'sigla' => 'DF' ],
            [ 'nome' => 'Espirito Santo', 'sigla' => 'ES' ],
            [ 'nome' => 'Goias', 'sigla' => 'GO' ],
            [ 'nome' => 'Maranhão', 'sigla' => 'MA' ],
            [ 'nome' => 'Mato Grosso', 'sigla' => 'MG' ],
            [ 'nome' => 'Mato Grosso do Sul', 'sigla' => 'MS' ],
            [ 'nome' => 'Minas Gerais', 'sigla' => 'AC' ],
            [ 'nome' => 'Para', 'sigla' => 'AC' ],
            [ 'nome' => 'Paraiba', 'sigla' => 'AC' ],
            [ 'nome' => 'Parana', 'sigla' => 'AC' ],
            [ 'nome' => 'Pernambuco', 'sigla' => 'AC' ],
            [ 'nome' => 'Piaui', 'sigla' => 'AC' ],
            [ 'nome' => 'Rio de Janeiro', 'sigla' => 'AC' ],
            [ 'nome' => 'Rio Grande do Norte', 'sigla' => 'AC' ],
            [ 'nome' => 'Rio Grande do Sul', 'sigla' => 'AC' ],
            [ 'nome' => 'Rondonia', 'sigla' => 'AC' ],
            [ 'nome' => 'Roraima', 'sigla' => 'AC' ],
            [ 'nome' => 'Santa Catarina', 'sigla' => 'AC' ],
            [ 'nome' => 'São Paulo', 'sigla' => 'AC' ],
            [ 'nome' => 'Sergipe', 'sigla' => 'AC' ],
            [ 'nome' => 'Tocantins', 'sigla' => 'AC' ],
            [ 'nome' => 'Acre', 'sigla' => 'AC' ],
        ];

        $this->table('estado')->insert($estados)->save();
    }
}

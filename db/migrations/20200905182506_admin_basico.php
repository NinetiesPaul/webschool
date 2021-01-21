<?php

use Phinx\Migration\AbstractMigration;

class AdminBasico extends AbstractMigration
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
        $userRow = [
            [
                'nome' => 'admin',
                'email' => 'admin',
                'pass' => 'a150d62572c2125532bdac83ef5a0eac',
                'salt' => '1599331881'
            ]
        ];

        $adminRow = [
            [
                'usuario'  => 1
            ]
        ];

        $this->table('usuario')->insert($userRow)->save();
        $this->table('admin')->insert($adminRow)->save();
    }
}
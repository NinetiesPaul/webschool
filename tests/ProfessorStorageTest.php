<?php declare(strict_types=1);

use App\DB\Storage\ProfessorStorage;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

class ProfessorStorageTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testVerProfessoresRetornoValido(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('fetchAll')
            ->once()
            ->andReturn([]);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('query')
            ->once()
            ->andReturn($stmtMock);

        $professores = $this->createProfessorStorage($pdoMock)->verProfessores();

        $this->assertIsArray($professores);
    }

    public function testVerProfessorRetornoValido(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('fetch')
            ->once()
            ->andReturn((object) ['id' => 1, 'nome' => 'Turma A', 'ano' => 2021]);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('query')
            ->once()
            ->andReturn($stmtMock);

        $professor = $this->createProfessorStorage($pdoMock)->verProfessor(1);

        $this->assertInstanceOf(stdClass::class, $professor);
    }

    public function testAdicionarProfessorRetornoValido(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('fetch')->andReturn(false);
        $stmtMock->shouldReceive('execute')->andReturn(true);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('query')->andReturn($stmtMock);
        $pdoMock->shouldReceive('prepare')->andReturn($stmtMock);
        $pdoMock->shouldReceive('lastInsertId')->andReturn('1');

        $resultado = $this->createProfessorStorage($pdoMock)
            ->adicionarProfessor('email', 'nome', 'password', 'salt');

        $this->assertNull($resultado);
    }

    public function testAdicionarProfessorUsuarioJaExiste(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('fetch')->andReturn(true);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('query')->andReturn($stmtMock);

        $resultado = $this->createProfessorStorage($pdoMock)
            ->adicionarProfessor('email', 'nome', 'password', 'salt');

        $this->assertFalse($resultado);
    }

    public function testRemoverProfessorRetornoValido(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('execute')->andReturn(true);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('prepare')->andReturn($stmtMock);

        $resultado = $this->createProfessorStorage($pdoMock)->removerProfessor(
            1,
            1,
            1,
            ['usuario' => (object) ['nome' => 'responsavel']]
        );

        $this->assertNull($resultado);
    }

    public function testDesativarProfessorRetornoValido(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('rowCount')->andReturn(1);
        $stmtMock->shouldReceive('fetch')
            ->once()
            ->andReturn((object) ['is_deleted' => 1]);
        $stmtMock->shouldReceive('execute')->andReturn(true);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('query')
            ->once()
            ->andReturn($stmtMock);
        $pdoMock->shouldReceive('prepare')
            ->once()
            ->andReturn($stmtMock);

        $resultado = $this->createProfessorStorage($pdoMock)->desativarProfessor(1);

        $this->assertNull($resultado);
    }

    public function testDesativarProfessorRetornoExcecao(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('rowCount')->andReturn(1, 0);
        $stmtMock->shouldReceive('fetch')
            ->once()
            ->andReturn((object) ['is_deleted' => 1]);
        $stmtMock->shouldReceive('execute')->andReturn(true);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('query')
            ->once()
            ->andReturn($stmtMock);
        $pdoMock->shouldReceive('prepare')
            ->once()
            ->andReturn($stmtMock);

        $this->expectException(Exception::class);
        $this->createProfessorStorage($pdoMock)->desativarProfessor(1);
    }

    public function testDesativarProfessorRetornoUsuarioDesconhecido(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('rowCount')
            ->once()
            ->andReturn(0);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('query')
            ->once()
            ->andReturn($stmtMock);

        $this->expectException(Exception::class);
        $this->createProfessorStorage($pdoMock)->desativarProfessor(1);
    }

    public function testAdicionarMateriaPorProfessorRetornoValido(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('fetchAll')
            ->once()
            ->andReturn([
                (object) ['id' => 1],
                (object) ['id' => 2],
            ]);
        $stmtMock->shouldReceive('execute')->andReturn(true);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('query')
            ->once()
            ->andReturn($stmtMock);
        $pdoMock->shouldReceive('prepare')->andReturn($stmtMock);

        $resultado = $this->createProfessorStorage($pdoMock)
            ->adicionarMateriaPorProfessor(1, 1, 1);

        $this->assertNull($resultado);
    }

    public function testVerificarMateriaPorProfessorRetornoValido(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('fetchAll')
            ->once()
            ->andReturn([
                (object) ['id' => 1, 'nome' => 'Turma A', 'ano' => 2021],
                (object) ['id' => 2, 'nome' => 'Turma B', 'ano' => 2022],
            ]);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('query')
            ->once()
            ->andReturn($stmtMock);

        $resultado = $this->createProfessorStorage($pdoMock)
            ->verificarMateriaPorProfessor(1, 1, 'professor');

        $this->assertIsArray($resultado);
    }

    public function testRemoverProfessorPorMateriaRetornoValido(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('execute')
            ->once()
            ->andReturn(true);
        $stmtMock->shouldReceive('rowCount')
            ->once()
            ->andReturn(1);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('prepare')
            ->once()
            ->andReturn($stmtMock);

        $resultado = $this->createProfessorStorage($pdoMock)->removerProfessorPorMateria(1);

        $this->assertNull($resultado);
    }

    public function testRemoverProfessorPorMateriaExcecao(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('execute')
            ->once()
            ->andReturn(true);
        $stmtMock->shouldReceive('rowCount')
            ->once()
            ->andReturn(0);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('prepare')
            ->once()
            ->andReturn($stmtMock);

        $this->expectException(Exception::class);
        $this->createProfessorStorage($pdoMock)->removerProfessorPorMateria(1);
    }

    public function testVerProfessorPorMateriaRetornoValido(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('fetchAll')
            ->once()
            ->andReturn([]);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('query')
            ->once()
            ->andReturn($stmtMock);

        $resultado = $this->createProfessorStorage($pdoMock)->verProfessorPorMateria(1);

        $this->assertIsArray($resultado);
    }

    /**
     * @param PDO&MockInterface $pdoMock
     */
    private function createProfessorStorage($pdoMock): ProfessorStorage
    {
        return new ProfessorStorage($pdoMock);
    }
}

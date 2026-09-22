<?php declare(strict_types=1);

use App\DB\Storage\ResponsavelStorage;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

class ResponsavelStorageTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testVerResponsaveisRetornoValido(): void
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

        $resultado = $this->createResponsavelStorage($pdoMock)->verResponsaveis();

        $this->assertIsArray($resultado);
    }

    public function testVerResponsavelRetornoValido(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('fetch')
            ->once()
            ->andReturn((object) ['id' => 1, 'nome' => 'Turma A', 'ano' => 2021]);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('query')
            ->once()
            ->andReturn($stmtMock);

        $resultado = $this->createResponsavelStorage($pdoMock)->verResponsavel(1);

        $this->assertInstanceOf(stdClass::class, $resultado);
    }

    public function testAdicionarResponsavelRetornoValido(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('fetch')->andReturn(false);
        $stmtMock->shouldReceive('execute')->andReturn(true);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('query')->andReturn($stmtMock);
        $pdoMock->shouldReceive('prepare')->andReturn($stmtMock);
        $pdoMock->shouldReceive('lastInsertId')->andReturn('1');

        $resultado = $this->createResponsavelStorage($pdoMock)
            ->adicionarResponsavel('email', 'nome', 'password', 'salt');

        $this->assertNull($resultado);
    }

    public function testAdicionarResponsavelUsuarioJaExiste(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('fetch')->andReturn(true);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('query')->andReturn($stmtMock);

        $resultado = $this->createResponsavelStorage($pdoMock)
            ->adicionarResponsavel('email', 'nome', 'password', 'salt');

        $this->assertFalse($resultado);
    }

    public function testRemoverResponsavelRetornoValido(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('execute')->andReturn(true);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('prepare')->andReturn($stmtMock);

        $resultado = $this->createResponsavelStorage($pdoMock)->removerResponsavel(
            1,
            1,
            1,
            ['usuario' => (object) ['nome' => 'responsavel']]
        );

        $this->assertNull($resultado);
    }

    public function testAdicionarAlunoPorResponsavelRetornoValido(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('execute')
            ->once()
            ->andReturn(true);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('prepare')
            ->once()
            ->andReturn($stmtMock);

        $resultado = $this->createResponsavelStorage($pdoMock)
            ->adicionarAlunoPorResponsavel(1, 1);

        $this->assertNull($resultado);
    }

    public function testRemoverAlunoPorResponsavel(): void
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
        $this->createResponsavelStorage($pdoMock)->removerAlunoPorResponsavel(1);
    }

    public function testVerAlunosDoResponsavelRetornoValido(): void
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

        $turmas = $this->createResponsavelStorage($pdoMock)->verAlunosDoResponsavel(1);

        $this->assertIsArray($turmas);
    }

    public function testVerResponsaveisPeloAlunoRetornoValido(): void
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

        $resultado = $this->createResponsavelStorage($pdoMock)->verResponsaveisPeloAluno(1);

        $this->assertIsArray($resultado);
    }

    public function testDesativarResponsavelAtualizacaoFalhou(): void
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
        $this->createResponsavelStorage($pdoMock)->desativarResponsavel(1);
    }

    public function testDesativarResponsavelNaoEncontrado(): void
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
        $this->createResponsavelStorage($pdoMock)->desativarResponsavel(1);
    }

    /**
     * @param PDO&MockInterface $pdoMock
     */
    private function createResponsavelStorage($pdoMock): ResponsavelStorage
    {
        return new ResponsavelStorage($pdoMock);
    }
}

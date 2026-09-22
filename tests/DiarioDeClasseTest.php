<?php declare(strict_types=1);

use App\DB\Storage\DiarioDeClasseStorage;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

class DiarioDeClasseTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testInserirDiarioDeClasseRetornoValido(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('execute')
            ->once()
            ->andReturn(true);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('prepare')
            ->once()
            ->andReturn($stmtMock);

        $resultado = $this->createDiarioDeClasseStorage($pdoMock)->inserirDiarioDeClasse([
            'idAluno' => 1,
            'idDisciplina' => 1,
            'idTurma' => 1,
        ]);

        $this->assertNull($resultado);
    }

    public function testVerFaltasDoAlunoDaTurmaRetornoValido(): void
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

        $faltas = $this->createDiarioDeClasseStorage($pdoMock)->verFaltasDoAlunoDaTurma(1, 1);

        $this->assertIsArray($faltas);
    }

    public function testVerFaltasDoAlunoDaturmaPorDataRetornoValido(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('fetch')
            ->once()
            ->andReturn((object) []);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('query')
            ->once()
            ->andReturn($stmtMock);

        $falta = $this->createDiarioDeClasseStorage($pdoMock)
            ->verFaltasDoAlunoDaturmaPorData(1, 1, 1, 'data');

        $this->assertInstanceOf(stdClass::class, $falta);
    }

    public function testAdicionarFaltaRetornoValido(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('execute')
            ->once()
            ->andReturn(true);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('prepare')
            ->once()
            ->andReturn($stmtMock);

        $resultado = $this->createDiarioDeClasseStorage($pdoMock)
            ->adicionarFalta(1, 1, 1, 'data');

        $this->assertNull($resultado);
    }

    public function testAlterarFaltaRetornoValido(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('execute')
            ->once()
            ->andReturn(true);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('prepare')
            ->once()
            ->andReturn($stmtMock);

        $resultado = $this->createDiarioDeClasseStorage($pdoMock)->alterarFalta(true, 1);

        $this->assertNull($resultado);
    }

    public function testVerComentariosDoAlunoDaTurmaRetornoValido(): void
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

        $comentarios = $this->createDiarioDeClasseStorage($pdoMock)
            ->verComentariosDoAlunoDaTurma(1, 1, 1, 'data', 1);

        $this->assertIsArray($comentarios);
    }

    public function testAdicionarComentarioRetornoValido(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('execute')
            ->once()
            ->andReturn(true);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('prepare')
            ->once()
            ->andReturn($stmtMock);
        $pdoMock->shouldReceive('lastInsertId')
            ->once()
            ->andReturn('1');

        $result = $this->createDiarioDeClasseStorage($pdoMock)
            ->adicionarComentario(1, 1, 1, 'mensagem', 'data', 1);

        $this->assertEquals('1', $result);
    }

    public function testRemoverComentarioRetornoValido(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('execute')
            ->once()
            ->andReturn(true);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('prepare')
            ->once()
            ->andReturn($stmtMock);

        $resultado = $this->createDiarioDeClasseStorage($pdoMock)->removerComentario(1);

        $this->assertNull($resultado);
    }

    public function testVerDiarioDeClassePorProfessorRetornoValido(): void
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

        $resultado = $this->createDiarioDeClasseStorage($pdoMock)
            ->verDiarioDeClassePorProfessor(1);

        $this->assertIsArray($resultado);
    }

    public function testVerDiarioDeClassePorAlunoRetornoValido(): void
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

        $resultado = $this->createDiarioDeClasseStorage($pdoMock)
            ->verDiarioDeClassePorAluno(1);

        $this->assertIsArray($resultado);
    }

    public function testVerFaltasPorAlunoDaMateriaETurmaRetornoValido(): void
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

        $resultado = $this->createDiarioDeClasseStorage($pdoMock)
            ->verFaltasPorAlunoDaMateriaETurma(1, 1, 1);

        $this->assertIsArray($resultado);
    }

    public function testVerComentariosPorAlunoDaMateriaETurmaRetornoValido(): void
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

        $resultado = $this->createDiarioDeClasseStorage($pdoMock)
            ->verComentariosPorAlunoDaMateriaETurma(1, 1, 1);

        $this->assertIsArray($resultado);
    }

    /**
     * @param PDO&MockInterface $pdoMock
     */
    private function createDiarioDeClasseStorage($pdoMock): DiarioDeClasseStorage
    {
        return new DiarioDeClasseStorage($pdoMock);
    }
}

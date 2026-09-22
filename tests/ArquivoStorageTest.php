<?php declare(strict_types=1);

use App\DB\Storage\ArquivoStorage;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

class ArquivoStorageTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testAdicionarArquivoRetornoValido(): void
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

        $result = $this->createArquivoStorage($pdoMock)
            ->adicionarArquivo('file_name', 'urlThumbFinal', 'urlFinal', 'dataComentario', 1);

        $this->assertEquals('1', $result);
    }

    public function testVerArquivoDoDiarioRetornoValido(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('fetch')
            ->once()
            ->andReturn((object) []);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('query')
            ->once()
            ->andReturn($stmtMock);

        $arquivo = $this->createArquivoStorage($pdoMock)->verArquivoDoDiario(1);

        $this->assertInstanceOf(stdClass::class, $arquivo);
    }

    public function testRemoverArquivoDoComentarioRetornoValido(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('execute')
            ->once()
            ->andReturn(true);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('prepare')
            ->once()
            ->andReturn($stmtMock);

        $resultado = $this->createArquivoStorage($pdoMock)->removerArquivoDoComentario(1);

        $this->assertNull($resultado);
    }

    public function testVerArquivoPorIdRetornoValido(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('fetch')
            ->once()
            ->andReturn((object) []);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('query')
            ->once()
            ->andReturn($stmtMock);

        $arquivo = $this->createArquivoStorage($pdoMock)->verArquivoPorId(1);

        $this->assertInstanceOf(stdClass::class, $arquivo);
    }

    public function testVerArquivoPorAlunoRetornoValido(): void
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

        $arquivos = $this->createArquivoStorage($pdoMock)->verArquivoPorAluno(1);

        $this->assertIsArray($arquivos);
    }

    public function testVerArquivoPorProfessorRetornoValido(): void
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

        $arquivos = $this->createArquivoStorage($pdoMock)->verArquivoPorProfessor(1);

        $this->assertIsArray($arquivos);
    }

    /**
     * @param PDO&MockInterface $pdoMock
     */
    private function createArquivoStorage($pdoMock): ArquivoStorage
    {
        return new ArquivoStorage($pdoMock);
    }
}

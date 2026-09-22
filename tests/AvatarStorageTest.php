<?php declare(strict_types=1);

use App\DB\Storage\AvatarStorage;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

class AvatarStorageTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testInserirUsuarioNaAvatarRetornoValido(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('execute')
            ->once()
            ->andReturn(true);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('prepare')
            ->once()
            ->andReturn($stmtMock);

        $resultado = $this->createAvatarStorage($pdoMock)->inserirUsuarioNaAvatar(1);

        $this->assertNull($resultado);
    }

    public function testAtualizarAvatarRetornoValido(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('fetch')
            ->once()
            ->andReturn(false);
        $stmtMock->shouldReceive('execute')
            ->once()
            ->andReturn(true);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('query')
            ->once()
            ->andReturn($stmtMock);
        $pdoMock->shouldReceive('prepare')
            ->once()
            ->andReturn($stmtMock);

        $resultado = $this->createAvatarStorage($pdoMock)
            ->atualizarAvatar('urlFinal', 'urlThumbFinal', 1);

        $this->assertNull($resultado);
    }

    public function testAtualizarAvatarExcecao(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('fetch')
            ->once()
            ->andReturn((object) [
                'endereco_thumb' => 'endereco_thumb_mock',
                'endereco' => 'endereco_mock',
            ]);
        $stmtMock->shouldReceive('execute')
            ->twice()
            ->andReturn(true);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('query')
            ->once()
            ->andReturn($stmtMock);
        $pdoMock->shouldReceive('prepare')
            ->twice()
            ->andReturn($stmtMock);

        $resultado = $this->createAvatarStorage($pdoMock)
            ->atualizarAvatar('endereco_mock', 'endereco_thumb_mock', 1);

        $this->assertNull($resultado);
    }

    public function testVerAvatarRetornoValido(): void
    {
        $stmtMock = Mockery::mock(PDOStatement::class);
        $stmtMock->shouldReceive('fetch')
            ->once()
            ->andReturn((object) ['id' => 1, 'nome' => 'Turma A', 'ano' => 2021]);

        $pdoMock = Mockery::mock(PDO::class);
        $pdoMock->shouldReceive('query')
            ->once()
            ->andReturn($stmtMock);

        $avatar = $this->createAvatarStorage($pdoMock)->verAvatar(1);

        $this->assertInstanceOf(stdClass::class, $avatar);
    }

    /**
     * @param PDO&MockInterface $pdoMock
     */
    private function createAvatarStorage($pdoMock): AvatarStorage
    {
        return new AvatarStorage($pdoMock);
    }
}

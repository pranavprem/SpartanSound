<?php

declare(strict_types=1);

namespace SpartanSound;

final class SpartanServer
{
    private mixed $process = null;

    public function __construct(private string $host, private int $port, private string $root)
    {
    }

    public function start(): void
    {
        $command = [PHP_BINARY, '-S', $this->host . ':' . $this->port, 'router.php'];
        $log = fopen($this->root . '/tests/server.log', 'wb');
        if ($log === false) {
            throw new RuntimeException('Could not open the server log.');
        }
        $pipes = [];
        $process = proc_open(
            $command,
            [1 => ['file', '/dev/null', 'w'], 2 => $log],
            $pipes,
            $this->root
        );
        unset($pipes);
        if (!is_resource($process)) {
            throw new RuntimeException('Could not start the PHP server.');
        }
        $this->process = $process;
        $ready = false;
        $errno = 0;
        $errstr = '';
        for ($attempt = 0; $attempt < 50; $attempt++) {
            $socket = @fsockopen($this->host, $this->port, $errno, $errstr, 0.2);
            if (is_resource($socket)) {
                fclose($socket);
                $ready = true;
                break;
            }
            usleep(100000);
        }
        if (!$ready) {
            $this->stop();
            throw new RuntimeException('PHP server did not start (' . $errno . ' ' . $errstr . ').');
        }
    }

    public function stop(): void
    {
        if (is_resource($this->process) || is_object($this->process)) {
            proc_terminate($this->process);
            proc_close($this->process);
            $this->process = null;
        }
    }

    public function baseUrl(): string
    {
        return 'http://' . $this->host . ':' . $this->port;
    }
}

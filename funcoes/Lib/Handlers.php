<?php

namespace Funcoes\Lib;

use Funcoes\Helpers\HTML;
use Funcoes\Layout\Layout as L;
use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Formatter\LineFormatter;

class Handlers
{
    private Logger $logger;

    // Ler o config, montar as informações
    public function __construct()
    {
        global $config;

        $this->setErrorHandler();
        $this->setExceptionHandler();

        if (!empty($config->get('err.errors', null)) && !empty($config->get('err.log_folder', null))) {
            $formatter = new LineFormatter(
                null,
                null,
                true,
                true
            );
            $logHandler = new RotatingFileHandler(__DIR__ . '/../..' . $config->get('err.log_folder') . 'err.log', 7);
            $logHandler->setFormatter($formatter);
            $this->logger = new Logger('err');
            $this->logger->pushHandler($logHandler);
        }
    }

    private function setExceptionHandler()
    {
        global $config;
        $cfg = $config->get('err', null);
        set_exception_handler(function (\Throwable $exception) use ($cfg) {
            $trace = $exception->getTraceAsString();
            $this->log($exception->getCode(), $exception->getMessage(), $exception->getFile(), $exception->getLine(), $trace);
            //if (!empty($cfg['emails'])) {
            //    $this->enviarEmail($exception->getCode(), $exception->getMessage(), $exception->getFile(), $exception->getLine(), $trace, $cfg);
            //}

            $trace = str_replace('#', '<br>#', $trace);
            $this->error_page($exception->getCode(), $exception->getMessage(), $exception->getFile(), $exception->getLine(), $trace, $cfg);
        });
    }

    private function setErrorHandler()
    {
        global $config;
        $cfg = $config->get('err', null);
        if (!empty($cfg)) {
            error_reporting($cfg['errors'] ?? 0);
            set_error_handler(function ($errno, $errstr, $errfile, $errline) use ($cfg) {
                $err_reporting = $cfg['errors'] ?? 0;
                if (!($errno & $err_reporting)) {
                    return;
                }

                $this->log($errno, $errstr, $errfile, $errline, $this->stackTrace(""));

                //if (!empty($cfg['emails'])) {
                //    $this->enviarEmail($errno, $errstr, $errfile, $errline, $this->stackTrace(""), $cfg);
                //}

                $this->error_page($errno, $errstr, $errfile, $errline, $this->stackTrace(), $cfg);
            });
        }
    }

    private function error_page($errno, $errstr, $errfile, $errline, $trace, $cfg)
    {
        global $response;

        $detalhes = '';
        if ($cfg['details'] ?? false) {
            $detalhes = "<div class=\"mt-5\"><b>{$this->err($errno)}:</b> $errfile:$errline - $errstr <br> $trace</div>";
        }

        $voltar = L::backButton('Voltar', 'fas fa-angle-left', 'link');

        $mensagem = <<<HTML
            <h4><i class="fas fa-exclamation-triangle fa-lg text-danger mr-2"></i>Ocorreu um erro ao executar a última ação.</h4>
            <p><b>A equipe de desenvolvimento já foi notificada a respeito do problema.</b></p>
            <p class="mt-2">{$voltar}</p>
            $detalhes
        HTML;

        $response->error($mensagem, ['title' => 'Erro inesperado']);
        exit;
    }

    public function stackTrace($lineBreak = '<br>')
    {
        ob_start();
        debug_print_backtrace();
        $trace = ob_get_contents();
        ob_end_clean();
        $trace = str_replace('#', "$lineBreak#", $trace);
        return $trace;
    }

    private function err($errno)
    {
        $err = array(
            "0" => "EXCEPTION",
            "1" => "E_ERROR",
            "2" => "E_WARNING",
            "4" => "E_PARSE",
            "8" => "E_NOTICE",
            "16" => "E_CORE_ERROR",
            "32" => "E_CORE_WARNING",
            "64" => "E_COMPILE_ERROR",
            "128" => "E_COMPILE_WARNING",
            "256" => "E_USER_ERROR",
            "512" => "E_USER_WARNING",
            "1024" => "E_USER_NOTICE",
            "2048" => "E_STRICT",
            "4096" => "E_RECOVERABLE_ERROR",
            "8192" => "E_DEPRECATED",
            "16384" => "E_USER_DEPRECATED",
            "32767" => "E_ALL"
        );
        return $err[$errno] ?? $errno;
    }

    private function log($errno, $errstr, $errfile, $errline, $trace)
    {
        global $session;
        $usuario = $session->get('credentials.default', 'Não logado');
        $detalhes = "Usuário: $usuario\n{$this->err($errno)}: $errfile:$errline - $errstr \n $trace";
        $this->logger->error($detalhes);
    }
}

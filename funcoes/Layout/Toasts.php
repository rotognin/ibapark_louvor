<?php

namespace Funcoes\Layout;

class Toasts
{
    public static function json()
    {
        global $session;
        global $config;

        $dark_mode = $config->get('app.dark_mode', false);
        $background = ($dark_mode) ? 'gray' : 'white';

        $toasts = [];
        $flashMessages = $session->get('flash', []);
        foreach ($flashMessages as $type => $msgs) {
            if ($type == 'previous') {
                continue;
            }
            foreach ($msgs as $msg) {
                $toasts[] = [
                    'icon' => $type,
                    'title' => $msg,
                    'background' => $background
                ];
            }
        }
        return json_encode($toasts);
    }
}

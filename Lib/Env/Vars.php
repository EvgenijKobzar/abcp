<?php

namespace Lib\Env;

class Vars
{
    static public function getVar(string $name): array
    {
        $evn['handler'] = [
            'secret_key' => 'DnNLUCm4JX5gB7tQ5LcMb2tp4qermAF7pPVwYya+iNY='
        ];
        return $evn[$name];
    }
}
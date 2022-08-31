<?php
namespace Tuezy\Routing\Contract;

interface RouterManagerInterface{
    public function match(string $uri);
}
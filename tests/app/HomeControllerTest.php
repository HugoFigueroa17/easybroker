<?php
use PHPUnit\Framework\TestCase;



class HomeControllerTest extends TestCase
{
    public function testOne()
    {


        helper('funciones');

        $output = fotoPerfil(0);

        $this->assertSame($output,base_url("assets/img/avatar.jpg"));
        //$this->assertSame('2204', '2204');
    }

    public function testTwo(){

        $output = $this->get('GET', '/');
        $this->assertStringContainsString('<h1>Home</h1>', $output);
    }

}
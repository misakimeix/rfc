<?php 

/**
 * Created on Mar 5, 2020
 * @author Alexandre Bezerra Barbosa
 * @email alxbbarbosa@yahoo.com.br
 * @author Gabriel Assuero
 * @email gabrielassuerors@gmail.com
 * Version 1.0 final
 */

define('conf', '../config/configdb.ini');


final class Connection{


    private static $connection;

    private function __construct(){
        //Don't use __construct for database, sincleiton is fine xD
    }   
 
 
    private static function load(string $arquivo = conf): array{
 
        if(file_exists($arquivo)) {
            $dados = parse_ini_file($arquivo);
        } else {
            throw new Exception('ERROR: ACH_NOT_FOUND');
        }
        return $dados;
    }

    private static function make(array $dados): PDO{

        $sgdb     = isset($dados['sgdb']) ? $dados['sgdb'] : NULL;
        $usuario  = isset($dados['usuario']) ? $dados['usuario'] : NULL;
        $senha    = isset($dados['senha']) ? $dados['senha'] : NULL;
        $banco    = isset($dados['banco']) ? $dados['banco'] : NULL;
        $servidor = isset($dados['servidor']) ? $dados['servidor'] : NULL;
        $porta    = isset($dados['porta']) ? $dados['porta'] : NULL;
     
        if(!is_null($sgdb)) {

            switch (strtoupper($sgdb)) {
                case 'MYSQL' : 
                    
                    $porta = isset($porta) ? $porta : 3306 ; 
                    return new PDO("mysql:host={$servidor};port={$porta};dbname={$banco}", $usuario, $senha);
                    
                    break;

                case 'MSSQL' : 
                    
                    $porta = isset($porta) ? $porta : 1433 ;
                    return new PDO("mssql:host={$servidor},{$porta};dbname={$banco}", $usuario, $senha);
                    
                    break;

                case 'PGSQL' : 
                    
                    $porta = isset($porta) ? $porta : 5432 ;
                    return new PDO("pgsql:dbname={$banco}; user={$usuario}; password={$senha}, host={$servidor};port={$porta}");
                    
                    break;

                case 'SQLITE' : 
                    return new PDO("sqlite:{$banco}");
                    break;

                case 'OCI8' : 
                    return new PDO("oci:dbname={$banco}", $usuario, $senha);
                    break;

                case 'FIREBIRD' : 
                    return new PDO("firebird:dbname={$banco}",$usuario, $senha);
                    break;

                /*
                *
                * add mongo suport
                *
                */


            }

        } else {

            throw new Exception('ERROR: CNT_NOT_FOUND');
        }

    }

    public static function getInstance(string $arquivo = conf): PDO{

        if(self::$connection == NULL) {
           self::$connection = self::make(self::load($arquivo));
           self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
           self::$connection->exec("set names utf8");
        }
        return self::$connection;

    }

    private function __clone(){}
 
    private function __wakeup(){}

}
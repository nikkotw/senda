<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use  App\Test;

class UserTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'User:Test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Probando Items';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try{
        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', 'http://192.168.0.12:8000/api/testing', [
            // array de datos del formulario

            'headers' => ['Content-Type' => 'application/json'],
            'body' => json_encode([
                'user' => 'Pablo',
                'pass' => 'secret', 
            ]),
        ]);

        if($response->getStatusCode()===500){
            $data="Hubo un error al procesar la pagina";
            $nuevo = new Test;
            $nuevo->usuario = "Error";
            $nuevo->pass = "error1";
            $nuevo->save();
        }else{
            $data= json_decode($response->getBody()->getContents());
            $nuevo = new Test;
            $nuevo->usuario = $data->usuario;
            $nuevo->pass = $data->pass;
            $nuevo->save();
            }
          echo "Tarea exitosa";   
        }
        catch(Exception $e){
            echo "hubo un error" . $e;
        }
    }
}

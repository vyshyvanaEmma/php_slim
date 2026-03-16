<?php
use Slim\Factory\AppFactory;

require __DIR__ . '/vendor/autoload.php';       // require è come import, prende il codice del file richiesto cartella vendor la crea composser
require __DIR__ . '/controllers/AlunniController.php';
require __DIR__ . '/controllers/CertificazioniController.php';
use Psr\Http\Message\ResponseInterface as Response;     // importa interfaccia PSR-7 per la risposta http come Response 
use Psr\Http\Message\ServerRequestInterface as Request; // importa interfaccia PSR-7 per la richiesta http come Request 

$app = AppFactory::create(); // avvia l’app Slim e prepara la struttura per gestire richieste HTTP




    // Definisce una rotta GET con parametro dinamico {name}.
    // Quando arriva una richiesta a /hello/qualcosa (name),
    // Slim esegue questa funzione (callback) passando:
    // - $request  -> oggetto richiesta HTTP
    // - $response -> oggetto risposta HTTP
    // - $args     -> array con i parametri della rotta
$app->get('/hello/{name}/{surname}', function (Request $request, Response $response, array $args) {       // si vuole rispondere alla rotta get con /hello/{name}, slim va a invocare una funzione che prende la richiesta e prepara una risposta
    $name = $args['name'];   // recupera il valore del parametro {name} dall'array $args che regala slim 
    $surname = $args['surname'];
    $response->getBody()->write("Hello, $name $surname");    // preparo una risposta e conm GetBody write scrive nella risposta Hello nella varibalie name

    return $response; // returna alla risposta
});

$app->get('/cicio', function (Request $request, Response $response, array $args){

    $response->getBody()->write("Hello cicio");

    return $response;
});

// I controller servono a gestire le richieste dell’utente, eseguire la logica
// dell’applicazione (eventualmente interagendo con modelli o servizi) e restituire la risposta appropriata
// controller - AlunniController
// controller serve per fare le fuznioni - function (Request $request, Response $response, array $args) { ...    
$app->get('/alunni', "AlunniController:index"); // chiama funzione index dell AlunniController.php che ho importato all'inizio,
                                                // che svolge le stesse funzionalitta di sopra ma solo che è in una funzione in un file controller

$app->get('/alunni/{id}', "AlunniController:show");

$app->post('/alunni', "AlunniController:create");

$app->put('/alunni/{id}', "AlunniController:update");

$app->delete('/alunni/{id}', "AlunniController:destroy");

// certificazioni 
//faccio /alunni/id/certificazioni per gestire il collegamento 1:N
$app->get('/alunni/{idAlunno}/certificazioni', "CertificazioniController:index");

$app->get('/alunni/{idAlunno}/certificazioni/{idCert}', "CertificazioniController:show");

$app->post('/alunni/{idAlunno}/certificazioni', "CertificazioniController:create");

// da fare
$app->put('/alunni/{idAlunno}/certificazioni/{idCert}', "CertificazioniController:update");

$app->delete('/alunni/{idAlunno}/certificazioni/{idCert}', "CertificazioniController:destroy");

$app->run();

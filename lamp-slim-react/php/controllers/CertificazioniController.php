<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CertificazioniController
{
  public function index(Request $request, Response $response, $args){
    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');
    if ($mysqli_connection->connect_error) {
      $response->getBody()->write(json_encode(['error' => 'Errore di connessione al database']));
      return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
    $idAlunno = $args['idAlunno'];
    $result = $mysqli_connection->query("SELECT * FROM certificazioni c JOIN alunni a ON c.alunno_id = a.id WHERE a.id = $idAlunno");
    $results = $result->fetch_all(MYSQLI_ASSOC);

    $response->getBody()->write(json_encode($results)); 
    return $response->withHeader("Content-type", "application/json")->withStatus(200); 
  }

  public function show(Request $request, Response $response, $args){
   $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');
    if ($mysqli_connection->connect_error) {
      $response->getBody()->write(json_encode(['error' => 'Errore di connessione al database']));
      return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
    $idAlunno = $args['idAlunno'];
    $idCert = $args['idCert'];
    $result = $mysqli_connection->query("SELECT * FROM certificazioni c JOIN alunni a ON c.alunno_id = a.id WHERE a.id = $idAlunno AND c.id = $idCert");
    $results = $result->fetch_all(MYSQLI_ASSOC);

    if ($result->num_rows == 0) {
      $response->getBody()->write(json_encode(['error'=>'Certificazione non trovata']));
      return $response->withHeader('Content-Type','application/json')->withStatus(404);
    }

    $response->getBody()->write(json_encode($results));
    return $response->withHeader("Content-type", "application/json")->withStatus(200); 
  }

  public function create(Request $request, Response $response, $args){

  }

  public function update(Request $request, Response $response, $args){

  }

  public function destroy(Request $request, Response $response, $args){

  }
}
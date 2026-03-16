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
    $data = json_decode($request->getBody(), true);

    // controllo se i campi sono riempiti
    if (empty($data['titolo']) || empty($data['votazione']) || empty($data['ente'])) {
      $response->getBody()->write(json_encode([
        'error' => 'titolo, votazione e ente sono obbligatori'
      ]));
      return $response->withHeader('Content-Type','application/json')->withStatus(400);
    }

    $alunno_id = $args['idAlunno'];
    $titolo = $data['titolo']; 
    $votazione = $data['votazione'];
    $ente = $data['ente'];

    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');
    if ($mysqli_connection->connect_error) {
      $response->getBody()->write(json_encode(['error' => 'errore di connessione al database']));
      return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }

    $result = $mysqli_connection->query("INSERT INTO certificazioni (alunno_id, titolo, votazione, ente) VALUES($alunno_id, '$titolo', $votazione, '$ente')");

    if ($result) {
      $response->getBody()->write(json_encode([
        'id' => $mysqli_connection->insert_id,
        'alunno_id' => $alunno_id,
        'titolo' => $titolo,
        'votazione' => $votazione,
        'ente' => $ente,
      ]));
      return $response->withHeader("Content-type", "application/json")->withStatus(201); 
    } else {
      $response->getBody()->write(json_encode(['error' => 'errore di insert']));
      return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
  }

  public function update(Request $request, Response $response, $args){

  }

  public function destroy(Request $request, Response $response, $args){

  }
}
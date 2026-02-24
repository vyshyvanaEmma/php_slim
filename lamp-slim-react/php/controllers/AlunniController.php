<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AlunniController
{
  public function index(Request $request, Response $response, $args){
    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');
    if ($mysqli_connection->connect_error) {
      $response->getBody()->write(json_encode(['error' => 'Errore di connessione al database']));
      return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }

    $result = $mysqli_connection->query("SELECT * FROM alunni");
    $results = $result->fetch_all(MYSQLI_ASSOC);

    $response->getBody()->write(json_encode($results)); // converte l'array php in formato json e lo scrive nel body http
    return $response->withHeader("Content-type", "application/json")->withStatus(200); // restituisce la risposta con content-type (application/json) e imposta il codice 200 (richiesta andata a buon fine)
  }

  public function show(Request $request, Response $response, $args){
    $id = $args['id'];
    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');
    if ($mysqli_connection->connect_error) {
      $response->getBody()->write(json_encode(['error' => 'Errore di connessione al database']));
      return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }

    $result = $mysqli_connection->query("SELECT * FROM alunni WHERE id = $id");
    if ($result->num_rows == 0) {
      $response->getBody()->write(json_encode(['error' => 'Alunno non trovato']));
      return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
    }

    $alunno = $result->fetch_assoc();
    $response->getBody()->write(json_encode($alunno)); 
    return $response->withHeader("Content-type", "application/json")->withStatus(200); 
  }

  public function create(Request $request, Response $response, $args){
    $data = json_decode($request->getBody(), true);

    // controllo se i campi sono riempiti
    if (empty($data['nome']) || empty($data['cognome'])) {
      $response->getBody()->write(json_encode(['error' => 'nome e cognome sono da inserire']));
      return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

    $nome = $data['nome'];
    $cognome = $data['cognome'];

    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');
    if ($mysqli_connection->connect_error) {
      $response->getBody()->write(json_encode(['error' => 'errore di connessione al database']));
      return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }

    $result = $mysqli_connection->query("INSERT INTO alunni (nome, cognome) VALUES('$nome', '$cognome')");

    if ($result) {
      $response->getBody()->write(json_encode([
        'nome' => $nome,
        'cognome' => $cognome,
        'id' => $mysqli_connection->insert_id
      ]));
      return $response->withHeader("Content-type", "application/json")->withStatus(201); 
    } else {
      $response->getBody()->write(json_encode(['error' => 'errore di insert']));
      return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
  }

  public function update(Request $request, Response $response, $args){
    $id = $args['id'];
    $data = json_decode($request->getBody(), true);

    // controllo dei campi inseriti
    if (empty($data['nome']) || empty($data['cognome'])) {
      $response->getBody()->write(json_encode(['error' => 'inserisci nome e cognome']));
      return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

    $nome = $data['nome'];
    $cognome = $data['cognome'];

    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');
    if ($mysqli_connection->connect_error) {
      $response->getBody()->write(json_encode(['error' => 'errore di connessione al database']));
      return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }

    $result = $mysqli_connection->query("SELECT * FROM alunni WHERE id = $id");
    if ($result->num_rows == 0) {
      $response->getBody()->write(json_encode(['error' => 'Alunno non trovato']));
      return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
    }

    $update_result = $mysqli_connection->query("UPDATE alunni SET nome = '$nome', cognome = '$cognome' WHERE id = $id");

    if ($update_result) {
      $response->getBody()->write(json_encode([
        'nome' => $nome,
        'cognome' => $cognome,
        'id' => $id
      ])); 
      return $response->withHeader("Content-type", "application/json")->withStatus(200); 
    } else {
      $response->getBody()->write(json_encode(['error' => 'errore nel update']));
      return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
  }

  public function destroy(Request $request, Response $response, $args){
    $id = $args['id'];
    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');
    if ($mysqli_connection->connect_error) {
      $response->getBody()->write(json_encode(['error' => 'errore di connessione al database']));
      return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }

    // control se alunno è presente nella tabbella
    $result = $mysqli_connection->query("SELECT * FROM alunni WHERE id = $id");
    if ($result->num_rows == 0) {
      $response->getBody()->write(json_encode(['error' => 'Alunno non trovato']));
      return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
    }

    $delete_result = $mysqli_connection->query("DELETE FROM alunni WHERE id = $id");

    if ($delete_result) {
      $response->getBody()->write(json_encode(['message' => 'Alunno eliminato'])); 
      return $response->withHeader('Content-Type', 'application/json')->withStatus(200); 
    } else {
      $response->getBody()->write(json_encode(['error' => 'Errore di delete']));
      return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
  }
}
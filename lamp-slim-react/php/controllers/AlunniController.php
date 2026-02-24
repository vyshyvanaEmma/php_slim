<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AlunniController
{
  public function index(Request $request, Response $response, $args){
    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');
    $result = $mysqli_connection->query("SELECT * FROM alunni");
    $results = $result->fetch_all(MYSQLI_ASSOC);

    $response->getBody()->write(json_encode($results)); // converte l'array php in formato json e lo scrive nel body http
    return $response->withHeader("Content-type", "application/json")->withStatus(200); // restituisce la risposta con content-type (application/json) e imposta il codice 200 (richiesta andata a buon fine)
  }

  public function show(Request $request, Response $response, $args){
    $id = $args['id'];
    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');
    $result = $mysqli_connection->query("SELECT * FROM alunni WHERE id = $id");
    $alunno = $result->fetch_assoc();

    $response->getBody()->write(json_encode($alunno)); 
    return $response->withHeader("Content-type", "application/json")->withStatus(200); 

  }

  public function create(Request $request, Response $response, $args){

    $data = json_decode($request->getBody(), true);

    $nome = $data['nome'];
    $cognome = $data['cognome'];

    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');
    $result = $mysqli_connection->query("INSERT INTO alunni (nome, cognome) VALUES('$nome', '$cognome')");

    $response->getBody()->write(json_encode([
      'nome' => $nome,
      'cognome' => $cognome,
      'id' => $mysqli_connection->insert_id
    ])); 

    return $response->withHeader("Content-type", "application/json")->withStatus(201); 
  }

  public function update(Request $request, Response $response, $args){
    $id = $args['id'];
    $data = json_decode($request->getBody(), true);

    $nome = $data['nome'];
    $cognome = $data['cognome'];

    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');
    $result = $mysqli_connection->query("UPDATE alunni SET nome = '$nome', cognome = '$cognome 'WHERE id = $id");

    $response->getBody()->write(json_encode([
      'nome' => $nome,
      'cognome' => $cognome,
      'id' => $id
    ])); 
    return $response->withHeader("Content-type", "application/json")->withStatus(200); 
  }

  public function destroy(Request $request, Response $response, $args){
    $id = $args['id'];
    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');
    $result = $mysqli_connection->query("DELETE FROM alunni WHERE id = $id");


    if ($result) {
      
      $response->getBody()->write(json_encode(['message' => 'Alunno eliminato'])); 
      return $response->withHeader('Content-Type', 'application/json')->withStatus(200); 
    } else {
        
        $response->getBody()->write(json_encode(['error' => 'Errore di eliminazione']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
  }
}

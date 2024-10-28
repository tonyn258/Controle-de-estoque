
<?php
require_once 'connect.php';

class Produto extends Connect
{
  function index()
  {
    // Verifica se a conexão com o banco de dados está funcionando
    if (!$this->SQL) {
    die("Conexão com o banco de dados falhou: " . mysqli_connect_error());
    }
    $this->query = "SELECT * FROM `produto` ";
    $this->result = mysqli_query($this->SQL, $this->query) or die(mysqli_error($this->SQL));
    $row = array();
    while ($r = mysqli_fetch_assoc($this->result)) {
      $row[] = $r;
    }

    // Verifica se houve algum resultado
    if (count($row) > 0) {
      return json_encode($row);
    } else {
      return json_encode(array("message" => "Nenhum resultado encontrado."));
    }
  }
  //fim -- index

  // Inicio Insert Cliente

  function insertProduto($skuProduto,$model,$NomeProduto, $Quantidade, $Conexao, $Marca, $idUsuario,){              
    $skuProduto   = mysqli_real_escape_string($this->SQL, $skuProduto);
    $model        = mysqli_real_escape_string($this->SQL, $model);
    $NomeProduto  = mysqli_real_escape_string($this->SQL, $NomeProduto);
    $Quantidade   = mysqli_real_escape_string($this->SQL, $Quantidade);
    $Conexao      = mysqli_real_escape_string($this->SQL, $Conexao);
    $Marca        = mysqli_real_escape_string($this->SQL, $Marca);
    $idUsuario    = mysqli_real_escape_string($this->SQL, $idUsuario);


    $query = "INSERT INTO `produto`(`idProduto`, `skuProduto`, `model`,`NomeProduto`, `Quantidade`, `Conexao`, `Marca`, `statusProduto`) 
    VALUES (NULL,'$skuProduto','$model','$NomeProduto','$Quantidade','$Conexao','$Marca','$idUsuario')";
    $result = mysqli_query($this->SQL, $query) or die(mysqli_error($this->SQL));

    if ($result) {
      return 1;
    } else {
      return 0;
    }              
    mysqli_close($this->SQL);              
  }
  // Fim Insert Produto 
  public function EditProduto($idProduto){

    $this->query = "SELECT * FROM `produto` WHERE `idProduto` = '$idProduto'";
    if($this->result = mysqli_query($this->SQL, $this->query) or die(mysqli_error($this->SQL))){
  
      if($row = mysqli_fetch_array($this->result)){
  
        $skuProduto  = $row ['skuProduto'];
        $model       = $row ['model'];
        $NomeProduto = $row ['NomeProduto'];
        $Quantidade = $row ['Quantidade'];
        $Conexao = $row ['Conexao'];
        

        // Declare a variável $array fora do bloco condicional
        $array = array('produto' => [
          'SKU'             => $skuProduto,
          'Modelo'          => $model,
          'Nome do Produto' => $NomeProduto,
          'Quantidade'        => $Quantidade,
          'Conexao'           => $Conexao,
         
        ]);

        return $array; // feche a chave da função
      }
    }
    return 0; // retorne um valor padrão para o caso em que a query não é executada
  }
      
  //Inicio update Produto
  function updateProduto($idProduto, $skuProduto,$model,$NomeProduto, $Quantidade, $Conexao, $Marca, $perm)
  {
    if ($perm == 1) {                
      $skuProduto  = mysqli_real_escape_string($this->SQL, $skuProduto);
      $model       = mysqli_real_escape_string($this->SQL, $model);
      $NomeProduto = mysqli_real_escape_string($this->SQL, $NomeProduto);
      $Quantidade  = mysqli_real_escape_string($this->SQL, $Quantidade);
      $Conexao     = mysqli_real_escape_string($this->SQL, $Conexao);
      $Marca       = mysqli_real_escape_string($this->SQL, $Marca);

      $query = "UPDATE `produto` SET `skuProduto`='$skuProduto',`model`='$model',`NomeProduto`='$NomeProduto',`Quantidade`=
      '$Quantidade',`Conexao`='$Conexao', `Marca`= '$Marca' WHERE`idProduto`= '$idProduto'";
      $result = mysqli_query($this->SQL, $query) or die(mysqli_error($this->SQL));

      if ($result) {
        return 1;
      } else {
        return 0;
      }
      mysqli_close($this->SQL);
    }
  }


  function search($value){
      if(isset($value)){  
        //$output = '';  
        $query = "SELECT * FROM `produto` WHERE `skuProduto` LIKE '".
        $value."%' OR `model` LIKE '".
        $value."%' OR `NomeProduto` LIKE '".
        $value."%' LIMIT 5";  
        $result = mysqli_query($this->SQL, $query); 

        if(mysqli_num_rows($result) > 0){        
        while($row = mysqli_fetch_array($result)){                      
          $output[] = $row; 
        }      
        return array('data' => $output);      
      }else{      
        return 0;
      }        
    }
  }//------

  function searchdata($value){   
    
    $value = explode(' ', $value);
    $valor = str_replace("." , "" , $value[0] ); // Primeiro tira os pontos
    $valor = str_replace("-" , "" , $valor); // Depois tira o taço
    $value = $valor;

    if(isset($value)){  
      //$output = '';  
      $query = "SELECT * FROM `produto` WHERE `skuProduto` = '$value'";  
      $result = mysqli_query($this->SQL, $query);  
      if(mysqli_num_rows($result) > 0){ 
          if($row = mysqli_fetch_array($result))  
          {  
            $output[] = $row; 
          }  
          return array('data' => $output); 
        }else{
          return $value;
      } 
    }  
  }//----searc


}//Fim update Produto
$produto = new Produto;
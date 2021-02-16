<?php
    /* CASO TENHA ALGUM DATABASE COM O NOME REGISTROSPESSOAS, ELE SERÁ APAGADO QUANDO CLIKADO O BOTÃO GRAVAR */
    class Base{
        protected $textJson;
        protected $connection;

        function __construct($host, $user, $password){      
            $this->connection = mysqli_connect($host, $user, $password);
            if($this->connection->connect_error){
                die("Connection failed: " . $this->conn->connect_error);
            }
        }

        /* Foi apresentado na explicação da proposta, que pode ser limpo toda vez as tables referente aos registros */
        function dropTables(){
            $sql = "DROP TABLE registrospessoas.filho";
			mysqli_query($this->connection, $sql);

            if (mysqli_error($this->connection)){ 
				$msg = mysqli_error($this->connection);
                echo $msg;
			} else{
                $sql = "DROP TABLE registrospessoas.pessoa";
			    mysqli_query($this->connection, $sql);

                if (mysqli_error($this->connection)){ 
                    $msg = mysqli_error($this->connection);
                    echo $msg;
                }             
            }
        }

        function criarBase(){
            $sql = "CREATE DATABASE IF NOT EXISTS registrospessoas";
			mysqli_query($this->connection, $sql);

            if (mysqli_error($this->connection)){ 
				$msg = mysqli_error($this->connection);
                echo $msg;
			} else{
                $this->criarTables();
            }
        }

        function criarTables(){
            $sql = "CREATE TABLE IF NOT EXISTS registrospessoas.pessoa (id INT AUTO_INCREMENT NOT NULL, nome varchar(20) NOT NULL, PRIMARY KEY(id))";
			mysqli_query($this->connection, $sql);

            if (mysqli_error($this->connection)){ 
				$msg = mysqli_error($this->connection);
                echo $msg;
			} else{
                $sql = "CREATE TABLE IF NOT EXISTS registrospessoas.filho (id INT AUTO_INCREMENT NOT NULL, id_pessoa INT NOT NULL, nome varchar(20) NOT NULL, PRIMARY KEY(id), FOREIGN KEY (id_pessoa) REFERENCES registrosPessoas.pessoa(id))";
			    mysqli_query($this->connection, $sql);

                if (mysqli_error($this->connection)){ 
                    $msg = mysqli_error($this->connection);
                    echo $msg;
                } else{
                    //echo "Criado Tudo com sucesso";
                }               
            }
        }

        function insertRegistro($register){

            $sql = "INSERT INTO registrospessoas.pessoa (nome) values ('$register')";
			mysqli_query($this->connection, $sql);

            if (mysqli_error($this->connection)){ 
				echo mysqli_error($this->connection);
			}
        }

        function insertRegistroFilhos($index, $pessoa, $filho){

            $sql = "SELECT id FROM registrospessoas.pessoa WHERE id = '$index' AND nome = '$pessoa->nome' LIMIT 1";
			$result = mysqli_query($this->connection, $sql);
            $result = $result->fetch_array();
            $id = (int)$result['id'];

            if (mysqli_error($this->connection)){ 
				echo mysqli_error($this->connection);
			} else{
                $sql = "INSERT INTO registrospessoas.filho (id_pessoa, nome) values ('$id', '$filho')";
			    mysqli_query($this->connection, $sql);

                if (mysqli_error($this->connection)){ 
                    echo mysqli_error($this->connection);     
                }
            }
        }

        function ler(){

            $sql = "SELECT * from registrospessoas.pessoa";
			$resultPai = mysqli_query($this->connection, $sql);
            
            $sql = "SELECT * from registrospessoas.filho";
			$resultFilho = mysqli_query($this->connection, $sql);
            
            if (mysqli_error($this->connection)){ 
				echo mysqli_error($this->connection);
			} else{
                
                $index = 0;

                foreach($resultPai as $resPai){

                    $arrFilhos = array();
                    
                    foreach($resultFilho as $resFilho){
                        
                        if($resPai['id'] == $resFilho['id_pessoa']){
                            array_push($arrFilhos, $resFilho['nome']);
                        }
                    }
                    $this->textJson[$index] = array('nome' => $resPai['nome'], 'filhos' => $arrFilhos);
                    $index++;           
                }
                $this->textJsonFinish['pessoas'] = $this->textJson;
                echo json_encode($this->textJson);

            }
        }
    }
    
    /* Definir as informações de instancia com a base de dados */
    /* Database omitido para a criação de uma com o nome registrospessoas */
    $obj = new Base("localhost","root","");

    if($_GET['acao'] == 'gravar'){
        
        $obj->dropTables();
        $obj->criarBase();

        $textJson = json_decode($_POST['textJson']);
        
        $index = 1;

        $textPessoas = $textJson->pessoas;
        foreach($textPessoas as $pessoas){
            $obj->insertRegistro($pessoas->nome);

            foreach($pessoas->filhos as $filhos){
                $obj->insertRegistroFilhos($index, $pessoas, $filhos);
            }
            $index++;
        }
    } else if($_GET['acao'] == 'ler'){
        $obj->ler();
    }
?>
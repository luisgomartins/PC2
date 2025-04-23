<?php
//é obrigatório ser a primeira linha da págia que usa session
session_start();

//variaveis para controlar o preeenchimento
$email = $senha = "";
$ErroEmail = $ErroSenha = ""; //se não preenhceu os campos

//verificar se foi click (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //verificar preenchimento email
    if (empty($_POST["inputemail"])) {
        $ErroEmail = " is-invalid";
    } else {
        $email = test_input($_POST["inputemail"]);
    }
    //verificar preenchimento senha
    if (empty($_POST["inputsenha"])) {
        $ErroSenha = " is-invalid";
    } else {
        $senha = test_input($_POST["inputsenha"]);
    }
    //se nao teve erro podemos tentar o Login
    //não teve erro quer dizer que as variaveis de erro estão vazias
    if (empty($ErroEmail) && empty($ErroSenha)) {
        //código copiado e colado do W3schools PHP MySQL Select Data
        require 'app/conexao.php';
        //CALCULAR o HASH da Senha
        $hash_senha = md5($senha);
        //comando select modificado para a nossa tabela
        $sql = "SELECT * FROM tb_usuarios WHERE email = '$email' AND senha = '$hash_senha';";
        //obtem o resultado do SELECT
        $result = $conn->query($sql);
        //se tem mais que 0 linhas acertou o email e senha
        if ($result->num_rows > 0) {
            //Pode fazer Login
            while ($row = $result->fetch_assoc()) {
               //carregar as variaveis de Sessão do usuário
               $_SESSION["id_user"] = $row["id"];
               $_SESSION["foto"] = $row["foto"];
               $_SESSION["email"] = $row["email"];
               $_SESSION["nome"] = $row["nome"];
               //redirecionar para página de entrada Segura
               header("location:Sistema/entrada.php");
            }
        } else {
            echo "e-mail ou senha inálidos!!!";
        }
        $conn->close();
    }
}


$lingua = "pt-br";

function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>

<!DOCTYPE html>
<html lang="<?php echo $lingua; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo "Home Page"; ?></title>
    <!-- links do Boostrap-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
    <a href="cadastro.php">Cadastro</a>
    <br>
    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#LoginModal">
        Login
    </button>

    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#DuvidasModal">
        Tirar dúvidas
    </button>

    <!-- Modal Duvidas -->
    <div class="modal fade" id="DuvidasModal" tabindex="-1" aria-labelledby="DuvidasModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="DuvidasModalLabel">Tire suas dúvidas</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal Login-->
    <div class="modal fade" id="LoginModal" tabindex="-1" aria-labelledby="LoginModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="LoginModalLabel">Login</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">

                    <div class="modal-body">
                        <!-- Montaremos o formullário dentro do Modal-->
                        <!-- Tag <form> do w3hools PHP forms validate -->
                        <div class="form-floating mb-3">
                            <input name="inputemail" type="email" class="form-control" id="floatingInput" placeholder="name@example.com">
                            <label for="floatingInput">Email address</label>
                        </div>
                        <div class="form-floating">
                            <input name="inputsenha" type="password" class="form-control" id="floatingPassword" placeholder="Password">
                            <label for="floatingPassword">Password</label>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>

</html>
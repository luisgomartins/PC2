<?php
//é obrigatório ser a primeira linha da págia que usa session
session_start();
//se existe alguma session criada, o usuario pode entrar
if (empty($_SESSION["id_user"])) {
    //se veio vazio, redireciono o usuário de volta para Login(index.php)
    header("location:../index.php?erro=sem session");
}
//codigo do cadastro da COISA
//variáveis 
$exibeMensagem = "";
$textoMensagem = "";
$titulo = $descricao = $foto = $valor = $cor = "";
//variáveis para controlar o que não foi preenchido
$vazioTitulo = $vazioDescricao = "";
//verificar se foi POST (POST é um clique no botão submit)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //verificar se foi botão postar_coisa que foi clicado
    if (isset($_POST["botao_postar"])) {
        //verificar de preencheu tudo que precisa
        if (empty($_POST["inputTitulo"])) {
            $vazioTitulo = " Esta vazio Titulo";
            $exibeMensagem = " show";
            $textoMensagem = "Não preencheu o Título";
        } else {
            $titulo = $_POST["inputTitulo"];
        }
        //verifica se descricao está preenchida
        if (empty($_POST["inputDescricao"])) {
            $vazioDescricao = " Esta vazio Descricao";
            $exibeMensagem = " show";
            $textoMensagem = "Não preencheu a Descrição";
        } else {
            $descricao = $_POST["inputDescricao"];
        }
        $foto = "sem_foto.png";
        $valor = $_POST["inputValor"];
        $cor = $_POST["inputCor"];
        //se o $vazioTitulo e $vazioDescricao estão vazios pode gravar 
        if (empty($vazioTitulo) || empty($vazioDescricao)) {
            //conectar com banco 
            require '../app/conexao.php';
            //pegando o id do usuario da session
            $idusuario = $_SESSION["id_user"];
            //inserir no banco com SQL
            $sql = "INSERT INTO `tb_coisa`(`id_usuario`, `titulo`, `imagem`, `descricao`, `valor`, `cor`) 
            VALUES ($idusuario,'$titulo','$foto','$descricao',$valor,'$cor')";

            if ($conn->query($sql) === TRUE) {
                // echo "New record created successfully";
                $exibeMensagem = " show";
                $textoMensagem = "Seu POST foi realizado com sucesso!";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }

            $conn->close();
        }
    }
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página protegida por Login</title>
    <style>
        .mensagemOK {
            z-index: 9000;
            position: absolute;
            top: 60vh;
            left: 10%;
            box-shadow: 15px 15px 17px;
            width: 80%;
        }
    </style>
</head>

<body>
    <?php require "menu.php"; ?>

    <h1>Só entrou se logou</h1>

    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
        Postar uma coisa
    </button>
    <div class="container p-2">
        <h2>Postagens das Coisas</h2>
        <div class="container p-2">
            <?php
            //chama o arquivo da conexão
            require '../app/conexao.php';
            //seleciona as coisas
            $sql = "SELECT * FROM tb_coisa";
            $result = $conn->query($sql);
            //se achou mais que 0 coisas
            if ($result->num_rows > 0) {
                //faz um loop em todas as linhas da Tb_coisa
                while ($row = $result->fetch_assoc()) {
                    echo $row["titulo"] . "<br>";
                }
            } else {
                echo "0 results";
            }
            $conn->close();
            ?>

        </div>

    </div>












    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Poste uma coisa</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form enctype="multipart/form-data" method="post"
                    action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="modal-body">
                        <h3>Preencha os dados da coisa</h3>
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" name="inputTitulo" id="inputTitulo" placeholder="Título da coisa" />
                            <label for="inputTitulo">Título da Coisa</label>
                        </div>
                        <div class="form-floating mb-3">
                            <textarea class="form-control" name="inputDescricao" id="inputDescricao" placeholder="Descrição da coisa">
                            </textarea>
                            <label for="inputDescricao">Descrição da Coisa</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="file" class="form-control" name="inputImagem" id="inputImagem" placeholder="Escolha uma imagem" />
                            <label for="inputImagem">Arquivo da Imagem</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="number" value="0" step="0.01" class="form-control" name="inputValor" id="inputValor" placeholder="Valor da coisa em R$" />
                            <label for="inputValor">Valor da Coisa</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="color" class="form-control" name="inputCor" id="inputCor" placeholder="Cor da Coisa" />
                            <label for="inputCor">Cor da Coisa</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button name="botao_postar" type="submit" class="btn btn-primary">Postar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <div class="mensagemOK alert alert-warning alert-dismissible fade <?php echo $exibeMensagem ?>" role="alert">
        <strong>Atenção</strong>
        <?php echo $textoMensagem ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</body>

</html>
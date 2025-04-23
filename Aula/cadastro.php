<?php
//variáveis de controle dos dados
$nome = $email = $senha = "";
$ErroNome = $ErroEmail = $ErroSenha = "";
$cadastrou = "";
//verificar se foi click (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    echo "Foi POST porque alguem clicou no submit";
    //verificar se preencheu os campos
    if (empty($_POST["inputnome"])) {
        $ErroNome = " is-invalid";
    } else {
        $nome = test_input($_POST["inputnome"]);
    }

    if (empty($_POST["inputemail"])) {
        $ErroEmail = " is-invalid";
    } else {
        $email = test_input($_POST["inputemail"]);
    }

    if (empty($_POST["inputsenha"])) {
        $ErroSenha = " is-invalid";
    } else {
        $senha = test_input($_POST["inputsenha"]);
    }
    //se está tudo preenchido, cadastrar no banco de dados
    if (empty($ErroNome) && empty($ErroEmail) && empty($ErroSenha)) {
        //TODOS ERRROS VAZIOS (Não teve erros)
        //vamos inserir no banco de dados 
        require 'app/conexao.php';

        $hash_da_senha = md5($senha);
        $sql = "INSERT INTO tb_usuarios (nome, email, senha)
        VALUES ('$nome', '$email', '$hash_da_senha')";
        echo "SQL executado: " . $sql;
        if ($conn->query($sql) === TRUE) {
            echo "Foi cadastrado";
            $cadastrou = " show";
        } else {
            echo "Error:  $sql <br>" . $conn->error;
        }
        $conn->close();
    }
} else {
    echo "Não foi POST, foi GET!!!!!!!!!";
}


function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Usuários</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .btnver {
            position: relative;
            top: -50px;
            right: -90%;
            z-index: 500;
        }
    </style>
</head>

<body>
    <div class="container w-50 p-2 mt-5">
        <h1>Cadastro de usuário</h1>
        <!-- Formulário para cadastro -->
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-floating mb-3">
                <input type="text" class="form-control <?php echo $ErroNome; ?>" id="inputnome" name="inputnome" placeholder="Seu nome ou apelido">
                <label for="inputnome">Nome </label>
            </div>
            <div class="form-floating mb-3">
                <input type="email" class="form-control <?php echo $ErroEmail; ?>" id="inputemail" name="inputemail" placeholder="e-mail">
                <label for="inputemail">Email </label>
            </div>
            <div class="form-floating">
                <input type="password" class="form-control <?php echo $ErroSenha; ?>" id="inputsenha" name="inputsenha" placeholder="Senha">
                <span id="botaoVerSenha" class="btnver btn btn-secondary" onclick="verSenha()"><i class="bi bi-eye"></i></span>
                <label for="inputsenha">Senha</label>
            </div>
            <div class="text-center p-2">
                <button class="btn btn-primary" type="submit">Cadastrar</button>
            </div>

            <div class="alert alert-success alert-dismissible fade <?php echo $cadastrou; ?>" role="alert">
                <i class="bi bi-check-circle"></i>
                <strong>OK!</strong> Cadastro realizado com sucesso.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>

        </form>
    </div>
    <script>
        function verSenha() {
            //trocar o type para text do campo inputsenha
            iconeDoOlho = document.getElementById("botaoVerSenha");
            campoSenha = document.getElementById("inputsenha");
            if (campoSenha.type == "password") {
                iconeDoOlho.innerHTML = '<i class="bi bi-eye-slash"></i>';
                campoSenha.type = "text";
            } else {
                iconeDoOlho.innerHTML = '<i class="bi bi-eye"></i>';
                campoSenha.type = "password";
            }
        }

        function sanitizeInput(input) {
            if (typeof input !== "string") return input;

            // Normaliza espaços extras e remove espaços antes de palavras-chave SQL
            let sanitized = input.trim().replace(/\s+/g, " ");

            // Evita ataques comuns de SQL Injection
            const blacklist = [
                /(\b)(SELECT|INSERT|UPDATE|DELETE|DROP|TRUNCATE|ALTER|EXEC|UNION|OR|AND)(\b)/gi, // Palavras-chave SQL suspeitas
                /(\b)(TABLE|DATABASE|SCHEMA|COLUMN|ROW|GRANT|REVOKE)(\b)/gi, // Outras palavras-chave SQL
                /(--|#)/g, // Remove comentários SQL
                /(\b)(xp_|sp_)/gi, // Bloqueia execuções de procedures perigosas no SQL Server
                /(['"]\s*(OR|AND)\s*['"]\d+=\d+)/gi, // Ataques tipo "OR 1=1"
                /(\b)(LIKE\s*('|").*('|"))/gi, // Bloqueia padrões maliciosos com LIKE
                /(;|--|\bEXEC\b|\bUNION\b)/gi, // Remove comandos maliciosos diretos
                /(\b)(SLEEP|WAITFOR DELAY|BENCHMARK)(\b)/gi // Bloqueia ataques de tempo
            ];

            // Aplica os filtros da lista negra
            blacklist.forEach((pattern) => {
                sanitized = sanitized.replace(pattern, "");
            });

            // Escapa aspas simples para evitar quebras em SQL
            sanitized = sanitized.replace(/'/g, "''");

            return sanitized;
        }
    </script>
</body>

</html>
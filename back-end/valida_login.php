<?php
    session_start(); //'$_SESSION' é uma var global que pode ser acessada de qualquer parte da aplicação, essa função inicia ela
    
    $autenticacao = false;

    /*$usuarios_app = [['email' => 'deivite.santos@masterhoteis.com.br', 'senha' => 'd2912780']]; // Definindo User e Senha
    //foreach()
    foreach($usuarios_app as $ident => $user) {
        if($user['email'] == $_POST['email'] AND $user['senha'] == $_POST['senha']){
            $autenticacao = true;
        }
    }*/

    function conectar(){ //Conexão com PDO e busca por email e senha
        $dsn = 'mysql:host=localhost;dbname=enviadorDeFaturas';
        $usuario = 'root';
        $senha = '';
        $conexao = new PDO($dsn,$usuario,$senha);
        $conexao->exec('set charset set uft8');
        $query = 'select * from tb_usuarios where email = :usuario and senha = :senha';
        $stmt = $conexao->prepare($query);
        $stmt->bindValue(':usuario',$_POST['email']);
        $stmt->bindValue(':senha',$_POST['senha']);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        return $usuario;
    }

    $usuario = conectar();
    if(!empty($usuario['email']) && !empty($usuario['senha'])){
        $autenticacao = true;
    }
    if($autenticacao){
        $_SESSION['autenticado'] = 'SIM';
        header('Location: home.php');
    } else{
        header('Location: index.php?login=erro');//Encaminhar para pagina index.php
        $_SESSION['autenticado'] = 'NAO';
    }
?>
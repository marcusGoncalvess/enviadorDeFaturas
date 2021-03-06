<?php 
    require './lib/PHPMailer/Exception.php';
    require './lib/PHPMailer/OAuth.php';
    require './lib/PHPMailer/PHPMailer.php';
    require './lib/PHPMailer/POP3.php';
    require './lib/PHPMailer/SMTP.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    session_start();
    class Mensagem {
        private $para = null;
        private $fatura = null;
        private $anexo = null;
        private $hotel = null;
        private $copia = null;
        public $status = ['codigo_status' => null, 'descricao_status' => ''];

        public function __get($atributo){
            return $this->$atributo;
        }
        public function __set($atributo,$valor){
            $this->$atributo = $valor;
        }
        public function mensagemValida(){
            if(empty($this->para) || empty($this->anexo)){
                return false;
            }
            return true;
        }
        public function tiraPdf(){
            $fatura = str_replace('.pdf','',$this->fatura);
            return $fatura;
        }
    }
    $mensagem = new Mensagem();
    $mensagem->__set('para',$_POST['para']);
    $mensagem->__set('fatura',$_FILES['arquivo']['name']);
    $mensagem->__set('fatura',$mensagem->tiraPdf());
    $mensagem->__set('anexo','anexo/' . basename($_FILES['arquivo']['name']));
    $mensagem->__set('hotel',$_POST['hotel']);
    $mensagem->__set('copia',$_POST['copia']);
    $copia = $mensagem->__get('copia');
    $file_tmp  = $_FILES['arquivo']['tmp_name'];
    $file_name = $_FILES['arquivo']['name'];
    move_uploaded_file($file_tmp,"anexo/".$file_name);
    if(!$mensagem->mensagemValida()){
        echo 'Mensagem não é válida';
        header('Location: index.php?envio=erro');
    }
    $mail = new PHPMailer(true);
    try {
        //Server settings
        $mail->SMTPDebug = false;                      // Enable verbose debug output
        $mail->isSMTP();                                            // Send using SMTP
        $mail->Host       = 'smtp.live.com';      //smtp.live.com              // Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
        $mail->Username   = $_SESSION['email'];                     // SMTP username
        $mail->Password   = $_SESSION['senha'];                               // SMTP password
        $mail->SMTPSecure = 'tls';         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
        $mail->Port       = 587;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
    
        //Recipients
        $mail->setFrom($mensagem->__get('hotel'),$mensagem->__get('hotel'));
        $mail->addAddress($mensagem->__get('para'),$mensagem->__get('para'));     // Add a recipient
        //$mail->addAddress('ellen@example.com');               // Name is optional
        $mail->addReplyTo($mensagem->__get('hotel'),$mensagem->__get('hotel'));
        if(!empty($copia)){
            $mail->addCC($copia,$copia);
        }
        //$mail->addCC($mensagem->__get('copia'));
        //$mail->addBCC('bcc@example.com');
    
        // Attachments
        $mail->addAttachment($mensagem->__get('anexo'));         // Add attachments
        //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
    
        // Content
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = 'FATURAMENTO: '. $mensagem->__get('fatura');
        $mail->Body    = 'Ol&aacute; prezados, tudo bem?</br></br>
 
        Segue em anexo o faturamento.</br>
        Caso tenhamos mandado ao e-mail errado, por favor, nos informar para quem deveremos encaminhar, ou voc&ecirc;s podem nos pedir para cadastrar um e-mail em nosso registro e no e-mail que estiver cadastrado recebera todas as faturas da empresa.</br></br>

        Obs.: Caso o boleto n&atilde;o for quitado na data do vencimento, ap&oacute;s 10 dias do vencimento o valor ser&aacute; protestado automaticamente.</br></br>
        
        Qualquer d&uacute;vida estou a disposi&ccedil;&atilde;o.';
        $mail->AltBody = 'Ola, segue em anexo o faturamento.';
    
        $mail->send();
        $mensagem->status['codigo_status'] = 1;
        $mensagem->status['descricao_status'] = 'E-mail enviada com sucesso';
    } catch (Exception $e) {
        $mensagem->status['codigo_status'] = 2;
        $mensagem->status['descricao_status'] = "Não foi possívem enviar esse e-mail. Mailer Error: {$mail->ErrorInfo}";
    }
?>
<html>
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <title>Enviar Faturas</title>
    <link rel="apple-touch-icon" sizes="57x57" href="favicon/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="favicon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="favicon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="favicon/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="favicon/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="favicon/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="favicon/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="favicon/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="favicon/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="favicon/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon/favicon-16x16.png">
    <link rel="manifest" href="favicon/manifest.json">
</head>
<body>
    <div class="container">
        <div class="py-3 text-center">
				<img class="d-block mx-auto mb-2" src="logo-master.png" alt="" width="72" height="72">
				<h2>Enviar Faturamento</h2>
			</div>
        <div class="row">
            <div class='col-md-12'>
            <? if($mensagem->status['codigo_status'] == 1) {  ?>
                <div class='container text-center'>
                    <h1 class='display-4 text-success'>Sucesso</h1>
                    <p><?= $mensagem->status['descricao_status'] ?></p>
                    <a href="home.php" class='btn btn-success btn-lg mt-5 text-white'>Voltar</a>
                </div>
            <? } ?>
            <? if($mensagem->status['codigo_status'] == 2) {  ?>
                <div class='container text-center'>
                    <h1 class='display-4 text-danger'>Ops!</h1>
                    <p><?= $mensagem->status['descricao_status'] ?></p>
                    <a href="home.php" class='btn btn-danger btn-lg mt-5 text-white'>Voltar</a>
                </div>
                <? } ?>
            </div>
        </div>
    </div>
</body>
</html>
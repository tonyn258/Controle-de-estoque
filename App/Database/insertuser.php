<?php
require_once '../auth.php';
require_once '../Models/usuario.class.php';

    $Username  = $_POST['Username'];
    $Email      = $_POST['Email'];
    $Password   = md5 ($_POST['Password']);
    $permissao      = $_POST['permissao'];

    if ($Username != NULL && $Password != NULL && $perm ==1) {

        if (!file_exists($_FILES['arquivo']['name'])) {		
			
			$pt_file =  '../../views/dist/img/'.$_FILES['arquivo']['name'];
			
			if ($pt_file != '../../views/dist/img/'){	
				
				$destino =  '../../views/dist/img/'.$_FILES['arquivo']['name'];				
				$arquivo_tmp = $_FILES['arquivo']['tmp_name'];
				move_uploaded_file($arquivo_tmp, $destino);
				chmod ($destino, 0644);	

                $nomeimagem =  'dist/img/'.$_FILES['arquivo']['name'];

            }elseif($_POST['valor'] != NULL) {

                $arq = explode('../',$_POST['valor']);
                $nomeimagem = $arq[1];

            }
        }   
    
    $usuario->InsertUser($Username, $Email, $Password, $nomeimagem, $permissao);
} 
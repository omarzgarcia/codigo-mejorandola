<?php 
//Incluímos el archivo que contiene la cadena de conección a la BD
include("npconection.php");

function quitar($mensaje)
{
$mensaje = str_replace("<","<",$mensaje);
$mensaje = str_replace(">",">",$mensaje);
$mensaje = str_replace("\'","'",$mensaje);
//$mensaje = str_replace('\"',""",$mensaje);
//$mensaje = str_replace("\\\\","\",$mensaje);
return $mensaje;
}

if(trim($_POST["nick"]) != "" && trim($_POST["password"]) != "")
{
	$nickN   = quitar($_POST["nick"]);
	$passN   = quitar($_POST["password"]);
        $md5pass = md5($passN);	
	
	//Buscamos el password a partir del usuario que ingreso en la caja de texto
         mysql_query("SET NAMES utf8");
	$result = mysql_query("SELECT Id_Usuario, password, Id_Perfil, CONCAT(Nombre, ' ', Ap_Paterno, ' ', Ap_Materno) Usuario, Activado FROM usuario WHERE mail='$nickN'");
	if($row = mysql_fetch_array($result))
	{
			//Si el password que recuperamos del query es igual al password que ingresarón
			//en la caja de texto, va a permitir el ingreso al contenido
			if($row["password"] == $md5pass)
			{

                            //Código para validar que el usuario se activo a través del mail que se les envía
                            if($row["Activado"]!=1)
                             {
      		                ?>
		                <SCRIPT LANGUAGE="javascript">
		                 alert("Debe activar su cuenta por medio del mail que le hemos enviado");
		                 location.href = "index.php";
		                 </SCRIPT>
		                 <?php
                                 exit();
                              }

				//90 dias dura la cookie
				setcookie("usNick",$nickN,time()+120);
				setcookie("usPass",$md5pass,time()+120);

	                       session_start();
	                       $_SESSION['nivelPerfil']= $row["Id_Perfil"] ;	
	                       $_SESSION['Usuario']   = $row["Usuario"] ;
	                       $_SESSION['IdUsuario']   = $row["Id_Usuario"] ;	                       

                                          $orig = $_POST["urlRequest"];
                                          $orig = explode("@",$orig); 
                                          echo $orig;

				?>
				Usuario registrado. Disfrute de nuestro contenido
				<SCRIPT LANGUAGE="javascript">
                                var variable='<?php echo$orig[0];?>'
                                  if(variable != "")
				    location.href = variable;
                                  else
                                    location.href = "index.php";
				</SCRIPT>
				<?php 
			}
			//Si el password que se recupero del query no es el mismo al que se ingreso en la 
			//caja de texto, se envía una alerta de error
			else
			{ 

		                      ?>
		                      <SCRIPT LANGUAGE="javascript">
		                      alert("Password incorrecto favor de validarlo");
		                      location.href = "npform-login.php";
		                      </SCRIPT>
		                      <?php
			}
	mysql_free_result($result);		
	}
	
	//Si no se encontro un Password para el usuario ingresado se debe a que los datos no son correctos
	else
	{
		?>
		<SCRIPT LANGUAGE="javascript">
		alert("Datos incorrectos favor de verificarlos");
		location.href = "npform-login.php";
		</SCRIPT>
		<?php

	}
}
mysql_close();
?>
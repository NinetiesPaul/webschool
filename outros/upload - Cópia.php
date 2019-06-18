					<?php
					

						if(!empty($_POST)){
							$userId = $_POST['usuario'];
								
								$data = getdate();
								$long = $data[0];
								$file_name=$long.'-'.$_FILES['arquivo']['name'];
								$file_name_thumb=$long.'-thumbs-'.$_FILES['arquivo']['name'];
								
							$target_dir = "uploads/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
							
							if ((($_FILES["arquivo"]["type"] == "image/gif")
							|| ($_FILES["arquivo"]["type"] == "image/jpeg")
							|| ($_FILES["arquivo"]["type"] == "image/jpg")
							|| ($_FILES["arquivo"]["type"] == "image/pjpeg")
							|| ($_FILES["arquivo"]["type"] == "image/x-png")
							|| ($_FILES["arquivo"]["type"] == "image/png"))
							&& in_array($extension, $allowedExts))
							{
								
								$path='../res/images/';
								$pathThumb='../res/images/thumbs/';
								
								if(!file_exists($path)){
								mkdir($path);
								}
								if(!file_exists($pathThumb)){
								mkdir($pathThumb);
								}
								
								$data = getdate();
								$long = $data[0];
								$file_name=$long.'-'.$_FILES['arquivo']['name'];
								$file_name_thumb=$long.'-thumbs-'.$_FILES['arquivo']['name'];
								
								echo $file_name.' '.$file_name_thumb;
								
								$images = $_FILES["arquivo"]["tmp_name"];
								$new_images = $long.'-thumbs-'.$_FILES['arquivo']['name'];
								$width=200;
								$size=GetimageSize($images);
								$height=round($width*$size[1]/$size[0]);
								$images_orig = ImageCreateFromJPEG($images);
								$photoX = ImagesX($images_orig);
								$photoY = ImagesY($images_orig);
								$images_fin = ImageCreateTrueColor($width, $height);
								ImageCopyResampled($images_fin, $images_orig, 0, 0, 0, 0, $width+1, $height+1, $photoX, $photoY);
								ImageJPEG($images_fin,$pathThumb.$new_images);
								ImageDestroy($images_orig);
								ImageDestroy($images_fin);
								
								move_uploaded_file($_FILES['arquivo']['tmp_name'], $path . $file_name);
								move_uploaded_file($_FILES['arquivo']['tmp_name'], $path . $file_name_thumb);
								
								$urlFinal = 'uploads/'.$file_name;
								$urlThumbFinal = 'uploads/uploadsThumb/'.$file_name_thumb;
								
								#upload_image($urlFinal,$urlThumbFinal,$imovel);
								
								$user = $db->prepare("INSERT INTO fotosdeavatar (imagemThumbUrl, imagemUrl, idUsuario) VALUES (:imagemThumbUrl, :imagemUrl, :idUsuario)");
		
								$user->execute([
									'imagemThumbUrl' => $urlThumbFinal,
									'imagemUrl' => $urlFinal,
									'idUsuario' => $userId,
								]);
							}else{
								echo '<div class="erro">Arquivo selecionado não é um arquivo válido.
								<a href="#" class="ok">Ok</a> </div>';
							}
						}
						
						echo '<br/><hr/>Fotos desse imovel:<br/>';
						/*$imagens = get_images($imovel);
						while ($imagem = pg_fetch_array($imagens)) {
							echo '<center><a href=../../'.$imagem['imagemurl'].'><img src=../../'.$imagem['imagemthumburl'].' /></a>
							<a href="deletarFotoImovel.php?foto='.$imagem['idimagemimovel'].'"><br/>Deletar foto</a><p/></center>';
						} */
						
						echo '<p/><a href="imoveis.php">Voltar </>';

					?>
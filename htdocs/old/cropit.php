<?php
	if ($_SERVER['REQUEST_METHOD'] == 'GET')
		header("Location: http://cropit.rf.gd/"); 
?>
<?php
	session_start();
	$counter = file_get_contents("./count/count"); 
	file_put_contents("./count/count",++$counter); 
?>
<html>
<head>
	<title>CropIt: Image Resizer</title>
	<style>
		#photo { 
		
			margin-left: 35%; 
			border: 1px solid black; 
			margin-bottom: 10px; 
		}
		#sign { 
			margin-left: 20px; 
			border: 1px solid black;
			margin-bottom: 10px; 
		}
		.success { 
			text-align: center; 
		}
		.del { 
			text-align: center; 
		}
		#sub { 
			font-size: 30px;
			border: 1px solid black; 
		}
		#footer { 
			text-align: center; 
		}
	</style>
</head>
<body>
<?php
	require 'vendor/autoload.php'; 
	use Imagine\Image\Box;
	use Imagine\Image\Point;
	use Imagine\Image\ImageInterface;
	use lsolesen\pel\PelJpeg; 
	use lsolesen\pel\PelTag; 
	use lsolesen\pel\PelIfd; 
	if ($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		if (isset($_FILES['photo']) && isset($_FILES['sign']) && file_exists($_FILES['photo']['tmp_name']) && file_exists($_FILES['sign']['tmp_name']))
		{
			if (isset($_POST['p_unit']) && isset($_POST['s_unit']) && isset($_POST['p_width']) && isset($_POST['p_height']) && isset($_POST['s_width']) && isset($_POST['s_height'])) 
			{
				$folder = time(); 
				$_SESSION['folder'] = md5($folder); 
				mkdir('./upload/'.$_SESSION['folder']);
				$p_unit = $_POST['p_unit']; 
				$p_width = $_POST['p_width']; 
				$p_height = $_POST['p_height']; 
				$p_min = filter_var($_POST['p_min'], FILTER_VALIDATE_FLOAT) ? $_POST['p_min'] : 1;
				$p_min = ($p_min < 50) ? $p_min : 1; 
				$p_max= filter_var($_POST['p_max'], FILTER_VALIDATE_FLOAT) ? $_POST['p_max'] : 50; 

				$s_unit = $_POST['s_unit']; 
				$s_width = $_POST['s_width']; 
				$s_height = $_POST['s_height']; 
				$s_min = filter_var($_POST['s_min'], FILTER_VALIDATE_FLOAT) ? $_POST['s_min'] : 1; 
				$s_min = ($s_min < 50) ? $s_min : 1; 
				$s_max = filter_var($_POST['s_max'], FILTER_VALIDATE_FLOAT) ? $_POST['s_max'] : 50; 
				
			if (((($p_unit == 'p_px') && ($s_unit == 's_px'))||(($p_unit == 'p_cm') && ($s_unit == 's_cm'))) && filter_var($p_width, FILTER_VALIDATE_FLOAT) && filter_var($p_height, FILTER_VALIDATE_FLOAT) &&  filter_var($s_width, FILTER_VALIDATE_FLOAT) && filter_var($s_height, FILTER_VALIDATE_FLOAT) && ($p_min <= $p_max) && ($s_min <= $s_max))
			{
				$fileinfo = finfo_open(FILEINFO_MIME_TYPE);
				if ((finfo_file($fileinfo, $_FILES['photo']['tmp_name']) == 'image/jpeg')&& (finfo_file($fileinfo, $_FILES['sign']['tmp_name']) == 'image/jpeg'))
				{
					if((move_uploaded_file($_FILES['photo']['tmp_name'],"./upload/".$_SESSION["folder"].'/'.$_FILES['photo']['name'])) &&(move_uploaded_file($_FILES['sign']['tmp_name'],"./upload/".$_SESSION["folder"].'/'.$_FILES['sign']['name'])))
					{	
						if (($p_unit == 'p_cm') && ($s_unit == 's_cm'))
						{	
							$p_width = $p_width * 0.39 * 200; 
							$p_height = $p_height* 0.39 * 200; 
							$s_width = $s_width * 0.39 * 200; 
							$s_height= $s_height* 0.39 * 200; 
						}
						
						$p_path = "./upload/".$_SESSION["folder"].'/'.$_FILES['photo']['name'];
						$s_path = "./upload/".$_SESSION["folder"].'/'.$_FILES['sign']['name'];
						$imagine = new Imagine\Gd\Imagine(); 
						$p_options = array('jpeg_quality' => 100);
						$image_p = $imagine->open($p_path);
						$image_p->resize(new Box($p_width,$p_height));
						$image_p->save('./upload/'.$_SESSION['folder'].'/photo_resized.jpeg',$p_options);
						$photo_size =  filesize('./upload/'.$_SESSION['folder'].'/photo_resized.jpeg')/1000;
//						echo "photo size: ".$photo_size." kb<br/>"; 
						if( ($photo_size <= $p_max) && ($photo_size >= $p_min) )
						{
//							echo "pmax = ".$p_max."<br>"; 
//							echo "pmin = ".$p_min."<br>";
							showFinalPhoto($qual='', $p_width, $p_height); 
						}
						else 
						{
							$qual = manupulatePhoto($photo_size, $p_max, $p_min, $p_width, $p_height); 	
							showFinalPhoto($qual, $p_width, $p_height); 
						}
					
						$s_options = array('jpeg_quality' => 100,
								'resolution-units' => ImageInterface::RESOLUTION_PIXELSPERINCH,
    								'resolution-x' => 300,
    								'resolution-y' => 300,
								);
						$image_s = $imagine->open($s_path);
						$image_s->resize(new Box($s_width,$s_height));
						$image_s->save('./upload/'.$_SESSION['folder'].'/sign_resized.jpeg',$p_options);
						$sign_size =  filesize('./upload/'.$_SESSION['folder'].'/sign_resized.jpeg')/1000;
//						echo "sign size: ".$sign_size." kb<br/>"; 
						if( ($sign_size <= $s_max) && ($sign_size >= $s_min) )
							showFinalSign($qual='', $s_width, $s_height); 
						else 
						{
							$qual = manupulateSign($sign_size, $s_max, $s_min, $s_width, $s_height); 	
							showFinalSign($qual, $s_width, $s_height);
						}
//						echo "Done"; 
					}
					else 
					{
						echo "<h1>Server Error!!</h1><p>Please try again</p>"; 
						img_destroy();  
						sess_destroy(); 
					}
				}
				else 
				{
					unlink($_FILES['photo']['tmp_name']); 
					unlink($_FILES['sign']['tmp_name']); 
					echo "<h1>Sorry...Only supports jpeg files</h1>"; 
					img_destroy(); 
					sess_destroy(); 
				}
			}
			else 
			{
				echo "<h1>Error occured</h1><p>Please enter proper dimensions.</p><p>Check entered output sizes: enter minimum size in 1<sup>st</sup> box and maximum in 2<sup>nd</sup> box</p>"; 
				img_destroy();  
				sess_destroy(); 
			}
			}
			else 
			{
				echo "<h1>Please select/enter proper dimensions</h1>"; 
				sess_destroy(); 
			}
		}
		else 
		{
			echo "<p><strong>Error: </strong>Please fill all options properly</p><p><strong>Note: </strong>You have to fill all options for both <strong>Photo & Signature</strong></p>"; 
			sess_destroy();
		} 
	}
	
	function manupulatePhoto($photo_size, $p_max, $p_min,$p_width, $p_height, $count=0) 	
	{
//		echo "in functino"; 
		while( !(($photo_size <= $p_max) && ($photo_size >= $p_min)) )
		{
			if ($photo_size > $p_max)
			{
				$count += 1; 
				$qual = 100 - $count; 
				$imagine = new Imagine\Gd\Imagine(); 
				$p_options = array('jpeg_quality' => $qual);
				$image_p = $imagine->open('./upload/'.$_SESSION['folder'].'/photo_resized.jpeg');
				$image_p->resize(new Box($p_width,$p_height));
				//$image_p->save('./upload/photo_resized.jpeg',$p_options);
				$image_p->save('./upload/'.$_SESSION['folder'].'/photo_resized'.$qual.'.jpeg',$p_options);
				//$photo_size =  filesize('./upload/photo_resized.jpeg')/1000;
				$photo_size =  filesize('./upload/'.$_SESSION['folder'].'/photo_resized'.$qual.'.jpeg')/1000;
				continue; 
			}
			else
			{	
//				echo "in else block"; 
				if ($count == 0)
					$qual = 'x'; 
				$req = $p_min - $photo_size; 
				$req = str_repeat(' ', $req*1000);
				$jpeg = new PelJpeg('./upload/'.$_SESSION['folder'].'/photo_resized.jpeg');

		        	$jpeg1 = new PelJpeg('./upload/3bytes.jpg');
	       			$exif1 = $jpeg1->getExif();

	        		$jpeg->setExif($exif1);
        			$exif = $jpeg->getExif();
			        $tiff = $exif->getTiff();
			        $ifd0 = $tiff->getIfd();
			        $desc = $ifd0->getEntry(PelTag::SOFTWARE);
			        //$s = str_repeat(' ', 1000);  
			        $desc->setValue($req);  
			        $jpeg->saveFile('./upload/'.$_SESSION['folder'].'/photo_resizedx.jpeg');
				return $qual;
			} 
		}
			return $qual;
	}	
	function showFinalPhoto($qual, $p_width, $p_height) 
	{
	/*	$photo_size =  filesize('./upload/photo_resized'.$qual.'.jpeg')/1000;
		//echo "Final photo size is: ".$photo_size." kb"; 
		$image =  './upload/photo_resized'.$qual.'.jpeg';
		$name =  'photo_resized'.$qual.'.jpeg';
		$info = getimagesize($image);
		$fs = filesize($image);
		header("Content-Type: image/jpeg\n"); 
		header("content-disposition: attachment; filename=\"$name\"\n"); 
		readfile($image); 
	*/
		$image =  './upload/'.$_SESSION['folder'].'/photo_resized'.$qual.'.jpeg';
		echo "<div class='success'><h1>Operations performed successfully...</h1>";
		echo "<p><strong>Note: </strong>To <strong>download</strong> images Right-Click on each image and Click 'Save image as..'</p>";
		echo "<p><strong>Note: </strong>To <strong>delete</strong> images from server Click on Delete Images button below..'</p></div>";
		echo "<img id='photo' src='$image' width=$p_width height=$p_height alt='error'>"; 
	}
	function manupulateSign($sign_size, $s_max, $s_min,$s_width, $s_height, $count=0) 	
	{
//		echo "in functino"; 
		while( !(($sign_size <= $s_max) && ($sign_size >= $s_min)) )
		{
			if ($sign_size > $s_max)
			{
				$count += 1; 
				$qual = 100 - $count; 
				$imagine = new Imagine\Gd\Imagine(); 
				$s_options = array('jpeg_quality' => $qual);
				$image_s = $imagine->open('./upload/'.$_SESSION['folder'].'/sign_resized.jpeg');
				$image_s->resize(new Box($s_width,$s_height));
				//$image_s->save('./upload/sign_resized.jpeg',$s_options);
				$image_s->save('./upload/'.$_SESSION['folder'].'/sign_resized'.$qual.'.jpeg',$s_options);
				//$photo_size =  filesize('./upload/photo_resized.jpeg')/1000;
				$sign_size =  filesize('./upload/'.$_SESSION['folder'].'/sign_resized'.$qual.'.jpeg')/1000;
				continue; 
			}
			else
			{	
//				echo "in else block"; 
				if ($count == 0)
					$qual = 'x'; 
				$req = $s_min - $sign_size; 
				$req = str_repeat(' ', $req*1000);
				$jpeg = new PelJpeg('./upload/'.$_SESSION['folder'].'/sign_resized.jpeg');

		        	$jpeg1 = new PelJpeg('./upload/3bytes.jpg');
	       			$exif1 = $jpeg1->getExif();

	        		$jpeg->setExif($exif1);
        			$exif = $jpeg->getExif();
			        $tiff = $exif->getTiff();
			        $ifd0 = $tiff->getIfd();
			        $desc = $ifd0->getEntry(PelTag::SOFTWARE);
			        //$s = str_repeat(' ', 1000);  
			        $desc->setValue($req);  
			        $jpeg->saveFile('./upload/'.$_SESSION['folder'].'/sign_resizedx.jpeg');
				return $qual;
			} 
		}
			return $qual;
	}	
	function showFinalSign($qual, $s_width, $s_height) 
	{
		//$sign_size =  filesize('./upload/sign_resized'.$qual.'.jpeg')/1000;
		//echo "Final sign size is: ".$sign_size." kb"; 
		$image =  './upload/'.$_SESSION['folder'].'/sign_resized'.$qual.'.jpeg';
		echo "<img id='sign' src='$image' width=$s_width height=$s_height alt='error'>"; 
		viewDeleteButton(); 
	}
	function viewDeleteButton()
	{
		echo "<div class='del'><form action='deleteimages.php' method='POST'><input id='sub' type='submit' name='delete' value='Delete Images'></form></div>"; 
		
	}
	function img_destroy() 
	{ 
		foreach (glob('./upload/'.$_SESSION['folder'].'/*.*') as $filename)
		{
			if(is_file($filename)) 
			{
				unlink($filename); 
			}
		}
		rmdir('./upload/'.$_SESSION['folder']); 
	}
	
	function sess_destroy()
	{
		session_unset(); 
		session_destroy(); 
	}

?>
	<div id="footer">
		<pre><p>Site Developed By <strong>Lalit Munne.</strong></p></pre>
	</div>
	<script>
			function bottom() { 
				btm = document.getElementById('footer'); 
				height = document.body.clientHeight; 
				width = document.body.clientWidth; 
				btm.style.position = 'absolute'; 
				btm.style.left = width/2 - 120; 
				btm.style.top= height - 60; 
			}
			bottom(); 
			window.onresize = function() { bottom() } ;
	</script>
	<script>
                (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
                })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

               ga('create', 'UA-88916663-2', 'auto');
               ga('send', 'pageview');

        </script>
</body>
</html>

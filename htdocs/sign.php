<?php
//tochange
if ($_SERVER['REQUEST_METHOD'] == 'GET')
		header("Location: http://cropit.rf.gd/");
session_start();
include_once("analytics.php");
ini_set("max_execution_time",30);
register_shutdown_function('shutdown');
?>
<html>
	<head>
		<title>CropIt: Image Resizer</title>
		<meta name="description" content="Welcome to CropIt: Image Resizer. A website, dedicated to students filling online competitive exam forms.">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<script src="//ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
		<style>
         body { 
            height: 100%; 
            padding-top: 70px; 
            background-color: #333;
        }
        .gap { 
            margin-top: 45px; 
        }
        .text-muted {
            color: #fff; 
            margin-top: 15px; 
            font-size: 120%; 
        }
        .text-muted a{ 
            color: #fff;
        }
        .caption { 
        	color: aliceblue;
        }
        form { 
        	display:inline;
        }
        .btn-danger {
        	border-radius: 0px 4px 4px 0px;
        }
        body > .container {
      		padding: 0px 15px 70px;
    	}
        </style>
	</head>

	<body>
		<nav class="navbar navbar-inverse navbar-fixed-top">
			<div class="container">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="index.html">CropIt</a>
				</div>
				<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
					<ul class="nav navbar-nav">
						<li><a href="index.html">Both</a></li>
						<li><a href="photo.html">Photo</a></li>
						<li class="active"><a href="sign.html">Sign</a></li>
					</ul> 
					<ul class="nav navbar-nav navbar-right">
						<li><a href="feedback.html">Feedback</a></li>
						<li><a href="about.html">About Us</a></li>
					</ul>
				</div>

			</div>
		</nav>
		<div class="container">
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
		if (isset($_FILES['sign']) && file_exists($_FILES['sign']['tmp_name']))
		{
			if (isset($_POST['s_unit']) && isset($_POST['s_width']) && isset($_POST['s_height'])) 
			{
				$folder = time()+rand(); 
				$_SESSION['folder'] = md5($folder); 
				mkdir('./upload/'.$_SESSION['folder']);
				
				$s_unit = $_POST['s_unit']; 
				$s_width = abs($_POST['s_width']); 
				$s_height = abs($_POST['s_height']); 
				$s_min = filter_var(abs($_POST['s_min']), FILTER_VALIDATE_FLOAT) ? $_POST['s_min'] : 1; 
				$s_min = ($s_min < 70) ? $s_min : 1; 
				$s_max = filter_var(abs($_POST['s_max']), FILTER_VALIDATE_FLOAT) ? $_POST['s_max'] : 50; 
				if (($s_max - $s_min) < 5)
					killme('atleast 5kb difference is required in between output sizes.');
				if (($s_width >=700)||($s_height >=700))
										killme('Maximum allowed resolution exceeds.');
				if ((($s_unit == 's_px')||($s_unit == 's_cm')) && filter_var($s_width, FILTER_VALIDATE_FLOAT) && filter_var($s_height, FILTER_VALIDATE_FLOAT) && ($s_min <= $s_max))
				{
					$fileinfo = finfo_open(FILEINFO_MIME_TYPE);
					if (finfo_file($fileinfo, $_FILES['sign']['tmp_name']) == 'image/jpeg')
					{
						if(move_uploaded_file($_FILES['sign']['tmp_name'],"./upload/".$_SESSION["folder"].'/'.$_FILES['sign']['name']))
						{	
							if ($s_unit == 's_cm')
							{	
								$s_diff = $s_max - $s_min;
								if (($s_width >=10)||($s_height >=10))
										killme('Maximum allowed resolution exceeds.');
								$s_width = $s_width * 0.39 * 200; 
								$s_height= $s_height* 0.39 * 200; 
							}
							
							$s_path = "./upload/".$_SESSION["folder"].'/'.$_FILES['sign']['name'];
							$imagine = new Imagine\Gd\Imagine();
							$p_options = array('jpeg_quality' => 100);
							$image_s = $imagine->open($s_path);
							$image_s->resize(new Box($s_width,$s_height));
							$image_s->save('./upload/'.$_SESSION['folder'].'/sign_resized.jpeg',$p_options);
							$sign_size =  filesize('./upload/'.$_SESSION['folder'].'/sign_resized.jpeg')/1000;
							clearstatcache(); 
							if( ($sign_size <= $s_max) && ($sign_size >= $s_min) )
									showFinalSign($qual='', $s_width, $s_height, $s_unit); 
							else 
							{
								$qual = manupulateSign($sign_size, $s_max, $s_min, $s_width, $s_height); 	
								showFinalSign($qual, $s_width, $s_height, $s_unit);
							}
						}
						else 
						{
							echo '<div class="alert alert-warning" role="alert"><strong>Server Error!</strong> Please try again.</div>'; 
							img_destroy();  
							sess_destroy(); 
						}
					}
					else 
					{
						unlink($_FILES['sign']['tmp_name']); 
						echo '<div class="alert alert-warning" role="alert"><strong>Error!</strong> Only Supports JPEG File</div><div class="alert alert-info" role="alert">You can easily convet it into JPEG using paint.</div>'; 
						img_destroy(); 
						sess_destroy(); 
					}
				}
				else 
				{
					echo '<div class="alert alert-warning" role="alert"><strong>Error!</strong> Please enter proper dimensions.</div>';
					echo '<div class="alert alert-info" role="alert">Check entered output sizes.</div>';
					img_destroy();  
					sess_destroy(); 
				}
			}
			else 
			{
				echo '<div class="alert alert-warning" role="alert"><strong>Error!</strong> Please enter proper dimensions.</div>';
				sess_destroy(); 
			}
		}
		else 
		{
			echo '<div class="alert alert-warning" role="alert"><strong>Error!</strong> Please fill all options properly.</div>';
			sess_destroy();
		}

	}
	function manupulateSign($sign_size, $s_max, $s_min,$s_width, $s_height, $count=0) 	
	{
		$qual = '';
		while( !(($sign_size <= $s_max) && ($sign_size >= $s_min)) )
		{
			clearstatcache(); 
			if ($sign_size > $s_max)
			{
				$count += 1; 
				$qual = 100 - $count; 
				$imagine = new Imagine\Gd\Imagine(); 
				$s_options = array('jpeg_quality' => $qual);
				$image_s = $imagine->open('./upload/'.$_SESSION['folder'].'/sign_resized.jpeg');
				$image_s->resize(new Box($s_width,$s_height));

				$image_s->save('./upload/'.$_SESSION['folder'].'/sign_resized'.$qual.'.jpeg',$s_options);

				$sign_size =  filesize('./upload/'.$_SESSION['folder'].'/sign_resized'.$qual.'.jpeg')/1000;
			}
			else
			{	
				clearstatcache(); 
				$req = $s_min - $sign_size; 
				if ($req > 3)
					$req = str_repeat(' ', $req*1000);
				$jpeg = new PelJpeg('./upload/'.$_SESSION['folder'].'/sign_resized'.$qual.'.jpeg');
			try {
                                @$jpeg1 = new PelJpeg('./upload/3bytes.jpg');
                        }
                        catch (Exception $e) {
                                copy('./3bytes.jpg', './upload/3bytes.jpg');
                                @$jpeg1 = new PelJpeg('./upload/3bytes.jpg');
                        }
       			$exif1 = $jpeg1->getExif();
        		$jpeg->setExif($exif1);
    			$exif = $jpeg->getExif();
		        $tiff = $exif->getTiff();
		        $ifd0 = $tiff->getIfd();
		        $desc = $ifd0->getEntry(PelTag::SOFTWARE);  
		        $desc->setValue($req);  
		        $jpeg->saveFile('./upload/'.$_SESSION['folder'].'/sign_resized'.$qual.'.jpeg');
		        $sign_size =  filesize('./upload/'.$_SESSION['folder'].'/sign_resized'.$qual.'.jpeg')/1000;
		        return $qual;
			} 
		}
		return $qual;
	}	
	function showFinalSign($qual, $s_width, $s_height, $s_unit) 
	{
		clearstatcache(); 
		$s = substr($s_unit,2);
		if ($s == 'cm') 
		{
			$width = $s_width / 0.39 / 200; 
			$height= $s_height / 0.39 / 200; 
		}
		else 
		{
				$width = $s_width;
				$height = $s_height;
		}

		$image =  './upload/'.$_SESSION['folder'].'/sign_resized'.$qual.'.jpeg';
		$sign_size =  filesize($image)/1000;
		echo "<div class='text-center'><img id='sign' class='img-thumbnail' src='$image' width=$s_width height=$s_height alt='error'><div class='caption'><p>$width x $height $s | $sign_size kb</p></div></div>"; 
		viewDeleteButton(); 
	}
	function viewDeleteButton()
	{
		echo '<div class="text-center"><div class="btn-group"><button class="btn btn-success">Download</button><form action="delete.php" method="POST"><button class="btn btn-danger" name="delete" value="Delete Images">Delete</button></form></div></div>';
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
	function killme($msg)
	{
		echo '<div class="alert alert-warning" role="alert"><strong>Error!</strong> '.$msg.'</div>'; 
		echo '</div><nav class="navbar navbar-inverse navbar-fixed-bottom"><p class="text-muted text-center">&copy; Copyright 2016-2019 <a href="mailto:lalitmunne9@gmail.com">Lalit Munne</a></p>
    </nav>';
		exit(0);
	}
	function shutdown()
	{
		$a = error_get_last();
		if ($a['type'] == 1)
		{
			echo '<script>a=$(".container")[1]; $(a).html(\'<div class="alert alert-warning" role="alert"><strong>Error!</strong> please enter proper inputs.</div></div><nav class="navbar navbar-inverse navbar-fixed-bottom"><p class="text-muted text-center">&copy; Copyright 2016-2019 <a href="mailto:lalitmunne9@gmail.com">Lalit Munne</a></p></nav>\');</script>';
    	}
	}

?>
	</div>
	<div class="gap"></gap>
		<nav class="navbar navbar-inverse navbar-fixed-bottom">
            <div class="f">
		<p class="text-muted text-center">Copyright &copy; 2016-<script>document.write((new Date()).getFullYear())</script> <a href="mailto:lalitmunne9@gmail.com">Lalit Munne</a></p>

            </div>
        </nav>
		<iframe id="frame" style="display:none;"></iframe>
        <script>
        	$count = 1;
            $('.btn-success').on('click',function(){
            	$count++;
            	$('img').each(function(){
            		var $this = $(this);
            		$this.wrap('<a href="' + $this.attr('src') + '" download />');
            		$this.click();
            	})
            	if($count > 2)
            	{
            		$('img').each(function(){ 
            			$("#frame").attr('src',$(this)[0].src);
            		});
            	}
            });
        </script>
    </body>
</html>

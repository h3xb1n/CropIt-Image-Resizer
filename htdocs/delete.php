<?php
//tochange
if ($_SERVER['REQUEST_METHOD'] == 'GET')
		header("Location: http://cropit.rf.gd/");
include_once("analytics.php");
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
						<li><a href="sign.html">Sign</a></li>
					</ul> 
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
	if ($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		if (isset($_POST['delete']) && $_POST['delete'] == 'Delete Images')
		{
                	foreach (glob('./upload/'.$_SESSION['folder'].'/*.*') as $filename)
                	{
                       		if(is_file($filename))
                        	{
                                	unlink($filename);
	                        }
        	        }
               		rmdir('./upload/'.$_SESSION['folder']);
			echo '<div class="alert alert-success" role="alert"><strong>Done!</strong> Deleted Successfully.</div>'; 
			echo '<div class="alert alert-info" role="alert"><strong>Thanks!</strong> For using website.</div>';
			echo '<div class="alert alert-info" role="alert">Feel free to share with others.</div>';

			header("refresh:7; url=http://cropit.rf.gd/"); 
        	}
		else 
			echo '<div class="alert alert-danger" role="alert"><strong>Error!</strong> Server fault.</div>'; 
	}
	else 
		header("Location: http://cropit.rf.gd/"); 
?>

	</div>
	<div class="gap gap1"></gap>
		<nav class="navbar navbar-inverse navbar-fixed-bottom">
			<div class="f">
				<p class="text-muted text-center">Copyright &copy; 2016-<script>document.write((new Date()).getFullYear())</script> <a href="mailto:lalitmunne9@gmail.com">Lalit Munne</a></p>

			</div>
		</nav>
        <script>
            if ($(document).height() > $(window).height()){
                $(".gap1").append($(".f"));
                $(".f").addClass('navbar navbar-inverse');
                $(".f").css("marginBottom","0px");
                $(".navbar-fixed-bottom").css('display','none');
            }
        </script>
    </body>
</html>

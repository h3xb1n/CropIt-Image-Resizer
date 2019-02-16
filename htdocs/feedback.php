<?php
//tochange
if ($_SERVER['REQUEST_METHOD'] == 'GET')
        header("Location: http://cropit.rf.gd/");
include_once("analytics.php");
?>
<!DOCTYPE html>
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
                    <ul class="nav navbar-nav navbar-right">
                        <li class="active"><a href="feedback.html">Feedback</a></li>
                        <li><a href="about.html">About Us</a></li>
                    </ul>
                </div>
            </div>
        </nav>
        <div class="container">
<?php
	if($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		function spam_scrubber($value) 
		{
			$very_bad = array('to:','cc:','content-type:','mime-version:','multipart-mixed:','content-transfer-encoding:');
			foreach ($very_bad as $v)
			{
				if(stripos($value,$v) !== false)
					return '';
			} 
			$value = str_replace(array( "\r","\n","%0a","%0d"), '', $value);

			return trim($value);
		}

		$scrubbed = array_map('spam_scrubber', $_POST);
		if (!empty($scrubbed['message']) && !empty($scrubbed['subject']) && !empty($scrubbed['email']))
		{
			$message = wordwrap($scrubbed['message'], 70);
			$subject = wordwrap($scrubbed['subject'], 70);
			$headers = 'From: '.$scrubbed['email'] . "\r\n" . 'Cc: lalitmunne9@gmail.com'."\r\n";
            $e = "Email Address: ".$_POST['email']."\n";
            $s = "Subject: ".$_POST['subject']."\n";
            $m = "Message: ".$_POST['message']."\n---------------------------------------------------------\n";
            $feedback_file = fopen("xfeedbackx", "a+");
            fwrite($feedback_file, $e);
            fwrite($feedback_file, $s);
            fwrite($feedback_file, $m);
            fclose($feedback_file); 
			if(mail('lalitmunne9@gmail.com', $subject, $message, $headers))
				echo '<div class="alert alert-success" role="alert"><strong>Thanks!</strong> We have received your message.</div>';
			else
				echo '<div class="alert alert-danger" role="alert"><strong>Oh snap!</strong> Unexpected error. Please try again later.</div>';

		}
		else 
		{
			echo '<div class="alert alert-warning" role="alert"><strong>Error!</strong> Please fill out the form completely.</div>';
		}
	}
?> 

</div>
		 <div class="gap"></div>
        <nav class="navbar navbar-inverse navbar-fixed-bottom">
            <div class="f">
		<p class="text-muted text-center">Copyright &copy; 2016-<script>document.write((new Date()).getFullYear())</script> <a href="mailto:lalitmunne9@gmail.com">Lalit Munne</a></p>

            </div>
        </nav>
    </body>
</html>

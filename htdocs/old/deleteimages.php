<?php 
	session_start(); 
?>
<html>
<head>
	<title>CropIt: Image Resizer</title>
	<style>
	h1, h4, p { 
		text-align: center; 
	}
	#footer { 
		text-align: center; 
		}
	</style>
	
</head>
<body>
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
			echo "<h1>Operation Performed Successfully</h1>"; 
			echo "<h4>Thanks for using site...</h4>";
			echo "<p><strong>Note:</strong> Your images are successfully deleted from server. You will be automatically redirected to Main Page..</p>";
			header("refresh:5; url=http://cropit.rf.gd/"); 
        	}
		else 
			echo "<h1>Error Occured</h1>";
	}
	else 
		header("Location: http://cropit.rf.gd/"); 
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
	



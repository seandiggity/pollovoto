<?php
// require configuration file
require ("conf.php");

if ($viewErrors == "1") {
	// activate full error reporting
	error_reporting(E_ALL & E_STRICT);
}

if ($forceSSL == "1") {
	// Detect if this is an SSL connection, switch to SSL if not
	if ( !isset($_SERVER['HTTPS']) || strtolower($_SERVER['HTTPS']) != 'on' ) {
		//SSL is OFF
	   header ('Location: https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
	   exit();
	}
}

// set counter
$submitted = 0;

// if form has been submitted, save data from form fields as a file on the server
if (isset($_POST['submit'])) {

	// set counter
	$submitted = 1;

	if (empty($errors)) {
		$date1 = date("Y-m-d_H-i-s");
		$date2 = date("l F j, Y");
		$randstring = getRandomString();
		//$lastName = just_clean(stripslashes($_POST['Last_Name']));
		//$firstName = just_clean(stripslashes($_POST['First_Name']));
		//$filename = $folderpath.$lastName."_".$firstName."_".$date1.$filetype;
		$filename = $folderpath.$formname."_".$date1."_".$randstring.$filetype;
		$dbfilename = $folderpath.$formname.$filetype; //"database" file, the file with combined content of all files
		$dbbackuptemp = $folderpath.$formname."_backup_".$date1."_".$randstring.$filetype; //temp "database" backup file
		$dbbackup = $folderpath.$formname."_backup".$filetype; //"database" backup file
		$temp_filename = $folderpath.$formname."_".$date1."_".$randstring."_temp".$filetype;
		touch($filename) or die('<div class="alert alert-error">'.$failMsg.'</div>');

		if (is_writable($filename)) {

			if ($fp = fopen($filename, 'w')) {
			$ip_addr = $_SERVER['REMOTE_ADDR'];

				//if (filter_var($ip_addr, FILTER_VALIDATE_IP)) { //filter_var doesn't exist on server for some reason
				
				if ($filetype==".csv") { //for generating an MS Excel-readable .csv file
					foreach($_POST as $name => $value) { //first line of .csv, with column headings
						if ($name!='submit'&&$value!='Submit'&&$name!='formSubmit'&&$name!='FormSubmit'&&$value!='Save'&&$value!=='') {
						$name = '"'.str_replace('_',' ',$name).'",';
						fputs($fp, "$name");
						}
					}
				fputs($fp, "\r\n"); //first line break
					foreach($_POST as $name => $value) { //second line of .csv, with user data
						if ($name!='submit'&&$value!='Submit'&&$name!='formSubmit'&&$name!='FormSubmit'&&$value!='Save'&&$value!==''&&$value!='Submit'&&$value!='formSubmit'&&$value!='FormSubmit') {
						$value = '"'.stripslashes(str_replace('_',' ',$value)).'",';
						fputs($fp, "$value");
						}
					}
				fputs($fp, "\r\n"); //second line break
				} //end .csv if statement

				if ($filetype==".rtf") { //for generating a MS Word-readable .rtf file
					fputs($fp, "Vote from $ip_addr on $date2\r\n\r\n");
						foreach($_POST as $name => $value) {
							if ($value!='Submit'&&$value!='formSubmit'&&$value!='FormSubmit') {
							$name = str_replace("_"," ",$name);
							$value = stripslashes($value);
							fputs($fp, "$name: \t$value\r\n\r\n");
							}
						}
					} //end .rtf if statement

				/*}
       		 		else {
				die('<div class="alert alert-error">'.$failMsg.'</div>');
				}*/

			fclose($fp);
			
			// this logic for one big "database" file applies mainly to the CSV option, and may need to be tweaked for the RTF option to look nice
			if (file_exists($dbfilename)) {
				stream_copy($dbfilename, $dbbackuptemp); //make backup of "database" file if it exists

				if ($fp = fopen($dbfilename, 'a')) { //open "database" for writing

						foreach($_POST as $name => $value) { //add new line to "database" with new submission data
							if ($name!='submit'&&$value!='Submit'&&$name!='formSubmit'&&$name!='FormSubmit'&&$value!='Save'&&$value!==''&&$value!='Submit'&&$value!='formSubmit'&&$value!='FormSubmit') {
							$value = '"'.stripslashes(str_replace('_',' ',$value)).'",';
							fputs($fp, "$value");
							}
						}
						
					fputs($fp, "\r\n"); //insert line break
					fclose($fp);
				}
				else {
				die('<div class="alert alert-error">'.$failMsg.'</div>');
				}

			unlink($filename); //del file for original submission
			}
 
			else { //if "database" file doesn't exist, make one with the latest submission
				if (file_exists($filename)) { stream_copy($filename, $dbfilename); stream_copy($dbfilename, $dbbackuptemp); unlink($filename); } }

			//clean up backups and temp files
			unlink($dbbackup);
			stream_copy($dbbackuptemp, $dbbackup);
			unlink($dbbackuptemp);
			
			//remove last form submission from memory
			unset($_POST);
			
			//echo('<div id="formSuccess" style="">'.$successMsg.'</div>');
			//header("refresh: 0; url=index.php");
		        }
       		 	else {
			die('<div class="alert alert-error">'.$failMsg.'</div>');
			}

			}
		else {
		die('<div class="alert alert-error">'.$failMsg.'</div>');
		}

	}
	else {
	echo '<div class="span4"><div class="alert alert-error"><a class="close">×</a><strong>Error:</strong> <br>';
		foreach ($errors as $error) {
		echo $error.'<br>';
		}
	echo '</div></div></div><br><br><br>';
	}

	// reset counter, clear $_POST
	unset($_POST);
}

// optimized copy function
    function stream_copy($src, $dest)
    {
        $fsrc = fopen($src,'r');
        $fdest = fopen($dest,'w+');
        $len = stream_copy_to_stream($fsrc,$fdest);
        fclose($fsrc);
        fclose($fdest);
        return $len;
    } 

// Generate random string of a specific length
function getRandomString($length = 6) {
	$validCharacters = "abcdefghijklmnopqrstuxyvwzABCDEFGHIJKLMNOPQRSTUXYVWZ0123456789";
	$validCharNumber = strlen($validCharacters);
	$result = "";
	for ($i = 0; $i < $length; $i++) {
		$index = mt_rand(0, $validCharNumber - 1);
		$result .= $validCharacters[$index];
		}
	return $result;
	}

function just_clean($string) {
// Replace other special chars
$specialCharacters = array(
	'#' => '',
	'$' => '',
	'%' => '',
	'&' => '',
	'@' => '',
	'.' => '',
	'€' => '',
	'+' => '',
	'=' => '',
	'§' => '',
	'\\' => '',
	'/' => '',
);
 
	while (list($character, $replacement) = each($specialCharacters)) {
	$string = str_replace($character, $replacement, $string);
	}
 
$string = strtr($string,
"ÀÁÂÃÄÅ? áâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ",
"AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn"
);
 
// Remove all remaining other unknown characters
$string = preg_replace('/[^a-zA-Z0-9-]/', '', $string);
$string = preg_replace('/^[-]+/', '', $string);
$string = preg_replace('/[-]+$/', '', $string);
$string = preg_replace('/[-]{2,}/', '', $string);

return $string;
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title><?= $title; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.css" media="screen">
    <link rel="stylesheet" href="css/bootswatch.min.css">
    <link rel="stylesheet" href="fancybox/source/jquery.fancybox.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="css/custom.css">
    <!-- HTML5 shim, Modernizr and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="js/html5shiv.js"></script>
      <script src="js/modernizr.min.js"></script>
      <script src="js/respond.min.js"></script>
      
    <![endif]-->
  </head>

  <body<?php if ($submitted==1) { ?> onload="$('a.fancybox.success').trigger('click');"<?php } ?>>

    <div class="navbar navbar-default navbar-fixed-top" >
      <div class="container">
        <div class="navbar-header">
	  <a href="#submitVote" class="btn btn-danger fancybox" title=""><?= $headerText; ?></a>
          <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-main" id="toggleButton" onclick="toggleLogo();">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
        </div>
        <div class="navbar-collapse collapse" id="navbar-main" style="border:0;">
          <ul class="nav navbar-nav navbar-right">
		<li><span class="topnav_btn"><a href="#about" class="btn btn-mini fancybox" title=""><?= $aboutTitle; ?></a></span></li>
          </ul>
        </div>
      </div>
    </div>

    <div class="container">
<div id="topLogo"><img src="img/top_logo.png" alt="<?= $formname; ?>" id="topLogoImg"></div>

	<div id="about" class="" style="display:none">
                  <legend><?= $aboutTitle; ?></legend>
		<p class="bodyText"><?= $aboutText; ?></p>
	</div><!--/about-->


    	<div id="submitVote" class="" style="display:none">
                  <legend><?= $headerText; ?></legend>
		<p><?= $formIntro; ?></p>

<form action="<?= $_SERVER['PHP_SELF']; ?>" method="post" name="<?= $formname; ?>">
	<input type="hidden" name="FormSubmit" value="1">
	<input type="hidden" name="Date" value="<?=date('Y-m-d');?>">
	<input type="hidden" name="Time" value="<?=date('H:i:s');?>">
	<div id="formFields">

	<h3 class="panel-title"><?= $voteQuestion; ?></h3>

        <div id="formPanel" class="panel-body">

	<?php if ($simpleYesNo == "1") { ?>        
          <label class="radio-inline" style="margin-bottom:12px;">
		<input type="radio" name="voteChoice" id="Yes" value="Yes" style="width:60px;"> <span style="font-size:1.6em;">Yes</span>
          </label><br>
          <label class="radio-inline" style="margin-bottom:12px;">
		<input type="radio" name="voteChoice" id="No" value="No" style="width:60px;"> <span style="font-size:1.6em;">No</span>
          </label><br>
		<?php if ($allowAbstain == "1") { ?>
		  <label class="radio-inline" style="margin-bottom:12px;">
			<input type="radio" name="voteChoice" id="Abstain" value="Abstain" style="width:60px;"> <span style="font-size:1.6em;"><?= $abstainText; ?></span>
		  </label><br>
		<?php } ?>
	<?php } ?>

	<?php if ($askName == "1") { ?>
	<input class="form-control" name="First_Name" id="First_Name" type="text" maxlength="80" placeholder="First Name" <?php if ($reqName = "1") { ?>required=""<?php } ?>>
	<input class="form-control" name="Last_Name" id="Last_Name" type="text" maxlength="80" placeholder="Last Name" style="margin-top:12px;" <?php if ($reqName = "1") { ?>required=""<?php } ?>>
	<?php } ?>

	<?php if ($askEmail == "1") { ?>
	<input class="form-control" name="Email" id="Email" type="text" maxlength="80" placeholder="Email" style="margin-top:12px;margin-bottom:20px;" <?php if ($reqEmail = "1") { ?>required=""<?php } ?>>
	<?php } ?>

	<?php if ($allowComments == "1") { ?>
	<textarea class="form-control" rows="4" maxlength="<?= $commentsLength; ?>" name="<?= $commentsTitle; ?>" id="<?= $commentsTitle; ?>" placeholder="<?= $commentsTitle; ?>" required="" lengthcut="true"></textarea>
	<?php } ?>

        </div><!--/formPanel-->

	<br>
	<input type="submit" name="submit" value="<?= $buttonText; ?>" class="btn btn-danger fancybox">
	</div><!--/formFields-->

	<!--<a href="#formSuccess" class="btn btn-danger fancybox" title=""></a>-->
</form>

	</div><!--/submitVote-->


	<a href="#formSuccess" class="btn btn-mini fancybox success" style="display:none;visibility:hidden;" title=""></a>
    	<div id="formSuccess" class="" style="display:none">
		<?= $successMsg; ?>
	</div><!--/formSuccess-->


    </div><!--/container-->

    <script src="js/jquery-1.10.2.min.js"></script>
    <script src="js/jquery.easing.1.3.js"></script>
    <script src="js/jquery-smooth-scroll.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/bootswatch.js"></script>
    <script src="js/charcount.js"></script>
    <script src="fancybox/source/jquery.fancybox.pack.js"></script>
    <script type="text/javascript">
	$(document).ready(function() {

	/* This is basic - uses default settings */
	
	$("a.fancybox").fancybox();
	
	/* Using custom settings */
	
	$("a#inline").fancybox({
		'hideOnContentClick': true
	});

	/* Apply fancybox to multiple items */
	
	$("a.group").fancybox({
		'transitionIn'	:	'elastic',
		'transitionOut'	:	'elastic',
		'speedIn'		:	600, 
		'speedOut'		:	200, 
               'overlayOpacity'	:	0.8,
		'overlayColor'		:	'black',
		'overlayShow'		:	true,
        'padding' : 30,
        'width' : 600
	});
	
	});
    </script>
    <script src="js/custom.js"></script>
  </body>
</html>

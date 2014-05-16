<?php
/* Configuration for Pollovoto voting system by Sean "Diggity" O'Brien sean@webio.me */

$title = "Pollovoto: one-time password voting"; // title for poll or vote

$headerText = "Cast Your Vote"; // text for header
$buttonText = "Submit Vote"; // text for voting button
$formIntro = "<em>Please vote with the form below.</em>"; // intro text for form

$aboutTitle = "About"; // title of "About" section
$aboutText = "Pollovoto is a voting system using one-time passwords (OTP or tokens) for voting.  It records the vote but does not record information about the voter, yet it is still able to verify if a vote has been cast by that specific voter for a given poll or election."; // text description of vote

$voteQuestion = "<p>Do you vote for a <strong>motion of no confidence</strong> against <strong>person</strong>?</p>"; // question to ask voters

$simpleYesNo = "1"; // is this a simple Yes or No question?
$allowAbstain = "1"; // allow an Abstain option?
$abstainText = "I choose to abstain"; // abstain text

$askName = "0"; // ask for name?
$reqName = "0"; // require name?
$askEmail = "0"; // ask for e-mail?
$reqEmail = "0"; // require e-mail?

$allowComments = "0"; // allow comments?
$commentsLength = "160"; // number of characters allowed in comments field
$commentsTitle = "Comments"; // title to use for comments field

$successMsg = "<legend>Success!</legend><p>Your vote has been submitted.  Thank you.</p>"; // text for success message
$failMsg = "<h4>There has been an error with the form.</h4>"; // text for error message;

$formname = "poll"; // short name of voting form
$filetype = ".csv"; // either ".csv" or ".rtf", Comma Separated Values (for spreadsheet program) or Rich Text Format (for word processor)
$folderpath = "./admin/"; // admin location to store and view results

$viewErrors = "1"; // enable strict error reporting?
$forceSSL == "1"; // force SSL?
?>

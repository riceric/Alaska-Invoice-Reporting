<?php
/*
* example.php
* class_session.php example usage
* Author: Troy Wolf (troy@troywolf.com)
*/

/*
Include the session class. Modify path according to where you put the class
file.
*/
require_once(dirname(__FILE__).'/class_session.php');

/*
Instantiate a new session object. If session exists, it will be restored,
otherwise, a new session will be created--placing a sid cookie on the user's
computer. You can pass "true" to session() to require the user to login before
accessing this page. Read the help documentation and the comments in 
class_session.php for more help with the password-protect feature.
*/
if (!$s = new session()) {
  /*
  There is a problem with the session! The class has a 'log' property that
  contains a log of events. This log is useful for testing and debugging.
  */
  echo "<h2>There is a problem with the session!</h2>";
  echo $s->log;
  exit();
}

/*
Add some data to the session.
*/
$s->data['uname'] = "John Doe";
$s->data['favcolor'] = "orange";
$s->data['ip_address'] = $_SERVER['REMOTE_ADDR'];

/*
Save the session.
*/
if (!$s->save()) {
  /*
  There is a problem with the session! The class has a 'log' property that
  contains a log of events. This log is useful for testing and debugging.
  */
  echo "<h2>There is a problem with the session!</h2>";
  echo $s->log;
  exit();
}

/*
On additional pages, you instantiate the session same as above. You can then
access the session data using the data[] property.
*/
echo "<br />Your name is ".$s->data['uname'];
echo "<br />Your favorite color is ".$s->data['favcolor'];
echo "<br />Your IP Address is ".$s->data['ip_address'];

/*
Just for fun, display the session log.
*/
echo "<hr /><b>Session log</b><br />";
echo $s->log;
?>

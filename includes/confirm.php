<?php
/*
 * Filename.....: confirm.php
 * Aufgabe......: Generates a javascript-(vbscript)-question before a
 *                critical action may be executed.
 *                Needs JavaScript enabled to work.
 *                If VBScript is available, it works even better.
 * Parameter....: see test_confirm.php for examples how to use the class
 * Erstellt am..: Montag, 27. Januar 2003 08:59:18
 *       _  __      _ _
 *  ||| | |/ /     (_) |        Wirtschaftsinformatiker IHK
 * \. ./| ' / _ __  _| |_ ___   www.ingoknito.de
 * - ^ -|  < | '_ \| | __/ _ \
 * / - \| . \| | | | | || (_) | Peter Klauer
 *  ||| |_|\_\_| |_|_|\__\___/  06131-651236
 * mailto.......: knito@knito.de
 *
 * Remarks: Code snippets found
 * makeMsgBox = http://www.perlscriptsjavascripts.com/js/alertboxes.html (for binding into html page)
 * makeMsgBox = http://www.webreference.com/dhtml/column22/js-vbNewvb.html (for parameters)
 *
 * Changes:
 * 2005-11-18: Repaired confirm.php: new functions were pasted in older file,
 *             destructing the newer version. The error happened on 2005-11-03.
 *             Compensating this by even more phpdoc comments.
 *             Deleted: confirm::scripttags (was always true)
 *             Deleted: confirm::getbrowsertype()
 * 2005-11-03: New functions: sconfirm_url() and sconfirm_button() return strings.
 *             Changed: confirm_url and confirm_button use the new functions.
 * 2005-10-08: msg_mousetxt = obsolete changed: confirm::mousetext() to use msg_title instead
 * 2005-01-04: new var confirm::msg_mousetext, phpdoc-Comments, confirm_button changed
 *             new confirm_submit will replace confirm_button in the future
 *             new function getbrowsertype() determines if browser does like vbscript
 *             including GetBrowserType.php (Package 289) is no longer needed.
 *             http://knito.users.phpclasses.org/browse.html/package/289.html
 * 2003-08-02: Introduced var $GetBrowserTypeLoaded because I ran in trouble
 *             without it. Now I set $this->GetBrowserTypeLoaded to true and
 *             the class won't try to load it a second time, when the page had
 *             already included GetBrowserType.php.
 * 2003-01-29: Changed default dir from '' to '.' for *ix. Mr. Rissmann reported
 *             that IE5.1 on Mac does not support VBScript => changed.
 *             For research purposes (does my browser support vbscript?) a new
 *             var $this->_tryanyway was introduced. This overrides the browser-
 *             evaluation and tries to open a vbscript dialog resulting usually in no
 *             action taken when the browser does not support vbscript.
 */

class confirm
{

  #
  # Did you define a css .class?
  # Put the name of the class via $confirm->cssclass = 'mycssclass'
  #
  var $cssclass   = '';

  #
  # Do you work with frames?
  # Then the target window should be given here
  #
  var $target     = '';

  #
  # defaults for makeMsgBox()
  #
  var $msg_title = 'Question'; // Caption line of dialog form
  var $msg_icon   = 2; // 0=none 1=X 2=? 3=! 4=i
  var $msg_default_button = 1; // 0=first, 1=second, 2=...
  var $msg_buttons = 4; // 0=ok 1=ok_cancel 2=abort_retry_cancel 3=yes_no_cancel 4=yes_no 5=retry_cancel
  var $msg_modal = 0; // 0=Application_Modal 1=System_Modal
  var $msg_mousetext = ''; // onmouseover-Text

  var $_debug = false;
  var $_installed = false;
  var $_tryanyway = false;

  #
  # These vars will be set from install_confirm()
  #
  var $BrowserCanDoVBScript = false; // Use enhanced Forms instead of standard confirm() dialog
  var $browser_type = 0; // 1 = IE, 0 = all others
  var $browser_version = 0;

  /**
  * install_confirm() must be used in the head section of the page
  * it installs javascript and vbscript functions
  * @return void
  **/
  function install_confirm()
  {

    #$this->getbrowsertype(); // set BrowserCanDoVBScript
    
    global $HTTP_USER_AGENT;

    $this->browser_version = intval(trim(substr($HTTP_USER_AGENT, 4 + strpos($HTTP_USER_AGENT, 'MSIE'), 3)));

    if ($this->browser_version > 0) // this is MS IE
    {
      $this->browser_type = 1; // 1 = IE, 0 = all others...
      $this->BrowserCanDoVBScript = true;

      #
      # But reported was that IE 5.1 on Mac does not support VBScript.
      # Reporter: gunther rissmann
      #
      if (eregi('MAC', $HTTP_USER_AGENT)) $this->BrowserCanDoVBScript = false;

      #
      # if there are non-ie browsers out there which can do VBScript
      # or ie browsers which cannot (like MAC)
      # please tell me!
      #

    } // if this is an IE    
    

    if( $this->_debug )
    {
      #
      # Debugging generates non-tidy-proof code since this
      # is written into the <head>-section.
      #
      echo "<br>the browser version is : $this->browser_version<br>".
      "the browser type is : $this->browser_type<br>";
    } // _debug == true

    if( $this->_tryanyway ) $this->BrowserCanDoVBScript = true;

    if( $this->BrowserCanDoVBScript )
    {
      #
      # The script section is only inserted when the
      # browser can do vbscript.
      #
  ?>
<script language="JavaScript" type="text/javascript">
<!--
function qconfirm(title,mess,icon,buts,defbut,mods)
{
  retVal = makeMsgBox( title,mess,icon,buts,defbut,mods);
  if(retVal == 6 || retVal == 1)
  {
    return true;
  }
  else
  {
    return false;
  }
}
//-->
</script>

<SCRIPT LANGUAGE="VBScript" TYPE="text/vbscript">
<!--
Function makeMsgBox(title,mess,icon,buts,defbut,mods)
butVal = buts + (icon*16) + (defbut*256) + (mods*4096)
makeMsgBox = MsgBox(mess,butVal,title)
End Function
//-->
</SCRIPT>

<?php
    } // BrowserCanDoVBScript == true

    $this->_installed = true;

  } // eof install_confirm()

  /**
  * echo an url with onclick function
  * the url is only processed when the question is answered with "Yes"
  *
  * @param string $question = Text for messagebox
  * @param string $value = text of URL to click on it (underlined, mostly)
  * @param string $url = the http-file the url points to
  * @return void
  **/
  function confirm_url( $question, $value, $url)
  {
    echo $this->sconfirm_url( $question, $value, $url);
  } // eof confirm_url()

  /**
  * echo a form button with onclick function
  * the form is only processed when the question is answered with "Yes"
  *
  * @param string $question = Text for messagebox
  * @param string $value = caption of the button
  * @param optional string $name        = name of form element
  * @return void
  **/
  function confirm_button( $question, $value, $name='' )
  {
    echo $this->sconfirm_button(  $question, $value, $name );
  } // eof confirm_button()

  /**
  * return a string containing an url with onclick function
  * the url is only processed when the question is answered with "Yes"
  *
  * @param string $question = Text for messagebox
  * @param string $value = text of URL to click on it (underlined, mostly)
  * @param string $url = the http-file the url points to
  * @return string
  **/
  function sconfirm_url( $question, $value, $url)
  {
     $c = $this->css_class();
     $t = $this->target_string();
    return "<a href=\"$url\"$t ".
    $this->onclick($question)."$c>$value</a> ";
  } // eof sconfirm_url()

  /**
  * return a string containing a form button with onclick function
  * the form is only processed when the question is answered with "Yes"
  *
  * @param string $question = Text for messagebox
  * @param string $value = caption of the button
  * @param optional string $name        = name of form element
  * @return string
  **/
  function sconfirm_button( $question, $value, $name='' )
  {
    $n = $name == '' ? 'question_button' : $name;
    $c = $this->css_class();
    return "<input type=submit name=\"$n\"$c ".
    $this->onclick($question)." value='$value'> ";
  } // eof sconfirm_button()


  /**
  * echo a submit button with onclick function
  * give it a (hopefully) unique name when $name is empty
  * the form will only be submitted if "YES" is chosen
  *
  * @param string $question = Text for messagebox
  * @param string $value = caption of the button
  * @param optional string $name        = name of form element
  * @return void
  **/
  function confirm_submit( $question, $value, $name='' )
  {
    static $counter=0;

    $counter++;
    $n = $name == '' ? ('question_button'.$counter) : $name;
    $c = $this->css_class();
    echo "<input type=submit name=\"$n\"$c ".
    $this->onclick($question).
    $this->mousetext().
    " value='$value'> ";

  } // eof confirm_submit()


  #
  # Internal functions
  #

  /**
  * return ' class="cssclass"'
  * tell about missing install_confirm() if necessary
  * @return string
  **/
  function css_class()
  {

    if( !$this->_installed )
    {
      echo '<h3>Dear Programmer!</h3>Please call the '.
      'function install_confirm() in the &lt;head&gt; section of this document.'.
      '<br><br>Regards, Knito<br><br>';
    }
//return( strlen($this->cssclass) == 0 ? '' : ' class="'.$this->cssclass.'"');
return( "class=style.css" );

  } // eof css_class()

  /**
  * if target window for url is given in $this->target then return
  * ' target="targetwindow"' else empty string
  *
  * @return string
  **/
  function target_string()
  {

//    return( strlen($this->target) == 0 ? '' : ' target="'.$this->target.'"');
return( "target=_parent" );

  } // eof target_string()

  /**
  * returns 'title="msg_mousetext"'
  *
  * @param void
  * @return string
  **/
  function mousetext()
  {

    return ( strlen( trim( $this->msg_title ) ) == 0 ? '' : ' title="'.$this->msg_title.'"' ) ;

  } // eof mousetext()  

  
  /**
  * Chose the script to use for the confirmation window
  * return a javascript onclick string "onclick=..."
  *
  * @param string $question = question to be asked when clicked on
  * @return string
  **/
  function onclick( $question )
  {

    return 'onclick="return '.
    ( $this->BrowserCanDoVBScript ?
    "qconfirm('$this->msg_title','$question',$this->msg_icon,".
    "$this->msg_buttons,$this->msg_default_button,$this->msg_modal)\"" :
    "confirm('$question')\"" );

  } // eof onclick()


  
} // end of class confirm
?>
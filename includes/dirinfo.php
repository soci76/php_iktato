<?php


class DIRINFO
{
    var $root_dir;
    var $root_url;
    var $date_format="%d %b %y %H:%M";
    var $icon = array(
                'default'=>'none.gif',
                'File Folder'=>'/icons/dir.gif',
                'html'=>'/icons/world1.gif');
    var $list_detail = array(
        //"Name"=>"Name",
        "Name-url"=>"Name",
        "Size"=>"Size",
        //"Attribute"=>"Attribute",
        "Type"=>"Type",
        //"Created"=>"Created",
        // "Accessed"=>"Accessed",
        "Modified"=>"Modified"
        // "Comment"=>""
    );

    var $stat=array( array() );
    var $sort=array();

    //#-------------------------------------------------------------------------
    //#---- FUNCTION DIRINFO($root_dir, $root_url, $filter, $icon=NULL, $date="")
    //#---- DESCRIPTION  ::
    //#----      Read all the file under $root_dir with url path $root_url
    //#----      Assemble defualt field
    //#---- INPUT ::
    //#----      $root_dir:     directory path to be read
    //#----      $root_url:     corresponding url
    //#----      $filter:     eg "*" read all, "*.html" only html
    //#----      $icon:      icon type and images see sample
    //#----      $date:     date format to b used according to strftime
    //#----             eg %d/%m/%y %H:%M  gives 31/12/2003 23:59
    //#----             eg %d %b %y    gives 21 Dec 2003
    //#---- OUTPUT ::
    //#----       no of item: $this->no_item
    //#----       file info stored:  $this->stat[index]['attribute name']
    //#----         'Name': file name with icon, no http link
    //#----         'Name-url': file name with icon, with http link
    //#----         'fname': file name without icon without http link
    //#----         'Type': file extension, or 'File Folder' or 'default'
    //#----         'Size': size in byte
    //#----         'Modified', 'Created', 'Accessed': modified/created/last
    //#----                 access time
    //#----         'ftype': file type returned by function filetype()
    //#-----------------------------------------------------------------
    function DIRINFO($root_dir, $root_url, $filter, $icon=NULL, $date="")
    {   // initialize class
        $this->root_dir = $root_dir;
        $this->root_url = $root_url;
        $this->filter = $filter;
        if ($date != "") $this->date_format = $date;
        // files
        $this->no_item = 0;
        $dh = ( is_dir($root_dir) ? opendir($root_dir): FALSE);
        foreach ( glob("$root_dir/$filter") as $file_full )
        {   $file = basename($file_full);
            if ( $file != "." && $file != "..")
            {   // set index
                $id = $this->no_item;
                $this->order[$id] = $id;
                $this->no_item ++;
                // item info
                $item="$root_dir/$file";
                //=== Extract File Information
                $stat = stat($item);
                //=== Determine Name and Name-url
                $this->stat[$id]['fname'] = $file;
                // determine icon
                $this->stat[$id]['ftype'] = filetype($item);
                if ( $this->stat[$id]['ftype'] == "dir" )
                    $this->stat[$id]['Type'] = "File Folder";
                else  if ( $this->stat[$id]['ftype'] == "file")
                   $this->stat[$id]['Type'] = substr(strrchr($file, '.'), 1 );
                else
                   $this->stat[$id]['Type'] = "Unknown";
                // url
                $this->stat[$id]['url']=$this->root_url . "/$file";
                // file name
                $this->stat[$id]['Name'] =$file;
                // file name with url link
                $this->stat[$id]['Name-url'] =$file;
                //=== Determine Size
                $this->stat[$id]['Size'] =
                        $stat['size']. " Byte";
                //=== Determine Attribute
                $this->stat[$id]['Attribute'] = $stat['mode'];
                $this->stat[$id]['Created'] = strftime($this->date_format, $stat['ctime']);
                $this->stat[$id]['Accessed'] = strftime($this->date_format, $stat['atime']);
                $this->stat[$id]['Modified'] = strftime($this->date_format, $stat['mtime']);
            }
        } // if glob
    } // end GetFileList

    //#-------------------------------------------------------------------------
    //#---- FUNCTION Sort($sortkey=array('ftype', 'Name'))
    //#---- DESCRIPTION  ::
    //#----      Sort the file list according to $sortkey array
    //#----      Typically file type is sorted first followed by name
    //#----      This give all directory to be on top
    //#---- INPUT ::
    //#----      $sortkey: array: first key being most important.
    //#-------------------------------------------------------------------------
    function Sort($sortkey=array('ftype', 'Name'))
    {   if ($this->no_item > 0)
        {
            $this->sort = $sortkey;
            usort($this->order, array($this, "DirSort"));
        }
    }
    // supporting function for sorting
    function DirSort($a, $b)
    {   $order=1;
        foreach ( $this->sort as  $key )
        {   if ( $this->stat[$a][$key] <  $this->stat[$b][$key])
            {   $order = -1;
                break;
            }
        }
        return $order;
    }

    //#-------------------------------------------------------------------------
    //#---- FUNCTION DisplayList($id="", $attrib=array())
    //#---- DESCRIPTION  ::
    //#----      Display the file list in tabular format
    //#----      The first row is the header row:
    //#----      <TH>desc1</TH><TH>desc2</TH>...
    //#----      each rowin the table contain one file item:
    //#----          <TD class='attrib1'>attrib1 data</TD>
    //#----          <TD class='attrib2'>attrib2 data</TD>
    //#--- -                ....
    //#---- INPUT ::
    //#----      $id:  attribute to be used within <TABLE> tag
    //#----            typically a class id for CSS format
    //#----      $attrib: attribute to be displayed
    //#----         each element is 'field'=>'description'
    //#----         field: see $this->stat list
    //#----         description: for the header row
    //#-------------------------------------------------------------------------
    function DisplayList($id="", $attrib=array())
    {   if ($attrib==NULL)
            $attrib=$this->list_detail;
        echo "<TABLE $id><TR>";
        foreach ( $attrib as $column=>$desc )
            echo "<TH class=header>$desc";
        echo "</TH>";
        if ($this->no_item > 0)
        {   for ($id = 0; $id < $this->no_item; $id ++)
            {   $i = $this->order[$id];
                echo "<TR>";
                foreach ( $attrib as $column=>$desc )
                    echo "<TD class='$column'>".$this->stat[$i][$column];
                echo "\n";
            } // for $id
        } // if no_item > 0
        echo "</TABLE>";
    }

} // End class

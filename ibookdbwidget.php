<?php
/*
Plugin Name: Internet Book Database Book Widgets
Plugin URI: http://www.ibookdb.net/forums/index.php/topic,2054.0.html
Description: Internet Book Database Bookshelf and Book Widgets
Author: Siddharth Dalal
Version: 1.3
Author URI: http://www.ibookdb.net/forums/index.php/topic,2054.0.html
*/

function widget_iBookDB_Book($args) {
  extract($args);
  echo $before_widget;
  $options = get_option('widget_iBookDB_Book');
  if($options['isbn']) echo file_get_contents('http://www.ibookdb.net/bookbadgetest.php?isbn=' . $options['isbn']);
  else echo 'No ISBN Selected';
  echo $after_widget;
}

function widget_iBookDB_Bookshelf($args) {
  extract($args);
  echo $before_widget;
  $options = get_option('widget_iBookDB_Bookshelf');
  echo '<script type="text/javascript" src="http://www.ibookdb.net/blogshelf.php?username=' . $options['username'] . '&cols=' . $options['cols'] . '&limit=' . ($options['cols']*$options['rows']) . '&smallimage=' . $options['smallimage'] . '"></script>';
  echo $after_widget;
}

function options_iBookDB_Book() {
	$options = get_option('widget_iBookDB_Book');
	if ($_POST['widget_iBookDB_Book-isbn']) {
		$isbn=trim($_POST['widget_iBookDB_Book-isbn']);
		if (strlen($isbn)==10 or strlen($isbn)==13) {
			$newoptions['isbn']=$isbn;
		}
		if($options!=$newoptions) {
			$options=$newoptions;
			update_option('widget_iBookDB_Book', $options);
		}
	
	}
	
	
	$isbn=$options['isbn'];
	
	echo '<div>
	<label for="widget_iBookDB_Book-isbn">ISBN: <input type="text" id="widget_iBookDB_Book-isbn" name="widget_iBookDB_Book-isbn" value="' . $isbn . '"/></label>
	</div>';
	
}

function options_iBookDB_Bookshelf() {
	//echo 'options<br>';
	$options = get_option('widget_iBookDB_Bookshelf');
	//print_r ($_POST);
	if ($_POST['widget_iBookDB_Bookshelf-username'] and $_POST['widget_iBookDB_Bookshelf-rows'] and $_POST['widget_iBookDB_Bookshelf-cols']) {
		//echo 'options submitted<br>';
		$newoptions['username']=$_POST['widget_iBookDB_Bookshelf-username'];
		$newoptions['rows']=$_POST['widget_iBookDB_Bookshelf-rows'];
		$newoptions['cols']=$_POST['widget_iBookDB_Bookshelf-cols'];
		$newoptions['smallimage']=$_POST['widget_iBookDB_Bookshelf-smallimage'];
		if($options!=$newoptions) {
			$options=$newoptions;
			update_option('widget_iBookDB_Bookshelf', $options);
			echo 'options saved<br>';
		}
		//echo 'after options saved<br>';
	}
	
	echo '<div>
	<label for="widget_iBookDB_Bookshelf-username">Username: <input type="text" id="widget_iBookDB_Bookshelf-username" name="widget_iBookDB_Bookshelf-username" value="' . $options['username'] . '"/></label><br>
	<label for="widget_iBookDB_Bookshelf-rows">Rows: <input type="text" id="widget_iBookDB_Bookshelf-rows" name="widget_iBookDB_Bookshelf-rows" value="' . $options['rows'] . '"/></label><br>
	<label for="widget_iBookDB_Bookshelf-cols">Cols: <input type="text" id="widget_iBookDB_Bookshelf-cols" name="widget_iBookDB_Bookshelf-cols" value="' . $options['cols'] . '"/></label><br>
	<label for="widget_iBookDB_Bookshelf-smallimage">Image Size: <select id="widget_iBookDB_Bookshelf-smallimage" name="widget_iBookDB_Bookshelf-smallimage">
		<option value="1">Small Images</option>
		<option value="0" ' . ($options['smallimage']=='0'?'selected':'') . '>Medium Images</option>
	</select></label>
	</div>';
	
}

function iBookDB_init() {
  register_sidebar_widget(__('Internet Book Database Book Widget'), 'widget_iBookDB_Book');
  register_widget_control(__('Internet Book Database Book Widget'), 'options_iBookDB_Book');
  
  register_sidebar_widget(__('Internet Book Database Bookshelf Widget'), 'widget_iBookDB_Bookshelf');
  register_widget_control(__('Internet Book Database Bookshelf Widget'), 'options_iBookDB_Bookshelf');
}

function isbn_replace($index_array) {
	//echo $index_array[0];
	return file_get_contents('http://www.ibookdb.net/bookbadgetest.php?isbn=' . str_replace(array('[ISBN:', ']'), '', $index_array[0]));
}

function iBookDB_ISBN($content) {
	$content=preg_replace_callback ('/\[ISBN:([0-9xX]+)\]/' , 'isbn_replace' , $content);
	return $content;
}

add_action("plugins_loaded", "iBookDB_init");
add_filter('the_content', 'iBookDB_ISBN');

?>
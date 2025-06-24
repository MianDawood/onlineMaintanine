<?php
require ('includes/application_top.php');

if(isset($_POST['update_bookmark'])) {
  $infos = json_decode($_POST['update_bookmark'], true);
  
  $validation = $db->Execute("SELECT * FROM bookmark WHERE title = '".$infos['title']."'");
  if ($validation->RecordCount() > 0) {
    // already bookmarked
    $remove_bookmark = $db->Execute('DELETE FROM bookmark WHERE title = "'.$infos['title'].'"');

    $response = array(
      'status' => true,
      'message' => 'removed',
    );
  } else {
    // set bookmark
    $db->Execute('INSERT INTO bookmark (title, url) VALUES ("'.$infos['title'].'", "'.$infos['url'].'")');
    $response = array(
      'status' => true,
      'message' => 'added',
    );
  }
  $bookmarks = $db->Execute("SELECT * FROM bookmark");

  $bookmarks_result = array();
  while (!$bookmarks->EOF) {
    $bookmarks_result[] = $bookmarks->fields;
    $bookmarks->MoveNext();      
  }
  $response['data'] = $bookmarks_result;
  echo json_encode($response);
  exit;
}
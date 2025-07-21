<?php
/**
 * @package admin
 * @copyright Copyright 2003-2014 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version GIT: $Id: Author: DrByte  Jun 30 2014 Modified in v1.5.4 $
 */


  ini_set('LimitRequestBody', 0);
  require('includes/application_top.php');
  

session_start();
  if(isset($_POST['showimages']))
{
  $_SESSION['showImage'] = 1;

  header('location:custom_template.php');
  exit;
}

if(isset($_POST['hideImages']))
{
  unset($_SESSION['showImage']);
  header('location:custom_template.php');
  exit;
}
  ## Get the qs urls
  $sql_qs_url = 'SELECT url FROM query_software';
  $temp_qs_url = $db->Execute($sql_qs_url);
  $qs_urls = array();
  while(!$temp_qs_url->EOF) {
    $qs_urls[] = $temp_qs_url->fields['url'];
    $temp_qs_url->MoveNext();
  }

  ## Get URI Mapping
  $pdt_cat_sql = "select *
    from ceon_uri_mappings where current_uri = 1 and main_page = 'product_info'";

  $pdt_cat = $db->Execute($pdt_cat_sql);
  
  $url_tags = array();
  while(!$pdt_cat->EOF) {
    $url_tags[] = $pdt_cat->fields['uri'];
    $pdt_cat->MoveNext();
  }
  # End

  ## Get Custom tags
  $custom_tags_sql = "select * from custom_template";

  $custom_tags_sql_result = $db->Execute($custom_tags_sql);
  
  $custom_tags = array();
  while(!$custom_tags_sql_result->EOF) {
    $tmp = str_replace('<?','{?', $custom_tags_sql_result->fields['custom_tags']);
    $custom_tags[] = str_replace('?>','?}', $tmp);
    $custom_tags_sql_result->MoveNext();
  }
  
  # End
  ## Get all custom templates 
  // $files = array(); 
  // $files[0] = array('id' => 0, 'text' => '--choose the template--');
  
  // foreach (scandir('../../cdn/custom_template/') as $id => $file) {
  //     if (
  //         $file != '.' && $file != '..' &&
  //         $file != 'css' && $file != 'local_Files' &&
  //         $file != 'backup' && $file != 'js' && $file != 'images'
  //     ) {
  //         preg_match_all('!\d+!', $file, $matches);
          
  //         $idVal = isset($matches[0][0]) ? $matches[0][0] : uniqid(); // fallback ID
  
  //         $files[] = array(
  //             'id' => $idVal,
  //             'text' => $file,
  //             'status' => $_SESSION['showImage'] ?? '' // avoid undefined index
  //         );
  //     }
  // }

  $files = array(); 
$files[] = array('id' => 0, 'text' => '--choose the template--');

// Fetch templates from database
$template_sql = "SELECT id, template_name FROM custom_template_file ORDER BY template_name ASC";
$templates = $db->Execute($template_sql);

while (!$templates->EOF) {
    $files[] = array(
        'id' => $templates->fields['id'],
        'text' => $templates->fields['template_name'],
        'status' => $_SESSION['showImage'] ?? ''  // Retain status logic if needed
    );
    $templates->MoveNext();
}

  

  ## Get the Meta Title, Description and Keywords from meta_tags_template table
  $meta_data_sql = "SELECT * FROM meta_tags_template";
  $meta_data = $db->Execute($meta_data_sql);
  
  $meta_title_lists = array();
  $meta_desc_lists = array();
  $meta_key_lists = array();
  while(!$meta_data->EOF) {
    $meta_title_lists[] = $meta_data->fields['title'];
    $meta_desc_lists[] = rtrim($meta_data->fields['description']);
    $meta_key_lists[] = $meta_data->fields['keywords'];
    $meta_data->MoveNext();
  }

  $subject_sql = 'SELECT subject, paragraph, custom_tags FROM reviews_template ORDER BY id ASC';
  $subjects = $db->Execute($subject_sql);
  $paragraph_lists = array();
  while(!$subjects->EOF) {
    $paragraph_lists[] = $subjects->fields['paragraph'];
    $subjects->MoveNext();
  }
  
  if(isset($_POST['img_val'])) {
    //Get the base-64 string from data
    $filteredData=substr($_POST['img_val'], strpos($_POST['img_val'], ",")+1);

    //Decode the string
    $unencodedData=base64_decode($filteredData);

    $img_name = str_replace('.php', '.png', $_POST['template_name']);
    //Save the image
    file_put_contents('../../cdn/images/custom_template_screenshot/'.$img_name, $unencodedData);
    echo "success";
    exit;
  }
  
  if(isset($_POST['upload_template'])) {
    $template_infos = json_decode($_POST['upload_template'], true);
    $template_name = $template_infos['template_name'];
    $template_code = $template_infos['template_code'];
    $template_css = $template_infos['template_css'];
    
    $template_old_name = '';
    if(array_key_exists('old_template_name', $template_infos)) {
      $template_old_name = $template_infos['old_template_name'];
    }
    
    ## Remove the original file name for re create or update with other file name
    if($template_old_name != '') {
      $remove_file = '../../cdn/custom_template/'.$template_old_name.'.php';
      if (file_exists($remove_file)) {
          unlink($remove_file);
      } else {
          // File not found.
      }
    }
    

    ## Get the template names existed on the site already
    $remote_url = '../../cdn/custom_template/'.$template_name;
    echo $remote_url; exit;
    foreach(glob('../../cdn/custom_template/*.*') as $file) {
      if(strpos($file, $template_name) !== false ) {
        echo json_encode(array("result"=>"failed","message"=>"Sorry, template name is already exisited. Please change the template name."));
        exit;
      }
    }
    // upload the template html code
    if (file_put_contents('../../cdn/custom_template/'.$template_name, $template_code) !== false) {
        echo json_encode(array("result"=>"success","message"=>"Success, File created (" . basename($template_name) . ") at 'cdn/custom_template/' directory"));
    } else {
        echo json_encode(array("result"=>"failed","message"=>"Cannot create file (" . basename($template_name) . ")"));
    }
    
    // upload the template css file
    $css_file_name = str_replace('.php', '.css', $template_name);
    if (file_put_contents('../../cdn/custom_template/css/'.$css_file_name, $template_css) !== false) {
//         echo json_encode(array("result"=>"success","message"=>"Success, File created (" . basename($template_name) . ") at 'cdn/custom_template/' directory"));
    } else {
//         echo json_encode(array("result"=>"failed","message"=>"Cannot create file (" . basename($template_name) . ")"));
    }
    exit;
  }

  if(isset($_POST['insert_design_block'])) {
    $tmp_infos = json_decode($_POST['insert_design_block'], true);
    $tmp_name = $tmp_infos['template_name'];
    $tmp_code = $tmp_infos['template_code'];
    $tmp_screenshot = $tmp_infos['screenshot'];
    list($type, $tmp_screenshot) = explode(';', $tmp_screenshot);
    list(, $tmp_screenshot)      = explode(',', $tmp_screenshot);
    $data = base64_decode($tmp_screenshot);

    $added_tmp_name = str_replace('.php', '', $tmp_name);
    
    file_put_contents('../../cdn/design_block/photos/'.$added_tmp_name.'.png', $data);
    $screenshot_name = $added_tmp_name.'.png';
    
    $old_contents = file_get_contents('../../cdn/design_block/js/pages_design_blocks_pkgd.js', true);
    $added_contents = '),n.RegisterDesignBlock("Custom Template", `<section class="fdb-block">\n  <div class="container">\n'.$tmp_code.'</div>\n</section>\n`, "/'.$screenshot_name.'");})';
    $new_contents = str_replace(');})', $added_contents ,$old_contents);
    if (file_put_contents('../../cdn/design_block/js/pages_design_blocks_pkgd.js', $new_contents) !== false) {
        echo json_encode(array("result"=>"success","message"=>"Success, custom template inserted into the design block"));
    } else {
        echo json_encode(array("result"=>"failed","message"=>"Cannot insert this custom template"));
    }
    exit;
  }

  if(isset($_POST['backup_temp'])) {
    $tmp_name = json_decode($_POST['backup_temp']);
    $targetBackupFile = '../../cdn/custom_template/'.$tmp_name;
    
    if (file_exists($targetBackupFile)) {
      $backup_filename = str_replace('.php', '('.substr(date("YmdHisu"), 0, -3).').php', $tmp_name);
      $BackupFile = '../../cdn/custom_template/backup/'.$backup_filename;
      copy($targetBackupFile, $BackupFile);
    }
    echo json_encode('success');
    exit;
  }

  if(isset($_POST['backup_css_temp'])) {
    $tmp_name = json_decode($_POST['backup_css_temp']);
    $tmp_css_name  =str_replace('.php', '.css', $tmp_name);
    $targetBackupFile = '../../cdn/custom_template/css/'.$tmp_css_name;
    
    if (file_exists($targetBackupFile)) {
      $backup_filename = str_replace('.css', '('.substr(date("YmdHisu"), 0, -3).').css', $tmp_css_name);
      $BackupFile = '../../cdn/custom_template/css/css_backup/'.$backup_filename;
      copy($targetBackupFile, $BackupFile);
    }
    echo json_encode('success');
    exit;
  }

  if(isset($_POST['remove_temp'])) {
    $tmp_name = json_decode($_POST['remove_temp']);
    $remove_file = '../../cdn/custom_template/'.$tmp_name;
    if (file_exists($remove_file)) {
        unlink($remove_file);
    }
    $remove_css_file = '../../cdn/custom_template/css/'.str_replace('.php', '.css', $tmp_name);
    if (file_exists($remove_css_file)) {
        unlink($remove_css_file);
    }
    echo json_encode('success');
    exit;
  }

  if(isset($_POST['create_thumbnail'])) {
    $tmp_infos = json_decode($_POST['create_thumbnail'], true);
    $tmp_name = $tmp_infos['template_name'];
    $tmp_screenshot = $tmp_infos['screenshot'];
    list($type, $tmp_screenshot) = explode(';', $tmp_screenshot);
    list(, $tmp_screenshot)      = explode(',', $tmp_screenshot);
    $data = base64_decode($tmp_screenshot);

    $added_tmp_name = str_replace('.php', '', $tmp_name);
    file_put_contents('../../cdn/design_block/photos/'.$added_tmp_name.'.png', $data);
    echo json_encode('success');
    exit;
  }

  if(isset($_POST['pull_template_name'])) {
    
    $template_name = $_POST['pull_template_name'];
    $contents = file_get_contents('../../cdn/custom_template/'.$template_name, true);
  
    // Global CSS check
    $aquery = "SELECT * FROM custom_template_file WHERE template_name = '" . $template_name . "' AND global = 1";
    $aquery_result = $db->Execute($aquery);

    $custom_files = array();
    while(!$aquery_result->EOF) {
        $custom_files[] = $aquery_result->fields['file_name'];
        $aquery_result->MoveNext();
    }

    if($aquery_result->RecordCount() > 0) {
        $global_css_contents = file_get_contents('../../cdn/custom_template/css/'.$custom_files[0], true);
    } else {
        $global_css_contents = "";
    }

    $css_file_name = str_replace('.php', '.css', $template_name);
    $css_contents = file_get_contents('../../cdn/custom_template/css/'.$css_file_name, true);

    $js_file_name = str_replace('.php', '.js.php', $template_name);
    $js_contents = file_get_contents('../../cdn/custom_template/js/'.$js_file_name, true);

    echo json_encode(array(
        'result' => 'success',
        'html' => $contents,
        'css' => $css_contents,
        'js' => $js_contents,
        'files' => $custom_files,
        'global_contents' => $global_css_contents
    ));
    exit;
}

  
  if(isset($_POST['update_custom_template'])) {
    $template_infos = json_decode($_POST['update_custom_template'], true);
    $template_code = $template_infos['template_code'];
    $old_temp_name = $template_infos['old_temp_name'];
    $updated_temp_name = $template_infos['updated_temp_name'];
    
    $remove_file = '../../cdn/custom_template/'.$old_temp_name;
    if (file_exists($remove_file)) {
        unlink($remove_file);
    } else {
        // File not found.
    }    
    
    /* Move over the individual css file upload function 
    
    $old_css_name = str_replace('.php', '.css', $old_temp_name);
    $new_css_name = str_replace('.php', '.css', $updated_temp_name);
    if (file_exists('../../cdn/custom_template/css/'.$old_css_name)) {
        if($old_css_name != $new_css_name) {
          copy('../../cdn/custom_template/css/'.$old_css_name, '../../cdn/custom_template/css/'.$new_css_name);
          unlink('../../cdn/custom_template/css/'.$old_css_name);
        }
    } else {
        // File not found.
    } 
    */
    
    $old_js_name = str_replace('.php', '.js.php', $old_temp_name);
    $new_js_name = str_replace('.php', '.js.php', $updated_temp_name);
    if (file_exists('../../cdn/custom_template/js/'.$old_js_name)) {
        if($old_js_name != $new_js_name) {
          copy('../../cdn/custom_template/js/'.$old_js_name, '../../cdn/custom_template/js/'.$new_js_name);
          unlink('../../cdn/custom_template/js/'.$old_js_name);
        }
    } else {
        // File not found.
    } 
    
    ## Get the template names existed on the site already
    $remote_url = '../../cdn/custom_template/'.$updated_temp_name;
    foreach(glob('../../cdn/custom_template/*.*') as $file) {
      if(strpos($file, $updated_temp_name) !== false ) {
        echo json_encode(array("result"=>"failed","message"=>"Sorry, template name is already exisited. Please change the template name."));
        exit;
      }
    }
    if (file_put_contents('../../cdn/custom_template/'.$updated_temp_name, $template_code) !== false) {
        echo json_encode(array("result"=>"success","message"=>"Success, File created (" . basename($updated_temp_name) . ") at 'cdn/custom_template' directory"));
    } else {
        echo json_encode(array("result"=>"failed","message"=>"Cannot create file (" . basename($updated_temp_name) . ")"));
    }
    exit;
  }

  if(isset($_POST['clone_custom_template'])) {
    $template_infos = json_decode($_POST['clone_custom_template'], true);
    $template_code = $template_infos['template_code'];
    $old_temp_name = $template_infos['old_temp_name'];
    $updated_temp_name = $template_infos['updated_temp_name'];
    
    /**
     * Check the Global CSS file existance
     **/
    $global_css_query = 'SELECT * FROM custom_template_file WHERE template_name = "'.$old_temp_name.'"';
    $global_css = $db->Execute($global_css_query);
    $globalCSS = '';
    if($global_css->RecordCount() > 0) {
      while(!$global_css->EOF) {
        $globalCSS = $global_css->fields['file_name'];
        $global_css->MoveNext();
      }
    }

    ## Get the template names existed on the site already
    $remote_url = '../../cdn/custom_template/'.$updated_temp_name;
    foreach(glob('../../cdn/custom_template/*.*') as $file) {
      if(strpos($file, $updated_temp_name) !== false ) {
        echo json_encode(array("result"=>"failed","message"=>"Sorry, template name is already exisited. Please change the template name."));
        exit;
      }
    }
    
    if($globalCSS == '') {
      $old_css_name = str_replace('.php', '.css', $old_temp_name);
      $new_css_name = str_replace('.php', '.css', $updated_temp_name);
    } else {
      $new_css_name = '';
    }
    
    
    $old_js_name = str_replace('.php', '.js.php', $old_temp_name);
    $new_js_name = str_replace('.php', '.js.php', $updated_temp_name);
    
    $targetCloneFile = '../../cdn/custom_template/'.$old_temp_name;
    
    $message = '';
    
    if (file_exists($targetCloneFile)) {
      $cloneFile = '../../cdn/custom_template/'.$updated_temp_name;
      copy($targetCloneFile, $cloneFile);
      $message = "Cloned the PHP file. ";
    } else {
      $message = "Can not clone the PHP file for some reason.";
    }
    
    if($new_css_name != '') {
      $targetCloneCSSFile = '../../cdn/custom_template/css/'.$old_css_name;
      if (file_exists($targetCloneCSSFile)) {
        $cloneCSSFile = '../../cdn/custom_template/css/'.$new_css_name;
        copy($targetCloneCSSFile, $cloneCSSFile);
        $message .= " && Cloned the CSS file. ";
      } else {
        $message .= " && Can not clone the CSS file for some reason.";
      }
    } else {
      $insert_file = "INSERT INTO custom_template_file (template_name, file_name, global) VALUES ('".$updated_temp_name."' , '".$globalCSS."', 1)";
      $db->Execute($insert_file);
    }
    
    
    $targetCloneJSFile = '../../cdn/custom_template/js/'.$old_js_name;
    if (file_exists($targetCloneJSFile)) {
      $cloneJSFile = '../../cdn/custom_template/js/'.$new_js_name;
      copy($targetCloneJSFile, $cloneJSFile);
      $message .= " && Cloned the JS file. ";
    } else {
      $message .= " && Can not clone the JS file for some reason.";
    }
    echo json_encode(array("result"=>$message));
    exit;
  }
  if(isset($_POST['save_custom_template_css'])) {
    $template_infos = json_decode($_POST['save_custom_template_css'], true);
    $template_css_code = $template_infos['template_css_code'];
    $template_name = $template_infos['template_name'];
    $cssfileName = $template_infos['cssfileName'];
    $uploadGlobalMode = $template_infos['uploadGlobalMode'];
    if($uploadGlobalMode) {
      $valid = $db->Execute("SELECT * FROM custom_template_file WHERE template_name = '" . $template_name . "' AND global = 1");
      if($valid->RecordCount() > 0) {
        $asql = "UPDATE custom_template_file SET file_name = '" . $cssfileName . "', global = 1 WHERE template_name = '" . $template_name . "'";
        $db->Execute($asql);
      } else {
        $asql = "INSERT INTO custom_template_file (template_name, file_name, global) VALUES ('".$template_name."', '".$cssfileName."', 1)";
        $db->Execute($asql);
      }
    }    
  }
  if(isset($_POST['pull_global_css'])) {
    $template_infos = json_decode($_POST['pull_global_css'], true);
    $cssfileName = $template_infos['cssfileName'];
    if(file_exists('../../cdn/custom_template/css/'.$cssfileName)) {
      $cssGlobalcontents = file_get_contents('../../cdn/custom_template/css/'.$cssfileName, true);
      echo json_encode(array("result"=>"success","css"=>$cssGlobalcontents));
      exit;
    } else {
      echo json_encode(array("result"=>"failed"));
      exit;
    }
  }
  
  if(isset($_POST['update_custom_template_css'])) {
    $template_infos = json_decode($_POST['update_custom_template_css'], true);
    $template_css_code = $template_infos['template_css_code'];
    $template_name = $template_infos['template_name'];
    $cssfileName = $template_infos['cssfileName'];
    $uploadGlobalMode = $template_infos['uploadGlobalMode'];
    
    if($uploadGlobalMode) {

      $valid = $db->Execute("SELECT * FROM custom_template_file WHERE template_name = '" . $template_name . "' AND global = 1");
      if($valid->RecordCount() > 0) {
        $asql = "UPDATE custom_template_file SET file_name = '" . $cssfileName . "', global = 1 WHERE template_name = '" . $template_name . "'";
      } else {
        $asql = "INSERT INTO custom_template_file (template_name, file_name, global) VALUES ('".$template_name."', '".$cssfileName."', 1)";
      }
      $db->Execute($asql);
        
      if (file_put_contents('../../cdn/custom_template/css/'.$cssfileName, $template_css_code) !== false) {
        echo json_encode(array("result"=>"success","message"=>"Success! Global CSS File is uploaded (" . basename($cssfileName) . ") at 'cdn/custom_template/css' directory"));
      } else {
        echo json_encode(array("result"=>"failed","message"=>"Cannot create file (" . basename($template_css_name) . ")"));
      }
      exit;
      
    } else {
      $template_css_name = str_replace('.php', '.css', $template_name);
      if (file_put_contents('../../cdn/custom_template/css/'.$template_css_name, $template_css_code) !== false) {
        $bsql = $db->Execute("DELETE FROM custom_template_file WHERE template_name = '" . $template_name . "' AND global = 1");
        echo json_encode(array("result"=>"success","message"=>"Success! Local CSS File is uploaded (" . basename($template_css_name) . ") at 'cdn/custom_template/css' directory"));
      } else {
        echo json_encode(array("result"=>"failed","message"=>"Cannot create file (" . basename($template_css_name) . ")"));
      }
    }
    exit;
  }
  
  if(isset($_POST['update_custom_template_js'])) {
    $template_infos = json_decode($_POST['update_custom_template_js'], true);
    $template_js_code = $template_infos['template_js_code'];
    $template_name = $template_infos['template_name'];
    $template_js_name = str_replace('.php', '.js.php', $template_name);
    if (file_put_contents('../../cdn/custom_template/js/'.$template_js_name, $template_js_code) !== false) {
        echo json_encode(array("result"=>"success","message"=>"Success, File updated (" . basename($template_js_name) . ") at 'cdn/custom_template/js' directory"));
    } else {
        echo json_encode(array("result"=>"failed","message"=>"Cannot create file (" . basename($template_js_name) . ")"));
    }
    exit;
  }

?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo HEADING_TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">

<!-- Load custom stylesheet only for this page -->

<link rel="stylesheet" type="text/css" href="includes/cssjsmenuhover.css" media="all" id="hoverJS">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
<script language="javascript" src="includes/menu.js"></script>
<script language="javascript" src="includes/general.js"></script>

<link href="../froala-editor/css/froala_editor.pkgd.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.3.0/codemirror.min.css">
<!-- Include the plugin CSS file. -->
<link rel="stylesheet" href="../froala-editor/css/plugins/code_view.min.css">
  
<!-- Include TUI CSS. -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tui-image-editor@3.2.2/dist/tui-image-editor.css">
<link rel="stylesheet" href="https://uicdn.toast.com/tui-color-picker/latest/tui-color-picker.css">

<!-- Include TUI Froala Editor CSS. -->
<link rel="stylesheet" href="../froala-editor/css/third_party/image_tui.min.css">  
  
 

<script type="text/javascript">

  function init()
  {
    cssjsmenu('navbar');
    if (document.getElementById)
    {
      var kill = document.getElementById('hoverJS');
      kill.disabled = true;
    }
if (typeof _editor_url == "string") HTMLArea.replaceAll();
 }
 // -->
</script>
<?php
if (
    isset($action, $editor_handler) &&
    $action !== 'new_product_meta_tags' &&
    $editor_handler !== ''
) {
    include($editor_handler);
}
?>

</head>
<body id="custom_template_page" style="position:relative;" marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onLoad="init()">
  <!-- <?php
  //require('drabble-search-component.php');
  ?> -->
<div class="loading-container hidden">
    <div class="loading"></div>
    <div id="loading-text">loading</div>
</div>
<div id="spiffycalendar" class="text"></div>
<!-- header //-->
<?php
require(DIR_WS_INCLUDES . 'header.php'); ?>
  
  <div id="main-wrapper">
  <input type="hidden" id="tab_tmp_mode" value=""/>
  <h1 class="pageHeading"><?php echo HEADING_TITLE; ?></h1>
  <div class="template_section" style="padding:5px 10px;">
    <div class="upload_template_section">
      <div class="form">
      
        <ul class="tab-group">
          <li class="tab active"><a href="#create_tmp">Create Template</a></li>
          <li class="tab"><a href="#upload_tmp">Upload Template</a></li>
        </ul>
        
        <div class="tab-content">
          <div id="create_tmp">   
            <i>*create a custom template from Design Blocks</i>   
      
            <fieldset>
              <legend>Template Name</legend>
              Name your Template: <input type="text" class="template_name" placeholder="custom_template" /> <i>(* template name should be like this <b>custom_template1, custom_template2,...</b>)</i>
              <input type="hidden" class="old_template_name" value="" />
              <button id="btn_create_tmp">Create template from design block ></button>
            </fieldset>
            
          </div>
          <div id="upload_tmp">   
      
            <fieldset>
              <legend>Template Name</legend>
              Name your Template: <input type="text" class="template_name" /> <br><i>(* template name should be like this <b>custom_template1.php, custom_template2.php,... DO NOT forget to add the file extension(.php) to the end of file name</b>)</i>
              <input type="hidden" class="old_template_name" value="" />
            </fieldset>

            <fieldset>
                <legend>Code your own</legend>
                <div class="code_own_part" style="display: flex;">
                  <div class="paste_code">
                    <i class="fa fa-code" aria-hidden="true"></i>
                    <span>Paste in code</span>
                    <p>
                      Create a template by pasting your custom code design.
                    </p>
                  </div>
                  <div class="import_html">
                    <i class="fa fa-cloud-upload" aria-hidden="true"></i>
                    <span>Import HTML</span>
                    <p>
                      Create a template by uploading an HTML file with your template code.
                    </p>
                  </div>
                  <div class="import_zip">
                    <i class="fa fa-cloud-upload" aria-hidden="true"></i>
                    <span>Import zip</span>
                    <p>
                      Create a template by uploading a zip file with your template code.
                    </p>
                  </div>
                </div>
            </fieldset>

            <fieldset id="preview_section" class="template_info">
              <legend>Preview Uploaded Template</legend>
              <textarea id="summernote" name="editordata"></textarea>
              <button class="save_template_css_code hidden">Upload CSS ></button>
              <button class="save_template_code hidden">Save and Preview ></button>
              <button class="update_template_code hidden">Update template ></button>
            </fieldset>
          </div>          
        </div>
      </div>
      
    </div>
    <div class="preview_template_section">
      <h3>Customize the Template: </h3>
      <form action="custom_template.php" method="post">
  <?php if(isset($_SESSION['showImage']))
  { ?>
    <input type="submit" style="background-color: #3498db; color: white; padding: 5px 7px; text-align: center; text-decoration: none; display: inline-block; font-size: 12px; margin: 1px 1px; cursor: pointer; border-radius: 2px;" name="hideImages" value="Hide Images">
<?php } else{?>
  <input type="submit" style="background-color: #4CAF50; color: white; padding: 5px 7px; text-align: center; text-decoration: none; display: inline-block; font-size: 12px; margin: 1px 1px; cursor: pointer; border-radius: 2px;" name="showimages" value="Show Images">
  <?php } ?>
    </form>
  <div class="row">
    <div class="col-lg-6">
    <div class="choose-template" style="margin: 10px 0;">
  <label for="choose_template" style="font-weight: bold; display: block; margin-bottom: 5px;">
    Choose the template to customize:
  </label>
  <select name="choose_template" id="choose_template" style="width:100%;">
  <option></option> <!-- needed for placeholder support -->
  <?php 

    foreach($files as $file) {
      if($file['text'] == '--choose the template--') {
        echo "<option id='".$file['id']."'>".$file['text']."</option>";
      } else {
        $screenshot_name = str_replace('.php','.png', $file['text']);
        if (file_exists ( '../../cdn/design_block/photos/'.$screenshot_name) ) {
          if($file['status'] == 1)
          {
            echo "<option data-image='../../cdn/design_block/photos/".$screenshot_name."' id='".$file['id']."'>".$file['text']."</option>";

          }
          else
          {
            echo "<option  id='".$file['id']."'>".$file['text']."</option>";

          }
        } else {
          if($file['status'] == 1)
          {
            echo "<option data-image='../../cdn/design_block/photos/default.jpg' id='".$file['id']."'>".$file['text']."</option>";

          }
          else
          {
            echo "<option id='".$file['id']."'>".$file['text']."</option>";

          }
        }
      }
    }
  ?>
</select>

</div>
    </div>

    <div class="col-lg-12"></div>
  </div>

      <div class="link_stylesheet"></div>
      <div class="link_script"></div>
      <div class="search_title" id="draggableBox">
	      <div class="draggableSearch" id="draggableBoxSearch">Click here to move</div>
        <input type="text" name="search_title" class="title_keyword" value="" placeholder="FILTER TITLE:"></br>
        <input type="text" name="search_parag" class="parag_keyword" value="" placeholder="FILTER PARAGRAPH:"></br>
        <input type="text" name="search_meta_title" class="meta_title_keyword" value="" placeholder="FILTER META TITLE:"></br>
        <input type="text" name="search_meta_des" class="meta_des_keyword" value="" placeholder="FILTER META DESCRIPTION:"></br>
        <input type="text" name="search_qs_urls" class="filter_qs_urls" value="" placeholder="FILTER QS URLS:"></br>
        <input type="text" name="search_urls" class="filter_urls" value="" placeholder="FILTER URLS:">
      </div>
      <fieldset id="modify_section" class="template_info">
        <legend>Custom Template</legend>
        <div style="margin-bottom: 10px;" >
          <input class="find"  type="text" placeholder="Find">
          <input class="replace" type="text" placeholder="Replace">
          <button class="find_replace_go" >find</button>
          <button class="find_replace_running" >Replace</button>
          <button class="refresh_page" style="float: right;">Refresh</button>
        </div>
        <textarea id="template_preview" name="content"></textarea>
        <button class="create_thumb"><i class="fa fa-camera-retro"></i>&nbsp;Create a thumbnail ></button>
        <button class="show_template_css_code"><i class="fa fa-wrench"></i>&nbsp;Check CSS ></button>
        <button class="backup_template_css_code"><i class="fa fa-database"></i>&nbsp;Backup CSS ></button>
        <button class="show_template_js_code"><i class="fa fa-wrench"></i>&nbsp;Check JS ></button>
        <button class="update_original_template_code"><i class="fa fa-cloud-upload"></i>&nbsp;Update template ></button>
        <button class="clone_template_code"><i class="fa fa-copy"></i>&nbsp;Clone ></button>
        <button class="backup_template_code"><i class="fa fa-database"></i>&nbsp;Backup ></button>
        <button class="remove_template_code"><i class="fa fa-trash"></i>&nbsp;Remove ></button>
        <button class="add_template_design_block">Insert this template into design block ></button>
        <button class="reload_template_design_block">Reload ></button>
        <button class="translate_template" style="color:#e8e8e8; background:#000;float:left; margin: 5px 0;font-size: 11px;padding: 5px 10px;font-weight: 700; cursor: pointer;">Translate ></button>
        <form method="POST" enctype="multipart/form-data" action="#" id="myForm">
            <input type="hidden" name="img_val" id="img_val" value="" />
        </form>
        <div id="img_msg"></div>
        <h2 class="toCanvas"> <a href="javascript:void(0);" class="btn btn-danger"></a></h2>
      </fieldset>
    </div>
  </div>
</div>

<div class="modal" id="update_template_modal">
  <div class="modal-sandbox"></div>
  <div class="modal-box">
    <div class="modal-header">
      <div class="close-modal">&#10006;</div> 
      <h1></h1>
    </div>
    <div class="modal-body">
      <div class="row">
        <div class="temp_info_section">
          <h3 style="display: flex;"> Please confirm the template name.</h3>
          <i style="display:flex;">
            Want to change the template name:
            <input type="text" name="template_name" id="template_name" value="" />
            <input type="hidden" name="old_template_name" id="old_template_name" value="" />
          </i>
        </div>
        <br />
      </div>
      <br />
      <a href="javascript:void(0)"  class="close-modal" style="float: right;"><img src="includes/languages/english/images/buttons/button_cancel.gif" border="0" alt="Cancel" title=" Cancel "></a>
      <?php echo zen_image_submit('button_update.gif', IMAGE_UPDATE); ?>
      </form>
    </div>
  </div>
</div>  

<div class="modal" id="clone_template_modal">
  <div class="modal-sandbox"></div>
  <div class="modal-box">
    <div class="modal-header">
      <div class="close-modal">&#10006;</div> 
      <h1></h1>
    </div>
    <div class="modal-body">
      <div class="row">
        <div class="temp_info_section">
          <h3 style="display: flex;"> Please confirm the cloned template name.</h3>
          <i style="display:flex;">
            <input type="text" name="template_name" id="template_name" value="" style="width:70%;"/>
            <input type="hidden" name="old_template_name" id="old_template_name" value="" />
          </i>
        </div>
        <br />
      </div>
      <br />
      <a href="javascript:void(0)"  class="close-modal" style="float: right;"><img src="includes/languages/english/images/buttons/button_cancel.gif" border="0" alt="Cancel" title=" Cancel "></a>
      <?php echo zen_image_submit('button_update.gif', IMAGE_UPDATE); ?>
      </form>
    </div>
  </div>
</div>  


<div class="modal" id="create_template_modal">
  <div class="modal-sandbox"></div>
  <div class="modal-box">
    <div class="modal-header hidden">
      <div class="close-modal">&#10006;</div> 
      <h1></h1>
    </div>
    <div class="modal-body">
      <div class="row">
        <div class="temp_info_section">
          <!-- <div class="design_stylesheet">
            <link href="../../cdn/custom_template/css/custom_template1.css" rel="stylesheet" type="text/css">
          </div> -->
          <iframe class="template_info" name="myframe" id="design_block" src="../../cdn/design_block/index.php"></iframe>
        </div>
        <br />
      </div>
      <br />
      <a href="javascript:void(0)"  class="close-modal" style="float: right;"><img src="includes/languages/english/images/buttons/button_cancel.gif" border="0" alt="Cancel" title=" Cancel "></a>
      <?php echo zen_image_submit('button_update.gif', IMAGE_UPDATE); ?>
      </form>
    </div>
  </div>
</div>

<div class="modal" id="create_template_css_modal">
  <div class="modal-sandbox"></div>
  <div class="modal-box">
    <div class="modal-header hidden">
      <div class="close-modal">&#10006;</div> 
      <h1></h1>
    </div>
    <div class="modal-body">
      <div class="row">
        <textarea id="tmp_css_code_section"></textarea>
        <br />
      </div>
      <br />
      <a href="javascript:void(0)"  class="close-modal" style="float: right;"><img src="includes/languages/english/images/buttons/button_cancel.gif" border="0" alt="Cancel" title=" Cancel "></a>
      <?php echo zen_image_submit('button_update.gif', IMAGE_UPDATE); ?>
      </form>
    </div>
  </div>
</div>

<div class="modal" id="update_template_css_modal">
  <div class="modal-sandbox"></div>
  <div class="modal-box">
    <div class="modal-header hidden">
      <div class="close-modal">&#10006;</div> 
      <h1></h1>
    </div>
    <div class="modal-body">
      <div class="row">
        <div class="form-group" style="display:flex;">
          <input type="radio" name="global_css_mode" id="global_css">
          <lable for="global_css">Global CSS Link</lable>
          <input type="text" id="global_css_link" >
          <i class="fa fa-check-circle validGlobal"></i>
        </div>
        <div class="form-group">
          <input type="radio" name="global_css_mode" id="local_css">
          <lable for="local_css">Local CSS Link</lable>
          <input type="text" id="local_css_link" readonly>
        </div>
      </div>
      <div class="row">
        <textarea id="stylesheet_section"></textarea>
        <textarea id="global_stylesheet_section_hidden"></textarea>
        <textarea id="local_stylesheet_section_hidden"></textarea>
        <br />
      </div>
      <br />
      <a href="javascript:void(0)"  class="close-modal" style="float: right;"><img src="includes/languages/english/images/buttons/button_cancel.gif" border="0" alt="Cancel" title=" Cancel "></a>
      <?php echo zen_image_submit('button_update.gif', IMAGE_UPDATE); ?>
      </form>
    </div>
  </div>
</div>

<div class="modal" id="update_template_js_modal">
  <div class="modal-sandbox"></div>
  <div class="modal-box">
    <div class="modal-header hidden">
      <div class="close-modal">&#10006;</div> 
      <h1></h1>
    </div>
    <div class="modal-body">
      <div class="row">
        <textarea id="script_section"></textarea>
        <br />
      </div>
      <br />
      <a href="javascript:void(0)"  class="close-modal" style="float: right;"><img src="includes/languages/english/images/buttons/button_cancel.gif" border="0" alt="Cancel" title=" Cancel "></a>
      <?php echo zen_image_submit('button_update.gif', IMAGE_UPDATE); ?>
      </form>
    </div>
  </div>
</div>

<div id="loader" style="display:none;"><h3>Please Wait ....</h3></div>



<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br />
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>

<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js"></script> -->
<script src="https://netdna.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.js"></script> 

<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js"></script>  -->
<script src="https://cdn.jsdelivr.net/jquery.validation/1.19.5/jquery.validate.min.js"></script>

<!-- For screenshot  -->

<!-- <script src="js/jquery.dd.js"></script> -->
<!-- <script type="text/javascript" src="../html2canvas/dist/html2canvas.js"></script> -->
<script type="text/javascript" src="../html2canvas/html2canvas.min.js"></script>
<script type="text/javascript" src="../html2canvas/canvas2image.js"></script>
<script type="text/javascript" src="../html2canvas/canvas-getsvg.js"></script>

<!-- include summernote css/js -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote.js"></script>

<script type="text/javascript" src="../froala-editor/js/froala_editor.pkgd.js"></script>  
<script type="text/javascript" src="../froala-editor/js/plugins/code_view.min.js"></script> 
<script type="text/javascript" src="../froala-editor/js/third_party/font_awesome.min.js"></script> 

<!-- Include TUI JS. -->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/1.6.7/fabric.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/tui-code-snippet@1.4.0/dist/tui-code-snippet.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/tui-image-editor@3.2.2/dist/tui-image-editor.min.js"></script>

<!-- Include TUI plugin. -->
<script type="text/javascript" src="../froala-editor/js/third_party/image_tui.js"></script>  

<script type="text/javascript">
  var buttonClick = 0;
  $(document).ready(function(){
  
    
    function escapeRegExp(string){
      return string.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
    }
    let find = document.querySelector(".find")
    let replace = document.querySelector(".replace")
    let go = document.querySelector(".find_replace_go")
    let find_replace = document.querySelector(".find_replace_running")

    find_replace.onclick = function() {
      let html =  window.editor.html.get();
      var string = $('.highlight').html();
      if(string != '' && replace.value != '') {
        html = html.replace('<span class="highlight">' + string + '</span>', replace.value);
        window.editor.html.set(html)
      }
    }
    
    go.onclick = function () {
      buttonClick++;
      let html =  window.editor.html.get();
      var hightlightedString = $('.highlight').html();
      
      if(hightlightedString != '')
        html = html.replace('<span class="highlight">' + hightlightedString + '</span>', hightlightedString);
      
      var searchKey = html.split(new RegExp(escapeRegExp(find.value),"i"));
      if(buttonClick > (searchKey.length-1)) buttonClick = 0;
      var highlightHtml = '';
      
      $.each(searchKey, function(index, value) { 
        if (index == (buttonClick-1)) highlightHtml += value + '<span class="highlight">' + find.value + '</span>';
        else {
          if(index == searchKey.length - 1) { 
            highlightHtml += value;
          } else {
            highlightHtml += value + find.value;
          }
        }
      })
      window.editor.html.set(highlightHtml)
      return false;
      html = html.replace( new RegExp(find.value,"g") , replace.value )
      window.editor.html.set(html)
    }
    
    
  
    
    $('#summernote').summernote({
        placeholder: 'Preview the custom template here...',
        tabsize: 2,
        height: 600,
        width: 800
    });
    $('#summernote_preview').summernote({
        tabsize: 2,
        height: 600,
        width: 800
    });
    window.editor = new FroalaEditor('#template_preview', 
     {
      key: "Ne2C1sF4D3C3A14A7D9jF1QUg1Xc2OZE1ABVJRDRNGGUH1ITrA1C7A6F6E1E4H4E1A9C6==",
      // Set custom buttons.
      toolbarButtons: {
        'moreText': {
          'buttons': ['bold', 'italic', 'underline', 'strikeThrough', 'subscript', 'superscript', 'fontFamily', 'fontSize', 'textColor', 'backgroundColor', 'inlineClass', 'inlineStyle', 'clearFormatting']
        },
        'moreParagraph': {
          'buttons': ['alignLeft', 'alignCenter', 'formatOLSimple', 'alignRight', 'alignJustify', 'formatOL', 'formatUL', 'paragraphFormat', 'paragraphStyle', 'lineHeight', 'outdent', 'indent', 'quote']
        },
        'moreRich': {
          'buttons': ['insertLink', 'insertImage', 'insertVideo', 'insertTable', 'emoticons', 'fontAwesome', 'specialCharacters', 'embedly', 'insertFile', 'insertHR']
        },
        'moreMisc': {
          'buttons': ['undo', 'redo', 'fullscreen', 'print', 'getPDF', 'spellChecker', 'selectAll', 'html', 'help']
        }
      }
    })
    
    /**
     * Insert the Find & Replace section into the FroalaEditor
     **/
    
    /**
     * Get the Highlighted/Selected text via FroalaEditor
     **/
    
    /**
     *
     * Handle all the events such as button click, modal popup, etc
     *
     **/
    $('#summernote').summernote('disable');
    $(document)
      .on('click', '.paste_code i', function() {
        if($(this).hasClass('clicked')) {
          $(this).removeClass('clicked');
          $('#preview_section .btn-codeview').click();
          $('#preview_section .btn-codeview').removeClass('active');
          $('#preview_section .btn-codeview').removeAttr('disabled');
          $('#summernote').summernote('disable');
          $('.save_template_css_code').addClass('hidden');
          $('.save_template_code').addClass('hidden');
        } else {
          if($('.import_html i').hasClass('clicked')) $('.import_html i').removeClass('clicked');
          if($('.import_zip i').hasClass('clicked')) $('.import_zip i').removeClass('clicked');
          $(this).addClass('clicked');
          $('#summernote').summernote('enable');
          $('#preview_section .btn-codeview').click();
          $('#preview_section .btn-codeview').removeClass('active');
          $('#preview_section .btn-codeview').attr('disabled', 'disabled');
          $('html,body').animate({
             scrollTop: $("#preview_section").offset().top
          });
          $('.save_template_code').removeClass('hidden');
          $('.save_template_css_code').removeClass('hidden');
        }
      })
      .on('click', '.import_html i', function() {
        $('#preview_section .btn-codeview').removeClass('active');
        $('#preview_section .btn-codeview').removeAttr('disabled');
        $('#summernote').summernote('disable');
        $('.save_template_code').addClass('hidden');
        $('.save_template_css_code').addClass('hidden');
        if($(this).hasClass('clicked')) {
          $(this).removeClass('clicked');
        } else {
          if($('.paste_code i').hasClass('clicked')) $('.paste_code i').removeClass('clicked');
          if($('.import_zip i').hasClass('clicked')) $('.import_zip i').removeClass('clicked');
          $(this).addClass('clicked');
        }
      })
      .on('click', '.import_zip i', function() {
        $('#preview_section .btn-codeview').removeClass('active');
        $('#preview_section .btn-codeview').removeAttr('disabled');
        $('#summernote').summernote('disable');
        $('.save_template_code').addClass('hidden');
        $('.save_template_css_code').addClass('hidden');
        if($(this).hasClass('clicked')) {
          $(this).removeClass('clicked');
        } else {
          if($('.import_html i').hasClass('clicked')) $('.import_html i').removeClass('clicked');
          if($('.paste_code i').hasClass('clicked')) $('.paste_code i').removeClass('clicked');
          $(this).addClass('clicked');
        }
      })
      .on('click', '.save_template_code', function() {
        var template_code = $('#summernote').summernote('code');
        var template_name = $('#upload_tmp input.template_name').val();
        if(template_name == '') { alert('Please confirm the template name...'); $('#upload_tmp input.template_name').focus(); return false; }
        if(template_name.indexOf('.php') > -1) {
          // do nothing
        }else{
          alert('Please check once more you have the file extension in the filename');
          $('#upload_tmp input.template_name').focus();
          return false;
        }
        if(template_code == '<p><br></p>' || template_code == '') { alert('Please confirm the template code you pasted...'); return false; }
        
        var css_code = $('textarea#tmp_css_code_section').val();
        if(css_code == '') { 
          if(confirm('Are you sure to upload the template code without CSS file?')) {
            var data = {
              template_name : template_name,
              template_code : template_code,
              template_css  : css_code
            }
            req_data = {
              'upload_template': JSON.stringify(data)
            }    
            var url = document.location.href;
            $.post(url, req_data, function(res){
              if(res.result == "failed") {
                alert(res.message);
              }
              if(res.result == "success") {
                alert(res.message);            
                location.reload(true);
                $('#summernote').summernote('enable');
                $('#preview_section .btn-codeview').removeAttr('disabled');
                $('#preview_section .btn-codeview').click();
                $('.save_template_code').addClass('hidden');
                $('.update_template_code').removeClass('hidden');
                $('input.old_template_name').val(template_name);
              }
            }, 'json')
          } else {
            $('.save_template_css_code').click();
          }
         } else {
           var data = {
              template_name : template_name,
              template_code : template_code,
              template_css  : css_code
            }
            req_data = {
              'upload_template': JSON.stringify(data)
            }    
            var url = document.location.href;
            $.post(url, req_data, function(res){
              if(res.result == "failed") {
                alert(res.message);
              }
              if(res.result == "success") {
                alert(res.message);            
                location.reload(true);
                $('#summernote').summernote('enable');
                $('#preview_section .btn-codeview').removeAttr('disabled');
                $('#preview_section .btn-codeview').click();
                $('.save_template_code').addClass('hidden');
                $('.update_template_code').removeClass('hidden');
                $('input.old_template_name').val(template_name);
              }
            }, 'json')
         }
      })
      .on('click', '.update_template_code', function() {
        var old_template_name = $('input.old_template_name').val();
        var template_name = $('input.template_name').val();
      
        if( old_template_name != template_name ){
          if (confirm('Are you sure to change the template name?')) {
              // do nothing
          } else {
              $('input.template_name').focus();
              return false;
          }
        }
          
        var template_code = $('#summernote').summernote('code');
        var data = {
          old_template_name : old_template_name,
          template_name     : template_name,
          template_code     : template_code
        }
        req_data = {
          'upload_template': JSON.stringify(data)
        }    
        var url = document.location.href;
        $.post(url, req_data, function(res){
          if(res.result == "failed") {
            alert("Faield updates...");
          }
          if(res.result == "success") {
            alert("Success!");
          }
        }, 'json')
      
      })
//       .on('change','select#choose_template', function() {
      .on('click', '#view_templates', function() {
//         var template_name = $('#tmp_filter_box').val();
        var template_name = $('#choose_template option:selected').html();  
        if(template_name != '--select the template--' && template_name != '') {
          req_data = {
            'template_name': JSON.stringify(template_name)
          }    
          var url = document.location.href;
          $.post(url, req_data, function(res){
            $('#modify_section .fr-element.fr-view').html(res.html);
            $('#stylesheet_section').html(res.css);
            $('#stylesheet_section_hidden').html(res.css);
            $('#stylesheet_section').val(res.css);
            $('.link_stylesheet').html('<link href="../../cdn/custom_template/css/'+template_name.replace('.php', '.css')+'" rel="stylesheet" type="text/css" />');
            $('.link_script').html(res.js);
          }, 'json')
        }
      })
      .on('click', '.update_original_template_code', function() {
        var template_name = $('#choose_template option:selected').text();        
        if(template_name == '--choose the template--') { 
          alert('Please choose the template you want to update.');
          $('#tmp_filter_box').focus();
          return false; 
        }
        $('#update_template_modal #template_name').val(template_name);
        $('#update_template_modal #old_template_name').val(template_name);
        $('#update_template_modal').css({"display":"block"});
      })
      .on('click', '.clone_template_code', function() {
        var template_name = $('#choose_template option:selected').text();        
        if(template_name == '--choose the template--') { 
          alert('Please choose the template you want to update.');
          $('#tmp_filter_box').focus();
          return false; 
        }
        var new_template_name = template_name.replace('.php', '_clone.php');
        $('#clone_template_modal #template_name').val(new_template_name);
        $('#clone_template_modal #old_template_name').val(template_name);
        $('#clone_template_modal').css({"display":"block"});
      })
      .on('click', '#btn_create_tmp', function() {
        $('#create_template_modal').css({"display":"block"});
      })
      .on('click', '.save_template_css_code', function() {
        $('#create_template_css_modal').css({"display":"block"});
      })
      .on('click', ".close-modal, .modal-sandbox", function() {
        $('#update_template_modal').css({"display":"none"});
        $('#clone_template_modal').css({"display":"none"});
        $('#create_template_modal').css({"display":"none"});
        $('#update_template_css_modal').css({"display":"none"});
        $('#update_template_js_modal').css({"display":"none"});
      })
      .on('click', '#update_template_modal input[title=" Update "]', function() {
        var template_code     = $('#modify_section .fr-element.fr-view').html(),
            updated_temp_name = $('#update_template_modal input#template_name').val(),
            old_temp_name     = $('#update_template_modal input#old_template_name').val();
        
        var temp_converted_template_code = template_code.replace(/{\?/gm, '<\?'),
            converted_template_code = temp_converted_template_code.replace(/\?}/gm, '\?>');
      
        if(updated_temp_name != '') {
          var data = {
            template_code : converted_template_code,
            old_temp_name : old_temp_name,
            updated_temp_name : updated_temp_name
          }
          req_data = {
            'update_custom_template': JSON.stringify(data)
          }    
          var url = document.location.href;
          if(confirm('If you have changed anything in CSS or JS files, then please make sure to update them before doing this action!')) {
            $.post(url, req_data, function(res){
              alert(res.message);
              if(res.result == 'success') {
                $('#update_template_modal').css({"display":"none"});
              }
            }, 'json')
          }
        }
      })
      .on('click', '#clone_template_modal input[title=" Update "]', function() {
        var template_code     = $('#modify_section .fr-element.fr-view').html(),
            updated_temp_name = $('#clone_template_modal input#template_name').val(),
            old_temp_name     = $('#clone_template_modal input#old_template_name').val();
        
        var temp_converted_template_code = template_code.replace(/{\?/gm, '<\?'),
            converted_template_code = temp_converted_template_code.replace(/\?}/gm, '\?>');
      
        if(updated_temp_name != '') {
          var data = {
//             template_code : converted_template_code,
            old_temp_name : old_temp_name,
            updated_temp_name : updated_temp_name
          }
          req_data = {
            'clone_custom_template': JSON.stringify(data)
          }    
          var url = document.location.href;
          $.post(url, req_data, function(res){
            alert(res.result);
            $('#clone_template_modal').css({"display":"none"});
          }, 'json')
        }
      })
      .on('click', '#create_template_modal input[title=" Update "]', function() {
        var contents = $('#create_template_modal iframe#design_block').contents().find('iframe#froala-pages').html();
      })
      .on('click', '#create_template_css_modal input[title=" Update "]', function() {
        $('#create_template_css_modal').css({"display":"none"});
      })  
      .on('click', '#create_template_css_modal .close-modal', function() {
        $('#tmp_css_code_section').val('');
        $('#tmp_css_code_section').html('');
        $('#create_template_css_modal').css({"display":"none"});
      }) 
      .on('click', '.show_template_css_code', function() {
        $('#update_template_css_modal').css({"display":"block"});
      })
      .on('click', '.show_template_js_code', function() {
        $('#update_template_js_modal').css({"display":"block"});
      })
      .on('click', '#update_template_css_modal input[title=" Update "]', function() {
        var updated_css_code = $('#stylesheet_section').val();
        var template_name = $('#choose_template option:selected').html();  
        var uploadGlobalMode = $('#update_template_css_modal input#global_css').prop('checked');
        if(uploadGlobalMode) {
          if($('#update_template_css_modal input#global_css_link').val() == '') { alert('Please fill the CSS file name to go ahead...'); return false; }
          else if($('#update_template_css_modal input#global_css_link').val().indexOf('.css') == -1) {
            alert('Please confirm if you enter the correct CSS file name or not. The file extension should be ".css" ');
            return false;
          } else if(updated_css_code == '') {
            alert('Please put the styles on the texteditor');
            return false;
          } else { var cssfileName = $('#update_template_css_modal input#global_css_link').val(); }
        } else {
          var cssfileName = $('#update_template_css_modal input#local_css_link').val();
        }          
        if(uploadGlobalMode) {
          if(confirm("This action will be overwritten the original global CSS file. Are you sure?")) {
            // do nothing!
          } else {
            return false;
          }
        }
        var data = {
          template_css_code : updated_css_code,
          template_name : template_name,
          cssfileName : cssfileName,
          uploadGlobalMode : uploadGlobalMode
        }
        
        req_data = {
          'update_custom_template_css': JSON.stringify(data)
        }    
        var url = document.location.href;
        $.post(url, req_data, function(res){
          if(res.result == 'success') {
            alert(res.message);
            $('#update_template_css_modal').css({"display":"none"});
          } 
          /*
          else if(res.result == 'failed') {
            if(confirm(res.message)) {
              var req_data1 = {
                'save_custom_template_css': JSON.stringify(data)
              }
              $.post(url, req_data1, function(res1){
                alert(res1);
              }, 'json')
            } else {
              
            }
          }
          */
        }, 'json')
      })
      .on('click', '#update_template_css_modal .validGlobal', function() {
        var globalName = $('#global_css_link').val();
        if(globalName == '') {
          alert('Please fill the Global CSS Name first.');
          return false;
        } else {
          var data = {
            cssfileName : globalName
          }
          req_data = {
            'pull_global_css': JSON.stringify(data)
          }
          var url = document.location.href;
          
          $.post(url, req_data, function(res){
            if(res.result == "success") {
              alert("The CSS file is existed, You can check them out below texteditor");
              $('#update_template_css_modal #stylesheet_section').val(res.css);
            } else {
              alert("There is no CSS file you entered. If you want to create a new one, please fill the css below texteditor and update it.");
              $('#update_template_css_modal #stylesheet_section').val('');
            }
          }, 'json')
            
        }
      })
      .on('change', '#update_template_css_modal input[name="global_css_mode"]', function() {
        if(this.id == "global_css") {
          $('#update_template_css_modal #stylesheet_section').val($('#update_template_css_modal #global_stylesheet_section_hidden').val());
        } else {
          var originalSource = $('#update_template_css_modal #local_stylesheet_section_hidden').val();
          $('#update_template_css_modal #stylesheet_section').val(originalSource);
        }
      })
      .on('click', '#update_template_js_modal input[title=" Update "]', function() {
        var updated_js_code = $('#script_section').val();
        var template_name = $('#choose_template option:selected').html();  
        var data = {
          template_js_code : updated_js_code,
          template_name : template_name
        }
        req_data = {
          'update_custom_template_js': JSON.stringify(data)
        }    
        var url = document.location.href;
        $.post(url, req_data, function(res){
          alert(res.message);
          if(res.result == 'success') {
            $('#update_template_js_modal').css({"display":"none"});
          }
        }, 'json')
      })
      .on('click', '.reload_template_design_block', function() {
        
        var arg1 = $('#choose_template option:selected').text();
        var arg2 = $('#choose_template option:selected').val();
        showValue(arg1, arg2);
      })

      .on('click', '.translate_template', function() {
       
        var template_code     = $('#modify_section .fr-element.fr-view').html(),
            updated_temp_name = $('#clone_template_modal input#template_name').val(),
          old_temp_name     = $('#clone_template_modal input#old_template_name').val();
             
          var data = {
            template_code : template_code,
            old_temp_name : old_temp_name,
            updated_temp_name : updated_temp_name
          }
          req_data = {
            'translate_custom_template': JSON.stringify(data)
          }    
         // console.log(req_data); return;
          //var url = document.location.href;
          var url = 'spanish_language_translation_api.php';
          $('#loader').show();
          $.post(url, req_data, function(res){
            console.log(res);
            //console.log(res.error);
            if(res.error)
            {
              $('#loader').html(res.error);
            }
            else
            {
              $('#loader').hide();
            $('#modify_section .fr-element.fr-view').html(res.result);
            }
           
            
          }, 'json')
      
      })
      .on('click', '.add_template_design_block', function(e) {
      
          var test = $(".fr-element.fr-view").get(0);
      
          html2canvas(test, { scale: 0.5 }).then(function(canvas) {
            // canvas width
            var canvasWidth = canvas.width;
            // canvas height
            var canvasHeight = canvas.height;
            // render canvas
            
            // render image
            $(".toCanvas").after(canvas);
            var img = Canvas2Image.convertToImage(canvas, canvasWidth, canvasHeight);
            var dataURL = canvas.toDataURL();
            
//             var tmp_name = $('#tmp_filter_box').val();  
            var tmp_name = $('#choose_template option:selected').html();  
            var custom_template_code = $('#modify_section .fr-element.fr-view').html();
            var temp_converted_template_code = custom_template_code.replace(/{\?/gm, '<\?'),
            converted_template_code = temp_converted_template_code.replace(/\?}/gm, '\?>');
            var data = {
              template_code : converted_template_code,
              template_name : tmp_name,
              screenshot    : dataURL
            }
            req_data = {
              'insert_design_block': JSON.stringify(data)
            }    
            var url = document.location.href;
            $.post(url, req_data, function(res)
            {
              alert(res.message);
              if(res.result == 'success') {
                $('#update_template_css_modal').css({"display":"none"});
              }
            }, 'json')
          });   
      
        /*
        html2canvas($(document).find(".fr-element.fr-view")[0]).then(function(canvas) {
          $('#img_val').val(canvas.toDataURL("image/png"));
          var form = new FormData(document.getElementById('myForm'));
          var file = canvas.toDataURL("image/png");
          $('#img_msg').html('<img src="'+file+'" />')
          if (file) {   
              form.append('img_val', canvas.toDataURL("image/png"));
          }
          form.append('template_name',tmp_name);
          
          $.ajax({
              type: "POST",
              url: document.location.href,
              data: form,             
              cache: false,
              contentType: false, //must, tell jQuery not to process the data
              processData: false,
              success: function(data)
              {
                  console.log(data)
              }
          });
        });
        */
      })
      .on('click', '.create_thumb', function(e) {
          $('.loading-container').removeClass('hidden');
          var test = $(".fr-element.fr-view").get(0);
      
          html2canvas(test, { scale: 0.5 }).then(function(canvas) {
            // canvas width
            var canvasWidth = canvas.width;
            // canvas height
            var canvasHeight = canvas.height;
            // render canvas
            
            // render image
            $(".toCanvas").after(canvas);
            var img = Canvas2Image.convertToImage(canvas, canvasWidth, canvasHeight);
            var dataURL = canvas.toDataURL();
            
//             var tmp_name = $('#tmp_filter_box').val(); 
            var tmp_name = $('#choose_template option:selected').text(); 
            var data = {
              template_name : tmp_name,
//               screenshot    : dataURItoBlob(dataURL)
              screenshot    : dataURL
            }
            req_data = {
              'create_thumbnail': JSON.stringify(data)
            }    
            var url = document.location.href;
            $.post(url, req_data, function(res){
              $('.loading-container').addClass('hidden');
            }, 'json')
          });   
      })
      .on('click', '.backup_template_code', function() {
        var tmp_name = $('#choose_template option:selected').text(); 
        var r = confirm("Are you sure to backup this template?");
        if (r == true) {
          req_data = {
            'backup_temp': JSON.stringify(tmp_name)
          }    
          var url = document.location.href;
          $.post(url, req_data, function(res){
            alert(res);
          }, 'json')
        } else {
          // do nothing
        }
      })
      .on('click', '.backup_template_css_code', function() {
        var tmp_name = $('#choose_template option:selected').text(); 
        var r = confirm("Are you sure to backup the css file of this template?");
        if (r == true) {
          req_data = {
            'backup_css_temp': JSON.stringify(tmp_name)
          }    
          var url = document.location.href;
          $.post(url, req_data, function(res){
            alert(res);
          }, 'json')
        } else {
          // do nothing
        }
      })
      .on('click', '.remove_template_code', function() {
        var tmp_name = $('#choose_template option:selected').text(); 
        var r = confirm("Are you sure to remove this template?");
        if (r == true) {
          req_data = {
            'remove_temp': JSON.stringify(tmp_name)
          }    
          var url = document.location.href;
          $.post(url, req_data, function(res){
            window.location.reload();
          }, 'json')
        } else {
          // do nothing
        }
      })
      .on('click', '#choose_template_msdd', function() {
        console.log('test');
      })
      .on('change', '.ddChild ul li', function() {
          alert(this.value); 
      });
    
      
  
    $(document).on('keyup', 'input.img_keyword', function(){
      var txt = $(this).val();
      $(document).find('div.fr-image-list div.fr-list-column .fr-image-container').hide();
      $(document).find('div.fr-image-list div.fr-list-column .fr-image-container').children().each(function(i, obj) {
        if($(obj).data('name') != undefined) {
          if($(obj).data('name').toUpperCase().indexOf(txt.toUpperCase()) != -1) {
            $(obj).closest('.fr-image-container').show();
          }
        }
      })
    })
    
    $(document).on('click','#fr-link-insert-layer-url-1', function() {
      var temp_qs_url_json = '<?php echo nl2br(addslashes(json_encode($qs_urls))); ?>';
      var qs_urls = JSON.parse(temp_qs_url_json);
      
      var temp_uri_json = '<?php echo nl2br(addslashes(json_encode($url_tags))); ?>';
      var uris = JSON.parse(temp_uri_json); 
      $(this).closest('#fr-link-insert-layer-1').find('.qs_url_section').remove();
      $(this).closest('#fr-link-insert-layer-1').find('.url_section').remove();
      var qs_section = '<div class="search-wrapper qs_url_section"> <input type="text" name="search_qs" class="qs_keyword" value="" placeholder="FILTER QS URLS:"><div class="tags_section">';
      $.each(qs_urls, function(i, obj) {
        qs_section += '<div class="each_tags">'+escapeHtml(obj)+'</div>';
      })  
      qs_section += '</div></div>';
      
      /* Add the uri tags to the modal */
      var uris_section = '<div class="search-wrapper url_section"> <input type="text" name="search_uri" class="uri_keyword" value="" placeholder="FILTER URLS:"><div class="tags_section">';
      $.each(uris, function(i, obj) {
        uris_section += '<div class="each_tags">'+escapeHtml(obj)+'</div>';
      })  
      uris_section += '</div></div>';
      /* Custom Tags End */
      
      $(this).before(qs_section);
      $(this).after(uris_section);
    })
  
    $(document).on('click','#fr-link-insert-layer-text-1', function() {
      var temp_custom_tags_json = '<?php echo nl2br(addslashes(json_encode($custom_tags))); ?>';
      var custom_tags = JSON.parse(temp_custom_tags_json);
      
      $(this).closest('.fr-input-line').find('.custom_tags_section').remove();
      
      var ctags_section = '<div class="search-wrapper custom_tags_section"> <input type="text" name="search_tags" class="tags_keyword" value="" placeholder="CUSTOM TAGS:"><div class="tags_section">';
      $.each(custom_tags, function(i, obj) {
        ctags_section += '<div class="each_tags">'+escapeHtml(obj)+'</div>';
      })  
      ctags_section += '</div></div>';
      /* Custom Tags End */
      
      $(this).after(ctags_section);
    })
  
    $(document).on('click','#fr-image-alt-layer-text-1', function() {
      var temp_custom_tags_json = '<?php echo nl2br(addslashes(json_encode($custom_tags))); ?>';
      var custom_tags = JSON.parse(temp_custom_tags_json);
      
      $(this).closest('.fr-input-line').find('.custom_tags_section').remove();
      
      var ctags_section = '<div class="search-wrapper custom_tags_section"> <input type="text" name="search_tags-image" tabindex="1" class="search_tags-image" value="" placeholder="CUSTOM TAGS:"><div class="tags_section">';
      $.each(custom_tags, function(i, obj) {
        ctags_section += '<div class="each_tags">'+escapeHtml(obj)+'</div>';
      })  
      ctags_section += '</div></div>';
      /* Custom Tags End */
      
      $(this).closest('div.fr-input-line').append(ctags_section);
    })
  
    $(document).on('keyup', '.search-wrapper input.tags_keyword', function(){
      var txt = $(this).val();
      $(document).find('div.custom_tags_section div.tags_section').children().hide();
      $(document).find('.custom_tags_section div.tags_section').children().each(function(i, obj) {
        if($(obj).html().toUpperCase().indexOf(txt.toUpperCase()) != -1) {
          $(obj).show();
        }
      })
    }) 
  
    $(document).on('keyup', '.search-wrapper input.search_tags-image', function(){
      var txt = $(this).val();
      $(document).find('div.custom_tags_section div.tags_section').children().hide();
      $(document).find('.custom_tags_section div.tags_section').children().each(function(i, obj) {
        if($(obj).html().toUpperCase().indexOf(txt.toUpperCase()) != -1) {
          $(obj).show();
        }
      })
    }) 
  
    $(document).on('keyup', '.search-wrapper input.qs_keyword', function(){
      var txt = $(this).val();
      $(document).find('div.qs_url_section div.tags_section').children().hide();
      $(document).find('.qs_url_section div.tags_section').children().each(function(i, obj) {
        if($(obj).html().toUpperCase().indexOf(txt.toUpperCase()) != -1) {
          $(obj).show();
        }
      })
    }) 
  
    $(document)
      .on('keyup', '.search-wrapper input.uri_keyword', function(){
        var txt = $(this).val();
        $(document).find('div.url_section div.tags_section').children().hide();
        $(document).find('div.url_section div.tags_section').children().each(function(i, obj) {
          if($(obj).html().toUpperCase().indexOf(txt.toUpperCase()) != -1) {
            $(obj).show();
          }
        })
      })
      
      $('.upload_template_section .tab a').on('click', function (e) {
  
        e.preventDefault();

        $(this).parent().addClass('active');
        $(this).parent().siblings().removeClass('active');

        target = $(this).attr('href');

        $('.tab-content > div').not(target).hide();

        $(target).fadeIn(600);

      });
    
      function convertCanvasToImage(canvas) {
        var image = new Image();
        image.src = canvas.toDataURL("image/png");
        return image;
      }
    
      
//       button = document.querySelector('button');
//       datalist = document.querySelector('datalist');
//       select = document.querySelector('choose_template');
//       options = select.options;

      /* on arrow button click, show/hide the DDL*/
//       button.addEventListener('click', toggle_ddl);

//       function toggle_ddl() {
//         if (datalist.style.display === '') {
//           datalist.style.display = 'block';
//           this.textContent = "▲";
//           /* If input already has a value, select that option from DDL */
//           var val = input.value;
//           for (var i = 0; i < options.length; i++) {
//             if (options[i].text === val) {
//               select.selectedIndex = i;
//               break;
//             }
//           }
//         } else hide_select();
//       }

//       /* when user selects an option from DDL, write it to text field */
//       select.addEventListener('change', fill_input);

//       function fill_input() {
//         input.value = options[this.selectedIndex].value;
//         console.log(options[this.selectedIndex].value)
//         hide_select();
//       }

//       /* when user wants to type in text field, hide DDL */
//       input = document.querySelector('input');
//       input.addEventListener('focus', hide_select);

//       function hide_select() {
//         datalist.style.display = '';
//         button.textContent = "▼";
//       }
    
//       $("input#tmp_filter_box").focusout(function(){
//           alert($(this).val());
//       });
    
      $("#tmp_filter_box").on('keyup', function () {
          var val = this.value;
          if($('#choose_template option').filter(function(){
              return this.value.toUpperCase() === val.toUpperCase();        
          }).length) {
              //send ajax request
              alert(this.value);
          }
      });
      
  })
  
  /**
   * Choose the template via template dropdown box
   **/
  
  
  function showValue(arg, arg2) {
    var template_name = $('#choose_template option:selected').html();  

    if(arg != '--choose the template--' && arg != '') {
      req_data = {
  'pull_template_name': arg  // ← this is correct
} 

      var url = document.location.href;
      $.post(url, req_data, function(res){
        var file_link = res.files;
        var encodeHtml = unescapeHtml(decodeURI(res.html.replace(/%(?![0-9][0-9a-fA-F]+)/g, '%25')));

      
      var convertedHtml = encodeHtml.replace(/<\?/gm, '{?').replace(/\?>/gm, '?}');
      if (window.editor) {
          window.editor.html.set(convertedHtml);   // This sets the content correctly in Froala editor
          window.editor.events.focus();            // Optional: focus the editor
                           }

        $('#script_section').val(res.js);
        $('#update_template_css_modal input#global_css_link').val('');
        
        if(file_link.length > 0) {
          $('#update_template_css_modal input#global_css').prop('checked', true);
          $('#update_template_css_modal input#global_css_link').val(res.files[0]);
          $('.link_stylesheet').html('<link href="../../cdn/custom_template/css/'+res.files[0]+'" rel="stylesheet" type="text/css" />');
          $('#stylesheet_section').val(res.global_contents);
        } else {
          $('#stylesheet_section').val(res.css);
          $('#update_template_css_modal input#local_css').prop('checked', true);
          $('.link_stylesheet').html('<link href="../../cdn/custom_template/css/'+arg.replace('.php', '.css')+'" rel="stylesheet" type="text/css" />');
        }
        $('#update_template_css_modal input#local_css_link').val(arg.replace('.php', '.css'))
        $('#local_stylesheet_section_hidden').val(res.css);
        $('#global_stylesheet_section_hidden').val(res.global_contents);
        $('.link_script').html(res.js);
      }, 'json')
    }
  }
  
  /**
   * Update the contentes in Froala textarea with 
   * the content you selected via sticky thing
   **/
  $(document).on('click', '.each_tags', function(){
      var txtToAddCodeView = $(this).html();
      var hasCodeView=$('#codeViewTextArea').closest('div.fr-box');
    if($(hasCodeView).hasClass('fr-code-view')) { 
            var cursorPos = $('#codeViewTextArea').prop('selectionStart');
            var v = $('#codeViewTextArea').val();
            var textBefore = v.substring(0,  cursorPos );
            var textAfter  = v.substring( cursorPos, v.length );
            $('#codeViewTextArea').val( textBefore+ txtToAddCodeView +textAfter );

    }

    if($(this).closest('div.search-wrapper').attr('class').indexOf('custom_tags_section') > -1) {
      var $txt = $(this).closest('div.fr-input-line').find('input#fr-link-insert-layer-text-1');
      var caretPos = $txt[0].setSelectionRange(0, 0);
      var textAreaTxt = $txt.val();
      var txtToAdd = $(this).html();
      $txt.val(textAreaTxt.substring(0, caretPos) + htmlDecode(txtToAdd));
    } else if($(this).closest('div.search-wrapper').attr('class').indexOf('custom_title_section') > -1) {
      var $txt = $('.template-info .focused_point');
      var textAreaTxt = $txt.val();
      var txtToAdd = $(this).html();
      $txt.html(htmlDecode(txtToAdd));
    } else {
      var $txt = $(this).closest('div.fr-input-line').find('input#fr-link-insert-layer-url-1');
      var caretPos = $txt[0].setSelectionRange(0, 0);
      var textAreaTxt = $txt.val();
      var txtToAdd = $(this).html();
      $txt.val(textAreaTxt.substring(0, caretPos) + htmlDecode(txtToAdd));      
    }
  })
  .on('click', '.template-info', function(e){
    var element = $('.template-info').find('.focused_point');
    element.removeClass('focused_point');
    $ele = $(e.target);
    $ele.addClass('focused_point');

  })
  .on('click', '.paragraph_lists li', function(){
    var $txt = $('.template-info .focused_point');
    var textAreaTxt = $txt.val();
    var txtToAdd = $(this).html();
    $txt.html(txtToAdd);
  })
  
  $(document).on('click','.search_title input.title_keyword', function() {
    var temp_custom_tags_json = '<?php echo nl2br(addslashes(json_encode($custom_tags))); ?>';
    var custom_tags = JSON.parse(temp_custom_tags_json);

    $(this).closest('search_title').find('.custom_title_section').remove();
    $(this).closest('search_title').find('.custom_parag_section').remove();
    $(this).closest('.search_title').find('.custom_meta_title_section').remove();
    $(this).closest('.search_title').find('.custom_meta_des_section').remove();
      $(this).closest('.search_title').find('.custom_qs_url_section').remove();
      $(this).closest('.search_title').find('.custom_url_section').remove();

    var ctags_section = '<div class="search-wrapper custom_title_section"><div class="title_section">';
    $.each(custom_tags, function(i, obj) {
      ctags_section += '<div class="each_tags">'+escapeHtml(obj)+'</div>';
    })  
    ctags_section += '</div></div>';
    /* Custom Tags End */
    $(this).closest('.search_title').find('.custom_title_section').remove();
    $(this).closest('.search_title').find('.custom_meta_title_section').remove();
    $(this).closest('.search_title').find('.custom_meta_des_section').remove();
    $(this).closest('.search_title').find('.custom_parag_section').remove();
      $(this).closest('.search_title').find('.custom_qs_url_section').remove();
      $(this).closest('.search_title').find('.custom_url_section').remove();

    $(this).after(ctags_section);
  })
  $(document).on('click','.search_title input.parag_keyword', function() {
    var temp_paragraph_lists_json = '<?php echo nl2br(addslashes(json_encode($paragraph_lists))); ?>';
    var paragraph_lists = JSON.parse(temp_paragraph_lists_json);

    $(this).closest('.search_title').find('.custom_parag_section').remove();
    $(this).closest('.search_title').find('.custom_title_section').remove();
    $(this).closest('.search_title').find('.custom_meta_title_section').remove();
    $(this).closest('.search_title').find('.custom_meta_des_section').remove();
    $(this).closest('.search_title').find('.custom_qs_url_section').remove();
    $(this).closest('.search_title').find('.custom_url_section').remove();

    var ctags_section = '<div class="search-wrapper custom_parag_section paragraph_lists"><div class="parag_section"><ul>';
    $.each(paragraph_lists, function(i, obj) {
      if(escapeHtml(obj) != '')
        ctags_section += '<li>'+escapeHtml(obj)+'</li>';
    })  
    ctags_section += '</ul></div></div>';
    /* Custom Tags End */
    $(this).closest('.search_title').find('.custom_parag_section').remove();
    $(this).closest('.search_title').find('.custom_title_section').remove();
    $(this).closest('.search_title').find('.custom_meta_title_section').remove();
    $(this).closest('.search_title').find('.custom_meta_des_section').remove();
    $(this).closest('.search_title').find('.custom_qs_url_section').remove();
    $(this).closest('.search_title').find('.custom_url_section').remove();

    $(this).after(ctags_section);
  })
  $(document).on('click','.search_title input.meta_title_keyword', function() {
    var temp_meta_title_lists_json = '<?php echo nl2br(addslashes(json_encode($meta_title_lists))); ?>';
    var meta_title_lists = JSON.parse(temp_meta_title_lists_json);

    $(this).closest('.search_title').find('.custom_parag_section').remove();
    $(this).closest('.search_title').find('.custom_title_section').remove();
    $(this).closest('.search_title').find('.custom_meta_title_section').remove();
    $(this).closest('.search_title').find('.custom_meta_des_section').remove();
    $(this).closest('.search_title').find('.custom_qs_url_section').remove();
    $(this).closest('.search_title').find('.custom_url_section').remove();

    var ctags_section = '<div class="search-wrapper custom_meta_title_section paragraph_lists"><div class="meta_title_section"><ul>';
    $.each(meta_title_lists, function(i, obj) {
      if(escapeHtml(obj) != '')
        ctags_section += '<li>'+escapeHtml(obj)+'</li>';
    })  
    ctags_section += '</ul></div></div>';
    /* Custom Tags End */
    $(this).closest('.search_title').find('.custom_parag_section').remove();
    $(this).closest('.search_title').find('.custom_title_section').remove();
    $(this).closest('.search_title').find('.custom_meta_title_section').remove();
    $(this).closest('.search_title').find('.custom_meta_des_section').remove();
    $(this).closest('.search_title').find('.custom_qs_url_section').remove();
    $(this).closest('.search_title').find('.custom_url_section').remove();

    $(this).after(ctags_section);
  })
  $(document).on('click','.search_title input.meta_des_keyword', function() {
    var temp_meta_desc_lists_json = '<?php echo nl2br(addslashes(json_encode($meta_desc_lists))); ?>';
    var meta_desc_lists = JSON.parse(temp_meta_desc_lists_json);

    $(this).closest('.search_title').find('.custom_parag_section').remove();
    $(this).closest('.search_title').find('.custom_title_section').remove();
    $(this).closest('.search_title').find('.custom_meta_title_section').remove();
    $(this).closest('.search_title').find('.custom_meta_des_section').remove();
    $(this).closest('.search_title').find('.custom_qs_url_section').remove();
    $(this).closest('.search_title').find('.custom_url_section').remove();

    var ctags_section = '<div class="search-wrapper custom_meta_des_section paragraph_lists"><div class="meta_des_section"><ul>';
    $.each(meta_desc_lists, function(i, obj) {
      if(escapeHtml(obj) != '')
        ctags_section += '<li>'+escapeHtml(obj)+'</li>';
    })  
    ctags_section += '</ul></div></div>';
    /* Custom Tags End */
    $(this).closest('.search_title').find('.custom_parag_section').remove();
    $(this).closest('.search_title').find('.custom_title_section').remove();
    $(this).closest('.search_title').find('.custom_meta_title_section').remove();
    $(this).closest('.search_title').find('.custom_meta_des_section').remove();
    $(this).closest('.search_title').find('.custom_qs_url_section').remove();
    $(this).closest('.search_title').find('.custom_url_section').remove();

    $(this).after(ctags_section);
  })
  
  
  $(document).on('keyup', 'input.title_keyword', function(){
    var txt = $(this).val();
    $(document).find('div.custom_title_section div.title_section').children().hide();
    $(document).find('.custom_title_section div.title_section').children().each(function(i, obj) {
      if($(obj).html().toUpperCase().indexOf(txt.toUpperCase()) != -1) {
        $(obj).show();
      }
    })
  })  
  $(document).on('keyup','.meta_title_keyword',function(){  
      var searchWarpper = $(this).closest('.search_title');
      searchWarpper.find('div.meta_title_section ul').children().hide();
      var txt = $(this).val();
      searchWarpper.find('div.meta_title_section ul').children().each(function(i,v) {
        if($(this).text().toUpperCase().indexOf(txt.toUpperCase()) != -1) {
          $(this).show();
        }
      })
  })
  $(document).on('keyup','.meta_des_keyword',function(){  
      var searchWarpper = $(this).closest('.search_title');
      searchWarpper.find('div.meta_des_section ul').children().hide();
      var txt = $(this).val();
      searchWarpper.find('div.meta_des_section ul').children().each(function(i,v) {
        if($(this).text().toUpperCase().indexOf(txt.toUpperCase()) != -1) {
          $(this).show();
        }
      })
  })
  $(document).on('keyup','.parag_keyword',function(){  
      var searchWarpper = $(this).closest('.search_title');
      searchWarpper.find('div.parag_section ul').children().hide();
      var txt = $(this).val();
      searchWarpper.find('div.parag_section ul').children().each(function(i,v) {
        if($(this).text().toUpperCase().indexOf(txt.toUpperCase()) != -1) {
          $(this).show();
        }
      })
  })
  
  .on('click', '.template-info', function(e){
    var element = $('.template-info').find('.focused_point');
    element.removeClass('focused_point');
    $ele = $(e.target);
    $ele.addClass('focused_point');

  })
  .on('click', '.paragraph_lists li', function(){
    var $txt = $('.template-info .focused_point');
    var textAreaTxt = $txt.val();
    var txtToAdd = $(this).html();
    $txt.html(txtToAdd);
  })
  .on('submit', '.upload_img form#myForm', function(e) {
    $.ajax( {
        url: '../get_image.php',
        type: 'POST',
        data: new FormData( this ),
        processData: false,
        contentType: false,
        success: function(result){
          alert(result);

        }
    } );
    e.preventDefault();
  })
  
  
  $(document).on('click','.search_title input.filter_qs_urls', function() {
    var temp_qs_url_json = '<?php echo nl2br(addslashes(json_encode($qs_urls))); ?>';
    var qs_urls = JSON.parse(temp_qs_url_json);
    var qs_section = '<div class="search-wrapper custom_qs_url_section qs_urls paragraph_lists"><div class="qs_url_section"><ul>';
    $.each(qs_urls, function(i, obj) {
      qs_section += '<li>'+escapeHtml(obj)+'</li>';
    })  
    qs_section += '</ul></div></div>';

    /* Custom Tags End */
    $(this).closest('.search_title').find('.custom_parag_section').remove();
    $(this).closest('.search_title').find('.custom_title_section').remove();
    $(this).closest('.search_title').find('.custom_meta_title_section').remove();
    $(this).closest('.search_title').find('.custom_meta_des_section').remove();
    $(this).closest('.search_title').find('.custom_url_section').remove();

    $(this).after(qs_section);
  })
  $(document).on('click','.search_title input.filter_urls', function() {
    var temp_uri_json = '<?php echo nl2br(addslashes(json_encode($url_tags))); ?>';
    var uris = JSON.parse(temp_uri_json); 
    var uris_section = '<div class="search-wrapper custom_url_section urls paragraph_lists"><div class="url_section"><ul>';
    $.each(uris, function(i, obj) {
      uris_section += '<li>'+escapeHtml(obj)+'</li>';
    })  
    uris_section += '</ul></div></div>';

    $(this).closest('.search_title').find('.custom_parag_section').remove();
    $(this).closest('.search_title').find('.custom_title_section').remove();
    $(this).closest('.search_title').find('.custom_meta_title_section').remove();
    $(this).closest('.search_title').find('.custom_meta_des_section').remove(); 
    $(this).closest('.search_title').find('.custom_qs_url_section').remove();

    $(this).after(uris_section);
  })
  $(document).on('keyup','input.filter_qs_urls',function(){  
      var searchWarpper = $(this).closest('.search_title');
      searchWarpper.find('div.custom_qs_url_section ul').children().hide();
      var txt = $(this).val();
      searchWarpper.find('div.custom_qs_url_section ul').children().each(function(i,v) {
        if($(this).text().toUpperCase().indexOf(txt.toUpperCase()) != -1) {
          $(this).show();
        }
      })
  })
  $(document).on('keyup','input.filter_urls',function(){  
      var searchWarpper = $(this).closest('.search_title');
      searchWarpper.find('div.custom_url_section ul').children().hide();
      var txt = $(this).val();
      searchWarpper.find('div.custom_url_section ul').children().each(function(i,v) {
        if($(this).text().toUpperCase().indexOf(txt.toUpperCase()) != -1) {
          $(this).show();
        }
      })
  })
  
//   $(document).on('click', '.each_tags', function(){
//     if($(this).closest('div.search-wrapper').attr('class').indexOf('custom_tags_section') > -1) {
//       var $txt = $(this).closest('div.fr-input-line').find('input#fr-link-insert-layer-text-1');
//       var caretPos = $txt[0].setSelectionRange(0, 0);
//       var textAreaTxt = $txt.val();
//       var txtToAdd = $(this).html();
//       $txt.val(textAreaTxt.substring(0, caretPos) + htmlDecode(txtToAdd));
//     } else if($(this).closest('div.search-wrapper').attr('class').indexOf('custom_title_section') > -1) {
//       var $txt = $('.template-info .focused_point');
//       var textAreaTxt = $txt.val();
//       var txtToAdd = $(this).html();
//       $txt.html(htmlDecode(txtToAdd));
//     } else {
//       var $txt = $(this).closest('div.fr-input-line').find('input#fr-link-insert-layer-url-1');
//       var caretPos = $txt[0].setSelectionRange(0, 0);
//       var textAreaTxt = $txt.val();
//       var txtToAdd = $(this).html();
//       $txt.val(textAreaTxt.substring(0, caretPos) + htmlDecode(txtToAdd));      
//     }
//   })
  
  function escapeHtml(unsafe) {
      return unsafe
           .replace(/&/g, "&amp;")
           .replace(/</g, "&lt;")
           .replace(/>/g, "&gt;")
           .replace(/"/g, "&quot;")
           .replace(/'/g, "&#039;");
  }
  function htmlDecode(value){
    return $('<div/>').html(value).text();
  }
  function unescapeHtml(safe) {
      return safe.replace(/&amp;/g, '&')
          .replace(/&lt;/g, '<')
          .replace(/&gt;/g, '>')
          .replace(/&quot;/g, '"')
          .replace(/&#039;/g, "'");
  }

  function dataURItoBlob(dataURI) {
    // convert base64 to raw binary data held in a string
    // doesn't handle URLEncoded DataURIs - see SO answer #6850276 for code that does this
    var byteString = atob(dataURI.split(',')[1]);

    // separate out the mime component
    var mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0]

    // write the bytes of the string to an ArrayBuffer
    var ab = new ArrayBuffer(byteString.length);

    // create a view into the buffer
    var ia = new Uint8Array(ab);

    // set the bytes of the buffer to the correct values
    for (var i = 0; i < byteString.length; i++) {
        ia[i] = byteString.charCodeAt(i);
    }

    // write the ArrayBuffer to a blob, and you're done
    var blob = new Blob([ab], {type: mimeString});
    return blob;

  }
  

// js for drag fields shani



//Make the DIV element draggagle:
dragElement(document.getElementById("draggableBox"));

function dragElement(elmnt) {
  var pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
  if (document.getElementById(elmnt.id + "Search")) {
    /* if present, the header is where you move the DIV from:*/
    document.getElementById(elmnt.id + "Search").onmousedown = dragMouseDown;
  } else {
    /* otherwise, move the DIV from anywhere inside the DIV:*/
    elmnt.onmousedown = dragMouseDown;
  }

  function dragMouseDown(e) {
    e = e || window.event;
    e.preventDefault();
    // get the mouse cursor position at startup:
    pos3 = e.clientX;
    pos4 = e.clientY;
    document.onmouseup = closeDragElement;
    // call a function whenever the cursor moves:
    document.onmousemove = elementDrag;
  }

  function elementDrag(e) {
    e = e || window.event;
    e.preventDefault();
    // calculate the new cursor position:
    pos1 = pos3 - e.clientX;
    pos2 = pos4 - e.clientY;
    pos3 = e.clientX;
    pos4 = e.clientY;
    // set the element's new position:
    elmnt.style.top = (elmnt.offsetTop - pos2) + "px";
    elmnt.style.left = (elmnt.offsetLeft - pos1) + "px";
  }

  function closeDragElement() {
    /* stop moving when mouse button is released:*/
    document.onmouseup = null;
    document.onmousemove = null;
  }
}

$(document).ready(function () {
  $('#choose_template').select2({
    placeholder: '--choose the template--',
    allowClear: true,
    width: '100%',
    templateResult: formatTemplateOption,
    templateSelection: formatTemplateSelection
  });

  $('#choose_template').on('change', function () {
    const selectedVal = $(this).val();
    const selectedText = $(this).find('option:selected').text();
    if (selectedVal !== '') {
      showValue(selectedText, selectedVal);
    }
  });

  function formatTemplateOption(option) {
    if (!option.id) return option.text;

    const imageUrl = $(option.element).data('image');
    if (imageUrl) {
      return $(`
        <span style="display:flex; align-items:center;">
          <img src="${imageUrl}" style="width:40px; height:auto; margin-right:8px;" />
          ${option.text}
        </span>
      `);
    }

    return option.text;
  }

  function formatTemplateSelection(option) {
    return option.text;
  }
});




</script>
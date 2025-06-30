<?php

if (isset($_POST['ajax_action']) && $_POST['ajax_action'] === 'insert_new_record') {
    $domain             = zen_db_input(trim($_POST['domain']));
    $url                = zen_db_input(trim($_POST['url']));
    $meta_title         = zen_db_input(trim($_POST['meta_title']));
    $meta_description   = zen_db_input(trim($_POST['meta_description']));
    $cookiecutterqs_temp= zen_db_input(trim($_POST['cookiecutterqs_temp']));
  
    $insert_template    = isset($_POST['insert_template_table']);
    $insert_qs          = isset($_POST['insert_qs_table']);
  
    // Template and Textbox Fields
    $template_fields = [];
    $textbox_fields = [];
    for ($i = 1; $i <= 5; $i++) {
        $template_fields["template$i"] = zen_db_input(trim($_POST["template$i"] ?? ''));
        $textbox_fields["textbox$i"] = zen_db_input(trim($_POST["textbox$i"] ?? ''));
    }
  
    // ✅ Check for existing URL in template_to_urls for this domain
    $checkSql = "SELECT COUNT(*) as cnt FROM template_to_urls WHERE domain = '$domain' AND url = '$url'";
    $checkResult = $db->Execute($checkSql);
    $count = $checkResult->fields['cnt'] ?? 0;
  
    if ($count > 0) {
        echo json_encode(['status' => 'error', 'message' => 'This URL already exists for this domain.']);
        exit;
    }
  
    // ✅ Insert if not duplicate
    $sql1 = sprintf(
        "INSERT INTO template_to_urls (domain, url, meta_title, meta_description, cookiecutterqs_temp, template1, template2, template3, template4, template5, textbox1, textbox2, textbox3, textbox4, textbox5)
         VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
        $domain, $url, $meta_title, $meta_description, $cookiecutterqs_temp,
        $template_fields['template1'], $template_fields['template2'], $template_fields['template3'], $template_fields['template4'], $template_fields['template5'],
        $textbox_fields['textbox1'], $textbox_fields['textbox2'], $textbox_fields['textbox3'], $textbox_fields['textbox4'], $textbox_fields['textbox5']
    );
  
    $query = $db->Execute($sql1);
  
    if ($query) {
        echo json_encode(['status' => 'success', 'message' => "Added new URL: $url"]);
        exit;
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to insert the record.']);
        exit;
    }
  }
  
 
//   if ($insert_qs) {
//     $cookiecutter_id = (int)($_POST['cookiecutterqs_temp'] ?? 0);

//     if ($cookiecutter_id <= 0 || $domain == '' || $url == '') {
//         echo json_encode(['status' => 'error', 'message' => 'Missing or invalid required fields for QS table insert!']);
//         exit;
//     }

//     $sql2 = "
//         INSERT INTO cookiecutterQS_qs_url (
//             cookiecutter_id, domain, qs_url,
//             qsTextbox1, qsTextbox2, qsTextbox3, qsTextbox4, qsTextbox5
//         ) VALUES (
//             $cookiecutter_id, '$domain', '$url',
//             '{$textbox_fields['textbox1']}', '{$textbox_fields['textbox2']}', '{$textbox_fields['textbox3']}',
//             '{$textbox_fields['textbox4']}', '{$textbox_fields['textbox5']}'
//         )
//     ";
//     $db->Execute($sql2);
//     echo json_encode(['test' => $sql2]);
//     exit;
//     try {
       
//     } catch (Exception $e) {
//         echo json_encode(['status' => 'error', 'message' => $e->getMessage(), 'sql' => $sql2]);
//         exit;
//     }
// }





if (isset($_POST['ajax_action']) && $_POST['ajax_action'] === 'load_dropdown_data') {
  $domains    = $db->Execute("SELECT DISTINCT domain FROM template_to_urls");
  $urls       = $db->Execute("SELECT DISTINCT uri FROM ceon_uri_mappings");
  $templates  = $db->Execute("SELECT template_name FROM custom_template_file");
  $textboxes  = $db->Execute("SELECT template_name FROM custom_template_file"); // Could filter if needed
  $cookie_rs  = $db->Execute("SELECT id, name FROM cookie_cutter_qs ORDER BY name ASC");

  function buildOptions($recordset, $key = 'template_name') {
      $result = [];
      while (!$recordset->EOF) {
          $val = $recordset->fields[$key];
          $result[] = ['value' => $val, 'text' => $val];
          $recordset->MoveNext();
      }
      return $result;
  }

  $response = [
      'domains'        => buildOptions($domains, 'domain'),
      'urls'           => buildOptions($urls, 'uri'),
      'templates'      => buildOptions($templates, 'template_name'),
      'textboxes'      => buildOptions($textboxes, 'template_name'),
      'cookiecutterqs' => buildOptions($cookie_rs, 'name')
  ];

  echo json_encode($response);
  exit;
}



if (isset($_POST['ajax_action']) && $_POST['ajax_action'] === 'get_css_js_files') {
  $css_files = glob('../../cdn/custom_template/css/*.css');
  $js_files = glob('../../cdn/custom_template/js/*.js.php');

  $css_list = array_map('basename', $css_files);
  $js_list = array_map('basename', $js_files);

  echo json_encode([
      'status' => 'success',
      'css_files' => $css_list,
      'js_files' => $js_list
  ]);
  exit;
}


if (isset($_POST['ajax_action']) && $_POST['ajax_action'] == 'backup_template') {
  $domain = $_POST['domain'];
  $template_name = $_POST['template_name'];
  $url = $_POST['url'];

  $basePath = '../../cdn/custom_template/';
  $cssPath  = $basePath . 'css/';
  $jsPath   = $basePath . 'js/';
  $backupHtmlPath = $basePath . 'backup/';
  $backupCssPath  = $cssPath . 'css_backup/';
  $backupJsPath   = $jsPath . 'backup/';

  // Ensure backup directories exist
  if (!is_dir($backupHtmlPath)) mkdir($backupHtmlPath, 0777, true);
  if (!is_dir($backupCssPath)) mkdir($backupCssPath, 0777, true);
  if (!is_dir($backupJsPath)) mkdir($backupJsPath, 0777, true);

  // Original files
  $html_file = $basePath . $template_name;
  $css_file  = $cssPath . str_replace('.php', '.css', $template_name);
  $js_file   = $jsPath . str_replace('.php', '.js.php', $template_name);

  // Backup file names
  $backup_suffix = '_backup_' . date('Ymd_His');
  $html_backup = $backupHtmlPath . str_replace('.php', $backup_suffix . '.php', $template_name);
  $css_backup  = $backupCssPath  . str_replace('.php', $backup_suffix . '.css', $template_name);
  $js_backup   = $backupJsPath   . str_replace('.php', $backup_suffix . '.js.php', $template_name);

  $backed_up = [];

  if (file_exists($html_file)) {
      copy($html_file, $html_backup);
      $backed_up[] = 'HTML';
  }
  if (file_exists($css_file)) {
      copy($css_file, $css_backup);
      $backed_up[] = 'CSS';
  }
  if (file_exists($js_file)) {
      copy($js_file, $js_backup);
      $backed_up[] = 'JS';
  }

  if (!empty($backed_up)) {
      echo json_encode([
          'status' => 'success',
          'message' => 'Backed up: ' . implode(', ', $backed_up)
      ]);
  } else {
      echo json_encode([
          'status' => 'error',
          'message' => 'No files found to backup.'
      ]);
  }

  exit;
}





if ($_POST['ajax_action'] === 'save_template_all_fields') {
  $fileName = basename($_POST['file_name']);
  $html = $_POST['html'];
  $css  = $_POST['css'];
  $js   = $_POST['js'];

  file_put_contents("../../cdn/custom_template/{$fileName}", $html);
  file_put_contents("../../cdn/custom_template/css/" . str_replace('.php', '.css', $fileName), $css);
  file_put_contents("../../cdn/custom_template/js/" . str_replace('.php', '.js.php', $fileName), $js);

  echo json_encode(['status' => 'success', 'message' => 'Files saved']);
  exit;
}

if(isset($_POST['pull_template'])) {
     
  $template_name = $_POST['pull_template'];
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

// ----- BACKEND HANDLER -----
if (isset($_POST['ajax_action']) && $_POST['ajax_action'] === 'get_template_details') {
  $template_name = $_POST['template_name'];
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


//empty template field from a record///

if (isset($_POST['ajax_action']) && $_POST['ajax_action'] == 'delete_template_field') {
  $id = (int)$_POST['id'];
  $columnName = $_POST['columnName'];

  // Define columns for each table
  $template_columns = array(
      'template', 'template1', 'template2', 'template3', 'template4', 'template5',
      'cookiecutterqs_temp', 'textbox1', 'textbox2', 'textbox3', 'textbox4', 'textbox5'
  );

  $qs_columns = array(
      'qsTextbox1', 'qsTextbox2', 'qsTextbox3', 'qsTextbox4', 'qsTextbox5'
  );

  if (in_array($columnName, $template_columns)) {
      $sql = "UPDATE template_to_urls 
              SET `$columnName` = '' 
              WHERE id = $id";
      $db->Execute($sql);

      echo json_encode(array('status' => 'success', 'message' => 'Field cleared in template_to_urls.', 'id' => $id));
  } elseif (in_array($columnName, $qs_columns)) {
      $sql = "UPDATE cookiecutterQS_qs_url 
              SET `$columnName` = '' 
              WHERE id = $id";
      $db->Execute($sql);

      echo json_encode(array('status' => 'success', 'message' => 'Field cleared in cookiecutterQS_qs_url.', 'id' => $id));
  } else {
      echo json_encode(array('status' => 'error', 'message' => 'Invalid column.'));
  }

  exit;
}


//update the templetes in the database data from ajax request in the script
if (isset($_POST['ajax_action']) && $_POST['ajax_action'] == 'save_template_field') {
  $domain = zen_db_input($_POST['domain']);
  $columnName = $_POST['columnName'];
  $newValue = zen_db_input($_POST['newValue']);
  $rowId = (int) $_POST['rowId'];
  $actionType = $_POST['actionType'];

  // Define allowed columns for both tables
  $template_table_columns = array(
      'template', 'template1', 'template2', 'template3', 'template4', 'template5',
      'textbox1', 'textbox2', 'textbox3', 'textbox4', 'textbox5',
      'cookiecutterqs_temp', 'meta_title', 'meta_description'
  );

  $qs_table_columns = array(
      'qsTextbox1', 'qsTextbox2', 'qsTextbox3', 'qsTextbox4', 'qsTextbox5'
  );

  if (in_array($columnName, $template_table_columns)) {
      $sql = "UPDATE template_to_urls SET `$columnName` = '$newValue' WHERE id = $rowId AND domain = '$domain'";
      $db->Execute($sql);
      echo json_encode(array('status' => 'success', 'action' => $actionType, 'message' => 'Field updated (template_to_urls).'));
  } elseif (in_array($columnName, $qs_table_columns)) {
      $sql = "UPDATE cookiecutterQS_qs_url SET `$columnName` = '$newValue' WHERE id = $rowId AND domain = '$domain'";
      $db->Execute($sql);
      echo json_encode(array('status' => 'success', 'action' => $actionType, 'message' => 'Field updated (cookiecutterQS_qs_url).'));
  } else {
      echo json_encode(array('status' => 'error', 'message' => 'Invalid column.'));
  }

  exit;
}

// Initialize
session_start();

$results = array();
$error = '';
$current_page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$per_page = isset($_GET['per_page']) ? max(1, (int)$_GET['per_page']):(isset($form_data['per_page']) ? (int)$form_data['per_page'] : 20);
$offset = ($current_page - 1) * $per_page;

// Save form data to session on POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_GET['page']) && isset($_GET['action']) && $_GET['action'] == 'get_urls_data') {
  $_SESSION['form_data'] = $_POST;
}

$form_data = $_SESSION['form_data'] ?? [];

if (isset($_GET['action']) && $_GET['action'] == 'get_urls_data') {
    $selected_table = $form_data['database_table'] ?? '';
    $selected_domain = trim($form_data['url_domain_list'] ?? '');
    $search_url = trim($form_data['url'] ?? '');

    $include_textboxes = isset($form_data['include_textboxes']);
    $include_templates = isset($form_data['include_templates']);
    $include_meta      = isset($form_data['include_meta']);

    if ($selected_table == 'template_to_urls') {
        $fields = "id, domain, url";

        if ($include_meta) {
            $fields .= ", meta_title, meta_description";
        }

        if ($include_textboxes) {
            $fields .= ", textbox1, textbox2, textbox3, textbox4, textbox5";
        }

        if ($include_templates) {
            $fields .= ", template, template1, template2, template3, template4, template5, cookiecutterqs_temp";
        }

        $sql = "SELECT $fields FROM template_to_urls WHERE domain = '" . zen_db_input($selected_domain) . "'";
        $count_sql = "SELECT COUNT(*) as total FROM template_to_urls WHERE domain = '" . zen_db_input($selected_domain) . "'";

        if (!empty($search_url)) {
            $sql .= " AND url LIKE '%" . zen_db_input($search_url) . "%'";
            $count_sql .= " AND url LIKE '%" . zen_db_input($search_url) . "%'";
        }

        $sql .= " ORDER BY id DESC LIMIT $per_page OFFSET $offset";

    } elseif ($selected_table == 'cookiecutterQS_qs_url') {
        $fields = "id, domain, qs_url";

        if ($include_textboxes) {
            $fields .= ",qsTextbox1, qsTextbox2, qsTextbox3, qsTextbox4, qsTextbox5,
                        qsTextbox1_mode, qsTextbox2_mode, qsTextbox3_mode, qsTextbox4_mode, qsTextbox5_mode";
        }

        $sql = "SELECT $fields FROM cookiecutterQS_qs_url WHERE domain = '" . zen_db_input($selected_domain) . "'";
        $count_sql = "SELECT COUNT(*) as total FROM cookiecutterQS_qs_url WHERE domain = '" . zen_db_input($selected_domain) . "'";

        if (!empty($search_url)) {
            $sql .= " AND qs_url LIKE '%" . zen_db_input($search_url) . "%'";
            $count_sql .= " AND qs_url LIKE '%" . zen_db_input($search_url) . "%'";
        }

        $sql .= " ORDER BY id DESC LIMIT $per_page OFFSET $offset";

    } elseif ($selected_table == '2') {
        $sql = "SELECT domain, header, footer, template1, template2, template3, template4, template5,
                        meta_title, meta_description, url
                FROM template_to_urls
                WHERE regular_mode = 2 AND domain = '" . zen_db_input($selected_domain) . "'";

        $count_sql = "SELECT COUNT(*) as total FROM template_to_urls WHERE regular_mode = 2 AND domain = '" . zen_db_input($selected_domain) . "'";

        if (!empty($search_url)) {
            $sql .= " AND url LIKE '%" . zen_db_input($search_url) . "%'";
            $count_sql .= " AND url LIKE '%" . zen_db_input($search_url) . "%'";
        }

        $sql .= " ORDER BY id DESC LIMIT $per_page OFFSET $offset";

    } else {
        $error = 'Invalid table selected.';
    }

    if (empty($error)) {
        $stmt = $db->Execute($sql);
        $results = array();
        while (!$stmt->EOF) {
            $results[] = $stmt->fields;
            $stmt->MoveNext();
        }

        $total_stmt = $db->Execute($count_sql);
        $total_rows = $total_stmt->fields['total'] ?? 0;
        $total_pages = ceil($total_rows / $per_page);
    }
}

// Clear session
if (isset($_GET['clear'])) {
    unset($_SESSION['form_data']);
    header("Location: meta_new.php");
    exit;
}




/* ==================================================================== */


?>
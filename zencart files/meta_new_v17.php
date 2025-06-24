<?php
/**
 * @copyright Copyright 2003-2024 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: proseLA 2023 Oct 24 Modified in v2.0.0-alpha1 $
 */
require('includes/application_top.php');

// Assuming this is FILENAME_META_NEW (your form posts here)
require('includes/application_top.php');

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
<!doctype html>
<html <?php echo HTML_PARAMS; ?>>
  <head>
  <?php require DIR_WS_INCLUDES . 'admin_html_head.php'; ?>
<!-- Froala Editor Styles and Scripts -->
<link href="https://cdn.jsdelivr.net/npm/froala-editor@4.0.12/css/froala_editor.pkgd.min.css" rel="stylesheet" type="text/css" />
<script src="https://cdn.jsdelivr.net/npm/froala-editor@4.0.12/js/froala_editor.pkgd.min.js"></script>
<style>
.pagination > li > a,
.pagination > li > span {
  padding: 8px 14px;
  font-size: 13px;
  color: #337ab7;
  border: 1px solid #ddd;
  background: #fff;
  margin: 0 2px;
  border-radius: 4px;
  transition: all 0.2s ease-in-out;
}

.pagination > li.active > a {
  background-color: #337ab7;
  color: #fff;
  border-color: #337ab7;
}

.pagination > li.disabled > a {
  color: #999;
  pointer-events: none;
  background-color: #f5f5f5;
  border-color: #ddd;
}
</style>

  </head>
  <body>
    <?php echo $outCount;?>
    <!-- header //-->
    <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
    <!-- header_eof //-->
    <?php
    echo $output;
    ?>
    <div class="container-fluid">
      <h1 class="pageHeading"><?php echo HEADING_TITLE; ?></h1>
      <!-- body //-->

      <!-- body_text //-->
     
      

        <!-- eof: Locate template Files -->
        <div class="row"><?php echo zen_draw_separator(); ?></div>
        <!-- bof: Locate all files -->

        <div class="row">
          <div class="col-sm-12"><?php echo TEXT_ALL_FILES_CONSTANT; ?></div>
        </div>
        <?php echo zen_draw_form('get_urls_data', FILENAME_META_NEW, 'action=get_urls_data', 'post', 'class="form-horizontal"'); ?>
        <div class="form-group">
            <?php echo zen_draw_label('Search for URL', 'configuration_key_af', 'class="control-label col-sm-3"'); ?>
          <div class="col-sm-9 col-md-6"><?php echo zen_draw_input_field('url', $q_all, 'id="url" class="form-control" size="40" placeholder="Enter Url you want to search"'); ?></div>
        </div>

       <!-- Website list dropdown -->
        <div class="form-group">
            <?php 
            $website_sql = "SELECT * FROM websites ORDER BY website_id ASC";
            $websites = $db->Execute($website_sql);

            $za_lookup_domain = array();
            while (!$websites->EOF) {
                $za_lookup_domain[] = array('id' => $websites->fields['website'], 'text' => $websites->fields['website']);
                $websites->MoveNext();
            }
            ?>
            <?php echo zen_draw_label('Select Website to edit', 'zv_files_website', 'class="control-label col-sm-3"'); ?>
            <div class="col-sm-9 col-md-6">
                <?php echo zen_draw_pull_down_menu('url_domain_list', $za_lookup_domain, (isset($action) && $action == 'get_urls_data' ? (int)$_POST['url_domain_list'] : '1'), 'id="url_domain_list" class="form-control select2"'); ?>
            </div>
        </div>

<!-- Files lookups dropdown -->
<div class="form-group">
    <?php
    $za_lookup = array(
        array('id' => 'template_to_urls', 'text' => 'ALL URLS'),
        array('id' => 'cookiecutterQS_qs_url', 'text' => 'QS URLS'),
        array('id' => '2', 'text' => 'HomePage Only'),
    );

    // Use session-stored form data if available
    $form_data = $_SESSION['form_data'] ?? [];
    $selected_table = $form_data['database_table'] ?? 'template_to_urls'; // default to 'template_to_urls'
    ?>

    <?php echo zen_draw_label("Select Table", 'zv_files_lookup', 'class="control-label col-sm-3"'); ?>
    <div class="col-sm-9 col-md-6">
        <?php echo zen_draw_pull_down_menu(
            'database_table',
            $za_lookup,
            $selected_table,
            'id="database_table" class="form-control select2"'
        ); ?>
    </div>
</div>


        <div class="form-group">
            <?php echo zen_draw_label('Load TextBoxes?', 'include_textboxes', 'class="control-label col-sm-3"'); ?>
          <div class="col-sm-9 col-md-6">
            <div class="checkbox">
     <label for="include_textboxes">
     <input type="checkbox" name="include_textboxes" id="include_textboxes"  <?php echo (isset($form_data['include_textboxes']) ? 'checked' : ''); ?> >
 </label>
            </div>
          </div>
        </div>

        <div class="form-group">
            <?php echo zen_draw_label('Load Templates?', 'include_templates', 'class="control-label col-sm-3"'); ?>
          <div class="col-sm-9 col-md-6">
            <div class="checkbox">
              <label for="include_templates">
              <input type="checkbox" name="include_templates" id="include_templates" value="1" <?php echo (isset($form_data['include_templates']) ? 'checked' : ''); ?> >

            </label>
            </div>
          </div>
        </div>


        <div class="form-group">
            <?php echo zen_draw_label('Load Meta Info?', 'include_meta', 'class="control-label col-sm-3"'); ?>
          <div class="col-sm-9 col-md-6">
            <div class="checkbox">
              <label for="include_meta">
              <input type="checkbox" name="include_meta" id="include_meta" value="1" <?php echo (isset($form_data['include_meta']) ? 'checked' : ''); ?> >

            </label>
            </div>
          </div>
        </div>


        <div class="form-group">
            <?php echo zen_draw_label('Load SiteMap?', 'include_sitemap', 'class="control-label col-sm-3"'); ?>
          <div class="col-sm-9 col-md-6">
            <div class="checkbox">
              <label for="include_sitemap">
              <input type="checkbox" name="include_sitemap" id="include_sitemap" value="1" <?php echo (isset($form_data['include_sitemap']) ? 'checked' : ''); ?> >

 
            </label>
            </div>
          </div>
        </div>

        <div class="form-group">
            <?php echo zen_draw_label('Load Index?', 'include_index', 'class="control-label col-sm-3"'); ?>
          <div class="col-sm-9 col-md-6">
            <div class="checkbox">
              <label for="include_index">
              <?php echo zen_draw_checkbox_field('include_index', '', (isset($form_data['include_index']) ? true : false), 'id="include_index" aria-label="include_index"'); ?>

              </label>
            </div>
          </div>
        </div>
      
        <div class="form-group">
          <div class="col-sm-12 text-right">
          <input type="hidden" name="form_submit" value="1">
            <button type="submit" class="btn btn-primary"><?php echo TEXT_BUTTON_SEARCH; ?></button>
            <input type="button" class="btn btn-primary" value="<?php echo TEXT_BUTTON_REGEX_SEARCH; ?>" onClick="document.get_urls_data.action = '<?php echo zen_href_link(FILENAME_META_NEW, 'action=get_urls_data&m=r') ?>'; document.get_urls_data.submit();" title="<?php echo TEXT_BUTTON_REGEX_SEARCH_ALT; ?>">
            <a href="meta_new.php?clear=1" class="btn btn-warning">Clear Search</a>
          </div>
        </div>
        <?php echo '</form>'; ?>
        <!-- eof: Locate all files -->
     
      <div class="row"><?php echo zen_draw_separator(); ?></div>

      <?php if (!empty($results)): ?>

<h3>Search Results:</h3>
<div class="row">

<div class="col-lg-9 col-sm-9">
<?php
$current_per_page = isset($_GET['per_page']) ? (int)$_GET['per_page'] : ($_SESSION['form_data']['per_page'] ?? 20);
?>
<div class="text-left" style="margin-bottom: 10px;">
  <form method="get" action="meta_new.php" class="form-inline" style="display: inline-block;">
    <input type="hidden" name="action" value="get_urls_data">
    <input type="hidden" name="page" value="1">
    <label for="per_page">Show</label>
    <select name="per_page" id="per_page" class="form-control input-sm" onchange="this.form.submit()">
      <?php
      foreach ([10, 20, 50, 100] as $opt) {
        $selected = ($current_per_page == $opt) ? 'selected' : '';
        echo "<option value=\"$opt\" $selected>$opt</option>";
      }
      ?>
    </select>
    <label>records per page</label>
  </form>
</div>
</div>

<div class="col-sm-3 col-lg-3 text-right">
    <label>Search:
    <input type="text" id="tableSearch" class="form-control" placeholder="Search in this page...">

    </label>
    <p id="noResultsMsg" style="display:none; margin-top:10px; color:red;">No records found on this page.</p>

  </div>

</div>



<table class="table table-bordered table-striped table-sm">
    <thead>
        <tr>
            <?php
            // Auto detect column names from first row
            foreach (array_keys($results[0]) as $column_name) {
                echo '<th>' . htmlspecialchars(ucwords(str_replace('_', ' ', $column_name))) . '</th>';
            }
            
            ?>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($results as $row): ?>
    <tr>
        <?php foreach ($row as $column_name => $cell): ?>
            <td id="template_cell_<?php echo htmlspecialchars($row['id']) . '_' . htmlspecialchars($column_name); ?>">
                <?php
                // Template fields
                $template_fields = array(
                    'template', 'template1', 'template2', 'template3', 'template4', 'template5','textbox1', 'textbox2', 'textbox3', 'textbox4', 'textbox5','qsTextbox1','qsTextbox2','qsTextbox3','qsTextbox4','qsTextbox5'
                );

                
                
                // Meta fields
                $meta_fields = array('meta_title', 'meta_description');

                if ($column_name === 'cookiecutterqs_temp' && isset($form_data['include_templates'])) {
                    // Show value
                    echo nl2br(htmlspecialchars($cell));

                    if (trim($cell) != '') {
                        // Edit Button - opens CookieQS Modal
                        echo ' <button class="btn btn-xs btn-outline-primary" style="padding:2px 5px; font-size:10px;" title="Edit" 
                            onclick="openCookieQSModal(\'edit\', \'' . htmlspecialchars($column_name) . '\', \'' . htmlspecialchars($row['domain']) . '\', \'' . htmlspecialchars($cell) . '\', \'' . $row['id'] . '\')">✏️</button>';
      
                         
                        // Delete Button
                        echo ' <button class="btn btn-xs btn-outline-danger" style="padding:2px 5px; font-size:10px;" title="Delete" 
                            onclick="deleteTemplateField(\'' . htmlspecialchars($column_name) . '\', \'' . htmlspecialchars($row['domain']) . '\', \'' . $row['id'] . '\')">🗑️</button>';
                    } else {
                        // Add Button
                        echo ' <button class="btn btn-xs btn-outline-success" style="padding:2px 5px; font-size:10px;" title="Add" 
                            onclick="openCookieQSModal(\'add\', \'' . htmlspecialchars($column_name) . '\', \'' . htmlspecialchars($row['domain']) . '\', \'\', \'' . $row['id'] . '\')">➕</button>';
                    }

                } elseif (
                  (in_array($column_name, ['template', 'template1', 'template2', 'template3', 'template4', 'template5']) && isset($form_data['include_templates'])) ||
                  (in_array($column_name, ['textbox1', 'textbox2', 'textbox3', 'textbox4', 'textbox5','qsTextbox1','qsTextbox2','qsTextbox3','qsTextbox4','qsTextbox5']) && isset($form_data['include_textboxes']))
              ) {
                    // Show value
                    echo nl2br(htmlspecialchars($cell));

                    if (trim($cell) != '') {
                        // Edit button for normal templates and textboxes 1-5
                        echo ' <button class="btn btn-xs btn-outline-primary" style="padding:2px 5px; font-size:10px;" title="Edit" 
                            onclick="openTemplateModal(\'edit\', \'' . htmlspecialchars($column_name) . '\', \'' . htmlspecialchars($row['domain']) . '\', \'' . htmlspecialchars($cell) . '\', \'' . $row['id'] . '\')">✏️</button>';
                          
                            echo ' <button class="btn btn-xs btn-outline-primary" style="padding:2px 5px; font-size:10px;" title="View" 
                            onclick="edit_each_temp(\'' . htmlspecialchars($column_name) . '\', \'' . htmlspecialchars($row['domain']) . '\', \'' . htmlspecialchars($cell) . '\', \'' . $row['id'] . '\')">👁️</button>';
                    

                        // Delete button
                        echo ' <button class="btn btn-xs btn-outline-danger" style="padding:2px 5px; font-size:10px;" title="Delete" 
                            onclick="deleteTemplateField(\'' . htmlspecialchars($column_name) . '\', \'' . htmlspecialchars($row['domain']) . '\', \'' . $row['id'] . '\')">🗑️</button>';
                    } else {
                        // Add button
                        echo ' <button class="btn btn-xs btn-outline-success" style="padding:2px 5px; font-size:10px;" title="Add" 
                            onclick="openTemplateModal(\'add\', \'' . htmlspecialchars($column_name) . '\', \'' . htmlspecialchars($row['domain']) . '\', \'\', \'' . $row['id'] . '\')">➕</button>';
                    }

                } elseif (in_array($column_name, $meta_fields) && isset($form_data['include_meta'])) {
                    // Show value
                    echo nl2br(htmlspecialchars($cell));

                    if (trim($cell) != '') {
                        // Edit button for meta fields
                        echo ' <button class="btn btn-xs btn-outline-primary" style="padding:2px 5px; font-size:10px;" title="Edit" 
                            onclick="openMetaModal(\'edit\', \'' . htmlspecialchars($column_name) . '\', \'' . htmlspecialchars($row['domain']) . '\', \'' . htmlspecialchars($cell) . '\', \'' . $row['id'] . '\')">✏️</button>';

                        // Clear button
                        echo ' <button class="btn btn-xs btn-outline-danger" style="padding:2px 5px; font-size:10px;" title="Clear" 
                            onclick="clearMetaField(\'' . htmlspecialchars($column_name) . '\', \'' . htmlspecialchars($row['domain']) . '\', \'' . $row['id'] . '\')">🗑️</button>';
                    } else {
                        // Add button
                        echo ' <button class="btn btn-xs btn-outline-success" style="padding:2px 5px; font-size:10px;" title="Add" 
                            onclick="openMetaModal(\'add\', \'' . htmlspecialchars($column_name) . '\', \'' . htmlspecialchars($row['domain']) . '\', \'\', \'' . $row['id'] . '\')">➕</button>';
                    }

                } else {
                    // Normal display
                    echo nl2br(htmlspecialchars($cell));
                }
                ?>
            </td>
        <?php endforeach; ?>
    </tr>
     <?php endforeach; ?>
    </tbody>
</table>
<?php if (!empty($results) && isset($total_pages) && $total_pages > 1): ?>
 

<div class="row">
<div class="col-sm-3 col-lg-3 text-left">
    <form method="get" action="meta_new.php" class="form-inline" style="margin-bottom: 10px;">
      <input type="hidden" name="action" value="get_urls_data">
      <input type="hidden" name="per_page" value="<?= $per_page ?>">
      <label for="jump_page">Jump to:</label>
      <select name="page" id="jump_page" class="form-control input-sm" onchange="this.form.submit()" style="margin-left: 5px;">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
          <option value="<?= $i ?>" <?= ($i == $current_page) ? 'selected' : '' ?>>
            Page <?= $i ?>
          </option>
        <?php endfor; ?>
      </select>
    </form>
  </div>
  <div class="col-sm-6 col-lg-6 text-center">
    <nav aria-label="Page navigation" style="margin-top: 20px;">
      <ul class="pagination pagination-sm">

        <!-- Prev -->
        <li class="<?= ($current_page <= 1) ? 'disabled' : ''; ?>">
          <a href="meta_new.php?action=get_urls_data&page=<?= max(1, $current_page - 1) ?>&per_page=<?= $per_page ?>" aria-label="Previous">
            <span aria-hidden="true">&laquo;</span> Prev
          </a>
        </li>

        <!-- Page Numbers -->
        <?php
        $range = 3; // Show +/- 3 pages around current
        $start = max(1, $current_page - $range);
        $end = min($total_pages, $current_page + $range);

        if ($start > 1) {
          echo '<li><a href="#">...</a></li>';
        }

        for ($i = $start; $i <= $end; $i++): ?>
          <li class="<?= ($i == $current_page) ? 'active' : ''; ?>">
            <a href="meta_new.php?action=get_urls_data&page=<?= $i ?>&per_page=<?= $per_page ?>"><?= $i ?></a>
          </li>
        <?php endfor;

        if ($end < $total_pages) {
          echo '<li><a href="#">...</a></li>';
        }
        ?>

        <!-- Next -->
        <li class="<?= ($current_page >= $total_pages) ? 'disabled' : ''; ?>">
          <a href="meta_new.php?action=get_urls_data&page=<?= min($total_pages, $current_page + 1) ?>&per_page=<?= $per_page ?>" aria-label="Next">
            Next <span aria-hidden="true">&raquo;</span>
          </a>
        </li>

      </ul>
    </nav>
  </div>
  <div class="col-sm-3 col-lg-3 text-right">
    <p class="text-muted" style="margin: 7px 0;">
      Showing page <strong><?= $current_page ?></strong> of <strong><?= $total_pages ?></strong>
      (Total: <?= $total_rows ?> results)
    </p>
  </div>
</div>
<?php if (!empty($results) && $total_pages > 1): ?>

<?php endif; ?>

<?php endif; ?>



<?php else: ?>

<?php if (isset($_POST['action']) && $_POST['action'] == 'get_urls_data'): ?>
    <div class="alert alert-info">No results found.</div>
<?php endif; ?>

<?php endif; ?>

      <!-- body_text_eof //-->
    </div>

<!-- templates dropdown from custom_template_file //-->
<div class="form-group">
    <?php 
    // Fetch all template names from custom_template_file
    $template_sql = "SELECT id, template_name FROM custom_template_file ORDER BY template_name ASC";
    $templates = $db->Execute($template_sql);

    $za_template_names = array();
    while (!$templates->EOF) {
        $za_template_names[] = array(
            'id' => $templates->fields['id'],
            'text' => $templates->fields['template_name']
        );
        $templates->MoveNext();
    }
    ?>
    <?php echo zen_draw_label('Select Template to Edit', 'zv_template_list', 'class="control-label col-sm-3"'); ?>
    <div class="col-sm-9 col-md-6">
        <?php echo zen_draw_pull_down_menu('template_list', $za_template_names, (isset($_POST['template_list']) ? $_POST['template_list'] : ''), 'id="template_list" class="form-control select2"'); ?>
    </div>
</div>


<!-- Modal for templates list dropdown -->
<?php
// ----- PHP: Template Modal with View Logic -----
$template_sql = "SELECT id, template_name FROM custom_template_file ORDER BY template_name ASC";
$templates = $db->Execute($template_sql);

$za_template_names = array();
while (!$templates->EOF) {
    $za_template_names[] = array(
        'id' => $templates->fields['template_name'],
        'text' => $templates->fields['template_name']
    );
    $templates->MoveNext();
}
?>


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

<!-- Modal for templates list dropdown -->


<!-- Cookiecutterqs Temp Modal -->
<div class="modal fade" id="cookieqsModal" tabindex="-1" role="dialog" aria-labelledby="cookieqsModalLabel">
  <div class="modal-dialog" role="document">
    <form id="cookieqs_form" onsubmit="return saveCookieQSValue();">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="cookieqsModalLabel">Edit Cookiecutterqs Temp</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="cookieqs_columnName" name="columnName">
          <input type="hidden" id="cookieqs_domain" name="domain">
          <input type="hidden" id="cookieqs_rowId" name="rowId">
          <input type="hidden" id="cookieqs_actionType" name="actionType">
          
          <div class="form-group">
            <label for="cookieqs_dropdown">Select Template:</label>
            <select id="cookieqs_dropdown" name="newValue" class="form-control select2" style="width: 100%">
              <?php
              $qs_sql = "SELECT id, name FROM cookie_cutter_qs ORDER BY name ASC";
              $qs_results = $db->Execute($qs_sql);
              while (!$qs_results->EOF) {
                echo '<option value="' . htmlspecialchars($qs_results->fields['name']) . '">' . htmlspecialchars($qs_results->fields['name']) . '</option>';
                $qs_results->MoveNext();
              }
              ?>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </div>
    </form>
  </div>
</div>



<!-- MODAL -->
<div class="modal fade" id="templateEditModal" tabindex="-1" role="dialog" aria-labelledby="templateEditModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Select & View Template</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="form-group">
  <?php echo zen_draw_pull_down_menu('modal_template_list', $za_template_names, '', 'id="modal_template_list" class="form-control select2"'); ?>
</div>

<!-- Action Buttons -->
<button class="btn btn-sm btn-primary mb-2 ml-3" onclick="viewSelectedTemplate()">View Template</button>
<button class="btn btn-sm btn-info mb-2" onclick="viewSelectedCSS()">View CSS</button>
<button class="btn btn-sm btn-warning mb-2" onclick="viewSelectedJS()">View JS</button>

<!-- Editors Area -->
<div id="template_view_section" style="border:1px solid #ccc; padding:10px; display:none;">
  <div id="template_html_wrapper" style="display: none;">
    <h6>Template HTML</h6>
    <div id="template_html_editor"></div>
  </div>

  <div id="template_css_wrapper" style="display: none;">
    <h6>Edit CSS</h6>
    <div id="template_css_editor"></div>
  </div>

  <div id="template_js_wrapper" style="display: none;">
    <h6>Edit JavaScript</h6>
    <div id="template_js_editor"></div>
  </div>
</div>

      <div class="modal-footer">
        <button type="button" class="btn btn-success" onclick="saveTemplateSelection()">Save</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>


<!-- Meta Field Edit Modal -->
<div class="modal fade" id="metaEditModal" tabindex="-1" role="dialog" aria-labelledby="metaEditModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="metaEditModalLabel">Edit Meta Content</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="meta_columnName">
        <input type="hidden" id="meta_domain">
        <input type="hidden" id="meta_rowId">
        <input type="hidden" id="meta_actionType">
        
        <div class="form-group">
          <label for="meta_content">Content:</label>
          <textarea id="meta_content" class="form-control" rows="5"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="saveMetaContent()">Save Changes</button>
      </div>
    </div>
  </div>
</div>




<!-- Update Template CSS Modal -->
<!-- MODAL -->
<div class="modal fade" id="update_template_css_modal" tabindex="-1" role="dialog" aria-labelledby="templateEditModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="metaEditModalLabel">View Template</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <button class="btn btn-sm btn-secondary" onclick="showTemplateHTML()">View HTML</button>
          <button class="btn btn-sm btn-secondary" onclick="showTemplateCSS()">View CSS</button>
          <button class="btn btn-sm btn-secondary" onclick="showTemplateJS()">View JS</button>
        </div>

        <div id="template_edit_section" style="display:none;">
          <div id="froala_editor"></div>
        </div>

        <div id="css_edit_section" style="display:none;">
          <textarea id="css_editor_raw" class="form-control" style="min-height: 300px; font-family: monospace;"></textarea>
        </div>

        <div id="js_edit_section" style="display:none;">
          <div id="js_editor"></div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="saveUpdatedTemplateFile()">Save Changes</button>
      </div>
    </div>
  </div>
</div>


    <!-- body_eof //-->

    <!-- footer //-->
    <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
  
    <!-- footer_eof //-->
    <!-- script for templates list dropdown -->
    <script>
$('#templateEditModal').on('shown.bs.modal', function () {
  $('#modal_template_list').select2({
    dropdownParent: $('#templateEditModal'),
    width: '100%',
    theme: 'bootstrap',
    placeholder: 'Please select',
    allowClear: true
  }).addClass('form-control');
});

// Destroy Select2 when modal is hidden to prevent duplicates
$('#templateEditModal').on('hidden.bs.modal', function () {
  $('#modal_template_list').select2('destroy');
});

var currentTemplateField = '';
var currentDomain = '';
var currentOriginalValue = '';
var currentAction = '';
var currentRowId = '';

function openTemplateModal(action, columnName, domain, currentValue, rowId) {
    currentAction = action;
    currentTemplateField = columnName;
    currentDomain = domain;
    currentOriginalValue = currentValue;
    currentRowId = rowId;

    console.log('Opening modal: action=' + action + ', field=' + columnName + ', domain=' + domain + ', id=' + rowId + ', value=' + currentValue);

    // Pre-select dropdown
    $('#templateEditModal').on('shown.bs.modal', function () {
        if ($("#modal_template_list option[value='" + currentValue + "']").length) {
            $('#modal_template_list').val(currentValue).trigger('change');
        }
    });

    // Show modal
    $('#templateEditModal').modal('show');
}

function saveTemplateSelection() {
    var selectedTemplate = $('#modal_template_list').val();
    var selectedText = $('#modal_template_list option:selected').text(); // Get the display text
 console.log('row:'+currentRowId);
 console.log('Domian:'+currentDomain);
 console.log('field:'+currentTemplateField);
 console.log('Sleected:'+selectedTemplate);
 console.log('Action:'+currentAction);
    $.ajax({
        url: 'meta_new.php',
        type: 'POST',
        data: {
                ajax_action: 'save_template_field',
                rowId: currentRowId, // ✅ match PHP expected key
                domain: currentDomain,
                columnName: currentTemplateField,
                newValue: selectedTemplate,
                actionType: currentAction
              },
        success: function(response) {
            console.log(response);

            // Rebuild the cell HTML using the selected text
            var newHtml = nl2br(htmlspecialchars(selectedText));

            if (selectedTemplate.trim() != '') {
                // Edit button
                  newHtml += ' <button class="btn btn-xs btn-outline-primary" style="padding:2px 5px; font-size:10px;" title="Edit" ' +
                    'onclick="openTemplateModal(\'edit\', \'' + currentTemplateField + '\', \'' + currentDomain + '\', \'' + escapeQuotes(selectedTemplate) + '\', \'' + currentRowId + '\')">✏️</button>';
                
                    newHtml += ' <button class="btn btn-xs btn-outline-primary" style="padding:2px 5px; font-size:10px;" title="Edit" ' +
                    'onclick="edit_each_temp( \'' + currentTemplateField + '\', \'' + currentDomain + '\', \'' + escapeQuotes(selectedTemplate) + '\')">👁️</button>';

                // Delete button
                newHtml += ' <button class="btn btn-xs btn-outline-danger" style="padding:2px 5px; font-size:10px;" title="Delete" ' +
                    'onclick="deleteTemplateField(\'' + currentTemplateField + '\', \'' + currentDomain + '\', \'' + currentRowId + '\')">🗑️</button>';
            } else {
                // Add button
                newHtml += ' <button class="btn btn-xs btn-outline-success" style="padding:2px 5px; font-size:10px;" title="Add" ' +
                    'onclick="openTemplateModal(\'add\', \'' + currentTemplateField + '\', \'' + currentDomain + '\', \'\', \'' + currentRowId + '\')">➕</button>';
            }

            // Update the table cell
            var cellId = 'template_cell_' + currentRowId + '_' + currentTemplateField;
            $('#' + cellId).html(newHtml);

            // Close modal
            $('#templateEditModal').modal('hide');

            // Optional message
            alert('Template ' + currentAction + ' saved and updated!');
        },
        error: function() {
            alert('Error saving template!');
        }
    });
}

// Helper function to escape quotes for HTML attributes
function escapeQuotes(text) {
    return text.replace(/'/g, "\\'").replace(/"/g, '&quot;');
}

// Helper function to handle HTML special chars
function htmlspecialchars(str) {
    return str.replace(/&/g, '&amp;')
              .replace(/</g, '&lt;')
              .replace(/>/g, '&gt;')
              .replace(/"/g, '&quot;')
              .replace(/'/g, '&#039;');
}

// Helper to handle line breaks (optional)
function nl2br(str) {
    return str.replace(/\n/g, '<br>');
}


function deleteTemplateField(columnName, domain, rowId) {
    if (!confirm('Are you sure you want to delete this template field?')) {
        return; // Cancel
    }

    $.ajax({
        url: 'meta_new.php',
        type: 'POST',
        data: {
            ajax_action: 'delete_template_field',
            id: rowId,
            domain: domain,
            columnName: columnName
        },
        success: function(response) {
            console.log(response);

            // After delete → show Add button
            var newHtml = '';
            if(columnName =='cookiecutterqs_temp')
            {
              newHtml += ' <button class="btn btn-xs btn-outline-success" style="padding:2px 5px; font-size:10px;" title="Add" ' +
                'onclick="openCookieQSModal(\'add\', \'' + columnName + '\', \'' + domain + '\', \'\', \'' + rowId + '\')">➕</button>';
            }
            else

            newHtml += ' <button class="btn btn-xs btn-outline-success" style="padding:2px 5px; font-size:10px;" title="Add" ' +
                'onclick="openTemplateModal(\'add\', \'' + columnName + '\', \'' + domain + '\', \'\', \'' + rowId + '\')">➕</button>';

            // Update the table cell
            var cellId = 'template_cell_' + rowId + '_' + columnName;
            $('#' + cellId).html(newHtml);

            alert('Template field deleted!');
        },
        error: function() {
            alert('Error deleting template!');
        }
    });
}

    $('#cookieqsModal').on('shown.bs.modal', function () {
      $('#cookieqs_dropdown').select2({
        dropdownParent: $('#cookieqsModal'),
        width: '100%',
        theme: 'bootstrap',
        placeholder: 'Please select',
        allowClear: true
      }).addClass('form-control')
    });

    $('#cookieqsModal').on('hidden.bs.modal', function () {
  $('#cookieqs_dropdown').select2('destroy');
});

function openCookieQSModal(action, columnName, domain, currentValue, rowId) {
    $('#cookieqsModalLabel').text((action === 'edit' ? 'Edit' : 'Add') + ' Cookiecutterqs Temp');
    $('#cookieqs_columnName').val(columnName);
    $('#cookieqs_domain').val(domain);
    $('#cookieqs_rowId').val(rowId);
    $('#cookieqs_actionType').val(action);
    $('#cookieqs_newValue').val(currentValue); // fallback if dropdown is not preloaded

    // Preselect dropdown
    $('#cookieqsModal').on('shown.bs.modal', function () {
        if ($("#cookieqs_dropdown option[value='" + currentValue + "']").length) {
            $('#cookieqs_dropdown').val(currentValue).trigger('change');
        }
    });

    $('#cookieqsModal').modal('show');
}



function saveCookieQSValue() {
    const data = {
        ajax_action: 'save_template_field',
        columnName: $('#cookieqs_columnName').val(),
        domain: $('#cookieqs_domain').val(),
        rowId: $('#cookieqs_rowId').val(),
        actionType: $('#cookieqs_actionType').val(),
        newValue: $('#cookieqs_dropdown').val()
    };

    $.post('meta_new.php', data, function(response) {
        const res = JSON.parse(response);
        if (res.status === 'success') {
            $('#cookieqsModal').modal('hide');
            alert('QSTemplate field Added!');
            const cellId = `#template_cell_${data.rowId}_${data.columnName}`;
            $(cellId).html(`${$('#cookieqs_dropdown').select2('data')[0].text} 
                <button class="btn btn-xs btn-outline-primary" title="Edit" onclick="openCookieQSModal('edit', '${data.columnName}', '${data.domain}', '${$('#cookieqs_dropdown').val()}', '${data.rowId}')">✏️</button>
                <button class="btn btn-xs btn-outline-danger" title="Delete" onclick="deleteTemplateField('${data.columnName}', '${data.domain}', '${data.rowId}')">🗑️</button>`);
        } else {
            alert(res.message);
        }
    });

    return false; // prevent default form submit
}


// Meta Field Functions
function openMetaModal(action, columnName, domain, currentValue, rowId) {
    $('#metaEditModalLabel').text((action === 'edit' ? 'Edit' : 'Add') + ' ' + columnName.replace('_', ' '));
    $('#meta_columnName').val(columnName);
    $('#meta_domain').val(domain);
    $('#meta_rowId').val(rowId);
    $('#meta_actionType').val(action);
    $('#meta_content').val(currentValue);
    
    $('#metaEditModal').modal('show');
}

function saveMetaContent() {
    var columnName = $('#meta_columnName').val();
    var domain = $('#meta_domain').val();
    var rowId = $('#meta_rowId').val();
    var actionType = $('#meta_actionType').val();
    var newValue = $('#meta_content').val();

    $.ajax({
        url: 'meta_new.php',
        type: 'POST',
        data: {
            ajax_action: 'save_template_field',
            columnName: columnName,
            domain: domain,
            rowId: rowId,
            actionType: actionType,
            newValue: newValue
        },
        success: function(response) {
            var res = JSON.parse(response);
            if (res.status === 'success') {
                // Update the cell content
                var cellId = 'template_cell_' + rowId + '_' + columnName;
                var newHtml = nl2br(htmlspecialchars(newValue)) + 
                    ' <button class="btn btn-xs btn-outline-primary" style="padding:2px 5px; font-size:10px;" title="Edit" ' +
                    'onclick="openMetaModal(\'edit\', \'' + columnName + '\', \'' + domain + '\', \'' + newValue.replace(/'/g, "\\'") + '\', \'' + rowId + '\')">✏️</button>' +
                    ' <button class="btn btn-xs btn-outline-danger" style="padding:2px 5px; font-size:10px;" title="Clear" ' +
                    'onclick="clearMetaField(\'' + columnName + '\', \'' + domain + '\', \'' + rowId + '\')">🗑️</button>';

                $('#' + cellId).html(newHtml);
                $('#metaEditModal').modal('hide');
                alert('' + columnName + ' saved and updated!');
            } else {
                alert('Error: ' + res.message);
            }
        },
        error: function() {
            alert('Error saving meta content!');
        }
    });
}

function clearMetaField(columnName, domain, rowId) {
    if (!confirm('Are you sure you want to clear this meta field?')) {
        return;
    }

    $.ajax({
        url: 'meta_new.php',
        type: 'POST',
        data: {
            ajax_action: 'save_template_field',
            columnName: columnName,
            domain: domain,
            rowId: rowId,
            newValue: '',
            actionType: 'clear'
        },
        success: function(response) {
            var res = JSON.parse(response);
            if (res.status === 'success') {
                // Update the cell to show Add button
                var cellId = 'template_cell_' + rowId + '_' + columnName;
                var newHtml = ' <button class="btn btn-xs btn-outline-success" style="padding:2px 5px; font-size:10px;" title="Add" ' +
                    'onclick="openMetaModal(\'add\', \'' + columnName + '\', \'' + domain + '\', \'\', \'' + rowId + '\')">➕</button>';

                $('#' + cellId).html(newHtml);
                alert('' + columnName + ' Cleared!');
            } else {
                alert('Error: ' + res.message);
            }
        },
        error: function() {
            alert('Error clearing meta field!');
        }
    });
}


let templateData = {};
let viewEditor = null;
let cssEditor = null;
let jsEditor = null;

function showOnly(section) {
  $('#template_html_wrapper').hide();
  $('#template_css_wrapper').hide();
  $('#template_js_wrapper').hide();

  if (section === 'html') {
    $('#template_html_wrapper').show();
  } else if (section === 'css') {
    $('#template_css_wrapper').show();
  } else if (section === 'js') {
    $('#template_js_wrapper').show();
  }
}

function destroyAllEditors() {
  if (viewEditor) { viewEditor.destroy(); viewEditor = null; }
  if (cssEditor) { cssEditor.destroy(); cssEditor = null; }
  if (jsEditor) { jsEditor.destroy(); jsEditor = null; }
}

function viewSelectedTemplate() {
  const selectedTemplate = $('#modal_template_list').val();
  if (!selectedTemplate) return;

  destroyAllEditors();
  $('#template_html_editor').html('');
  $('#template_css_editor').html('');
  $('#template_js_editor').html('');
  $('#template_view_section').show();

  $.ajax({
    url: 'meta_new.php',
    type: 'POST',
    data: {
      ajax_action: 'get_template_details',
      template_name: selectedTemplate
    },
    success: function (response) {
      const res = JSON.parse(response);
      if (res.result === 'success') {
        templateData = res;
        showOnly('html');

        viewEditor = new FroalaEditor('#template_html_editor', {
          key: "Ne2C1sF4D3C3A14A7D9jF1QUg1Xc2OZE1ABVJRDRNGGUH1ITrA1C7A6F6E1E4H4E1A9C6==",
          heightMin: 250,
          readOnly: true,
          events: {
            initialized: function () {
              this.html.set(res.html || 'Not found');
            }
          }
        });

      } else {
        alert(res.message);
      }
    }
  });
}

function viewSelectedCSS() {
  destroyAllEditors();
  $('#template_css_editor').html('');
  $('#template_html_editor').html('');
  $('#template_js_editor').html('');
  showOnly('css');

  cssEditor = new FroalaEditor('#template_css_editor', {
    key: "Ne2C1sF4D3C3A14A7D9jF1QUg1Xc2OZE1ABVJRDRNGGUH1ITrA1C7A6F6E1E4H4E1A9C6==",
    heightMin: 200,
    codeMirror: true,
    codeMirrorOptions: {
      mode: 'css'
    },
    events: {
      initialized: function () {
        this.html.set('<pre><code>' + (templateData.css || '/* No CSS Found */') + '</code></pre>');
      }
    }
  });
}

function viewSelectedJS() {
  destroyAllEditors();
  $('#template_js_editor').html('');
  $('#template_html_editor').html('');
  $('#template_css_editor').html('');
  showOnly('js');

  jsEditor = new FroalaEditor('#template_js_editor', {
    key: "Ne2C1sF4D3C3A14A7D9jF1QUg1Xc2OZE1ABVJRDRNGGUH1ITrA1C7A6F6E1E4H4E1A9C6==",
    heightMin: 200,
    codeMirror: true,
    codeMirrorOptions: {
      mode: 'javascript'
    },
    events: {
      initialized: function () {
        this.html.set('<pre><code>' + (templateData.js || '// No JS Found') + '</code></pre>');
      }
    }
  });
}





    $('#templateEditModal').on('hidden.bs.modal', function () {
  // Clear previous preview content
  $('#template_html').text('');
  $('#template_css').text('');
  $('#template_js').text('');
  $('#template_view_section').hide();
});


$(document).ready(function() {
    $('.select2').select2({
      placeholder: 'Please select',
      allowClear: true
    });
  });


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


function unescapeHtml(safe) {
  return $('<textarea/>').html(safe).text();
}


function highlightMatch(text, term) {
  if (!term) return text; // nothing to highlight
  const regex = new RegExp(`(${term})`, 'gi');
  return text.replace(regex, '<span style="background-color: yellow;">$1</span>');
}



function changePerPage(select) {
  var perPage = select.value;
  var currentUrl = new URL(window.location.href);
  currentUrl.searchParams.set("per_page", perPage);
  currentUrl.searchParams.set("page", 1); // Reset to first page
  window.location.href = currentUrl.toString();
}

function highlightMatch(text, term) {
  if (!term) return text;
  const regex = new RegExp(`(${term})`, 'gi');
  return text.replace(regex, '<span style="background-color: yellow;">$1</span>');
}

document.getElementById("tableSearch").addEventListener("keyup", function () {
  const input = this.value.toLowerCase().trim();
  const rows = document.querySelectorAll("table tbody tr");
  let visibleCount = 0;

  rows.forEach(row => {
    const cells = row.querySelectorAll("td");
    let rowMatch = false;

    cells.forEach(cell => {
      const originalText = cell.textContent;
      // Reset the cell content before checking
      cell.innerHTML = originalText;

      if (input && originalText.toLowerCase().includes(input)) {
        rowMatch = true;
        cell.innerHTML = highlightMatch(originalText, input);
      }
    });

    if (!input) {
      row.style.display = ""; // Show all rows if input is empty
      visibleCount++;
    } else {
      row.style.display = rowMatch ? "" : "none";
      if (rowMatch) visibleCount++;
    }
  });

  document.getElementById("noResultsMsg").style.display = (visibleCount === 0 && input) ? "block" : "none";
});



function showTemplateHTML() {
  $('#template_edit_section').show();
  $('#css_edit_section').hide();
  $('#js_edit_section').hide();
}

function showTemplateCSS() {
  $('#template_edit_section').hide();
  $('#css_edit_section').show();
  $('#js_edit_section').hide();
}

function showTemplateJS() {
  $('#template_edit_section').hide();
  $('#css_edit_section').hide();
  $('#js_edit_section').show();
}


function edit_each_temp(cell, domain, rowid) {
  currentRowId = rowid;
  const req_data = { pull_template: rowid };

  $.post('meta_new.php', req_data, function (res) {
    console.log("AJAX Response:", res);

    let convertedHtml = '';
    try {
      const encodeHtml = unescapeHtml(decodeURI((res.html || '').replace(/%(?![0-9][0-9a-fA-F]+)/g, '%25')));
      convertedHtml = encodeHtml.replace(/<\?/gm, '{?').replace(/\?>/gm, '?}');
    } catch (e) {
      console.warn("HTML decoding failed:", e);
    }

    if (!convertedHtml || !convertedHtml.trim()) {
      convertedHtml = '<p style="color: gray; text-align: center;">No template available to preview</p>';
    }

    currentTemplateName = res.file_name || rowid;

    $('#froala_editor, #css_editor_raw, #js_editor').html('');
    if (window.editor) window.editor.destroy();
    if (window.jsEditor) window.jsEditor.destroy();

    $('#update_template_css_modal').modal('show');
    $('#update_template_css_modal').off('shown.bs.modal').on('shown.bs.modal', function () {
      showTemplateHTML();

      window.editor = new FroalaEditor('#froala_editor', {
        key: "Ne2C1sF4D3C3A14A7D9jF1QUg1Xc2OZE1ABVJRDRNGGUH1ITrA1C7A6F6E1E4H4E1A9C6==",
        heightMin: 300,
        codeMirror: true,
        codeMirrorOptions: { mode: 'htmlmixed', theme: 'monokai' },
        events: {
          initialized: function () {
            this.html.set(convertedHtml);
          }
        }
      });

      $('#css_editor_raw').val(res.css || '/* No CSS found */');

      window.jsEditor = new FroalaEditor('#js_editor', {
        key: "Ne2C1sF4D3C3A14A7D9jF1QUg1Xc2OZE1ABVJRDRNGGUH1ITrA1C7A6F6E1E4H4E1A9C6==",
        heightMin: 300,
        codeMirror: true,
        codeMirrorOptions: { mode: 'javascript', theme: 'monokai' },
        events: {
          initialized: function () {
            this.html.set(res.js || '// No JS found');
          }
        }
      });
    });
  }, 'json');
}

function saveUpdatedTemplateFile() {
  const htmlContent = window.editor ? window.editor.html.get() : '';
  const cssContent = document.getElementById('css_editor_raw').value;
  const jsContent = window.jsEditor ? window.jsEditor.html.get() : '';

  $.ajax({
    url: 'meta_new.php',
    method: 'POST',
    data: {
      ajax_action: 'save_template_all_fields',
      file_name: currentTemplateName,
      html: htmlContent,
      css: cssContent,
      js: jsContent
    },
    success: function (response) {
      try {
        const res = JSON.parse(response);
        if (res.status === 'success') {
          alert('Template files updated successfully!');
        } else {
          alert('Error: ' + res.message);
        }
      } catch (e) {
        console.error('Invalid response from server:', response);
        alert('Unexpected error occurred.');
      }
    }
  });
}





</script>

    <!-- script for templates list dropdown -->
  </body>
</html>

<!-- Froala Editor JS -->

<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>

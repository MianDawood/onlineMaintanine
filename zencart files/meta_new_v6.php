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
  $template_name = zen_db_input($_POST['template_name']);

  $query = "SELECT filename, css_name, js_name FROM custom_templates_admin WHERE template_name = '" . $template_name . "' LIMIT 1";
  $result = $db->Execute($query);

  if (!$result->EOF) {
    $basePath =   '../../cdn/custom_templates/';

    $html = $basePath . $result->fields['filename'];
    $css  = @file_get_contents($basePath . 'css/' . $result->fields['css_name']);
    $js   = @file_get_contents($basePath . 'js/' . $result->fields['js_name']);

    echo json_encode([
      'status' => 'success',
      'html' => $html ?: $query,
      'css' => $css ?: $css,
      'js' => $js ?: 'Not found'
    ]);
  } else {
    echo json_encode(['status' => 'error', 'message' => 'Template not found.']);
  }
  exit;
}


//empty template field from a record///

if (isset($_POST['ajax_action']) && $_POST['ajax_action'] == 'delete_template_field') {
  $id = (int)$_POST['id'];
  $columnName = $_POST['columnName'];

  $allowed_columns = array('template', 'template1', 'template2', 'template3', 'template4', 'template5', 'cookiecutterqs_temp');

  if (in_array($columnName, $allowed_columns)) {
      $sql = "UPDATE template_to_urls 
              SET `$columnName` = '' 
              WHERE id = $id";

      $db->Execute($sql);

      echo json_encode(array('status' => 'success', 'message' => 'Template field deleted.', 'id' => $id));
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

  $allowed_columns = array('template', 'template1', 'template2', 'template3', 'template4', 'template5', 
  'cookiecutterqs_temp', 'meta_title', 'meta_description');
  if (in_array($columnName, $allowed_columns)) {
      $sql = "UPDATE template_to_urls SET `$columnName` = '$newValue' WHERE id = $rowId AND domain = '$domain'";
      $db->Execute($sql);

      echo json_encode(array('status' => 'success', 'action' => $actionType, 'message' => 'Field updated.'));
  } else {
      echo json_encode(array('status' => 'error', 'message' => 'Invalid column.'));
  }
  exit;
}




// Initialize
$results = array();
$error = '';
//david/
if (isset($_GET['action']) && $_GET['action'] == 'get_urls_data') {

  // Get selected table
  $selected_table = $_POST['database_table'];
  $selected_domain = trim($_POST['url_domain_list']);
  $search_url = trim($_POST['url']); // optional url search

  // Check checkboxes
  $include_textboxes = isset($_POST['include_textboxes']);
  $include_templates = isset($_POST['include_templates']);
  $include_meta      = isset($_POST['include_meta']);

  // No need for table_name anymore — we handle based on selected_table directly

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

      if (!empty($search_url)) {
          $sql .= " AND url LIKE '%" . zen_db_input($search_url) . "%'";
      }

  } elseif ($selected_table == 'cookiecutterQS_qs_url') {

      $fields = "id, domain, qs_url";

      if ($include_textboxes) {
          $fields .= ", qsTextbox1, qsTextbox2, qsTextbox3, qsTextbox4, qsTextbox5,
                      qsTextbox1_mode, qsTextbox2_mode, qsTextbox3_mode, qsTextbox4_mode, qsTextbox5_mode";
      }

      $sql = "SELECT $fields FROM cookiecutterQS_qs_url WHERE domain = '" . zen_db_input($selected_domain) . "'";

      if (!empty($search_url)) {
          $sql .= " AND qs_url LIKE '%" . zen_db_input($search_url) . "%'";
      }

  } elseif ($selected_table == '2') { // HomePage special case

  $sql = "SELECT domain, header, footer, template1, template2, template3, template4, template5,
                     meta_title, meta_description, url
              FROM template_to_urls
              WHERE regular_mode = 2 
                AND domain = '" . zen_db_input($selected_domain) . "'"; 

      if (!empty($search_url)) {
          $sql .= " AND url LIKE '%" . zen_db_input($search_url) . "%'";
      }

  } else {
      $error = 'Invalid table selected.';
  }

  // Execute query if no error
  if (empty($error)) {
      $stmt = $db->Execute($sql);

      // Fetch results
      $results = array();
      while (!$stmt->EOF) {
          $results[] = $stmt->fields;
          $stmt->MoveNext();
      }
  }
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
        array('id' => 'cookiecutterQS_qs_url','text' => 'QS URLS'),
        array('id' => '2','text' => 'HomePage Only'),

    );
    ?>
    <?php echo zen_draw_label("Select Table", 'zv_files_lookup', 'class="control-label col-sm-3"'); ?>
    <div class="col-sm-9 col-md-6">
        <?php echo zen_draw_pull_down_menu('database_table', $za_lookup, (isset($action) && $action == 'get_urls_data' ? (int)$_POST['database_table'] : '1'), 'id="database_table" class="form-control select2"'); ?>
    </div>
</div>

        <div class="form-group">
            <?php echo zen_draw_label('Load TextBoxes?', 'include_textboxes', 'class="control-label col-sm-3"'); ?>
          <div class="col-sm-9 col-md-6">
            <div class="checkbox">
     <label for="include_textboxes">
     <input type="checkbox" name="include_textboxes" id="include_textboxes"  <?php echo (isset($_POST['include_textboxes']) ? 'checked' : ''); ?> >
 </label>
            </div>
          </div>
        </div>

        <div class="form-group">
            <?php echo zen_draw_label('Load Templates?', 'include_templates', 'class="control-label col-sm-3"'); ?>
          <div class="col-sm-9 col-md-6">
            <div class="checkbox">
              <label for="include_templates">
              <input type="checkbox" name="include_templates" id="include_templates" value="1" <?php echo (isset($_POST['include_templates']) ? 'checked' : ''); ?> >

            </label>
            </div>
          </div>
        </div>


        <div class="form-group">
            <?php echo zen_draw_label('Load Meta Info?', 'include_meta', 'class="control-label col-sm-3"'); ?>
          <div class="col-sm-9 col-md-6">
            <div class="checkbox">
              <label for="include_meta">
              <input type="checkbox" name="include_meta" id="include_meta" value="1" <?php echo (isset($_POST['include_meta']) ? 'checked' : ''); ?> >

            </label>
            </div>
          </div>
        </div>


        <div class="form-group">
            <?php echo zen_draw_label('Load SiteMap?', 'include_sitemap', 'class="control-label col-sm-3"'); ?>
          <div class="col-sm-9 col-md-6">
            <div class="checkbox">
              <label for="include_sitemap">
              <input type="checkbox" name="include_sitemap" id="include_sitemap" value="1" <?php echo (isset($_POST['include_sitemap']) ? 'checked' : ''); ?> >

 
            </label>
            </div>
          </div>
        </div>

        <div class="form-group">
            <?php echo zen_draw_label('Load Index?', 'include_index', 'class="control-label col-sm-3"'); ?>
          <div class="col-sm-9 col-md-6">
            <div class="checkbox">
              <label for="include_index">
              <?php echo zen_draw_checkbox_field('include_index', '', (isset($_POST['include_index']) ? true : false), 'id="include_index" aria-label="include_index"'); ?>

              </label>
            </div>
          </div>
        </div>
      
        <div class="form-group">
          <div class="col-sm-12 text-right">
            <button type="submit" class="btn btn-primary"><?php echo TEXT_BUTTON_SEARCH; ?></button>
            <input type="button" class="btn btn-primary" value="<?php echo TEXT_BUTTON_REGEX_SEARCH; ?>" onClick="document.get_urls_data.action = '<?php echo zen_href_link(FILENAME_META_NEW, 'action=get_urls_data&m=r') ?>'; document.get_urls_data.submit();" title="<?php echo TEXT_BUTTON_REGEX_SEARCH_ALT; ?>">
            <button class="btn btn-primary" title="<?php echo TEXT_RESET_BUTTON_ALT; ?>" onClick="document.get_urls_data.action = '<?php echo zen_href_link(FILENAME_META_NEW); ?>'; document.get_urls_data.submit();"><?php echo SEARCH_CFG_KEYS_FORM_BUTTON_RESET; ?></button>
          </div>
        </div>
        <?php echo '</form>'; ?>
        <!-- eof: Locate all files -->
     
      <div class="row"><?php echo zen_draw_separator(); ?></div>
      <div id="update_template_css_modal" style="display:none;">
  <div id="froala_editor"></div>
</div>
      <?php if (!empty($results)): ?>

<h3>Search Results:</h3>
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
                    'template', 'template1', 'template2', 'template3', 'template4', 'template5'
                );
                
                // Meta fields
                $meta_fields = array('meta_title', 'meta_description');

                if ($column_name === 'cookiecutterqs_temp' && isset($_POST['include_templates'])) {
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

                } elseif (in_array($column_name, $template_fields) && isset($_POST['include_templates'])) {
                    // Show value
                    echo nl2br(htmlspecialchars($cell));

                    if (trim($cell) != '') {
                        // Edit button for normal templates
                        echo ' <button class="btn btn-xs btn-outline-primary" style="padding:2px 5px; font-size:10px;" title="Edit" 
                            onclick="openTemplateModal(\'edit\', \'' . htmlspecialchars($column_name) . '\', \'' . htmlspecialchars($row['domain']) . '\', \'' . htmlspecialchars($cell) . '\', \'' . $row['id'] . '\')">✏️</button>';
                            echo ' <button class="btn btn-xs btn-outline-primary" style="padding:2px 5px; font-size:10px;" title="Edit" 
                            onclick="edit_each_temp(\'' . htmlspecialchars($column_name) . '\', \'' . htmlspecialchars($row['domain']) . '\', \'' . htmlspecialchars($cell) . '\', \'' . $row['id'] . '\')">✏️</button>';

                        // Delete button
                        echo ' <button class="btn btn-xs btn-outline-danger" style="padding:2px 5px; font-size:10px;" title="Delete" 
                            onclick="deleteTemplateField(\'' . htmlspecialchars($column_name) . '\', \'' . htmlspecialchars($row['domain']) . '\', \'' . $row['id'] . '\')">🗑️</button>';
                    } else {
                        // Add button
                        echo ' <button class="btn btn-xs btn-outline-success" style="padding:2px 5px; font-size:10px;" title="Add" 
                            onclick="openTemplateModal(\'add\', \'' . htmlspecialchars($column_name) . '\', \'' . htmlspecialchars($row['domain']) . '\', \'\', \'' . $row['id'] . '\')">➕</button>';
                    }

                } elseif (in_array($column_name, $meta_fields) && isset($_POST['include_meta'])) {
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

<?php else: ?>

<?php if (isset($_POST['action']) && $_POST['action'] == 'get_urls_data'): ?>
    <div class="alert alert-info">No results found.</div>
<?php endif; ?>

<?php endif; ?>


<textarea id="template_preview" name="content"></textarea>

      <!-- body_text_eof //-->
    </div>


<!-- templates dropdown from custom_templates_admin //-->
<div class="form-group">
    <?php 
    // Fetch all template names from custom_templates_admin
    $template_sql = "SELECT id, template_name FROM custom_templates_admin ORDER BY template_name ASC";
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
$template_sql = "SELECT id, template_name FROM custom_templates_admin ORDER BY template_name ASC";
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
      <div class="modal-body">
        <div class="form-group">
          <?php echo zen_draw_pull_down_menu('modal_template_list', $za_template_names, '', 'id="modal_template_list" class="form-control select2"'); ?>
        </div>
        <button class="btn btn-sm btn-secondary mb-2" onclick="viewSelectedTemplate()">View Template</button>

        <div id="template_view_section" style="display:none; border:1px solid #ccc; padding:10px;">
          <h6>HTML</h6>
          <pre id="template_html" style="background:#f9f9f9;"></pre>

          <h6>CSS</h6>
          <pre id="template_css" style="background:#f9f9f9;"></pre>

          <h6>JavaScript</h6>
          <pre id="template_js" style="background:#f9f9f9;"></pre>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick="saveTemplateSelection()">Save</button>
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
<!-- Modal -->

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

function viewSelectedTemplate() {
  var selectedTemplate = $('#modal_template_list').val();
  if (!selectedTemplate) return;

  // Clear previous values
  $('#template_html').text('');
  $('#template_css').text('');
  $('#template_js').text('');
  $('#template_view_section').hide();

  $.ajax({
    url: 'meta_new.php',
    type: 'POST',
    data: {
      ajax_action: 'get_template_details',
      template_name: selectedTemplate
    },
    success: function(response) {
      var res = JSON.parse(response);
      if (res.status === 'success') {
        $('#template_view_section').show();
        $('#template_html').text(res.html || 'Not found');
        $('#template_css').text(res.css || 'Not found');
        $('#template_js').text(res.js || 'Not found');
      } else {
        alert(res.message);
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

function edit_each_temp(cell, domain, rowid) {
  const req_data = { pull_template: rowid };

  $.post('meta_new.php', req_data, function (res) {
    console.log("AJAX Response:", res);

    const encodeHtml = unescapeHtml(decodeURI(res.html.replace(/%(?![0-9][0-9a-fA-F]+)/g, '%25')));
    const convertedHtml = encodeHtml.replace(/<\?/gm, '{?').replace(/\?>/gm, '?}');

    $('#update_template_css_modal').show(); // ← Force modal open

    // Check if editor is initialized
    if (window.editor && window.editor.html) {
      window.editor.html.set(convertedHtml);
      window.editor.events.focus();
    } else {
      console.warn("Initializing Froala...");
      window.editor = new FroalaEditor('#froala_editor', {
        key: "Ne2C1sF4D3C3A14A7D9jF1QUg1Xc2OZE1ABVJRDRNGGUH1ITrA1C7A6F6E1E4H4E1A9C6==",
        heightMin: 300,
        events: {
          'initialized': function () {
            this.html.set(convertedHtml);
          }
        }
      });
    }

  }, 'json');
}


</script>

    <!-- script for templates list dropdown -->
  </body>
</html>

<!-- Froala Editor JS -->

<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>

<?php
/**
 * @copyright Copyright 2003-2024 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: proseLA 2023 Oct 24 Modified in v2.0.0-alpha1 $
 */
//Dropdown for css and js added successfull but not saving data yet in the database.
require('includes/application_top.php');

// Assuming this is FILENAME_META_NEW (your form posts here)
require('includes/application_top.php');

// all the php functions for listing, add, updating and deleting urls 
// table template_to_urls and cookiecutterQS_qs_url are used mainly
require('includes/urls_process.php');



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

/* Simple animated CSS spinner for modal overlay */
#addModalLoaderOverlay .spinner {
  border: 4px solid rgba(0,0,0,0.1);
  border-top-color: #007bff;
  border-radius: 50%;
  width: 60px;
  height: 60px;
  animation: spin 1s linear infinite;
  margin: 0 auto;
}

#qsModalLoaderOverlay .spinner {
  border: 4px solid rgba(0,0,0,0.1);
  border-top-color: #007bff;
  border-radius: 50%;
  width: 60px;
  height: 60px;
  animation: spin 1s linear infinite;
  margin: 0 auto;
}
@keyframes spin {
  to { transform: rotate(360deg); }
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
     <div class="row">
      <div class="col-md-12 col-lg-12">
    <div class="text-right">
     <button class="btn btn-primary" data-toggle="modal" data-target="#addRecordModal">
  ➕ Add New URL
    </button>

    <button class="btn btn-info" data-toggle="modal" data-target="#addQsRecordModal">
  ➕ Add QS URL
</button>
</div>

      </div>
     </div>
      

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
                <?php echo zen_draw_pull_down_menu('url_domain_list', $za_lookup_domain, (isset($action) && $action == 'get_urls_data' ? (int)$_POST['url_domain_list'] : '1'), 'id="url_domain_list" class="form-control select-global"'); ?>
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
            'id="database_table" class="form-control select-global"'
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
                $meta_fields = array('meta_title', 'meta_description','qs_meta_title','qs_meta_description');

                if (($column_name === 'cookiecutterqs_temp' || $column_name === 'qs_cookiecutter_name') && isset($form_data['include_meta'])) {
                  // Show value
                  echo nl2br(htmlspecialchars($cell));
              
                  // Set the value to pass based on the column_name
                  $valueToPass = ($column_name === 'qs_cookiecutter_name') ? htmlspecialchars($row['cookiecutter_id']) : htmlspecialchars($cell);
              
                  // Set the column_name to cookiecutter_id if it is 'qs_cookiecutter_name'
                  $columnToPass = ($column_name === 'qs_cookiecutter_name') ? 'cookiecutter_id' : $column_name;
              
                  // Add or edit functionality based on whether the cell value is empty
                  if (trim($cell) != '') {
                    
                      // Edit Button - opens CookieQS Modal
                      echo ' <button class="btn btn-xs btn-outline-primary" style="padding:2px 5px; font-size:10px;" title="Edit" 
                          onclick="openCookieQSModal(\'edit\', \'' . htmlspecialchars($columnToPass) . '\', \'' . htmlspecialchars($row['domain']) . '\', \'' . $valueToPass . '\', \'' . $row['id'] . '\')">✏️</button>';
              
                      // Delete Button
                      echo ' <button class="btn btn-xs btn-outline-danger" style="padding:2px 5px; font-size:10px;" title="Delete" 
                          onclick="deleteTemplateField(\'' . htmlspecialchars($columnToPass) . '\', \'' . htmlspecialchars($row['domain']) . '\', \'' . $row['id'] . '\')">🗑️</button>';
                  } else {
                      // Add Button (when cell is empty)
                      echo ' <button class="btn btn-xs btn-outline-success" style="padding:2px 5px; font-size:10px;" title="Add" 
                          onclick="openCookieQSModal(\'add\', \'' . htmlspecialchars($columnToPass) . '\', \'' . htmlspecialchars($row['domain']) . '\', \'\', \'' . $row['id'] . '\')">➕</button>';
                  }
              }
              
                elseif (
                  (in_array($column_name, ['template', 'template1', 'template2', 'template3', 'template4', 'template5']) && isset($form_data['include_templates'])) ||
                  (in_array($column_name, ['textbox1', 'textbox2', 'textbox3', 'textbox4', 'textbox5','qsTextbox1','qsTextbox2','qsTextbox3','qsTextbox4','qsTextbox5']) && isset($form_data['include_textboxes']))
              ) {
                    // Show value
                    echo nl2br(htmlspecialchars($cell));

                    if (trim($cell) != '') {
                        // Edit button for normal templates and textboxes 1-5
                        echo ' <button class="btn btn-xs btn-outline-primary" style="padding:2px 5px; font-size:10px;" title="Change template" 
                            onclick="openTemplateModal(\'edit\', \'' . htmlspecialchars($column_name) . '\', \'' . htmlspecialchars($row['domain']) . '\', \'' . htmlspecialchars($cell) . '\', \'' . $row['id'] . '\')">✏️</button>';
                          
                            echo ' <button class="btn btn-xs btn-outline-primary" style="padding:2px 5px; font-size:10px;" title="Update Template" 
                            onclick="edit_each_temp(\'' . htmlspecialchars($column_name) . '\', \'' . htmlspecialchars($row['domain']) . '\', \'' . htmlspecialchars($cell) . '\', \'' . $row['id'] . '\')">👁️</button>';
                    
                            echo ' <button class="btn btn-xs btn-outline-primary" style="padding:2px 5px; font-size:10px;" title="Backup Template" 
                            onclick="backup_each_temp(\'' . htmlspecialchars($column_name) . '\', \'' . htmlspecialchars($row['domain']) . '\', \'' . htmlspecialchars($cell) . '\', \'' . $row['id'] . '\')"><i class="fas fa-download"></i></button>';
                        // Delete button
                        echo ' <button class="btn btn-xs btn-outline-danger" style="padding:2px 5px; font-size:10px;" title="Delete Template" 
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
          <!-- Hidden Fields -->
          <input type="hidden" id="cookieqs_columnName" name="columnName">
          <input type="hidden" id="cookieqs_domain" name="domain">
          <input type="hidden" id="cookieqs_rowId" name="rowId">
          <input type="hidden" id="cookieqs_actionType" name="actionType">
          <input type="hidden" id="cookiecutter_id" name="cookiecutter_id">

          <!-- Select Dropdown -->
          <div class="form-group">
            <label for="cookieqs_dropdown">Select Template:</label>
            <select id="cookieqs_dropdown" name="newValue" class="form-control select2" style="width: 100%">
              <?php
              $qs_sql = "SELECT id, name FROM cookie_cutter_qs ORDER BY name ASC";
              $qs_results = $db->Execute($qs_sql);
              while (!$qs_results->EOF) {
                  echo '<option value="' . htmlspecialchars($qs_results->fields['name']) . '" data-id="' . htmlspecialchars($qs_results->fields['id']) . '">' . htmlspecialchars($qs_results->fields['name']) . '</option>';
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
        <!-- HTML Template Dropdown -->
        
        <div class="form-row">
  <!-- Template Dropdown -->
  <div class="form-group col-md-4">
    <?php echo zen_draw_label('Template', 'modal_template_list', 'class="control-label"'); ?>
    <?php echo zen_draw_pull_down_menu('modal_template_list', $za_template_names, '', 'id="modal_template_list" class="form-control select2"'); ?>
  </div>

  <!-- CSS File Dropdown -->
  <div class="form-group col-md-4">
    <label for="modal_css_list" class="control-label">CSS File</label>
    <select id="modal_css_list" class="form-control select2">
      <option value="">Loading CSS files...</option>
    </select>
  </div>

  <!-- JS File Dropdown -->
  <div class="form-group col-md-4">
    <label for="modal_js_list" class="control-label">JS File</label>
    <select id="modal_js_list" class="form-control select2">
      <option value="">Loading JS files...</option>
    </select>
  </div>
</div>


        <!-- Action Buttons -->
        <div class="mb-2">
          <button class="btn btn-sm btn-primary" onclick="viewSelectedTemplate()">View Template</button>
          <button class="btn btn-sm btn-info" onclick="viewSelectedCSS()">View CSS</button>
          <button class="btn btn-sm btn-warning" onclick="viewSelectedJS()">View JS</button>
        </div>

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
          <div id="css_editor_container" style="min-height: 300px; font-family: monospace; background-color: #1e1e1e; color: #d4d4d4; border: 1px solid #444; padding: 10px; display: flex;">
            <pre id="css_editor_lines" style="margin: 0; padding-right: 10px; text-align: right; color: #888; user-select: none;"></pre>
            <textarea id="css_editor_raw" class="form-control" style="flex: 1; min-height: 300px; font-family: monospace; background-color: #1e1e1e; color: #d4d4d4; border: none; resize: none; overflow: auto;"></textarea>
          </div>
        </div>

        <div id="js_edit_section" style="display:none;">
          <div id="js_editor_container" style="min-height: 300px; font-family: monospace; background-color: #1e1e1e; color: #d4d4d4; border: 1px solid #444; padding: 10px; display: flex;">
            <pre id="js_editor_lines" style="margin: 0; padding-right: 10px; text-align: right; color: #888; user-select: none;"></pre>
            <textarea id="js_editor_raw" class="form-control" style="flex: 1; min-height: 300px; font-family: monospace; background-color: #1e1e1e; color: #d4d4d4; border: none; resize: none; overflow: auto;"></textarea>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="saveUpdatedTemplateFile()">Save Changes</button>
      </div>
    </div>
  </div>
</div>



<!-- //adding new data modal -->
<div class="modal fade" id="addRecordModal" tabindex="-1" role="dialog" aria-labelledby="addRecordModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content position-relative">
      
      <!-- Loader Overlay -->
      <div id="addModalLoaderOverlay" style="
        display: none;
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background-color: rgba(255, 255, 255, 0.85);
        z-index: 1051;
        text-align: center;
        padding-top: 150px;
      ">
        <div class="spinner"></div>
        <div style="margin-top: 10px; font-weight: bold;">Loading... Please wait</div>
      </div>

      <!-- Modal Header -->
      <div class="modal-header">
        <h5 class="modal-title">Add New URL Record</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <!-- Modal Body -->
      <div class="modal-body">
        <form id="addRecordForm">
          <div class="row">
            <div class="col-md-4">
              <label>Domain</label>
              <select id="add_domain" name="domain" class="form-control select2-add-record" required></select>
            </div>
            <div class="col-md-4">
              <label>URL</label>
              <select id="add_url" name="url[]" class="form-control select2-add-record" multiple required></select>
              <small class="form-text text-muted">You can select multiple URLs to add them all with these settings.</small>
            </div>
            <div class="col-md-4">
              <label>Cookiecutterqs Temp</label>
              <select id="cookiecutterqs_temp" name="cookiecutterqs_temp" class="form-control select2-add-record"></select>
            </div>
            <div class="col-md-6 mt-2">
              <label>Meta Title</label>
              <input type="text" name="meta_title" class="form-control" required />
            </div>
            <div class="col-md-6 mt-2">
              <label>Meta Description</label>
              <textarea name="meta_description" class="form-control" rows="2"></textarea>
            </div>
           
          </div>

          <div class="row mt-3">
            <div class="col-md-6">
          
              <div class="row">
                <div class="col-md-6 mb-1">
                <div class="form-group">
                <label>Template 1</label>
                  <select id="template1" name="template1" class="form-control select2-add-record"></select>
                </div>
                </div>
                <div class="col-md-6 mb-1">
                <div class="form-group">
                <label>Template 2</label>
                  <select id="template2" name="template2" class="form-control select2-add-record"></select>
                </div>
                </div>
                <div class="col-md-6 mb-1">
                <div class="form-group">
                <label>Template 3</label>
                  <select id="template3" name="template3" class="form-control select2-add-record"></select>
                </div>
                </div>
                <div class="col-md-6 mb-1">
                <div class="form-group">
                <label>Template 4</label>
                  <select id="template4" name="template4" class="form-control select2-add-record"></select>
                </div>
                </div>
                <div class="col-md-6 mb-1">
                <div class="form-group">
                <label>Template 5</label>
                  <select id="template5" name="template5" class="form-control select2-add-record"></select>
                </div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              
              <div class="row">
                <div class="col-md-6 mb-1">
                <div class="form-group">
                <label> TextBox 1</label>
                  <select id="textbox1" name="textbox1" class="form-control select2-add-record"></select>
                </div>
                </div>
                <div class="col-md-6 mb-1">
                <div class="form-group">
                <label> TextBox 2</label>
                  <select id="textbox2" name="textbox2" class="form-control select2-add-record"></select>
                </div>
                </div>
                <div class="col-md-6 mb-1">
                <div class="form-group">
                <label> TextBox 3</label>
                  <select id="textbox3" name="textbox3" class="form-control select2-add-record"></select>
                </div>
                </div>
                <div class="col-md-6 mb-1">
                <div class="form-group">
                <label> TextBox 4</label>
                  <select id="textbox4" name="textbox4" class="form-control select2-add-record"></select>
                </div>
                </div>
                <div class="col-md-6 mb-1">
                <div class="form-group">
                <label> TextBox 5</label>
                  <select id="textbox5" name="textbox5" class="form-control select2-add-record"></select>
                </div>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>

      <!-- Modal Footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-success" onclick="submitAddRecordForm()">Add Record</button>
      </div>
    </div>
  </div>
</div>


<!-- //adding QS data modal -->
<div class="modal fade" id="addQsRecordModal" tabindex="-1" role="dialog" aria-labelledby="addQsRecordModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content position-relative">
      
      <!-- Loader Overlay for QS Modal -->
      <div id="qsModalLoaderOverlay" style="
        display: none;
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background-color: rgba(255, 255, 255, 0.85);
        z-index: 1051;
        text-align: center;
        padding-top: 150px;
      ">
        <div class="spinner"></div>
        <div style="margin-top: 10px; font-weight: bold;">Loading... Please wait</div>
      </div>
      
      <!-- Modal Header -->
      <div class="modal-header">
        <h5 class="modal-title">Add New QS URL Record</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <!-- Modal Body -->
      <div class="modal-body">
        <form id="addQsRecordForm">
          <input type="hidden" name="add_mode" value="qs" />
          <div class="form-group">
            <label>Domain</label>
            <select id="add_qs_domain" name="domain" class="form-control" required></select>
          </div>

          <div class="form-group">
            <label>QS URL</label>
            <select id="qs_url" name="qs_url[]" class="form-control select2-add-record" multiple required></select>
              <small class="form-text text-muted">You can select multiple URLs to add them all with these settings.</small>  
          </div>

          <div class="row">
            <div class="col-md-12">
             
              <div class="row">
              <div class="col-md-6 mb-1">
              <div class="form-group">
              <label>QS TextBox 1</label>
                  <select id="qsTextbox1" name="qsTextbox1" class="form-control select2-add-record"></select>
               </div> 
                </div>
                <div class="col-md-6 mb-1">
                <div class="form-group">
                <label>QS TextBox 2</label>
                  <select id="qsTextbox2" name="qsTextbox2" class="form-control select2-add-record"></select>
                </div>
                </div>
                <div class="col-md-6 mb-1">
                <div class="form-group">
                <label>QS TextBox 3</label>
                  <select id="qsTextbox3" name="qsTextbox3" class="form-control select2-add-record"></select>
                </div>
                </div>
                <div class="col-md-6 mb-1">
                <div class="form-group">
                <label>QS TextBox 4</label>
                  <select id="qsTextbox4" name="qsTextbox4" class="form-control select2-add-record"></select>
                </div>
                </div>
                <div class="col-md-6 mb-1">
                <div class="form-group">
                <label>QS TextBox 5</label>
                  <select id="qsTextbox5" name="qsTextbox5" class="form-control select2-add-record"></select>
                </div>
                </div>

                <div class="col-md-6 mb-1">
                <div class="form-group">
                  <label>Cookiecutterqs Temp</label>
                  <select id="qs_cookiecutterqs_temp" name="cookiecutterqs_temp" class="form-control select2-add-record"></select>
                </div>
              </div>


                <div class="col-md-6 mt-2">
              <label>Meta Title</label>
              <input type="text" name="qs_meta_title" class="form-control" required />
            </div>
            <div class="col-md-6 mt-2">
              <label>Meta Description</label>
              <textarea name="qs_meta_description" class="form-control" rows="2"></textarea>
            </div>
              </div>
            </div>
          </div>
        </form>
      </div>

      <!-- Modal Footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-success" onclick="submitAddQsRecordForm()">Add QS Record</button>
      </div>
    </div>
  </div>
</div>



    <!-- body_eof //-->

    <!-- footer //-->
    <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
  
    <!-- footer_eof //-->
    <!-- script for templates list adding deleting and updating contain all javascript fuctions needed for this page -->
<script src="includes/urls_process.js"></script>
    <!-- script for templates list dropdown -->
  </body>
</html>

<!-- Froala Editor JS -->

<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>

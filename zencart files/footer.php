<?php



if (isset($_POST['ajax_action']) && $_POST['ajax_action'] === 'insert_new_record') {
  $domain             = zen_db_input($_POST['domain']);
  $url                = zen_db_input($_POST['url']);
  $meta_title         = zen_db_input($_POST['meta_title']);
  $meta_description   = zen_db_input($_POST['meta_description']);
  $cookiecutterqs_temp= zen_db_input($_POST['cookiecutterqs_temp']);

  $insert_template    = isset($_POST['insert_template_table']) ? true : false;
  $insert_qs          = isset($_POST['insert_qs_table']) ? true : false;

  // Template and Textbox Fields
  $template_fields = [];
  $textbox_fields = [];

  for ($i = 1; $i <= 5; $i++) {
    $template_fields["template$i"] = isset($_POST["template$i"]) ? zen_db_input($_POST["template$i"]) : '';
    $textbox_fields["textbox$i"] = isset($_POST["textbox$i"]) ? zen_db_input($_POST["textbox$i"]) : '';
}

  if ($insert_template) {
      $sql1 = "INSERT INTO template_to_urls (
          domain, url, meta_title, meta_description, cookiecutterqs_temp,
          template1, template2, template3, template4, template5,
          textbox1, textbox2, textbox3, textbox4, textbox5
      ) VALUES (
          '$domain', '$url', '$meta_title', '$meta_description', '$cookiecutterqs_temp',
          '{$template_fields['template1']}', '{$template_fields['template2']}', '{$template_fields['template3']}',
          '{$template_fields['template4']}', '{$template_fields['template5']}',
          '{$textbox_fields['textbox1']}', '{$textbox_fields['textbox2']}', '{$textbox_fields['textbox3']}',
          '{$textbox_fields['textbox4']}', '{$textbox_fields['textbox5']}'
      )";
      $db->Execute($sql1);
  }
  echo json_encode(['status' => $sql1]); exit;
  if ($insert_qs) {
      $sql2 = "INSERT INTO cookiecutterQS_qs_url (
          domain, qs_url, qsTextbox1, qsTextbox2, qsTextbox3, qsTextbox4, qsTextbox5
      ) VALUES (
          '$domain', '$url',
          '{$textbox_fields['textbox1']}', '{$textbox_fields['textbox2']}', '{$textbox_fields['textbox3']}',
          '{$textbox_fields['textbox4']}', '{$textbox_fields['textbox5']}'
      )";
      $db->Execute($sql2);
  }

  echo json_encode(['status' => 'success']);
  exit;
}


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


?>
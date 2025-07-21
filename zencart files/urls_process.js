$('#templateEditModal').on('shown.bs.modal', function () {
  const selects = ['#modal_template_list', '#modal_css_list', '#modal_js_list'];

  selects.forEach(function (selector) {
    const $select = $(selector);

    if ($select.hasClass('select2-hidden-accessible')) {
      $select.select2('destroy');
    }

    $select.select2({
      dropdownParent: $('#templateEditModal'),
      width: '100%',
      theme: 'bootstrap',
      placeholder: 'Please select',
      allowClear: true
    }).addClass('form-control');
  });
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
                  newHtml += ' <button class="btn btn-xs btn-outline-primary" style="padding:2px 5px; font-size:10px;" title="Change Template" ' +
                    'onclick="openTemplateModal(\'edit\', \'' + currentTemplateField + '\', \'' + currentDomain + '\', \'' + escapeQuotes(selectedTemplate) + '\', \'' + currentRowId + '\')">✏️</button>';
                
                    newHtml += ' <button class="btn btn-xs btn-outline-primary" style="padding:2px 5px; font-size:10px;" title="Update Template" ' +
                    'onclick="edit_each_temp( \'' + currentTemplateField + '\', \'' + currentDomain + '\', \'' + escapeQuotes(selectedTemplate) + '\')">👁️</button>';
                // BackUp button
                    newHtml += ' <button class="btn btn-xs btn-outline-primary" style="padding:2px 5px; font-size:10px;" title="Backup Template" ' +
                    'onclick="backup_each_temp( \'' + currentTemplateField + '\', \'' + currentDomain + '\', \'' + escapeQuotes(selectedTemplate) + '\')"><i class="fas fa-download"></i></button>';
                // Delete button
                newHtml += ' <button class="btn btn-xs btn-outline-danger" style="padding:2px 5px; font-size:10px;" title="Delete Template" ' +
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
            if(columnName =='cookiecutterqs_temp' || columnName =='cookiecutter_id')
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



// 1️⃣ SELECT2 LIFECYCLE HANDLING
$('#cookieqsModal').on('show.bs.modal', function () {
  if ($('#cookieqs_dropdown').hasClass('select2-hidden-accessible')) {
    $('#cookieqs_dropdown').select2('destroy');
  }
  $('#cookieqs_dropdown').select2({
    dropdownParent: $('#cookieqsModal'),
    width: '100%',
    theme: 'bootstrap',
    placeholder: 'Please select',
    allowClear: true
  });
});

$('#cookieqsModal').on('hidden.bs.modal', function () {
  if ($('#cookieqs_dropdown').hasClass('select2-hidden-accessible')) {
    $('#cookieqs_dropdown').select2('destroy');
  }
});

function openCookieQSModal(action, columnName, domain, currentValue, rowId) {
  $('#cookieqsModalLabel').text((action === 'edit' ? 'Edit ' : 'Add ') + columnName);
  $('#cookieqs_columnName').val(columnName);
  $('#cookieqs_domain').val(domain);
  $('#cookieqs_rowId').val(rowId);
  $('#cookieqs_actionType').val(action);

  if (columnName === 'cookiecutter_id') {
    let found = false;
    $('#cookieqs_dropdown option').each(function() {
      if ($(this).data('id') == currentValue) {
        $('#cookieqs_dropdown').val($(this).val());
        found = true;
        return false;
      }
    });
    if (!found) {
      $('#cookieqs_dropdown').val(null);
    }
  } else if (columnName === 'cookiecutterqs_temp') {
    $('#cookieqs_dropdown').val(currentValue);
  } else {
    $('#cookieqs_dropdown').val(null);
  }

  $('#cookieqs_dropdown').trigger('change');
  $('#cookieqsModal').modal('show');
}

$('#cookieqs_dropdown').on('change', function () {
  const selectedOption = $(this).find('option:selected');
  const dataId = selectedOption.data('id') || '';
  $('#cookiecutter_id').val(dataId);
});


function saveCookieQSValue() {
  const columnName = $('#cookieqs_columnName').val();
  const rowId = $('#cookieqs_rowId').val();

  let newValue = '';
  if (columnName === 'cookiecutter_id') {
    newValue = $('#cookiecutter_id').val();
  } else if (columnName === 'cookiecutterqs_temp') {
    newValue = $('#cookieqs_dropdown').val();
  } else {
    alert('Unknown column type: ' + columnName);
    return false;
  }

  const data = {
    ajax_action: 'save_template_field',
    columnName: columnName,
    domain: $('#cookieqs_domain').val(),
    rowId: rowId,
    actionType: $('#cookieqs_actionType').val(),
    newValue: newValue
  };

  $.post('meta_new.php', data, function(response) {
    let res;
    try {
      res = JSON.parse(response);
    } catch (e) {
      alert('Server error: invalid JSON');
      return;
    }

    if (res.status === 'success') {
      $('#cookieqsModal').modal('hide');
      alert('QSTemplate field Added!');

      const displayText = res.displayText;
      const cellSelector = `#cell_${rowId}_${columnName}`;
      $(cellSelector).html(`
        ${displayText}
        <button class="btn btn-xs btn-outline-primary" title="Edit" onclick="openCookieQSModal('edit', '${data.columnName}', '${data.domain}', '${newValue}', '${data.rowId}')">✏️</button>
        <button class="btn btn-xs btn-outline-danger" title="Delete" onclick="deleteTemplateField('${data.columnName}', '${data.domain}', '${data.rowId}')">🗑️</button>
      `);
    } else {
      alert(res.message);
    }
  });

  return false;
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
              this.html.set(decodeEntities(res.html || 'Not found'));
            }
          }
        });

      } else {
        alert(res.message);
      }
    }
  });
}

// Helper function to decode HTML entities
function decodeEntities(encodedString) {
  const textarea = document.createElement('textarea');
  textarea.innerHTML = encodedString;
  return textarea.value;
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
    $('.select-global').select2({
      placeholder: 'Please select'
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

const tableSearchInput = document.getElementById("tableSearch");

if (tableSearchInput) {
  tableSearchInput.addEventListener("keyup", function () {
    const input = this.value.toLowerCase().trim();
    const rows = document.querySelectorAll("table tbody tr");
    let visibleCount = 0;

    rows.forEach(row => {
      const cells = row.querySelectorAll("td");
      let rowMatch = false;

      cells.forEach(cell => {
        const originalText = cell.textContent;
        cell.innerHTML = originalText;

        if (input && originalText.toLowerCase().includes(input)) {
          rowMatch = true;
          cell.innerHTML = highlightMatch(originalText, input);
        }
      });

      if (!input) {
        row.style.display = "";
        visibleCount++;
      } else {
        row.style.display = rowMatch ? "" : "none";
        if (rowMatch) visibleCount++;
      }
    });

    const noResultsMsg = document.getElementById("noResultsMsg");
    if (noResultsMsg) {
      noResultsMsg.style.display = (visibleCount === 0 && input) ? "block" : "none";
    }
  });
}




function showTemplateHTML() {
  $('#template_edit_section').show();
  $('#css_edit_section, #js_edit_section').hide();
}

function showTemplateCSS() {
  $('#css_edit_section').show();
  $('#template_edit_section, #js_edit_section').hide();
  updateCSSLineNumbers();
}

function showTemplateJS() {
  $('#js_edit_section').show();
  $('#template_edit_section, #css_edit_section').hide();
  updateJSLineNumbers();
}

function updateCSSLineNumbers() {
  const cssEditor = document.getElementById('css_editor_raw');
  const linesContainer = document.getElementById('css_editor_lines');
  const lineCount = cssEditor.value.split('\n').length;
  linesContainer.innerHTML = Array.from({length: lineCount}, (_, i) => i + 1).join('\n');
}

function updateJSLineNumbers() {
  const jsEditor = document.getElementById('js_editor_raw');
  const linesContainer = document.getElementById('js_editor_lines');
  const lineCount = jsEditor.value.split('\n').length;
  linesContainer.innerHTML = Array.from({length: lineCount}, (_, i) => i + 1).join('\n');
}

document.getElementById('css_editor_raw').addEventListener('input', updateCSSLineNumbers);
document.getElementById('css_editor_raw').addEventListener('scroll', function () {
  document.getElementById('css_editor_lines').scrollTop = this.scrollTop;
});

document.getElementById('js_editor_raw').addEventListener('input', updateJSLineNumbers);
document.getElementById('js_editor_raw').addEventListener('scroll', function () {
  document.getElementById('js_editor_lines').scrollTop = this.scrollTop;
});


function edit_each_temp(cell, domain, rowid) {
  currentRowId = rowid;
  const req_data = { pull_template: rowid };

  $.post('meta_new.php', req_data, function (res) {
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

    if (window.editor) window.editor.destroy();
    $('#froala_editor').html('');

    $('#css_editor_raw').val(res.css || '/* No CSS found */');
    $('#js_editor_raw').val(res.js || '// No JS found');

    updateCSSLineNumbers();
    updateJSLineNumbers();

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
    });
  }, 'json');
}

function saveUpdatedTemplateFile() {
  const htmlContent = window.editor ? window.editor.html.get() : '';
  const cssContent = document.getElementById('css_editor_raw').value;
  const jsContent = document.getElementById('js_editor_raw').value;

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
          $('#update_template_css_modal').modal('hide');
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

function backup_each_temp(cell, domain, rowid) {
  console.log(cell,domain,rowid);
  if (!confirm("Are you sure you want to backup this template?")) return;

  $.ajax({
    url: 'meta_new.php',
    method: 'POST',
    data: {
      ajax_action: 'backup_template',
      domain: domain,
      template_name: rowid,
      fieldname: cell
    },

    success: function (res) {
      try {
        const json = typeof res === 'string' ? JSON.parse(res) : res;
        if (json.status === 'success') {
          alert('✅ ' + json.message);
        } else {
          alert('❌ ' + json.message);
        }
      } catch (err) {
        console.error("Unexpected response:", res);
        alert('Error during backup.');
      }
    }
  });
}

$('#templateEditModal').on('shown.bs.modal', function () {
  // Only load once
  if ($('#modal_css_list option').length <= 1) {
    $.post('meta_new.php', { ajax_action: 'get_css_js_files' }, function (res) {
      if (res.status === 'success') {
        // Populate CSS dropdown
        const cssSelect = $('#modal_css_list');
        cssSelect.html('<option value="">Select CSS file</option>');
        res.css_files.forEach(file => {
          cssSelect.append(`<option value="${file}">${file}</option>`);
        });

        // Populate JS dropdown
        const jsSelect = $('#modal_js_list');
        jsSelect.html('<option value="">Select JS file</option>');
        res.js_files.forEach(file => {
          jsSelect.append(`<option value="${file}">${file}</option>`);
        });
      } else {
        alert('Failed to load files');
      }
    }, 'json');
  }
});

$(document).ready(function(){
  $('[data-toggle="tooltip"]').tooltip(); 

});

$(function () {
  $('button[title]').tooltip();
});

///
let cachedAddRecordDropdownData = null;
let cachedQsRecordDropdownData = null;

let addUrlFilteredIds = [];
let qsUrlFilteredIds = [];

let addUrlCurrentSearchTerm = '';
let qsUrlCurrentSearchTerm = '';

// Show / Hide Loading Overlay
function showAddModalLoadingOverlay() { $('#addModalLoaderOverlay').show(); }
function hideAddModalLoadingOverlay() { $('#addModalLoaderOverlay').hide(); }
function showQsModalLoadingOverlay() { $('#qsModalLoaderOverlay').show(); }
function hideQsModalLoadingOverlay() { $('#qsModalLoaderOverlay').hide(); }

// Utility to fill select options
function fillSelect(select, items) {
  select.empty().append('<option value="">Select</option>');
  if (items && items.length) {
    items.forEach(function (item) {
      select.append('<option value="' + item.value + '">' + item.text + '</option>');
    });
  } else {
    select.append('<option value="">No options found</option>');
  }
}

// Checkbox-style rendering for Select2
function select2WithCheckboxTemplate(option) {
  if (!option.id) return option.text;
  const isSelected = $(option.element).prop('selected');
  return $(`
    <span>
      <input type="checkbox" ${isSelected ? 'checked' : ''} style="margin-right: 6px;" /> ${option.text}
    </span>
  `);
}

// ------------------- Add URL Modal --------------------
$('#addRecordModal').on('shown.bs.modal', function () {
  showAddModalLoadingOverlay();
  if (cachedAddRecordDropdownData) {
    setTimeout(() => {
      populateAddRecordModalDropdowns(cachedAddRecordDropdownData);
      hideAddModalLoadingOverlay();
    }, 200);
  } else {
    loadAddRecordModalDropdownData();
  }
});

$('#addRecordModal').on('hidden.bs.modal', function () {
  addUrlCurrentSearchTerm = '';
});

function loadAddRecordModalDropdownData() {
  $.ajax({
    url: 'meta_new.php',
    type: 'POST',
    data: { ajax_action: 'load_dropdown_data' },
    dataType: 'json',
    success: function (data) {
      cachedAddRecordDropdownData = data;
      populateAddRecordModalDropdowns(data);
      hideAddModalLoadingOverlay();
    },
    error: function () {
      alert('Error loading dropdown data. Please try again.');
      $('#addRecordModal select').empty().append('<option>Error loading</option>');
      hideAddModalLoadingOverlay();
    }
  });
}

function populateAddRecordModalDropdowns(data) {
  fillSelect($('#add_domain'), data.domains);
  fillSelect($('#add_url'), data.urls);
  fillSelect($('#cookiecutterqs_temp'), data.cookiecutterqs);

  for (let i = 1; i <= 5; i++) {
    fillSelect($('#template' + i), data.templates);
    fillSelect($('#textbox' + i), data.textboxes);
  }

  $('#addRecordModal .select2-add-record').each(function () {
    const $select = $(this);
    const isUrlMulti = $select.attr('id') === 'add_url';

    $select.select2({
      dropdownParent: $('#addRecordModal'),
      width: '100%',
      placeholder: 'Select an option',
      allowClear: true,
      templateResult: isUrlMulti ? select2WithCheckboxTemplate : undefined
    });

    if (isUrlMulti) {
      $select.on('select2:open', function () {
        setTimeout(() => {
          addUrlFilteredIds = getCurrentlyVisibleOptions();
        }, 50);

        let searchInput = $('.select2-container--open .select2-search__field');
        searchInput.off('input').on('input', function () {
          addUrlCurrentSearchTerm = $(this).val().toLowerCase();
        });
      });
    }
  });
}

// ------------------- QS URL Modal --------------------
$('#addQsRecordModal').on('shown.bs.modal', function () {
  showQsModalLoadingOverlay();
  if (cachedQsRecordDropdownData) {
    setTimeout(() => {
      populateQsRecordModalDropdowns(cachedQsRecordDropdownData);
      hideQsModalLoadingOverlay();
    }, 200);
  } else {
    loadQsRecordModalDropdownData();
  }
});

$('#addQsRecordModal').on('hidden.bs.modal', function () {
  qsUrlCurrentSearchTerm = '';
});

function loadQsRecordModalDropdownData() {
  $.ajax({
    url: 'meta_new.php',
    type: 'POST',
    data: { ajax_action: 'load_dropdown_qs_data' },
    dataType: 'json',
    success: function (data) {
      cachedQsRecordDropdownData = data;
      populateQsRecordModalDropdowns(data);
      hideQsModalLoadingOverlay();
    },
    error: function () {
      alert('Error loading dropdown data. Please try again.');
      $('#addQsRecordModal select').empty().append('<option>Error loading</option>');
      hideQsModalLoadingOverlay();
    }
  });
}

function populateQsRecordModalDropdowns(data) {
  fillSelect($('#add_qs_domain'), data.domains);
  fillSelect($('#qs_url'), data.urls);
  fillSelect($('#qs_cookiecutterqs_temp'), data.cookiecutterqs);

  for (let i = 1; i <= 5; i++) {
    fillSelect($('#qsTextbox' + i), data.textboxes);
  }

  $('#addQsRecordModal .select2-add-record').each(function () {
    const $select = $(this);
    const isUrlMulti = $select.attr('id') === 'qs_url';

    $select.select2({
      dropdownParent: $('#addQsRecordModal'),
      width: '100%',
      placeholder: 'Select an option',
      allowClear: true,
      templateResult: isUrlMulti ? select2WithCheckboxTemplate : undefined
    });

    if (isUrlMulti) {
      $select.on('select2:open', function () {
        setTimeout(() => {
          qsUrlFilteredIds = getCurrentlyVisibleOptions();
        }, 50);

        let searchInput = $('.select2-container--open .select2-search__field');
        searchInput.off('input').on('input', function () {
          qsUrlCurrentSearchTerm = $(this).val().toLowerCase();
        });
      });
    }
  });
}

// ------------------- External Buttons --------------------
// Utility: get filtered options based on current search
function getFilteredOptionValues($select, searchTerm) {
  if (!searchTerm) {
    return $select.find('option').map(function() {
      return this.value;
    }).get().filter(v => v);
  }

  return $select.find('option').filter(function() {
    return $(this).text().toLowerCase().includes(searchTerm);
  }).map(function() {
    return this.value;
  }).get().filter(v => v);
}

// Add URL Modal: Select All
$('#addUrlSelectAllBtn').on('click', function() {
  const $select = $('#add_url');
  const currentValues = $select.val() || [];
  const filtered = getFilteredOptionValues($select, addUrlCurrentSearchTerm);

  const merged = Array.from(new Set(currentValues.concat(filtered)));

  if (merged.length) {
    $select.val(merged).trigger('change');
  } else {
    $select.val(null).trigger('change');
  }
});


// Add URL Modal: Clear All
$('#addUrlClearAllBtn').on('click', function() {
  $('#add_url').val(null).trigger('change');
});

// QS URL Modal: Select All
$('#qsUrlSelectAllBtn').on('click', function() {
  const $select = $('#qs_url');
  const currentValues = $select.val() || [];
  const filtered = getFilteredOptionValues($select, qsUrlCurrentSearchTerm);

  const merged = Array.from(new Set(currentValues.concat(filtered)));

  if (merged.length) {
    $select.val(merged).trigger('change');
  } else {
    $select.val(null).trigger('change');
  }
});


// QS URL Modal: Clear All
$('#qsUrlClearAllBtn').on('click', function() {
  $('#qs_url').val(null).trigger('change');
});

// ------------------- Get visible options in open dropdown (still used if you need it) --------------------
function getCurrentlyVisibleOptions() {
  let ids = [];
  $('.select2-container--open .select2-results__option')
    .filter(':not(.select2-results__option--loading)')
    .filter(function () {
      return $(this).data('data') && $(this).attr('aria-disabled') !== 'true';
    })
    .each(function () {
      const data = $(this).data('data');
      if (data && data.id) ids.push(data.id);
    });
  return ids;
}

// ------------------- Keep Select2 open after (un)select --------------------
$('#add_url').on('select2:select select2:unselect', function () {
  let select = $(this);
  setTimeout(() => select.select2('open'), 0);
});

$('#qs_url').on('select2:select select2:unselect', function () {
  let select = $(this);
  setTimeout(() => select.select2('open'), 0);
});

// ------------------- Form Submit --------------------
function submitAddRecordForm() {
  const formData = $('#addRecordForm').serialize();
  $.post('meta_new.php', formData + '&ajax_action=insert_new_record', function(res) {
    const response = JSON.parse(res);
    if (response.status === 'success') {
      alert(response.message);
      $('#addRecordModal').modal('hide');
      if (window.dataTable) dataTable.ajax.reload();
    } else {
      alert('Error: ' + response.message);
    }
  });
}

function submitAddQsRecordForm() {
  const formData = $('#addQsRecordForm').serialize() + '&ajax_action=insert_qs_new_record';
  $.ajax({
    url: 'meta_new.php',
    type: 'POST',
    data: formData,
    dataType: 'json'
  })
  .done(function(response) {
    if (response.status === 'success') {
      alert(response.message);
      $('#addQsRecordModal').modal('hide');
      if (window.dataTable) dataTable.ajax.reload();
    } else {
      alert('Error: ' + response.message);
    }
  })
  .fail(function(xhr, status, error) {
    alert('Server error: ' + error + '. Check console for details.');
    console.error('AJAX Error:', status, error, xhr.responseText);
  });
}



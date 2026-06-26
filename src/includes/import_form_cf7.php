<?php

// Add toggle option to Screen Options
add_filter('screen_settings', 'add_cf7_importer_screen_options', 10, 2);
function add_cf7_importer_screen_options($status, $args) {
    if (isset($_GET['page']) && $_GET['page'] === 'wpcf7' && !isset($_GET['post'])) {
        $is_show = get_user_meta(get_current_user_id(), 'cf7_show_importer', true);
        $checked = $is_show ? 'checked="checked"' : '';
        $legend = esc_html__('Auto Form Generator', 'cf7-template-importer');
        $label = esc_html__('Template Importer Tool', 'cf7-template-importer');
        
        $status .= '
        <fieldset class="metabox-prefs">
            <legend>' . $legend . '</legend>
            <label for="show_cf7_importer">
                <input name="show_cf7_importer" type="checkbox" id="show_cf7_importer" value="1" ' . $checked . ' />
                ' . $label . '
            </label>
        </fieldset>';
    }
    return $status;
}

// Save state to Database via AJAX
add_action('wp_ajax_save_cf7_importer_state', 'ajax_save_cf7_importer_state');
function ajax_save_cf7_importer_state() {
    if (current_user_can('manage_options')) {
        $state = isset($_POST['state']) ? intval($_POST['state']) : 0;
        update_user_meta(get_current_user_id(), 'cf7_show_importer', $state);
    }
    wp_send_json_success();
}

// Add auto-generate button to Contact Form 7 admin screen
add_action('admin_footer', 'add_cf7_template_import_button');
function add_cf7_template_import_button() {
    // Only show on CF7 list page
    if (isset($_GET['page']) && $_GET['page'] === 'wpcf7' && !isset($_GET['post'])) {
        // Auto scan all .php files in theme
        $php_files = glob(get_template_directory() . '/*.php');
        $file_options = array();
        if ($php_files) {
            foreach ($php_files as $file) {
                $file_options[] = basename($file);
            }
        }
        // Get display state from database
        $is_show = get_user_meta(get_current_user_id(), 'cf7_show_importer', true);
        $display = $is_show ? 'flex' : 'none';
        ?>
        <div id="cf7-template-importer-panel" class="notice notice-info" style="display: <?php echo $display; ?>; margin: 15px 0; padding: 10px 15px; background: #fff; border-left: 4px solid #2271b1; box-shadow: 0 1px 1px rgba(0,0,0,.04); align-items: center; gap: 15px; flex-wrap: wrap;">
            <div style="font-weight: 600; font-size: 14px; color: #1d2327; display: flex; align-items: center; gap: 5px; white-space: nowrap;">
                <span class="dashicons dashicons-media-code" style="color: #2271b1; margin-top: 2px;"></span>
                <?php esc_html_e('Import Form from Template', 'cf7-template-importer'); ?>
            </div>
            
            <div style="display: flex; gap: 10px; align-items: center; flex: 1; flex-wrap: wrap;">
                <input type="text" id="cf7-template-title" placeholder="<?php esc_attr_e('Form Name...', 'cf7-template-importer'); ?>" style="flex: 1; min-width: 180px; padding: 0 8px; line-height: 2; min-height: 30px; height: 30px;">
                
                <select id="cf7-template-file" style="flex: 1; min-width: 220px; padding: 0 8px; line-height: 2; height: 30px; border: 1px solid #8c8f94; border-radius: 3px;">
                    <option value=""><?php esc_html_e('-- Select Template File --', 'cf7-template-importer'); ?></option>
                    <?php foreach ($file_options as $file): ?>
                        <option value="<?php echo esc_attr($file); ?>"><?php echo esc_html($file); ?></option>
                    <?php endforeach; ?>
                </select>
                
                <input type="text" id="cf7-template-class" placeholder="<?php esc_attr_e('CSS Class...', 'cf7-template-importer'); ?>" style="flex: 1; min-width: 180px; padding: 0 8px; line-height: 2; min-height: 30px; height: 30px;">
            </div>
            
            <button id="cf7-import-btn" class="button button-primary" style="display: inline-flex; align-items: center; justify-content: center; gap: 4px; height: 30px; min-height: 30px; padding: 0 12px; margin: 0; white-space: nowrap;">
                <span class="dashicons dashicons-update-alt" style="font-size: 16px; width: 16px; height: 16px; display: flex; align-items: center;"></span>
                <?php esc_html_e('Generate Form', 'cf7-template-importer'); ?>
            </button>
        </div>
        
        <script>
        (function() {
            var i18n = {
                missingName: "<?php echo esc_js(__('Missing Form Name!', 'cf7-template-importer')); ?>",
                missingFile: "<?php echo esc_js(__('Missing Template File!', 'cf7-template-importer')); ?>",
                missingClass: "<?php echo esc_js(__('Missing CSS Class!', 'cf7-template-importer')); ?>",
                processing: "<?php echo esc_js(__('Processing...', 'cf7-template-importer')); ?>",
                errorPrefix: "<?php echo esc_js(__('Error: ', 'cf7-template-importer')); ?>",
                unknownError: "<?php echo esc_js(__('An error occurred: ', 'cf7-template-importer')); ?>",
                networkError: "<?php echo esc_js(__('Network error!', 'cf7-template-importer')); ?>"
            };

            // Load Select2 library to add quick search to Select tag
            if (typeof jQuery !== 'undefined') {
                if (!document.getElementById('select2-css')) {
                    var link = document.createElement('link');
                    link.id = 'select2-css';
                    link.rel = 'stylesheet';
                    link.href = 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css';
                    document.head.appendChild(link);
                }
                if (typeof jQuery.fn.select2 === 'undefined') {
                    var script = document.createElement('script');
                    script.src = 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js';
                    script.onload = function() {
                        jQuery('#cf7-template-file').select2();
                        jQuery('.select2-selection--single').css({'height': '30px', 'display': 'flex', 'align-items': 'center', 'border': '1px solid #8c8f94'});
                    };
                    document.head.appendChild(script);
                } else {
                    jQuery('#cf7-template-file').select2();
                }
            }

            // Move panel to the best position in wp-admin (under page title)
            var headerEnd = document.querySelector('.wp-header-end');
            var panel = document.getElementById('cf7-template-importer-panel');
            if (headerEnd && panel) {
                headerEnd.parentNode.insertBefore(panel, headerEnd.nextSibling);
            }
            
            // Manage state using Database (AJAX)
            var checkbox = document.getElementById('show_cf7_importer');
            if (checkbox && panel) {
                checkbox.addEventListener('change', function() {
                    var isChecked = this.checked;
                    panel.style.display = isChecked ? 'flex' : 'none';
                    
                    var fd = new FormData();
                    fd.append('action', 'save_cf7_importer_state');
                    fd.append('state', isChecked ? 1 : 0);
                    fetch(ajaxurl, { method: 'POST', body: fd });
                });
            }
            
            var input = document.getElementById('cf7-template-title');
            var inputFile = document.getElementById('cf7-template-file');
            var inputClass = document.getElementById('cf7-template-class');
            var btn = document.getElementById('cf7-import-btn');
            
            if (!btn) return;

                input.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        btn.click();
                    }
                });
                inputClass.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        btn.click();
                    }
                });
                // No need to listen to Enter key on file input since using Select2
                
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    var title = input.value.trim();
                    var className = inputClass.value.trim();
                    var fileName = inputFile.value.trim();
                    
                    if (!title) {
                        alert(i18n.missingName);
                        return;
                    }
                    if (!fileName) {
                        alert(i18n.missingFile);
                        return;
                    }
                    if (!className) {
                        alert(i18n.missingClass);
                        return;
                    }
                    
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner is-active" style="float:none; margin:0 5px 0 0;"></span> ' + i18n.processing;
                    input.disabled = true;
                    inputClass.disabled = true;
                    inputFile.disabled = true;
                    
                    var formData = new FormData();
                    formData.append('action', 'import_default_cf7');
                    formData.append('_wpnonce', '<?php echo wp_create_nonce("import_cf7_nonce"); ?>');
                    formData.append('form_title', title);
                    formData.append('form_file', fileName);
                    formData.append('form_class', className);
                    
                    fetch(ajaxurl, {
                        method: 'POST',
                        body: formData
                    }).then(res => res.json()).then(response => {
                        if (response.success) {
                            if (response.data.status === 'success') {
                                window.location.href = window.location.href; // reload
                            } else {
                                alert(i18n.errorPrefix + response.data.message);
                                window.location.reload();
                            }
                        } else {
                            alert(i18n.unknownError + (response.data ? response.data.message : 'Unknown error'));
                            window.location.reload();
                        }
                    }).catch(err => {
                        alert(i18n.networkError);
                        window.location.reload();
                    });
                });
        })();
        </script>
        <?php
    }
}

// Process AJAX dynamic template import
add_action('wp_ajax_import_default_cf7', 'ajax_handle_cf7_template_import');
function ajax_handle_cf7_template_import() {
    check_ajax_referer('import_cf7_nonce', '_wpnonce');
    
    if (!current_user_can('wpcf7_edit_contact_forms')) {
        wp_send_json_success(array('status' => 'error', 'message' => __('You do not have permission to manage CF7.', 'cf7-template-importer')));
        exit;
    }
    
    $form_title = isset($_POST['form_title']) ? sanitize_text_field($_POST['form_title']) : '';
    $form_class = isset($_POST['form_class']) ? sanitize_text_field($_POST['form_class']) : '';
    $form_file = isset($_POST['form_file']) ? sanitize_text_field($_POST['form_file']) : '';
    if (empty($form_title) || empty($form_class) || empty($form_file)) {
        wp_send_json_success(array('status' => 'error', 'message' => __('Missing Form information.', 'cf7-template-importer')));
        exit;
    }
    
    $existing_form = get_page_by_title($form_title, OBJECT, 'wpcf7_contact_form');
    if ($existing_form) {
        wp_send_json_success(array('status' => 'error', 'message' => __('Form name already exists.', 'cf7-template-importer')));
        exit;
    }

    // 1. Read file content
    $possible_paths = array(
        get_stylesheet_directory() . '/' . ltrim($form_file, '/'),
        get_template_directory() . '/' . ltrim($form_file, '/')
    );

    $file_path = '';
    foreach ($possible_paths as $path) {
        if (file_exists($path)) {
            $file_path = realpath($path) ?: $path;
            break;
        }
    }
    
    if (empty($file_path) || !file_exists($file_path)) {
        wp_send_json_success(array('status' => 'error', 'message' => __('File not found: ', 'cf7-template-importer') . $form_file));
        exit;
    }
    
    if (!is_writable($file_path)) {
        wp_send_json_success(array('status' => 'error', 'message' => __('File is not writable: ', 'cf7-template-importer') . basename($file_path)));
        exit;
    }
    
    $content = file_get_contents($file_path);

    // 2. Find static form block via regex based on user-input class
    // This regex finds the opening tag (e.g. <div class="c-form" ...>)
    // IMPROVEMENT: Ignore '>' if it's inside a PHP tag
    $php_or_not_gt = '(?:[^>]|<\?.*?\?' . '>)';
    $class_pattern = '/<([a-zA-Z0-9]+)(' . $php_or_not_gt . '*?class="[^"]*\b' . preg_quote($form_class, '/') . '\b[^"]*"' . $php_or_not_gt . '*?)>/is';
    
    if (!preg_match($class_pattern, $content, $matches, PREG_OFFSET_CAPTURE)) {
        wp_send_json_success(array('status' => 'error', 'message' => __('Class not found: ', 'cf7-template-importer') . $form_class));
        exit;
    }

    $start_pos = $matches[0][1];
    $tag_name = strtolower($matches[1][0]);

    $length = strlen($content);
    $tag_count = 0;
    $end_pos = -1;
    $current_pos = $start_pos;

    while ($current_pos < $length) {
        $next_open = strpos(strtolower($content), '<' . $tag_name, $current_pos);
        $next_close = strpos(strtolower($content), '</' . $tag_name, $current_pos);

        if ($next_open === false && $next_close === false) {
            break;
        }

        if ($next_open !== false && $next_open < $next_close) {
            // Ensure not catching wrong tags (e.g. div vs div2)
            $char_after = substr($content, $next_open + strlen($tag_name) + 1, 1);
            if ($char_after === ' ' || $char_after === '>' || $char_after === "\n" || $char_after === "\r" || $char_after === "\t") {
                $tag_count++;
            }
            $current_pos = $next_open + strlen($tag_name) + 1;
        } else if ($next_close !== false) {
            $tag_count--;
            $current_pos = $next_close + strlen($tag_name) + 3;

            if ($tag_count === 0) {
                $end_tag_close_pos = strpos($content, '>', $next_close);
                if ($end_tag_close_pos !== false) {
                    $end_pos = $end_tag_close_pos + 1;
                } else {
                    $end_pos = $current_pos;
                }
                break;
            }
        }
    }

    if ($end_pos === -1) {
        wp_send_json_success(array('status' => 'error', 'message' => 'Malformed HTML: Form end not found.'));
        exit;
    }

    $raw_form_html = substr($content, $start_pos, $end_pos - $start_pos);

    // 3. Initialize DOMDocument
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    // Wrap in dummy body for parsing
    $dom->loadHTML('<?xml encoding="utf-8" ?><body>' . $raw_form_html . '</body>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    libxml_clear_errors();
    $xpath = new DOMXPath($dom);

    $fields = array();

    if (!function_exists('cf7_get_title_for_node')) {
        function cf7_get_title_for_node($node, $xpath) {
            // Automatically find the closest preceding wpf-title tag (regardless of HTML hierarchy)
            $title_node = $xpath->query('(ancestor::*[contains(concat(" ", normalize-space(@class), " "), " wpf-title ")] | preceding::*[contains(concat(" ", normalize-space(@class), " "), " wpf-title ")])[last()]', $node)->item(0);
            
            if ($title_node) {
                $clone = $title_node->cloneNode(true);
                // Remove child nodes (like span required) to get pure text only
                foreach ($xpath->query('.//*', $clone) as $child) {
                    $child->parentNode->removeChild($child);
                }
                // Remove extra whitespace
                return trim(preg_replace('/\s+/', ' ', $clone->textContent));
            }
            return '';
        }
    }

    $email_field_name = '';
    $name_field_name = '';
    $file_field_names = array();

    // A. Process text, email, tel, url, number, date, hidden and other custom inputs
    $inputs = $xpath->query('//input[not(@type="radio") and not(@type="checkbox") and not(@type="file") and not(@type="submit") and not(@type="button") and not(@type="image")]');
    foreach ($inputs as $input) {
        $name = $input->getAttribute('name');
        if (!$name) continue;
        $type = $input->getAttribute('type');
        
        // Process hidden tag specifically
        if ($type === 'hidden') {
            $value = $input->getAttribute('value');
            $tag = "[hidden $name \"$value\"]";
            
            $textNode = $dom->createTextNode($tag);
            $input->parentNode->replaceChild($textNode, $input);
            continue;
        }

        $required = $input->getAttribute('required') === 'required' ? '*' : '';
        $class = $input->getAttribute('class');
        $class_str = $class ? " class:$class" : "";
        $id = $input->getAttribute('id');
        $id_str = $id ? " id:$id" : "";
        $placeholder = $input->getAttribute('placeholder');
        $placeholder_str = $placeholder ? " placeholder \"$placeholder\"" : "";
        
        // CF7 supports [url], [number], [date] besides [email], [tel]. Keep as is if dev uses special custom type
        $tag_type = in_array($type, array('email', 'tel', 'url', 'number', 'date', 'your-email-re')) ? $type : 'text';
        
        if (empty($email_field_name) && ($type === 'email' || strpos(strtolower($name), 'mail') !== false || strpos(strtolower($name), 'email') !== false)) {
            $email_field_name = $name;
            if ($tag_type === 'text') {
                $tag_type = 'email'; // Automatically upgrade to CF7 email tag for standard validation
            }
        }
        
        // Try to find customer name field (e.g. your-name, customer_name, etc...)
        if (strpos(strtolower($name), 'name') !== false && empty($name_field_name)) {
            $name_field_name = $name;
        }
        
        // Support min/max for number and date
        $min = $input->getAttribute('min');
        $max = $input->getAttribute('max');
        $min_str = $min !== '' ? " min:$min" : "";
        $max_str = $max !== '' ? " max:$max" : "";
        
        $tag = "[$tag_type$required $name$id_str$class_str$min_str$max_str$placeholder_str]";
        
        $title = cf7_get_title_for_node($input, $xpath);
        if ($title) $fields[$name] = $title;
        
        $textNode = $dom->createTextNode($tag);
        $input->parentNode->replaceChild($textNode, $input);
    }

    // B. Process Textarea
    $textareas = $xpath->query('//textarea');
    foreach ($textareas as $textarea) {
        $name = $textarea->getAttribute('name');
        if (!$name) continue;
        $required = $textarea->getAttribute('required') === 'required' ? '*' : '';
        $class = $textarea->getAttribute('class');
        $id = $textarea->getAttribute('id');
        $placeholder = $textarea->getAttribute('placeholder');
        
        $class_str = $class ? " class:$class" : "";
        $id_str = $id ? " id:$id" : "";
        $placeholder_str = $placeholder ? " placeholder \"$placeholder\"" : "";
        
        $tag = "[textarea$required $name$id_str$class_str$placeholder_str]";
        
        $title = cf7_get_title_for_node($textarea, $xpath);
        if ($title) $fields[$name] = $title;
        
        $textNode = $dom->createTextNode($tag);
        $textarea->parentNode->replaceChild($textNode, $textarea);
    }

    // C. Process Select
    $selects = $xpath->query('//select');
    foreach ($selects as $select) {
        $name = $select->getAttribute('name');
        if (!$name) continue;
        $required = $select->getAttribute('required') === 'required' ? '*' : '';
        $class = $select->getAttribute('class');
        $id = $select->getAttribute('id');
        
        $options = array();
        $first_as_label = false;
        $first = true;
        foreach ($xpath->query('.//option', $select) as $option) {
            $val = trim($option->textContent);
            if ($first && $option->getAttribute('value') === '') {
                $first_as_label = true;
                $options[] = "\"$val\"";
            } else {
                $options[] = "\"$val\"";
            }
            $first = false;
        }
        
        $class_str = $class ? " class:$class" : "";
        $id_str = $id ? " id:$id" : "";
        $tag = "[select$required $name$id_str$class_str" . ($first_as_label ? " first_as_label" : "") . " " . implode(" ", $options) . "]";
        
        $title = cf7_get_title_for_node($select, $xpath);
        if ($title) $fields[$name] = $title;
        
        $textNode = $dom->createTextNode($tag);
        $select->parentNode->replaceChild($textNode, $select);
    }

    // D. Process Checkbox / Radio
    $check_radio_names = array();
    foreach ($xpath->query('//input[@type="checkbox" or @type="radio"]') as $input) {
        $name = $input->getAttribute('name');
        $is_acceptance = $xpath->query('ancestor-or-self::*[contains(concat(" ", normalize-space(@class), " "), " wpf-acceptance ")]', $input)->length > 0;
        if ($name && !$is_acceptance) {
            $check_radio_names[$name] = $input->getAttribute('type');
        }
    }

    foreach ($check_radio_names as $name => $type) {
        $inputs = $xpath->query('//input[@name="'.$name.'"]');
        if ($inputs->length == 0) continue;
        
        $first_input = $inputs->item(0);
        $required = $first_input->getAttribute('required') === 'required' ? '*' : '';
        if ($type == 'radio') $required = ''; // CF7 radio does not have *
        
        $options = array();
        
        $inputs_array = array();
        foreach ($inputs as $inp) {
            $inputs_array[] = $inp;
        }

        // Automatically find the Lowest Common Ancestor (LCA) containing all these inputs
        $lca = $first_input->parentNode;
        while ($lca && $lca->nodeName != 'body') {
            $contains_all = true;
            foreach ($inputs_array as $inp) {
                $p = $inp;
                $found = false;
                while ($p && $p->nodeName != 'body') {
                    if ($p === $lca) {
                        $found = true;
                        break;
                    }
                    $p = $p->parentNode;
                }
                if (!$found) {
                    $contains_all = false;
                    break;
                }
            }
            if ($contains_all) {
                break;
            }
            $lca = $lca->parentNode;
        }

        foreach ($inputs as $input) {
            $label = $xpath->query('ancestor-or-self::label', $input)->item(0);
            if ($label) {
                $clone = $label->cloneNode(true);
                foreach ($xpath->query('.//input', $clone) as $inp) {
                    $inp->parentNode->removeChild($inp);
                }
                $options[] = "\"" . trim($clone->textContent) . "\"";
            } else {
                $options[] = "\"" . $input->getAttribute('value') . "\"";
            }
        }
        
        $id = $first_input->getAttribute('id');
        $id_str = $id ? " id:$id" : "";
        $options_str = implode(" ", $options);
        $tag = ($type == 'checkbox') ? "[checkbox$required $name$id_str use_label_element $options_str]" : "[radio $name$id_str use_label_element default:1 $options_str]";
        
        $title = cf7_get_title_for_node($first_input, $xpath);
        if ($title) $fields[$name] = $title;
        
        // Remove all wrapper nodes of the remaining inputs (from the 2nd input onwards)
        $nodes_to_remove = array();
        for ($i = 1; $i < count($inputs_array); $i++) {
            $node = $inputs_array[$i];
            while ($node->parentNode && $node->parentNode !== $lca && $node->parentNode->nodeName !== 'body') {
                $node = $node->parentNode;
            }
            if ($node && $node->parentNode === $lca && !in_array($node, $nodes_to_remove, true)) {
                $nodes_to_remove[] = $node;
            }
        }
        
        foreach ($nodes_to_remove as $node) {
            // Remove extra whitespace (newlines, tabs) before the node to prevent blank lines
            $prev = $node->previousSibling;
            if ($prev && $prev->nodeType === XML_TEXT_NODE && trim($prev->textContent) === '') {
                $prev->parentNode->removeChild($prev);
            }
            $node->parentNode->removeChild($node);
        }
        
        // Replace the first input's wrapper node with shortcode
        $first_node = $first_input;
        while ($first_node->parentNode && $first_node->parentNode !== $lca && $first_node->parentNode->nodeName !== 'body') {
            $first_node = $first_node->parentNode;
        }
        
        $textNode = $dom->createTextNode($tag);
        if ($first_node && $first_node->parentNode === $lca) {
            $lca->replaceChild($textNode, $first_node);
        } else {
            $first_input->parentNode->replaceChild($textNode, $first_input);
        }
    }

    // E. Process File upload
    $files = $xpath->query('//input[@type="file"]');
    foreach ($files as $file) {
        $name = $file->getAttribute('name');
        if (!$name) continue;
        
        $file_field_names[] = $name;
        $required = $file->getAttribute('required') === 'required' ? '*' : '';
        $accept = $file->getAttribute('accept');
        $filetypes = str_replace(array('.', ','), array('', '|'), $accept);
        $class = $file->getAttribute('class');
        $class_str = $class ? " class:$class" : "";
        $id = $file->getAttribute('id');
        $id_str = $id ? " id:$id" : "";
        
        $tag = "[file$required $name$id_str limit:10mb filetypes:$filetypes$class_str]";
        
        $title = cf7_get_title_for_node($file, $xpath);
        if ($title) $fields[$name] = $title;
        
        $textNode = $dom->createTextNode($tag);
        $file->parentNode->replaceChild($textNode, $file);
    }

    // F. Process Acceptance
    $acceptances = $xpath->query('//*[contains(concat(" ", normalize-space(@class), " "), " wpf-acceptance ")]');
    foreach ($acceptances as $acc_node) {
        $input = $xpath->query('.//input[@type="checkbox"]', $acc_node)->item(0);
        if (!$input) continue;
        $name = $input->getAttribute('name');
        $label = $xpath->query('.//label', $acc_node)->item(0);
        $text = '';
        if ($label) {
            $clone = $label->cloneNode(true);
            foreach ($xpath->query('.//input', $clone) as $inp) {
                $inp->parentNode->removeChild($inp);
            }
            $text = trim($clone->textContent);
        }
        $class = $input->getAttribute('class');
        $class_str = $class ? " class:$class" : "";
        $id = $input->getAttribute('id');
        $id_str = $id ? " id:$id" : "";
        $tag = "[acceptance $name$id_str$class_str] $text [/acceptance]";
        
        $title = cf7_get_title_for_node($input, $xpath);
        if ($title) $fields[$name] = $title;
        
        $textNode = $dom->createTextNode($tag);
        
        // IMPROVEMENT: Keep wpf-acceptance container instead of deleting it
        if ($acc_node->nodeName !== 'label') {
            while ($acc_node->hasChildNodes()) {
                $acc_node->removeChild($acc_node->firstChild);
            }
            $acc_node->appendChild($textNode);
        } else {
            $acc_node->parentNode->replaceChild($textNode, $acc_node);
        }
    }

    // G. Process Submit Button
    $submits = $xpath->query('//input[@type="submit"] | //button[@type="submit"]');
    foreach ($submits as $submit) {
        $class = $submit->getAttribute('class');
        $class_str = $class ? " class:$class" : "";
        $id = $submit->getAttribute('id');
        $id_str = $id ? " id:$id" : "";
        
        if (strtolower($submit->nodeName) === 'input') {
            $text = trim($submit->getAttribute('value'));
        } else {
            $clone = $submit->cloneNode(true);
            foreach ($xpath->query('.//*', $clone) as $child) {
                $child->parentNode->removeChild($child);
            }
            $text = trim(preg_replace('/\s+/', ' ', $clone->textContent));
        }
        
        $text_str = $text ? " \"$text\"" : "";
        $tag = "[submit$id_str$class_str$text_str]";
        
        $textNode = $dom->createTextNode($tag);
        $submit->parentNode->replaceChild($textNode, $submit);
    }

    // 4. Generate final form HTML code
    $form_html = $dom->saveHTML();
    $form_html = str_replace(array('<?xml encoding="utf-8" ?>', '<body>', '</body>'), '', $form_html);
    $form_html = html_entity_decode($form_html, ENT_QUOTES, 'UTF-8');
    $form_html = str_replace(array('%5B', '%5D'), array('[', ']'), $form_html); // Fix saveHTML encode error
    $form_html = trim($form_html);

    // 5. Auto generate Mail 1 and Mail 2 content
    $mail_body_content = "";
    foreach ($fields as $name => $title) {
        if ($title) {
            $mail_body_content .= "■ {$title}: [{$name}]\n";
        }
    }

    $mail_1_body = "以下の内容でお問い合わせがありました。\n\n━━━━━━　お問い合わせ内容　━━━━━━\n" . $mail_body_content . "\n━━━━━━━━━━━━━━━━━━";

    $customer_name_tag = !empty($name_field_name) ? "[$name_field_name]" : "[your-name]";
    $mail_2_body = "{$customer_name_tag} 様\n\nこの度はお問い合せ頂き誠にありがとうございました。\n改めて担当者よりご連絡をさせていただきます。\n\n\n━━━━━━　お問い合わせ内容　━━━━━━\n" . $mail_body_content . "\n━━━━━━━━━━━━━━━━━━\n\n頂戴いたしましたお問い合わせにつきましては、内容を確認の上、\n後ほどご回答いたします。\nなお、お問い合わせの内容によっては、ご回答まで数日かかる場合\nやご回答いたしかねる場合がございます。\n恐れ入りますが、予めご了承くださいますようお願いいたします。\n\n\n———————————————————————\n\n*company name\n*company address and tell number\n\n———————————————————————";

    // 6. Create new form
    $post_args = array(
        'post_title' => $form_title,
        'post_type' => 'wpcf7_contact_form',
        'post_status' => 'publish'
    );
    
    $post_id = wp_insert_post($post_args);
    
    if ($post_id && !is_wp_error($post_id)) {
        // Save HTML to post meta
        update_post_meta($post_id, '_form', $form_html);
        
        $attachments_str = !empty($file_field_names) ? '[' . implode('] [', $file_field_names) . ']' : '';
        $recipient_str = !empty($email_field_name) ? "[$email_field_name]" : '[your-email]';

        // Mail 1
        update_post_meta($post_id, '_mail', array(
            'active' => true,
            'subject' => 'お問い合わせフォームから',
            'sender' => '*company name <kensyo@j-line.co.jp>',
            'recipient' => 'kensyo@j-line.co.jp',
            'body' => $mail_1_body,
            'additional_headers' => 'Reply-To: ' . $recipient_str,
            'attachments' => $attachments_str,
            'use_html' => false,
            'exclude_blank' => true
        ));
        
        // Mail 2
        update_post_meta($post_id, '_mail_2', array(
            'active' => true,
            'subject' => 'お問い合わせを受け付けました｜*company name',
            'sender' => '*company name <kensyo@j-line.co.jp>',
            'recipient' => $recipient_str,
            'body' => $mail_2_body,
            'additional_headers' => 'Reply-To: kensyo@j-line.co.jp',
            'attachments' => '',
            'use_html' => false,
            'exclude_blank' => true
        ));
        
        $cf7_messages = array();
        if (class_exists('WPCF7_ContactFormTemplate') && method_exists('WPCF7_ContactFormTemplate', 'messages')) {
            $cf7_messages = WPCF7_ContactFormTemplate::messages();
        } elseif (function_exists('wpcf7_messages')) {
            foreach (wpcf7_messages() as $key => $arr) {
                $cf7_messages[$key] = isset($arr['default']) ? $arr['default'] : '';
            }
        }
        update_post_meta($post_id, '_messages', $cf7_messages);
        update_post_meta($post_id, '_additional_settings', "acceptance_as_validation: on\n");
        update_post_meta($post_id, '_locale', get_user_locale());

        $hash_id = '';
        if (function_exists('wpcf7_generate_contact_form_hash')) {
            $hash_id = wpcf7_generate_contact_form_hash($post_id);
            update_post_meta($post_id, '_hash', $hash_id);
        }

        // Get the first 7 characters of hash like how it displays in WP Admin
        $short_hash = !empty($hash_id) ? substr($hash_id, 0, 7) : '';

        // 7. Edit page-contactform7.php with shortcode
        // Use original string to accurately replace that entire div block
        $id_for_shortcode = !empty($short_hash) ? $short_hash : $post_id;
        $shortcode = "<?php echo do_shortcode('[contact-form-7 id=\"{$id_for_shortcode}\" title=\"{$form_title}\"]'); ?>";
        $new_content = str_replace($raw_form_html, $shortcode, $content);
        
        if (file_put_contents($file_path, $new_content)) {
            // SYNC BACK TO src/ DIRECTORY IF RUNNING LOCAL
            $theme_dir = get_template_directory();
            // Go back 4 levels: original-theme -> themes -> wp-content -> public -> TEMPLATE_WP
            $project_root = dirname(dirname(dirname(dirname($theme_dir))));
            $src_file_path = $project_root . '/src/' . ltrim($form_file, '/');
            
            if (file_exists($src_file_path) && is_writable($src_file_path)) {
                $src_content = file_get_contents($src_file_path);
                $new_src_content = str_replace($raw_form_html, $shortcode, $src_content);
                file_put_contents($src_file_path, $new_src_content);
            }
            
            wp_send_json_success(array('status' => 'success'));
        } else {
            wp_send_json_success(array('status' => 'error', 'message' => __('Failed to write to file.', 'cf7-template-importer')));
        }
    } else {
        wp_send_json_success(array('status' => 'error', 'message' => __('Database error.', 'cf7-template-importer')));
    }
}

// -----------------------------------------------------------------------------
// AUTOMATIC TRANSLATION (i18n) WITHOUT .MO FILE
// -----------------------------------------------------------------------------
add_filter('gettext', 'cf7_importer_translate', 10, 3);
function cf7_importer_translate($translated_text, $text, $domain) {
    if ($domain === 'cf7-template-importer') {
        $locale = get_user_locale();
        
        // Vietnamese Translation
        if (strpos($locale, 'vi') === 0) {
            $strings = array(
                'Auto Form Generator' => 'Tạo Form Tự Động',
                'Template Importer Tool' => 'Công cụ Import Template',
                'Import Form from Template' => 'Import Form từ Template',
                'Form Name...' => 'Tên Form...',
                '-- Select Template File --' => '-- Chọn File Template --',
                'CSS Class...' => 'Class CSS...',
                'Generate Form' => 'Tạo Form',
                'Missing Form Name!' => 'Thiếu Tên Form!',
                'Missing Template File!' => 'Thiếu File Template!',
                'Missing CSS Class!' => 'Thiếu Class CSS!',
                'Processing...' => 'Đang xử lý...',
                'Error: ' => 'Lỗi: ',
                'An error occurred: ' => 'Có lỗi xảy ra: ',
                'Network error!' => 'Lỗi mạng!',
                'You do not have permission to manage CF7.' => 'Không có quyền quản lý CF7.',
                'Missing Form information.' => 'Thiếu thông tin Form.',
                'Form name already exists.' => 'Tên form đã tồn tại.',
                'File not found: ' => 'Không tìm thấy file: ',
                'File is not writable: ' => 'File không có quyền ghi: ',
                'Class not found: ' => 'Không tìm thấy Class: ',
                'Failed to write to file.' => 'Lưu form thành công nhưng không thể ghi đè file template tĩnh. Hãy kiểm tra quyền (CHMOD).',
                'Database error.' => 'Lỗi khi tạo post trong database.'
            );
        }
        // Japanese Translation
        elseif (strpos($locale, 'ja') === 0) {
            $strings = array(
                'Auto Form Generator' => '自動フォームジェネレーター',
                'Template Importer Tool' => 'テンプレートからインポート',
                'Import Form from Template' => 'テンプレートからフォームをインポート',
                'Form Name...' => 'フォーム名...',
                '-- Select Template File --' => '-- テンプレートファイルを選択 --',
                'CSS Class...' => 'CSSクラス...',
                'Generate Form' => 'フォーム作成',
                'Missing Form Name!' => 'フォーム名がありません！',
                'Missing Template File!' => 'テンプレートファイルがありません！',
                'Missing CSS Class!' => 'CSSクラスがありません！',
                'Processing...' => '処理中...',
                'Error: ' => 'エラー：',
                'An error occurred: ' => 'エラーが発生しました：',
                'Network error!' => 'ネットワークエラー！',
                'You do not have permission to manage CF7.' => 'CF7を管理する権限がありません。',
                'Missing Form information.' => 'フォーム情報が不足しています。',
                'Form name already exists.' => 'そのフォーム名は既に存在します。',
                'File not found: ' => 'ファイルが見つかりません：',
                'File is not writable: ' => 'ファイルに書き込み権限がありません：',
                'Class not found: ' => 'クラスが見つかりません：',
                'Failed to write to file.' => 'フォームの保存に成功しましたが、ファイルの上書きに失敗しました。パーミッションを確認してください。',
                'Database error.' => 'データベースエラー。'
            );
        }
        
        if (isset($strings) && isset($strings[$text])) {
            return $strings[$text];
        }
    }
    return $translated_text;
}

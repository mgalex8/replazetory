function doPost(e) {
  return handleRequest(e);
}

function handleRequest(e) {
  var errors = Array();

  if (typeof e.parameter == 'undefined') {
    errors.push('parameter `parameter` is undefined');
    e.errors = errors;
    return ContentService.createTextOutput(JSON.stringify(e));
  }
  else {
    if (typeof e.parameter.source_lang == 'undefined') {
      errors.push('parameter source_lang is required');
    }
    if (typeof e.parameter.target_lang == 'undefined') {
      errors.push('parameter target_lang is required');
    }
    if (typeof e.parameter.text == 'undefined') {
      errors.push('parameter text is required');
    }

    var source_lang = e.parameter.source_lang.trim();
    if (source_lang == '') {
      errors.push('parameter source_lang is not be empty');
    }

    var target_lang = e.parameter.target_lang.trim();
    if (target_lang == '') {
      errors.push('parameter target_lang is not be empty');
    }

    var text = e.parameter.text.trim();
    if (text == '') {
      errors.push('parameter text is not be empty');
    }

    if (source_lang == 'auto') {
      source_lang = '';
    }
    try {
      var result_text = LanguageApp.translate(text, source_lang, target_lang);
    } catch(err) {
      errors.push(err.message);
    }

    if (errors.length == 0 && typeof result_text !== 'undefined') {
      var result = JSON.stringify({
        status: 'success',
        source_lang: source_lang,
        target_lang: target_lang,
        result_text: result_text,
        text: text,
      })
    } else {
      var result = JSON.stringify({
        status: 'error',
        source_lang: source_lang,
        target_lang: target_lang,
        text: text,
        errors: errors,
      })
    }
    return ContentService.createTextOutput(result);
  }
}

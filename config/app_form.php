<?php 
return [
    'inputContainer' => '<div class="form-group row">{{content}}</div>',
    'input'               => '<div class="col-9"><input class="form-control" type="{{type}}" name="{{name}}" for="{{name}}" {{attrs}}></div>',
    'file'               => '<div class="col-9"><div class="custom-file">
							<input type="file" name="{{name}}" class="custom-file-input" id="customFile">
							<label class="custom-file-label" for="customFile">Choisir le fichier</label>
						</div></div>',
    'select' => '<div class="col-9"><select name="{{name}}"{{attrs}}>{{content}}</select></div>',
	'option' => '<option value="{{value}}"{{attrs}}>{{text}}</option>',
    'label'               => '<label {{required}}  class="col-3">{{text}}</label>',
    'error'               => '<span class="help-block">{{content}}</span>',
    'textarea'            => '<textarea class="form-control input-sm" rows="2" name="{{name}}" {{attrs}}>{{value}}</textarea>',
    'inputContainerError' => '<div class="form-group has-error" {{required}}>{{content}}</div>'
];
 ?>
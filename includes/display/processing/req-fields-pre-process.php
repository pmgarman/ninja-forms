<?php
add_action( 'init', 'ninja_forms_register_req_fields_process' );
function ninja_forms_register_req_fields_process(){
	add_action( 'ninja_forms_pre_process', 'ninja_forms_req_fields_process', 13);
}

function ninja_forms_req_fields_process(){
	global $ninja_forms_processing, $ninja_forms_fields;
	$all_fields = $ninja_forms_processing->get_all_fields();

	if( is_array( $all_fields ) AND !empty( $all_fields ) ){
		foreach($all_fields as $field_id => $user_value){
			$field_row = $ninja_forms_processing->get_field_settings( $field_id );

			if( isset( $field_row['data'] ) ){
				$field_data = $field_row['data'];
			}else{
				$field_data = '';
			}

			$field_type = $field_row['type'];

			if( isset( $field_data['req'] ) ){
				$req = $field_data['req'];
			}else{
				$req = '';
			}

			if( isset( $field_data['label_pos'] ) ){
				$label_pos = $field_data['label_pos'];
			}else{
				$label_pos = '';
			}

			if( isset( $field_data['label'] ) ){
				$label = $field_data['label'];
			}else{
				$label = '';
			}

			$reg_type = $ninja_forms_fields[$field_type];
			$req_validation = $reg_type['req_validation'];

			$plugin_settings = get_option("ninja_forms_settings");
			$req_field_error = $plugin_settings['req_field_error'];
			if( isset( $plugin_settings['req_error_label'] ) ){
				$req_error_label = $plugin_settings['req_error_label'];
			}else{
				$req_error_label = __( 'Please check required fields.', 'ninja-forms' );
			}

			if($req == 1){
				if($req_validation != ''){
					$arguments['field_id'] = $field_id;
					$arguments['user_value'] = $user_value;
					$req = call_user_func_array($req_validation, $arguments);
					if(!$req){
						$ninja_forms_processing->add_error('required-'.$field_id, $req_field_error, $field_id);
						$ninja_forms_processing->add_error('required-general', $req_error_label, 'general');
					}
				}else{
					if($label_pos == 'inside'){
						if($user_value == $label){
							$ninja_forms_processing->add_error('required-'.$field_id, $req_field_error, $field_id);
							$ninja_forms_processing->add_error('required-general', $req_error_label, 'general');
						}
					}else{
						if($user_value == ''){
							$ninja_forms_processing->add_error('required-'.$field_id, $req_field_error, $field_id);
							$ninja_forms_processing->add_error('required-general', $req_error_label, 'general');
						}
					}
				}
			}
		}
	}
}
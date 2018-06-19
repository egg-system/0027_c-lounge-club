<?php

/**
 * Description of class
 *
 * @author nur
 */
class SwpmFbProfileFormmeta extends SwpmFbFormmeta {

    public function create() {
        $this->id = null; // force to save it as new form.
        $this->type = SwpmFbFormCustom::PROFILE;
        $this->success_message = '<p id="form_success">' . SwpmUtils::_("Profile Updated.") . '</p>';
        foreach ($this->fields as &$value) {
            $value->reg_field_id = $value->id;
            $value->id = null;
        }
        return $this->save();
    }

}

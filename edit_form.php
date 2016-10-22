<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Form for editing Blog tags block instances.
 *
 * @package     block_userquiz_limits
 * @category    blocks
 * @copyright   2015 Valery Fremaux
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_userquiz_limits_edit_form extends block_edit_form {

    protected function specific_definition($mform) {
        global $DB, $COURSE;

        // Fields for editing user_contact settings.
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));

        $options = array();
        $options = $DB->get_records_menu('quiz', array('course' => $COURSE->id), 'name', 'id,name');

        $mform->addElement('select', 'config_quizid', get_string('configquizid', 'block_userquiz_limits'), $options);
    }
}

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
 * @package   block_userquizlimits
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_userquiz_limits extends block_base {

    public function init() {
        $this->title = get_string('userquiz_limits', 'block_userquiz_limits') ;
    }

    public function has_config() {
        return false;
    }

    public function instance_allow_config() {
        return true;
    }

    public function applicable_formats() {
        return array('all' => false, 'mod' => false, 'tag' => false, 'course' => true);
    }

    public function get_content() {
        global $DB, $COURSE, $SESSION;

        // Only for logged in users!
        if (!isloggedin() || isguestuser()) {
            return false;
        }

        // Must be teacher in of course.
        if (!has_capability('moodle/course:manageactivities', context_course::instance($COURSE->id))) {
            return false;
        }

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = array();
        $this->content->footer = '';

        if (empty($this->config->quizid)) {
            $this->content->text = get_string('notconfigured', 'block_userquiz_limits');
            return $this->content;
        }

        if ($quiz = $DB->get_record('quiz', array('id' => $this->config->quizid))) {
            $module = $DB->get_record('modules', array('name' => 'quiz'));
            $cm = $DB->get_record('course_modules', array('instance' => $quiz->id, 'module' => $module->id));

            if (has_capability('mod/quiz:manage', context_module::instance($cm->id))){
                $setattemptslimitsstr = get_string('setattemptslimits', 'block_userquiz_limits');
                $SESSION->namefilter = '';
                $params = array('id' => $COURSE->id, 'blockid' => $this->instance->id);
                $seturl = new moodle_url('/blocks/userquiz_limits/userattempts.php', $params);
                $this->content->text = '<a href="'.$seturl.'">'.$setattemptslimitsstr.'</a>';
                $this->content->footer = $quiz->name;
            } else {
                $this->content->text = get_string('notallowed', 'block_userquiz_limits');
            }

        } else {
            $this->content->text = get_string('misconfigured', 'block_userquiz_limits');
        }

        return $this->content;
    }
}


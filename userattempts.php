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

require('../../config.php');
require_once($CFG->dirroot.'/blocks/userquiz_limits/userattempts_form.php');
require_once($CFG->dirroot.'/mod/quiz/locallib.php');

// Remember the current time as the time any responses were submitted.

$timestamp = time();

// Get submitted parameters.

$id = required_param('id', PARAM_INT); // The course id.
$blockid = required_param('blockid', PARAM_INT); // The block.
$groupid = optional_param('groupid', '', PARAM_INT); // A Group id.

if (!$instance = $DB->get_record('block_instances', array('id' => $blockid))) {
    print_error('coursemisconf');
}
$theblock = block_instance('userquiz_limits', $instance);

if (!$course = $DB->get_record("course", array('id' => $id))) {
    print_error('coursemisconf');
}

$url = new moodle_url('/blocks/userquiz_limits/userattempts.php', array('id' => $id, 'blockid' => $blockid));
$courseurl  = new moodle_url('/course/view.php', array('id' => $id));

// Security.

require_login($course->id);
$coursecontext = context_course::instance($course->id);
require_capability('moodle/course:manageactivities', $coursecontext);

$context = context_block::instance($blockid);

// If user limitation for attempts is not enabled return to edit.php.

if (!$quiz = $DB->get_record('quiz', array('id' => $theblock->config->quizid))) {
    print_error('badmoduleid');
}

$cm = get_coursemodule_from_instance('quiz', $quiz->id, $course->id);

$quizobj = quiz::create($theblock->config->quizid, $USER->id);
$rule = quizaccess_usernumattempts::make($quizobj, null, null);

if (!$rule->is_enabled() && has_capability('mod/quiz:manage', $context)) {
    redirect(new moodle_url('/mod/quiz/edit.php'), array('q' => $quiz->id));
}

$PAGE->set_url($url);
$PAGE->set_context($coursecontext);
$PAGE->set_heading(get_string('blockname', 'block_userquiz_limits'));
$PAGE->set_title(get_string('blockname', 'block_userquiz_limits'));
$PAGE->requires->js('/blocks/userquiz_limits/js/js.js');

$mform = new User_Attempts_Form($quizobj, 20, optional_param('from', 0, PARAM_INT), '', $groupid);

if ($mform->is_cancelled()) {
    redirect($courseurl);
}

if ($data = $mform->get_data()) {
    if (isset($data->setfilter) || isset($data->groupid)) {
        if (isset($data->setfilter)) {
            $SESSION->namefilter = stripslashes($data->namefilter);
        }
        $mform = new User_Attempts_Form($quizobj, 20, optional_param('from', 0, PARAM_INT), '', $data->groupid);
    } else if (isset($data->clearfilter)) {
        unset($SESSION->namefilter);
        $data->namefilter = '';
        $_POST['namefilter'] = '';
        $mform = new User_Attempts_Form($quizobj, 20, optional_param('from', 0, PARAM_INT), '', $data->groupid);
    } else {
        $selection = preg_grep('/^limit/', array_keys((array)$data));
        $selection = preg_replace('/^limit/', '', $selection);

        // Non optimal.
        $todelete = array();
        if (!empty($SESSION->namefilter)) {
            $fields = 'u.id,'.get_all_user_name_fields(true, 'u');
            if ($quizusers = get_users_by_capability($context, 'mod/quiz:attempt', $fields, 'lastname,firstname', '', '', $groupid)) {
                foreach ($quizusers as $user) {
                    if (!empty($SESSION->namefilter)) {
                        if (!preg_match("/{$SESSION->namefilter}/i", $user->firstname) &&
                                !preg_match("/{$SESSION->namefilter}/i", $user->lastname)) {
                            continue;
                        }
                    }
                    $todelete[] = $user->id;
                }
            }
        }

        if (!empty($todelete)) {
            // Purge all limitations before storing them again.
            list($insql, $params) = $DB->get_in_or_equal($todelete, SQL_PARAMS_NAMED);
            $params['quizid'] = $quiz->id;
            $select = "
                quizid = :quizid AND
                userid $insql
            ";
            $DB->delete_records_select('qa_usernumattempts_limits', $select, $params);
        }

        if (!empty($selection)) {
            foreach ($selection as $userid) {
                if ($limit = $DB->get_record('qa_usernumattempts_limits', array('userid' => $userid, 'quizid' => $quiz->id))) {
                    $datakey = 'limit'.$userid;
                    $limit->maxattempts = $data->$datakey;
                    $DB->update_record('qa_usernumattempts_limits', $limit);
                } else {
                    $limit = new StdClass();
                    $limit->courseid = $COURSE->id;
                    $limit->quizid = $quiz->id;
                    $limit->userid = $userid;
                    $datakey = 'limit'.$userid;
                    $limit->maxattempts = $data->$datakey;
                    $DB->insert_record('qa_usernumattempts_limits', $limit);
                }
            }
        }
    }
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('setuserattemptslimits', 'block_userquiz_limits'));
echo $OUTPUT->box_start('', 'userquiz-limits-userattempts-form');

$presetdata = array();
$presetdata['id'] = $id;
$presetdata['groupid'] = $groupid;
$presetdata['blockid'] = $blockid;
if ($userlimitations = $DB->get_records('qa_usernumattempts_limits', array('quizid' => $quiz->id))) {
    foreach ($userlimitations as $limit) {
        $presetdata['limit'.$limit->userid] = $limit->maxattempts;
    }
}
$mform->set_data($presetdata);
$mform->display();

echo '<center>';
$context = context_module::instance($quizobj->get_cmid());

$fields = 'u.id,'.get_all_user_name_fields(true, 'u');
$allquizusers = get_users_by_capability($context, 'mod/quiz:attempt', $fields, 'lastname,firstname', '', '', $groupid);
if ($allquizusers) {
    $alluserscount = count($allquizusers);
} else {
    $alluserscount = 0;
}
if (empty($SESSION->namefilter)) {
    echo $mform->pager($alluserscount, $url);
}
echo '</center>';
echo $OUTPUT->box_end();

echo $OUTPUT->footer();

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
 * @package     block_userquiz_limits
 * @category    blocks
 * @author      Valery Fremaux <valery.fremaux@gmail.com>
 * @copyright   Valery Fremaux <valery.fremaux@gmail.com> (MyLearningFactory.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * Observes enrols to setup an initial value as user limit
 * It will search for an hasbehalfon enabled role in the nearest context
 * This event will be executed by admin/tool/sync users management with group assigns
 * @see (non standard) admin/tool/sync
 */
class block_userquiz_limits_event_observer {

    public static function on_user_enrolment_created($eventdata) {
        global $DB;

        $userid = $eventdata->userid;
        $courseid = $eventdata->courseid;

        // Get all userquiz_limits intances in course.
        $parentcontext = context_course::instance($courseid);
        $params = ['blockname' => 'userquiz_limits', 'parentcontextid' => $parentcontext->id];
        $blockinstances = $DB->get_records('block_instances', $params);
        if (!$blockinstances) {
            // Do nothing.
            return;
        }

        foreach ($blockinstances as $bi) {
            $instance = block_instance('userquiz_limits', $bi);
            if ($oldrecord = $DB->get_record('qa_usernumattempts_limits', $params)) {
                $oldrecord->maxattempts = $instance->config->initialcredit;
                $DB->update_record('qa_usernumattempts_limits', $oldrecord);
            } else {
                $uqlimit = new Stdclass;
                $uqlimit->userid = $userid;
                $uqlimit->quizid = $instance->config->quizid;
                $uqlimit->maxattempts = $instance->config->initialcredit;
                $DB->insert_record('qa_usernumattempts_limits', $uqlimit);
            }
        }
    }
}

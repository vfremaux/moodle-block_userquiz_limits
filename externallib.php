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
 * @author      Valery Fremaux (valery.fremaux@gmail.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * this file provides with WS document requests to externalize report documents
 * from within an external management system
 */
defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir.'/externallib.php');

class block_userquiz_limits_external extends external_api {

    protected static function get_user_quiz_limits_parameters() {
        return new external_function_parameters(array('userfield' => new external_value(PARAM_TEXT, 'The source for user identification'),
            'users' => new external_multiple_structure(
                    new external_value(PARAM_TEXT, 'The user id')),
            'quizfield' => new external_value(PARAM_TEXT, 'The source for quiz identification'),
            'quizzes' => new external_multiple_structure(
                    new external_value(PARAM_TEXT, 'The quiz id')),
        ));
    }

    public static function get_user_quiz_limits($userfield, $users, $quizfield, $quizzes) {
        global $DB;

        // Standard validation (structure and datatypes);
        $configparamdefs = self::get_user_quiz_limits_parameters();
        $inputs = array(
            'userfield' => $userfield,
            'users' => $users,
            'quizfield' => $quizfield,
            'quizzes' => $quizzes,
        );
        $status = self::validate_parameters($configparamdefs, $inputs);

        if (empty($users)) {
            return array();
        }

        if (empty($quizzes)) {
            return array();
        }

        foreach ($users as $uid) {
            // Will throw exception if one is not good.
            $userobjs[$uid] = self::validate_user($userfield, $uid);
        }

        foreach ($quizzes as $qid) {
            // Will throw exception if one is not good.
            $quizobjs[$qid] = self::validate_quiz($quizfield, $qid);
        }

        // Now everything is clear. We can process.
        foreach ($userobjs as $uid => $user) {
            foreach ($quizobjs as $qid => $quiz) {
                $params = array('userid' => $user->id, 'quizid' => $quiz->id);
                if ($oldrec = $DB->get_record('qa_usernumattempts_limits', $params)) {
                    $record = new Stdclass;
                    $record->userid = $user->$userfield;
                    $record->quizid = $qid;
                    $record->limit = $oldrec->maxattempts;
                    $results[] = $record;
                } else {
                    $record = new Stdclass;
                    $record->userid = $user->$userfield;
                    $record->quizid = $quiz->$quizfield;
                    $record->limit = -1;
                    $results[] = $record;
                }
            }
        }

        return $results;
    }

    protected static function get_user_quiz_limits_returns() {
        return new external_multiple_structure(
            new external_single_structure(
            array(
                'userid' => new external_value(PARAM_TEXT, 'The user id'),
                'quizid' => new external_value(PARAM_TEXT, 'The quiz id'),
                'limit' => new external_value(PARAM_FLOAT, 'The limit value'),
                )
            )
        );
    }

    protected static function set_user_quiz_limits_parameters() {
        return new external_function_parameters(array('userfield' => new external_value(PARAM_TEXT, 'The source for user identification'),
            'users' => new external_multiple_structure(
                    new external_value(PARAM_TEXT, 'The user id')),
            'quizfield' => new external_value(PARAM_TEXT, 'The source for quiz identification'),
            'quizzes' => new external_multiple_structure(
                    new external_value(PARAM_TEXT, 'The quiz id')),
            'limit' => new external_value(PARAM_INT, 'The limit value to set'),
        ));
    }

    public static function set_user_quiz_limits($userfield, $users, $quizfield, $quizzes, $limit) {
        global $DB;

        // Standard validation (structure and datatypes);
        $configparamdefs = self::set_user_quiz_limits_parameters();
        $inputs = array(
            'userfield' => $userfield,
            'users' => $users,
            'quizfield' => $quizfield,
            'quizzes' => $quizzes,
            'limit' => $limit,
        );
        $status = self::validate_parameters($configparamdefs, $inputs);

        if (empty($users)) {
            return true;
        }

        if (empty($quizzes)) {
            return true;
        }

        foreach ($users as $uid) {
            // Will throw exception if one is not good.
            $userobjs[$uid] = self::validate_user($userfield, $uid);
        }

        foreach ($quizzes as $qid) {
            // Will throw exception if one is not good.
            $quizobjs[$qid] = self::validate_quiz($quizfield, $qid);
        }

        // Now everything is clear. We can process.

        foreach ($userobjs as $uid => $user) {
            foreach ($quizobjs as $qid => $quiz) {
                $params = array('userid' => $user->id, 'quizid' => $quiz->id);
                if ($oldrec = $DB->get_record('qa_usernumattempts_limits', $params)) {
                    $oldrec->maxattempts = $limit;
                    $DB->update_record('qa_usernumattempts_limits', $oldrec);
                } else {
                    $record = new Stdclass;
                    $record->userid = $user->id;
                    $record->quizid = $quiz->id;
                    $record->maxattempts = $limit;
                    $DB->insert_record('qa_usernumattempts_limits', $record);
                }
            }
        }

        return false;
    }

    protected static function set_user_quiz_limits_returns() {
        return new external_value(PARAM_BOOL, 'The output status');
    }

    protected static function add_user_quiz_limits_parameters() {
        return new external_function_parameters(array('userfield' => new external_value(PARAM_TEXT, 'The source for user identification'),
            'users' => new external_multiple_structure(
                    new external_value(PARAM_TEXT, 'The user id')),
            'quizfield' => new external_value(PARAM_TEXT, 'The source for quiz identification'),
            'quizzes' => new external_multiple_structure(
                    new external_value(PARAM_TEXT, 'The quiz id')),
            'limit' => new external_value(PARAM_INT, 'The limit value to set'),
        ));
    }

    public static function add_user_quiz_limits($userfield, $users, $quizfield, $quizzes, $limit) {
        global $DB;

        // Standard validation (structure and datatypes);
        $configparamdefs = self::add_user_quiz_limits_parameters();
        $inputs = array(
            'userfield' => $userfield,
            'users' => $users,
            'quizfield' => $quizfield,
            'quizzes' => $quizzes,
            'limit' => $limit,
        );
        $status = self::validate_parameters($configparamdefs, $inputs);

        if (!in_array($userfield, array('id', 'idnumber', 'username'))) {
            throw new invalid_parameter_exception('User identification field not in accepted range: '.$inputs['userfield']);
        }

        if (!in_array($quizfield, array('instanceid', 'idnumber', 'cmid'))) {
            throw new invalid_parameter_exception('Quiz identification field not in accted range: '.$inputs['quizfield']);
        }

        if (empty($users)) {
            return true;
        }

        if (empty($quizzes)) {
            return true;
        }

        foreach ($users as $uid) {
            // Will throw exception if one is not good.
            $userobjs[$uid] = self::validate_user($userfield, $uid);
        }

        foreach ($quizzes as $qid) {
            // Will throw exception if one is not good.
            $quizobjs[$qid] = self::validate_quiz($quizfield, $qid);
        }

        // Now everything is clear. We can process.

        foreach ($userobjs as $uid => $user) {
            foreach ($quizobjs as $qid => $quiz) {
                $params = array('userid' => $user->id, 'quizid' => $quiz->id);
                if ($oldrec = $DB->get_record('qa_usernumattempts_limits', $params)) {
                    $oldrec->maxattempts += $limit;
                    $DB->update_record('qa_usernumattempts_limits', $oldrec);
                } else {
                    $record = new Stdclass;
                    $record->userid = $user->id;
                    $record->quizid = $quiz->id;
                    $record->maxattempts = $limit;
                    $DB->insert_record('qa_usernumattempts_limits', $record);
                }
            }
        }

        return false;
    }

    protected static function add_user_quiz_limits_returns() {
        return new external_value(PARAM_BOOL, 'The output status');
    }

    protected static function validate_user($userfield, $userid) {
        global $DB;

        $params = array($userfield => $userid);
        if (!$user = $DB->get_record('user', $params)) {
            throw new invalid_parameter_exception('User not valid by '.$userfield.': '.$userid);
        }

        return $user;
    }

    protected static function validate_quiz($quizfield, $quizid) {
        global $DB;

        $quizfieldinput = $quizfield;

        if ($quizfield == 'instanceid') {
            $quizfield = 'id';
        }

        if ($quizfield == 'cmid') {
            if (!$cm = $DB->get_record('course_modules', array('id' => $quizid))) {
                throw new invalid_parameter_exception('Quiz not valid by cmid: '.$quizid);
            }
            $quizid = $cm->instance;
            $quizfield = 'id';
        }

        $params = array($quizfield => $quizid);
        if (!$quiz = $DB->get_record('quiz', $params, 'id')) {
            throw new invalid_parameter_exception('Quiz not valid by '.$quizfieldinput.': '.$quizid);
        }

        return $quiz;
    }
}

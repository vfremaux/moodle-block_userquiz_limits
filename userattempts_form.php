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
 * @package     blocks_userquiz_limits
 * @category    blocks
 * @author      Valery Fremaux (valery.fremaux@gmail.com)
 * @copyright   Valery Fremaux (valery.fremaux@gmail.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

class User_Attempts_Form extends moodleform {

    protected $quiz;
    protected $pagesize;
    protected $offset;
    protected $filter;
    protected $url;

    public function __construct(&$quiz, $pagesize = 20, $offset = 0, $filter = '') {
        $this->quiz = $quiz;
        $this->pagesize = $pagesize;
        $this->offset = $offset;
        $this->filter = $filter;
        parent::__construct();
    }

    public function definition() {
        global $COURSE, $DB, $SESSION;

        $mform =& $this->_form;

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'blockid');
        $mform->setType('blockid', PARAM_INT);

        $mform->addElement('hidden', 'from', $this->offset);
        $mform->setType('from', PARAM_INT);

        $group[0] = & $mform->createElement('text', 'namefilter');
        $group[1] = & $mform->createElement('submit', 'setfilter', get_string('setfilter', 'block_userquiz_limits'));
        $group[2] = & $mform->createElement('submit', 'clearfilter', get_string('clearfilter', 'block_userquiz_limits'));

        $mform->setType('filter[namefilter]', PARAM_TEXT);

        $mform->addGroup($group, 'filter', get_string('namefilter', 'block_userquiz_limits'));
        $mform->addHelpButton('filter', 'regexfilter', 'block_userquiz_limits');

        $thiscontext = context_course::instance($COURSE->id);

        $attrs = array('maxlength' => 3, 'size' => 4);

        $userfields = 'u.id,'.get_all_user_name_fields(true, 'u');

        $context = context_module::instance($this->quiz->get_cmid());
        if (!empty($SESSION->namefilter)) {
            /*
             * If using name filter, we do not page the results. We will add an additional field to distribute,
             * add or substract some attempts to the selection.
             */
            if ($quizusers = get_users_by_capability($context, 'mod/quiz:attempt', $userfields, 'lastname,firstname')) {
                foreach ($quizusers as $user) {
                    if (!empty($SESSION->namefilter)) {
                        if (!preg_match("/{$SESSION->namefilter}/i", fullname($user))) {
                            continue;
                        }
                    }
                    $d = new Stdclass;
                    $d->user = fullname($user);
                    $select = "
                        quiz = ? AND
                        userid = ?
                    ";
                    $d->used = 0 + $DB->count_records_select('quiz_attempts', $select, array($this->quiz->get_quizid(), $user->id));

                    $group = array();
                    $group[] = $mform->createElement('text', 'limit'.$user->id, '', $attrs);
                    $mform->setType('limit'.$user->id, PARAM_TEXT);
                    $group[] = $mform->createElement('static', '', '', '');

                    $container = '&ensp;&ensp;<span class="html'.$user->id.'"></span>';
                    $label = get_string('attemptslimit', 'block_userquiz_limits', $d);
                    $mform->addGroup($group, 'limitgroup'.$user->id, $label, array($container), false);
                }

                $group = array();
                $group[] = $mform->createElement('text', 'value', '');
                $setattrs = array('onclick' => 'set_value("set")',
                                  'onmouseover' => 'preview_value("set")',
                                  'onmouseout' => 'cancel_preview()');
                $group[] = $mform->createElement('button', 'set', get_string('set', 'block_userquiz_limits'), $setattrs);
                $addattrs = array('onclick' => 'set_value("add")',
                                  'onmouseover' => 'preview_value("add")',
                                  'onmouseout' => 'cancel_preview()');
                $group[] = $mform->createElement('button', 'add', get_string('add', 'block_userquiz_limits'), $addattrs);
                $subattrs = array('onclick' => 'set_value("sub")',
                                  'onmouseover' => 'preview_value("sub")',
                                  'onmouseout' => 'cancel_preview()');
                $group[] = $mform->createElement('button', 'sub', get_string('sub', 'block_userquiz_limits'), $subattrs);

                $mform->setType('value', PARAM_INT);

                $mform->addGroup($group, 'selectionopgroup', get_string('withselection', 'block_userquiz_limits'), '', array(), false);
            }
        } else {
            if ($quizusers = get_users_by_capability($context, 'mod/quiz:attempt', $userfields, 'lastname,firstname',
                                                     $this->offset, $this->pagesize)) {
                foreach ($quizusers as $user) {
                    $d = new Stdclass;
                    $d->user = fullname($user);
                    $select = "
                        quiz = ? AND
                        userid = ?
                    ";
                    $d->used = 0 + $DB->count_records_select('quiz_attempts', $select, array($this->quiz->get_quizid(), $user->id));
                    $mform->addElement('text', 'limit'.$user->id, get_string('attemptslimit', 'block_userquiz_limits', $d), $attrs);
                    $mform->setType('limit'.$user->id, PARAM_TEXT);
                }
            }
        }

        $this->add_action_buttons();
    }

    public function pager($maxobjects, $url) {

        if ($maxobjects <= $this->pagesize) {
            return;
        }

        $current = ceil(($this->offset + 1) / $this->pagesize);
        $pages = array();
        $off = 0;

        for ($p = 1 ; $p <= ceil($maxobjects / $this->pagesize) ; $p++) {
            if ($p == $current) {
                $pages[] = $p;
            } else {
                $pages[] = "<a href=\"{$url}&from={$off}\">{$p}</a>";
            }
            $off = $off + $this->pagesize;
        }

        return implode(' - ', $pages);
    }
}
